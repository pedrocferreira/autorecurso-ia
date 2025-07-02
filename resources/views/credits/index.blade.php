<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Histórico de Créditos') }}
            </h2>
            <div class="flex items-center">
                <span class="mr-4 px-4 py-2 bg-blue-100 text-blue-800 rounded-full">
                    <strong>Seus créditos:</strong> {{ Auth::user()->credits }}
                </span>
                <a href="{{ route('credits.packages') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    Comprar créditos
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagem de sucesso ou erro -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(count($transactions) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b">
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">ID</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Data</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Tipo</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Status</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Pagamento</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Quantidade</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Saldo Após</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Descrição</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="p-3 text-sm font-mono">#{{ $transaction->id }}</td>
                                            <td class="p-3 text-sm">
                                                {{ $transaction->created_at->format('d/m/Y') }}
                                                <br>
                                                <span class="text-xs text-gray-500">{{ $transaction->created_at->format('H:i') }}</span>
                                            </td>
                                            <td class="p-3 text-sm">
                                                @if($transaction->type == 'purchase')
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                                        <i class="fas fa-shopping-cart mr-1"></i>Compra
                                                    </span>
                                                @elseif($transaction->type == 'consumption')
                                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                                        <i class="fas fa-minus-circle mr-1"></i>Consumo
                                                    </span>
                                                @elseif($transaction->type == 'admin_adjustment')
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                                        <i class="fas fa-cogs mr-1"></i>Ajuste
                                                    </span>
                                                @elseif($transaction->type == 'refund')
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                                        <i class="fas fa-undo mr-1"></i>Reembolso
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-sm">
                                                @if($transaction->status == 'completed')
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                                        <i class="fas fa-check-circle mr-1"></i>Completado
                                                    </span>
                                                @elseif($transaction->status == 'pending')
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                                        <i class="fas fa-clock mr-1"></i>Pendente
                                                    </span>
                                                @elseif($transaction->status == 'failed')
                                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                                        <i class="fas fa-times-circle mr-1"></i>Falhou
                                                    </span>
                                                @elseif($transaction->status == 'cancelled')
                                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">
                                                        <i class="fas fa-ban mr-1"></i>Cancelado
                                                    </span>
                                                @elseif($transaction->status == 'expired')
                                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs">
                                                        <i class="fas fa-hourglass-end mr-1"></i>Expirado
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-sm">
                                                @if($transaction->payment_method)
                                                    <div class="flex items-center">
                                                        @if($transaction->payment_method == 'pix')
                                                            <i class="fas fa-qrcode text-green-600 mr-1"></i>
                                                            <span class="text-green-700">PIX</span>
                                                        @elseif($transaction->payment_method == 'stripe')
                                                            <i class="fas fa-credit-card text-blue-600 mr-1"></i>
                                                            <span class="text-blue-700">Cartão</span>
                                                        @elseif($transaction->payment_method == 'boleto')
                                                            <i class="fas fa-barcode text-yellow-600 mr-1"></i>
                                                            <span class="text-yellow-700">Boleto</span>
                                                        @elseif($transaction->payment_method == 'admin')
                                                            <i class="fas fa-user-shield text-purple-600 mr-1"></i>
                                                            <span class="text-purple-700">Admin</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-sm">
                                                @if($transaction->amount > 0)
                                                    <span class="text-green-600 font-semibold">+{{ $transaction->amount }}</span>
                                                @else
                                                    <span class="text-red-600 font-semibold">{{ $transaction->amount }}</span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-sm font-medium">{{ $transaction->balance_after }}</td>
                                            <td class="p-3 text-sm">
                                                <div class="max-w-xs">
                                                    {{ $transaction->description }}
                                                    @if($transaction->reference)
                                                        <br>
                                                        <span class="text-xs text-gray-500 font-mono">
                                                            Ref: {{ Str::limit($transaction->reference, 20) }}
                                                        </span>
                                                    @endif
                                                    @if($transaction->paid_at)
                                                        <br>
                                                        <span class="text-xs text-green-600">
                                                            <i class="fas fa-calendar-check mr-1"></i>
                                                            Pago em {{ $transaction->paid_at->format('d/m/Y H:i') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <div class="text-gray-500 text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-lg">Nenhuma transação encontrada.</p>
                            <p class="mt-2">
                                <a href="{{ route('credits.packages') }}" class="text-blue-600 hover:underline">Compre seus primeiros créditos</a>
                                para começar a usar o sistema.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
