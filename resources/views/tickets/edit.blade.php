<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Multa') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tickets.show', $ticket->id) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded text-sm">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagem de sucesso ou erro -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('tickets.update', $ticket->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Dados Pessoais -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Dados Pessoais</h3>
                            
                            <div>
                                <x-input-label for="name" :value="__('Nome Completo')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $ticket->name)" required autofocus />
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
                            
                            <div>
                                <x-input-label for="infraction_type_id" :value="__('Tipo de Infração')" />
                                <select id="infraction_type_id" name="infraction_type_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Selecione o tipo de infração</option>
                                    @foreach(\App\Models\InfractionType::where('active', true)->orderBy('code')->get() as $type)
                                        <option value="{{ $type->id }}" {{ (old('infraction_type_id', $ticket->infraction_type_id) == $type->id) ? 'selected' : '' }}>
                                            {{ $type->code }} - {{ $type->description }} (R$ {{ number_format($type->base_amount, 2, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('infraction_type_id')" />
                            </div>

                            <div>
                                <x-input-label for="date" :value="__('Data da Infração')" />
                                <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date', $ticket->date ? $ticket->date->format('Y-m-d') : '')" required />
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
                                <x-input-label for="reason" :value="__('Observações (opcional)')" />
                                <textarea id="reason" name="reason" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('reason', $ticket->reason) }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Descreva o que aconteceu, circunstâncias ou detalhes relevantes sobre a infração.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Atualizar Multa') }}
                            </x-primary-button>
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
            const infractionTypeSelect = document.getElementById('infraction_type_id');
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
        });
    </script>
    @endpush
</x-app-layout> 