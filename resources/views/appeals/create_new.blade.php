<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gerar Novo Recurso') }}
            </h2>
            <div class="flex items-center">
                <span class="mr-4 px-4 py-2 bg-blue-100 text-blue-800 rounded-full">
                    <strong>Créditos disponíveis:</strong> {{ Auth::user()->credits }}
                </span>
                <a href="{{ route('appeals.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded">
                    Voltar
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
                    <form action="{{ route('appeals.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Alertas -->
                        <div class="bg-blue-50 p-4 rounded mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Informações importantes</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>A geração de um recurso consumirá <strong>1 crédito</strong> da sua conta.</p>
                                        <p>Preencha todos os dados corretamente para garantir a eficácia do recurso.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dados Pessoais -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Dados Pessoais</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="cpf" class="block text-sm font-medium text-gray-700 mb-1">CPF</label>
                                    <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('cpf')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="driver_license" class="block text-sm font-medium text-gray-700 mb-1">Número da CNH</label>
                                    <input type="text" id="driver_license" name="driver_license" value="{{ old('driver_license') }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('driver_license')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="driver_license_category" class="block text-sm font-medium text-gray-700 mb-1">Categoria da CNH</label>
                                    <select id="driver_license_category" name="driver_license_category" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Selecione a categoria...</option>
                                        <option value="A" {{ old('driver_license_category') == 'A' ? 'selected' : '' }}>A - Motocicleta</option>
                                        <option value="B" {{ old('driver_license_category') == 'B' ? 'selected' : '' }}>B - Automóvel</option>
                                        <option value="C" {{ old('driver_license_category') == 'C' ? 'selected' : '' }}>C - Caminhão</option>
                                        <option value="D" {{ old('driver_license_category') == 'D' ? 'selected' : '' }}>D - Ônibus</option>
                                        <option value="E" {{ old('driver_license_category') == 'E' ? 'selected' : '' }}>E - Carreta</option>
                                    </select>
                                    @error('driver_license_category')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="(00) 00000-0000" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('phone')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                                    <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Endereço Completo</label>
                                    <input type="text" id="address" name="address" value="{{ old('address') }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('address')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Dados do Veículo -->
                        <div class="border-b pb-6 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Dados do Veículo</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="plate" class="block text-sm font-medium text-gray-700 mb-1">Placa do Veículo</label>
                                    <input type="text" id="plate" name="plate" value="{{ old('plate') }}" placeholder="AAA0000" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('plate')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="vehicle_model" class="block text-sm font-medium text-gray-700 mb-1">Modelo do Veículo</label>
                                    <input type="text" id="vehicle_model" name="vehicle_model" value="{{ old('vehicle_model') }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('vehicle_model')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="vehicle_year" class="block text-sm font-medium text-gray-700 mb-1">Ano do Veículo</label>
                                    <input type="number" id="vehicle_year" name="vehicle_year" value="{{ old('vehicle_year') }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('vehicle_year')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="vehicle_color" class="block text-sm font-medium text-gray-700 mb-1">Cor do Veículo</label>
                                    <input type="text" id="vehicle_color" name="vehicle_color" value="{{ old('vehicle_color') }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('vehicle_color')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="vehicle_chassi" class="block text-sm font-medium text-gray-700 mb-1">Chassi do Veículo</label>
                                    <input type="text" id="vehicle_chassi" name="vehicle_chassi" value="{{ old('vehicle_chassi') }}" maxlength="17" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <p class="text-xs text-gray-500 mt-1">O chassi deve ter até 17 caracteres.</p>
                                    @error('vehicle_chassi')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="vehicle_renavam" class="block text-sm font-medium text-gray-700 mb-1">RENAVAM</label>
                                    <input type="text" id="vehicle_renavam" name="vehicle_renavam" value="{{ old('vehicle_renavam') }}" maxlength="11" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <p class="text-xs text-gray-500 mt-1">O RENAVAM deve ter até 11 caracteres.</p>
                                    @error('vehicle_renavam')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Dados da Multa -->
                        <div class="border-b pb-6 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Dados da Multa</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="citation_number" class="block text-sm font-medium text-gray-700 mb-1">Número da Autuação</label>
                                    <input type="text" id="citation_number" name="citation_number" value="{{ old('citation_number') }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('citation_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Data da Infração</label>
                                    <input type="date" id="date" name="date" value="{{ old('date') }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="time" class="block text-sm font-medium text-gray-700 mb-1">Horário da Infração</label>
                                    <input type="time" id="time" name="time" value="{{ old('time') }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('time')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="infraction_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Infração</label>
                                    <select id="infraction_type" name="infraction_type" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Selecione o tipo de infração...</option>
                                        @foreach($infractionTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('infraction_type') == $type->id ? 'selected' : '' }}>
                                                {{ $type->code }} - {{ $type->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('infraction_type')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Valor da Multa</label>
                                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}" step="0.01" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('amount')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="points" class="block text-sm font-medium text-gray-700 mb-1">Pontos na CNH</label>
                                    <input type="number" id="points" name="points" value="{{ old('points') }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('points')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-3">
                                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Local da Infração</label>
                                    <input type="text" id="location" name="location" value="{{ old('location') }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('location')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-3">
                                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Observações (opcional)</label>
                                    <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('reason') }}</textarea>
                                    <p class="text-sm text-gray-500 mt-1">Descreva o que aconteceu, circunstâncias ou detalhes relevantes sobre a infração.</p>
                                    @error('reason')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informações Adicionais -->
                        <div class="pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Adicionais</h3>
                            <div>
                                <label for="custom_details" class="block text-sm font-medium text-gray-700 mb-1">Detalhes Específicos da Situação</label>
                                <textarea id="custom_details" name="custom_details" rows="4" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('custom_details') }}</textarea>
                                <p class="text-sm text-gray-500 mt-1">Descreva detalhadamente as circunstâncias da infração, incluindo:</p>
                                <ul class="text-sm text-gray-500 mt-1 list-disc list-inside">
                                    <li>Condições climáticas no momento da infração</li>
                                    <li>Estado do trânsito no local</li>
                                    <li>Presença de sinalização</li>
                                    <li>Qualquer outro fator relevante que possa ajudar na defesa</li>
                                </ul>
                                @error('custom_details')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition mr-2">
                                Cancelar
                            </a>
                            <button type="submit" id="submit-btn" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition">
                                <i class="fas fa-file-alt mr-2"></i>
                                Gerar Recurso
                            </button>
                        </div>

                        <!-- Loading Overlay -->
                        <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex-col items-center justify-center hidden">
                            <div class="bg-white p-8 rounded-lg shadow-lg max-w-md mx-auto mt-20 text-center">
                                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-green-500 mx-auto mb-4"></div>
                                <h3 class="text-xl font-bold mb-2">Gerando Recurso</h3>
                                <p class="mb-4">Estamos elaborando um recurso personalizado com base nos dados fornecidos.</p>
                                <div id="loading-status" class="text-sm text-gray-600">
                                    <p class="mb-2" id="status-message">Analisando os detalhes da infração...</p>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                                        <div id="progress-bar" class="bg-green-600 h-2.5 rounded-full" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mapeamento de infrações com seus respectivos pontos e valores
            const infractionData = {
                @foreach(\App\Models\InfractionType::where('active', true)->get() as $type)
                    "{{ $type->id }}": { 
                        points: {{ $type->points }}, 
                        amount: {{ $type->base_amount }}, 
                        description: "{{ $type->description }}",
                        article: "{{ $type->article }}",
                        code: "{{ $type->code }}"
                    },
                @endforeach
            };

            // Elementos do formulário
            const infractionTypeSelect = document.getElementById('infraction_type');
            const pointsInput = document.getElementById('points');
            const amountInput = document.getElementById('amount');

            // Atualiza os campos quando um tipo de infração é selecionado
            if (infractionTypeSelect) {
                infractionTypeSelect.addEventListener('change', function() {
                    const selectedInfractionId = this.value;
                    
                    if (selectedInfractionId && infractionData[selectedInfractionId]) {
                        // Preenche os pontos
                        if (pointsInput) {
                            pointsInput.value = infractionData[selectedInfractionId].points;
                        }
                        
                        // Preenche o valor da multa
                        if (amountInput) {
                            amountInput.value = infractionData[selectedInfractionId].amount.toFixed(2);
                        }
                    }
                });
            }

            // Código para o loading overlay
            const form = document.querySelector('form');
            const submitBtn = document.getElementById('submit-btn');
            const loadingOverlay = document.getElementById('loading-overlay');
            const progressBar = document.getElementById('progress-bar');
            const statusMessage = document.getElementById('status-message');
            
            const messages = [
                "Analisando os detalhes da infração...",
                "Consultando a legislação de trânsito...",
                "Identificando fundamentos jurídicos...",
                "Elaborando argumentação técnica...",
                "Verificando jurisprudência aplicável...",
                "Estruturando a defesa administrativa...",
                "Aplicando formatação jurídica...",
                "Finalizando a redação do recurso...",
                "Gerando o documento PDF..."
            ];
            
            let currentStep = 0;
            let interval;
            
            form.addEventListener('submit', function(e) {
                // Mostrar a overlay de carregamento
                loadingOverlay.classList.remove('hidden');
                loadingOverlay.classList.add('flex');
                
                // Desabilitar o botão de submit
                submitBtn.disabled = true;
                
                // Iniciar a animação de progresso
                currentStep = 0;
                updateStatus();
                
                interval = setInterval(function() {
                    currentStep++;
                    if (currentStep >= messages.length) {
                        clearInterval(interval);
                        return;
                    }
                    updateStatus();
                }, 3000);
                
                // O formulário continua normalmente
            });
            
            function updateStatus() {
                statusMessage.textContent = messages[currentStep];
                const progress = Math.min(100, Math.round((currentStep + 1) / messages.length * 100));
                progressBar.style.width = progress + '%';
            }
        });
    </script>
    @endpush
</x-app-layout>
