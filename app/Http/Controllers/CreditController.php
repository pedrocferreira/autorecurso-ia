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
        // Carrega os pacotes a partir do arquivo de configuração
        // e converte para um array numérico para a view.
        $packages = collect(config('credits.packages'))
            ->map(function ($pkg, $id) {
                return array_merge($pkg, ['id' => $id]);
            })
            ->values()
            ->all();

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

        $packages = config('credits.packages');

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
}
