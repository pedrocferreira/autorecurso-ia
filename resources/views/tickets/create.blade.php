<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nova Multa') }}
            </h2>
            <div class="flex flex-wrap gap-2 items-center">
               <button type="button" onclick="fillFormWithTestData()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm flex items-center">
                    <i class="fas fa-clipboard-list mr-1" style="display: none;"></i> Preencher Dados
                </button>
                <button type="button" onclick="testQuickSubmit()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded text-sm flex items-center">
                    <i class="fas fa-flask mr-1"></i> Testar Formulário
                </button>
                <button type="button" onclick="quickSubmit()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm flex items-center">
                    <i class="fas fa-bolt mr-1" style="display: none;"></i> Cadastro Automático
                </button>
                <button type="button" onclick="toggleMinimalForm()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm flex items-center">
                    <i class="fas fa-compress-alt mr-1"></i> Modo Simples
                </button>
                <button type="button" onclick="fixInfractionValues()"  class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded text-sm flex items-center">
                    <i class="fas fa-wrench mr-1" style="display: none;"></i> Corrigir Valores
                </button>
                <button type="button" onclick="showInfractionTable()" class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded text-sm flex items-center">
                    <i class="fas fa-table mr-1" style="display: none;"></i> Ver Tabela
                </button> 
                <a href="{{ route('tickets.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded text-sm flex items-center">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar
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
                    <form method="POST" action="{{ route('tickets.store') }}" id="ticketForm">
                        @csrf

                        <!-- Erros gerais de validação -->
                        @if ($errors->any())
                        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                            <div class="font-bold">Ocorreram erros de validação:</div>
                            <ul class="mt-2 ml-4 list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Dados Pessoais -->
                        <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6" data-section="personal-data">
                            <div class="bg-blue-600 text-white px-4 py-3 flex items-center">
                                <i class="fas fa-user-circle mr-2"></i>
                                <h6 class="font-semibold text-lg">Dados Pessoais</h6>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('name') border-red-500 @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" required placeholder="Ex: João Silva">
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="cpf" class="block text-sm font-medium text-gray-700 mb-1">CPF</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('cpf') border-red-500 @enderror" 
                                               id="cpf" name="cpf" value="{{ old('cpf') }}" required placeholder="Ex: 123.456.789-00">
                                        @error('cpf')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('phone') border-red-500 @enderror" 
                                               id="phone" name="phone" value="{{ old('phone') }}" required placeholder="Ex: (11) 98765-4321">
                                        @error('phone')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                                        <input type="email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('email') border-red-500 @enderror" 
                                               id="email" name="email" value="{{ old('email') }}" required placeholder="Ex: joao.silva@email.com">
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Endereço Completo</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('address') border-red-500 @enderror" 
                                               id="address" name="address" value="{{ old('address') }}" required placeholder="Ex: Rua das Flores, 123, Apto 45, Centro, São Paulo - SP">
                                        @error('address')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
                                        <input type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('birth_date') border-red-500 @enderror" 
                                               id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required>
                                        @error('birth_date')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="cnh_number" class="block text-sm font-medium text-gray-700 mb-1">Número da CNH</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('cnh_number') border-red-500 @enderror" 
                                               id="cnh_number" name="cnh_number" value="{{ old('cnh_number') }}" required>
                                        @error('cnh_number')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="cnh_category" class="block text-sm font-medium text-gray-700 mb-1">Categoria da CNH</label>
                                        <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('cnh_category') border-red-500 @enderror" 
                                                id="cnh_category" name="cnh_category" required>
                                            <option value="">Selecione a categoria</option>
                                            <option value="A" {{ old('cnh_category') == 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ old('cnh_category') == 'B' ? 'selected' : '' }}>B</option>
                                            <option value="C" {{ old('cnh_category') == 'C' ? 'selected' : '' }}>C</option>
                                            <option value="D" {{ old('cnh_category') == 'D' ? 'selected' : '' }}>D</option>
                                            <option value="E" {{ old('cnh_category') == 'E' ? 'selected' : '' }}>E</option>
                                            <option value="AB" {{ old('cnh_category') == 'AB' ? 'selected' : '' }}>AB</option>
                                            <option value="AC" {{ old('cnh_category') == 'AC' ? 'selected' : '' }}>AC</option>
                                            <option value="AD" {{ old('cnh_category') == 'AD' ? 'selected' : '' }}>AD</option>
                                            <option value="AE" {{ old('cnh_category') == 'AE' ? 'selected' : '' }}>AE</option>
                                        </select>
                                        @error('cnh_category')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="cnh_expiration" class="block text-sm font-medium text-gray-700 mb-1">Validade da CNH</label>
                                        <input type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('cnh_expiration') border-red-500 @enderror" 
                                               id="cnh_expiration" name="cnh_expiration" value="{{ old('cnh_expiration') }}" required>
                                        @error('cnh_expiration')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dados do Veículo -->
                        <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6" data-section="vehicle-data">
                            <div class="bg-blue-600 text-white px-4 py-3 flex items-center">
                                <i class="fas fa-car mr-2"></i>
                                <h6 class="font-semibold text-lg">Dados do Veículo</h6>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="vehicle_plate" class="block text-sm font-medium text-gray-700 mb-1">Placa</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('vehicle_plate') border-red-500 @enderror" 
                                               id="vehicle_plate" name="vehicle_plate" value="{{ old('vehicle_plate') }}" required placeholder="Ex: ABC1234">
                                        @error('vehicle_plate')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="vehicle_chassi" class="block text-sm font-medium text-gray-700 mb-1">Chassi</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('vehicle_chassi') border-red-500 @enderror" 
                                               id="vehicle_chassi" name="vehicle_chassi" value="{{ old('vehicle_chassi') }}" maxlength="17" placeholder="Ex: 9BWZZZ377VT004251">
                                        <p class="mt-1 text-sm text-gray-500">Digite os 17 caracteres do chassi do veículo</p>
                                        @error('vehicle_chassi')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="vehicle_renavam" class="block text-sm font-medium text-gray-700 mb-1">RENAVAM</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('vehicle_renavam') border-red-500 @enderror" 
                                               id="vehicle_renavam" name="vehicle_renavam" value="{{ old('vehicle_renavam') }}" maxlength="11" placeholder="Ex: 12345678901">
                                        <p class="mt-1 text-sm text-gray-500">Digite os 11 dígitos do RENAVAM do veículo</p>
                                        @error('vehicle_renavam')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="vehicle_brand" class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('vehicle_brand') border-red-500 @enderror" 
                                               id="vehicle_brand" name="vehicle_brand" value="{{ old('vehicle_brand') }}" required placeholder="Ex: Volkswagen">
                                        @error('vehicle_brand')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="vehicle_model" class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('vehicle_model') border-red-500 @enderror" 
                                               id="vehicle_model" name="vehicle_model" value="{{ old('vehicle_model') }}" required placeholder="Ex: Gol 1.0">
                                        @error('vehicle_model')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="vehicle_year" class="block text-sm font-medium text-gray-700 mb-1">Ano</label>
                                        <input type="number" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('vehicle_year') border-red-500 @enderror" 
                                               id="vehicle_year" name="vehicle_year" value="{{ old('vehicle_year') }}" required placeholder="Ex: 2022">
                                        @error('vehicle_year')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="vehicle_color" class="block text-sm font-medium text-gray-700 mb-1">Cor</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('vehicle_color') border-red-500 @enderror" 
                                               id="vehicle_color" name="vehicle_color" value="{{ old('vehicle_color') }}" required placeholder="Ex: Prata">
                                        @error('vehicle_color')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="vehicle_owner" class="block text-sm font-medium text-gray-700 mb-1">Proprietário</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('vehicle_owner') border-red-500 @enderror" 
                                               id="vehicle_owner" name="vehicle_owner" value="{{ old('vehicle_owner') }}" required placeholder="Ex: João Silva">
                                        @error('vehicle_owner')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dados da Infração -->
                        <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6" data-section="infraction-data">
                            <div class="bg-blue-600 text-white px-4 py-3 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <h6 class="font-semibold text-lg">Dados da Infração</h6>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="infraction_type_id" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Infração</label>
                                        <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('infraction_type_id') border-red-500 @enderror" 
                                                id="infraction_type_id" name="infraction_type_id" required 
                                                onchange="console.log('Alteração detectada no select:', this.value); updateInfractionFields();">
                                            <option value="">Selecione o tipo de infração</option>
                                            @foreach($infractionTypes as $type)
                                                <option value="{{ $type->id }}" 
                                                    {{ old('infraction_type_id') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->code }} - {{ $type->description }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('infraction_type_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="infraction_date" class="block text-sm font-medium text-gray-700 mb-1">Data da Infração</label>
                                        <input type="datetime-local" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('infraction_date') border-red-500 @enderror" 
                                               id="infraction_date" name="infraction_date" value="{{ old('infraction_date') }}" required>
                                        @error('infraction_date')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="infraction_location" class="block text-sm font-medium text-gray-700 mb-1">Local da Infração</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('infraction_location') border-red-500 @enderror" 
                                               id="infraction_location" name="infraction_location" value="{{ old('infraction_location') }}" required placeholder="Ex: Av. Paulista, 1000, São Paulo - SP">
                                        @error('infraction_location')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="infraction_agent" class="block text-sm font-medium text-gray-700 mb-1">Agente de Trânsito</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('infraction_agent') border-red-500 @enderror" 
                                               id="infraction_agent" name="infraction_agent" value="{{ old('infraction_agent') }}" required placeholder="Ex: Carlos Silva (Matrícula 12345)">
                                        @error('infraction_agent')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="infraction_equipment" class="block text-sm font-medium text-gray-700 mb-1">Equipamento Utilizado</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('infraction_equipment') border-red-500 @enderror" 
                                               id="infraction_equipment" name="infraction_equipment" value="{{ old('infraction_equipment') }}" placeholder="Ex: Radar Fixo RDF-384">
                                        <p class="mt-1 text-sm text-gray-500">Opcional. Preencha se houver equipamento envolvido na autuação.</p>
                                        @error('infraction_equipment')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="process_number" class="block text-sm font-medium text-gray-700 mb-1">Número do Processo</label>
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('process_number') border-red-500 @enderror" 
                                               id="process_number" name="process_number" value="{{ old('process_number') }}" placeholder="Ex: AB123456789/2024">
                                        <p class="mt-1 text-sm text-gray-500">Número do processo administrativo da multa (opcional)</p>
                                        @error('process_number')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <x-input-label for="client_justification" :value="__('Sua Justificativa / Versão dos Fatos')" />
                                        <textarea id="client_justification" name="client_justification" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4">{{ old('client_justification') }}</textarea>
                                        <p class="mt-1 text-sm text-gray-500">Descreva sua versão dos fatos e argumentos que justificam a contestação da multa.</p>
                                        <x-input-error class="mt-2" :messages="$errors->get('client_justification')" />
                                    </div>
                                    <div class="hidden">
                                        <input type="hidden" id="infraction_points" name="infraction_points" value="{{ old('infraction_points', 0) }}">
                                        <input type="number" id="infraction_amount" name="infraction_amount" value="{{ old('infraction_amount', '0.00') }}" step="0.01" min="0">
                                        <input type="hidden" id="infraction_observation" name="infraction_observation" value="Registro via sistema">
                                    </div>
                                </div>

                                <!-- Detalhes da Infração -->
                                <div id="infraction-details" class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200 hidden">
                                    <h3 class="text-lg font-medium text-gray-800 mb-3">Detalhes da Infração</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Código:</p>
                                            <p id="infraction-code" class="text-gray-900 font-bold"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Artigo:</p>
                                            <p id="infraction-article" class="text-gray-900"></p>
                                        </div>
                                        <div class="md:col-span-2">
                                            <p class="text-sm font-medium text-gray-700">Descrição:</p>
                                            <p id="infraction-description" class="text-gray-900"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Valor da Multa:</p>
                                            <p id="infraction-amount" class="text-red-600 font-bold text-lg"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Pontos na CNH:</p>
                                            <p id="infraction-points" class="text-red-600 font-bold text-lg"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Gravidade:</p>
                                            <p id="infraction-gravity" class="text-orange-600 font-bold"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center">
                                <i class="fas fa-save mr-2"></i> Registrar Multa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-sm w-full text-center">
            <div class="mb-4">
                <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto"></div>
            </div>
            <div class="mb-4">
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full" id="loading-progress" style="width: 0%"></div>
                </div>
            </div>
            <div class="text-gray-700 text-lg font-medium" id="loading-status">Processando...</div>
        </div>
    </div>

    <!-- Modal Tabela de Valores -->
    <div id="infraction-table-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[90vh] overflow-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Tabela de Valores de Infrações</h2>
                <button onclick="document.getElementById('infraction-table-modal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-6">
                <!-- Gravíssimas -->
                <div class="border-l-4 border-red-600 pl-4">
                    <h3 class="text-lg font-bold text-red-600 mb-2 flex justify-between items-center">
                        1. Infrações Gravíssimas
                        <button onclick="setInfractionByGravity('gravissima')" class="bg-red-600 hover:bg-red-700 text-white text-sm py-1 px-3 rounded">
                            Selecionar
                        </button>
                    </h3>
                    <p class="font-semibold">Multa: R$ 293,47 * 3, 5 ou 10 vezes | 7 pontos na CNH</p>
                    <ul class="list-disc ml-6 mt-2 text-gray-700">
                        <li>Dirigir sob influência de álcool (art. 165) ou drogas.</li>
                        <li>Recusar o teste do bafômetro (art. 165-A).</li>
                        <li>Ultrapassar em locais proibidos (como em curvas ou faixa contínua).</li>
                        <li>Disputar rachas ("pegas") (art. 173).</li>
                        <li>Excesso de velocidade acima de 50% do permitido (art. 218).</li>
                        <li>Não usar capacete (motociclistas) ou cinto de segurança (art. 244 e 167).</li>
                        <li>Dirigir usando celular (art. 252, VI).</li>
                        <li>Transportar crianças sem equipamentos de segurança (art. 168).</li>
                    </ul>
                </div>
                
                <!-- Graves -->
                <div class="border-l-4 border-orange-500 pl-4">
                    <h3 class="text-lg font-bold text-orange-500 mb-2 flex justify-between items-center">
                        2. Infrações Graves
                        <button onclick="setInfractionByGravity('grave')" class="bg-orange-500 hover:bg-orange-600 text-white text-sm py-1 px-3 rounded">
                            Selecionar
                        </button>
                    </h3>
                    <p class="font-semibold">Multa: R$ 195,23 | 5 pontos na CNH</p>
                    <ul class="list-disc ml-6 mt-2 text-gray-700">
                        <li>Estacionar em vagas reservadas para idosos/PCD (art. 181, XVII).</li>
                        <li>Dirigir sem CNH ou com documento vencido (art. 162).</li>
                        <li>Fazer conversão proibida (art. 202).</li>
                        <li>Não manter distância segura do veículo à frente (art. 192).</li>
                        <li>Transitar pelo acostamento (art. 193).</li>
                        <li>Usar veículo para jogar água ou detritos em pedestres (art. 171).</li>
                    </ul>
                </div>
                
                <!-- Médias -->
                <div class="border-l-4 border-yellow-500 pl-4">
                    <h3 class="text-lg font-bold text-yellow-600 mb-2 flex justify-between items-center">
                        3. Infrações Médias
                        <button onclick="setInfractionByGravity('media')" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm py-1 px-3 rounded">
                            Selecionar
                        </button>
                    </h3>
                    <p class="font-semibold">Multa: R$ 130,16 | 4 pontos na CNH</p>
                    <ul class="list-disc ml-6 mt-2 text-gray-700">
                        <li>Estacionar em locais proibidos (ex.: frente a hidrantes) (art. 181).</li>
                        <li>Ultrapassar pela direita (exceto em vias de múltiplas faixas) (art. 199).</li>
                        <li>Dirigir sem documentos obrigatórios (ex.: CRLV) (art. 232).</li>
                        <li>Não sinalizar manobras (art. 196).</li>
                        <li>Transportar carga de forma inadequada (art. 230).</li>
                    </ul>
                </div>
                
                <!-- Leves -->
                <div class="border-l-4 border-blue-500 pl-4">
                    <h3 class="text-lg font-bold text-blue-500 mb-2 flex justify-between items-center">
                        4. Infrações Leves
                        <button onclick="setInfractionByGravity('leve')" class="bg-blue-500 hover:bg-blue-600 text-white text-sm py-1 px-3 rounded">
                            Selecionar
                        </button>
                    </h3>
                    <p class="font-semibold">Multa: R$ 88,38 | 3 pontos na CNH</p>
                    <ul class="list-disc ml-6 mt-2 text-gray-700">
                        <li>Usar buzina prolongadamente sem motivo (art. 227).</li>
                        <li>Estacionar afastado da guia (mais de 1 metro) (art. 181, IV).</li>
                        <li>Transitar com luzes queimadas (art. 230).</li>
                        <li>Não usar cadeirinha para crianças (quando aplicável).</li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-6 text-center">
                <button onclick="document.getElementById('infraction-table-modal').classList.add('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                    Fechar
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Biblioteca IMask para máscaras de entrada -->
    <script src="https://unpkg.com/imask"></script>
    <script>
    // Função para preencher o formulário com dados de teste (escopo global)
    window.fillFormWithTestData = function() {
        console.log('Iniciando preenchimento do formulário com dados de teste...');
        
        try {
            // Dados Pessoais
            const personalFields = {
                'name': 'João da Silva',
                'cpf': '123.456.789-00',
                'phone': '(11) 98765-4321',
                'email': 'joao.silva@exemplo.com',
                'address': 'Rua das Flores, 123 - São Paulo, SP - CEP 01234-567',
                'birth_date': '1985-05-15',
                'cnh_number': '12345678901',
                'cnh_category': 'B',
                'cnh_expiration': '2030-12-31'
            };

            // Dados do Veículo
            const vehicleFields = {
                'vehicle_plate': 'ABC1D23',
                'vehicle_chassi': '9BWZZZ377VT004251',
                'vehicle_renavam': '12345678901',
                'vehicle_brand': 'Volkswagen',
                'vehicle_model': 'Gol',
                'vehicle_year': '2020',
                'vehicle_color': 'Preto',
                'vehicle_owner': 'João da Silva'
            };

            // Dados da Infração
            const infractionFields = {
                'infraction_date': new Date().toISOString().slice(0, 16),
                'infraction_location': 'Avenida Paulista, 1000, São Paulo - SP',
                'infraction_agent': 'Carlos Souza',
                'infraction_equipment': '',
                'infraction_observation': 'Registro via sistema',
                'client_justification': 'Não cometi esta infração. O veículo estava estacionado corretamente e havia sinalização adequada. Solicito a revisão desta autuação conforme evidências que serão apresentadas.'
            };

            // Preencher campos pessoais
            console.log('Preenchendo campos pessoais...');
            Object.entries(personalFields).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.value = value;
                    console.log(`Campo ${id} preenchido com: ${value}`);
                } else {
                    console.warn(`Campo ${id} não encontrado no DOM`);
                }
            });

            // Preencher campos do veículo
            console.log('Preenchendo campos do veículo...');
            Object.entries(vehicleFields).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.value = value;
                    console.log(`Campo ${id} preenchido com: ${value}`);
                } else {
                    console.warn(`Campo ${id} não encontrado no DOM`);
                }
            });

            // Preencher campos da infração
            console.log('Preenchendo campos da infração...');
            Object.entries(infractionFields).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.value = value;
                    console.log(`Campo ${id} preenchido com: ${value}`);
                } else {
                    console.warn(`Campo ${id} não encontrado no DOM`);
                }
            });

            // Selecionar e configurar infração
            if (window.infractionData && window.infractionData.length > 0) {
                console.log('Configurando tipo de infração...');
                const infractionSelect = document.getElementById('infraction_type_id');
                if (infractionSelect) {
                    infractionSelect.value = window.infractionData[0].id;
                    const event = new Event('change');
                    infractionSelect.dispatchEvent(event);
                    console.log('Tipo de infração selecionado e evento disparado');
                } else {
                    console.warn('Select de infração não encontrado no DOM');
                }
            } else {
                console.warn('Nenhuma infração disponível no sistema');
            }

            // Atualizar campos de infração
            console.log('Atualizando campos de infração...');
            window.updateInfractionFields();

            console.log('Formulário preenchido com sucesso!');
        } catch (error) {
            console.error('Erro ao preencher formulário:', error);
            alert('Ocorreu um erro ao tentar preencher o formulário. Por favor, tente novamente.');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM carregado, verificando campos...');
        
        // Verificar se o botão existe
        const fillButton = document.querySelector('button[onclick="fillFormWithTestData()"]');
        if (fillButton) {
            console.log('Botão "Preencher Dados" encontrado');
            // Adicionar evento de clique como backup
            fillButton.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Botão clicado via evento');
                window.fillFormWithTestData();
            });
        } else {
            console.warn('Botão "Preencher Dados" não encontrado no DOM');
        }

        // Verificar campos obrigatórios
        const requiredFields = [
            'name', 'cpf', 'phone', 'email', 'address', 'birth_date',
            'cnh_number', 'cnh_category', 'cnh_expiration',
            'vehicle_plate', 'vehicle_chassi', 'vehicle_renavam',
            'vehicle_brand', 'vehicle_model', 'vehicle_year',
            'vehicle_color', 'vehicle_owner',
            'infraction_type_id', 'infraction_date', 'infraction_location',
            'infraction_agent', 'infraction_observation', 'client_justification'
        ];

        console.log('Verificando campos obrigatórios...');
        requiredFields.forEach(fieldId => {
            const element = document.getElementById(fieldId);
            if (element) {
                console.log(`Campo ${fieldId} encontrado`);
            } else {
                console.warn(`Campo ${fieldId} não encontrado no DOM`);
            }
        });

        // Função para aplicar máscaras com fallback
        function applyMask(element, maskOptions) {
            if (typeof IMask !== 'undefined') {
                return IMask(element, maskOptions);
            } else {
                console.warn('IMask não está disponível. As máscaras não serão aplicadas.');
                return { value: '' }; // objeto de fallback
            }
        }

        // Máscaras para os campos
        const cpfMask = applyMask(document.getElementById('cpf'), {
            mask: '000.000.000-00'
        });

        const phoneMask = applyMask(document.getElementById('phone'), {
            mask: '(00) 00000-0000'
        });

        const plateMask = applyMask(document.getElementById('vehicle_plate'), {
            mask: 'AAA0A00'
        });

        const chassiMask = applyMask(document.getElementById('vehicle_chassi'), {
            mask: /^[a-zA-Z0-9]{17}$/
        });

        const renavamMask = applyMask(document.getElementById('vehicle_renavam'), {
            mask: '00000000000'
        });

        // Mapeamento de infração para pontos e valores (escopo global)
        window.infractionData = {!! json_encode($infractionTypes->map(function($type) {
            return [
                'id' => $type->id,
                'points' => (int)$type->points,
                'amount' => (float)$type->base_amount,
                'description' => $type->description,
                'article' => $type->article ?? 'N/A',
                'code' => $type->code
            ];
        }), JSON_PRETTY_PRINT) !!};
        
        console.log('Dados de infrações carregados:', window.infractionData);

        // Função para atualizar os campos baseado na infração selecionada (escopo global)
        window.updateInfractionFields = function() {
            const infractionTypeId = document.getElementById('infraction_type_id').value;
            console.log('Atualizando campos para infração ID:', infractionTypeId);
            
            if (!infractionTypeId) {
                console.warn('Nenhuma infração selecionada');
                return;
            }
            
            const infraction = window.infractionData.find(i => i.id == infractionTypeId);
            const detailsSection = document.getElementById('infraction-details');
            
            if (infraction) {
                console.log('Infração encontrada:', infraction);
                // Classificar a gravidade baseada no valor
                let gravidade = '';
                if (infraction.amount >= 293.47) {
                    gravidade = 'Gravíssima (7 pontos)';
                } else if (infraction.amount >= 195.23) {
                    gravidade = 'Grave (5 pontos)';
                } else if (infraction.amount >= 130.16) {
                    gravidade = 'Média (4 pontos)';
                } else {
                    gravidade = 'Leve (3 pontos)';
                }
                
                // Converter para número e garantir que é um decimal válido
                const amountValue = Number(infraction.amount);
                
                // Preencher campos ocultos para envio ao servidor
                document.getElementById('infraction_points').value = infraction.points;
                document.getElementById('infraction_amount').value = amountValue.toFixed(2);
                
                console.log('Valores definidos:', {
                    points: infraction.points,
                    amount: document.getElementById('infraction_amount').value,
                    amountOriginal: infraction.amount,
                    amountType: typeof amountValue,
                    gravidade: gravidade
                });
                
                // Garantir que o campo infraction_amount tem um valor válido
                const amountField = document.getElementById('infraction_amount');
                if (!amountField.value || amountField.value === '0' || amountField.value === '0.00' || isNaN(parseFloat(amountField.value))) {
                    // Se estiver vazio ou for zero, usar o valor da infração
                    amountField.value = parseFloat(infraction.amount).toFixed(2);
                }
                
                // Atualizar display para o usuário
                document.getElementById('infraction-code').textContent = infraction.code || 'N/A';
                document.getElementById('infraction-description').textContent = infraction.description || 'N/A';
                document.getElementById('infraction-article').textContent = infraction.article || 'N/A';
                document.getElementById('infraction-amount').textContent = 
                    new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(amountValue);
                document.getElementById('infraction-points').textContent = infraction.points + ' pontos';
                
                // Atualizar informação de gravidade
                const gravidadeElement = document.getElementById('infraction-gravity');
                if (gravidadeElement) {
                    gravidadeElement.textContent = gravidade;
                }
                
                detailsSection.classList.remove('hidden');
            } else {
                document.getElementById('infraction_points').value = '';
                document.getElementById('infraction_amount').value = '';
                detailsSection.classList.add('hidden');
            }
        };

        // Inicializa os campos de infração se já houver um tipo selecionado
        if (document.getElementById('infraction_type_id').value) {
            updateInfractionFields();
        }

        // Adiciona o overlay de carregamento ao enviar o formulário
        document.getElementById('ticketForm').addEventListener('submit', function(event) {
            console.log('Formulário sendo enviado...');
            
            // Verificar se o endereço está preenchido
            const addressField = document.getElementById('address');
            if (!addressField || !addressField.value) {
                console.error('Campo de endereço ausente ou vazio!');
                alert('Por favor, preencha o campo de endereço.');
                event.preventDefault();
                return;
            }
            
            // Verificar se o tipo de infração foi selecionado
            const infractionTypeId = document.getElementById('infraction_type_id').value;
            if (!infractionTypeId) {
                alert('Por favor, selecione um tipo de infração.');
                document.getElementById('infraction_type_id').focus();
                event.preventDefault();
                return;
            }
            
            // Encontrar a infração selecionada
            const selectedInfraction = window.infractionData.find(i => i.id == infractionTypeId);
            if (selectedInfraction) {
                // Garantir que os campos estão preenchidos com os valores corretos
                const pointsField = document.getElementById('infraction_points');
                const amountField = document.getElementById('infraction_amount');
                
                // Se os campos estiverem vazios ou zerados, usar os valores do tipo de infração
                if (!pointsField.value || pointsField.value == '0') {
                    pointsField.value = selectedInfraction.points;
                }
                
                if (!amountField.value || amountField.value == '0' || amountField.value == '0.00' || isNaN(parseFloat(amountField.value))) {
                    // Garantir que é um número com duas casas decimais
                    const amount = parseFloat(selectedInfraction.amount);
                    amountField.value = amount.toFixed(2);
                }
                
                console.log('Valores finais de infração antes do envio:', {
                    points: pointsField.value,
                    amount: amountField.value,
                    infractionType: selectedInfraction
                });
            }
            
            // Verificar se os campos obrigatórios estão preenchidos
            const requiredFields = document.querySelectorAll('[required]');
            let allFieldsValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value) {
                    console.error(`Campo obrigatório não preenchido: ${field.id}`);
                    allFieldsValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (!allFieldsValid) {
                event.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios.');
                return;
            }
            
            // Exibe o overlay de carregamento
            const overlay = document.getElementById('loading-overlay');
            overlay.classList.remove('hidden');
            
            let progress = 0;
            const progressBar = document.getElementById('loading-progress');
            const statusText = document.getElementById('loading-status');
            
            const interval = setInterval(function() {
                progress += Math.random() * 10;
                if (progress > 100) progress = 100;
                
                progressBar.style.width = progress + '%';
                
                if (progress < 30) {
                    statusText.textContent = 'Validando dados...';
                } else if (progress < 60) {
                    statusText.textContent = 'Processando infração...';
                } else if (progress < 90) {
                    statusText.textContent = 'Finalizando registro...';
                } else {
                    statusText.textContent = 'Concluindo...';
                    clearInterval(interval);
                }
            }, 500);
        });

        // Função para testar rapidamente o JavaScript
        window.testQuickSubmit = function() {
            console.log('==== Iniciando teste rápido ====');
            
            // Preencher o formulário com dados de teste
            fillFormWithTestData();
            
            // Criar dados específicos de teste para este caso
            const now = new Date();
            const formattedDate = now.toISOString().slice(0, 16);
            document.getElementById('infraction_date').value = formattedDate;
            
            // Seleção aleatória de uma infração
            if (window.infractionData && window.infractionData.length > 0) {
                const randomIndex = Math.floor(Math.random() * window.infractionData.length);
                const randomInfraction = window.infractionData[randomIndex];
                console.log('Selecionando infração aleatória:', randomInfraction);
                
                // Definir a infração no select
                document.getElementById('infraction_type_id').value = randomInfraction.id;
                updateInfractionFields();
                
                // Personalizar a justificativa com detalhes da infração
                document.getElementById('client_justification').value = `Não cometi esta infração (${randomInfraction.code} - ${randomInfraction.description}). O veículo estava estacionado corretamente e havia sinalização adequada. Solicito a revisão desta autuação conforme evidências que serão apresentadas.`;
                
                // Informar o processo de teste
                document.getElementById('process_number').value = `TEST${Math.floor(Math.random() * 1000000)}/2024`;
            }
            
            alert('Formulário preenchido para teste rápido!\n\nA infração foi selecionada aleatoriamente e todos os campos foram preenchidos.\n\nVocê pode revisar e enviar o formulário.');
            
            // Rolar para o botão de submissão
            document.querySelector('button[type="submit"]').scrollIntoView({ behavior: 'smooth' });
        }

        // Função para alternar entre formulário completo e simplificado
        window.toggleMinimalForm = function() {
            const personalData = document.querySelector('[data-section="personal-data"]');
            const vehicleData = document.querySelector('[data-section="vehicle-data"]');
            const infractionData = document.querySelector('[data-section="infraction-data"]');
            
            // Alternar visibilidade
            personalData.classList.toggle('hidden');
            vehicleData.classList.toggle('hidden');
            
            // Atualizar texto do botão
            const button = document.querySelector('button[onclick="toggleMinimalForm()"]');
            if (personalData.classList.contains('hidden')) {
                button.textContent = 'Modo Completo';
                button.classList.remove('bg-green-500', 'hover:bg-green-600');
                button.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
                
                // Preencher campos necessários com dados fictícios
                if (document.getElementById('name').value === '') {
                    fillFormWithTestData();
                }
                
                // Foco na seção de infrações
                infractionData.scrollIntoView({ behavior: 'smooth' });
            } else {
                button.textContent = 'Modo Simples';
                button.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                button.classList.add('bg-green-500', 'hover:bg-green-600');
            }
        }

        // Função para cadastro rápido
        window.quickSubmit = function() {
            if (confirm('Isso irá preencher todos os campos e ENVIAR o formulário automaticamente para criar uma multa com recurso. Deseja continuar?')) {
                try {
                    // Feedback visual
                    const overlay = document.getElementById('loading-overlay');
                    const statusText = document.getElementById('loading-status');
                    overlay.classList.remove('hidden');
                    statusText.textContent = 'Preparando dados para cadastro rápido...';
                    
                    // Preencher dados de teste
                    fillFormWithTestData();
                    
                    // Usar dados específicos para cadastro rápido
                    const now = new Date();
                    
                    // Gerar um número aleatório para o registro
                    const randomNum = Math.floor(Math.random() * 10000);
                    document.getElementById('process_number').value = `AUTO${randomNum}/2024`;
                    
                    // Garantir que uma infração válida está selecionada
                    if (!document.getElementById('infraction_type_id').value) {
                        if (window.infractionData && window.infractionData.length > 0) {
                            // Selecionar uma infração aleatória
                            const randomIndex = Math.floor(Math.random() * window.infractionData.length);
                            document.getElementById('infraction_type_id').value = window.infractionData[randomIndex].id;
                            updateInfractionFields();
                        } else {
                            alert('Não foi possível selecionar uma infração válida.');
                            overlay.classList.add('hidden');
                            return;
                        }
                    }
                    
                    // Verificar campos obrigatórios
                    const requiredFields = document.querySelectorAll('[required]');
                    let allValid = true;
                    
                    requiredFields.forEach(field => {
                        if (!field.value) {
                            console.error(`Campo obrigatório não preenchido: ${field.id}`);
                            allValid = false;
                        }
                    });
                    
                    if (!allValid) {
                        overlay.classList.add('hidden');
                        alert('Alguns campos obrigatórios não foram preenchidos. O formulário não pode ser enviado automaticamente.');
                        return;
                    }
                    
                    // Feedback antes do envio
                    statusText.textContent = 'Enviando formulário...';
                    
                    // Pequeno atraso para mostrar o feedback
                    setTimeout(() => {
                        // Enviar o formulário
                        document.getElementById('ticketForm').submit();
                    }, 800);
                } catch (error) {
                    console.error('Erro ao executar cadastro rápido:', error);
                    alert('Ocorreu um erro ao tentar realizar o cadastro rápido: ' + error.message);
                    document.getElementById('loading-overlay').classList.add('hidden');
                }
            }
        }

        // Função para mostrar a tabela de valores de infração
        window.showInfractionTable = function() {
            document.getElementById('infraction-table-modal').classList.remove('hidden');
        }

        // Função para selecionar automaticamente uma infração por gravidade
        window.setInfractionByGravity = function(gravity) {
            const modal = document.getElementById('infraction-table-modal');
            
            // Buscar uma infração com a gravidade especificada
            let targetAmount = 0;
            let targetPoints = 0;
            
            switch(gravity) {
                case 'gravissima':
                    targetAmount = 293.47;
                    targetPoints = 7;
                    break;
                case 'grave':
                    targetAmount = 195.23;
                    targetPoints = 5;
                    break;
                case 'media':
                    targetAmount = 130.16;
                    targetPoints = 4;
                    break;
                case 'leve':
                    targetAmount = 88.38;
                    targetPoints = 3;
                    break;
                default:
                    alert('Gravidade inválida');
                    return;
            }
            
            // Encontrar infrações que correspondam à gravidade
            const matchingInfractions = window.infractionData.filter(infraction => 
                Math.abs(infraction.amount - targetAmount) < 1 && infraction.points == targetPoints
            );
            
            if (matchingInfractions.length > 0) {
                // Selecionar a primeira infração correspondente
                document.getElementById('infraction_type_id').value = matchingInfractions[0].id;
                updateInfractionFields();
                modal.classList.add('hidden');
                
                // Rolar até a seção de infrações
                document.querySelector('[data-section="infraction-data"]').scrollIntoView({ behavior: 'smooth' });
            } else {
                alert('Não foi encontrada nenhuma infração correspondente a essa gravidade.');
            }
        }

        // Função para corrigir manualmente os valores de infração
        window.fixInfractionValues = function() {
            // Verificar se há infrações disponíveis
            if (!window.infractionData || window.infractionData.length === 0) {
                alert('Nenhuma infração disponível no sistema.');
                return;
            }
            
            // Selecionar uma infração gravíssima como padrão
            const gravissima = window.infractionData.find(i => i.amount >= 293.47 && i.points === 7);
            const grave = window.infractionData.find(i => i.amount >= 195.23 && i.amount < 293.47 && i.points === 5);
            const media = window.infractionData.find(i => i.amount >= 130.16 && i.amount < 195.23 && i.points === 4);
            const leve = window.infractionData.find(i => i.amount < 130.16 && i.points === 3);
            
            // Escolher a primeira infração disponível na ordem de gravidade
            const selectedInfraction = gravissima || grave || media || leve || window.infractionData[0];
            
            console.log('Infração selecionada para correção:', selectedInfraction);
            
            // Definir a infração selecionada no dropdown
            const selectElement = document.getElementById('infraction_type_id');
            selectElement.value = selectedInfraction.id;
            
            // Atualizar os campos manualmente
            document.getElementById('infraction_points').value = selectedInfraction.points;
            document.getElementById('infraction_amount').value = Number(selectedInfraction.amount).toFixed(2);
            
            // Atualizar display
            document.getElementById('infraction-code').textContent = selectedInfraction.code || 'N/A';
            document.getElementById('infraction-description').textContent = selectedInfraction.description || 'N/A';
            document.getElementById('infraction-article').textContent = selectedInfraction.article || 'N/A';
            document.getElementById('infraction-amount').textContent = 
                new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(selectedInfraction.amount);
            document.getElementById('infraction-points').textContent = selectedInfraction.points + ' pontos';
            
            // Determinar gravidade
            let gravidade = '';
            if (selectedInfraction.amount >= 293.47) {
                gravidade = 'Gravíssima (7 pontos)';
            } else if (selectedInfraction.amount >= 195.23) {
                gravidade = 'Grave (5 pontos)';
            } else if (selectedInfraction.amount >= 130.16) {
                gravidade = 'Média (4 pontos)';
            } else {
                gravidade = 'Leve (3 pontos)';
            }
            
            // Atualizar gravidade
            document.getElementById('infraction-gravity').textContent = gravidade;
            
            // Mostrar seção de detalhes
            document.getElementById('infraction-details').classList.remove('hidden');
            
            // Scroll para a seção
            document.querySelector('[data-section="infraction-data"]').scrollIntoView({ behavior: 'smooth' });
            
            console.log('Valores corrigidos para:', {
                points: selectedInfraction.points,
                amount: Number(selectedInfraction.amount).toFixed(2)
            });
            
            alert('Valores de infração corrigidos com sucesso!');
        }
    });
    </script>
    @endpush

    @push('styles')
    <style>
    #loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: none;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        z-index: 9999;
    }

    .progress {
        width: 300px;
        height: 20px;
    }

    .form-control-static {
        padding: 0.375rem 0;
        margin-bottom: 0;
        color: #495057;
    }
    </style>
    @endpush
</x-app-layout>
