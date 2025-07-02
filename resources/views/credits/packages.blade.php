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

            @if (request('payment') === 'success')
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <i class="fas fa-check-circle mr-2"></i>
                    Pagamento realizado com sucesso! Seus créditos foram adicionados à sua conta.
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Sobre os Créditos</h3>
                    <div class="text-gray-600">
                        <p class="mb-3">
                            Os créditos são utilizados para gerar recursos personalizados para suas multas de trânsito.
                            O sistema de <strong>Inteligência Híbrida</strong> combina 3 IAs especializadas e consome <span class="font-bold text-blue-600">3 créditos</span> por recurso gerado (qualidade máxima).
                        </p>
                        <p class="mb-3">
                            Você pode adquirir pacotes de créditos com <span class="font-bold text-green-600">descontos progressivos</span>.
                            Quanto maior o pacote, maior o desconto.
                        </p>
                        <p>
                            Seus créditos não expiram e você pode utilizá-los a qualquer momento.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Métodos de Pagamento -->
            <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">
                    <i class="fas fa-credit-card mr-2 text-blue-600"></i>
                    Métodos de Pagamento Disponíveis
                </h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg p-4 text-center border-2 border-green-200">
                        <i class="fas fa-qrcode text-3xl text-green-600 mb-2"></i>
                        <h4 class="font-semibold text-green-800">PIX</h4>
                        <p class="text-sm text-gray-600">Pagamento instantâneo</p>
                        <p class="text-xs text-green-600 font-medium">Processamento imediato</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 text-center border-2 border-blue-200">
                        <i class="fas fa-credit-card text-3xl text-blue-600 mb-2"></i>
                        <h4 class="font-semibold text-blue-800">Cartão de Crédito</h4>
                        <p class="text-sm text-gray-600">Visa, Mastercard, Elo</p>
                        <p class="text-xs text-blue-600 font-medium">Parcelamento disponível</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($packages as $package)
                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 {{ $package['recommended'] ? 'ring-2 ring-blue-500 transform scale-105' : '' }} relative">
                        @if ($package['recommended'])
                            <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg">
                                <i class="fas fa-star mr-1"></i>
                                RECOMENDADO
                            </div>
                        @endif
                        
                        <div class="p-6">
                            <!-- Cabeçalho do Pacote -->
                            <div class="text-center mb-6">
                                <div class="bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                                    <span class="text-2xl font-bold">{{ $package['amount'] }}</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $package['amount'] }} Créditos</h3>
                                @if ($package['discount'] > 0)
                                    <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full">
                                        {{ $package['discount'] }}% OFF
                                    </span>
                                @endif
                            </div>

                            <!-- Preço -->
                            <div class="text-center mb-6">
                                <p class="text-3xl font-bold text-gray-900">
                                    R$ {{ number_format($package['price'], 2, ',', '.') }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    R$ {{ number_format($package['price'] / $package['amount'], 2, ',', '.') }} por crédito
                                </p>
                            </div>

                            <!-- Botões de Pagamento -->
                            <div class="space-y-3">
                                <!-- PIX -->
                                <form action="{{ route('credits.payment.form') }}" method="GET" class="w-full">
                                    <input type="hidden" name="package_id" value="{{ $package['id'] }}">
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                                        <i class="fas fa-qrcode mr-2"></i>
                                        Pagar com PIX
                                    </button>
                                </form>

                                <!-- Cartão de Crédito -->
                                <form action="{{ route('credits.checkout') }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $package['id'] }}">
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                                        <i class="fas fa-credit-card mr-2"></i>
                                        Cartão de Crédito
                                    </button>
                                </form>
                            </div>

                            <!-- Informações Extras -->
                            <div class="mt-4 text-center">
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-shield-alt mr-1 text-green-500"></i>
                                    Pagamento 100% seguro
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Informações Adicionais -->
            <div class="mt-8 bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Informações Importantes
                </h3>
                <div class="grid md:grid-cols-2 gap-6 text-gray-600">
                    <div>
                        <h4 class="font-semibold mb-2 text-gray-800">Sobre os Pagamentos</h4>
                        <ul class="space-y-1 text-sm">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                PIX: Processamento em tempo real
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                Cartão: Processamento via Stripe
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                Créditos adicionados automaticamente
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-2 text-gray-800">Política de Créditos</h4>
                        <ul class="space-y-1 text-sm">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                Créditos não expiram
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                Uso ilimitado no tempo
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                Suporte especializado
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
