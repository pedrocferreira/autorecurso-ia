<?php

namespace App\Http\Controllers;

use App\Models\CreditTransaction;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $packages = [
            [
                'id' => '5_credits',
                'amount' => 5,
                'price' => 19.90,
                'discount' => 0,
                'recommended' => false,
            ],
            [
                'id' => '10_credits',
                'amount' => 10,
                'price' => 34.90,
                'discount' => 12,
                'recommended' => true,
            ],
            [
                'id' => '20_credits',
                'amount' => 20,
                'price' => 59.90,
                'discount' => 25,
                'recommended' => false,
            ],
            [
                'id' => '50_credits',
                'amount' => 50,
                'price' => 129.90,
                'discount' => 35,
                'recommended' => false,
            ],
        ];

        return view('credits.packages', compact('packages'));
    }

    /**
     * Processa a compra de créditos.
     */
    public function purchase(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|string',
            'payment_method' => 'required|in:credit_card,pix,boleto',
        ]);

        $packages = [
            '5_credits' => ['amount' => 5, 'price' => 19.90],
            '10_credits' => ['amount' => 10, 'price' => 34.90],
            '20_credits' => ['amount' => 20, 'price' => 59.90],
            '50_credits' => ['amount' => 50, 'price' => 129.90],
        ];

        if (!isset($packages[$validated['package_id']])) {
            return back()->withErrors(['package_id' => 'Pacote inválido.']);
        }

        $package = $packages[$validated['package_id']];
        $user = Auth::user();

        // Em um ambiente real, aqui seria feita a integração com o gateway de pagamento
        // Por enquanto, apenas simulamos a compra
        try {
            $transaction = $this->creditService->addCredits(
                $user,
                $package['amount'],
                "Compra de pacote de {$package['amount']} créditos",
                [
                    'payment_method' => $validated['payment_method'],
                    'price' => $package['price'],
                    'package_id' => $validated['package_id'],
                ]
            );

            return redirect()->route('credits.index')
                ->with('success', "Você adquiriu {$package['amount']} créditos com sucesso!");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao processar o pagamento: ' . $e->getMessage()]);
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
}
