<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dados para Pagamento PIX') }}
            </h2>
            <div class="flex items-center">
                <span class="mr-4 px-4 py-2 bg-blue-100 text-blue-800 rounded-full">
                    <strong>Seus créditos:</strong> {{ Auth::user()->credits }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagens de erro -->
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Resumo do Pacote -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-shopping-cart mr-2 text-blue-600"></i>
                        Resumo da Compra
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Pacote:</span>
                            <span class="font-semibold">{{ $package['amount'] }} Créditos</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Método:</span>
                            <span class="font-semibold text-green-600">
                                <i class="fas fa-qrcode mr-1"></i>
                                PIX
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-lg font-bold text-blue-600 border-t pt-2">
                            <span>Total:</span>
                            <span>R$ {{ number_format($package['price'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulário de Dados -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user mr-2 text-blue-600"></i>
                        Dados para Pagamento
                    </h3>
                    
                    <form action="{{ route('credits.pix.payment') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package_id }}">
                        
                        <!-- Nome (apenas exibição) -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nome Completo
                            </label>
                            <input type="text" id="name" name="name" value="{{ $user->name }}" readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500 cursor-not-allowed">
                            <p class="text-xs text-gray-500 mt-1">Este é o nome cadastrado em sua conta</p>
                        </div>

                        <!-- Email (apenas exibição) -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                E-mail
                            </label>
                            <input type="email" id="email" name="email" value="{{ $user->email }}" readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500 cursor-not-allowed">
                        </div>

                        <!-- CPF -->
                        <div>
                            <label for="cpf" class="block text-sm font-medium text-gray-700 mb-2">
                                CPF <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $user->cpf ?? '') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="000.000.000-00" maxlength="14">
                            <p class="text-xs text-gray-500 mt-1">Informe um CPF válido para o pagamento PIX</p>
                        </div>

                        <!-- Telefone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Telefone/Celular
                            </label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="(00) 00000-0000" maxlength="15">
                            <p class="text-xs text-gray-500 mt-1">Opcional - será usado como contato de backup</p>
                        </div>

                        <!-- Botões -->
                        <div class="flex gap-4 pt-4">
                            <a href="{{ route('credits.packages') }}" 
                                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-3 px-4 rounded-lg text-center transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Voltar
                            </a>
                            <button type="submit" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                                <i class="fas fa-qrcode mr-2"></i>
                                Gerar PIX
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informações sobre Segurança -->
            <div class="mt-6 bg-blue-50 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-shield-alt text-blue-600 text-lg mr-3 mt-1"></i>
                    <div>
                        <h4 class="font-semibold text-blue-800 mb-1">Pagamento 100% Seguro</h4>
                        <p class="text-sm text-blue-700">
                            Seus dados são protegidos e o pagamento é processado através da AbacatePay com total segurança.
                            O PIX será gerado imediatamente após a validação dos dados.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Formatação automática do CPF
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})/, '$1-$2');
            e.target.value = value;
        });

        // Formatação automática do telefone
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4,5})(\d{4})/, '$1-$2');
            e.target.value = value;
        });
    </script>
</x-app-layout> 