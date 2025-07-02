<?php

namespace App\Http\Controllers;

use App\Models\CreditTransaction;
use App\Services\AbacatePayService;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AbacatePayController extends Controller
{
    protected $abacatePayService;
    protected $creditService;

    public function __construct(AbacatePayService $abacatePayService, CreditService $creditService)
    {
        $this->middleware('auth')->except(['webhook']);
        $this->middleware(\App\Http\Middleware\VerifyCsrfToken::class)->except(['webhook']);
        $this->abacatePayService = $abacatePayService;
        $this->creditService = $creditService;
    }

    /**
     * Limpa e valida um CPF informado pelo usuário
     */
    private function cleanAndValidateCpf(?string $cpf): ?string
    {
        if (!$cpf) {
            throw new \Exception('CPF é obrigatório para o pagamento');
        }

        // Log do CPF informado pelo usuário
        Log::info('CPF informado pelo usuário para validação:', [
            'cpf_original' => $cpf
        ]);

        // Remove tudo que não for número
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Log após limpeza
        Log::info('CPF após limpeza:', [
            'cpf_limpo' => $cpf,
            'tamanho' => strlen($cpf)
        ]);

        // Valida tamanho - deve ter exatamente 11 dígitos
        if (strlen($cpf) !== 11) {
            throw new \Exception('CPF deve ter exatamente 11 dígitos');
        }

        // Valida se todos os dígitos são iguais (CPFs inválidos)
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            throw new \Exception('CPF não pode ter todos os dígitos iguais');
        }

        // Validação completa do CPF usando algoritmo oficial
        if (!$this->isValidCpf($cpf)) {
            Log::error('CPF informado é inválido:', ['cpf' => $cpf]);
            throw new \Exception('CPF inválido. Por favor, informe um CPF válido.');
        }

        Log::info('CPF informado validado com sucesso:', ['cpf' => $cpf]);
        return $cpf;
    }

    /**
     * Valida se o CPF é válido segundo o algoritmo oficial
     */
    private function isValidCpf(string $cpf): bool
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

    /**
     * Cria uma cobrança PIX na AbacatePay
     */
    public function createPixPayment(Request $request)
    {
        $request->validate([
            'package_id' => 'required|string',
            'cpf' => 'required|string|min:11|max:14', // Aceita CPF com ou sem formatação
            'phone' => 'nullable|string'
        ]);

        $package = config('credits.packages')[$request->package_id] ?? null;
        if (!$package) {
            return back()->with('error', 'Pacote inválido.');
        }

        $user = Auth::user();

        try {
            // Log inicial dos dados recebidos
            Log::info('Dados recebidos para pagamento:', [
                'user_id' => $user->id,
                'cpf_informado' => $request->cpf,
                'phone_informado' => $request->phone,
                'package_id' => $request->package_id
            ]);

            // Limpeza e validação do CPF informado pelo usuário
            $cpf = $this->cleanAndValidateCpf($request->cpf);
            
            // Log após validação bem-sucedida
            Log::info('CPF informado pelo usuário validado com sucesso:', [
                'cpf_validado' => $cpf,
                'tamanho' => strlen($cpf)
            ]);
            
            // Limpa o número de telefone (usa o informado ou o do perfil)
            $phone = $request->phone ? preg_replace('/[^0-9]/', '', $request->phone) : preg_replace('/[^0-9]/', '', $user->phone ?? '');
            if (empty($phone)) {
                $phone = '11999999999'; // Número padrão se não houver telefone
            } elseif (strlen($phone) < 10 || strlen($phone) > 11) {
                return back()->with('error', 'Número de telefone inválido. Deve ter 10 ou 11 dígitos.');
            }

            // Log do telefone após limpeza
            Log::info('Telefone após limpeza:', [
                'phone' => $phone,
                'tamanho' => strlen($phone)
            ]);

            // Cria transação pendente no banco
            $transaction = CreditTransaction::create([
                'user_id' => $user->id,
                'type' => 'purchase',
                'amount' => $package['amount'],
                'balance_after' => $user->credits,
                'description' => "Compra PIX de {$package['amount']} créditos",
                'status' => 'pending',
                'payment_method' => 'pix',
                'metadata' => [
                    'package_id' => $request->package_id,
                    'price' => $package['price'],
                    'gateway' => 'abacatepay'
                ]
            ]);

            // Prepara dados para a AbacatePay
            $paymentData = [
                'amount' => (int) ($package['price'] * 100), // Converte para centavos
                'description' => "AutoRecurso - {$package['amount']} créditos",
                'customer' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $phone,
                    'document' => $cpf
                ],
                'metadata' => [
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id,
                    'package_id' => $request->package_id,
                    'credits' => $package['amount']
                ],
                'return_url' => route('credits.success', ['transaction_id' => $transaction->id]),
                'completion_url' => route('credits.success', ['transaction_id' => $transaction->id])
            ];

            // Log detalhado dos dados do cliente
            Log::info('Dados do cliente para AbacatePay:', [
                'name' => $paymentData['customer']['name'],
                'email' => $paymentData['customer']['email'],
                'phone' => $paymentData['customer']['phone'],
                'document' => $paymentData['customer']['document'],
                'document_length' => strlen($paymentData['customer']['document']),
                'phone_length' => strlen($paymentData['customer']['phone'])
            ]);

            // Log dos dados enviados para AbacatePay
            Log::info('Dados enviados para AbacatePay:', [
                'payment_data' => array_merge($paymentData, [
                    'customer' => array_merge($paymentData['customer'], [
                        'document_raw' => $paymentData['customer']['document'],
                        'document_length' => strlen($paymentData['customer']['document']),
                        'phone_length' => strlen($paymentData['customer']['phone'])
                    ])
                ])
            ]);
            
            // Log detalhado do payload final
            Log::info('Payload detalhado para AbacatePay:', [
                'customer_name' => $paymentData['customer']['name'],
                'customer_email' => $paymentData['customer']['email'],
                'customer_phone' => $paymentData['customer']['phone'],
                'customer_document' => $paymentData['customer']['document'],
                'amount' => $paymentData['amount'],
                'description' => $paymentData['description'],
                'all_fields_valid' => [
                    'name_not_empty' => !empty($paymentData['customer']['name']),
                    'email_not_empty' => !empty($paymentData['customer']['email']),
                    'phone_not_empty' => !empty($paymentData['customer']['phone']),
                    'document_not_empty' => !empty($paymentData['customer']['document']),
                    'document_is_11_digits' => strlen($paymentData['customer']['document']) === 11,
                    'phone_is_valid_length' => strlen($paymentData['customer']['phone']) >= 10,
                    'amount_positive' => $paymentData['amount'] > 0
                ]
            ]);

            // Cria cobrança na AbacatePay
            $result = $this->abacatePayService->createPixPayment($paymentData);

            // Log da resposta do AbacatePay
            Log::info('Resposta do AbacatePay:', [
                'result' => $result
            ]);

            if (!$result['success']) {
                // Remove transação em caso de erro
                $transaction->delete();
                
                return back()->with('error', 'Erro ao gerar PIX: ' . $result['error']);
            }

            return view('credits.pix-payment', [
                'transaction' => $transaction,
                'payment_url' => $result['data']['data']['url'] ?? '#',
                'amount' => $package['price'],
                'credits' => $package['amount'],
                'billing_id' => $result['data']['data']['id'] ?? null,
                'qr_code' => $result['data']['data']['pix']['qrcode'] ?? '',
                'pix_code' => $result['data']['data']['pix']['copiaecola'] ?? ''
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao criar pagamento PIX', [
                'user_id' => $user->id,
                'package_id' => $request->package_id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Erro interno ao processar pagamento PIX.');
        }
    }

    /**
     * Consulta status de um pagamento PIX
     */
    public function checkPaymentStatus(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|integer'
        ]);

        $transaction = CreditTransaction::where('id', $request->transaction_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'error' => 'Transação não encontrada'
            ]);
        }

        // Se já foi paga, retorna sucesso
        if ($transaction->status === 'completed') {
            return response()->json([
                'success' => true,
                'status' => 'paid',
                'message' => 'Pagamento confirmado!'
            ]);
        }

        // Consulta status na AbacatePay
        if ($transaction->reference) {
            $result = $this->abacatePayService->getBillingStatus($transaction->reference);
            
            if ($result['success']) {
                $status = $result['status'];
                
                // Se foi pago na AbacatePay mas não processado ainda
                if ($status === 'PAID' && $transaction->status !== 'completed') {
                    try {
                        // Confirma a transação usando o CreditService
                        $this->creditService->confirmTransaction($transaction);

                        return response()->json([
                            'success' => true,
                            'status' => 'paid',
                            'message' => 'Pagamento confirmado! Créditos adicionados à sua conta.'
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Erro ao confirmar transação', [
                            'transaction_id' => $transaction->id,
                            'error' => $e->getMessage()
                        ]);

                        return response()->json([
                            'success' => false,
                            'error' => 'Erro ao processar pagamento'
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'status' => strtolower($status),
                    'message' => $this->getStatusMessage($status)
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'status' => $transaction->status,
            'message' => 'Aguardando confirmação do pagamento...'
        ]);
    }

    /**
     * Página de sucesso após pagamento
     */
    public function success(Request $request)
    {
        $transactionId = $request->get('transaction_id');
        
        if ($transactionId) {
            $transaction = CreditTransaction::where('id', $transactionId)
                ->where('user_id', Auth::id())
                ->first();
                
            if ($transaction && $transaction->status === 'completed') {
                return redirect()->route('credits.packages')
                    ->with('success', 'Pagamento PIX realizado com sucesso! Seus créditos foram adicionados à sua conta.');
            }
        }
        
        return redirect()->route('credits.packages')
            ->with('info', 'Aguarde a confirmação do seu pagamento PIX...');
    }

    /**
     * Página de cancelamento
     */
    public function cancel(Request $request)
    {
        return redirect()->route('credits.packages')
            ->with('error', 'Pagamento PIX cancelado.');
    }

    /**
     * Webhook da AbacatePay
     */
    public function webhook(Request $request)
    {
        // Log inicial do webhook
        Log::info('Webhook AbacatePay recebido', [
            'headers' => $request->headers->all(),
            'query' => $request->query(),
            'payload' => $request->all()
        ]);

        // Valida o webhook secret via query parameter
        $webhookSecret = $request->query('webhookSecret');
        $expectedSecret = config('abacatepay.webhook_secret');

        if (!$webhookSecret || $webhookSecret !== $expectedSecret) {
            Log::warning('Webhook AbacatePay com secret inválido', [
                'provided_secret' => $webhookSecret,
                'expected_secret' => $expectedSecret,
                'ip' => $request->ip()
            ]);
            
            return response('Unauthorized', 401);
        }

        try {
            $payload = $request->all();
            
            Log::info('Webhook AbacatePay validado com sucesso', [
                'event' => $payload['event'] ?? 'unknown',
                'billing_id' => $payload['data']['id'] ?? 'N/A',
                'status' => $payload['data']['status'] ?? 'N/A',
                'amount' => $payload['data']['amount'] ?? 0
            ]);

            // Processa o webhook
            $result = $this->abacatePayService->processWebhook($payload);

            Log::info('Resultado do processamento do webhook', [
                'success' => $result['success'],
                'message' => $result['message'] ?? null,
                'error' => $result['error'] ?? null
            ]);

            if ($result['success']) {
                return response('OK', 200);
            } else {
                Log::error('Erro ao processar webhook', ['result' => $result]);
                return response('Error processing webhook', 500);
            }

        } catch (\Exception $e) {
            Log::error('Exceção no webhook AbacatePay', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response('Internal server error', 500);
        }
    }

    /**
     * Converte status da AbacatePay para mensagem amigável
     */
    private function getStatusMessage(string $status): string
    {
        switch (strtoupper($status)) {
            case 'PENDING':
                return 'Aguardando pagamento...';
            case 'PAID':
                return 'Pagamento confirmado!';
            case 'EXPIRED':
                return 'PIX expirado. Gere um novo PIX.';
            case 'CANCELLED':
                return 'Pagamento cancelado.';
            default:
                return 'Status: ' . $status;
        }
    }

    /**
     * Mostra formulário para inserir dados do pagamento
     */
    public function showPaymentForm(Request $request)
    {
        $request->validate([
            'package_id' => 'required|string',
        ]);

        $package = config('credits.packages')[$request->package_id] ?? null;
        if (!$package) {
            return back()->with('error', 'Pacote inválido.');
        }

        $user = Auth::user();

        return view('credits.payment-form', [
            'package' => $package,
            'package_id' => $request->package_id,
            'user' => $user
        ]);
    }
} 