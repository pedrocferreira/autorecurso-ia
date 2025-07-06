<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AbacatePayService;
use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\AppealDraft;

class ChatPaymentController extends Controller
{
    public function createPix(Request $request)
    {
        try {
            // Validação básica dos dados
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'cpf' => 'required|string|min:11',
                'phone' => 'required|string|min:10',
                'amount' => 'required|numeric|min:1'
            ]);

            // Busca usuário autenticado ou cria temporário (ajuste conforme seu fluxo de auth)
            $user = Auth::user();
            if (!$user) {
                // Se não estiver autenticado, busca por CPF ou email
                $cpf = preg_replace('/[^0-9]/', '', $request->cpf);
                $user = User::where('cpf', $cpf)
                    ->orWhere('email', $request->email)
                    ->first();

                if (!$user) {
                    // Se não encontrou, cria um usuário temporário
                    $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'cpf' => $cpf,
                        'phone' => $request->phone,
                        'password' => bcrypt(uniqid())
                    ]);
                }
            }

            // Cria / atualiza draft do recurso com todos os dados enviados pelo chat
            $draft = AppealDraft::create([
                'user_id'    => $user->id,
                'form_data'  => $request->all(),
                'status'     => 'pending',
            ]);

            // Monta os dados para o serviço AbacatePay
            $paymentData = [
                'amount' => (int) ($request->amount * 100), // centavos
                'description' => 'AutoRecurso - Recurso de multa',
                'customer' => [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'document' => preg_replace('/[^0-9]/', '', $request->cpf)
                ],
                'metadata' => [
                    'user_id' => $user->id,
                    'credits' => 1,
                    'gateway' => 'abacatepay',
                    'chat_payment' => true,
                    'draft_id' => $draft->id,
                ],
                'return_url' => url('/cliente/sucesso'),
                'completion_url' => url('/cliente/sucesso')
            ];

            $abacatePay = new AbacatePayService();
            $result = $abacatePay->createPixPayment($paymentData);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar PIX: ' . $result['error']
                ], 500);
            }

            $billing = $result['data']['data'] ?? null;
            if (!$billing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar pagamento PIX. Tente novamente.'
                ], 500);
            }

            // Aceita a resposta se tiver QR Code OU URL de pagamento
            if (empty($billing['pix']['qrcode']) && empty($billing['url'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar link de pagamento PIX. Tente novamente.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $billing
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar PIX no chat', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao processar pagamento PIX.'
            ], 500);
        }
    }

    public function checkStatus($id)
    {
        // Procura transação relacionada ao billing/reference
        $tx = CreditTransaction::where('reference', $id)->first();
        if (!$tx) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transação não encontrada'
            ]);
        }

        if ($tx->status === 'completed') {
            return response()->json(['status' => 'paid']);
        }

        // Caso ainda pendente, consulta AbacatePay
        $abacate = new \App\Services\AbacatePayService();
        $resp = $abacate->getBillingStatus($id);
        if ($resp['success']) {
            return response()->json([
                'status' => strtolower($resp['status']),
                'message' => $resp['status']
            ]);
        }

        return response()->json([
            'status' => 'pending',
            'message' => 'Aguardando pagamento'
        ]);
    }
} 