<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Comprar Créditos') }}
            </h2>
            <div class="flex items-center">
                <span class="mr-4 px-4 py-2 bg-blue-100 text-blue-800 rounded-full">
                    <strong>Seus créditos:</strong> {{ Auth::user()->credits }}
                </span>
                <a href="{{ route('credits.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded">
                    Ver histórico
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

            @if (session('warning'))
                <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                    {{ session('warning') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Sobre os Créditos</h3>
                    <div class="text-gray-600">
                        <p class="mb-3">
                            Os créditos são utilizados para gerar recursos personalizados para suas multas de trânsito.
                            Cada geração de recurso consome 1 crédito.
                        </p>
                        <p class="mb-3">
                            Você pode adquirir pacotes de créditos com descontos progressivos.
                            Quanto maior o pacote, maior o desconto.
                        </p>
                        <p>
                            Seus créditos não expiram e você pode utilizá-los a qualquer momento.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($packages as $package)
                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 {{ $package['recommended'] ? 'ring-2 ring-blue-500' : '' }}">
                        <div class="p-5">
                            @if ($package['recommended'])
                                <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs font-bold px-3 py-1 transform translate-x-2 -translate-y-0 rotate-45 origin-bottom-left">
                                    RECOMENDADO
                                </div>
                            @endif
                            <div class="text-center mb-4">
                                <h3 class="text-2xl font-bold text-gray-800">{{ $package['amount'] }}</h3>
                                <p class="text-gray-600">créditos</p>
                            </div>
                            <div class="text-center mb-5">
                                <p class="text-3xl font-bold text-gray-900">
                                    R$ {{ number_format($package['price'], 2, ',', '.') }}
                                </p>
                                @if ($package['discount'] > 0)
                                    <p class="text-sm text-green-600 font-medium">
                                        {{ $package['discount'] }}% de desconto
                                    </p>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">
                                    R$ {{ number_format($package['price'] / $package['amount'], 2, ',', '.') }} por crédito
                                </p>
                            </div>
                            <form action="{{ route('credits.purchase') }}" method="POST">
                                @csrf
                                <input type="hidden" name="package_id" value="{{ $package['id'] }}">

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Forma de pagamento
                                    </label>
                                    <select name="payment_method" class="w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="credit_card">Cartão de Crédito</option>
                                        <option value="pix">Pix</option>
                                        <option value="boleto">Boleto Bancário</option>
                                    </select>
                                </div>

                                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition-colors">
                                    Comprar agora
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
