<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Appeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Construtor que aplica middlewares de autenticação e admin.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Exibe o painel administrativo principal.
     */
    public function index(): View
    {
        // Estatísticas gerais
        $stats = [
            'users_count' => User::count(),
            'tickets_count' => Ticket::count(),
            'appeals_count' => Appeal::count(),
            'appeals_pending' => Appeal::where('status', 'pending')->count(),
            'appeals_sent' => Appeal::where('status', 'sent')->count(),
            'appeals_successful' => Appeal::where('status', 'successful')->count(),
            'appeals_rejected' => Appeal::where('status', 'rejected')->count(),
        ];

        // Usuários mais ativos (com mais recursos)
        $top_users = User::withCount('appeals')
            ->orderBy('appeals_count', 'desc')
            ->take(5)
            ->get();

        // Estatísticas mensais de recursos gerados
        $monthly_stats = Appeal::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact('stats', 'top_users', 'monthly_stats'));
    }

    /**
     * Exibe a lista de usuários.
     */
    public function users(): View
    {
        $users = User::withCount(['tickets', 'appeals'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users', compact('users'));
    }

    /**
     * Exibe a lista de multas.
     */
    public function tickets(): View
    {
        $tickets = Ticket::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.tickets', compact('tickets'));
    }

    /**
     * Exibe a lista de recursos.
     */
    public function appeals(): View
    {
        $appeals = Appeal::with(['user', 'ticket'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.appeals', compact('appeals'));
    }
}
