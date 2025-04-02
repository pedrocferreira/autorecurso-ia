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

            <!-- Aviso sobre consumo de créditos -->
            <div class="mb-6 bg-yellow-50 p-4 border-l-4 border-yellow-400 rounded-r">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Atenção: Consumo de Créditos</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>O consumo de créditos varia conforme a gravidade da infração que você selecionar:</p>
                            <ul class="mt-1 list-disc list-inside pl-2 space-y-1">
                                <li>Infrações <strong>Leves</strong>: 1 crédito</li>
                                <li>Infrações <strong>Médias</strong>: 3 créditos</li>
                                <li>Infrações <strong>Graves</strong>: 5 créditos</li>
                                <li>Infrações <strong>Gravíssimas</strong>: 8 créditos</li>
                            </ul>
                            <p class="mt-1">Seu saldo atual: <strong>{{ Auth::user()->credits }} créditos</strong></p>
                            <p class="mt-1 text-xs">O valor será debitado automaticamente após a geração do recurso. Você verá mais detalhes ao selecionar o tipo de infração.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form id="appealForm" action="{{ route('appeals.store') }}" method="POST" class="space-y-6">
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
                                        <p>A geração de um recurso consumirá créditos da sua conta conforme a gravidade da infração:</p>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-2">
                                            <div class="bg-white rounded p-2 text-center border border-blue-100">
                                                <p class="font-semibold text-gray-700">Leve</p>
                                                <p class="text-blue-600 font-bold">1 crédito</p>
                                            </div>
                                            <div class="bg-white rounded p-2 text-center border border-blue-100">
                                                <p class="font-semibold text-gray-700">Média</p>
                                                <p class="text-blue-600 font-bold">3 créditos</p>
                                            </div>
                                            <div class="bg-white rounded p-2 text-center border border-blue-100">
                                                <p class="font-semibold text-gray-700">Grave</p>
                                                <p class="text-orange-600 font-bold">5 créditos</p>
                                            </div>
                                            <div class="bg-white rounded p-2 text-center border border-blue-100">
                                                <p class="font-semibold text-gray-700">Gravíssima</p>
                                                <p class="text-red-600 font-bold">8 créditos</p>
                                            </div>
                                        </div>
                                        <p class="mt-2">O valor será debitado automaticamente após a geração do recurso. Preencha todos os dados corretamente.</p>
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
                                    <p class="text-xs text-gray-500 mt-1">Número que consta no auto de infração</p>
                                    @error('citation_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="process_number" class="block text-sm font-medium text-gray-700 mb-1">Número do Processo</label>
                                    <input type="text" id="process_number" name="process_number" value="{{ old('process_number') }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <p class="text-xs text-gray-500 mt-1">Número do processo administrativo (se disponível)</p>
                                    @error('process_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="notification_number" class="block text-sm font-medium text-gray-700 mb-1">Número da Notificação</label>
                                    <input type="text" id="notification_number" name="notification_number" value="{{ old('notification_number') }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <p class="text-xs text-gray-500 mt-1">Número da notificação de penalidade</p>
                                    @error('notification_number')
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

                                <!-- Detalhes da Infração Selecionada -->
                                <div id="infraction-details" class="bg-blue-50 p-4 rounded-md border border-blue-200 mt-2 mb-2 md:col-span-3 hidden">
                                    <h4 class="font-medium text-blue-800 mb-2">Detalhes da Infração Selecionada:</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-sm text-gray-700"><span class="font-semibold">Código:</span> <span id="infraction-code">-</span></p>
                                            <p class="text-sm text-gray-700"><span class="font-semibold">Descrição:</span> <span id="infraction-description">-</span></p>
                                            <p class="text-sm text-gray-700"><span class="font-semibold">Artigo CTB:</span> <span id="infraction-article">-</span></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-700"><span class="font-semibold">Valor da Multa:</span> R$ <span id="infraction-amount">-</span></p>
                                            <p class="text-sm text-gray-700"><span class="font-semibold">Pontos na CNH:</span> <span id="infraction-points">-</span></p>
                                        </div>
                                    </div>
                                    
                                    <!-- Custo de créditos destacado -->
                                    <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded-md">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-yellow-400 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 10-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm font-medium text-gray-800">
                                                Este recurso consumirá <span id="infraction-credit-cost" class="font-bold text-red-600">-</span> créditos do seu saldo.
                                            </span>
                                            <span class="text-xs text-gray-500 ml-1" id="credit-warning-text"></span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1 ml-7">
                                            O consumo varia conforme a gravidade: Leve (1), Média (3), Grave (5), Gravíssima (8) créditos.
                                        </p>
                                    </div>
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
                            <button type="button" id="show-info-modal" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition mr-2">
                                <i class="fas fa-info-circle mr-2"></i>
                                Informações Importantes
                            </button>
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

    <!-- Modal de Informações -->
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">Informações Importantes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Como entregar seu recurso:</h6>
                        <p>Após gerar o recurso, você receberá um PDF com todas as informações necessárias. Para entregar o recurso:</p>
                        <ol>
                            <li>Imprima o PDF do recurso em duas vias</li>
                            <li>Assine todas as páginas</li>
                            <li>Colete os documentos necessários:
                                <ul>
                                    <li>Cópia da CNH</li>
                                    <li>Cópia do documento do veículo</li>
                                    <li>Cópia do comprovante de endereço</li>
                                    <li>Cópia da notificação da multa</li>
                                </ul>
                            </li>
                            <li>O endereço para entrega do recurso está disponível na notificação da multa</li>
                            <li>Envie via Correios com Aviso de Recebimento (AR)</li>
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
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
        const loadingOverlay = document.getElementById('loadingOverlay');
        const steps = document.querySelectorAll('.step');
        let currentStep = 0;

        form.addEventListener('submit', function(e) {
            loadingOverlay.style.display = 'flex';
            
            // Animar os passos
            const stepInterval = setInterval(() => {
                steps.forEach(step => step.classList.remove('active'));
                steps[currentStep].classList.add('active');
                
                currentStep++;
                if (currentStep >= steps.length) {
                    clearInterval(stepInterval);
                }
            }, 1500);
        });
    });
    </script>
</x-app-layout>
