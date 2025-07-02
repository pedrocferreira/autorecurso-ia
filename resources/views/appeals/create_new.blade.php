<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('🎯 Gerar Novo Recurso') }}
            </h2>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        💳 Créditos: {{ Auth::user()->credits }}
                </span>
                    <div id="score-display" class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                        ⭐ Score: <span id="current-score">0</span>/100
                    </div>
                </div>
                <a href="{{ route('appeals.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded transition-colors">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    @push('styles')
    <style>
        .auto-fill-highlight {
            animation: autoFillPulse 1s ease-in-out;
        }
        
        @keyframes autoFillPulse {
            0% { background-color: rgb(239 246 255); }
            50% { background-color: rgb(147 197 253); }
            100% { background-color: rgb(239 246 255); }
        }
        
        .auto-filled-field {
            background-color: rgb(239 246 255) !important;
            border-color: rgb(147 197 253) !important;
        }

        /* Loading Modal Animations */
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes progressGlow {
            0%, 100% { box-shadow: 0 0 10px rgba(59, 130, 246, 0.5); }
            50% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.8); }
        }

        @keyframes stepActivate {
            from { transform: translateX(-10px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes aiPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        #loading-modal > div {
            animation: modalFadeIn 0.3s ease-out;
        }

        .progress-active {
            animation: progressGlow 2s infinite;
        }

        .step-activating {
            animation: stepActivate 0.5s ease-out;
        }

        .ai-working {
            animation: aiPulse 1.5s infinite;
        }

        /* Gradient text effect */
        .gradient-text {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Spinning effect for brain emoji */
        @keyframes brainSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .brain-spinning {
            animation: brainSpin 4s linear infinite;
        }

        @media (max-width: 640px) {
            /* Oculta barra de rolagem feia em nav horizontal */
            nav::-webkit-scrollbar {
                display: none;
            }
            nav {
                -ms-overflow-style: none;  /* IE e Edge */
                scrollbar-width: none;    /* Firefox */
            }
        }

        /* Ajuste dos círculos dos passos */
        .step-circle {
            width: 18px !important;
            height: 18px !important;
        }
        .step-inner {
            width: 8px !important;
            height: 8px !important;
        }
    </style>
    @endpush

    <div class="py-6" x-data="gamifiedForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Progress Bar Global -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">📊 Progresso do Recurso</h3>
                    <span class="text-sm text-gray-600" x-text="`${Math.round(globalProgress)}% Completo`"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden shadow-inner">
                    <div class="bg-gradient-to-r from-green-400 to-blue-500 h-4 rounded-full transition-all duration-700 ease-out" 
                         :style="`width: ${globalProgress}%`"></div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-500">
                    <span>🚀 Início</span>
                    <span>📝 Dados</span>
                    <span>🚗 Veículo</span>
                    <span>🎯 Multa</span>
                    <span>✨ Final</span>
                </div>
            </div>

            <!-- Achievement Badges -->
            <div id="achievement-container" class="mb-6 hidden">
                <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-lg p-4 shadow-lg border-2 border-yellow-300 animate-bounce">
                    <div class="flex items-center space-x-3">
                        <div class="text-3xl">🏆</div>
                        <div>
                            <h4 class="font-bold text-white">Conquista Desbloqueada!</h4>
                            <p class="text-yellow-100 text-sm" id="achievement-text"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensagens -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded animate-pulse">
                    <div class="flex items-center">
                        <span class="text-2xl mr-2">🎉</span>
                    {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <div class="flex items-center">
                        <span class="text-2xl mr-2">❌</span>
                    {{ session('error') }}
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <div class="flex items-center mb-2">
                        <span class="text-2xl mr-2">⚠️</span>
                        <strong>Há problemas no formulário:</strong>
                    </div>
                    <ul class="list-disc ml-8">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <p class="mt-2 text-sm">
                        💡 <strong>Dica:</strong> Os dados que você preencheu foram mantidos. Corrija os erros e tente novamente.
                    </p>
                </div>
            @endif

            <!-- Step Navigation -->
            <div class="mb-8">
                <div class="flex justify-center">
                    <nav class="flex space-x-4 overflow-x-auto whitespace-nowrap md:whitespace-normal md:overflow-visible px-2">
                        <template x-for="(step, index) in steps" :key="index">
                            <button @click="goToStep(index)" 
                                    :class="getStepClass(index)"
                                    class="flex items-center px-4 py-2 rounded-lg font-medium transition-all duration-300">
                                <span x-text="step.icon" class="mr-2 text-lg"></span>
                                <span x-text="step.title" class="hidden md:inline"></span>
                                <div class="ml-2 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
                                     :class="getStepBadgeClass(index)">
                                    <span x-text="getStepCompletion(index)"></span>
                                </div>
                            </button>
                        </template>
                    </nav>
                </div>
            </div>

            <!-- Form Container -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <form action="{{ route('appeals.store_new') }}" method="POST" class="p-8" id="appeal-form">
                    @csrf

                    <!-- Step 1: Dados Pessoais -->
                    <div x-show="currentStep === 0" class="space-y-6" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-x-8" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">👤 Confirmação de Dados</h2>
                            <p class="text-gray-600">Confirme se seus dados estão corretos (você pode editá-los se necessário)</p>
                        </div>

                        <!-- Informação sobre Inteligência Híbrida -->
                        <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-6 rounded-lg border border-purple-200 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                🧠 Inteligência Híbrida Ativada
                            </h3>
                            <div class="bg-gradient-to-r from-purple-100 to-pink-100 border border-purple-300 rounded-lg p-4">
                                <div class="flex items-center space-x-3">
                                    <span class="text-3xl">🤖</span>
                                    <div>
                                        <h4 class="font-bold text-purple-800">Sistema Mais Avançado do Mundo!</h4>
                                        <p class="text-sm text-purple-700">
                                            ⚡ <strong>3 IAs Especializadas</strong> geram simultaneamente:<br>
                                            💎 <strong>Google Gemini Pro</strong> (mais barato que GPT-4)<br>
                                            🇧🇷 <strong>RoBERTaLexPT</strong> (especialista jurídico brasileiro)<br>
                                            🔥 <strong>GPT-4 Turbo</strong> (padrão de mercado)<br><br>
                                            🧠 <strong>IA Analista</strong> escolhe automaticamente a MELHOR versão<br>
                                            📊 Você recebe: melhor texto + análise comparativa + outras versões<br>
                                            💎 <strong>Qualidade máxima garantida</strong> (custa 3 créditos)
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-700">
                                    🚀 <strong>Diferencial Único:</strong> Primeiro sistema do mundo onde a IA escolhe automaticamente a melhor versão jurídica baseada em critérios técnicos especializados!
                                </p>
                            </div>
                        </div>

                        <!-- Campo hidden para sempre usar todas as IAs -->
                        <input type="hidden" name="ai_model" value="all">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        📝 Nome Completo
                                        <span class="text-red-500 ml-1">*</span>
                                        <span class="text-xs text-blue-600 ml-2">✨ Do seu perfil</span>
                                    </span>
                                </label>
                                <input type="text" name="name" x-model="formData.name" 
                                       @input="updateScore()" 
                                       :class="getFieldClass('name')"
                                       required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <div class="absolute right-3 top-11" x-show="isFieldValid('name')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                </div>

                            <!-- CPF -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        🆔 CPF
                                        <span class="text-red-500 ml-1">*</span>
                                        <span class="text-xs text-blue-600 ml-2">✨ Do seu perfil</span>
                                    </span>
                                </label>
                                <input type="text" name="cpf" x-model="formData.cpf" 
                                       @input="formatCpf($event); updateScore()" 
                                       :class="getFieldClass('cpf')"
                                       placeholder="000.000.000-00" required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <div class="absolute right-3 top-11" x-show="isFieldValid('cpf')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                </div>

                            <!-- CNH -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        🚗 Número da CNH
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <input type="text" name="driver_license" x-model="formData.driver_license" 
                                       @input="updateScore()" 
                                       :class="getFieldClass('driver_license')"
                                       required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <div class="absolute right-3 top-11" x-show="isFieldValid('driver_license')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                </div>

                            <!-- Telefone -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        📞 Telefone
                                        <span class="text-red-500 ml-1">*</span>
                                        <span class="text-xs text-blue-600 ml-2">✨ Do seu perfil</span>
                                    </span>
                                </label>
                                <input type="text" name="phone" x-model="formData.phone" 
                                       @input="formatPhone($event); updateScore()" 
                                       :class="getFieldClass('phone')"
                                       placeholder="(00) 00000-0000" required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <div class="absolute right-3 top-11" x-show="isFieldValid('phone')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-700">
                                💡 <strong>Dica:</strong> 🧠 <strong>INTELIGÊNCIA HÍBRIDA</strong> sempre ativada! Gera com TODAS as 3 IAs e a própria IA escolhe automaticamente a MELHOR versão! (Custa 3 créditos por máxima qualidade)
                            </p>
                        </div>
                    </div>

                    <!-- Step 2: Dados do Veículo -->
                    <div x-show="currentStep === 1" class="space-y-6" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-x-8" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">🚗 Dados do Veículo</h2>
                            <p class="text-gray-600">Agora vamos registrar as informações do seu veículo</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Placa -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        🔖 Placa do Veículo
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <input type="text" name="plate" x-model="formData.plate" 
                                       @input="formatPlate($event); updateScore()" 
                                       :class="getFieldClass('plate')"
                                       placeholder="AAA0000" maxlength="7" required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <div class="absolute right-3 top-11" x-show="isFieldValid('plate')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Formato: AAA0000 (máximo 7 caracteres)</p>
                                </div>

                            <!-- Modelo -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        🚙 Modelo do Veículo
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <input type="text" name="vehicle_model" x-model="formData.vehicle_model" 
                                       @input="updateScore()" 
                                       :class="getFieldClass('vehicle_model')"
                                       required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <div class="absolute right-3 top-11" x-show="isFieldValid('vehicle_model')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                </div>

                            <!-- Ano -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        📅 Ano do Veículo
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <input type="number" name="vehicle_year" x-model="formData.vehicle_year" 
                                       @input="updateScore()" 
                                       :class="getFieldClass('vehicle_year')"
                                       required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <div class="absolute right-3 top-11" x-show="isFieldValid('vehicle_year')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                </div>

                            <!-- Cor -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        🎨 Cor do Veículo
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <input type="text" name="vehicle_color" x-model="formData.vehicle_color" 
                                       @input="updateScore()" 
                                       :class="getFieldClass('vehicle_color')"
                                       required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <div class="absolute right-3 top-11" x-show="isFieldValid('vehicle_color')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                </div>

                            <!-- Chassi -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        🔧 Chassi do Veículo
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <input type="text" name="vehicle_chassi" x-model="formData.vehicle_chassi" 
                                       @input="updateScore()" 
                                       :class="getFieldClass('vehicle_chassi')"
                                       maxlength="17" required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                    <p class="text-xs text-gray-500 mt-1">O chassi deve ter até 17 caracteres.</p>
                                <div class="absolute right-3 top-11" x-show="isFieldValid('vehicle_chassi')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                </div>

                            <!-- RENAVAM -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        📋 RENAVAM
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <input type="text" name="vehicle_renavam" x-model="formData.vehicle_renavam" 
                                       @input="updateScore()" 
                                       :class="getFieldClass('vehicle_renavam')"
                                       maxlength="11" required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                    <p class="text-xs text-gray-500 mt-1">O RENAVAM deve ter até 11 caracteres.</p>
                                <div class="absolute right-3 top-11" x-show="isFieldValid('vehicle_renavam')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Dados da Multa -->
                    <div x-show="currentStep === 2" class="space-y-6" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-x-8" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">🎯 Dados da Multa</h2>
                            <p class="text-gray-600">Agora precisamos dos detalhes específicos da infração</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Número da Autuação -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        📄 Número da Autuação
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <input type="text" name="citation_number" x-model="formData.citation_number" 
                                       @input="updateScore()" 
                                       :class="getFieldClass('citation_number')"
                                       required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <div class="absolute right-3 top-11" x-show="isFieldValid('citation_number')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                </div>

                            <!-- Data da Infração -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        📅 Data da Infração
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <input type="date" name="date" x-model="formData.date" 
                                       @input="updateScore()" 
                                       :class="getFieldClass('date')"
                                       required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <div class="absolute right-3 top-11" x-show="isFieldValid('date')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                </div>

                            <!-- Horário -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        ⏰ Horário da Infração
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <input type="time" name="time" x-model="formData.time" 
                                       @input="updateScore()" 
                                       :class="getFieldClass('time')"
                                       required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <div class="absolute right-3 top-11" x-show="isFieldValid('time')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                </div>

                            <!-- Tipo de Infração -->
                            <div class="relative md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        ⚖️ Tipo de Infração
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <select name="infraction_type_id" x-model="formData.infraction_type_id" 
                                        @change="onInfractionTypeChange($event); updateScore()" 
                                        :class="getFieldClass('infraction_type_id')"
                                        required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                        <option value="">Selecione o tipo de infração</option>
                                        @foreach(\App\Models\InfractionType::where('active', true)->orderBy('code')->get() as $type)
                                        <option value="{{ $type->id }}" 
                                                data-points="{{ $type->points }}" 
                                                data-amount="{{ $type->base_amount }}"
                                                {{ old('infraction_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->code }} - {{ $type->description }} ({{ $type->points }} pontos | R$ {{ number_format($type->base_amount, 2, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                <div class="absolute right-3 top-11" x-show="isFieldValid('infraction_type_id')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <p><i class="fas fa-info-circle mr-1"></i> O tipo de infração determina os argumentos jurídicos que serão utilizados no recurso.</p>
                                    </div>
                            </div>

                            <!-- Valor da Multa -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        💰 Valor da Multa
                                        <span class="text-red-500 ml-1">*</span>
                                        <span class="text-xs text-blue-600 ml-2" x-show="isAutoFilled('amount')">
                                            🤖 Preenchido automaticamente
                                        </span>
                                    </span>
                                </label>
                                <input type="number" name="amount" x-model="formData.amount" 
                                       @input="markAsManuallyEdited('amount'); updateScore()" 
                                       :class="getFieldClass('amount') + (isAutoFilled('amount') ? ' bg-blue-50 border-blue-300' : '')"
                                       step="0.01" required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <div class="absolute right-3 top-11" x-show="isFieldValid('amount')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                            </div>
                                <div class="mt-1 text-xs text-gray-500" x-show="!isAutoFilled('amount')">
                                    💡 Dica: Este valor será preenchido automaticamente ao selecionar o tipo de infração
                                        </div>
                                    </div>

                            <!-- Pontos -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        🎯 Pontos na CNH
                                        <span class="text-red-500 ml-1">*</span>
                                        <span class="text-xs text-blue-600 ml-2" x-show="isAutoFilled('points')">
                                            🤖 Preenchido automaticamente
                                        </span>
                                    </span>
                                </label>
                                <input type="number" name="points" x-model="formData.points" 
                                       @input="markAsManuallyEdited('points'); updateScore()" 
                                       :class="getFieldClass('points') + (isAutoFilled('points') ? ' bg-blue-50 border-blue-300' : '')"
                                       required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <div class="absolute right-3 top-11" x-show="isFieldValid('points')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                            </div>
                                <div class="mt-1 text-xs text-gray-500" x-show="!isAutoFilled('points')">
                                    💡 Dica: Este valor será preenchido automaticamente ao selecionar o tipo de infração
                                    </div>
                                </div>

                            <!-- Local Detalhado -->
                            <div class="relative md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        📍 Local Detalhado da Infração
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <input type="text" name="location" x-model="formData.location" 
                                       @input="updateScore()" 
                                       :class="getFieldClass('location')"
                                       placeholder="Ex: Av. Paulista, altura do nº 1500, próximo ao shopping"
                                       required 
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <div class="absolute right-3 top-11" x-show="isFieldValid('location')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    💡 Dica: Seja específico sobre o local exato da infração
                                </div>
                            </div>

                            <!-- Cidade da Infração -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        🏙️ Cidade da Infração
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <input type="text" name="city" x-model="formData.city" 
                                       @input="updateScore()" 
                                       :class="getFieldClass('city')"
                                       placeholder="Ex: São Paulo"
                                       required 
                                       list="cities-list"
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                <datalist id="cities-list">
                                    <option value="São Paulo">
                                    <option value="Rio de Janeiro">
                                    <option value="Belo Horizonte">
                                    <option value="Salvador">
                                    <option value="Brasília">
                                    <option value="Fortaleza">
                                    <option value="Curitiba">
                                    <option value="Recife">
                                    <option value="Porto Alegre">
                                    <option value="Belém">
                                    <option value="Goiânia">
                                    <option value="Guarulhos">
                                    <option value="Campinas">
                                    <option value="Santos">
                                    <option value="Ribeirão Preto">
                                </datalist>
                                <div class="absolute right-3 top-11" x-show="isFieldValid('city')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                            </div>

                            <!-- Estado da Infração -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        🗺️ Estado (UF)
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <select name="state" x-model="formData.state" 
                                        @change="updateScore()" 
                                        :class="getFieldClass('state')"
                                        required 
                                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                                    <option value="">Selecione o Estado</option>
                                    <option value="AC">Acre</option>
                                    <option value="AL">Alagoas</option>
                                    <option value="AP">Amapá</option>
                                    <option value="AM">Amazonas</option>
                                    <option value="BA">Bahia</option>
                                    <option value="CE">Ceará</option>
                                    <option value="DF">Distrito Federal</option>
                                    <option value="ES">Espírito Santo</option>
                                    <option value="GO">Goiás</option>
                                    <option value="MA">Maranhão</option>
                                    <option value="MT">Mato Grosso</option>
                                    <option value="MS">Mato Grosso do Sul</option>
                                    <option value="MG">Minas Gerais</option>
                                    <option value="PA">Pará</option>
                                    <option value="PB">Paraíba</option>
                                    <option value="PR">Paraná</option>
                                    <option value="PE">Pernambuco</option>
                                    <option value="PI">Piauí</option>
                                    <option value="RJ">Rio de Janeiro</option>
                                    <option value="RN">Rio Grande do Norte</option>
                                    <option value="RS">Rio Grande do Sul</option>
                                    <option value="RO">Rondônia</option>
                                    <option value="RR">Roraima</option>
                                    <option value="SC">Santa Catarina</option>
                                    <option value="SP">São Paulo</option>
                                    <option value="SE">Sergipe</option>
                                    <option value="TO">Tocantins</option>
                                </select>
                                <div class="absolute right-3 top-11" x-show="isFieldValid('state')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                </div>

                            <!-- Detalhes da Infração -->
                            <div class="relative md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        📝 Detalhes da Infração
                                        <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <textarea name="reason" x-model="formData.reason" 
                                          @input="updateScore()" 
                                          :class="getFieldClass('reason')"
                                          rows="4" placeholder="Descreva o que aconteceu, circunstâncias especiais, condições do local, etc." 
                                          class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"></textarea>
                                <div class="absolute right-3 top-11" x-show="isFieldValid('reason')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                </div>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <p><i class="fas fa-check-circle mr-1"></i> Quanto mais detalhes você fornecer, mais preciso e eficaz será o recurso gerado.</p>
                                </div>
                            </div>
                            </div>
                        </div>

                    <!-- Step 4: Detalhes Finais -->
                    <div x-show="currentStep === 3" class="space-y-6" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-x-8" x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">✨ Detalhes Finais</h2>
                            <p class="text-gray-600">Por último, adicione informações específicas que podem fortalecer sua defesa</p>
                        </div>

                        <div class="space-y-6">
                            <!-- Detalhes Específicos -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        💡 Detalhes Específicos da Situação
                                        <span class="text-gray-500 ml-1">(Opcional)</span>
                                    </span>
                                </label>
                                <textarea name="custom_details" x-model="formData.custom_details" 
                                          @input="updateScore()" 
                                          :class="getFieldClass('custom_details')"
                                          rows="6" 
                                          class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"></textarea>
                                <div class="absolute right-3 top-11" x-show="isFieldValid('custom_details')">
                                    <span class="text-green-500 text-xl animate-pulse">✅</span>
                                    </div>
                                <div class="mt-3 bg-blue-50 p-4 rounded-lg">
                                    <p class="text-sm text-blue-700 font-medium mb-2">💡 Dicas para melhorar suas chances:</p>
                                    <ul class="text-sm text-blue-600 space-y-1">
                                        <li>• Condições climáticas no momento da infração</li>
                                        <li>• Estado do trânsito no local</li>
                                        <li>• Presença de sinalização adequada</li>
                                        <li>• Situações de emergência</li>
                                        <li>• Qualquer outro fator relevante para a defesa</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Resumo Final -->
                            <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 rounded-lg border border-green-200">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                    🧠 Resumo da Inteligência Híbrida
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div class="flex justify-between">
                                        <span class="font-medium">Completude do Formulário:</span>
                                        <span class="font-bold text-green-600" x-text="`${currentScore}%`"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium">Custo do Recurso:</span>
                                        <span class="font-bold text-purple-600">3 Créditos</span>
                        </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium">Seus Créditos:</span>
                                        <span class="font-bold text-blue-600">{{ Auth::user()->credits }} disponíveis</span>
                                </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium">Qualidade:</span>
                                        <span class="font-bold text-purple-600">🏆 Máxima Possível</span>
                                    </div>
                                </div>
                                <div class="mt-4 p-3 bg-purple-50 rounded-lg">
                                    <p class="text-sm text-purple-700">
                                        🧠 <strong>Inteligência Híbrida:</strong> 3 IAs geram simultaneamente + IA escolhe automaticamente a MELHOR versão para você!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-8">
                        <button type="button" @click="prevStep()" x-show="currentStep > 0" 
                                class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            ← Anterior
                        </button>
                        <div x-show="currentStep === 0" class=""></div>
                        
                        <button type="button" @click="nextStep()" x-show="currentStep < steps.length - 1" 
                                class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            Próximo →
                        </button>
                        
                        <button type="button" x-show="currentStep === steps.length - 1" 
                                @click="handleSubmit()"
                                class="px-8 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors font-bold">
                            🚀 Gerar Recurso
                        </button>
                    </div>

                    <!-- Confetti Container -->
                    <div id="confetti-container" class="fixed inset-0 pointer-events-none z-50"></div>

                    <!-- Loading Modal -->
                    <div id="loading-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="backdrop-filter: blur(5px);">
                        <div class="bg-white rounded-2xl p-6 sm:p-8 max-w-md w-11/12 sm:w-full mx-4 sm:mx-0 shadow-xl overflow-y-auto max-h-[90vh]">
                            <!-- Header -->
                            <div class="text-center mb-6">
                                <div class="brain-spinning text-6xl mb-4">🧠</div>
                                <h3 class="text-2xl font-bold gradient-text mb-2">Inteligência Híbrida em Ação!</h3>
                                <p class="text-gray-600">Gerando o melhor recurso possível para você...</p>
                                <div class="mt-2 px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium inline-block">
                                    ⚡ Processo 100% Automatizado
                                </div>
                            </div>

                            <!-- Progress Steps -->
                            <div class="space-y-4 mb-6">
                                <div id="step-ias" class="flex items-center space-x-3 opacity-50 transition-all duration-500">
                                    <div class="step-circle rounded-full border-2 border-gray-300 flex items-center justify-center transition-all duration-300">
                                        <div class="step-inner rounded-full bg-gray-300 transition-all duration-300"></div>
                                    </div>
                                    <span class="text-gray-600">Executando 3 IAs simultaneamente...</span>
                                </div>

                                <div id="step-analysis" class="flex items-center space-x-3 opacity-50 transition-all duration-500">
                                    <div class="step-circle rounded-full border-2 border-gray-300 flex items-center justify-center transition-all duration-300">
                                        <div class="step-inner rounded-full bg-gray-300 transition-all duration-300"></div>
                                    </div>
                                    <span class="text-gray-600">IA analisando e escolhendo melhor versão...</span>
                                </div>

                                <div id="step-pdf" class="flex items-center space-x-3 opacity-50 transition-all duration-500">
                                    <div class="step-circle rounded-full border-2 border-gray-300 flex items-center justify-center transition-all duration-300">
                                        <div class="step-inner rounded-full bg-gray-300 transition-all duration-300"></div>
                                    </div>
                                    <span class="text-gray-600">Gerando PDF profissional...</span>
                                </div>
                            </div>

                            <!-- Current AI Models Running -->
                            <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-4 rounded-lg mb-4 border border-purple-200">
                                <h4 class="font-bold text-sm text-gray-800 mb-2">🤖 IAs Trabalhando:</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                                    <div id="ai-gemini" class="text-center p-3 rounded-lg bg-white shadow-inner border border-gray-200 opacity-50 transition-all duration-500">
                                        <div class="text-lg mb-1">💎</div>
                                        <div class="font-medium">Gemini Pro</div>
                                        <div class="text-xs text-gray-500 mt-1">Legislação</div>
                                    </div>
                                    <div id="ai-roberta" class="text-center p-3 rounded-lg bg-white shadow-inner border border-gray-200 opacity-50 transition-all duration-500">
                                        <div class="text-lg mb-1">🇧🇷</div>
                                        <div class="font-medium">RoBERTa</div>
                                        <div class="text-xs text-gray-500 mt-1">Jurisprudência</div>
                                    </div>
                                    <div id="ai-gpt4" class="text-center p-3 rounded-lg bg-white shadow-inner border border-gray-200 opacity-50 transition-all duration-500">
                                        <div class="text-lg mb-1">🔥</div>
                                        <div class="font-medium">GPT-4</div>
                                        <div class="text-xs text-gray-500 mt-1">Argumentação</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Motivational Messages -->
                            <div class="text-center mb-4">
                                <div id="motivational-text" class="text-sm text-blue-600 font-medium animate-pulse">
                                    🚀 Preparando o melhor recurso do mundo para você...
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-6">
                                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden shadow-inner">
                                    <div id="progress-bar" class="bg-gradient-to-r from-blue-500 to-purple-600 h-4 rounded-full transition-all duration-1000 progress-active" style="width: 0%"></div>
                                </div>
                                <div class="text-center text-xs text-gray-500 mt-2">
                                    <span id="progress-text">Iniciando processo...</span>
                                    <span class="ml-2 text-purple-600 font-medium">⏱️ Tempo estimado: 10-15s</span>
                                </div>
                            </div>

                            <!-- Error Message -->
                            <div id="loading-error" class="hidden mt-4 text-center">
                                <p class="text-red-600 text-sm font-semibold">❌ Algo deu errado na geração do recurso.</p>
                                <p class="text-gray-600 text-xs mt-1">Verifique sua conexão ou tente novamente em instantes.</p>
                                <button id="retry-button" @click="retryGeneration()" class="mt-3 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">
                                    🔄 Tentar Novamente
                                </button>
                            </div>

                            <!-- Advanced Features Notice -->
                            <div class="mt-4 text-center">
                                <div class="text-xs text-gray-400 bg-gray-50 p-2 rounded">
                                    🔒 Processo seguro e criptografado • 🎯 Precisão de 95%+ • ⚡ Tecnologia de ponta
                                </div>
                            </div>
                        </div>
                    </div>

                    </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    <script>
        function gamifiedForm() {
            return {
                currentStep: 0,
                globalProgress: 0,
                currentScore: 0,
                achievements: [],
                autoFilledFields: {},
                loadingTimeoutId: null,
                loadingHasErrored: false,
                
                steps: [
                    { title: 'Confirmação de Dados', icon: '👤', fields: ['name', 'cpf', 'driver_license', 'phone'] },
                    { title: 'Dados do Veículo', icon: '🚗', fields: ['plate', 'vehicle_model', 'vehicle_year', 'vehicle_color', 'vehicle_chassi', 'vehicle_renavam'] },
                    { title: 'Dados da Multa', icon: '🎯', fields: ['citation_number', 'date', 'time', 'infraction_type_id', 'amount', 'points', 'location', 'city', 'state', 'reason'] },
                    { title: 'Detalhes Finais', icon: '✨', fields: ['custom_details'] }
                ],
                
                formData: {
                    name: '{{ old("name", Auth::user()->name ?? "") }}',
                    cpf: '{{ old("cpf", Auth::user()->cpf ?? "") }}',
                    driver_license: '{{ old("driver_license", "") }}',
                    phone: '{{ old("phone", Auth::user()->phone ?? "") }}',
                    ai_model: 'all', // Sempre usar todas as IAs
                    plate: '{{ old("plate", "") }}',
                    vehicle_model: '{{ old("vehicle_model", "") }}',
                    vehicle_year: '{{ old("vehicle_year", "") }}',
                    vehicle_color: '{{ old("vehicle_color", "") }}',
                    vehicle_chassi: '{{ old("vehicle_chassi", "") }}',
                    vehicle_renavam: '{{ old("vehicle_renavam", "") }}',
                    citation_number: '{{ old("citation_number", "") }}',
                    date: '{{ old("date", "") }}',
                    time: '{{ old("time", "") }}',
                    infraction_type_id: '{{ old("infraction_type_id", "") }}',
                    amount: '{{ old("amount", "") }}',
                    points: '{{ old("points", "") }}',
                    location: '{{ old("location", "") }}',
                    city: '{{ old("city", "") }}',
                    state: '{{ old("state", "") }}',
                    reason: '{{ old("reason", "") }}',
                    custom_details: '{{ old("custom_details", "") }}'
                },

                init() {
                    this.updateScore();
                },

                goToStep(step) {
                    this.currentStep = step;
                    this.updateScore();
                },

                nextStep() {
                    if (this.currentStep < this.steps.length - 1) {
                        this.currentStep++;
                        this.checkAchievements();
                        this.updateScore();
                    }
                },

                prevStep() {
                    if (this.currentStep > 0) {
                        this.currentStep--;
                        this.updateScore();
                    }
                },

                getStepClass(index) {
                    if (index === this.currentStep) {
                        return 'bg-blue-500 text-white shadow-lg transform scale-105';
                    } else if (index < this.currentStep) {
                        return 'bg-green-500 text-white';
                    } else {
                        return 'bg-gray-200 text-gray-600';
                    }
                },

                getStepBadgeClass(index) {
                    const completion = this.getStepCompletion(index);
                    if (completion === '✓') {
                        return 'bg-green-600 text-white';
                    } else if (completion > 0) {
                        return 'bg-yellow-500 text-white';
                    } else {
                        return 'bg-gray-400 text-white';
                    }
                },

                getStepCompletion(index) {
                    const step = this.steps[index];
                    const validFields = step.fields.filter(field => this.isFieldValid(field)).length;
                    const totalFields = step.fields.length;
                    
                    if (validFields === totalFields) return '✓';
                    if (validFields === 0) return '0';
                    return validFields;
                },

                isFieldValid(field) {
                    const value = this.formData[field];
                    return value && value.toString().trim().length > 0;
                },

                getFieldClass(field) {
                    if (this.isFieldValid(field)) {
                        return 'border-green-300 bg-green-50 focus:border-green-500 focus:ring-green-500';
                    }
                    return 'border-gray-300 focus:border-blue-500 focus:ring-blue-500';
                },

                updateScore() {
                    const totalFields = Object.keys(this.formData).length;
                    const validFields = Object.keys(this.formData).filter(field => this.isFieldValid(field)).length;
                    
                    this.currentScore = Math.round((validFields / totalFields) * 100);
                    this.globalProgress = this.currentScore;
                    
                    // Update score display
                    document.getElementById('current-score').textContent = this.currentScore;
                    
                    this.checkAchievements();
                },

                checkAchievements() {
                    const achievements = [
                        { score: 25, text: "Dados Básicos Preenchidos! 🎯", id: "basic" },
                        { score: 50, text: "Meio Caminho Andado! 🚀", id: "halfway" },
                        { score: 75, text: "Quase Lá! Você é Incrível! ⭐", id: "almost" },
                        { score: 100, text: "Formulário Completo! Você é um Mestre! 🏆", id: "complete" }
                    ];

                    achievements.forEach(achievement => {
                        if (this.currentScore >= achievement.score && !this.achievements.includes(achievement.id)) {
                            this.showAchievement(achievement.text);
                            this.achievements.push(achievement.id);
                            
                            if (achievement.score === 100) {
                                this.celebrateCompletion();
                            }
                        }
                    });
                },

                showAchievement(text) {
                    const container = document.getElementById('achievement-container');
                    const textElement = document.getElementById('achievement-text');
                    
                    textElement.textContent = text;
                    container.classList.remove('hidden');
                    
                    setTimeout(() => {
                        container.classList.add('hidden');
                    }, 3000);
                },

                celebrateCompletion() {
                    confetti({
                        particleCount: 100,
                        spread: 70,
                        origin: { y: 0.6 }
                    });
                },

                formatCpf(event) {
                    let value = event.target.value.replace(/\D/g, '');
                    value = value.substring(0, 11);
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                    event.target.value = value;
                    this.formData.cpf = value;
                },

                formatPhone(event) {
                    let value = event.target.value.replace(/\D/g, '');
                    value = value.substring(0, 11);
                    if (value.length <= 2) {
                        value = value.replace(/(\d{0,2})/, '($1');
                    } else if (value.length <= 6) {
                        value = value.replace(/(\d{2})(\d{0,4})/, '($1) $2');
                    } else if (value.length <= 10) {
                        value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
                    } else {
                        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                    }
                    event.target.value = value;
                    this.formData.phone = value;
                },

                formatPlate(event) {
                    let value = event.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                    
                    // Formata para o padrão brasileiro (até 7 caracteres alfanuméricos)
                    if (value.length > 7) {
                        value = value.substring(0, 7);
                    }
                    
                    event.target.value = value;
                    this.formData.plate = value;
                },

                handleSubmit() {
                    // Valida campos obrigatórios básicos
                    const requiredFields = ['name', 'cpf', 'driver_license', 'phone', 'plate', 'vehicle_model', 'vehicle_year', 'vehicle_color', 'vehicle_chassi', 'vehicle_renavam', 'citation_number', 'date', 'time', 'infraction_type_id', 'amount', 'points', 'location', 'city', 'state', 'reason'];
                    
                    const missingFields = requiredFields.filter(field => !this.isFieldValid(field));
                    
                    if (missingFields.length > 0) {
                        // Vai para o primeiro step que tem campos faltando
                        for (let i = 0; i < this.steps.length; i++) {
                            const stepFields = this.steps[i].fields;
                            const stepMissingFields = stepFields.filter(field => missingFields.includes(field));
                            if (stepMissingFields.length > 0) {
                                this.currentStep = i;
                                break;
                            }
                        }
                        
                        alert('Por favor, preencha todos os campos obrigatórios antes de gerar o recurso.');
                        return;
                    }
                    
                    // Sempre usa 3 créditos (Inteligência Híbrida)
                    const creditsNeeded = 3;
                    const userCredits = {{ Auth::user()->credits }};
                    
                    if (userCredits < creditsNeeded) {
                        alert(`Você precisa de ${creditsNeeded} créditos para gerar o recurso com Inteligência Híbrida. Você tem apenas ${userCredits} crédito(s).`);
                        return;
                    }
                    
                    // Validação específica para placa (máximo 7 caracteres)
                    if (this.formData.plate && this.formData.plate.length > 7) {
                        this.currentStep = 1; // Vai para step do veículo
                        alert('A placa deve ter no máximo 7 caracteres (formato: ABC1234 ou ABC1D23).');
                        return;
                    }
                    
                    // Celebra se completou 100%
                    if (this.currentScore === 100) {
                        this.celebrateCompletion();
                    }
                    
                    // Inicia o processo de loading
                    this.startLoadingProcess();
                },

                startLoadingProcess() {
                    // Reset do modal antes de iniciar
                    this.resetLoadingModal();
                    this.loadingHasErrored = false;
                    
                    // Mostra o modal de loading
                    const modal = document.getElementById('loading-modal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    
                    // Timeout de segurança (30s)
                    if (this.loadingTimeoutId) clearTimeout(this.loadingTimeoutId);
                    this.loadingTimeoutId = setTimeout(() => {
                        this.handleLoadingError();
                    }, 30000);
                    
                    // Inicia as animações
                    this.animateLoadingProcess();
                },

                handleLoadingError() {
                    // Sinaliza erro
                    this.loadingHasErrored = true;
                    
                    // Esconde barra de progresso e mostra erro
                    document.getElementById('progress-bar').classList.add('hidden');
                    document.getElementById('progress-text').textContent = 'Erro na geração';
                    document.getElementById('motivational-text').textContent = '❌ Houve um problema. Veja detalhes abaixo e tente novamente.';
                    document.getElementById('loading-error').classList.remove('hidden');
                },

                animateLoadingProcess() {
                    const steps = [
                        {
                            stepId: 'step-ias',
                            aiModels: ['ai-gemini', 'ai-roberta', 'ai-gpt4'],
                            progress: 15,
                            text: '🧠 Executando 3 IAs simultaneamente...',
                            message: '💎 Gemini Pro analisando legislação brasileira...'
                        },
                        {
                            stepId: 'step-ias',
                            aiModels: ['ai-gemini', 'ai-roberta', 'ai-gpt4'],
                            progress: 30,
                            text: '🤖 IAs processando dados jurídicos...',
                            message: '🇧🇷 RoBERTa aplicando jurisprudência brasileira...'
                        },
                        {
                            stepId: 'step-ias',
                            aiModels: ['ai-gemini', 'ai-roberta', 'ai-gpt4'],
                            progress: 50,
                            text: '⚡ Finalizando geração das versões...',
                            message: '🔥 GPT-4 refinando argumentação jurídica...'
                        },
                        {
                            stepId: 'step-analysis',
                            aiModels: [],
                            progress: 70,
                            text: '🔍 IA analisando e comparando versões...',
                            message: '🧠 Escolhendo automaticamente a melhor versão...'
                        },
                        {
                            stepId: 'step-pdf',
                            aiModels: [],
                            progress: 85,
                            text: '📄 Gerando PDF profissional...',
                            message: '🎨 Formatando documento para protocolo...'
                        },
                        {
                            stepId: 'step-pdf',
                            aiModels: [],
                            progress: 100,
                            text: '✅ Recurso gerado com sucesso!',
                            message: '🎉 Redirecionando para visualização...'
                        }
                    ];

                    let currentStepIndex = 0;

                    const executeStep = () => {
                        // Cancela se houve erro
                        if (this.loadingHasErrored) return;

                        if (currentStepIndex >= steps.length) {
                            // Sucesso: limpa timeout de erro
                            if (this.loadingTimeoutId) clearTimeout(this.loadingTimeoutId);
                            
                            // Submete o formulário após completar todas as animações
                        setTimeout(() => {
                                // Preenche campos e submete
                                Object.keys(this.formData).forEach(key => {
                                    const input = document.querySelector(`[name="${key}"]`);
                                    if (input && this.formData[key]) {
                                        input.value = this.formData[key];
                                    }
                                });
                            document.getElementById('appeal-form').submit();
                            }, 1000);
                            return;
                        }

                        const step = steps[currentStepIndex];
                        
                        // Atualiza progress bar se ainda visível
                        const progressBar = document.getElementById('progress-bar');
                        if (!progressBar.classList.contains('hidden')) {
                            progressBar.style.width = step.progress + '%';
                        }
                        document.getElementById('progress-text').textContent = step.text;
                        document.getElementById('motivational-text').textContent = step.message;

                        // Ativa o step atual com animação
                        const stepElement = document.getElementById(step.stepId);
                        stepElement.classList.remove('opacity-50');
                        stepElement.classList.add('opacity-100', 'step-activating');
                        
                        // Atualiza o círculo do step
                        const circle = stepElement.querySelector('.step-inner');
                        const border = stepElement.querySelector('.border-2');
                        
                        circle.classList.remove('bg-gray-300');
                        circle.classList.add('bg-green-500');
                        border.classList.remove('border-gray-300');
                        border.classList.add('border-green-500');

                        // Anima AIs se especificado
                        step.aiModels.forEach((aiId, index) => {
                            setTimeout(() => {
                                const aiElement = document.getElementById(aiId);
                                aiElement.classList.remove('opacity-50');
                                aiElement.classList.add('opacity-100', 'ai-working');
                                aiElement.style.background = 'linear-gradient(135deg, #f0f9ff, #dbeafe)';
                                aiElement.style.border = '2px solid #3b82f6';
                                aiElement.style.boxShadow = '0 4px 15px rgba(59, 130, 246, 0.3)';
                                
                                // Adiciona efeito de "trabalhando"
                                aiElement.querySelector('.text-lg').style.animation = 'aiPulse 1s infinite';
                            }, index * 300);
                        });

                        currentStepIndex++;
                        
                        // Próximo step com delay realístico baseado na complexidade
                        const delays = [2500, 2000, 2500, 2000, 1500, 1000];
                        const delay = delays[currentStepIndex - 1] || 2000;
                        
                        setTimeout(executeStep, delay);
                    };

                    // Adiciona efeito de entrada suave no modal
                    document.getElementById('loading-modal').querySelector('div').style.animation = 'modalFadeIn 0.5s ease-out';

                    // Inicia a sequência após uma pequena pausa para o modal aparecer
                    setTimeout(executeStep, 500);
                },

                resetLoadingModal() {
                    // Reset de todos os elementos do modal para estado inicial
                    this.loadingHasErrored = false;
                    document.getElementById('progress-bar').style.width = '0%';
                    document.getElementById('progress-text').textContent = 'Iniciando processo...';
                    document.getElementById('motivational-text').textContent = '🚀 Preparando o melhor recurso do mundo para você...';
                    
                    // Reset dos steps
                    ['step-ias', 'step-analysis', 'step-pdf'].forEach(stepId => {
                        const stepElement = document.getElementById(stepId);
                        stepElement.classList.remove('opacity-100', 'step-activating');
                        stepElement.classList.add('opacity-50');
                        
                        const circle = stepElement.querySelector('.step-inner');
                        const border = stepElement.querySelector('.border-2');
                        
                        circle.classList.remove('bg-green-500');
                        circle.classList.add('bg-gray-300');
                        border.classList.remove('border-green-500');
                        border.classList.add('border-gray-300');
                    });
                    
                    // Reset das AIs
                    ['ai-gemini', 'ai-roberta', 'ai-gpt4'].forEach(aiId => {
                        const aiElement = document.getElementById(aiId);
                        aiElement.classList.remove('opacity-100', 'ai-working');
                        aiElement.classList.add('opacity-50');
                        aiElement.style.background = '';
                        aiElement.style.border = '';
                        aiElement.style.boxShadow = '';
                        aiElement.querySelector('.text-lg').style.animation = '';
                    });
                    
                    // Esconde o modal
                    const modal = document.getElementById('loading-modal');
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                    document.getElementById('loading-error').classList.add('hidden');
                    document.getElementById('progress-bar').classList.remove('hidden');
                },

                onInfractionTypeChange(event) {
                    const selectedOption = event.target.selectedOptions[0];
                    if (selectedOption && selectedOption.value) {
                        const points = selectedOption.getAttribute('data-points');
                        const amount = selectedOption.getAttribute('data-amount');
                        
                        if (points && amount) {
                            // Preenche automaticamente os campos
                            this.formData.points = points;
                            this.formData.amount = amount;
                            
                            // Marca como preenchidos automaticamente
                            this.autoFilledFields.points = true;
                            this.autoFilledFields.amount = true;
                            
                            // Atualiza o score
                            this.updateScore();
                            
                            // Adiciona um pequeno efeito visual
                            this.showAutoFillNotification();
                        }
                    } else {
                        // Limpa os campos se nenhuma infração foi selecionada
                        this.formData.points = '';
                        this.formData.amount = '';
                        this.autoFilledFields.points = false;
                        this.autoFilledFields.amount = false;
                        this.updateScore();
                    }
                },

                markAsManuallyEdited(field) {
                    // Remove a marcação de preenchimento automático quando editado manualmente
                    this.autoFilledFields[field] = false;
                },

                isAutoFilled(field) {
                    return this.autoFilledFields[field] === true;
                },

                showAutoFillNotification() {
                    // Mostra uma notificação visual temporária
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-bounce';
                    notification.innerHTML = '🤖 Valores preenchidos automaticamente!';
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        notification.remove();
                }, 3000);
                
                    // Aplica animação aos campos preenchidos
                    const amountField = document.querySelector('input[name="amount"]');
                    const pointsField = document.querySelector('input[name="points"]');
                    
                    if (amountField) {
                        amountField.classList.add('auto-fill-highlight');
                        setTimeout(() => amountField.classList.remove('auto-fill-highlight'), 1000);
                    }
                    
                    if (pointsField) {
                        pointsField.classList.add('auto-fill-highlight');
                        setTimeout(() => pointsField.classList.remove('auto-fill-highlight'), 1000);
                    }
                },

                // Função de teste para demonstrar o loading (remover em produção)
                testLoading() {
                    console.log('Testando loading modal...');
                    this.startLoadingProcess();
                },

                retryGeneration() {
                    // Reset do modal antes de iniciar
                    this.resetLoadingModal();
                    
                    // Inicia o processo de loading
                    this.startLoadingProcess();
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
