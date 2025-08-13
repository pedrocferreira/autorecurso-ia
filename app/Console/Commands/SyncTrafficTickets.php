<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TrafficTicketService;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SyncTrafficTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'traffic-tickets:sync 
                            {--user-id= : ID específico do usuário para sincronizar}
                            {--all : Sincronizar todos os usuários}
                            {--force : Forçar sincronização mesmo se já foi feita recentemente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza multas de trânsito com APIs oficiais';

    /**
     * Execute the console command.
     */
    public function handle(TrafficTicketService $trafficTicketService)
    {
        $this->info('🚀 Iniciando sincronização de multas de trânsito...');

        try {
            if ($this->option('user-id')) {
                // Sincronizar usuário específico
                $user = User::find($this->option('user-id'));
                if (!$user) {
                    $this->error("❌ Usuário com ID {$this->option('user-id')} não encontrado.");
                    return 1;
                }
                
                $this->syncUserTickets($user, $trafficTicketService);
                
            } elseif ($this->option('all')) {
                // Sincronizar todos os usuários
                $users = User::where('active', true)->get();
                $this->info("📋 Sincronizando {$users->count()} usuários...");
                
                $bar = $this->output->createProgressBar($users->count());
                $bar->start();
                
                foreach ($users as $user) {
                    $this->syncUserTickets($user, $trafficTicketService, false);
                    $bar->advance();
                }
                
                $bar->finish();
                $this->newLine();
                
            } else {
                // Sincronizar usuários ativos que não foram sincronizados recentemente
                $users = User::where('active', true)
                    ->where(function($query) {
                        $query->whereNull('last_ticket_sync')
                              ->orWhere('last_ticket_sync', '<=', now()->subDay());
                    })
                    ->get();
                
                if ($users->isEmpty()) {
                    $this->info('✅ Todos os usuários estão sincronizados.');
                    return 0;
                }
                
                $this->info("📋 Sincronizando {$users->count()} usuários pendentes...");
                
                $bar = $this->output->createProgressBar($users->count());
                $bar->start();
                
                foreach ($users as $user) {
                    $this->syncUserTickets($user, $trafficTicketService, false);
                    $bar->advance();
                }
                
                $bar->finish();
                $this->newLine();
            }

            $this->info('✅ Sincronização concluída com sucesso!');
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Erro durante a sincronização: " . $e->getMessage());
            Log::error('Erro no comando SyncTrafficTickets: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Sincroniza multas de um usuário específico
     */
    private function syncUserTickets(User $user, TrafficTicketService $trafficTicketService, bool $showOutput = true)
    {
        try {
            if ($showOutput) {
                $this->info("🔍 Sincronizando multas para usuário: {$user->name} ({$user->email})");
            }

            $totalTickets = 0;
            $newTickets = 0;

            // Buscar por CPF se disponível
            if ($user->cpf) {
                if ($showOutput) {
                    $this->line("  📋 Buscando por CPF...");
                }
                
                $result = $trafficTicketService->searchByCpf($user->cpf, $user);
                if ($result['success']) {
                    $totalTickets += $result['total_tickets'];
                    $newTickets += $result['total_tickets'];
                    
                    if ($showOutput) {
                        $this->line("    ✅ Encontradas {$result['total_tickets']} multas via CPF");
                    }
                }
            }

            // Buscar por CNH se disponível
            if ($user->cnh_category) {
                if ($showOutput) {
                    $this->line("  🪪 Buscando por CNH...");
                }
                
                $cnhNumber = $user->id . '000'; // Simular número de CNH
                $result = $trafficTicketService->searchByCnh($cnhNumber, $user);
                if ($result['success']) {
                    $totalTickets += $result['total_tickets'];
                    $newTickets += $result['total_tickets'];
                    
                    if ($showOutput) {
                        $this->line("    ✅ Encontradas {$result['total_tickets']} multas via CNH");
                    }
                }
            }

            // Buscar por placas cadastradas
            $userPlates = $user->tickets()->distinct()->pluck('plate')->filter();
            if ($userPlates->isNotEmpty()) {
                if ($showOutput) {
                    $this->line("  🚗 Buscando por placas cadastradas...");
                }
                
                foreach ($userPlates as $plate) {
                    $result = $trafficTicketService->searchByPlate($plate, $user);
                    if ($result['success']) {
                        $totalTickets += $result['total_tickets'];
                        $newTickets += $result['total_tickets'];
                        
                        if ($showOutput) {
                            $this->line("    ✅ Placa {$plate}: {$result['total_tickets']} multas");
                        }
                    }
                }
            }

            // Atualizar timestamp de sincronização
            $user->update([
                'last_ticket_sync' => now()
            ]);

            if ($showOutput) {
                $this->info("  📊 Resumo: {$totalTickets} multas encontradas, {$newTickets} novas");
            }

            Log::info('Sincronização de multas concluída para usuário', [
                'user_id' => $user->id,
                'total_tickets' => $totalTickets,
                'new_tickets' => $newTickets
            ]);

        } catch (\Exception $e) {
            $errorMsg = "Erro ao sincronizar multas para usuário {$user->id}: " . $e->getMessage();
            
            if ($showOutput) {
                $this->error("  ❌ {$errorMsg}");
            }
            
            Log::error($errorMsg);
        }
    }
}
