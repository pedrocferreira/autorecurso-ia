<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

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
        $tickets_count = DB::table('tickets')->where('user_id', $user->id)->count();
        $appeals_count = DB::table('appeals')->where('user_id', $user->id)->count();
        $appeals_pending = DB::table('appeals')->where('user_id', $user->id)->where('status', 'pending')->count();
        $appeals_sent = DB::table('appeals')->where('user_id', $user->id)->where('status', 'sent')->count();
        $appeals_successful = DB::table('appeals')->where('user_id', $user->id)->where('status', 'successful')->count();
        $appeals_rejected = DB::table('appeals')->where('user_id', $user->id)->where('status', 'rejected')->count();

        $stats = [
            'tickets_count' => $tickets_count,
            'appeals_count' => $appeals_count,
            'appeals_pending' => $appeals_pending,
            'appeals_sent' => $appeals_sent,
            'appeals_successful' => $appeals_successful,
            'appeals_rejected' => $appeals_rejected,
        ];

        // Multas recentes
        $recent_tickets = $user->tickets()->latest()->take(5)->get();

        // Recursos recentes
        $recent_appeals = $user->appeals()->with('ticket')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recent_tickets', 'recent_appeals'));
    }
}
