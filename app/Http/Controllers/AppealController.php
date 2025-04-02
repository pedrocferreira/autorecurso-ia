<?php

namespace App\Http\Controllers;

use App\Models\Appeal;
use App\Models\Ticket;
use App\Models\User;
use App\Services\OpenAIService;
use App\Services\PDFService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Services\CreditService;
use App\Models\InfractionType;
use OpenAI\Laravel\Facades\OpenAI;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppealGeneratedEmail;
use App\Exceptions\InsufficientCreditsException;

class AppealController extends Controller
{
    protected $openAIService;
    protected $pdfService;
    protected $creditService;

    /**
     * Construtor que inicializa os serviços e aplica middleware de autenticação.
     */
    public function __construct(OpenAIService $openAIService, PDFService $pdfService, CreditService $creditService)
    {
        $this->middleware('auth');
        $this->openAIService = $openAIService;
        $this->pdfService = $pdfService;
        $this->creditService = $creditService;
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
            'date', 'amount', 'points', 'client_justification', 'infraction_type_id'
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
                ->with('error', 'Você precisa ter pelo menos 1 crédito para gerar um recurso. Por favor, adquira créditos para continuar.');
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
            // Validação dos campos
            $request->validate([
                'ticket_id' => 'required|exists:tickets,id',
                'name' => 'required|string|max:255',
                'cpf' => 'required|string|max:14',
                'driver_license' => 'required|string|max:20',
                'driver_license_category' => 'required|string|max:5',
                'address' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'email' => 'required|email|max:255',
                'plate' => 'required|string|max:10',
                'vehicle_model' => 'required|string|max:100',
                'vehicle_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'vehicle_color' => 'required|string|max:50',
                'vehicle_chassi' => 'required|string|max:50',
                'vehicle_renavam' => 'required|string|max:50',
                'date' => 'required|date',
                'amount' => 'required|numeric|min:0',
                'points' => 'required|integer|min:0',
                'client_justification' => 'required|string',
                'infraction_type_id' => 'required|exists:infraction_types,id',
                'process_number' => 'nullable|string|max:50'
            ]);

            // Busca a multa
            $ticket = Ticket::with('infractionType')->findOrFail($request->ticket_id);
            
            // Verifica se já existe um recurso para esta multa
            $existingAppeal = Appeal::where('ticket_id', $ticket->id)->first();
            if ($existingAppeal) {
                Log::info('Tentativa de criar recurso duplicado', [
                    'ticket_id' => $ticket->id,
                    'user_id' => auth()->id(),
                    'existing_appeal_id' => $existingAppeal->id
                ]);
                return redirect()->route('appeals.show', $existingAppeal)
                    ->with('info', 'Você já gerou um recurso para esta multa. Abaixo estão os detalhes do recurso existente.');
            }

            // Verifica se o usuário tem créditos suficientes
            $user = auth()->user();
            
            // Obtém o custo em créditos com base na gravidade da infração
            $infractionType = $ticket->infractionType;
            $creditCost = $this->creditService->getAppealCreditCost($infractionType);
            
            if (!$user->hasEnoughCredits($creditCost)) {
                $severityText = $infractionType ? ucfirst($infractionType->severity_text) : 'Não identificada';
                return redirect()->route('credits.packages')
                    ->with('error', "Você não possui créditos suficientes para gerar um recurso para esta infração. 
                        Gravidade: {$severityText}. Custo: {$creditCost} créditos.");
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

            // Atualiza o ticket com o número do processo se fornecido
            if (!empty($request->process_number)) {
                $ticket->update([
                    'process_number' => $request->process_number
                ]);
            }

            // Registra o consumo de créditos usando o serviço
            $this->creditService->consumeCreditsForAppeal($user, $appeal);

            // Envia email com o recurso gerado
            try {
                Log::channel('email')->info('Tentando enviar email de notificação de recurso gerado', [
                    'appeal_id' => $appeal->id,
                    'user_id' => $user->id,
                    'user_email' => $user->email
                ]);

                Mail::to($user->email)->send(new AppealGeneratedEmail($appeal));

                Log::channel('email')->info('Email de notificação de recurso gerado enviado com sucesso', [
                    'appeal_id' => $appeal->id,
                    'user_id' => $user->id
                ]);
            } catch (\Exception $e) {
                Log::channel('email')->error('Erro ao enviar email de notificação de recurso gerado', [
                    'appeal_id' => $appeal->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Não interrompe o fluxo, apenas registra o erro
            }

            return redirect()->route('appeals.show', $appeal)
                ->with('success', 'Recurso gerado com sucesso!');

        } catch (InsufficientCreditsException $e) {
            return redirect()->route('credits.packages')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Erro ao gerar recurso: ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao gerar o recurso. Por favor, tente novamente.');
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
               "- Renavam: {$data['vehicle_renavam']}\n\n" .
               "Dados da Infração:\n" .
               "- Data: {$data['date']}\n" .
               "- Valor: R$ {$data['amount']}\n" .
               "- Pontos: {$data['points']}\n" .
               "- Justificativa: {$data['client_justification']}\n" .
               "- Número do Processo: " . ($data['process_number'] ?? 'Não informado') . "\n" .
               "- Número da Notificação: " . ($data['notification_number'] ?? 'Não informado') . "\n";
    }

    protected function generateAppealText($data)
    {
        try {
            // Prepara os dados para o prompt
            $infraType = InfractionType::find($data['infraction_type_id']);
            $infractionName = $infraType ? $infraType->name : $data['client_justification'];
            $infractionCode = $infraType ? $infraType->code : '';
            $infractionArticle = $infraType ? $infraType->article : '';
            
            // Formatando a data para o formato brasileiro
            $date = new \DateTime($data['date']);
            $formattedDate = $date->format('d/m/Y');
            
            $prompt = "Elabore um recurso administrativo formal e profissional contra uma multa de trânsito, utilizando como base principal a descrição detalhada da infração fornecida. O recurso deve ser estruturado como uma petição administrativa, utilizando os seguintes dados:\n\n" .
                "DESCRIÇÃO DETALHADA DA INFRAÇÃO:\n" .
                "{$data['client_justification']}\n\n" .
                
                "QUALIFICAÇÃO DO RECORRENTE:\n" .
                "Nome: {$data['name']}\n" .
                "CPF: {$data['cpf']}\n" .
                "CNH: {$data['driver_license']} (categoria {$data['driver_license_category']})\n" .
                "Endereço: {$data['address']}\n" .
                "Telefone: {$data['phone']}\n" .
                "E-mail: {$data['email']}\n" .
                "Veículo: {$data['vehicle_model']}, placa {$data['plate']}, ano {$data['vehicle_year']}, cor {$data['vehicle_color']}\n" .
                "Chassi: {$data['vehicle_chassi']}\n" .
                "RENAVAM: {$data['vehicle_renavam']}\n\n" .
                
                "DADOS DA AUTUAÇÃO:\n" .
                "Infração: {$infractionName}\n" .
                "Código da infração: {$infractionCode}\n" .
                "Artigo do CTB: {$infractionArticle}\n" .
                "Data da infração: {$formattedDate}\n" .
                "Valor da multa: R$ {$data['amount']}\n" .
                "Pontuação: {$data['points']} pontos\n" .
                "Número do Processo: " . (isset($data['process_number']) && $data['process_number'] ? $data['process_number'] : 'Não informado') . "\n\n" .
                
                "INSTRUÇÕES ESPECÍFICAS:\n" .
                "1. O recurso deve começar com 'EXCELENTÍSSIMO(A) SENHOR(A) PRESIDENTE DA JUNTA ADMINISTRATIVA DE RECURSOS DE INFRAÇÕES DE TRÂNSITO - JARI'.\n" .
                "2. Se houver número do processo, o recurso deve começar com 'RECURSO ADMINISTRATIVO Nº [número do processo]' logo após o cabeçalho da JARI.\n" .
                "3. Desenvolva uma argumentação técnica e jurídica sólida, focando nos aspectos específicos mencionados na descrição da infração.\n" .
                "4. Cite artigos específicos do CTB e resoluções do CONTRAN pertinentes aos fatos narrados.\n" .
                "5. Inclua argumentos sobre aspectos formais e materiais da autuação, relacionando-os com a descrição fornecida.\n" .
                "6. Utilize linguagem formal e técnica, própria de documentos jurídicos.\n" .
                "7. Estruture o documento com as seguintes seções:\n" .
                "   - Cabeçalho com destinatário (JARI)\n" .
                "   - Número do Processo (se houver)\n" .
                "   - Qualificação do recorrente\n" .
                "   - Dos Fatos (narrativa detalhada do ocorrido)\n" .
                "   - Do Direito (fundamentação jurídica com citações)\n" .
                "   - Dos Fundamentos (argumentos específicos baseados na descrição)\n" .
                "   - Do Pedido\n" .
                "8. Não utilize estrutura de tópicos no corpo do recurso. O texto deve ser corrido e bem fundamentado.\n" .
                "9. Inclua jurisprudência relevante quando aplicável aos fatos narrados.\n" .
                "10. Ao final, após o 'Nestes termos, pede deferimento', adicione a cidade (extraída do endereço) e a data atual.\n" .
                "11. Inclua espaço para assinatura usando '___________________' e o nome completo abaixo.\n" .
                "12. O texto deve ser persuasivo e tecnicamente embasado, demonstrando profundo conhecimento da legislação de trânsito.\n" .
                "13. Não mencione que é um advogado ou inclua número de OAB.\n" .
                "14. O recurso deve ter uma estrutura profissional e coesa, sem divisões por tópicos ou marcadores.\n" .
                "15. Dê ênfase especial aos argumentos que se relacionam diretamente com a descrição da infração fornecida.\n" .
                "16. Inclua argumentos sobre:\n" .
                "    - Aspectos técnicos da infração\n" .
                "    - Possíveis vícios formais no auto\n" .
                "    - Circunstâncias atenuantes\n" .
                "    - Direitos do cidadão no processo administrativo\n" .
                "    - Princípios constitucionais aplicáveis\n" .
                "17. Desenvolva uma narrativa clara e detalhada dos fatos, explicando o contexto da infração.\n" .
                "18. Inclua citações de artigos do CTB e resoluções do CONTRAN que possam fundamentar a defesa.\n" .
                "19. Argumente sobre a possibilidade de erro na fiscalização ou na interpretação da infração.\n" .
                "20. Solicite a produção de provas técnicas quando relevante para o caso.\n" .
                "21. IMPORTANTE: A qualificação do recorrente deve ser feita em parágrafo corrido, sem tópicos.\n" .
                "22. IMPORTANTE: Os fatos devem ser narrados em parágrafos bem estruturados, sem tópicos.\n" .
                "23. IMPORTANTE: A fundamentação jurídica deve ser desenvolvida em parágrafos coesos, sem tópicos.\n" .
                "24. IMPORTANTE: O pedido deve ser claro e objetivo, sem tópicos.\n" .
                "25. IMPORTANTE: Se houver número do processo, ele deve aparecer logo após o cabeçalho da JARI, antes da qualificação do recorrente.";
                
            // Chamada à API do OpenAI (GPT-4)
            $result = OpenAI::chat()->create([
                'model' => 'gpt-4-turbo',
                'messages' => [
                    [
                        'role' => 'system', 
                        'content' => 'Você é um especialista em direito de trânsito com vasta experiência na elaboração de recursos administrativos. Seu objetivo é criar recursos tecnicamente precisos e persuasivos, utilizando como base principal a descrição detalhada da infração fornecida. Você deve analisar cuidadosamente os fatos narrados na descrição e desenvolver argumentos jurídicos específicos para cada caso. Utilize linguagem formal e técnica, própria de documentos jurídicos, e estruture o texto como uma petição administrativa profissional, sem usar tópicos ou marcadores. Mantenha um fluxo coeso e bem fundamentado, citando artigos específicos do CTB e resoluções do CONTRAN pertinentes aos fatos narrados. O texto deve ser convincente, formal e tecnicamente embasado, mas sem identificar-se como advogado. Desenvolva argumentos robustos sobre aspectos técnicos, vícios formais, circunstâncias atenuantes, direitos do cidadão e princípios constitucionais. Inclua citações precisas de legislação e jurisprudência quando relevante. A narrativa dos fatos deve ser clara e detalhada, explicando o contexto da infração. Argumente sobre possíveis erros na fiscalização e solicite provas técnicas quando necessário. IMPORTANTE: O texto deve ser desenvolvido em parágrafos bem estruturados, sem uso de tópicos ou marcadores, seguindo o padrão formal de petições administrativas.'
                    ],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2500
            ]);
            
            $appealText = $result->choices[0]->message->content;
            
            // Verificar se o texto contém uma linha de assinatura
            if (!str_contains($appealText, '___________________')) {
                $appealText = str_replace('[assinatura]', '___________________', $appealText);
            }
            
            // Verificar se o texto contém a data atual
            $currentDate = now()->format('d/m/Y');
            if (!str_contains($appealText, $currentDate)) {
                $appealText = str_replace('[inserir data]', $currentDate, $appealText);
                $appealText = str_replace('[data atual]', $currentDate, $appealText);
            }
            
            // Extrair cidade do endereço para verificação
            $city = null;
            if (isset($data['address']) && $data['address']) {
                $addressParts = explode(',', $data['address']);
                $lastPart = trim(end($addressParts));
                if (!empty($lastPart)) {
                    $city = preg_replace('/\s*-\s*[A-Z]{2}.*$/', '', $lastPart);
                }
            }
            
            // Verificar se a cidade está presente no final do documento
            if ($city && !str_contains($appealText, $city . ', ' . $currentDate)) {
                // Padrões comuns de fechamento do documento
                $patterns = [
                    '/Nestes termos,\s+Pede deferimento\./i',
                    '/Nesses termos,\s+Pede deferimento\./i',
                    '/Termos em que,\s+Pede deferimento\./i'
                ];
                
                // Tenta adicionar cidade e data após o pedido de deferimento
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $appealText)) {
                        $appealText = preg_replace($pattern, "$0\n\n$city, $currentDate", $appealText, 1);
                        break;
                    }
                }
            }
            
            Log::info('Recurso gerado com sucesso utilizando a API OpenAI');
            
            return $appealText;
        } catch (\Exception $e) {
            Log::error('Erro ao gerar texto de recurso com OpenAI: ' . $e->getMessage());
            
            // Fallback para o texto exemplo em caso de erro
            $currentDate = now()->format('d/m/Y');
            
            // Extrair cidade do endereço
            $city = 'Local';
            if (isset($data['address']) && $data['address']) {
                $addressParts = explode(',', $data['address']);
                $lastPart = trim(end($addressParts));
                if (!empty($lastPart)) {
                    $city = $lastPart;
                }
            }
            
            return "RECURSO ADMINISTRATIVO\n\n" .
                   "Ilmo(a). Sr(a). Presidente da JARI - Junta Administrativa de Recursos de Infrações\n\n" .
                   "Eu, {$data['name']}, portador(a) do CPF nº {$data['cpf']}, " .
                   "residente e domiciliado(a) em {$data['address']}, " .
                   "condutor(a) do veículo de placa {$data['plate']}, modelo {$data['vehicle_model']}, " .
                   "venho, respeitosamente, à presença de V.Sa., apresentar RECURSO ADMINISTRATIVO " .
                   "contra o Auto de Infração de Trânsito lavrado em {$data['date']}, " .
                   "no valor de R$ {$data['amount']}, com base nos fatos e fundamentos a seguir expostos.\n\n" .
                   "DOS FATOS\n\n" .
                   "Fui notificado da autuação referente à suposta infração \"{$data['client_justification']}\", " .
                   "ocorrida na data de {$data['date']}.\n\n" .
                   "DOS FUNDAMENTOS\n\n" .
                   "O auto de infração não preenche os requisitos legais estabelecidos pelo Código de Trânsito Brasileiro, " .
                   "apresentando vícios formais que comprometem sua validade.\n\n" .
                   "DO PEDIDO\n\n" .
                   "Diante do exposto, solicito o cancelamento da penalidade imposta, " .
                   "com o consequente arquivamento do auto de infração.\n\n" .
                   "Nestes termos,\n" .
                   "Pede deferimento.\n\n" .
                   $city . ", " . $currentDate . "\n\n" .
                   "___________________\n" .
                   "{$data['name']}\n" .
                   "CPF: {$data['cpf']}\n" .
                   "Endereço: {$data['address']}\n" .
                   "Telefone: {$data['phone']}\n" .
                   "E-mail: {$data['email']}";
        }
    }

    protected function generateAppealPDF($text, $ticket)
    {
        try {
            // Gera um nome único para o arquivo
            $filename = 'recurso_' . $ticket->id . '_' . time() . '.pdf';
            
            // Recarrega o ticket com todos os campos necessários
            $ticket = Ticket::select([
                'id', 'name', 'cpf', 'cnh_number', 'cnh_category',
                'address', 'phone', 'email', 'vehicle_plate', 'vehicle_model',
                'vehicle_year', 'vehicle_color', 'vehicle_chassi', 'vehicle_renavam',
                'infraction_date', 'infraction_amount', 'infraction_points', 'client_justification', 'infraction_type_id',
                'process_number'
            ])->find($ticket->id);
            
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
     * Baixa o arquivo do recurso no formato especificado.
     */
    public function download(Appeal $appeal, Request $request)
    {
        $this->authorize('view', $appeal);

        $format = $request->get('format', 'pdf');
        Log::info('Iniciando download do recurso', [
            'appeal_id' => $appeal->id,
            'format' => $format,
            'user_id' => auth()->id()
        ]);

        // Verifica se o texto do recurso existe
        if (!$appeal->generated_text) {
            Log::error('Texto do recurso não disponível', ['appeal_id' => $appeal->id]);
            return back()->with('error', 'O texto do recurso não está disponível.');
        }

        $filename = 'recurso_' . $appeal->ticket_id;

        switch ($format) {
            case 'pdf':
                if (!$appeal->pdf_path) {
                    Log::error('Caminho do PDF não disponível', ['appeal_id' => $appeal->id]);
                    return back()->with('error', 'O arquivo PDF do recurso não está disponível.');
                }
        $path = storage_path('app/public/' . $appeal->pdf_path);
                Log::info('Tentando baixar PDF', ['path' => $path]);
        
        if (!file_exists($path)) {
                    Log::error('Arquivo PDF não encontrado', ['path' => $path]);
                    return back()->with('error', 'O arquivo PDF do recurso não foi encontrado.');
                }
                return response()->download($path, $filename . '.pdf');

            case 'docx':
                try {
                    // Gera um arquivo DOCX temporário
                    $tempPath = storage_path('app/temp/' . $filename . '.docx');
                    if (!file_exists(storage_path('app/temp'))) {
                        mkdir(storage_path('app/temp'), 0775, true);
                    }
                    
                    Log::info('Gerando arquivo DOCX', ['path' => $tempPath]);
                    
                    // Cria um novo documento Word
                    $phpWord = new PhpWord();
                    $phpWord->setDefaultFontName('Times New Roman');
                    $phpWord->setDefaultFontSize(12);
                    
                    $section = $phpWord->addSection([
                        'marginLeft' => 1440,
                        'marginRight' => 1440,
                        'marginTop' => 1440,
                        'marginBottom' => 1440
                    ]);
                    
                    // Título
                    $section->addText('RECURSO ADMINISTRATIVO', [
                        'bold' => true,
                        'size' => 14
                    ], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                    $section->addTextBreak(2);
                    
                    // Texto do recurso com formatação
                    $paragraphs = explode("\n\n", $appeal->generated_text);
                    foreach ($paragraphs as $paragraph) {
                        if (trim($paragraph) !== '') {
                            $section->addText(trim($paragraph), null, [
                                'spacing' => 120,
                                'spaceAfter' => 240
                            ]);
                            $section->addTextBreak(1);
                        }
                    }
                    
                    // Salva o arquivo
                    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
                    $objWriter->save($tempPath);
                    
                    Log::info('Arquivo DOCX gerado com sucesso', ['path' => $tempPath]);
                    
                    // Faz o download e remove o arquivo temporário
                    return response()->download($tempPath, $filename . '.docx')->deleteFileAfterSend(true);
                } catch (\Exception $e) {
                    Log::error('Erro ao gerar arquivo DOCX', [
                        'error' => $e->getMessage(),
                        'appeal_id' => $appeal->id
                    ]);
                    return back()->with('error', 'Erro ao gerar o arquivo DOCX. Por favor, tente novamente.');
                }

            case 'txt':
                try {
                    // Gera um arquivo TXT temporário
                    $tempPath = storage_path('app/temp/' . $filename . '.txt');
                    if (!file_exists(storage_path('app/temp'))) {
                        mkdir(storage_path('app/temp'), 0775, true);
                    }
                    
                    Log::info('Gerando arquivo TXT', ['path' => $tempPath]);
                    
                    // Formata o texto para TXT
                    $text = "RECURSO ADMINISTRATIVO\n\n";
                    $text .= str_repeat("=", 50) . "\n\n";
                    
                    // Divide o texto em parágrafos e adiciona formatação
                    $paragraphs = explode("\n\n", $appeal->generated_text);
                    foreach ($paragraphs as $paragraph) {
                        if (trim($paragraph) !== '') {
                            $text .= wordwrap(trim($paragraph), 80, "\n") . "\n\n";
                        }
                    }
                    
                    // Salva o texto em um arquivo
                    file_put_contents($tempPath, $text);
                    
                    Log::info('Arquivo TXT gerado com sucesso', ['path' => $tempPath]);
                    
                    // Faz o download e remove o arquivo temporário
                    return response()->download($tempPath, $filename . '.txt')->deleteFileAfterSend(true);
                } catch (\Exception $e) {
                    Log::error('Erro ao gerar arquivo TXT', [
                        'error' => $e->getMessage(),
                        'appeal_id' => $appeal->id
                    ]);
                    return back()->with('error', 'Erro ao gerar o arquivo TXT. Por favor, tente novamente.');
                }

            default:
                Log::error('Formato de arquivo não suportado', [
                    'format' => $format,
                    'appeal_id' => $appeal->id
                ]);
                return back()->with('error', 'Formato de arquivo não suportado.');
        }
    }

    /**
     * Gera automaticamente um recurso para uma multa
     */
    public function generateAppealForTicket(Ticket $ticket, Appeal $appeal = null)
    {
        // Verificar se já existe um recurso para esta multa
        if (!$appeal) {
            $appeal = Appeal::where('ticket_id', $ticket->id)->first();
            if (!$appeal) {
                // Criar um novo recurso
                $appeal = Appeal::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $ticket->user_id,
                    'status' => 'pending'
                ]);
            }
        }

        // Preparar dados para o prompt
        $data = [
            'name' => $ticket->name,
            'cpf' => $ticket->cpf,
            'driver_license' => $ticket->driver_license ?? $ticket->cnh_number,
            'driver_license_category' => $ticket->driver_license_category ?? $ticket->cnh_category,
            'address' => $ticket->address,
            'phone' => $ticket->phone,
            'email' => $ticket->email,
            'plate' => $ticket->plate ?? $ticket->vehicle_plate,
            'vehicle_model' => $ticket->vehicle_model,
            'vehicle_year' => $ticket->vehicle_year,
            'vehicle_color' => $ticket->vehicle_color,
            'vehicle_chassi' => $ticket->vehicle_chassi,
            'vehicle_renavam' => $ticket->vehicle_renavam,
            'date' => $ticket->date ?? $ticket->infraction_date,
            'amount' => $ticket->amount ?? $ticket->infraction_amount,
            'points' => $ticket->points ?? $ticket->infraction_points,
            'client_justification' => $ticket->reason,
            'process_number' => $ticket->process_number ?? '',
            'notification_number' => $ticket->notification_number ?? '',
            'infraction_type_id' => $ticket->infraction_type_id
        ];

        // Extrair cidade do endereço
        $cityMatch = [];
        preg_match('/([A-Za-zÀ-ÿ\s]+)(?:\s*-\s*[A-Z]{2})?(?:\s*-\s*CEP:?.*)?$/', $ticket->address, $cityMatch);
        $city = !empty($cityMatch[1]) ? trim($cityMatch[1]) : 'São Paulo';
        $locationText = $city;

        // Gerar o texto do recurso
        $appealText = $this->generateAppealText($data);
        
        // Adicionar localidade e data
        $now = new \DateTime();
        $formattedDate = $now->format('d/m/Y');
        $finalAppealText = str_replace('[Localidade], [data]', "$locationText, $formattedDate", $appealText);
        
        // Criar o PDF do recurso
        $pdfPath = $this->generateAppealPDF($finalAppealText, $ticket);

        // Atualizar o recurso
        $appeal->update([
            'text' => $finalAppealText,
            'generated_text' => $finalAppealText,
            'pdf_path' => $pdfPath,
            'location' => $locationText,
            'generated_at' => now()
        ]);

        // Deduzir um crédito do usuário
        $user = User::find($ticket->user_id);
        if ($user && $user->credits > 0) {
            $user->decrement('credits');
            
            // Registrar a transação de créditos
            DB::table('credit_transactions')->insert([
                'user_id' => $user->id,
                'amount' => -1,
                'description' => 'Geração automática de recurso para multa #' . $ticket->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return $appeal;
    }
}
