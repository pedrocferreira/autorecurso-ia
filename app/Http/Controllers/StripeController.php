<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\CreditPackage; // assume custom model or we build inline
use App\Models\CreditTransaction;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'package_id' => 'required|string',
        ]);

        $package = config('credits.packages')[$request->package_id] ?? null;
        if (!$package) {
            return back()->with('error', 'Pacote inválido.');
        }

        Stripe::setApiKey(config('stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'brl',
                    'unit_amount' => (int) ($package['price'] * 100),
                    'product_data' => [
                        'name' => $package['amount'] . ' créditos AutoRecurso',
                    ],
                ],
                'quantity' => 1,
            ]],
            'customer_email' => Auth::user()->email,
            'metadata' => [
                'user_id' => Auth::id(),
                'package_id' => $request->package_id,
                'credits' => $package['amount'],
            ],
            'success_url' => route('credits.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('credits.cancel'),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        
        if ($sessionId) {
            Stripe::setApiKey(config('stripe.secret'));
            
            try {
                $session = Session::retrieve($sessionId);
                
                if ($session->payment_status === 'paid') {
                    return redirect()->route('credits.packages')->with('success', 'Pagamento realizado com sucesso! Seus créditos foram adicionados à sua conta.');
                }
            } catch (\Exception $e) {
                Log::error('Erro ao verificar sessão do Stripe: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('credits.packages')->with('info', 'Processando seu pagamento...');
    }

    public function cancel(Request $request)
    {
        return redirect()->route('credits.packages')->with('error', 'Pagamento cancelado.');
    }

    public function webhook(Request $request)
    {
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\Exception $e) {
            return response('Invalid payload', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $userId = $session->metadata->user_id ?? null;
            $credits = $session->metadata->credits ?? 0;

            if ($userId && $credits) {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $user->credits += $credits;
                    $user->save();

                    CreditTransaction::create([
                        'user_id' => $user->id,
                        'credits' => $credits,
                        'type' => 'purchase',
                        'reference' => $session->id,
                    ]);
                }
            }
        }

        return response('Webhook handled', 200);
    }
} 