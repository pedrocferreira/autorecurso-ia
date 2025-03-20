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
    public function index(): View
    {
        $appeals = Auth::user()->appeals()->with('ticket')->latest()->paginate(10);
        return view('appeals.index', compact('appeals'));
    }

    /**
     * Mostra o formulário para gerar um novo recurso.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        // Verificar se o usuário tem créditos suficientes
        if (!$user->hasEnoughCredits(1)) {
            return redirect()->route('credits.packages')
                ->with('warning', 'Você precisa ter pelo menos 1 crédito para gerar um recurso. Por favor, adquira créditos para continuar.');
        }

        $ticketId = $request->query('ticket_id');
        $ticket = null;
        $tickets = null;

        if ($ticketId) {
            $ticket = Ticket::findOrFail($ticketId);
            $this->authorize('view', $ticket);
        } else {
            $tickets = Auth::user()->tickets()->doesntHave('appeals')->get();
        }

        $data = [];
        if (isset($ticket)) $data['ticket'] = $ticket;
        if (isset($tickets)) $data['tickets'] = $tickets;

        return view('appeals.create', $data);
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
    public function store(Request $request): RedirectResponse
    {
        try {
            // Verifica se é um novo recurso completo ou apenas seleção de ticket
            if ($request->has('infraction_type_id')) {
                // Validação para o formulário completo
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'cpf' => 'required|string|max:14',
                    'driver_license' => 'required|string|max:20',
                    'address' => 'required|string|max:255',
                    'plate' => 'required|string|max:8',
                    'vehicle_model' => 'required|string|max:50',
                    'vehicle_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                    'infraction_type_id' => 'required|exists:infraction_types,id',
                    'citation_number' => 'required|string|max:30',
                    'date' => 'required|date',
                    'location' => 'required|string|max:255',
                    'custom_details' => 'nullable|string',
                ]);

                $user = Auth::user();

                // Verificar se o usuário tem créditos suficientes
                if (!$user->hasEnoughCredits(1)) {
                    return back()->withInput()->withErrors([
                        'error' => 'Você não tem créditos suficientes para gerar um recurso.',
                    ]);
                }

                // Criar o ticket
                $infractionType = \App\Models\InfractionType::findOrFail($validated['infraction_type_id']);

                $ticket = Ticket::create([
                    'user_id' => $user->id,
                    'infraction_type_id' => $validated['infraction_type_id'],
                    'plate' => $validated['plate'],
                    'date' => $validated['date'],
                    'location' => $validated['location'],
                    'reason' => $infractionType->description,
                    'amount' => $infractionType->base_amount,
                    'citation_number' => $validated['citation_number'],
                    'vehicle_model' => $validated['vehicle_model'],
                    'vehicle_year' => $validated['vehicle_year'],
                    'driver_license' => $validated['driver_license'],
                ]);

                // Gera o texto do recurso com dados adicionais
                $customDetails = $validated['custom_details'] ?? '';
                $generatedText = $this->openAIService->generateAppealText($ticket, [
                    'name' => $validated['name'],
                    'cpf' => $validated['cpf'],
                    'address' => $validated['address'],
                    'custom_details' => $customDetails,
                ]);
            } else {
                // Validação para o formulário original (apenas seleção de ticket)
                $validated = $request->validate([
                    'ticket_id' => 'required|exists:tickets,id',
                ]);

                $ticket = Ticket::findOrFail($validated['ticket_id']);
                $this->authorize('view', $ticket);
                $user = Auth::user();

                // Verificar se o usuário tem créditos suficientes
                if (!$user->hasEnoughCredits(1)) {
                    return back()->withInput()->withErrors([
                        'error' => 'Você não tem créditos suficientes para gerar um recurso.',
                    ]);
                }

                // Gera o texto do recurso
                $generatedText = $this->openAIService->generateAppealText($ticket);
            }

            // Cria o recurso no banco de dados
            $appeal = new Appeal([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'generated_text' => $generatedText,
                'status' => 'pending',
            ]);

            // Gera o PDF e salva o caminho
            $pdfPath = $this->pdfService->generatePDF($appeal);
            $appeal->pdf_path = $pdfPath;

            // Salva o recurso
            $appeal->save();

            // Registra o consumo de crédito
            app(CreditService::class)->consumeCreditsForAppeal($user, $appeal);

            return redirect()->route('appeals.show', $appeal->id)
                ->with('success', 'Recurso gerado com sucesso!');
        } catch (\Exception $e) {
            // Registro detalhado do erro
            Log::error('Erro detalhado ao gerar recurso: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()->withInput()->withErrors([
                'error' => 'Erro ao gerar o recurso: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Exibe um recurso específico.
     */
    public function show(Appeal $appeal): View
    {
        $this->authorize('view', $appeal);
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

        if (!$appeal->pdf_path || !Storage::exists('public/' . $appeal->pdf_path)) {
            return back()->withErrors([
                'error' => 'O arquivo do recurso não está disponível.',
            ]);
        }

        return Storage::download('public/' . $appeal->pdf_path, 'recurso.pdf');
    }
}
