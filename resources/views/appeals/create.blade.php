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
                    <form method="POST" action="{{ route('appeals.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

                        <!-- Dados Pessoais -->
                        <div class="space-y-4" x-data="{
                            formatCpf(value) {
                                let v = value.replace(/\D/g, '');
                                v = v.substring(0, 11);
                                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                                v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                                return v;
                            },
                            formatPhone(value) {
                                let v = value.replace(/\D/g, '');
                                v = v.substring(0, 11);
                                if (v.length <= 2) {
                                    v = v.replace(/(\d{0,2})/, '($1');
                                } else if (v.length <= 6) {
                                    v = v.replace(/(\d{2})(\d{0,4})/, '($1) $2');
                                } else if (v.length <= 10) {
                                    v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
                                } else { // v.length === 11
                                    v = v.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                                }
                                return v;
                            }
                        }">
                            <h3 class="text-lg font-medium text-gray-900">Dados Pessoais</h3>
                            
                            <div>
                                <x-input-label for="name" :value="__('Nome Completo')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', Auth::user()->name)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="cpf" :value="__('CPF')" />
                                <x-text-input id="cpf" name="cpf" type="text" class="mt-1 block w-full" 
                                              :value="old('cpf', Auth::user()->cpf)" 
                                              x-on:input="$event.target.value = formatCpf($event.target.value)" 
                                              required />
                                <x-input-error class="mt-2" :messages="$errors->get('cpf')" />
                            </div>

                            <div>
                                <x-input-label for="driver_license" :value="__('Número da CNH')" />
                                <x-text-input id="driver_license" name="driver_license" type="text" class="mt-1 block w-full" :value="old('driver_license')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('driver_license')" />
                            </div>

                            <div>
                                <x-input-label for="driver_license_category" :value="__('Categoria da CNH')" />
                                <select id="driver_license_category" name="driver_license_category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">{{ __('Selecione a categoria...') }}</option>
                                    @php $selectedCategory = old('driver_license_category', Auth::user()->cnh_category); @endphp
                                    <option value="A" {{ $selectedCategory == 'A' ? 'selected' : '' }}>A - Motocicleta</option>
                                    <option value="B" {{ $selectedCategory == 'B' ? 'selected' : '' }}>B - Automóvel</option>
                                    <option value="AB" {{ $selectedCategory == 'AB' ? 'selected' : '' }}>AB - Moto e Automóvel</option>
                                    <option value="C" {{ $selectedCategory == 'C' ? 'selected' : '' }}>C - Caminhão</option>
                                    <option value="AC" {{ $selectedCategory == 'AC' ? 'selected' : '' }}>AC - Moto e Caminhão</option>
                                    <option value="D" {{ $selectedCategory == 'D' ? 'selected' : '' }}>D - Ônibus</option>
                                    <option value="E" {{ $selectedCategory == 'E' ? 'selected' : '' }}>E - Carreta</option>
                                    <option value="ACC" {{ $selectedCategory == 'ACC' ? 'selected' : '' }}>ACC - Autorização para Conduzir Ciclomotor</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('driver_license_category')" />
                            </div>

                            <div>
                                <x-input-label for="address" :value="__('Endereço Completo')" />
                                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', Auth::user()->cnh_address)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            <div>
                                <x-input-label for="phone" :value="__('Telefone')" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" 
                                              :value="old('phone', Auth::user()->phone)" 
                                              x-on:input="$event.target.value = formatPhone($event.target.value)" 
                                              required />
                                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('E-mail')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', Auth::user()->email)" required />
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
                                <x-input-label for="reason" :value="__('Descrição da Infração')" />
                                <textarea id="reason" name="reason" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" required>{{ old('reason', $ticket->reason) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('reason')" />
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
</x-app-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
