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
                            O consumo de créditos varia de acordo com a <strong>gravidade da infração</strong>:
                        </p>
                        
                        <div class="mt-4 mb-5 overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Gravidade da Infração
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pontos na CNH
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Consumo de Créditos
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Exemplos de Infrações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-medium">Leve</span>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            3 pontos
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                1 crédito
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            Estacionar afastado da calçada, usar buzina prolongadamente
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-medium">Média</span>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            4 pontos
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                3 créditos
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            Parar sobre faixa de pedestres, estacionar em local proibido
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-medium">Grave</span>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            5 pontos
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                                5 créditos
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            Velocidade acima de 20%, transitar na contramão
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-medium">Gravíssima</span>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            7 pontos
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                8 créditos
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            Dirigir sob influência de álcool, avançar sinal vermelho
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Seu saldo atual: {{ auth()->user()->credits }} créditos</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($packages as $package)
                            <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="p-5">
                                    <div class="text-center mb-4">
                                        <h3 class="text-2xl font-bold text-gray-800">{{ $package->credits }}</h3>
                                        <p class="text-gray-600">créditos</p>
                                    </div>
                                    <div class="text-center mb-5">
                                        <p class="text-3xl font-bold text-gray-900">
                                            R$ {{ number_format($package->price, 2, ',', '.') }}
                                        </p>
                                        <p class="text-sm text-green-600 font-medium">
                                            🔥 {{ $package->discount }}% OFF
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            R$ {{ number_format($package->price_per_credit, 2, ',', '.') }} por crédito
                                        </p>
                                    </div>
                                    
                                    <!-- Número de recursos possíveis -->
                                    <div class="mb-4 bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs font-medium text-gray-600 mb-2 text-center">Recursos possíveis:</p>
                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                            @foreach ($package->resources as $resource)
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-500">{{ $resource['description'] }}</span>
                                                    <span class="font-bold text-{{ $resource['color'] }}-600">{{ $resource['count'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <form action="{{ route('credits.purchase') }}" method="POST" id="purchase-form-{{ $package->id }}">
                                        @csrf
                                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                                        <input type="hidden" name="amount" value="{{ $package->credits }}">
                                        <input type="hidden" name="price" value="{{ $package->price }}">
                                        <input type="hidden" name="payment_method" value="credit_card">

                                        <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                                            Comprar agora
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stripe = Stripe('{{ config('services.stripe.key') }}');
        const forms = document.querySelectorAll('.stripe-form');
        
        forms.forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                try {
                    const formData = new FormData(this);
                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            package_id: formData.get('package_id'),
                            amount: formData.get('amount'),
                            price: formData.get('price'),
                            payment_method: formData.get('payment_method'),
                            _ajax: 1
                        })
                    });

                    const session = await response.json();
                    
                    if (session.error) {
                        throw new Error(session.error);
                    }

                    // Redireciona para a URL da sessão do Stripe
                    if (session.url) {
                        console.log('Redirecionando para:', session.url);
                        window.location.href = session.url;
                    } else {
                        throw new Error('URL da sessão não encontrada');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Ocorreu um erro ao processar o pagamento. Por favor, tente novamente.');
                }
            });
        });
    });
</script>
@endpush
