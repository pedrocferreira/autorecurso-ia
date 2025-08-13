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

        // Log para debug
        \Log::info('🔍 Dashboard - Usuário logado', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'credits' => $user->credits
        ]);

        try {
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

            \Log::info('📊 Dashboard - Estatísticas calculadas', [
                'stats' => $stats,
                'tickets_count' => count($recent_tickets),
                'appeals_count' => count($recent_appeals)
            ]);

            return view('dashboard', compact('stats', 'recent_tickets', 'recent_appeals'));

        } catch (\Exception $e) {
            \Log::error('❌ Erro no dashboard', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Retornar dados vazios em caso de erro
            $stats = [
                'tickets_count' => 0,
                'appeals_count' => 0,
                'appeals_pending' => 0,
                'appeals_sent' => 0,
                'appeals_successful' => 0,
                'appeals_rejected' => 0,
            ];

            $recent_tickets = collect();
            $recent_appeals = collect();

        return view('dashboard', compact('stats', 'recent_tickets', 'recent_appeals'));
        }
    }
}
