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
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Quantidade</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Saldo Após</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Descrição</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="p-3 text-sm">#{{ $transaction->id }}</td>
                                            <td class="p-3 text-sm">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="p-3 text-sm">
                                                @if($transaction->type == 'purchase')
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Compra</span>
                                                @elseif($transaction->type == 'consumption')
                                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Consumo</span>
                                                @elseif($transaction->type == 'admin_adjustment')
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Ajuste</span>
                                                @elseif($transaction->type == 'refund')
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Reembolso</span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-sm">
                                                @if($transaction->amount > 0)
                                                    <span class="text-green-600">+{{ $transaction->amount }}</span>
                                                @else
                                                    <span class="text-red-600">{{ $transaction->amount }}</span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-sm">{{ $transaction->balance_after }}</td>
                                            <td class="p-3 text-sm">{{ $transaction->description }}</td>
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
