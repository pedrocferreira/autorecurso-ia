<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Appeal;
use App\Models\InfractionType;
use App\Services\OpenAIService;
use App\Services\PDFService;
use App\Mail\RecursoGeradoMail;
use App\Services\CreditService;
use App\Services\AbacatePayService;

class ChatController extends Controller
{
    protected $openAIService;
    protected $pdfService;
    protected $creditService;
    protected $abacatePayService;

    public function __construct(OpenAIService $openAIService, PDFService $pdfService, CreditService $creditService, AbacatePayService $abacatePayService)
    {
        $this->openAIService = $openAIService;
        $this->pdfService = $pdfService;
        $this->creditService = $creditService;
        $this->abacatePayService = $abacatePayService;
    }

    /**
     * Processa pagamento PIX e cria cobrança
     */
    public function createPixPayment(Request $request)
    {
        try {
            Log::info('Iniciando criação de pagamento PIX via chat', $request->all());

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'cpf' => 'required|string|max:14',
                'phone' => 'required|string|max:20',
                'amount' => 'required|numeric|min:0'
            ]);

            // Validar e limpar CPF
            $cpf = preg_replace('/[^0-9]/', '', $validated['cpf']);
            if (!$this->isValidCPF($cpf)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CPF inválido. Por favor, informe um CPF válido.'
                ], 422);
            }

            // Limpar telefone
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
            if (strlen($phone) < 10 || strlen($phone) > 11) {
                $phone = '11999999999'; // Telefone padrão se inválido
            }

            // Criar ou encontrar usuário
            $user = $this->createOrFindUser($validated);

            // Armazenar dados temporários na sessão para usar após pagamento
            session(['chat_payment_data' => $request->all()]);

            // Criar transação especial para chat (valor fixo de R$ 29,90)
            $chatTransaction = \App\Models\CreditTransaction::create([
                'user_id' => $user->id,
                'type' => 'purchase',
                'amount' => 0, // Não adiciona créditos, é para geração de recurso
                'balance_after' => $user->credits,
                'description' => 'Geração de Recurso via Chat Wizard',
                'status' => 'pending',
                'payment_method' => 'pix',
                'metadata' => [
                    'source' => 'chat_wizard',
                    'price' => 29.90,
                    'service_type' => 'recurso_generation',
                    'gateway' => 'abacatepay'
                ]
            ]);

            // Preparar dados para AbacatePay
            $paymentData = [
                'amount' => 2990, // R$ 29,90 em centavos
                'description' => 'AutoRecurso - Geração de Recurso de Multa',
                'customer' => [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $phone,
                    'document' => $cpf
                ],
                'metadata' => [
                    'user_id' => $user->id,
                    'transaction_id' => $chatTransaction->id,
                    'service' => 'recurso_generation',
                    'source' => 'chat_wizard'
                ],
                'return_url' => route('cliente.success'),
                'completion_url' => route('cliente.success')
            ];

            Log::info('Criando cobrança PIX via AbacatePay', [
                'user_id' => $user->id,
                'transaction_id' => $chatTransaction->id,
                'amount_cents' => $paymentData['amount']
            ]);

            // Criar cobrança na AbacatePay
            $result = $this->abacatePayService->createPixPayment($paymentData);

            if (!$result['success']) {
                // Remove transação em caso de erro
                $chatTransaction->delete();
                
                Log::error('Erro ao criar PIX na AbacatePay', ['error' => $result['error']]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar PIX: ' . $result['error']
                ], 500);
            }

            // Salvar billing_id na transação para consultas futuras
            $chatTransaction->update([
                'reference' => $result['data']['data']['id']
            ]);

            Log::info('PIX criado com sucesso via AbacatePay', [
                'billing_id' => $result['data']['data']['id'],
                'transaction_id' => $chatTransaction->id
            ]);

            return response()->json([
                'success' => true,
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao criar pagamento PIX via chat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Processa pagamento via cartão (Stripe) 
     */
    public function createStripePayment(Request $request)
    {
        try {
            Log::info('Iniciando criação de pagamento Stripe via chat', $request->all());

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255'
            ]);

            // Armazenar dados temporários na sessão
            session(['chat_payment_data' => $request->all()]);

            // Por enquanto, vamos redirecionar para um método que precisará ser implementado
            // Você pode integrar com Stripe ou outro gateway de cartão aqui
            Log::warning('Stripe payment not implemented yet, returning placeholder');

            return response()->json([
                'success' => true,
                'url' => route('cliente.wizard') . '?payment=stripe_pending'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao criar pagamento Stripe: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Verifica status do pagamento PIX
     */
    public function checkPixStatus($billingId)
    {
        try {
            Log::info('Verificando status do PIX', ['billing_id' => $billingId]);

            // Buscar transação pelo billing_id
            $transaction = \App\Models\CreditTransaction::where('reference', $billingId)
                ->where('payment_method', 'pix')
                ->whereJsonContains('metadata->source', 'chat_wizard')
                ->first();

            if (!$transaction) {
                Log::warning('Transação não encontrada para billing_id', ['billing_id' => $billingId]);
                return response()->json(['status' => 'not_found']);
            }

            // Se já foi processada, retorna sucesso
            if ($transaction->status === 'completed') {
                return response()->json(['status' => 'paid']);
            }

            // Consultar status na AbacatePay
            $result = $this->abacatePayService->getBillingStatus($billingId);

            if (!$result['success']) {
                Log::error('Erro ao consultar status na AbacatePay', [
                    'billing_id' => $billingId,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
                return response()->json(['status' => 'error']);
            }

            $abacatePayStatus = $result['status'] ?? 'PENDING';
            
            Log::info('Status retornado pela AbacatePay', [
                'billing_id' => $billingId,
                'status' => $abacatePayStatus
            ]);

            // Se foi pago na AbacatePay
            if (strtoupper($abacatePayStatus) === 'PAID' && $transaction->status !== 'completed') {
                try {
                    // Marcar transação como paga
                    $transaction->update([
                        'status' => 'completed',
                        'paid_at' => now()
                    ]);

                    // Processar geração do recurso
                    $this->processApprovedPayment($billingId, $transaction);

                    return response()->json(['status' => 'paid']);
                    
                } catch (\Exception $e) {
                    Log::error('Erro ao processar pagamento aprovado', [
                        'billing_id' => $billingId,
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage()
                    ]);
                    return response()->json(['status' => 'error']);
                }
            }

            // Converter status da AbacatePay para formato do frontend
            $frontendStatus = $this->convertAbacatePayStatus($abacatePayStatus);
            
            return response()->json(['status' => $frontendStatus]);

        } catch (\Exception $e) {
            Log::error('Erro ao verificar status PIX: ' . $e->getMessage());
            return response()->json(['status' => 'error']);
        }
    }

    /**
     * Processa pagamento aprovado e gera recurso
     */
    private function processApprovedPayment($billingId, $transaction = null)
    {
        try {
            DB::beginTransaction();

            $paymentData = session('chat_payment_data');
            if (!$paymentData) {
                throw new \Exception('Dados de pagamento não encontrados na sessão');
            }

            Log::info('Processando pagamento aprovado via AbacatePay', [
                'billing_id' => $billingId, 
                'transaction_id' => $transaction?->id,
                'data_keys' => array_keys($paymentData)
            ]);

            // Criar ou encontrar usuário
            $user = $this->createOrFindUser($paymentData);

            // Criar ticket temporário
            $ticket = $this->createTicketFromChatData($paymentData, $user);

            // Gerar recurso usando IA
            $appealText = $this->generateAppealWithHybridIA($paymentData);

            // Gerar PDF
            $pdfPath = $this->generateAppealPDF($appealText, $ticket);

            // Criar registro do recurso
            $appeal = Appeal::create([
                'ticket_id' => $ticket->id,
                'text' => $appealText,
                'generated_text' => $appealText,
                'pdf_path' => $pdfPath,
                'status' => 'pending',
                'user_id' => $user->id,
                'metadata' => json_encode([
                    'generated_via' => 'chat_wizard',
                    'payment_method' => 'pix',
                    'billing_id' => $billingId,
                    'generated_at' => now()->toISOString()
                ])
            ]);

            // Enviar email com recurso
            $this->sendRecursoEmail($user, $appeal);

            DB::commit();

            // Limpar dados da sessão
            session()->forget('chat_payment_data');

            Log::info('Recurso gerado e enviado com sucesso via chat', ['appeal_id' => $appeal->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao processar pagamento aprovado: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Criar ou encontrar usuário
     */
    private function createOrFindUser($data)
    {
        $cpf = preg_replace('/[^0-9]/', '', $data['cpf']);
        
        $user = User::where('cpf', $cpf)->first();
        
        if (!$user) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'cpf' => $cpf,
                'phone' => $data['phone'] ?? null,
                'password' => bcrypt('autorecurso' . rand(1000, 9999)),
                'email_verified_at' => now(),
                'credits' => 0
            ]);
        }

        return $user;
    }

    /**
     * Criar ticket a partir dos dados do chat
     */
    private function createTicketFromChatData($data, $user)
    {
        $infractionType = InfractionType::find($data['infraction_type']);

        return Ticket::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'cpf' => preg_replace('/[^0-9]/', '', $data['cpf']),
            'email' => $data['email'],
            'phone' => $data['phone'],
            'driver_license' => $data['driver_license'],
            'driver_license_category' => 'B', // Padrão
            'address' => 'Endereço não informado', // Campo obrigatório
            'plate' => strtoupper($data['placa']),
            'vehicle_model' => $data['vehicle_model'],
            'vehicle_year' => $data['vehicle_year'],
            'vehicle_color' => 'Não informado',
            'vehicle_chassi' => 'Não informado',
            'vehicle_renavam' => 'Não informado',
            'ticket_number' => $data['ticket_number'],
            'organ' => $data['organ'],
            'date' => $data['data'],
            'amount' => $data['amount'],
            'points' => $data['points'],
            'reason' => $data['details'] ?: ($infractionType ? $infractionType->description : 'Detalhes não informados'),
            'infraction_type_id' => $data['infraction_type'],
            'location' => $data['location'],
            'was_driver' => $data['was_driver'] === 'sim',
            'had_signage' => $data['had_signage'] === 'sim'
        ]);
    }

    /**
     * Gerar recurso usando IA híbrida
     */
    private function generateAppealWithHybridIA($data)
    {
        try {
            // Usar o mesmo método do AppealController para gerar com múltiplas IAs
            $appealTexts = $this->generateWithAllModels($data);
            $analysisData = $this->generateAnalysisAndBestText($appealTexts);
            
            return $analysisData['best_text'];
        } catch (\Exception $e) {
            Log::error('Erro ao gerar recurso com IA: ' . $e->getMessage());
            // Fallback para texto padrão
            return $this->buildFallbackAppealText($data);
        }
    }

    /**
     * Gerar recurso com todas as IAs (adaptado do AppealController)
     */
    private function generateWithAllModels($data)
    {
        $appealTexts = [];
        
        try {
            // GPT-4
            $appealTexts['gpt4'] = $this->generateWithGPT4($data);
        } catch (\Exception $e) {
            Log::warning('Erro ao gerar com GPT-4: ' . $e->getMessage());
            $appealTexts['gpt4'] = null;
        }

        try {
            // Gemini
            $appealTexts['gemini'] = $this->generateWithGemini($data);
        } catch (\Exception $e) {
            Log::warning('Erro ao gerar com Gemini: ' . $e->getMessage());
            $appealTexts['gemini'] = null;
        }

        try {
            // Claude (se disponível)
            $appealTexts['claude'] = $this->generateWithClaude($data);
        } catch (\Exception $e) {
            Log::warning('Erro ao gerar com Claude: ' . $e->getMessage());
            $appealTexts['claude'] = null;
        }

        return array_filter($appealTexts);
    }

    /**
     * Validar CPF
     */
    private function isValidCPF($cpf)
    {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        // Calcula os dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Webhook da AbacatePay para pagamentos do chat
     * (Processa quando um pagamento é aprovado e gera o recurso automaticamente)
     */
    public function webhookAbacatePay(Request $request)
    {
        try {
            // Validar webhook secret
            $webhookSecret = $request->query('webhookSecret');
            $expectedSecret = config('abacatepay.webhook_secret');

            if (!$webhookSecret || $webhookSecret !== $expectedSecret) {
                Log::warning('Webhook AbacatePay com secret inválido (Chat)', [
                    'provided_secret' => $webhookSecret,
                    'ip' => $request->ip()
                ]);
                return response('Unauthorized', 401);
            }

            $payload = $request->all();
            $billingId = $payload['data']['id'] ?? null;
            $status = $payload['data']['status'] ?? null;

            Log::info('Webhook AbacatePay recebido para chat', [
                'event' => $payload['event'] ?? 'unknown',
                'billing_id' => $billingId,
                'status' => $status
            ]);

            // Só processa pagamentos aprovados
            if (strtoupper($status) !== 'PAID') {
                return response('OK', 200);
            }

            // Buscar transação do chat
            $transaction = \App\Models\CreditTransaction::where('reference', $billingId)
                ->where('payment_method', 'pix')
                ->whereJsonContains('metadata->source', 'chat_wizard')
                ->first();

            if (!$transaction) {
                Log::warning('Transação do chat não encontrada para billing_id', ['billing_id' => $billingId]);
                return response('Transaction not found', 404);
            }

            // Se já foi processada, retorna sucesso
            if ($transaction->status === 'completed') {
                return response('Already processed', 200);
            }

            // Marcar como paga
            $transaction->update([
                'status' => 'completed',
                'paid_at' => now()
            ]);

            // Processar geração do recurso em background
            try {
                $this->processApprovedPayment($billingId, $transaction);
                Log::info('Recurso gerado com sucesso via webhook', ['billing_id' => $billingId]);
            } catch (\Exception $e) {
                Log::error('Erro ao gerar recurso via webhook', [
                    'billing_id' => $billingId,
                    'error' => $e->getMessage()
                ]);
                // Não falha o webhook, só loga o erro
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Erro no webhook do chat', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);
            return response('Internal server error', 500);
        }
    }

    /**
     * Converte status da AbacatePay para formato do frontend
     */
    private function convertAbacatePayStatus($abacatePayStatus)
    {
        switch (strtoupper($abacatePayStatus)) {
            case 'PAID':
                return 'paid';
            case 'PENDING':
                return 'pending';
            case 'EXPIRED':
                return 'expired';
            case 'CANCELLED':
                return 'cancelled';
            default:
                return 'pending';
        }
    }

    /**
     * Enviar email com recurso e instruções
     */
    private function sendRecursoEmail($user, $appeal)
    {
        try {
            Mail::to($user->email)->send(new RecursoGeradoMail($user, $appeal));
            Log::info('Email de recurso enviado com sucesso', ['user_id' => $user->id, 'appeal_id' => $appeal->id]);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de recurso: ' . $e->getMessage());
        }
    }

    /**
     * Gerar recurso com GPT-4
     */
    private function generateWithGPT4($data)
    {
        try {
            $prompt = $this->buildCleanLegalPrompt($data);
            
            $result = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => 'gpt-4-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Você é um advogado especialista em recursos de multas de trânsito no Brasil. GERE APENAS O TEXTO FINAL DO RECURSO ADMINISTRATIVO, COMPLETAMENTE LIMPO E PRONTO PARA PROTOCOLO. NUNCA inclua comentários, observações, notas explicativas, campos vazios como [INSERIR...], ou qualquer texto que não seja parte do recurso oficial. Preencha TODOS os dados fornecidos. O documento deve estar pronto para impressão, assinatura e protocolo imediatamente.'
                    ],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.4,
                'max_tokens' => 2500
            ]);
            
            return $this->cleanAppealText($result->choices[0]->message->content, $data);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar com GPT-4: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Gerar recurso com Gemini
     */
    private function generateWithGemini($data)
    {
        try {
            $prompt = $this->buildCleanLegalPrompt($data);
            
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro-latest:generateContent', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'key' => config('services.gemini.api_key')
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ],
                    'systemInstruction' => [
                        'parts' => [
                            [
                                'text' => 'Você é um advogado especialista em recursos de multas de trânsito no Brasil. GERE APENAS O TEXTO FINAL DO RECURSO, COMPLETAMENTE LIMPO E PRONTO PARA PROTOCOLO. Nunca inclua comentários, observações, notas ou campos vazios. Preencha TODOS os dados fornecidos. O documento deve estar pronto para impressão e assinatura imediatamente.'
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 2500,
                    ]
                ]
            ]);
            
            $result = json_decode($response->getBody(), true);
            $text = $result['candidates'][0]['content']['parts'][0]['text'];
            
            return $this->cleanAppealText($text, $data);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar com Gemini: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Gerar recurso com Claude (placeholder)
     */
    private function generateWithClaude($data)
    {
        // Implementar se tiver acesso à API do Claude
        return null;
    }

    /**
     * Analisa todas as versões e seleciona a melhor
     */
    private function generateAnalysisAndBestText($appealTexts)
    {
        $validTexts = array_filter($appealTexts);
        
        if (empty($validTexts)) {
            throw new \Exception('Nenhum texto de recurso foi gerado com sucesso');
        }

        // Se só tiver uma versão, retorna ela
        if (count($validTexts) === 1) {
            return [
                'best_text' => reset($validTexts),
                'selected_model' => key($validTexts),
                'selection_reason' => 'Única versão disponível',
                'detailed_analysis' => 'Análise não necessária - apenas uma versão gerada',
                'all_versions' => $validTexts
            ];
        }

        // Se tiver múltiplas versões, seleciona a do GPT-4 por padrão
        // ou a primeira disponível
        $priority = ['gpt4', 'gemini', 'claude'];
        
        foreach ($priority as $model) {
            if (isset($validTexts[$model]) && !empty($validTexts[$model])) {
                return [
                    'best_text' => $validTexts[$model],
                    'selected_model' => $model,
                    'selection_reason' => "Selecionado modelo {$model} por prioridade de qualidade",
                    'detailed_analysis' => 'Seleção baseada em hierarquia de modelos',
                    'all_versions' => $validTexts
                ];
            }
        }

        // Fallback para qualquer texto válido
        return [
            'best_text' => reset($validTexts),
            'selected_model' => key($validTexts),
            'selection_reason' => 'Fallback para primeira versão válida',
            'detailed_analysis' => 'Análise de fallback',
            'all_versions' => $validTexts
        ];
    }

    /**
     * Gera PDF do recurso
     */
    private function generateAppealPDF($text, $ticket)
    {
        try {
            $filename = 'recurso_chat_' . $ticket->id . '_' . time() . '.pdf';
            
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.appeal', [
                'text' => $text,
                'ticket' => $ticket
            ]);

            // Criar diretório se não existir
            $directory = storage_path('app/public/appeals');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $pdf->save(storage_path('app/public/appeals/' . $filename));

            return 'appeals/' . $filename;
        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF do recurso: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Constrói prompt legal limpo
     */
    private function buildCleanLegalPrompt($data)
    {
        $infractionType = InfractionType::find($data['infraction_type']);
        $infractionName = $infractionType ? $infractionType->description : ($data['reason'] ?? 'Infração não especificada');
        
        return "Gere um recurso administrativo completo e profissional para contestar uma multa de trânsito com os seguintes dados:

DADOS DO CONDUTOR:
- Nome: {$data['name']}
- CPF: " . preg_replace('/[^0-9]/', '', $data['cpf']) . "
- CNH: {$data['driver_license']}
- Telefone: {$data['phone']}
- Email: {$data['email']}

DADOS DO VEÍCULO:
- Placa: {$data['placa']}
- Modelo: {$data['vehicle_model']}
- Ano: {$data['vehicle_year']}

DADOS DA INFRAÇÃO:
- Auto de Infração: {$data['ticket_number']}
- Órgão Autuador: {$data['organ']}
- Data da Infração: {$data['data']}
- Local: {$data['location']}
- Tipo de Infração: {$infractionName}
- Valor: R$ {$data['amount']}
- Pontos: {$data['points']}
- Era o condutor: " . ($data['was_driver'] === 'sim' ? 'Sim' : 'Não') . "
- Havia sinalização adequada: " . ($data['had_signage'] === 'sim' ? 'Sim' : 'Não') . "
- Detalhes adicionais: " . ($data['details'] ?: 'Nenhum detalhe adicional informado') . "

INSTRUÇÕES:
1. Crie um recurso administrativo completo seguindo as normas do CTB
2. Use argumentos jurídicos sólidos baseados na legislação brasileira
3. Inclua fundamentação legal apropriada
4. O documento deve estar pronto para protocolo imediato
5. Use linguagem formal e técnica adequada
6. Preencha TODOS os dados fornecidos
7. NÃO deixe campos vazios ou com placeholders";
    }

    /**
     * Limpa o texto do recurso
     */
    private function cleanAppealText($text, $data)
    {
        // Remove comentários e observações
        $text = preg_replace('/\[.*?\]/', '', $text);
        $text = preg_replace('/\(.*observa.*\)/i', '', $text);
        $text = preg_replace('/\(.*nota.*\)/i', '', $text);
        
        // Remove múltiplas quebras de linha
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        
        // Garante que dados importantes estejam preenchidos
        $text = str_replace('[NOME]', $data['name'], $text);
        $text = str_replace('[CPF]', preg_replace('/[^0-9]/', '', $data['cpf']), $text);
        $text = str_replace('[CNH]', $data['driver_license'], $text);
        
        return trim($text);
    }

    /**
     * Texto de fallback em caso de erro
     */
    private function buildFallbackAppealText($data)
    {
        $cpf = preg_replace('/[^0-9]/', '', $data['cpf']);
        $date = \Carbon\Carbon::parse($data['data'])->format('d/m/Y');
        
        return "À AUTORIDADE DE TRÂNSITO COMPETENTE

ASSUNTO: Recurso Administrativo - Auto de Infração nº {$data['ticket_number']}

{$data['name']}, portador do CPF nº {$cpf}, CNH nº {$data['driver_license']}, vem respeitosamente à presença de Vossa Senhoria apresentar RECURSO ADMINISTRATIVO contra a penalidade imposta através do Auto de Infração em epígrafe, com fundamento no art. 286 do Código de Trânsito Brasileiro, pelos motivos a seguir expostos:

I - DOS FATOS

Em {$date}, no local {$data['location']}, foi lavrado o auto de infração nº {$data['ticket_number']} pelo órgão {$data['organ']}, imputando ao requerente a prática de infração prevista no Código de Trânsito Brasileiro.

II - DO DIREITO

O presente recurso fundamenta-se nos seguintes argumentos jurídicos:

1. DA PRESUNÇÃO DE LEGITIMIDADE DOS ATOS ADMINISTRATIVOS
2. DO PRINCÍPIO DA LEGALIDADE ESTRITA NO DIREITO ADMINISTRATIVO SANCIONADOR
3. DA NECESSIDADE DE COMPROVAÇÃO TÉCNICA DA INFRAÇÃO

III - DOS PEDIDOS

Ante o exposto, requer:

a) O recebimento do presente recurso;
b) A anulação do auto de infração por vício formal;
c) O arquivamento do processo administrativo.

Nestes termos,
Pede deferimento.

{$data['location']}, " . now()->format('d/m/Y') . "

_________________________________
{$data['name']}
CPF: {$cpf}
CNH: {$data['driver_license']}";
    }
} 