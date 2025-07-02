<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CreditTransaction;
use App\Services\AbacatePayService;
use Illuminate\Support\Facades\Log;

class CheckPendingPayments extends Command
{
    protected $signature = 'payments:check-pending';
    protected $description = 'Verifica pagamentos pendentes na AbacatePay';

    private $abacatePayService;

    public function __construct(AbacatePayService $abacatePayService)
    {
        parent::__construct();
        $this->abacatePayService = $abacatePayService;
    }

    public function handle()
    {
        $this->info('Verificando pagamentos pendentes...');
        
        // Busca transações pendentes das últimas 24 horas
        $pendingTransactions = CreditTransaction::where('status', 'pending')
            ->where('payment_method', 'pix')
            ->where('created_at', '>=', now()->subDay())
            ->get();

        $this->info("Encontradas {$pendingTransactions->count()} transações pendentes.");

        foreach ($pendingTransactions as $transaction) {
            $this->info("Verificando transação #{$transaction->id} ({$transaction->reference})...");
            
            // Pula transações sem referência
            if (!$transaction->reference) {
                $this->warn("⚠️ Transação #{$transaction->id} não tem referência da AbacatePay");
                continue;
            }
            
            try {
                $result = $this->abacatePayService->getBillingStatus($transaction->reference);
                
                if ($result['success'] && $result['status'] === 'PAID') {
                    // Confirma o pagamento
                    app(\App\Services\CreditService::class)->confirmTransaction($transaction);
                    
                    $this->info("✅ Transação #{$transaction->id} confirmada!");
                    Log::info("Pagamento confirmado via verificação automática", [
                        'transaction_id' => $transaction->id,
                        'billing_id' => $transaction->reference
                    ]);
                } else if ($result['success'] && in_array($result['status'], ['EXPIRED', 'CANCELLED'])) {
                    // Atualiza status
                    $transaction->update([
                        'status' => strtolower($result['status']),
                        'metadata' => array_merge($transaction->metadata ?? [], [
                            'status_checked_at' => now()->toISOString(),
                            'final_status' => $result['status']
                        ])
                    ]);
                    
                    $this->info("❌ Transação #{$transaction->id} {$result['status']}");
                }
            } catch (\Exception $e) {
                $this->error("Erro ao verificar transação #{$transaction->id}: {$e->getMessage()}");
                Log::error("Erro ao verificar pagamento pendente", [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info('Verificação concluída!');
    }
} 