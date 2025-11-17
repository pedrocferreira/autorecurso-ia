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

                        <!-- Importação via IA -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Importar dados da multa (opcional)</h3>
                            <p class="text-sm text-gray-600">
                                Use a inteligência artificial para reconhecer automaticamente os dados da autuação.
                            </p>

                            <div class="mt-4">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center bg-gray-50 hover:border-indigo-400 transition group">
                                    <input type="file" id="ocr-document" accept="image/*,application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" aria-describedby="ocr-help">
                                    <div class="flex flex-col items-center justify-center pointer-events-none space-y-3">
                                        <div class="flex items-center justify-center w-14 h-14 rounded-full bg-indigo-100 text-indigo-600 group-hover:bg-indigo-200 transition">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 12 3.5-3.5M12 16 8.5 12.5M6 20h12a2 2 0 0 0 2-2v-4.586a1 1 0 0 0-.293-.707l-6.414-6.414a1 1 0 0 0-1.414 0L4.293 12.707A1 1 0 0 0 4 13.414V18a2 2 0 0 0 2 2Z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 group-hover:text-indigo-600 transition">
                                                Arraste e solte ou clique para selecionar
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Formatos aceitos: JPG, PNG, PDF · Máx. 8MB
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <p id="ocr-help" class="text-xs text-gray-500 mt-2">
                                    Dica: prefira imagens legíveis, capturadas em boa iluminação.
                                </p>
                                <div id="ocr-loading" class="hidden mt-3 p-3 rounded-lg bg-indigo-50 border border-indigo-200 text-sm text-indigo-700 flex items-center">
                                    <svg class="animate-spin h-5 w-5 mr-2 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    Processando com IA... aguarde alguns segundos.
                                </div>
                                <div id="ocr-feedback" class="mt-3 text-sm text-gray-500"></div>
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

                                {{-- Campos removidos: telefone, email, endereço --}}
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

                                {{-- Campos removidos: ano e cor do veículo --}}

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
            const infractionByCode = {};
            Object.entries(infractionData).forEach(([id, data]) => {
                if (data.code) {
                    infractionByCode[data.code.replace(/[^A-Z0-9]/gi, '').toUpperCase()] = id;
                }
            });

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

            // Integração com OCR/Gemini
            const ocrInput = document.getElementById('ocr-document');
            const ocrFeedback = document.getElementById('ocr-feedback');
            const ocrLoading = document.getElementById('ocr-loading');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            const fieldMap = {
                name: 'name',
                cpf: 'cpf',
                driver_license: 'driver_license',
                driver_license_category: 'driver_license_category',
                address: 'address',
                phone: 'phone',
                email: 'email',
                plate: 'plate',
                vehicle_model: 'vehicle_model',
                vehicle_year: 'vehicle_year',
                vehicle_color: 'vehicle_color',
                vehicle_chassi: 'vehicle_chassi',
                vehicle_renavam: 'vehicle_renavam',
                citation_number: 'citation_number',
                date: 'date',
                time: 'time',
                location: 'location',
                amount: 'amount',
                points: 'points',
                reason: 'reason'
            };

            const clearOcrStatus = () => {
                ocrFeedback.textContent = '';
                ocrFeedback.classList.remove('text-red-600', 'text-green-600');
                ocrFeedback.classList.add('text-gray-500');
            };

            const setOcrStatus = (message, type = 'info') => {
                ocrFeedback.textContent = message;
                ocrFeedback.classList.remove('text-gray-500', 'text-red-600', 'text-green-600');
                if (type === 'error') {
                    ocrFeedback.classList.add('text-red-600');
                } else if (type === 'success') {
                    ocrFeedback.classList.add('text-green-600');
                } else {
                    ocrFeedback.classList.add('text-gray-500');
                }
            };

            const toggleOcrLoading = (show) => {
                if (show) {
                    ocrLoading.classList.remove('hidden');
                    ocrLoading.classList.add('flex');
                } else {
                    ocrLoading.classList.add('hidden');
                    ocrLoading.classList.remove('flex');
                }
            };

            function normalizeDate(value) {
                if (!value) return null;
                if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
                    return value;
                }
                if (/^\d{2}\/\d{2}\/\d{4}$/.test(value)) {
                    const [dia, mes, ano] = value.split('/');
                    return `${ano}-${mes}-${dia}`;
                }
                const parsed = new Date(value);
                if (!Number.isNaN(parsed.getTime())) {
                    return parsed.toISOString().slice(0, 10);
                }
                return null;
            }

            function normalizeTime(value) {
                if (!value) return null;
                if (/^\d{2}:\d{2}$/.test(value)) return value;
                const parsed = value.match(/(\d{1,2})[:h](\d{2})/);
                if (parsed) {
                    const hours = String(parsed[1]).padStart(2, '0');
                    const minutes = String(parsed[2]).padStart(2, '0');
                    return `${hours}:${minutes}`;
                }
                return null;
            }

            function applyOcrFields(fields, driver) {
                if (!fields || typeof fields !== 'object') {
                    setOcrStatus('Nenhum dado estruturado foi retornado pela IA.', 'error');
                    return;
                }

                Object.entries(fieldMap).forEach(([ocrKey, inputId]) => {
                    const value = fields[ocrKey];
                    if (value === undefined || value === null || value === '') {
                        return;
                    }

                    const element = document.getElementById(inputId);
                    if (!element) return;

                    let formattedValue = value;

                    if (ocrKey === 'driver_license_category') {
                        formattedValue = String(value).trim().toUpperCase();
                    }

                    if (ocrKey === 'vehicle_year') {
                        const numericValue = parseInt(value, 10);
                        if (!Number.isNaN(numericValue)) {
                            formattedValue = numericValue;
                        }
                    }

                    if (ocrKey === 'amount') {
                        const numericValue = parseFloat(String(value).replace(',', '.'));
                        if (!Number.isNaN(numericValue)) {
                            formattedValue = numericValue.toFixed(2);
                        }
                    }

                    if (ocrKey === 'points') {
                        const numericValue = parseInt(value, 10);
                        if (!Number.isNaN(numericValue)) {
                            formattedValue = numericValue;
                        }
                    }

                    if (ocrKey === 'date') {
                        const normalized = normalizeDate(value);
                        if (normalized) {
                            formattedValue = normalized;
                        } else {
                            return;
                        }
                    }

                    if (ocrKey === 'time') {
                        const normalized = normalizeTime(value);
                        if (normalized) {
                            formattedValue = normalized;
                        } else {
                            return;
                        }
                    }

                    if (ocrKey === 'reason' && element.value) {
                        // Concatena observações pré-existentes
                        element.value = `${element.value}\n${formattedValue}`.trim();
                    } else {
                        element.value = formattedValue;
                    }

                    element.dispatchEvent(new Event('change'));
                });

                if (fields.infraction_code) {
                    const normalizedCode = String(fields.infraction_code).replace(/[^A-Z0-9]/gi, '').toUpperCase();
                    const matchedId = infractionByCode[normalizedCode];
                    if (matchedId && infractionTypeSelect) {
                        infractionTypeSelect.value = matchedId;
                        infractionTypeSelect.dispatchEvent(new Event('change'));
                    }
                }

                if (fields.infraction_article && !infractionTypeSelect.value) {
                    const article = String(fields.infraction_article).toLowerCase();
                    const matchedId = Object.entries(infractionData).find(([, data]) =>
                        data.article && data.article.toLowerCase().includes(article)
                    );
                    if (matchedId) {
                        infractionTypeSelect.value = matchedId[0];
                        infractionTypeSelect.dispatchEvent(new Event('change'));
                    }
                }

                const appliedKeys = Object.keys(fieldMap).filter((key) => fields[key]);
                setOcrStatus(`Campos atualizados com sucesso (${appliedKeys.length}). Fonte: ${driver.toUpperCase()}.`, 'success');
            }

            async function handleOcrUpload(file) {
                if (!file) return;
                if (!csrfToken) {
                    setOcrStatus('Token CSRF não encontrado. Atualize a página e tente novamente.', 'error');
                    return;
                }

                // Validação do arquivo antes de enviar
                const maxSize = 8 * 1024 * 1024; // 8MB
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'application/pdf'];
                
                if (file.size > maxSize) {
                    setOcrStatus('O arquivo é muito grande. O tamanho máximo é 8MB.', 'error');
                    return;
                }
                
                if (!allowedTypes.includes(file.type)) {
                    setOcrStatus('Tipo de arquivo não suportado. Use JPEG, PNG, WEBP ou PDF.', 'error');
                    return;
                }

                clearOcrStatus();
                toggleOcrLoading(true);
                setOcrStatus('Enviando arquivo para análise...', 'info');

                const formData = new FormData();
                formData.append('document', file);

                // Log para debug
                console.log('Enviando arquivo:', {
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    lastModified: file.lastModified
                });

                try {
                    const response = await fetch('{{ route('tickets.ocr') }}', {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': csrfToken,
                            // Não definir Content-Type - deixar o browser definir automaticamente para FormData
                        },
                        body: formData,
                    });

                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        console.error('Resposta não-JSON recebida:', text.substring(0, 200));
                        throw new Error('Erro no servidor. Por favor, verifique se o arquivo é válido e tente novamente.');
                    }

                    const payload = await response.json();
                    if (!response.ok || !payload.success) {
                        // Se houver erros de validação, mostra o primeiro erro
                        if (payload.errors && Object.keys(payload.errors).length > 0) {
                            const firstErrorKey = Object.keys(payload.errors)[0];
                            const firstError = payload.errors[firstErrorKey];
                            const message = Array.isArray(firstError) ? firstError[0] : firstError;
                            throw new Error(message || payload.message);
                        }
                        const message = payload.message || payload.error || 'Falha ao executar o reconhecimento. Verifique o arquivo e tente novamente.';
                        throw new Error(message);
                    }

                    const data = payload.data || {};
                    if (!data.fields || typeof data.fields !== 'object') {
                        throw new Error('Nenhum dado foi extraído da imagem. Tente com uma foto mais nítida.');
                    }

                    applyOcrFields(data.fields, data.driver || 'gemini');
                } catch (error) {
                    console.error('Erro no OCR:', error);
                    setOcrStatus(error.message || 'Erro inesperado ao processar o arquivo.', 'error');
                } finally {
                    toggleOcrLoading(false);
                    if (ocrInput) {
                        ocrInput.value = '';
                    }
                }
            }

            if (ocrInput) {
                ocrInput.addEventListener('change', (event) => {
                    const [file] = event.target.files || [];
                    handleOcrUpload(file);
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
