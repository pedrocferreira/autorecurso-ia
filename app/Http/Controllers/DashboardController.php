<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Construtor que aplica middleware de autenticação.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe o painel principal com estatísticas do usuário.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Estatísticas para o dashboard
        $stats = [
            'tickets_count' => $user->tickets()->count(),
            'appeals_count' => $user->appeals()->count(),
            'appeals_pending' => $user->appeals()->where('status', 'pending')->count(),
            'appeals_sent' => $user->appeals()->where('status', 'sent')->count(),
            'appeals_successful' => $user->appeals()->where('status', 'successful')->count(),
            'appeals_rejected' => $user->appeals()->where('status', 'rejected')->count(),
        ];

        // Multas recentes
        $recent_tickets = $user->tickets()->latest()->take(5)->get();

        // Recursos recentes
        $recent_appeals = $user->appeals()->with('ticket')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recent_tickets', 'recent_appeals'));
    }
}
