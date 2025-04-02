<?php

namespace App\Http\Controllers;

use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class WebhookController extends Controller
{
    public function handleStripeWebhook(Request $request)
    {
        Log::info('Webhook do Stripe recebido', [
            'event_type' => $request->header('Stripe-Event-Type'),
            'environment' => app()->environment()
        ]);

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook.secret')
            );

            Log::info('Evento do Stripe verificado', [
                'event_type' => $event->type,
                'event_id' => $event->id,
                'environment' => app()->environment()
            ]);
        } catch (SignatureVerificationException $e) {
            Log::error('Erro na verificação da assinatura do webhook do Stripe', [
                'error' => $e->getMessage(),
                'environment' => app()->environment()
            ]);
            return response()->json(['error' => 'Assinatura inválida'], 400);
        }

        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    
                    Log::info('Processando checkout.session.completed', [
                        'session_id' => $session->id,
                        'payment_status' => $session->payment_status,
                        'environment' => app()->environment()
                    ]);

                    // Verifica se é uma sessão de compra de créditos
                    if (isset($session->metadata->user_id) && isset($session->metadata->package_id)) {
                        $user = User::find($session->metadata->user_id);
                        
                        if ($user) {
                            Log::info('Usuário encontrado para processamento', [
                                'user_id' => $user->id,
                                'session_id' => $session->id,
                                'environment' => app()->environment()
                            ]);

                            // Atualiza o status da transação
                            $transaction = CreditTransaction::where('metadata->payment_id', $session->id)->first();
                            
                            if ($transaction) {
                                Log::info('Transação encontrada para atualização', [
                                    'transaction_id' => $transaction->id,
                                    'user_id' => $user->id,
                                    'environment' => app()->environment()
                                ]);

                                $metadata = $transaction->metadata;
                                $metadata['status'] = 'completed';
                                $metadata['webhook_processed'] = true;
                                $metadata['webhook_processed_at'] = now()->toIso8601String();
                                $transaction->update(['metadata' => $metadata]);
                                
                                // Adiciona os créditos ao usuário
                                $user->increment('credits', $transaction->amount);
                                
                                Log::info('Pagamento confirmado e créditos adicionados', [
                                    'user_id' => $user->id,
                                    'session_id' => $session->id,
                                    'amount' => $transaction->amount,
                                    'new_balance' => $user->credits,
                                    'environment' => app()->environment()
                                ]);
                            } else {
                                Log::warning('Transação não encontrada para o session_id', [
                                    'session_id' => $session->id,
                                    'user_id' => $user->id,
                                    'environment' => app()->environment()
                                ]);
                            }
                        } else {
                            Log::warning('Usuário não encontrado para o session_id', [
                                'session_id' => $session->id,
                                'user_id' => $session->metadata->user_id,
                                'environment' => app()->environment()
                            ]);
                        }
                    } else {
                        Log::warning('Metadata incompleta na sessão', [
                            'session_id' => $session->id,
                            'metadata' => $session->metadata,
                            'environment' => app()->environment()
                        ]);
                    }
                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    
                    Log::info('Processando payment_intent.payment_failed', [
                        'payment_intent_id' => $paymentIntent->id,
                        'environment' => app()->environment()
                    ]);

                    // Atualiza o status da transação para falha
                    $transaction = CreditTransaction::where('metadata->payment_id', $paymentIntent->id)->first();
                    
                    if ($transaction) {
                        $metadata = $transaction->metadata;
                        $metadata['status'] = 'failed';
                        $metadata['webhook_processed'] = true;
                        $metadata['webhook_processed_at'] = now()->toIso8601String();
                        $transaction->update(['metadata' => $metadata]);
                        
                        Log::info('Status da transação atualizado para falha', [
                            'transaction_id' => $transaction->id,
                            'payment_intent_id' => $paymentIntent->id,
                            'environment' => app()->environment()
                        ]);
                    } else {
                        Log::warning('Transação não encontrada para payment_intent_id', [
                            'payment_intent_id' => $paymentIntent->id,
                            'environment' => app()->environment()
                        ]);
                    }
                    break;

                default:
                    Log::info('Evento do Stripe não processado', [
                        'event_type' => $event->type,
                        'event_id' => $event->id,
                        'environment' => app()->environment()
                    ]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook do Stripe', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event_type' => $event->type,
                'event_id' => $event->id,
                'environment' => app()->environment()
            ]);
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }
} 