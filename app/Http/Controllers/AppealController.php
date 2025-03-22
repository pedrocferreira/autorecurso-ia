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
                'amount' => -1,
                'description' => 'Geração de recurso para multa #' . $ticket->id,
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
        try {
            // Prepara os dados para o prompt
            $infraType = InfractionType::find($data['infraction_type_id']);
            $infractionName = $infraType ? $infraType->name : $data['reason'];
            $infractionCode = $infraType ? $infraType->code : '';
            $infractionArticle = $infraType ? $infraType->article : '';
            
            // Formatando a data para o formato brasileiro
            $date = new \DateTime($data['date']);
            $formattedDate = $date->format('d/m/Y');
            
            $prompt = "Gere um recurso administrativo COMPLETO e DETALHADO para multa de trânsito com os seguintes dados:\n\n" .
                "DADOS DO CONDUTOR:\n" .
                "- Nome: {$data['name']}\n" .
                "- CPF: {$data['cpf']}\n" .
                "- CNH: {$data['driver_license']} (categoria {$data['driver_license_category']})\n" .
                "- Endereço: {$data['address']}\n" .
                "- Telefone: {$data['phone']}\n" .
                "- E-mail: {$data['email']}\n\n" .
                
                "DADOS DO VEÍCULO:\n" .
                "- Modelo: {$data['vehicle_model']}\n" .
                "- Placa: {$data['plate']}\n" .
                "- Ano: {$data['vehicle_year']}\n" .
                "- Cor: {$data['vehicle_color']}\n" .
                "- Chassi: {$data['vehicle_chassi']}\n" .
                "- RENAVAM: {$data['vehicle_renavam']}\n\n" .
                
                "DADOS DA INFRAÇÃO:\n" .
                "- Infração: {$infractionName}\n" .
                "- Código da infração: {$infractionCode}\n" .
                "- Artigo do CTB: {$infractionArticle}\n" .
                "- Data da infração: {$formattedDate}\n" .
                "- Valor da multa: R$ {$data['amount']}\n" .
                "- Pontuação: {$data['points']} pontos\n" .
                "- Detalhes adicionais: {$data['reason']}\n\n" .
                
                "INSTRUÇÕES ESPECÍFICAS:\n" .
                "1. Crie um recurso administrativo de multa de trânsito seguindo a estrutura formal jurídica brasileira.\n" .
                "2. O recurso deve ser dirigido à JARI (Junta Administrativa de Recursos de Infrações).\n" .
                "3. Analise DETALHADAMENTE o tipo específico de infração mencionado acima e construa argumentos técnicos e jurídicos adequados especificamente para esse tipo de infração.\n" .
                "4. Cite artigos específicos do CTB (Código de Trânsito Brasileiro) relacionados à infração e aos procedimentos de autuação.\n" .
                "5. Inclua argumentos sobre possíveis vícios formais no auto de infração, como falta de elementos obrigatórios ou falhas procedimentais.\n" .
                "6. Mencione jurisprudência relevante para casos similares, se aplicável.\n" .
                "7. Estruture o documento com as seguintes seções: cabeçalho formal, qualificação do recorrente, dos fatos, do direito (fundamentação jurídica), do pedido e fechamento formal.\n" .
                "8. Use linguagem formal, técnica e respeitosa, apropriada para documentos jurídicos.\n" .
                "9. O texto deve ser convincente e baseado em argumentos legais sólidos que possam efetivamente contestar a infração descrita.";
                
            // Chamada à API do OpenAI (GPT-4)
            $result = OpenAI::chat()->create([
                'model' => 'gpt-4-turbo',
                'messages' => [
                    [
                        'role' => 'system', 
                        'content' => 'Você é um advogado especializado em recursos de multas de trânsito no Brasil, com profundo conhecimento do CTB (Código de Trânsito Brasileiro), resoluções do CONTRAN e jurisprudência. Sua tarefa é criar recursos administrativos detalhados e tecnicamente precisos, com fundamentação jurídica sólida.'
                    ],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2500
            ]);
            
            $appealText = $result->choices[0]->message->content;
            
            Log::info('Recurso gerado com sucesso utilizando a API OpenAI');
            
            return $appealText;
        } catch (\Exception $e) {
            Log::error('Erro ao gerar texto de recurso com OpenAI: ' . $e->getMessage());
            
            // Fallback para o texto exemplo em caso de erro
            return "RECURSO ADMINISTRATIVO DE MULTA DE TRÂNSITO\n\n" .
                   "Ilmo(a). Sr(a). Presidente da JARI - Junta Administrativa de Recursos de Infrações\n\n" .
                   "Eu, {$data['name']}, portador(a) do CPF nº {$data['cpf']}, " .
                   "residente e domiciliado(a) em {$data['address']}, " .
                   "condutor(a) do veículo de placa {$data['plate']}, modelo {$data['vehicle_model']}, " .
                   "venho, respeitosamente, à presença de V.Sa., apresentar RECURSO ADMINISTRATIVO " .
                   "contra o Auto de Infração de Trânsito lavrado em {$data['date']}, " .
                   "no valor de R$ {$data['amount']}, com base nos fatos e fundamentos a seguir expostos.\n\n" .
                   "DOS FATOS\n\n" .
                   "Fui notificado da autuação referente à suposta infração \"{$data['reason']}\", " .
                   "ocorrida na data de {$data['date']}.\n\n" .
                   "DOS FUNDAMENTOS\n\n" .
                   "O auto de infração não preenche os requisitos legais estabelecidos pelo Código de Trânsito Brasileiro, " .
                   "apresentando vícios formais que comprometem sua validade.\n\n" .
                   "DO PEDIDO\n\n" .
                   "Diante do exposto, solicito o cancelamento da penalidade imposta, " .
                   "com o consequente arquivamento do auto de infração.\n\n" .
                   "Nestes termos,\n" .
                   "Pede deferimento.\n\n" .
                   "Local e data,\n\n" .
                   "{$data['name']}\n" .
                   "CPF: {$data['cpf']}\n" .
                   "Endereço: {$data['address']}\n" .
                   "Telefone: {$data['phone']}\n" .
                   "E-mail: {$data['email']}";
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
}
