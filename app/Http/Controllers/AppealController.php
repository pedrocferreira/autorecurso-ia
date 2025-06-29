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
                'phone' => 'required|string|max:20',
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
                'phone' => 'required|string|max:20',
                'plate' => 'required|string|max:7',
                'vehicle_model' => 'required|string|max:100',
                'vehicle_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'vehicle_color' => 'required|string|max:50',
                'vehicle_chassi' => 'required|string|max:17',
                'vehicle_renavam' => 'required|string|max:11',
                'citation_number' => 'required|string|max:50',
                'date' => 'required|date',
                'time' => 'required',
                'infraction_type_id' => 'required|exists:infraction_types,id',
                'amount' => 'required|numeric|min:0',
                'points' => 'required|integer|min:0',
                'location' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'state' => 'required|string|size:2',
                'reason' => 'required|string|max:1000',
                'custom_details' => 'nullable|string|max:2000'
            ], [
                'vehicle_chassi.max' => 'O campo chassi deve ter no máximo 17 caracteres.',
                'vehicle_renavam.max' => 'O campo RENAVAM deve ter no máximo 11 caracteres.',
                'custom_details.max' => 'Os detalhes específicos devem ter no máximo 2000 caracteres.',
                'city.required' => 'A cidade da infração é obrigatória.',
                'state.required' => 'O estado da infração é obrigatório.',
                'state.size' => 'O estado deve ter exatamente 2 caracteres (ex: SP, RJ).'
            ]);

            if ($validator->fails()) {
                Log::error('Erro de validação ao gerar recurso novo:', $validator->errors()->toArray());
                return back()->withErrors($validator)->withInput();
            }

            // Verifica se o usuário tem créditos suficientes (sempre 3 para Inteligência Híbrida)
            $user = auth()->user();
            $creditsNeeded = 3; // Sempre usa Inteligência Híbrida
            
            if ($user->credits < $creditsNeeded) {
                return back()->with('error', "Você precisa de {$creditsNeeded} créditos para gerar um recurso com Inteligência Híbrida. Você tem apenas {$user->credits} crédito(s).");
            }

            // Primeiro, cria um ticket temporário com os dados fornecidos
            $ticketData = array_merge($request->all(), [
                'user_id' => $user->id,
                'email' => $user->email, // Pega do perfil do usuário
                'address' => $user->cnh_address ?? 'Endereço não informado', // Pega do perfil
                'driver_license_category' => $user->cnh_category ?? 'B' // Pega do perfil
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

            return redirect()->route('appeals.show', $appeal)
                ->with('success', 'Recurso gerado com INTELIGÊNCIA HÍBRIDA! 🧠🎉 Confira a melhor versão selecionada automaticamente!');

        } catch (\Exception $e) {
            Log::error('Erro ao gerar recurso novo: ' . $e->getMessage());
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
               "- Pontos: {$data['points']}\n" .
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
                ]
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            // RoBERTa pode retornar em formato diferente
            if (isset($result[0]['generated_text'])) {
                $text = $result[0]['generated_text'];
                return $this->cleanAppealText($text, $data);
            } else {
                // Fallback para estrutura de texto gerado manualmente
                return $this->buildCleanBrazilianLegalDocument($data);
            }
            
        } catch (\Exception $e) {
            Log::warning('RoBERTaLexPT não disponível, usando template brasileiro: ' . $e->getMessage());
            return $this->buildCleanBrazilianLegalDocument($data);
        }
    }

    /**
     * Limpa o texto do recurso removendo elementos indesejados - VERSÃO ULTRA ROBUSTA
     */
    private function cleanAppealText($text, $data)
    {
        // Remove observações e notas finais - VERSÃO ULTRA ROBUSTA MELHORADA
        $patterns = [
            // Remove "Página" em todas as variações possíveis
            '/\bPágina\b/i',
            '/\bPagina\b/i',
            '/Page\s*\d*/i',
            '/^\s*Página\s*$/m',
            '/^\s*Pagina\s*$/m',
            '/\n\s*Página\s*\n/i',
            '/\n\s*Pagina\s*\n/i',
            '/\n\s*Página\s*$/i',
            '/\n\s*Pagina\s*$/i',
            '/^\s*Página\s*\n/i',
            '/^\s*Pagina\s*\n/i',
            '/Página\s*$/m',
            '/Pagina\s*$/m',
            '/Página\s*\d*/i',
            '/Pagina\s*\d*/i',
            
            // Remove "Documento gerado em" em todas as variações
            '/Documento gerado em:.*$/m',
            '/Documento gerado em: \d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}/s',
            '/Documento gerado em \d{2}\/\d{2}\/\d{4}/s',
            '/Gerado em:.*$/m',
            '/Data de geração:.*$/m',
            '/Gerado automaticamente.*$/m',
            
            // Remove observações finais e notas
            '/\*\*Observações:\*\*.*$/s',
            '/\*\s*Este modelo.*$/s',
            '/Observações?:.*$/s',
            '/Nota:.*$/s',
            '/Notas?:.*$/s',
            '/Lembre-se.*$/s',
            '/\*\*Nota:.*$/s',
            '/\*\*Observação:.*$/s',
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
        $email = $data['email'] ?? auth()->user()->email ?? "{$data['name']}@email.com.br";
        $address = $data['address'] ?? 
                   $data['cnh_address'] ?? 
                   auth()->user()->cnh_address ?? 
                   "Rua {$data['name']}, nº 100, Centro, CEP 01000-000, {$city}/{$state}";
        
        // Data formatada para local/data
        $today = now()->format('d/m/Y');
        
        // Substitui campos vazios pelos dados reais
        $replacements = [
            '[endereço completo do recorrente]' => $address,
            '[número do AIT]' => $data['citation_number'] ?? '',
            '[Local]' => $city,
            '[Data]' => $today,
            '[Cidade]' => $city,
            '[Estado]' => $state,
            '[Cidade], [Estado]' => "{$city}/{$state}",
            'São Paulo/SP' => "{$city}/{$state}",
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
     * Constrói prompt limpo e específico para geração de recursos
     */
    private function buildCleanLegalPrompt($data)
    {
        // Prepara dados formatados
            $infraType = InfractionType::find($data['infraction_type_id']);
            $infractionName = $infraType ? $infraType->name : $data['reason'];
            $infractionCode = $infraType ? $infraType->code : '';
            $infractionArticle = $infraType ? $infraType->article : '';
            
            $date = new \DateTime($data['date']);
            $formattedDate = $date->format('d/m/Y');
        $formattedTime = $data['time'] ?? '(não informado)';
        
        // Usa cidade e estado fornecidos pelo usuário
        $city = $data['city'] ?? 'São Paulo';
        $state = $data['state'] ?? 'SP';
        
        // Email: busca em vários campos possíveis
        $email = $data['email'] ?? auth()->user()->email ?? "{$data['name']}@email.com.br";
        
        // Endereço: busca em vários campos possíveis  
        $address = $data['address'] ?? 
                   $data['cnh_address'] ?? 
                   auth()->user()->cnh_address ?? 
                   "Rua {$data['name']}, nº 100, Centro, CEP 01000-000, {$city}/{$state}";
            
        // Categoria da CNH ou padrão
        $cnhCategory = $data['driver_license_category'] ?? 
                       auth()->user()->cnh_category ?? 
                       'B';
        
        return "INSTRUÇÕES CRÍTICAS: Gere APENAS o texto do recurso final no FORMATO TRADICIONAL BRASILEIRO, completamente limpo e pronto para protocolo. NUNCA inclua: comentários, observações, notas, campos vazios como [INSERIR...], 'Página', 'Documento gerado em', ou qualquer texto que não seja parte do recurso oficial.\n\n" .
               
               "DADOS COMPLETOS PARA O RECURSO:\n" .
               "• Nome completo: {$data['name']}\n" .
               "• CPF: {$data['cpf']}\n" .
               "• CNH: {$data['driver_license']} (categoria {$cnhCategory})\n" .
               "• Endereço completo: {$address}\n" .
               "• Telefone: {$data['phone']}\n" .
               "• Email: {$email}\n" .
               "• Veículo: {$data['vehicle_model']}, placa {$data['plate']}, ano {$data['vehicle_year']}, cor {$data['vehicle_color']}\n" .
               "• Chassi: {$data['vehicle_chassi']}\n" .
               "• RENAVAM: {$data['vehicle_renavam']}\n" .
               "• Número da autuação: {$data['citation_number']}\n" .
               "• Data da infração: {$formattedDate}\n" .
               "• Hora da infração: {$formattedTime}\n" .
               "• Local da infração: {$data['location']}\n" .
               "• Cidade da infração: {$city}/{$state}\n" .
               "• Infração: {$infractionName}\n" .
               "• Código da infração: {$infractionCode}\n" .
               "• Artigo do CTB: {$infractionArticle}\n" .
               "• Valor da multa: R$ {$data['amount']}\n" .
               "• Pontos: {$data['points']}\n" .
               "• Motivo/Detalhes: {$data['reason']}\n\n" .
               
               "GERE: Um recurso administrativo no FORMATO TRADICIONAL com:\n" .
               "1. Cabeçalho: 'RECURSO ADMINISTRATIVO DE MULTA DE TRÂNSITO'\n" .
               "2. Destinatário: 'Ilmo(a). Sr(a). Presidente da JARI'\n" .
               "3. Qualificação completa do recorrente (COM TODOS OS DADOS ACIMA)\n" .
               "4. Seção 'DOS FATOS' com descrição técnica\n" .
               "5. Seção 'DOS FUNDAMENTOS' com fundamentação jurídica ROBUSTA\n" .
               "6. Seção 'DOS VÍCIOS' específicos para a infração {$infractionCode}\n" .
               "7. Seção 'DO PEDIDO' fundamentado\n" .
               "8. Fechamento: '{$city}/{$state}, " . now()->format('d/m/Y') . "'\n" .
               "9. Local para assinatura com nome e CPF\n\n" .
               
               "PROIBIDO: 'Página', observações finais, campos vazios, comentários da IA, 'Documento gerado em'.\n" .
               "OBRIGATÓRIO: Texto limpo, formato tradicional, pronto para protocolo.";
    }

    /**
     * Constrói documento brasileiro limpo e completo
     */
    private function buildCleanBrazilianLegalDocument($data)
    {
        $infraType = InfractionType::find($data['infraction_type_id']);
        $infractionName = $infraType ? $infraType->name : $data['reason'];
        $infractionCode = $infraType ? $infraType->code : '';
        $infractionArticle = $infraType ? $infraType->article : '';
        
        $date = new \DateTime($data['date']);
        $formattedDate = $date->format('d/m/Y');
        
        // Extrai cidade do local
        $cityFromLocation = $this->extractCityFromLocation($data['location'] ?? '');
        $city = $cityFromLocation ?: 'São Paulo';
        $state = $this->getStateFromCity($city);
        
        // Garantir dados completos com fallbacks inteligentes
        $email = $data['email'] ?? auth()->user()->email ?? "{$data['name']}@email.com.br";
        $address = $data['address'] ?? 
                   $data['cnh_address'] ?? 
                   auth()->user()->cnh_address ?? 
                   "Rua {$data['name']}, nº 100, Centro, CEP 01000-000, {$city}/{$state}";
        $cnhCategory = $data['driver_license_category'] ?? 
                       auth()->user()->cnh_category ?? 
                       'B';
        
        // Argumentos específicos baseados no tipo de infração
        $specificArguments = $this->getSpecificArguments($infractionCode, $data);
        
        return "RECURSO ADMINISTRATIVO DE MULTA DE TRÂNSITO\n\n" .
               "Ilmo(a). Sr(a). Presidente da JARI\n" .
               "Junta Administrativa de Recursos de Infrações\n\n" .
               
               "QUALIFICAÇÃO DO RECORRENTE:\n\n" .
               "Nome: {$data['name']}\n" .
               "CPF: {$data['cpf']}\n" .
               "CNH: {$data['driver_license']} (categoria {$cnhCategory})\n" .
               "Endereço: {$address}\n" .
               "Telefone: {$data['phone']}\n" .
               "E-mail: {$email}\n\n" .
               
               "DADOS DO VEÍCULO:\n\n" .
               "Modelo: {$data['vehicle_model']}\n" .
               "Placa: {$data['plate']}\n" .
               "Ano: {$data['vehicle_year']}\n" .
               "Cor: {$data['vehicle_color']}\n" .
               "Chassi: {$data['vehicle_chassi']}\n" .
               "RENAVAM: {$data['vehicle_renavam']}\n\n" .
               
               "DADOS DA AUTUAÇÃO:\n\n" .
               "Auto de Infração nº: {$data['citation_number']}\n" .
               "Data: {$formattedDate} às {$data['time']}\n" .
               "Local: {$data['location']}\n" .
               "Infração: {$infractionName} (código {$infractionCode})\n" .
               "Valor: R$ {$data['amount']}\n" .
               "Pontos: {$data['points']}\n\n" .
               
               "DOS FATOS:\n\n" .
               "Venho, respeitosamente, interpor RECURSO ADMINISTRATIVO contra o Auto de Infração " .
               "nº {$data['citation_number']}, lavrado em {$formattedDate}, às {$data['time']}, " .
               "referente à suposta infração do artigo {$infractionArticle} do CTB (código {$infractionCode}), " .
               "ocorrida em {$data['location']}, no valor de R$ {$data['amount']}, com pontuação de {$data['points']} pontos.\n\n" .
               
               "Conforme será demonstrado, a autuação é improcedente pelas razões fáticas e jurídicas a seguir expostas.\n\n" .
               
               "DOS FUNDAMENTOS:\n\n" .
               "O presente auto de infração deve ser cancelado por violação aos princípios da legalidade, " .
               "razoabilidade e proporcionalidade, além de não atender aos requisitos formais estabelecidos " .
               "no artigo 280 do Código de Trânsito Brasileiro (Lei 9.503/97).\n\n" .
               
               "O artigo 280 do CTB estabelece que o auto de infração deverá conter obrigatoriamente: " .
               "tipificação clara da infração, local exato com descrição detalhada, data e hora precisas, " .
               "caracterização específica da conduta infrativa, identificação completa do veículo e condutor, " .
               "além da assinatura legível do agente autuador.\n\n" .
               
               "A Resolução CONTRAN nº 404/2012 estabelece procedimentos específicos para autuação que " .
               "devem ser rigorosamente observados, sob pena de nulidade do ato administrativo.\n\n" .
               
               $specificArguments .
               
               "DOS VÍCIOS IDENTIFICADOS:\n\n" .
               "O auto de infração apresenta os seguintes vícios que comprometem sua validade:\n\n" .
               "1. Ausência de descrição detalhada e específica da conduta alegadamente infrativa;\n" .
               "2. Falta de elementos técnicos objetivos que comprovem a materialidade da infração;\n" .
               "3. Não observância integral dos procedimentos legais estabelecidos na legislação;\n" .
               "4. Deficiência na fundamentação fática e jurídica da autuação;\n" .
               "5. Aplicação desproporcional da penalidade face às circunstâncias específicas do caso.\n\n" .
               
               "O Superior Tribunal de Justiça consolidou o entendimento de que \"o auto de infração " .
               "é ato administrativo vinculado que deve observar rigorosamente os requisitos legais, " .
               "sob pena de nulidade\" (STJ, REsp 1.097.717/RS).\n\n" .
               
               "DO PEDIDO:\n\n" .
               "Ante o exposto, e com fundamento nos fatos e argumentos jurídicos apresentados, " .
               "requer-se respeitosamente:\n\n" .
               "a) O conhecimento e provimento integral do presente recurso;\n" .
               "b) O cancelamento definitivo da penalidade aplicada;\n" .
               "c) O arquivamento do processo administrativo;\n" .
               "d) A não incidência de pontos na CNH do recorrente.\n\n" .
               
               "Termos em que pede deferimento.\n\n" .
               
               "{$city}/{$state}, " . now()->format('d/m/Y') . "\n\n" .
               
               "______________________________\n" .
               "{$data['name']}\n" .
               "CPF: {$data['cpf']}\n" .
               "Telefone: {$data['phone']}\n" .
               "E-mail: {$email}";
    }

    /**
     * Retorna argumentos específicos baseados no tipo de infração
     */
    private function getSpecificArguments($infractionCode, $data)
    {
        switch($infractionCode) {
            case '162-10':
            case '162-20':
                return "3.2. DA INFRAÇÃO ESPECÍFICA - USO DE CELULAR\n\n" .
                       "A infração prevista no artigo 162 do CTB exige que o condutor esteja efetivamente " .
                       "\"dirigindo\" o veículo e \"segurando ou manuseando\" o telefone celular. " .
                       "A Resolução CONTRAN nº 798/2020 estabelece critérios específicos para caracterização " .
                       "desta infração.\n\n" .
                       "No presente caso, considerando que a autuação ocorreu em {$data['location']}, " .
                       "local caracterizado como rua residencial sem saída e com tráfego mínimo, " .
                       "não restou comprovada a situação de risco efetivo à segurança viária que " .
                       "justifique a aplicação da penalidade.\n\n" .
                       "O princípio da razoabilidade exige que a aplicação da norma considere as " .
                       "circunstâncias específicas de cada caso, não sendo admissível a aplicação " .
                       "automática da penalidade sem análise do contexto fático.\n\n";
            
            case '161-00':
                return "3.2. DA INFRAÇÃO ESPECÍFICA - ESTACIONAMENTO\n\n" .
                       "A configuração de estacionamento irregular exige a presença de sinalização " .
                       "clara e específica, conforme estabelecido na Resolução CONTRAN nº 180/2005. " .
                       "A ausência de sinalização adequada ou sua deficiência compromete a validade " .
                       "da autuação.\n\n";
                       
            case '554-20':
                return "3.2. DA INFRAÇÃO ESPECÍFICA - EXCESSO DE VELOCIDADE\n\n" .
                       "A autuação por excesso de velocidade deve observar rigorosamente os procedimentos " .
                       "estabelecidos na Resolução CONTRAN nº 396/2011, incluindo a calibração e " .
                       "aferição dos equipamentos utilizados.\n\n" .
                       "A margem de tolerância estabelecida deve ser considerada, bem como a " .
                       "comprovação da regularidade metrológica do equipamento.\n\n";
                       
            default:
                return "3.2. DA ANÁLISE ESPECÍFICA DA INFRAÇÃO\n\n" .
                       "A infração imputada deve ser analisada considerando as circunstâncias específicas " .
                       "do caso concreto e a observância dos princípios da razoabilidade e proporcionalidade " .
                       "na aplicação da penalidade administrativa.\n\n" .
                       "As condições locais, a ausência de risco efetivo à segurança viária e a " .
                       "desproporcionalidade da medida aplicada tornam a autuação improcedente.\n\n";
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
            $infractionName = $infraType ? $infraType->name : $data['reason'];
            $infractionCode = $infraType ? $infraType->code : '';
            $infractionArticle = $infraType ? $infraType->article : '';
            
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
     * Baixa o PDF de um recurso.
     */
    public function download(Appeal $appeal)
    {
        $this->authorize('view', $appeal);

        if (!$appeal->pdf_path) {
            return back()->with('error', 'O arquivo do recurso não está disponível.');
        }

        $path = storage_path('app/public/' . $appeal->pdf_path);
        
        if (!file_exists($path)) {
            return back()->with('error', 'O arquivo do recurso não foi encontrado.');
        }

        return response()->download($path, 'recurso_' . $appeal->ticket_id . '.pdf');
    }

    private function getFallbackText($data)
    {
        // Fallback usando template brasileiro limpo
        return $this->buildCleanBrazilianLegalDocument($data);
    }
}
