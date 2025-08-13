<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TrafficTicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TrafficTicketController extends Controller
{
    protected $trafficTicketService;

    public function __construct(TrafficTicketService $trafficTicketService)
    {
        $this->trafficTicketService = $trafficTicketService;
    }

    /**
     * Mostra a página de busca de multas
     */
    public function index()
    {
        $user = Auth::user();
        
        // Buscar multas existentes do usuário
        $existingTickets = $user->tickets()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('traffic-tickets.index', compact('existingTickets'));
    }

    /**
     * Busca multas por CPF
     */
    public function searchByCpf(Request $request)
    {
        try {
            $request->validate([
                'cpf' => 'required|string|min:11|max:14'
            ]);

            $cpf = $request->input('cpf');
            $user = Auth::user();

            Log::info('🔍 Iniciando busca de multas por CPF', [
                'cpf' => $this->maskCpf($cpf),
                'user_id' => $user->id
            ]);

            $result = $this->trafficTicketService->searchByCpf($cpf, $user);

            if ($result['success']) {
                Log::info('✅ Busca por CPF concluída com sucesso', [
                    'total_encontradas' => $result['total_tickets'],
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Encontradas {$result['total_tickets']} multas!",
                    'total_tickets' => $result['total_tickets'],
                    'tickets' => $result['tickets']
                ]);
            } else {
                Log::warning('⚠️ Busca por CPF não retornou resultados', [
                    'cpf' => $this->maskCpf($cpf),
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma multa encontrada para este CPF.',
                    'total_tickets' => 0
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ Erro na busca por CPF: ' . $e->getMessage(), [
                'cpf' => $request->input('cpf'),
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao buscar multas. Tente novamente.',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Busca multas por CNH
     */
    public function searchByCnh(Request $request)
    {
        try {
            $request->validate([
                'cnh' => 'required|string|min:9|max:11'
            ]);

            $cnh = $request->input('cnh');
            $user = Auth::user();

            Log::info('🔍 Iniciando busca de multas por CNH', [
                'cnh' => $this->maskCnh($cnh),
                'user_id' => $user->id
            ]);

            $result = $this->trafficTicketService->searchByCnh($cnh, $user);

            if ($result['success']) {
                Log::info('✅ Busca por CNH concluída com sucesso', [
                    'total_encontradas' => $result['total_tickets'],
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Encontradas {$result['total_tickets']} multas!",
                    'total_tickets' => $result['total_tickets'],
                    'tickets' => $result['tickets']
                ]);
            } else {
                Log::warning('⚠️ Busca por CNH não retornou resultados', [
                    'cnh' => $this->maskCnh($cnh),
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma multa encontrada para esta CNH.',
                    'total_tickets' => 0
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ Erro na busca por CNH: ' . $e->getMessage(), [
                'cnh' => $request->input('cnh'),
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao buscar multas. Tente novamente.',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Busca multas por placa
     */
    public function searchByPlate(Request $request)
    {
        try {
            $request->validate([
                'plate' => 'required|string|min:6|max:8'
            ]);

            $plate = $request->input('plate');
            $user = Auth::user();

            Log::info('🔍 Iniciando busca de multas por placa', [
                'plate' => $plate,
                'user_id' => $user->id
            ]);

            $result = $this->trafficTicketService->searchByPlate($plate, $user);

            if ($result['success']) {
                Log::info('✅ Busca por placa concluída com sucesso', [
                    'total_encontradas' => $result['total_tickets'],
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Encontradas {$result['total_tickets']} multas!",
                    'total_tickets' => $result['total_tickets'],
                    'tickets' => $result['tickets']
                ]);
            } else {
                Log::warning('⚠️ Busca por placa não retornou resultados', [
                    'plate' => $plate,
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma multa encontrada para esta placa.',
                    'total_tickets' => 0
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ Erro na busca por placa: ' . $e->getMessage(), [
                'plate' => $request->input('plate'),
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao buscar multas. Tente novamente.',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Busca multas por todos os documentos do usuário
     */
    public function searchAll(Request $request)
    {
        try {
            $user = Auth::user();
            
            Log::info('🔍 Iniciando busca completa de multas', [
                'user_id' => $user->id
            ]);

            $results = [];
            $totalTickets = 0;

            // Buscar por CPF se disponível
            if ($user->cpf) {
                $cpfResult = $this->trafficTicketService->searchByCpf($user->cpf, $user);
                if ($cpfResult['success']) {
                    $results['cpf'] = $cpfResult;
                    $totalTickets += $cpfResult['total_tickets'];
                }
            }

            // Buscar por CNH se disponível
            if ($user->cnh_category) {
                // Assumir que o usuário tem CNH se tem categoria
                $cnhResult = $this->trafficTicketService->searchByCnh($user->id . '000', $user);
                if ($cnhResult['success']) {
                    $results['cnh'] = $cnhResult;
                    $totalTickets += $cnhResult['total_tickets'];
                }
            }

            // Buscar por placa se o usuário tiver veículos cadastrados
            $userPlates = $user->tickets()->distinct()->pluck('plate')->filter();
            foreach ($userPlates as $plate) {
                $plateResult = $this->trafficTicketService->searchByPlate($plate, $user);
                if ($plateResult['success']) {
                    $results['plates'][$plate] = $plateResult;
                    $totalTickets += $plateResult['total_tickets'];
                }
            }

            Log::info('✅ Busca completa concluída', [
                'total_encontradas' => $totalTickets,
                'user_id' => $user->id,
                'sources' => array_keys($results)
            ]);

            return response()->json([
                'success' => true,
                'message' => "Busca completa concluída! Total: {$totalTickets} multas",
                'total_tickets' => $totalTickets,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro na busca completa: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao realizar busca completa. Tente novamente.',
                'debug' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Mostra estatísticas das multas
     */
    public function statistics()
    {
        $user = Auth::user();
        
        $stats = [
            'total_tickets' => $user->tickets()->count(),
            'total_amount' => $user->tickets()->sum('amount'),
            'total_points' => $user->tickets()->sum('points'),
            'by_source' => $user->tickets()
                ->selectRaw('source, COUNT(*) as count, SUM(amount) as total_amount')
                ->groupBy('source')
                ->get(),
            'by_month' => $user->tickets()
                ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get()
        ];

        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }

    /**
     * Mascara CPF para logs
     */
    private function maskCpf(string $cpf): string
    {
        $clean = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($clean) === 11) {
            return substr($clean, 0, 3) . '.***.***-' . substr($clean, -2);
        }
        return '***' . substr($clean, -3);
    }

    /**
     * Mascara CNH para logs
     */
    private function maskCnh(string $cnh): string
    {
        $clean = preg_replace('/[^0-9]/', '', $cnh);
        if (strlen($clean) >= 9) {
            return substr($clean, 0, 3) . '***' . substr($clean, -3);
        }
        return '***' . substr($clean, -3);
    }
}
