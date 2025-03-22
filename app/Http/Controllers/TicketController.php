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
        return view('tickets.create');
    }

    /**
     * Armazena uma nova multa no banco de dados.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:14',
            'driver_license' => 'required|string|max:255',
            'driver_license_category' => 'required|string|max:5',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'plate' => 'required|string|max:10',
            'vehicle_model' => 'required|string|max:255',
            'vehicle_year' => 'required|integer',
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

        $ticket = Auth::user()->tickets()->create($validated);

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
            'reason' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'citation_number' => 'nullable|string|max:255',
            'vehicle_model' => 'nullable|string|max:255',
            'vehicle_year' => 'nullable|string|max:4',
            'driver_license' => 'nullable|string|max:20',
            'vehicle_chassi' => 'nullable|string|max:17',
            'vehicle_renavam' => 'nullable|string|max:11',
            'infraction_type_id' => 'nullable|exists:infraction_types,id',
        ], [
            'vehicle_chassi.max' => 'O campo chassi deve ter no máximo 17 caracteres.',
            'vehicle_renavam.max' => 'O campo RENAVAM deve ter no máximo 11 caracteres.'
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
