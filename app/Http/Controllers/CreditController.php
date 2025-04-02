<?php

namespace App\Http\Controllers;

use App\Models\CreditPackage;
use App\Models\CreditTransaction;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use App\Models\Transaction;

class CreditController extends Controller
{
    protected $creditService;

    /**
     * Construtor que inicializa o serviço de créditos e aplica middleware de autenticação.
     */
    public function __construct(CreditService $creditService)
    {
        $this->middleware('auth');
        $this->creditService = $creditService;
        
        // Configura o Stripe com a chave apropriada baseada no ambiente
        $stripeKey = app()->environment('production') 
            ? config('services.stripe.secret')
            : config('services.stripe.secret');
            
        Stripe::setApiKey($stripeKey);
    }

    /**
     * Exibe o histórico de transações de créditos do usuário.
     */
    public function index()
    {
        $user = Auth::user();
        $transactions = CreditTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('credits.index', compact('transactions'));
    }

    /**
     * Exibe os pacotes de créditos disponíveis para compra.
     */
    public function packages()
    {
        $packages = CreditPackage::where('active', true)->get();
        return view('credits.packages', compact('packages'));
    }

    /**
     * Processa a compra de créditos.
     */
    public function purchase(Request $request)
    {
        try {
            $package = CreditPackage::findOrFail($request->package_id);
            
            // Cria uma transação pendente
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'amount' => $package->price,
                'credits' => $package->credits,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_id' => null
            ]);

            // Cria a sessão do Stripe
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'brl',
                        'product_data' => [
                            'name' => "{$package->credits} Créditos",
                            'description' => "Pacote de {$package->credits} créditos"
                        ],
                        'unit_amount' => (int)($package->price * 100), // Stripe trabalha com centavos
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('credits.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('credits.packages'),
                'metadata' => [
                    'user_id' => auth()->id(),
                    'transaction_id' => $transaction->id,
                    'package_id' => $package->id,
                    'credits' => $package->credits
                ]
            ]);

            // Atualiza a transação com o ID da sessão
            $transaction->update([
                'payment_id' => $session->id
            ]);

            // Redireciona diretamente para a URL do Stripe
            return redirect($session->url);

        } catch (\Exception $e) {
            Log::error('Erro ao criar sessão do Stripe: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao processar o pagamento. Por favor, tente novamente.');
        }
    }

    public function success(Request $request)
    {
        try {
            $session = Session::retrieve($request->session_id);
            
            if ($session->payment_status === 'paid') {
                $transaction = Transaction::where('payment_id', $session->id)->first();
                
                if ($transaction && $transaction->status === 'pending') {
                    $transaction->update(['status' => 'completed']);
                    
                    $user = $transaction->user;
                    $user->increment('credits', $transaction->credits);
                }
            }

            return view('credits.success');
        } catch (\Exception $e) {
            Log::error('Erro ao processar sucesso do pagamento: ' . $e->getMessage());
            return redirect()->route('credits.packages')->with('error', 'Erro ao processar o pagamento.');
        }
    }

    /**
     * Adiciona créditos gratuitos para testes (apenas em ambiente de desenvolvimento).
     */
    public function addFreeCredits()
    {
        if (!app()->environment(['local', 'development'])) {
            abort(404);
        }

        $user = Auth::user();
        $amount = 5;

        try {
            $transaction = $this->creditService->addCredits(
                $user,
                $amount,
                "Créditos gratuitos para testes",
                [
                    'payment_method' => 'free',
                    'price' => 0,
                ]
            );

            return redirect()->route('dashboard')
                ->with('success', "Você recebeu {$amount} créditos gratuitos para testes!");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao adicionar créditos: ' . $e->getMessage()]);
        }
    }

    public function history()
    {
        $transactions = auth()->user()->creditTransactions()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('credits.history', compact('transactions'));
    }
}
