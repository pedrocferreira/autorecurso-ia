<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gerar Recurso') }}
            </h2>
            <a href="{{ route('tickets.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded text-sm">
                Voltar
            </a>
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
                <div class="p-6 text-gray-900">
                    <form id="appealForm" method="POST" action="{{ route('appeals.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

                        <!-- Dados Pessoais -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Dados Pessoais</h3>
                            
                            <div>
                                <x-input-label for="name" :value="__('Nome Completo')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $ticket->name)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="cpf" :value="__('CPF')" />
                                <x-text-input id="cpf" name="cpf" type="text" class="mt-1 block w-full" :value="old('cpf', $ticket->cpf)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('cpf')" />
                            </div>

                            <div>
                                <x-input-label for="driver_license" :value="__('Número da CNH')" />
                                <x-text-input id="driver_license" name="driver_license" type="text" class="mt-1 block w-full" :value="old('driver_license', $ticket->driver_license)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('driver_license')" />
                            </div>

                            <div>
                                <x-input-label for="driver_license_category" :value="__('Categoria da CNH')" />
                                <x-text-input id="driver_license_category" name="driver_license_category" type="text" class="mt-1 block w-full" :value="old('driver_license_category', $ticket->driver_license_category)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('driver_license_category')" />
                            </div>

                            <div>
                                <x-input-label for="address" :value="__('Endereço Completo')" />
                                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $ticket->address)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            <div>
                                <x-input-label for="phone" :value="__('Telefone')" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $ticket->phone)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('E-mail')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $ticket->email)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>
                        </div>

                        <!-- Dados do Veículo -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Dados do Veículo</h3>
                            
                            <div>
                                <x-input-label for="plate" :value="__('Placa do Veículo')" />
                                <x-text-input id="plate" name="plate" type="text" class="mt-1 block w-full" :value="old('plate', $ticket->plate)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('plate')" />
                            </div>

                            <div>
                                <x-input-label for="vehicle_model" :value="__('Modelo do Veículo')" />
                                <x-text-input id="vehicle_model" name="vehicle_model" type="text" class="mt-1 block w-full" :value="old('vehicle_model', $ticket->vehicle_model)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('vehicle_model')" />
                            </div>

                            <div>
                                <x-input-label for="vehicle_year" :value="__('Ano do Veículo')" />
                                <x-text-input id="vehicle_year" name="vehicle_year" type="number" class="mt-1 block w-full" :value="old('vehicle_year', $ticket->vehicle_year)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('vehicle_year')" />
                            </div>

                            <div>
                                <x-input-label for="vehicle_color" :value="__('Cor do Veículo')" />
                                <x-text-input id="vehicle_color" name="vehicle_color" type="text" class="mt-1 block w-full" :value="old('vehicle_color', $ticket->vehicle_color)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('vehicle_color')" />
                            </div>

                            <div>
                                <x-input-label for="vehicle_chassi" :value="__('Chassi do Veículo')" />
                                <x-text-input id="vehicle_chassi" name="vehicle_chassi" type="text" class="mt-1 block w-full" :value="old('vehicle_chassi', $ticket->vehicle_chassi)" maxlength="17" required />
                                <p class="text-xs text-gray-500 mt-1">O chassi deve ter até 17 caracteres.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('vehicle_chassi')" />
                            </div>

                            <div>
                                <x-input-label for="vehicle_renavam" :value="__('RENAVAM')" />
                                <x-text-input id="vehicle_renavam" name="vehicle_renavam" type="text" class="mt-1 block w-full" :value="old('vehicle_renavam', $ticket->vehicle_renavam)" maxlength="11" required />
                                <p class="text-xs text-gray-500 mt-1">O RENAVAM deve ter até 11 caracteres.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('vehicle_renavam')" />
                            </div>
                        </div>

                        <!-- Dados da Multa -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Dados da Multa</h3>
                            
                            <!-- Detalhes da Multa -->
                            @if($ticket->infraction_type)
                            <div class="bg-blue-50 p-4 rounded-md border border-blue-200 mb-4">
                                <h4 class="font-medium text-blue-800 mb-2">Detalhes da Infração:</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <div>
                                        <p class="text-sm text-gray-700"><span class="font-semibold">Código:</span> {{ $ticket->infraction_type->code }}</p>
                                        <p class="text-sm text-gray-700"><span class="font-semibold">Descrição:</span> {{ $ticket->infraction_type->description }}</p>
                                        <p class="text-sm text-gray-700"><span class="font-semibold">Artigo CTB:</span> {{ $ticket->infraction_type->article }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-700"><span class="font-semibold">Valor da Multa:</span> R$ {{ number_format($ticket->amount, 2, ',', '.') }}</p>
                                        <p class="text-sm text-gray-700 font-bold text-red-600"><span class="font-semibold">Pontos na CNH:</span> {{ $ticket->points }}</p>
                                        <p class="text-sm text-gray-700"><span class="font-semibold">Data da Infração:</span> {{ $ticket->date ? $ticket->date->format('d/m/Y') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <div>
                                <x-input-label for="infraction_type_id" :value="__('Tipo de Infração')" />
                                <select id="infraction_type_id" name="infraction_type_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Selecione o tipo de infração</option>
                                    @foreach(\App\Models\InfractionType::where('active', true)->orderBy('code')->get() as $type)
                                        <option value="{{ $type->id }}" {{ old('infraction_type_id', $ticket->infraction_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->code }} - {{ $type->description }} (R$ {{ number_format($type->base_amount, 2, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('infraction_type_id')" />
                            </div>

                            <div>
                                <x-input-label for="date" :value="__('Data da Infração')" />
                                <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" 
                                    :value="old('date', $ticket->date ? $ticket->date->format('Y-m-d') : '')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('date')" />
                            </div>

                            <div>
                                <x-input-label for="amount" :value="__('Valor da Multa')" />
                                <x-text-input id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('amount', $ticket->amount)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                            </div>

                            <div>
                                <x-input-label for="points" :value="__('Pontos na CNH')" />
                                <x-text-input id="points" name="points" type="number" class="mt-1 block w-full" :value="old('points', $ticket->points)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('points')" />
                            </div>

                            <div>
                                <x-input-label for="client_justification" :value="__('Descrição da Infração')" />
                                <textarea id="client_justification" name="client_justification" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" required>{{ old('client_justification', $ticket->client_justification) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('client_justification')" />
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition mr-2">
                                Cancelar
                            </a>

                            <button type="submit" id="submit-btn" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition">
                                <i class="fas fa-file-alt mr-2"></i>
                                Gerar Recurso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Overlay de Carregamento -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-content">
            <div class="loading-animation">
                <div class="loading-circle"></div>
                <div class="loading-circle"></div>
                <div class="loading-circle"></div>
            </div>
            <h4>Gerando seu recurso...</h4>
            <div class="loading-progress">
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
                <div class="progress-text">Processando...</div>
            </div>
            <div class="loading-steps">
                <div class="step active">
                    <i class="fas fa-search"></i>
                    <span>Analisando os detalhes da infração</span>
                </div>
                <div class="step">
                    <i class="fas fa-book"></i>
                    <span>Consultando a legislação</span>
                </div>
                <div class="step">
                    <i class="fas fa-gavel"></i>
                    <span>Elaborando argumentação</span>
                </div>
                <div class="step">
                    <i class="fas fa-file-alt"></i>
                    <span>Gerando documento</span>
                </div>
            </div>
            <div class="alert alert-info mt-4">
                <h6><i class="fas fa-info-circle"></i> Informações sobre a entrega:</h6>
                <p>Após a geração, você receberá um PDF com todas as informações necessárias. O endereço para entrega do recurso está disponível na notificação da multa.</p>
                <p>Documentos necessários para anexar:</p>
                <ul>
                    <li>Cópia da CNH</li>
                    <li>Cópia do documento do veículo</li>
                    <li>Cópia do comprovante de endereço</li>
                    <li>Cópia da notificação da multa</li>
                </ul>
            </div>
        </div>
    </div>

    <style>
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        backdrop-filter: blur(5px);
    }

    .loading-content {
        text-align: center;
        max-width: 600px;
        padding: 2.5rem;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .loading-animation {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 2rem;
    }

    .loading-circle {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #4F46E5;
        margin: 0 8px;
        animation: bounce 1.4s infinite ease-in-out;
    }

    .loading-circle:nth-child(1) { animation-delay: -0.32s; }
    .loading-circle:nth-child(2) { animation-delay: -0.16s; }

    @keyframes bounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }

    .loading-content h4 {
        color: #1F2937;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .loading-progress {
        margin-bottom: 2rem;
    }

    .progress-bar {
        height: 6px;
        background-color: #E5E7EB;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .progress-fill {
        height: 100%;
        background-color: #4F46E5;
        width: 0%;
        animation: progress 2s ease-in-out infinite;
    }

    @keyframes progress {
        0% { width: 0%; }
        50% { width: 100%; }
        100% { width: 0%; }
    }

    .progress-text {
        color: #6B7280;
        font-size: 0.875rem;
    }

    .loading-steps {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .step {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        border-radius: 8px;
        background-color: #F3F4F6;
        transition: all 0.3s ease;
    }

    .step.active {
        background-color: #EEF2FF;
        color: #4F46E5;
    }

    .step i {
        font-size: 1.25rem;
    }

    .step span {
        font-size: 0.875rem;
    }

    .alert {
        background-color: #F0F9FF;
        border: 1px solid #BAE6FD;
        border-radius: 8px;
        padding: 1rem;
        text-align: left;
    }

    .alert h6 {
        color: #0369A1;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .alert p {
        color: #0369A1;
        margin-bottom: 0.5rem;
    }

    .alert ul {
        color: #0369A1;
        margin-bottom: 0;
        padding-left: 1.5rem;
    }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('appealForm');
            const submitBtn = document.getElementById('submit-btn');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const progressFill = document.querySelector('.progress-fill');
            const progressText = document.querySelector('.progress-text');
            const steps = document.querySelectorAll('.loading-steps .step');
            let currentStep = 0;

            // Função para atualizar o progresso
            function updateProgress(step) {
                const progress = (step / steps.length) * 100;
                progressFill.style.width = `${progress}%`;
                progressText.textContent = `Processando... ${Math.round(progress)}%`;
                
                // Atualiza os passos
                steps.forEach((s, index) => {
                    if (index < step) {
                        s.classList.add('active');
                        s.classList.add('completed');
                    } else if (index === step) {
                        s.classList.add('active');
                        s.classList.remove('completed');
                    } else {
                        s.classList.remove('active');
                        s.classList.remove('completed');
                    }
                });
            }

            // Função para simular o processamento
            function simulateProcessing() {
                const steps = [
                    { text: 'Analisando os detalhes da infração...', duration: 2000 },
                    { text: 'Consultando a legislação...', duration: 2500 },
                    { text: 'Elaborando argumentação...', duration: 3000 },
                    { text: 'Gerando documento...', duration: 2000 }
                ];

                let currentStep = 0;
                const totalSteps = steps.length;

                function processNextStep() {
                    if (currentStep < totalSteps) {
                        const step = steps[currentStep];
                        progressText.textContent = step.text;
                        updateProgress(currentStep + 1);

                        setTimeout(() => {
                            currentStep++;
                            processNextStep();
                        }, step.duration);
                    } else {
                        progressText.textContent = 'Processamento concluído!';
                        setTimeout(() => {
                            loadingOverlay.style.display = 'none';
                            submitBtn.disabled = false;
                        }, 1000);
                    }
                }

                processNextStep();
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validação básica dos campos obrigatórios
                const requiredFields = [
                    'name', 'cpf', 'driver_license', 'driver_license_category',
                    'address', 'phone', 'email', 'plate', 'vehicle_model',
                    'vehicle_year', 'vehicle_color', 'vehicle_chassi', 'vehicle_renavam',
                    'date', 'amount', 'points', 'client_justification', 'infraction_type_id'
                ];

                let missingFields = [];
                requiredFields.forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (!input || !input.value.trim()) {
                        missingFields.push(field);
                    }
                });

                if (missingFields.length > 0) {
                    alert('Por favor, preencha todos os campos obrigatórios:\n' + missingFields.join('\n'));
                    return;
                }

                // Desabilita o botão e mostra o overlay
                submitBtn.disabled = true;
                loadingOverlay.style.display = 'flex';
                updateProgress(0);

                // Simula o processamento
                simulateProcessing();

                // Envia o formulário
                form.submit();
            });
        });
    </script>
</x-app-layout>
