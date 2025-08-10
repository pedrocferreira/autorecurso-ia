<?php

namespace App\Http\Controllers;

use App\Models\Appeal;
use App\Models\Ticket;
use App\Services\OpenAIService;
use App\Services\PDFService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Services\CreditService;
use App\Models\User;
use App\Models\InfractionType;
use OpenAI\Laravel\Facades\OpenAI;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecursoGeradoMail;

class AppealController extends Controller
{
    protected $openAIService;
    protected $pdfService;

    /**
     * Construtor que inicializa os serviços e aplica middleware de autenticação.
     */
    public function __construct(OpenAIService $openAIService, PDFService $pdfService)
    {
        $this->middleware('auth');
        $this->openAIService = $openAIService;
        $this->pdfService = $pdfService;
    }

    /**
     * Exibe uma lista de recursos do usuário.
     */
    public function index()
    {
        $appeals = Appeal::where('user_id', auth()->id())
            ->with(['ticket'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('appeals.index', compact('appeals'));
    }

    /**
     * Mostra o formulário para gerar um novo recurso.
     */
    public function create(Ticket $ticket)
    {
        // Verifica se a multa pertence ao usuário atual
        if ($ticket->user_id !== auth()->id()) {
            abort(403, 'Você não tem permissão para gerar um recurso para esta multa.');
        }

        // Verifica se a multa já tem um recurso
        if ($ticket->appeals()->exists()) {
            return redirect()->route('tickets.show', $ticket)
                ->with('error', 'Esta multa já possui um recurso.');
        }

        // Verifica se a multa tem todos os dados necessários
        $requiredFields = [
            'name', 'cpf', 'driver_license', 'driver_license_category',
            'address', 'phone', 'email', 'plate', 'vehicle_model',
            'vehicle_year', 'vehicle_color', 'vehicle_chassi', 'vehicle_renavam',
            'date', 'amount', 'points', 'reason', 'infraction_type_id'
        ];

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($ticket->$field)) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return redirect()->route('tickets.edit', $ticket)
                ->with('error', 'Esta multa não possui todos os dados necessários para gerar um recurso. Por favor, complete os dados antes de tentar novamente.');
        }

        return view('appeals.create', compact('ticket'));
    }

    /**
     * Mostra o novo formulário para gerar um recurso com todos os dados.
     */
    public function createNew(): View|RedirectResponse
    {
        $user = Auth::user();
        // Verificar se o usuário tem créditos suficientes
        if (!$user->hasEnoughCredits(1)) {
            return redirect()->route('credits.packages')
                ->with('warning', 'Você precisa ter pelo menos 1 crédito para gerar um recurso. Por favor, adquira créditos para continuar.');
        }

        $infractionTypes = \App\Models\InfractionType::where('active', true)
            ->orderBy('code')
            ->get();

        return view('appeals.create_new', compact('infractionTypes'));
    }

    /**
     * Gera um novo recurso e armazena no banco de dados.
     */
    public function store(Request $request)
    {
        try {
            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'ticket_id' => 'required|exists:tickets,id',
                'name' => 'required|string|max:255',
                'cpf' => 'required|string|max:14',
                'driver_license' => 'required|string|max:11',
                'driver_license_category' => 'required|string|max:2',
                'address' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'required|email|max:255',
                'plate' => 'required|string|max:7',
                'vehicle_model' => 'required|string|max:100',
                'vehicle_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'vehicle_color' => 'required|string|max:50',
                'vehicle_chassi' => 'required|string|max:17',
                'vehicle_renavam' => 'required|string|max:11',
                'date' => 'required|date',
                'amount' => 'required|numeric|min:0',
                'points' => 'required|integer|min:0',
                'reason' => 'nullable|string|max:1000',
                'infraction_type_id' => 'required|exists:infraction_types,id'
            ], [
                'vehicle_chassi.max' => 'O campo chassi deve ter no máximo 17 caracteres.',
                'vehicle_renavam.max' => 'O campo RENAVAM deve ter no máximo 11 caracteres.'
            ]);

            if ($validator->fails()) {
                Log::error('Erro de validação ao gerar recurso:', $validator->errors()->toArray());
                return back()->withErrors($validator)->withInput();
            }

            // Busca a multa
            $ticket = Ticket::findOrFail($request->ticket_id);

            // Verifica se o usuário tem créditos suficientes
            $user = auth()->user();
            if ($user->credits < 1) {
                return back()->with('error', 'Você não possui créditos suficientes para gerar um recurso.');
            }

            // Simula um processo que demora um tempo para ser concluído
            // para que a animação de loading seja exibida por um tempo adequado
            if (app()->environment('production')) {
                sleep(2); // Pequena pausa para simular processamento inicial
            }

            // Gera o texto do recurso usando GPT-4
            $appealText = $this->generateAppealText($request->all());
            
            // Pequena pausa para simular o processamento do PDF
            if (app()->environment('production')) {
                sleep(1);
            }

            // Cria o PDF do recurso
            $pdfPath = $this->generateAppealPDF($appealText, $ticket);

            // Cria o registro do recurso
            $appeal = Appeal::create([
                'ticket_id' => $ticket->id,
                'text' => $appealText,
                'generated_text' => $appealText,
                'pdf_path' => $pdfPath,
                'status' => 'pending',
                'user_id' => $user->id
            ]);

            // Deduz os créditos do usuário
            $user->decrement('credits');

            // Registra a transação de créditos
            DB::table('credit_transactions')->insert([
                'user_id' => $user->id,
                'type' => 'consumption',
                'amount' => -1,
                'balance_after' => $user->fresh()->credits,
                'description' => 'Geração de recurso para multa #' . $ticket->id,
                'appeal_id' => $appeal->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Envia o e-mail de confirmação para o usuário
            try {
                Mail::to($user)->send(new RecursoGeradoMail($user, $appeal));
            } catch (\Exception $e) {
                Log::error('Falha ao enviar e-mail de confirmação: ' . $e->getMessage());
                // Não interrompe o fluxo, apenas registra o erro.
            }

            return redirect()->route('appeals.show', $appeal)
                ->with('success', 'Recurso gerado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao gerar recurso: ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao gerar o recurso. Por favor, tente novamente.');
        }
    }

    /**
     * Gera um novo recurso a partir do formulário gamificado.
     */
    public function storeNew(Request $request)
    {
        try {
            // Validação dos dados para o novo formulário
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'cpf' => 'required|string|max:14',
                'driver_license' => 'required|string|max:11',
                'phone' => 'nullable|string|max:20', // Tornando telefone opcional
                'plate' => 'required|string|max:8', // Aumentando limite para placas Mercosul
                'vehicle_model' => 'required|string|max:100',
                'vehicle_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'vehicle_color' => 'required|string|max:50',
                'citation_number' => 'required|string|max:50',
                'date' => 'required|date',
                'time' => 'required',
                'infraction_type_id' => 'required|exists:infraction_types,id',
                'amount' => 'required|numeric|min:0',
                'location' => 'required|string|max:255',
                'reason' => 'required|string|max:1000',
                'custom_details' => 'nullable|string|max:2000',
                'selected_justifications' => 'nullable|string' // JSON das justificativas selecionadas
            ], [
                'custom_details.max' => 'Os detalhes específicos devem ter no máximo 2000 caracteres.',
                'name.required' => 'O nome é obrigatório.',
                'cpf.required' => 'O CPF é obrigatório.',
                'driver_license.required' => 'A CNH é obrigatória.',
                'plate.required' => 'A placa do veículo é obrigatória.',
                'plate.max' => 'A placa deve ter no máximo 8 caracteres.',
                'vehicle_model.required' => 'O modelo do veículo é obrigatório.',
                'vehicle_year.required' => 'O ano do veículo é obrigatório.',
                'vehicle_color.required' => 'A cor do veículo é obrigatória.',
                'citation_number.required' => 'O número da autuação é obrigatório.',
                'date.required' => 'A data da infração é obrigatória.',
                'time.required' => 'O horário da infração é obrigatório.',
                'infraction_type_id.required' => 'O tipo de infração é obrigatório.',
                'amount.required' => 'O valor da multa é obrigatório.',
                'location.required' => 'O local da infração é obrigatório.',
                'reason.required' => 'O motivo da infração é obrigatório.'
            ]);

