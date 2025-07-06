<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\CreditTransaction;

class SimulateChatWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:simulate-webhook {billing_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simula o recebimento do webhook de pagamento aprovado do chat wizard (AbacatePay)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $billingId = $this->argument('billing_id');
        $transaction = CreditTransaction::where('reference', $billingId)
            ->where('payment_method', 'pix')
            ->whereJsonContains('metadata->source', 'chat_wizard')
            ->first();

        if (!$transaction) {
            $this->error('Transação não encontrada para o billing_id informado.');
            return 1;
        }

        $webhookSecret = config('abacatepay.webhook_secret');
        $url = url("/chat/webhook/abacatepay?webhookSecret={$webhookSecret}");

        $payload = [
            'event' => 'billing.paid',
            'data' => [
                'id' => $billingId,
                'status' => 'PAID',
                'amount' => $transaction->amount,
                'customer' => [
                    'id' => $transaction->user_id,
                    'metadata' => [
                        'name' => $transaction->user->name,
                        'cellphone' => $transaction->user->phone,
                        'taxId' => $transaction->user->cpf,
                        'email' => $transaction->user->email,
                    ]
                ]
            ],
            'devMode' => true,
            'webhookSecret' => $webhookSecret
        ];

        $this->info('Enviando webhook para: ' . $url);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($url, $payload);

        $this->info('Status: ' . $response->status());
        $this->info('Resposta: ' . $response->body());
        return 0;
    }
} 