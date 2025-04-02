<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\InfractionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TicketController extends Controller
{
    /**
     * Construtor que aplica middleware de autenticação.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe uma lista de multas do usuário.
     */
    public function index(): View
    {
        $tickets = Ticket::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Mostra o formulário para criar uma nova multa.
     */
    public function create(): View
    {
        $infractionTypes = InfractionType::where('active', true)
            ->orderBy('code')
            ->get();
        return view('tickets.create', compact('infractionTypes'));
    }

    /**
     * Armazena uma nova multa no banco de dados.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Dados Pessoais
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:14',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'cnh_number' => 'required|string|max:11',
            'cnh_category' => 'required|string|max:2',
            'cnh_expiration' => 'required|date',

            // Dados do Veículo
            'vehicle_plate' => 'required|string|max:7',
            'vehicle_chassi' => 'required|string|max:17',
            'vehicle_renavam' => 'required|string|max:11',
            'vehicle_brand' => 'required|string|max:100',
            'vehicle_model' => 'required|string|max:255',
            'vehicle_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'vehicle_color' => 'required|string|max:50',
            'vehicle_owner' => 'required|string|max:255',

            // Dados da Infração
            'infraction_type_id' => 'required|exists:infraction_types,id',
            'infraction_date' => 'required|date',
            'infraction_location' => 'required|string|max:255',
            'infraction_agent' => 'required|string|max:100',
            'infraction_equipment' => 'nullable|string|max:100',
            'infraction_points' => 'required|integer|min:0',
            'infraction_amount' => 'required|numeric|min:0',
            'client_justification' => 'required|string|max:5000',
            'process_number' => 'nullable|string|max:100',
        ], [
            'vehicle_chassi.max' => 'O campo chassi deve ter no máximo 17 caracteres.',
            'vehicle_renavam.max' => 'O campo RENAVAM deve ter no máximo 11 caracteres.',
            'client_justification.max' => 'A justificativa não pode exceder 5000 caracteres.'
        ]);

        // Obter os detalhes da infração
        $infractionType = InfractionType::findOrFail($request->infraction_type_id);
        
        // Se os pontos ou valor estiverem vazios, use os valores do tipo de infração
        if (empty($validated['infraction_points']) || $validated['infraction_points'] == 0) {
            $validated['infraction_points'] = $infractionType->points;
        }
        
        // Garantir que o valor da infração nunca seja nulo
        if (!isset($validated['infraction_amount']) || $validated['infraction_amount'] === null || $validated['infraction_amount'] === '' || floatval($validated['infraction_amount']) == 0) {
            // Usar base_amount em vez de amount
            $validated['infraction_amount'] = $infractionType->base_amount;
        } else {
            // Converter para float para garantir que seja um número válido
            $validated['infraction_amount'] = floatval($validated['infraction_amount']);
        }

        $ticket = Auth::user()->tickets()->create($validated);

        // Gerar recurso automaticamente
        if ($ticket) {
            try {
                // Extrair cidade do endereço do usuário
                $address = $validated['address'];
                $cityMatch = [];
                preg_match('/([A-Za-zÀ-ÿ\s]+)(?:\s*-\s*[A-Z]{2})?(?:\s*-\s*CEP:?.*)?$/', $address, $cityMatch);
                $city = !empty($cityMatch[1]) ? trim($cityMatch[1]) : 'São Paulo';
                
                // Dados para o recurso
                $appealData = [
                    'ticket_id' => $ticket->id,
                    'user_id' => Auth::id(),
                    'status' => 'pending',
                    'text' => null, // Será gerado pelo serviço
                    'location' => $city, // Cidade extraída do endereço
                    'generated_at' => now(),
                ];
                
                // Criar o recurso
                $appeal = \App\Models\Appeal::create($appealData);
                
                // Gerar o texto e PDF do recurso via AppealController
                $appealController = app()->make(\App\Http\Controllers\AppealController::class);
                $appeal = $appealController->generateAppealForTicket($ticket, $appeal);
                
                return redirect()->route('appeals.show', $appeal->id)
                    ->with('success', 'Multa cadastrada e recurso gerado com sucesso!');
            } catch (\Exception $e) {
                \Log::error('Erro ao gerar recurso automático: ' . $e->getMessage());
                return redirect()->route('tickets.show', $ticket->id)
                    ->with('warning', 'Multa cadastrada com sucesso, mas houve um erro ao gerar o recurso automático. Por favor, gere o recurso manualmente.');
            }
        }

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Multa cadastrada com sucesso!');
    }

    /**
     * Exibe uma multa específica.
     */
    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Mostra o formulário para editar uma multa.
     */
    public function edit(Ticket $ticket): View
    {
        $this->authorize('update', $ticket);
        $infractionTypes = InfractionType::where('active', true)
            ->orderBy('code')
            ->get();
        return view('tickets.edit', compact('ticket', 'infractionTypes'));
    }

    /**
     * Atualiza uma multa específica no banco de dados.
     */
    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate([
            'plate' => 'required|string|max:10',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'client_justification' => 'required|string|max:5000',
            'amount' => 'required|numeric|min:0',
            'citation_number' => 'nullable|string|max:255',
            'vehicle_model' => 'nullable|string|max:255',
            'vehicle_year' => 'nullable|string|max:4',
            'driver_license' => 'nullable|string|max:20',
            'vehicle_chassi' => 'nullable|string|max:17',
            'vehicle_renavam' => 'nullable|string|max:11',
            'infraction_type_id' => 'nullable|exists:infraction_types,id',
            'infraction_equipment' => 'nullable|string|max:100',
            'process_number' => 'nullable|string|max:100',
        ], [
            'vehicle_chassi.max' => 'O campo chassi deve ter no máximo 17 caracteres.',
            'vehicle_renavam.max' => 'O campo RENAVAM deve ter no máximo 11 caracteres.',
            'client_justification.max' => 'A justificativa não pode exceder 5000 caracteres.'
        ]);

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Multa atualizada com sucesso!');
    }

    /**
     * Remove uma multa do banco de dados.
     */
    public function destroy(Ticket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Multa excluída com sucesso!');
    }
}
