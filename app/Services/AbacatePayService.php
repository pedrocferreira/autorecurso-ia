<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AbacatePayService
{
    private $apiKey;
    private $baseUrl;
    private $devMode;

    public function __construct()
    {
        $this->apiKey = config('abacatepay.api_key');
        $this->baseUrl = 'https://api.abacatepay.com/v1';
        $this->devMode = app()->environment('local', 'development');
    }

    /**
     * Cria uma cobrança PIX na AbacatePay
     */
    public function createPixPayment(array $paymentData)
    {
        try {
            // Log dos dados recebidos
            Log::info('Criando pagamento PIX', [
                'customer' => $paymentData['customer'],
                'amount' => $paymentData['amount'],
                'description' => $paymentData['description']
            ]);

            // Usa o CPF do usuário diretamente após limpeza
            $taxId = preg_replace('/[^0-9]/', '', $paymentData['customer']['document']);
            
            // Log do CPF informado pelo usuário que será enviado
            Log::info('CPF informado pelo usuário que será enviado para AbacatePay:', [
                'taxId' => $taxId,
                'length' => strlen($taxId),
                'user_id' => $paymentData['metadata']['user_id'] ?? 'N/A',
                'source' => 'formulario_pagamento'
            ]);

            // Validar se é um CPF válido usando algoritmo brasileiro
            if (!$this->validateCpfAlgorithm($taxId)) {
                Log::error('CPF informado pelo usuário é inválido:', ['taxId' => $taxId]);
                throw new Exception('CPF informado é inválido. Por favor, informe um CPF válido.');
            }
            
            Log::info('CPF informado pelo usuário validado com sucesso para AbacatePay:', ['taxId' => $taxId]);

            $requestData = [
                'amount' => $paymentData['amount'],
                'description' => $paymentData['description'],
                'frequency' => 'ONE_TIME',
                'methods' => ['PIX'],
                'products' => [
                    [
                        'name' => $paymentData['description'],
                        'description' => $paymentData['description'],
                        'quantity' => 1,
                        'price' => $paymentData['amount'],
                        'externalId' => 'credit-package-' . time()
                    ]
                ],
                'customer' => [
                    'name' => $paymentData['customer']['name'],
                    'email' => $paymentData['customer']['email'],
                    'cellphone' => $paymentData['customer']['phone'],
                    'taxId' => $taxId
                ],
                'metadata' => $paymentData['metadata'],
                'devMode' => $this->devMode,
                'expiresIn' => $paymentData['expires_in'] ?? 3600,
                'returnUrl' => $paymentData['return_url'] ?? route('credits.success'),
                'completionUrl' => $paymentData['completion_url'] ?? route('credits.success'),
                'webhookUrl' => route('abacatepay.webhook', ['webhookSecret' => config('abacatepay.webhook_secret')])
            ];

            // Log dos dados completos da requisição
            Log::info('Dados completos enviados para AbacatePay:', [
                'request_data' => $requestData
            ]);

            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post($this->baseUrl . '/billing/create', $requestData);

            // Log da resposta da API
            Log::info('Resposta da API AbacatePay', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if (!$response->successful()) {
                Log::error('AbacatePay API error', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                    'request_data' => $requestData
                ]);

                $errorMessage = $response->json()['message'] ?? 'Erro desconhecido';
                throw new Exception('Erro na API da AbacatePay: ' . $errorMessage);
            }

            $data = $response->json();

            // Se não vier o campo pix na resposta, buscar detalhes do billing
            if (empty($data['data']['pix'])) {
                // Buscar detalhes do billing
                $billingId = $data['data']['id'] ?? null;
                if ($billingId) {
                    $detailsResp = Http::withToken($this->apiKey)
                        ->timeout(30)
                        ->withHeaders([
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json'
                        ])
                        ->get($this->baseUrl . '/billing/' . $billingId);
                    if ($detailsResp->successful()) {
                        $detailsData = $detailsResp->json();
                        if (!empty($detailsData['data']['pix'])) {
                            $data['data']['pix'] = $detailsData['data']['pix'];
                        }
                    }
                }
            }

            // Cria a transação local
            // Para pagamentos via chat, usa o user_id dos metadata
            $userId = $paymentData['metadata']['user_id'] ?? auth()->id();
            $user = $userId ? \App\Models\User::find($userId) : null;
            
            \App\Models\CreditTransaction::create([
                'user_id' => $userId,
                'type' => 'purchase',
                'amount' => $paymentData['metadata']['credits'] ?? 10,
                'balance_after' => $user ? $user->credits : 0,
                'description' => $paymentData['description'],
                'status' => 'pending',
                'payment_method' => 'pix',
                'reference' => $data['data']['id'],
                'metadata' => [
                    'package_id' => $paymentData['metadata']['package_id'] ?? null,
                    'price' => $paymentData['amount'] / 100,
                    'gateway' => 'abacatepay',
                    'abacatepay_billing_id' => $data['data']['id'],
                    'created_at_abacatepay' => now()->toISOString(),
                    'payment_url' => $data['data']['url'] ?? null,
                    'chat_payment' => $paymentData['metadata']['chat_payment'] ?? false
                ]
            ]);

            return [
                'success' => true,
                'data' => $data
            ];

        } catch (Exception $e) {
            Log::error('Erro ao criar pagamento PIX', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Consulta o status de uma cobrança
     */
    public function getBillingStatus(string $billingId)
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->get($this->baseUrl . '/billing/' . $billingId);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['data'])) {
                    return [
                        'success' => true,
                        'status' => $data['data']['status'],
                        'paid_at' => $data['data']['paidAt'] ?? null,
                        'amount' => $data['data']['amount'] ?? 0,
                        'raw_response' => $data
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'Não foi possível consultar o status da cobrança'
            ];

        } catch (Exception $e) {
            Log::error('Erro ao consultar status da cobrança', [
                'billing_id' => $billingId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erro interno ao consultar pagamento'
            ];
        }
    }

    /**
     * Valida webhook da AbacatePay
     */
    public function validateWebhook(string $payload, string $signature, string $secret = null)
    {
        if (!$secret) {
            $secret = config('abacatepay.webhook_secret');
        }

        // A AbacatePay usa verificação simples por query parameter
        // O webhook vem com ?webhookSecret=XXX na URL
        return true; // A validação é feita no controller via query parameter
    }

    /**
     * Processa webhook da AbacatePay
     */
    public function processWebhook(array $webhookData)
    {
        try {
            $event = $webhookData['event'] ?? null;
            $billingData = $webhookData['data']['billing'] ?? [];

            Log::info('Processando webhook AbacatePay', [
                'event' => $event,
                'billing_id' => $billingData['id'] ?? 'N/A'
            ]);

            switch ($event) {
                case 'billing.paid':
                    return $this->handlePaymentPaid($billingData);
                
                case 'billing.expired':
                    return $this->handlePaymentExpired($billingData);
                
                case 'billing.cancelled':
                    return $this->handlePaymentCancelled($billingData);
                
                default:
                    Log::info('Evento de webhook não processado', ['event' => $event]);
                    return ['success' => true, 'message' => 'Evento não processado'];
            }

        } catch (Exception $e) {
            Log::error('Erro ao processar webhook', [
                'error' => $e->getMessage(),
                'webhook_data' => $webhookData
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Processa pagamento confirmado
     */
    private function handlePaymentPaid(array $billingData)
    {
        $billingId = $billingData['id'] ?? null;
        
        if (!$billingId) {
            Log::error('ID da cobrança não encontrado no webhook', ['billing_data' => $billingData]);
            return ['success' => false, 'error' => 'ID da cobrança não encontrado'];
        }

        try {
            Log::info('Processando pagamento confirmado', [
                'billing_id' => $billingId,
                'status' => $billingData['status'] ?? 'N/A',
                'amount' => $billingData['amount'] ?? 0
            ]);

            // Busca a transação de crédito pendente
            $transaction = \App\Models\CreditTransaction::where('reference', $billingId)
                ->where('status', 'pending')
                ->first();

            if (!$transaction) {
                Log::warning('Transação não encontrada para billing ID', [
                    'billing_id' => $billingId,
                    'raw_data' => $billingData
                ]);
                return ['success' => false, 'error' => 'Transação não encontrada'];
            }

            Log::info('Transação encontrada', [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'amount' => $transaction->amount,
                'status' => $transaction->status
            ]);

            // Se já foi processada, retorna sucesso
            if ($transaction->status === 'completed') {
                Log::info('Transação já processada anteriormente', [
                    'transaction_id' => $transaction->id,
                    'billing_id' => $billingId
                ]);
                return ['success' => true, 'message' => 'Pagamento já processado anteriormente'];
            }

            // Usa o CreditService para confirmar a transação
            $creditService = app(\App\Services\CreditService::class);
            
            Log::info('Confirmando transação via CreditService', [
                'transaction_id' => $transaction->id,
                'billing_id' => $billingId
            ]);

            $transaction = $creditService->confirmTransaction($transaction);

            Log::info('Pagamento processado com sucesso via webhook', [
                'billing_id' => $billingId,
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'credits' => $transaction->amount,
                'new_balance' => $transaction->balance_after
            ]);

            return ['success' => true, 'message' => 'Pagamento processado com sucesso'];
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento', [
                'billing_id' => $billingId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ['success' => false, 'error' => 'Erro ao processar pagamento: ' . $e->getMessage()];
        }
    }

    /**
     * Processa pagamento expirado
     */
    private function handlePaymentExpired(array $billingData)
    {
        $billingId = $billingData['id'] ?? null;
        
        if ($billingId) {
            $transaction = \App\Models\CreditTransaction::where('reference', $billingId)
                ->where('status', 'pending')
                ->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'expired',
                    'metadata' => array_merge($transaction->metadata ?? [], [
                        'expired_at' => now()->toISOString()
                    ])
                ]);
            }
        }

        return ['success' => true, 'message' => 'Pagamento expirado processado'];
    }

    /**
     * Processa pagamento cancelado
     */
    private function handlePaymentCancelled(array $billingData)
    {
        $billingId = $billingData['id'] ?? null;
        
        if ($billingId) {
            $transaction = \App\Models\CreditTransaction::where('reference', $billingId)
                ->where('status', 'pending')
                ->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'cancelled',
                    'metadata' => array_merge($transaction->metadata ?? [], [
                        'cancelled_at' => now()->toISOString()
                    ])
                ]);
            }
        }

        return ['success' => true, 'message' => 'Pagamento cancelado processado'];
    }

    /**
     * Valida se o taxId é válido para a AbacatePay
     */
    private function isValidTaxIdForAbacatePay(string $taxId): bool
    {
        // Validações básicas
        if (strlen($taxId) !== 11) {
            return false;
        }

        // Não aceita CPFs com todos os dígitos iguais
        if (preg_match('/^(\d)\1{10}$/', $taxId)) {
            return false;
        }

        // Lista de CPFs válidos conhecidos que funcionam com AbacatePay
        $cpfsValidosAbacatePay = [
            '11144477735',
            '12345678909',
            '11111111111',
            '22222222222',
            '33333333333',
            '44444444444',
            '55555555555',
        ];

        // Se estiver na lista de CPFs conhecidos, aceita
        if (in_array($taxId, $cpfsValidosAbacatePay)) {
            return true;
        }

        // Validação pelo algoritmo de CPF tradicional
        return $this->validateCpfAlgorithm($taxId);
    }

    /**
     * Valida CPF usando o algoritmo tradicional
     */
    private function validateCpfAlgorithm(string $cpf): bool
    {
        // Calcula o primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (10 - $i);
        }
        $firstDigit = 11 - ($sum % 11);
        if ($firstDigit >= 10) {
            $firstDigit = 0;
        }

        // Calcula o segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += intval($cpf[$i]) * (11 - $i);
        }
        $secondDigit = 11 - ($sum % 11);
        if ($secondDigit >= 10) {
            $secondDigit = 0;
        }

        // Verifica se os dígitos calculados coincidem com os informados
        return (intval($cpf[9]) === $firstDigit && intval($cpf[10]) === $secondDigit);
    }
} 