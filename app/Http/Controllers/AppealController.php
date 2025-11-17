<?php

namespace App\Http\Controllers;

use App\Models\Appeal;
use App\Models\Ticket;
use App\Services\GeminiService;
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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AppealController extends Controller
{
    protected $geminiService;
    protected $pdfService;

    /**
     * Construtor que inicializa os serviços e aplica middleware de autenticação.
     */
    public function __construct(GeminiService $geminiService, PDFService $pdfService)
    {
        $this->middleware('auth');
        $this->geminiService = $geminiService;
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
            'plate', 'vehicle_model', 'vehicle_chassi', 'vehicle_renavam',
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
                'plate' => 'required|string|max:7',
                'vehicle_model' => 'required|string|max:100',
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
            $appealText = $this->geminiService->generateAppealText($ticket, $request->all());
            
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