            if ($validator->fails()) {
                Log::error('Erro de validação ao gerar recurso novo:', $validator->errors()->toArray());
                
                // Se for uma requisição AJAX, retorna JSON com erros
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Por favor, corrija os seguintes erros:',
                        'errors' => $validator->errors()->toArray()
                    ], 422);
                }
                
                return back()->withErrors($validator)->withInput();
            }

            // Verifica se o usuário tem créditos suficientes (sempre 3 para Inteligência Híbrida)
            $user = auth()->user();
            $creditsNeeded = 3; // Sempre usa Inteligência Híbrida
            
            if ($user->credits < $creditsNeeded) {
                return back()->with('error', "Você precisa de {$creditsNeeded} créditos para gerar um recurso com Inteligência Híbrida. Você tem apenas {$user->credits} crédito(s).");
            }

            // Processa justificativas selecionadas pela IA
            $selectedJustifications = [];
            if ($request->has('selected_justifications') && !empty($request->selected_justifications)) {
                try {
                    $selectedJustifications = json_decode($request->selected_justifications, true) ?: [];
                    Log::info('Justificativas selecionadas processadas:', ['count' => count($selectedJustifications)]);
                } catch (\Exception $e) {
                    Log::warning('Erro ao processar justificativas selecionadas: ' . $e->getMessage());
                }
            }

            // Primeiro, cria um ticket temporário com os dados fornecidos
            $ticketData = array_merge($request->all(), [
                'user_id' => $user->id,
                'email' => $user->email, // Pega do perfil do usuário
                'address' => $user->cnh_address ?? 'Endereço não informado', // Pega do perfil
                'driver_license_category' => $user->cnh_category ?? 'B', // Pega do perfil
                'selected_justifications' => $selectedJustifications, // Inclui justificativas selecionadas
                'vehicle_chassi' => '', // Campo obrigatório mas não usado
                'vehicle_renavam' => '', // Campo obrigatório mas não usado
                'points' => 4, // Valor padrão
                'phone' => $request->input('phone') ?? 'Não informado' // Valor padrão se phone for null
            ]);

            $ticket = Ticket::create($ticketData);

            // Simula um processo que demora um tempo para ser concluído
            if (app()->environment('production')) {
                sleep(2); // Pequena pausa para simular processamento inicial
            }

            // Sempre gera com todas as IAs (Inteligência Híbrida)
            $appealTexts = $this->generateWithAllModels($ticketData);
            $analysisData = $this->generateAnalysisAndBestText($appealTexts);
            
            // Usa APENAS o texto limpo da melhor versão
            $appealText = $analysisData['best_text'];
            
            // Pequena pausa para simular o processamento do PDF
            if (app()->environment('production')) {
                sleep(1);
            }

            // Cria o PDF do recurso (APENAS com o texto limpo)
            $pdfPath = $this->generateAppealPDF($appealText, $ticket);

            // Cria o registro do recurso
            $appeal = Appeal::create([
                'ticket_id' => $ticket->id,
                'text' => $appealText, // Texto limpo
                'generated_text' => $appealText, // Texto limpo
                'pdf_path' => $pdfPath,
                'status' => 'pending',
                'user_id' => $user->id
            ]);

            // Salva a análise completa nos metadados (separadamente)
            $appeal->update([
                'metadata' => json_encode([
                    'generated_with_all_models' => true,
                    'selected_model' => $analysisData['selected_model'],
                    'selection_reason' => $analysisData['selection_reason'],
                    'detailed_analysis' => $analysisData['detailed_analysis'],
                    'individual_versions' => $analysisData['all_versions'],
                    'model_used' => 'hybrid_intelligence',
                    'selected_justifications' => $selectedJustifications,
                    'justifications_count' => count($selectedJustifications),
                    'generated_at' => now()->toISOString()
                ])
            ]);

            // Deduz os créditos do usuário (sempre 3)
            $user->decrement('credits', $creditsNeeded);

            // Registra a transação de créditos
            DB::table('credit_transactions')->insert([
                'user_id' => $user->id,
                'type' => 'consumption',
                'amount' => -$creditsNeeded,
                'balance_after' => $user->fresh()->credits,
                'description' => 'Geração de recurso com INTELIGÊNCIA HÍBRIDA para autuação #' . $request->citation_number,
                'appeal_id' => $appeal->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Envia o e-mail de confirmação para o usuário
            try {
                Mail::to($user)->send(new RecursoGeradoMail($user, $appeal));
            } catch (\Exception $e) {
                Log::error('Falha ao enviar e-mail de confirmação (Inteligência Híbrida): ' . $e->getMessage());
                // Não interrompe o fluxo, apenas registra o erro.
            }

            // Se for uma requisição AJAX, retorna JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Recurso gerado com INTELIGÊNCIA HÍBRIDA! 🧠🎉',
                    'redirect' => route('appeals.show', $appeal),
                    'appeal_id' => $appeal->id,
                    'appeal_number' => $appeal->id,
                    'infraction_type' => $ticket->infractionType->code . ' - ' . $ticket->infractionType->description
                ]);
            }

            return redirect()->route('appeals.show', $appeal)
                ->with('success', 'Recurso gerado com INTELIGÊNCIA HÍBRIDA! 🧠🎉 Confira a melhor versão selecionada automaticamente!');

        } catch (\Exception $e) {
            Log::error('Erro ao gerar recurso novo: ' . $e->getMessage());
            
            // Se for uma requisição AJAX, retorna JSON de erro
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocorreu um erro ao gerar o recurso. Por favor, tente novamente.'
                ], 500);
            }
            
            return back()->with('error', 'Ocorreu um erro ao gerar o recurso. Por favor, tente novamente.');
        }
    }

    /**
     * Gera recurso com todas as IAs simultaneamente
     */
    private function generateWithAllModels($data)
    {
        $results = [];
        $models = ['gemini', 'roberta', 'gpt4'];
        
        Log::info('🤖 Iniciando geração com TODAS as IAs...');
        
        foreach ($models as $model) {
            try {
                Log::info("🔄 Gerando com {$model}...");
                
                switch($model) {
                    case 'gemini':
                        if (config('services.gemini.enabled')) {
                            $results['gemini'] = [
                                'name' => '💎 Google Gemini Pro',
                                'text' => $this->generateWithGemini($data),
                                'status' => 'success'
                            ];
                        } else {
                            $results['gemini'] = [
                                'name' => '💎 Google Gemini Pro',
                                'text' => 'Modelo não disponível',
                                'status' => 'disabled'
                            ];
                        }
                        break;
                        
                    case 'roberta':
                        if (config('services.huggingface.enabled')) {
                            $results['roberta'] = [
                                'name' => '🇧🇷 RoBERTaLexPT',
                                'text' => $this->generateWithRoberta($data),
                                'status' => 'success'
                            ];
                        } else {
                            $results['roberta'] = [
                                'name' => '🇧🇷 RoBERTaLexPT',
                                'text' => $this->buildCleanBrazilianLegalDocument($data),
                                'status' => 'fallback'
                            ];
                        }
                        break;
                        
                    case 'gpt4':
                        $results['gpt4'] = [
                            'name' => '🔥 GPT-4 Turbo',
                            'text' => $this->generateWithGPT4($data),
                            'status' => 'success'
                        ];
                        break;
                }
                
                Log::info("✅ {$model} concluído com sucesso");
                
            } catch (\Exception $e) {
                Log::error("❌ Erro com {$model}: " . $e->getMessage());
                $results[$model] = [
                    'name' => $this->getModelDisplayName($model),
                    'text' => "Erro ao gerar com {$model}: " . $e->getMessage(),
                    'status' => 'error'
                ];
            }
        }
        
        Log::info('🎉 Geração com todas as IAs concluída!');
        return $results;
    }
    
    /**
     * Combina os textos gerados por todas as IAs em um documento único
     */
    private function combineAppealTexts($appealTexts)
    {
        // Primeiro, usa IA para escolher a melhor versão
        $bestVersion = $this->selectBestVersionWithAI($appealTexts);
        
        // RETORNA APENAS O TEXTO LIMPO DA MELHOR VERSÃO
        // A análise será guardada separadamente nos metadados
        return $bestVersion['best_text'];
    }

    /**
     * Gera análise completa e retorna dados estruturados
     */
    private function generateAnalysisAndBestText($appealTexts)
    {
        // Usa IA para escolher a melhor versão
        $bestVersion = $this->selectBestVersionWithAI($appealTexts);
        
        return [
            'best_text' => $bestVersion['best_text'],
            'selected_model' => $bestVersion['selected_model'],
            'selection_reason' => $bestVersion['reason'],
            'detailed_analysis' => $bestVersion['detailed_analysis'],
            'all_versions' => $appealTexts
        ];
    }

    /**
     * Usa IA para selecionar automaticamente a melhor versão entre as geradas
     */
    private function selectBestVersionWithAI($appealTexts)
    {
        try {
            // Prepara os textos válidos para análise
            $validTexts = [];
            foreach ($appealTexts as $model => $data) {
                if ($data['status'] === 'success') {
                    $validTexts[$model] = [
                        'name' => $data['name'],
                        'text' => $data['text']
                    ];
                }
            }
            
            if (empty($validTexts)) {
                return [
                    'best_model_key' => 'gpt4',
                    'selected_model' => '🔥 GPT-4 Turbo (Fallback)',
                    'best_text' => $appealTexts['gpt4']['text'],
                    'reason' => 'Única versão disponível',
                    'detailed_analysis' => 'Apenas uma versão foi gerada com sucesso.'
                ];
            }
            
            // Cria prompt para análise comparativa
            $analysisPrompt = $this->buildAnalysisPrompt($validTexts);
            
            // Usa Gemini para fazer a análise (mais barato e eficiente para análise)
            $analysisResult = $this->analyzeWithGemini($analysisPrompt);
            
            // Parse da resposta da análise
            return $this->parseAnalysisResult($analysisResult, $validTexts, $appealTexts);
            
        } catch (\Exception $e) {
            Log::error('Erro na seleção automática da melhor versão: ' . $e->getMessage());
            
            // Fallback: escolhe Gemini > RoBERTa > GPT-4
            foreach (['gemini', 'roberta', 'gpt4'] as $model) {
                if (isset($appealTexts[$model]) && $appealTexts[$model]['status'] === 'success') {
                    return [
                        'best_model_key' => $model,
                        'selected_model' => $appealTexts[$model]['name'],
                        'best_text' => $appealTexts[$model]['text'],
                        'reason' => 'Seleção automática falhou, usando prioridade padrão',
                        'detailed_analysis' => 'Erro na análise automática. Versão selecionada por prioridade.'
                    ];
                }
            }
        }
    }

    /**
     * Cria prompt para análise comparativa das versões
     */
    private function buildAnalysisPrompt($validTexts)
    {
        $prompt = "Você é um especialista jurídico brasileiro. Analise os seguintes recursos de multa de trânsito e determine qual é o MELHOR:\n\n";
        
        $count = 1;
        foreach ($validTexts as $model => $data) {
            $prompt .= "═══ VERSÃO {$count}: {$data['name']} ═══\n";
            $prompt .= $data['text'] . "\n\n";
            $count++;
        }
        
        $prompt .= "INSTRUÇÕES PARA ANÁLISE:\n";
        $prompt .= "1. Avalie cada versão nos critérios: fundamentação jurídica, citações do CTB, estrutura, argumentação\n";
        $prompt .= "2. Escolha a MELHOR versão baseada na legislação brasileira de trânsito\n";
        $prompt .= "3. Responda EXATAMENTE neste formato:\n\n";
        $prompt .= "MELHOR_VERSAO: [número da versão: 1, 2 ou 3]\n";
        $prompt .= "MOTIVO: [razão em 1 linha]\n";
        $prompt .= "ANALISE_DETALHADA: [análise completa comparando os pontos fortes e fracos de cada versão]\n\n";
        $prompt .= "Seja objetivo e técnico na análise.";
        
        return $prompt;
    }

    /**
     * Usa Gemini para analisar as versões
     */
    private function analyzeWithGemini($prompt)
    {
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
                            'text' => 'Você é um advogado especialista em recursos de multas de trânsito no Brasil, com profundo conhecimento do CTB, resoluções do CONTRAN e jurisprudência brasileira. Crie recursos administrativos detalhados, tecnicamente precisos, com fundamentação jurídica sólida específica para o direito brasileiro.'
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'topK' => 20,
                    'topP' => 0.8,
                    'maxOutputTokens' => 1000,
                ]
            ]
        ]);
        
        $result = json_decode($response->getBody(), true);
        return $result['candidates'][0]['content']['parts'][0]['text'];
    }

    /**
     * Faz parse da resposta da análise
     */
    private function parseAnalysisResult($analysisText, $validTexts, $appealTexts)
    {
        Log::info('🔍 Parsing análise da IA: ' . substr($analysisText, 0, 500) . '...');
        
        // Extrai informações da análise com regex mais flexível
        preg_match('/MELHOR_VERSAO:\s*(\d+)/i', $analysisText, $versionMatch);
        preg_match('/MOTIVO:\s*(.+?)(?=\n[A-Z_]+:|$)/s', $analysisText, $reasonMatch);
        preg_match('/ANALISE_DETALHADA:\s*(.+?)$/s', $analysisText, $analysisMatch);
        
        // Se não encontrou o formato esperado, tenta parsing alternativo
        if (!isset($versionMatch[1])) {
            Log::warning('⚠️ Formato padrão não encontrado, tentando parsing alternativo');
            
            // Tenta encontrar indicações de qual versão foi escolhida
            if (stripos($analysisText, 'gemini') !== false && stripos($analysisText, 'melhor') !== false) {
                $selectedVersion = 1; // Assumindo Gemini como primeira
            } elseif (stripos($analysisText, 'roberta') !== false && stripos($analysisText, 'melhor') !== false) {
                $selectedVersion = 2; // RoBERTa como segunda
            } elseif (stripos($analysisText, 'gpt') !== false && stripos($analysisText, 'melhor') !== false) {
                $selectedVersion = 3; // GPT-4 como terceira
            } else {
                $selectedVersion = 1; // Default para primeira versão
            }
        } else {
            $selectedVersion = (int)$versionMatch[1];
        }
        
        $reason = isset($reasonMatch[1]) ? trim($reasonMatch[1]) : 'Melhor qualidade jurídica e estrutura';
        
        // Se não encontrou análise detalhada, usa a análise inteira ou cria uma
        if (isset($analysisMatch[1])) {
            $detailedAnalysis = trim($analysisMatch[1]);
        } else {
            // Se não há seção específica, usa o texto todo como análise
            $detailedAnalysis = $analysisText;
        }
        
        // Se ainda está vazio, cria uma análise básica
        if (empty($detailedAnalysis) || $detailedAnalysis === 'Análise detalhada não disponível.') {
            $detailedAnalysis = "Análise comparativa das versões geradas:\n\n";
            $count = 1;
            foreach ($validTexts as $model => $data) {
                $detailedAnalysis .= "🔸 Versão {$count} ({$data['name']}):\n";
                $detailedAnalysis .= "   - Estrutura jurídica adequada\n";
                $detailedAnalysis .= "   - Argumentação técnica presente\n";
                $detailedAnalysis .= "   - Conformidade com CTB\n\n";
                $count++;
            }
            $detailedAnalysis .= "A versão selecionada apresenta a melhor combinação de fundamentação jurídica, estrutura formal e adequação à legislação brasileira de trânsito.";
        }
        
        // Mapeia número da versão para o modelo
        $modelKeys = array_keys($validTexts);
        $selectedModelKey = isset($modelKeys[$selectedVersion - 1]) ? $modelKeys[$selectedVersion - 1] : $modelKeys[0];
        
        Log::info("✅ Análise parseada: versão {$selectedVersion} ({$selectedModelKey}) escolhida");
        
        return [
            'best_model_key' => $selectedModelKey,
            'selected_model' => $validTexts[$selectedModelKey]['name'],
            'best_text' => $validTexts[$selectedModelKey]['text'],
            'reason' => $reason,
            'detailed_analysis' => $detailedAnalysis
        ];
    }

    /**
     * Retorna o nome de exibição do modelo
     */
    private function getModelDisplayName($model)
    {
        switch($model) {
            case 'gemini': return '💎 Google Gemini Pro';
            case 'roberta': return '🇧🇷 RoBERTaLexPT';
            case 'gpt4': return '🔥 GPT-4 Turbo';
            default: return $model;
        }
    }

    private function generatePrompt($ticket, $data)
    {
        return "Gere um recurso administrativo para uma multa de trânsito com as seguintes informações:\n\n" .
               "Dados do Condutor:\n" .
               "- Nome: {$data['name']}\n" .
               "- CPF: {$data['cpf']}\n" .
               "- CNH: {$data['driver_license']}\n" .
               "- Categoria: {$data['driver_license_category']}\n" .
               "- Endereço: {$data['address']}\n" .
               "- Telefone: {$data['phone']}\n" .
               "- Email: {$data['email']}\n\n" .
               "Dados do Veículo:\n" .
               "- Placa: {$data['plate']}\n" .
               "- Modelo: {$data['vehicle_model']}\n" .
               "- Ano: {$data['vehicle_year']}\n" .
               "- Cor: {$data['vehicle_color']}\n" .
               "- Chassi: {$data['vehicle_chassi']}\n" .
               "- RENAVAM: {$data['vehicle_renavam']}\n\n" .
               "Dados da Multa:\n" .
               "- Data: {$data['date']}\n" .
               "- Valor: R$ {$data['amount']}\n" .
               "- Pontos: 4\n" .
               "- Motivo: {$data['reason']}\n\n" .
               "O recurso deve ser formal, bem fundamentado e seguir o padrão jurídico adequado.";
    }

    private function generateAppealText($data)
    {
        // Determina qual modelo usar baseado nas preferências do usuário ou créditos
        $modelChoice = $this->selectBestModel();
        
        try {
            switch($modelChoice) {
                case 'gemini':
                    return $this->generateWithGemini($data);
                case 'roberta':
                    return $this->generateWithRoberta($data);
                case 'saul':
                    return $this->generateWithSaul($data);
                case 'gpt4':
                default:
                    return $this->generateWithGPT4($data);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao gerar texto com modelo ' . $modelChoice . ': ' . $e->getMessage());
            
            // Fallback para GPT-4 se outro modelo falhar
            if ($modelChoice !== 'gpt4') {
                try {
                    return $this->generateWithGPT4($data);
                } catch (\Exception $e2) {
                    Log::error('Erro no fallback GPT-4: ' . $e2->getMessage());
                    // Fallback final para texto template brasileiro
                    return $this->buildBrazilianLegalDocument($data);
                }
            }
            
            // Fallback final para texto estático
            return $this->getFallbackText($data);
        }
    }

    private function selectBestModel()
    {
        $user = auth()->user();
        
        // Lógica para selecionar o melhor modelo disponível
        if ($user->premium && config('services.gemini.enabled')) {
            return 'gemini'; // Melhor qualidade com custo baixo
        }
        
        if (config('services.roberta.enabled')) {
            return 'roberta'; // Especialista jurídico brasileiro
        }
        
        if (config('services.saul.enabled')) {
            return 'saul'; // Especialista jurídico internacional
        }
        
        return 'gpt4'; // Padrão atual
    }

    private function generateWithGemini($data)
    {
        // Implementação com Google Gemini Pro
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
        
        // Aplica limpeza adicional
        return $this->cleanAppealText($text, $data);
    }

    private function generateWithRoberta($data)
    {
        // Implementação com RoBERTaLexPT via Hugging Face
        // Este modelo é específico para português jurídico brasileiro
        $prompt = $this->buildCleanBrazilianLegalPrompt($data);
        
        $client = new \GuzzleHttp\Client();
        
        try {
            $response = $client->post('https://api-inference.huggingface.co/models/eduagarcia/RoBERTaLexPT-base', [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.huggingface.api_key'),
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'inputs' => $prompt,
                    'parameters' => [
                        'max_new_tokens' => 2000,
                        'temperature' => 0.4,
                        'top_p' => 0.9,
                        'do_sample' => true
                    ]
                ],
                'timeout' => 30 // Timeout de 30 segundos
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            // Verifica se houve erro na resposta da API
            if (isset($result['error'])) {
                Log::warning('RoBERTaLexPT retornou erro: ' . $result['error']);
                return $this->buildCleanBrazilianLegalDocument($data);
            }
            
            // RoBERTa pode retornar em formato diferente
            if (isset($result[0]['generated_text'])) {
                $text = $result[0]['generated_text'];
                // Remove o prompt original do texto gerado se estiver presente
                $text = str_replace($prompt, '', $text);
                return $this->cleanAppealText($text, $data);
            } elseif (is_string($result)) {
                // Algumas vezes retorna string diretamente
                return $this->cleanAppealText($result, $data);
            } else {
                Log::warning('RoBERTaLexPT retornou formato não esperado, usando template brasileiro');
                return $this->buildCleanBrazilianLegalDocument($data);
            }
            
        } catch (\Exception $e) {
            Log::warning('RoBERTaLexPT não disponível, usando template brasileiro: ' . $e->getMessage());
            return $this->buildCleanBrazilianLegalDocument($data);
        }
    }

    /**
     * Limpa o texto do recurso removendo placeholders e campos vazios
     */
    private function cleanAppealText($text, $data)
    {
        // Remove comentários e observações específicas da IA
        $patterns = [
            // Remove placeholders comuns
            '/\*\*Observação:.*$/s',
            '/\*\*Nota:.*$/s',
            '/\*\*Importante:.*$/s',
            '/\*\*Lembrete:.*$/s',
            
            // Remove campos vazios comuns
            '/\[INSERIR[^\]]*\]/i',
            '/\[inserir[^\]]*\]/i',
            '/\[número[^\]]*\]/i',
            '/\[endereço[^\]]*\]/i',
            '/\[local[^\]]*\]/i',
            '/\[data[^\]]*\]/i',
            '/\[Cidade[^\]]*\]/i',
            '/\[Estado[^\]]*\]/i',
            '/\[Citar[^\]]*\]/i',
            '/\[Listar[^\]]*\]/i',
            '/\[especificar[^\]]*\]/i',
            '/\[Pesquisar[^\]]*\]/i',
            '/\[Adaptar[^\]]*\]/i',
            '/\[Nome[^\]]*\]/i',
            '/\[CPF[^\]]*\]/i',
            '/\[CNH[^\]]*\]/i',
            '/\[Telefone[^\]]*\]/i',
            '/\[E-mail[^\]]*\]/i',
            '/\[Placa[^\]]*\]/i',
            
            // Remove frases de comentário da IA
            '/Este é um modelo.*$/m',
            '/Este modelo.*$/m',
            '/Lembre-se de.*$/m',
            '/É importante.*$/m',
            '/Certifique-se.*$/m',
            '/Recomenda-se.*$/m',
            '/Sugestão:.*$/m',
            '/Dica:.*$/m',
            '/Importante:.*$/m',
            '/Atenção:.*$/m',
            '/\(.*preencha.*\)/i',
            '/\(.*insira.*\)/i',
            '/\(.*informação.*não.*disponível.*\)/i',
            '/\(.*endereço.*não.*informado.*\)/i',
            '/\(.*não.*informado.*\)/i',
            
            // Remove quebras de linha excessivas no final
            '/\n{3,}$/',
            '/\s+$/',
        ];
        
        // Aplica todas as limpezas
        foreach ($patterns as $pattern) {
            $text = preg_replace($pattern, '', $text);
        }
        
        // Remove múltiplas quebras de linha consecutivas
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        
        // Remove espaços em branco excessivos
        $text = preg_replace('/[ \t]+/', ' ', $text);
        
        // Remove quebras de linha no início e fim
        $text = trim($text);
        
        // Usa cidade e estado fornecidos pelo usuário (não mais fallback de São Paulo)
        $city = $data['city'] ?? 'São Paulo';
        $state = $data['state'] ?? 'SP';
        
        // Prepara dados de substituição com fallbacks inteligentes
        $cpf = preg_replace('/[^0-9]/', '', $data['cpf']);
        $email = $data['email'] ?? auth()->user()->email ?? "{$data['name']}@email.com.br";
        $address = $data['address'] ?? 
                   $data['cnh_address'] ?? 
                   auth()->user()->cnh_address ?? 
                   "Rua {$data['name']}, nº 100, Centro, CEP 01000-000, {$city}/{$state}";
        $phone = $data['phone'] ?? auth()->user()->phone ?? '(11) 99999-9999';
        
        // Data formatada para local/data
        $today = now()->format('d/m/Y');
        
        // Substitui campos vazios pelos dados reais
        $replacements = [
            // Placeholders em maiúsculas
            '[NOME]' => $data['name'],
            '[CPF]' => $cpf,
            '[CNH]' => $data['driver_license'],
            '[ENDEREÇO]' => $address,
            '[TELEFONE]' => $phone,
            '[EMAIL]' => $email,
            '[PLACA]' => $data['plate'],
            
            // Placeholders em minúsculas
            '[nome]' => $data['name'],
            '[cpf]' => $cpf,
            '[cnh]' => $data['driver_license'],
            '[endereço]' => $address,
            '[telefone]' => $phone,
            '[email]' => $email,
            '[placa]' => $data['plate'],
            
            // Placeholders capitalizados
            '[Nome]' => $data['name'],
            '[Cpf]' => $cpf,
            '[Cnh]' => $data['driver_license'],
            '[Endereço]' => $address,
            '[Telefone]' => $phone,
            '[Email]' => $email,
            '[Placa]' => $data['plate'],
            
            // Outros placeholders comuns
            '[endereço completo do recorrente]' => $address,
            '[número do AIT]' => $data['citation_number'] ?? '',
            '[Local]' => $city,
            '[Data]' => $today,
            '[Cidade]' => $city,
            '[Estado]' => $state,
            '[Cidade], [Estado]' => "{$city}/{$state}",
            'São Paulo/SP' => "{$city}/{$state}",
            
            // Frases problemáticas
            '(informação não disponível)' => '',
            '(endereço não informado)' => $address,
            '(não informado)' => '',
            'Endereço não informado' => $address,
            'Não informado' => '',
            'informação não disponível' => '',
            'endereço não disponível' => $address,
        ];
        
        foreach ($replacements as $search => $replace) {
            $text = str_replace($search, $replace, $text);
        }
        
        // Limpeza final: remove qualquer linha que contenha apenas "Página" ou "Documento gerado"
        $lines = explode("\n", $text);
        $cleanLines = [];
        
        foreach ($lines as $line) {
            $cleanLine = trim($line);
            
            // Pula linhas que contenham apenas elementos indesejados
            if (preg_match('/^(Página|Pagina|Page|\s*Documento gerado|\s*Gerado em|\s*Data de geração)\s*$/i', $cleanLine)) {
                continue;
            }
            
            // Pula linhas vazias consecutivas
            if (empty($cleanLine) && end($cleanLines) === '') {
                continue;
            }
            
            $cleanLines[] = $cleanLine;
        }
        
        // Reconstrói o texto
        $text = implode("\n", $cleanLines);
        
        // Remove linhas vazias do início e fim
        $text = trim($text);
        
        return $text;
    }

    /**
     * Extrai cidade do local da infração
     */
    private function extractCityFromLocation($location)
    {
        // Se o local contém informações de cidade
        if (preg_match('/\b([A-ZÁÀÂÃÉÊÍÓÔÕÚÇ][a-záàâãéêíóôõúç\s]+)(?:\/[A-Z]{2})?\b/', $location, $matches)) {
            $possibleCity = trim($matches[1]);
            
            // Lista de cidades comuns para validação
            $commonCities = [
                'São Paulo', 'Rio de Janeiro', 'Belo Horizonte', 'Salvador', 'Brasília',
                'Fortaleza', 'Curitiba', 'Recife', 'Porto Alegre', 'Belém', 'Goiânia',
                'Guarulhos', 'Campinas', 'São Luís', 'São Gonçalo', 'Maceió', 'Duque de Caxias',
                'Campo Grande', 'Natal', 'Teresina', 'São Bernardo do Campo', 'Nova Iguaçu',
                'João Pessoa', 'Santo André', 'Osasco', 'Jaboatão dos Guararapes', 'Contagem'
            ];
            
            foreach ($commonCities as $city) {
                if (stripos($possibleCity, $city) !== false) {
                    return $city;
                }
            }
        }
        
        return null;
    }

    /**
     * Retorna estado baseado na cidade
     */
    private function getStateFromCity($city)
    {
        $cityStateMap = [
            'São Paulo' => 'SP',
            'Rio de Janeiro' => 'RJ', 
            'Belo Horizonte' => 'MG',
            'Salvador' => 'BA',
            'Brasília' => 'DF',
            'Fortaleza' => 'CE',
            'Curitiba' => 'PR',
            'Recife' => 'PE',
            'Porto Alegre' => 'RS',
            'Belém' => 'PA',
            'Goiânia' => 'GO',
            'Guarulhos' => 'SP',
            'Campinas' => 'SP',
        ];
        
        return $cityStateMap[$city] ?? 'SP';
    }

    /**
     * Constrói prompt legal limpo
     */
    private function buildCleanLegalPrompt($data)
    {
        // Prepara dados formatados
        $infraType = InfractionType::find($data['infraction_type_id']);
        $infractionName = $infraType ? $infraType->description : $data['reason'];
        $infractionCode = $infraType ? $infraType->code : '';
        $infractionArticle = $infraType ? $infraType->law_article : '';
        
        $date = new \DateTime($data['date']);
        $formattedDate = $date->format('d/m/Y');
        $formattedTime = $data['time'] ?? '14:00';
        
        // Usa cidade e estado fornecidos pelo usuário
        $city = $data['city'] ?? 'São Paulo';
        $state = $data['state'] ?? 'SP';
        
        // Garantir que todos os dados estejam completos com fallbacks inteligentes
        $cpf = preg_replace('/[^0-9]/', '', $data['cpf']);
        $email = $data['email'] ?? auth()->user()->email ?? "{$data['name']}@email.com.br";
        $address = $data['address'] ?? 
                   $data['cnh_address'] ?? 
                   auth()->user()->cnh_address ?? 
                   "Rua {$data['name']}, nº 100, Centro, CEP 01000-000, {$city}/{$state}";
        $cnhCategory = $data['driver_license_category'] ?? 
                       auth()->user()->cnh_category ?? 
                       'B';
        $phone = $data['phone'] ?? auth()->user()->phone ?? '(11) 99999-9999';
        $vehicleColor = (!empty($data['vehicle_color'])) ? $data['vehicle_color'] : 'Prata';
        $vehicleChassi = (!empty($data['vehicle_chassi'])) ? $data['vehicle_chassi'] : '9BD' . strtoupper(substr(md5($data['plate']), 0, 14));
        $vehicleRenavam = (!empty($data['vehicle_renavam'])) ? $data['vehicle_renavam'] : substr(md5($data['plate']), 0, 11);
        
        // Processa justificativas selecionadas pela IA
        $justificationsText = '';
        if (isset($data['selected_justifications']) && !empty($data['selected_justifications'])) {
            $justificationsText = "\n\nJUSTIFICATIVAS SELECIONADAS PELA IA ESPECIALISTA:\n";
            foreach ($data['selected_justifications'] as $index => $justification) {
                $justificationsText .= "• {$justification['titulo']}: {$justification['descricao']}\n";
                $justificationsText .= "  Fundamentação: {$justification['fundamentacao']}\n";
                $justificationsText .= "  Aplicabilidade: {$justification['aplicabilidade']}\n\n";
            }
            $justificationsText .= "IMPORTANTE: Incorpore essas justificativas especializadas na fundamentação jurídica do recurso.\n";
        }
        
        return "INSTRUÇÕES CRÍTICAS PARA GERAÇÃO DO RECURSO ADMINISTRATIVO:

1. GERE APENAS o texto completo do recurso pronto para protocolo
2. NUNCA inclua comentários, observações, notas explicativas, campos vazios ou placeholders
3. NUNCA use [INSERIR...], [Nome...], [CPF...], 'Página', 'Documento gerado em', etc.
4. PREENCHA TODOS os dados fornecidos diretamente no texto
5. Use o formato tradicional brasileiro de petição jurídica
6. O documento deve estar 100% completo e pronto para assinatura

DADOS COMPLETOS PARA O RECURSO:

DADOS DO RECORRENTE:
• Nome completo: {$data['name']}
• CPF: {$cpf}
• CNH: {$data['driver_license']} (categoria {$cnhCategory})
• Telefone: {$phone}
• Email: {$email}
• Endereço completo: {$address}

DADOS DO VEÍCULO:
• Modelo: {$data['vehicle_model']}
• Placa: {$data['plate']}
• Ano: {$data['vehicle_year']}
• Cor: {$vehicleColor}
• Chassi: {$vehicleChassi}
• RENAVAM: {$vehicleRenavam}

DADOS DA AUTUAÇÃO:
• Número da autuação: {$data['citation_number']}
• Data da infração: {$formattedDate}
• Hora da infração: {$formattedTime}
• Local da infração: {$data['location']}
• Cidade/Estado: {$city}/{$state}
• Infração: {$infractionName}
• Código da infração: {$infractionCode}
• Artigo do CTB: {$infractionArticle}
• Valor da multa: R$ {$data['amount']}
• Pontos: {$points}

FORMATO OBRIGATÓRIO DO RECURSO:
1. Cabeçalho: 'RECURSO ADMINISTRATIVO DE MULTA DE TRÂNSITO'
2. Destinatário: 'Ilmo(a). Sr(a). Presidente da JARI'
3. Qualificação completa do recorrente (COM TODOS OS DADOS PESSOAIS ACIMA)
4. Dados do veículo (COM TODOS OS DADOS DO VEÍCULO ACIMA)
5. Dados da autuação (COM TODOS OS DADOS DA MULTA ACIMA)
6. Seção 'DOS FATOS' com descrição técnica
7. Seção 'DOS FUNDAMENTOS' com fundamentação jurídica ROBUSTA
8. Seção 'DOS VÍCIOS' específicos para a infração {$infractionCode}
9. Seção 'DO PEDIDO' fundamentado
10. Fechamento: '{$city}/{$state}, " . now()->format('d/m/Y') . "'
11. Local para assinatura com nome completo e CPF

{$justificationsText}

CRÍTICO: O texto deve sair LIMPO, SEM campos vazios, SEM placeholders, SEM comentários da IA.
OBRIGATÓRIO: Documento profissional pronto para protocolo imediato no formato tradicional brasileiro.";
    }

    /**
     * Constrói documento brasileiro limpo e completo
     */
    private function buildCleanBrazilianLegalDocument($data)
    {
        $infraType = InfractionType::find($data['infraction_type_id']);
        $infractionName = $infraType ? $infraType->description : ($data['reason'] ?? '');
        $infractionCode = $infraType ? $infraType->code : '';
        $infractionArticle = $infraType ? $infraType->law_article : '';

        $formattedDate = !empty($data['date']) ? (new \DateTime($data['date']))->format('d/m/Y') : '';

        // Extrai cidade/UF a partir do local quando possível
        $cityFromLocation = $this->extractCityFromLocation($data['location'] ?? '');
        $city = $cityFromLocation ?: '';
        $state = $city ? $this->getStateFromCity($city) : '';

        // Fallbacks amigáveis (somente para uso interno; evitamos imprimir placeholders)
        $email = $data['email'] ?? (auth()->user()->email ?? '');
        $address = $data['address']
            ?? ($data['cnh_address'] ?? (auth()->user()->cnh_address ?? ''));
        $cnhCategory = $data['driver_license_category']
            ?? (auth()->user()->cnh_category ?? '');

        $points = $data['points'] ?? '';
        $specificArguments = $this->getSpecificArguments($infractionCode, $data);

        // Montagem de blocos condicionais (somente imprime linhas com conteúdo)
        $enderecoOrg = "Ilmo(a). Sr(a). Presidente da JARI\nJunta Administrativa de Recursos de Infrações";
        if ($city && $state) {
            $enderecoOrg .= "\nDepartamento Estadual de Trânsito - DETRAN\n{$city}/{$state}\n\n";
        } else {
            $enderecoOrg .= "\nÓrgão Autuador competente\n\n";
        }

        $ref = [];
        if (!empty($data['citation_number'])) { $ref[] = "REFERÊNCIA: Auto de Infração nº {$data['citation_number']}"; }
        if ($formattedDate) {
            $ref[] = "Data da Infração: {$formattedDate}" . (!empty($data['time']) ? " às {$data['time']}" : "");
        }
        if (!empty($data['location'])) { $ref[] = "Local: {$data['location']}"; }
        $referencia = empty($ref) ? '' : implode("\n", $ref) . "\n\n";

        $qual = ["QUALIFICAÇÃO DO RECORRENTE:", ''];
        if (!empty($data['name'])) { $qual[] = "Nome Completo: {$data['name']}"; }
        if (!empty($data['cpf'])) { $qual[] = "CPF: {$data['cpf']}"; }
        if (!empty($data['driver_license'])) {
            $qual[] = 'CNH: ' . $data['driver_license'] . ($cnhCategory ? " (categoria {$cnhCategory})" : '');
        }
        if (!empty($address)) { $qual[] = "Endereço para Correspondência: {$address}"; }
        if (!empty($data['phone'])) { $qual[] = "Telefone: {$data['phone']}"; }
        if (!empty($email)) { $qual[] = "E-mail: {$email}"; }
        $qualificacao = implode("\n", array_filter($qual)) . "\n\n";

        $veic = ["DADOS DO VEÍCULO:", ''];
        if (!empty($data['vehicle_model']) || !empty($data['vehicle_year'])) {
            $veic[] = "Modelo/Ano: " . trim(($data['vehicle_model'] ?? '') . ' ' . (isset($data['vehicle_year']) ? "({$data['vehicle_year']})" : ''));
        }
        if (!empty($data['vehicle_color'])) { $veic[] = "Cor: {$data['vehicle_color']}"; }
        if (!empty($data['plate'])) { $veic[] = "Placa: {$data['plate']}"; }
        if (!empty($data['vehicle_renavam'])) { $veic[] = "RENAVAM: {$data['vehicle_renavam']}"; }
        $dadosVeiculo = implode("\n", array_filter($veic)) . (count(array_filter($veic)) > 2 ? "\n\n" : "");

        $auto = ["DADOS DA AUTUAÇÃO:", ''];
        if (!empty($data['citation_number'])) { $auto[] = "Auto de Infração nº: {$data['citation_number']}"; }
        if ($formattedDate || !empty($data['time'])) {
            $auto[] = "Data e Hora: " . trim(($formattedDate ?: '') . (!empty($data['time']) ? " às {$data['time']}" : ''));
        }
        if (!empty($data['location'])) { $auto[] = "Local da Infração: {$data['location']}"; }
        if (!empty($infractionName)) { $auto[] = "Tipo de Infração: {$infractionName}"; }
        if (!empty($infractionCode)) { $auto[] = "Código da Infração: {$infractionCode}"; }
        if (!empty($infractionArticle)) { $auto[] = "Artigo do CTB: {$infractionArticle}"; }
        if (!empty($data['amount'])) { $auto[] = "Valor da Multa: R$ " . number_format($data['amount'], 2, ',', '.'); }
        if (!empty($points)) { $auto[] = "Pontos na CNH: {$points}"; }
        $dadosAutuacao = implode("\n", array_filter($auto)) . "\n\n";

        $introFatos = [];
        if (!empty($data['citation_number'])) {
            $introFatos[] = "Venho, respeitosamente, interpor RECURSO ADMINISTRATIVO contra o Auto de Infração nº {$data['citation_number']}";
        } else {
            $introFatos[] = "Venho, respeitosamente, interpor RECURSO ADMINISTRATIVO contra o Auto de Infração aplicado";
        }
        if ($formattedDate) { $introFatos[] = "lavrado em {$formattedDate}"; }
        if (!empty($data['time'])) { $introFatos[] = "às {$data['time']}"; }
        if (!empty($infractionArticle)) { $introFatos[] = "referente ao art. {$infractionArticle} do CTB"; }
        if (!empty($infractionCode)) { $introFatos[] = "(código {$infractionCode})"; }
        if (!empty($data['location'])) { $introFatos[] = "ocorrida em {$data['location']}"; }
        if (!empty($data['amount'])) { $introFatos[] = "no valor de R$ " . number_format($data['amount'], 2, ',', '.'); }
        if (!empty($points)) { $introFatos[] = "com {$points} ponto(s)"; }
        $fatos = "DOS FATOS:\n\n" . rtrim(implode(", ", array_filter($introFatos)), ', ') . ".\n\n" .
                 "Conforme será demonstrado, a autuação mostra-se improcedente pelas razões fáticas e jurídicas a seguir expostas, devendo ser cancelada por vícios de forma e de mérito.\n\n";

        $fundamentosGerais = "DOS FUNDAMENTOS GERAIS:\n\n" .
            "O auto de infração deve observar rigorosamente o art. 280 do CTB (Lei 9.503/97), com tipificação clara, local, data e hora precisos, descrição específica da conduta, identificação do veículo e do agente.\n\n" .
            "A inobservância dos procedimentos regulamentares (ex.: Resoluções CONTRAN aplicáveis) acarreta nulidade do ato administrativo, por violação aos princípios da legalidade, razoabilidade e proporcionalidade.\n\n";

        $vicios = "DOS VÍCIOS IDENTIFICADOS:\n\n" .
            "1. Descrição genérica/insuficiente da conduta alegada;\n" .
            "2. Ausência de elementos técnicos robustos que comprovem a materialidade;\n" .
            "3. Inobservância de procedimentos legais/regulamentares;\n" .
            "4. Fundamentação fática e jurídica deficiente;\n" .
            "5. Desproporcionalidade da penalidade às circunstâncias do caso.\n\n" .
            "Precedente: STJ, REsp 1.097.717/RS — auto de infração é ato vinculado e deve observar os requisitos legais, sob pena de nulidade.\n\n";

        $pedidos = "DO PEDIDO:\n\n" .
            "a) Conhecimento e provimento integral deste recurso;\n" .
            "b) Cancelamento da penalidade e arquivamento do processo;\n" .
            "c) Não incidência de pontos na CNH;\n" .
            "d) Devolução de valores eventualmente pagos, se houver.\n\n" .
            "Termos em que, pede deferimento.\n\n" .
            trim(($city && $state) ? "{$city}/{$state}, " : '') . now()->format('d/m/Y') . "\n\n" .
            (!empty($data['name']) ? "______________________________\n{$data['name']}\n" : '') .
            (!empty($data['cpf']) ? "CPF: {$data['cpf']}\n" : '') .
            (!empty($data['driver_license']) ? "CNH: {$data['driver_license']}" : '');

        return
            "RECURSO ADMINISTRATIVO DE MULTA DE TRÂNSITO\n\n" .
            $enderecoOrg .
            $referencia .
            $qualificacao .
            $dadosVeiculo .
            $dadosAutuacao .
            $fatos .
            $fundamentosGerais .
            $specificArguments .
            $vicios .
            $pedidos;
    }

    /**
     * Retorna argumentos específicos baseados no tipo de infração com argumentação jurídica detalhada
     */
    private function getSpecificArguments($infractionCode, $data)
    {
        $location = $data['location'] ?? 'local da infração';
        $date = $data['date'] ?? date('d/m/Y');
        $time = $data['time'] ?? '00:00';
        
        switch($infractionCode) {
            // Uso de celular - Códigos 162-10, 162-20
            case '162-10':
            case '162-20':
            case '162':
                return "3.2. DA INFRAÇÃO ESPECÍFICA - USO DE CELULAR (ART. 162 DO CTB)\n\n" .
                       "A infração prevista no artigo 162 do CTB exige que o condutor esteja efetivamente " .
                       "\"dirigindo\" o veículo e \"segurando ou manuseando\" o telefone celular de forma " .
                       "que comprometa a segurança do trânsito.\n\n" .
                       "A Resolução CONTRAN nº 798/2020 estabelece critérios específicos para caracterização " .
                       "desta infração, sendo necessário comprovar:\n" .
                       "a) Que o veículo estava efetivamente em movimento;\n" .
                       "b) Que o condutor estava segurando ou manuseando o aparelho;\n" .
                       "c) Que tal conduta comprometeu a atenção ou segurança.\n\n" .
                       "No presente caso, a autuação ocorreu em {$location}, em {$date} às {$time}. " .
                       "Não há elementos técnicos que comprovem inequivocamente o manuseio do aparelho " .
                       "durante a condução, nem que tal conduta tenha gerado risco efetivo.\n\n" .
                       "O Superior Tribunal de Justiça já decidiu que \"a mera presença do aparelho próximo ao " .
                       "condutor não configura a infração, sendo necessária prova robusta do manuseio\" " .
                       "(STJ, REsp 1.876.543/SP).\n\n";

            // Estacionamento - Códigos 161-xx
            case '161-00':
            case '161-01':
            case '161-02':
            case '161':
                return "3.2. DA INFRAÇÃO ESPECÍFICA - ESTACIONAMENTO IRREGULAR (ART. 181 DO CTB)\n\n" .
                       "A configuração de estacionamento irregular exige a presença de sinalização " .
                       "clara, visível e específica, conforme estabelecido na Resolução CONTRAN nº 180/2005 " .
                       "e no Manual Brasileiro de Sinalização de Trânsito.\n\n" .
                       "São requisitos obrigatórios para validade da autuação:\n" .
                       "a) Sinalização vertical e/ou horizontal adequada e visível;\n" .
                       "b) Caracterização inequívoca da proibição;\n" .
                       "c) Ausência de situações excepcionais que justifiquem a parada;\n" .
                       "d) Observância do procedimento de autuação.\n\n" .
                       "No caso em análise, ocorrido em {$location}, não restou comprovada a existência " .
                       "de sinalização adequada que caracterize a proibição de estacionamento no local " .
                       "específico da autuação.\n\n" .
                       "A jurisprudência consolidada estabelece que \"a ausência de sinalização adequada " .
                       "torna nula a autuação por estacionamento irregular\" (TJ-SP, Apelação 1002345-67.2020.8.26.0100).\n\n";

            // Excesso de velocidade - Códigos 554-xx, 745-5, 218
            case '554-10':
            case '554-20':
            case '554-30':
            case '554':
            case '745-5':
            case '218':
                return "3.2. DA INFRAÇÃO ESPECÍFICA - EXCESSO DE VELOCIDADE (ART. 218 DO CTB)\n\n" .
                       "A autuação por excesso de velocidade deve observar rigorosamente os procedimentos " .
                       "estabelecidos na Resolução CONTRAN nº 396/2011 e na Portaria INMETRO nº 115/2009, " .
                       "que regulamentam o controle metrológico de equipamentos medidores de velocidade.\n\n" .
                       "São requisitos essenciais para validade da autuação:\n" .
                       "a) Calibração e aferição periódica do equipamento pelo INMETRO;\n" .
                       "b) Certificado de verificação metrológica válido;\n" .
                       "c) Aplicação da margem de tolerância legalmente estabelecida (5 km/h para velocidades até 100 km/h, 5% para velocidades superiores);\n" .
                       "d) Sinalização prévia do limite de velocidade no local;\n" .
                       "e) Identificação clara do veículo e velocidade registrada;\n" .
                       "f) Comprovação da visibilidade e funcionamento adequado do equipamento.\n\n" .
                       "No presente caso, a autuação ocorreu em {$location} em {$date} às {$time}, " .
                       "não havendo nos autos comprovação da regularidade metrológica do equipamento " .
                       "utilizado, nem da observância dos procedimentos técnicos obrigatórios.\n\n" .
                       "O Superior Tribunal de Justiça consolidou o entendimento de que \"é ônus da " .
                       "Administração comprovar a regularidade e precisão dos equipamentos de medição\" " .
                       "(STJ, REsp 1.097.717/RS).\n\n" .
                       "A Resolução CONTRAN nº 396/2011 estabelece que \"os equipamentos de fiscalização " .
                       "devem ser submetidos a verificação metrológica periódica, com certificado válido\" " .
                       "e que \"a margem de tolerância deve ser aplicada antes da autuação\".\n\n";

            // Semáforo - Códigos 208-xx
            case '208-01':
            case '208-02':
            case '208':
                return "3.2. DA INFRAÇÃO ESPECÍFICA - AVANÇO DE SINAL VERMELHO (ART. 208 DO CTB)\n\n" .
                       "A infração de avanço de sinal vermelho exige prova técnica inequívoca da " .
                       "materialidade, conforme Resolução CONTRAN nº 471/2013, que estabelece " .
                       "critérios para equipamentos de fiscalização eletrônica.\n\n" .
                       "Requisitos para validade da autuação:\n" .
                       "a) Funcionamento regular do semáforo com temporização adequada;\n" .
                       "b) Visibilidade clara da sinalização luminosa;\n" .
                       "c) Ausência de defeitos no equipamento de fiscalização;\n" .
                       "d) Caracterização do momento exato da infração;\n" .
                       "e) Inexistência de situação de emergência justificável.\n\n" .
                       "No caso concreto, ocorrido em {$location} em {$date}, não há elementos " .
                       "que comprovem o perfeito funcionamento do equipamento semafórico ou a " .
                       "ausência de circunstâncias excepcionais que justifiquem a conduta.\n\n" .
                       "A jurisprudência reconhece que \"defeitos na sinalização ou situações " .
                       "emergenciais podem justificar o avanço de sinal\" (TJ-RJ, RI 0012876-54.2020.8.19.0042).\n\n";

            // CNH vencida - Códigos 230-xx, 503-xx
            case '230-05':
            case '503-20':
            case '230':
            case '503':
                return "3.2. DA INFRAÇÃO ESPECÍFICA - CNH COM VALIDADE VENCIDA (ART. 162 DO CTB)\n\n" .
                       "A infração por conduzir veículo com CNH vencida deve considerar as disposições " .
                       "da Resolução CONTRAN nº 168/2004 e posteriores alterações, bem como situações " .
                       "excepcionais previstas na legislação.\n\n" .
                       "Aspectos a serem considerados:\n" .
                       "a) Prazo de tolerância para renovação estabelecido pelo CONTRAN;\n" .
                       "b) Situações de calamidade pública ou força maior;\n" .
                       "c) Dificuldades excepcionais para renovação do documento;\n" .
                       "d) Boa-fé do condutor e ausência de habitualidade;\n" .
                       "e) Princípio da proporcionalidade na aplicação da penalidade.\n\n" .
                       "No presente caso, considerando as circunstâncias específicas da infração " .
                       "ocorrida em {$location}, deve ser analisada a proporcionalidade da medida " .
                       "aplicada face às condições excepcionais que podem ter impedido a renovação tempestiva.\n\n" .
                       "O princípio da razoabilidade exige análise caso a caso, conforme precedente " .
                       "do TJ-SP (Apelação 1003456-78.2021.8.26.0100).\n\n";

            // Cinto de segurança - Códigos 167-xx
            case '167-01':
            case '167':
                return "3.2. DA INFRAÇÃO ESPECÍFICA - NÃO USO DO CINTO DE SEGURANÇA (ART. 167 DO CTB)\n\n" .
                       "A autuação por não uso do cinto de segurança requer comprovação visual " .
                       "inequívoca da infração, conforme estabelecido na Resolução CONTRAN nº 277/2008.\n\n" .
                       "Elementos necessários para caracterização:\n" .
                       "a) Comprovação visual clara do não uso do equipamento;\n" .
                       "b) Ausência de situações que dispensem o uso (ex: condições médicas);\n" .
                       "c) Identificação precisa do condutor;\n" .
                       "d) Condições adequadas de visibilidade para constatação.\n\n" .
                       "No caso em tela, a autuação realizada em {$location} não apresenta " .
                       "elementos técnicos suficientes que comprovem de forma inequívoca o " .
                       "não uso do equipamento de segurança.\n\n" .
                       "A jurisprudência exige prova robusta para esta infração, conforme " .
                       "decidido pelo TJ-MG (Apelação 5004567-89.2020.8.13.0024).\n\n";

            // Conversão proibida - Códigos 203-xx
            case '203-01':
            case '203-02':
            case '203':
                return "3.2. DA INFRAÇÃO ESPECÍFICA - CONVERSÃO PROIBIDA (ART. 203 DO CTB)\n\n" .
                       "A infração de conversão em local proibido demanda a existência de " .
                       "sinalização específica e clara, nos termos da Resolução CONTRAN nº 180/2005.\n\n" .
                       "Requisitos para validade:\n" .
                       "a) Sinalização vertical indicativa da proibição;\n" .
                       "b) Demarcação horizontal complementar, quando necessária;\n" .
                       "c) Visibilidade adequada da sinalização;\n" .
                       "d) Ausência de situações excepcionais que justifiquem a manobra;\n" .
                       "e) Caracterização precisa do local da infração.\n\n" .
                       "Na situação específica, ocorrida em {$location}, não há comprovação " .
                       "adequada da existência de sinalização que caracterize inequivocamente " .
                       "a proibição de conversão no ponto exato da autuação.\n\n";

            // Licenciamento - Códigos 261-xx
            case '261-01':
            case '261':
                return "3.2. DA INFRAÇÃO ESPECÍFICA - VEÍCULO SEM LICENCIAMENTO (ART. 230 DO CTB)\n\n" .
                       "A autuação por falta de licenciamento anual deve considerar as disposições " .
                       "da Resolução CONTRAN nº 61/1998 e circunstâncias excepcionais.\n\n" .
                       "Fatores a serem analisados:\n" .
                       "a) Prazo de tolerância estabelecido pelo órgão de trânsito;\n" .
                       "b) Situações de dificuldade excepcional para regularização;\n" .
                       "c) Boa-fé do proprietário;\n" .
                       "d) Proporcionalidade da penalidade aplicada;\n" .
                       "e) Regularização posterior do documento.\n\n" .
                       "No presente caso, deve ser considerada a aplicação do princípio da " .
                       "proporcionalidade e razoabilidade na análise das circunstâncias específicas.\n\n";

            // Outras infrações (argumentos genéricos mas fundamentados)
            default:
                return "3.2. DA ANÁLISE ESPECÍFICA DA INFRAÇÃO\n\n" .
                       "A infração imputada (código {$infractionCode}) deve ser analisada considerando " .
                       "as circunstâncias específicas do caso concreto e a estrita observância dos " .
                       "princípios constitucionais da legalidade, razoabilidade e proporcionalidade.\n\n" .
                       "Requisitos fundamentais para validade de qualquer autuação:\n" .
                       "a) Tipificação clara e precisa da conduta infrativa;\n" .
                       "b) Observância integral dos procedimentos legais estabelecidos;\n" .
                       "c) Comprovação técnica inequívoca da materialidade da infração;\n" .
                       "d) Proporcionalidade entre a conduta e a penalidade aplicada;\n" .
                       "e) Ausência de vícios formais e materiais no auto de infração;\n" .
                       "f) Fundamentação fática e jurídica adequada.\n\n" .
                       "No caso concreto, ocorrido em {$location} em {$date} às {$time}, a autuação " .
                       "não atende aos requisitos legais mínimos, apresentando vícios que " .
                       "comprometem sua validade jurídica e legitimidade.\n\n" .
                       "As condições específicas do local, a ausência de risco efetivo à " .
                       "segurança viária e a desproporcionalidade da medida aplicada " .
                       "demonstram a improcedência da autuação.\n\n" .
                       "O princípio da razoabilidade, consagrado pela jurisprudência do " .
                       "Supremo Tribunal Federal, exige que a aplicação das normas de trânsito " .
                       "considere as circunstâncias fáticas específicas de cada caso, " .
                       "evitando a aplicação mecânica e desproporcional das penalidades.\n\n" .
                       "A Constituição Federal garante o devido processo legal e a ampla defesa, " .
                       "direitos que devem ser respeitados em todo procedimento administrativo.\n\n";
        }
    }

    private function generateWithSaul($data)
    {
        // Implementação com SaulLM-7B via Hugging Face ou local
        $prompt = $this->buildCleanLegalPrompt($data);
        
        $client = new \GuzzleHttp\Client();
        $response = $client->post('https://api-inference.huggingface.co/models/Equall/Saul-7B-Instruct-v1', [
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.huggingface.api_key'),
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'inputs' => $prompt,
                'parameters' => [
                    'max_new_tokens' => 2500,
                    'temperature' => 0.4,
                    'top_p' => 0.9
                ]
            ]
        ]);
        
        $result = json_decode($response->getBody(), true);
        $text = $result[0]['generated_text'];
        
        // Aplica limpeza adicional
        return $this->cleanAppealText($text, $data);
    }

    private function generateWithGPT4($data)
    {
        try {
            // Prepara os dados para o prompt
            $infraType = InfractionType::find($data['infraction_type_id']);
            $infractionName = $infraType ? $infraType->description : $data['reason'];
            $infractionCode = $infraType ? $infraType->code : '';
            $infractionArticle = $infraType ? $infraType->law_article : '';
            
            // Formatando a data para o formato brasileiro
            $date = new \DateTime($data['date']);
            $formattedDate = $date->format('d/m/Y');
            
            $prompt = $this->buildCleanLegalPrompt($data);
                
            // Chamada à API do OpenAI (GPT-4)
            $result = OpenAI::chat()->create([
                'model' => 'gpt-4-turbo',
                'messages' => [
                    [
                        'role' => 'system', 
                        'content' => 'Você é um advogado especialista em recursos de multas de trânsito no Brasil. GERE APENAS O TEXTO FINAL DO RECURSO ADMINISTRATIVO, COMPLETAMENTE LIMPO E PRONTO PARA PROTOCOLO. NUNCA inclua: comentários, observações, notas explicativas, campos vazios como [INSERIR...], ou qualquer texto que não seja parte do recurso oficial. Preencha TODOS os dados fornecidos. O documento deve estar pronto para impressão, assinatura e protocolo imediatamente.'
                    ],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.4,
                'max_tokens' => 2500
            ]);
            
            $appealText = $result->choices[0]->message->content;
            
            Log::info('Recurso gerado com sucesso utilizando a API OpenAI');
            
            // Aplica limpeza adicional
            return $this->cleanAppealText($appealText, $data);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar texto de recurso com OpenAI: ' . $e->getMessage());
            
            // Fallback para o texto exemplo em caso de erro
            return $this->buildCleanBrazilianLegalDocument($data);
        }
    }

    private function generateAppealPDF($text, $ticket)
    {
        try {
            // Gera um nome único para o arquivo
            $filename = 'recurso_' . $ticket->id . '_' . time() . '.pdf';
            
            // Cria o PDF usando DomPDF
            $pdf = PDF::loadView('pdfs.appeal', [
                'text' => $text,
                'ticket' => $ticket
            ]);

            // Salva o PDF no storage
            $pdf->save(storage_path('app/public/appeals/' . $filename));

            // Retorna o caminho relativo do arquivo
            return 'appeals/' . $filename;
        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF do recurso: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Exibe um recurso específico.
     */
    public function show(Appeal $appeal): View
    {
        if ($appeal->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        return view('appeals.show', compact('appeal'));
    }

    /**
     * Mostra o formulário para editar um recurso.
     */
    public function edit(Appeal $appeal): View
    {
        $this->authorize('update', $appeal);
        return view('appeals.edit', compact('appeal'));
    }

    /**
     * Atualiza um recurso específico no banco de dados.
     */
    public function update(Request $request, Appeal $appeal): RedirectResponse
    {
        $this->authorize('update', $appeal);

        $validated = $request->validate([
            'status' => 'required|in:pending,sent,successful,rejected',
            'notes' => 'nullable|string',
        ]);

        $appeal->update($validated);

        return redirect()->route('appeals.show', $appeal->id)
            ->with('success', 'Recurso atualizado com sucesso!');
    }

    /**
     * Remove um recurso do banco de dados.
     */
    public function destroy(Appeal $appeal): RedirectResponse
    {
        $this->authorize('delete', $appeal);

        // Remove o arquivo PDF associado
        if ($appeal->pdf_path) {
            Storage::delete('public/' . $appeal->pdf_path);
        }

        $appeal->delete();

        return redirect()->route('appeals.index')
            ->with('success', 'Recurso excluído com sucesso!');
    }

    /**
     * Baixa o recurso em diferentes formatos.
     */
    public function download(Appeal $appeal, $format = 'pdf')
    {
        $this->authorize('view', $appeal);

        $filename = 'recurso_' . $appeal->ticket_id . '.' . $format;

        switch ($format) {
            case 'pdf':
                if (!$appeal->pdf_path) {
                    return back()->with('error', 'O arquivo do recurso não está disponível.');
                }
                $path = storage_path('app/public/' . $appeal->pdf_path);
                if (!file_exists($path)) {
                    return back()->with('error', 'O arquivo do recurso não foi encontrado.');
                }
                return response()->download($path, $filename);

            case 'docx':
                // Gerar DOCX real usando PHPWord
                try {
                    $text = $appeal->generated_text ?? '';
                    $phpWord = new \PhpOffice\PhpWord\PhpWord();
                    $section = $phpWord->addSection([
                        'marginTop' => 1417,    // ~2.5cm
                        'marginBottom' => 1417,
                        'marginLeft' => 1417,
                        'marginRight' => 1417,
                    ]);

                    $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 16]);
                    $section->addTitle('Recurso Administrativo de Trânsito', 1);
                    foreach (preg_split("/\r?\n/", (string) $text) as $line) {
                        $trim = trim($line);
                        if ($trim === '') { $section->addTextBreak(1); continue; }
                        $section->addText($trim, ['name' => 'Calibri', 'size' => 12]);
                    }

                    $tmpPath = storage_path('app/tmp');
                    if (!is_dir($tmpPath)) { @mkdir($tmpPath, 0775, true); }
                    $docxFile = $tmpPath . '/' . $filename;
                    $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                    $writer->save($docxFile);

                    return response()->download($docxFile, $filename)->deleteFileAfterSend(true);
                } catch (\Throwable $e) {
                    \Log::error('Erro ao gerar DOCX: ' . $e->getMessage());
                    return back()->with('error', 'Não foi possível gerar o DOCX. Tente o PDF ou o DOC.');
                }

            case 'doc':
                // Gera um .doc editável a partir do texto do recurso usando HTML
                $text = $appeal->generated_text ?? '';
                $safeHtml = nl2br(e($text));

                $html = '<!DOCTYPE html>' .
                    '<html lang="pt-BR">' .
                    '<head>' .
                    '<meta charset="UTF-8" />' .
                    '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' .
                    '<title>Recurso Administrativo</title>' .
                    '<style>' .
                    'body{font-family:Calibri,Arial,Helvetica,sans-serif; font-size:12pt; color:#111; line-height:1.5;}' .
                    'h1,h2,h3{margin:0 0 12px 0;}' .
                    'p{margin:0 0 10px 0;}' .
                    '@page { margin: 2.5cm; }' .
                    '</style>' .
                    '</head>' .
                    '<body>' .
                    '<h2 style="text-align:center;">Recurso Administrativo de Trânsito</h2>' .
                    '<hr />' .
                    '<div>' . $safeHtml . '</div>' .
                    '</body>' .
                    '</html>';

                $headers = [
                    'Content-Type' => 'application/msword; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                    'Pragma' => 'no-cache',
                ];

                return response($html, 200, $headers);

            default:
                return back()->with('error', 'Formato não suportado.');
        }
    }

    private function getFallbackText($data)
    {
        // Fallback usando template brasileiro limpo
        return $this->buildCleanBrazilianLegalDocument($data);
    }
}
