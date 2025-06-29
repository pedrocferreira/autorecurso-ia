<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">
            Criar conta
        </h2>
        <p class="text-gray-600 text-sm">
            Junte-se ao AutoRecurso gratuitamente
        </p>
    </div>

    <!-- Google Sign Up -->
    <div class="mb-6">
        <a href="{{ route('auth.google') }}" class="w-full flex justify-center items-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
            <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Criar conta com Google
        </a>
    </div>

    <!-- Divider -->
    <div class="relative mb-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">ou</span>
        </div>
    </div>

    <!-- Register Form -->
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nome completo')" class="text-sm font-medium text-gray-700" />
            <x-text-input id="name" class="block mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Seu nome completo" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700" />
            <x-text-input id="email" class="block mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="seu@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Senha')" class="text-sm font-medium text-gray-700" />
            <x-text-input id="password" class="block mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" type="password" name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmar senha')" class="text-sm font-medium text-gray-700" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirme sua senha" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- LGPD Consent Section -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-3">
            <h4 class="text-sm font-semibold text-blue-800">Consentimento para Tratamento de Dados (LGPD)</h4>
            
            <!-- Terms and Privacy Policy -->
            <div>
                <label class="flex items-start">
                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 mt-1" name="terms" required>
                    <span class="ml-2 text-sm text-gray-700">
                        Eu li e concordo com os 
                        <a href="{{ route('legal.terms') }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium underline">Termos de Serviço</a> 
                        e a 
                        <a href="{{ route('legal.privacy') }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium underline">Política de Privacidade</a>
                        <span class="text-red-500">*</span>
                    </span>
                </label>
                <x-input-error :messages="$errors->get('terms')" class="mt-2" />
            </div>

            <!-- Data Processing Consent -->
            <div>
                <label class="flex items-start">
                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 mt-1" name="data_processing_consent" required>
                    <span class="ml-2 text-sm text-gray-700">
                        Autorizo o tratamento dos meus dados pessoais para prestação dos serviços conforme descrito na Política de Privacidade
                        <span class="text-red-500">*</span>
                    </span>
                </label>
                <x-input-error :messages="$errors->get('data_processing_consent')" class="mt-2" />
            </div>

            <!-- Marketing Consent (Optional) -->
            <div>
                <label class="flex items-start">
                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 mt-1" name="marketing_consent">
                    <span class="ml-2 text-sm text-gray-700">
                        Desejo receber comunicações comerciais, ofertas e novidades por e-mail (opcional)
                    </span>
                </label>
            </div>

            <!-- Cookie Consent -->
            <div>
                <label class="flex items-start">
                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 mt-1" name="cookie_consent" required>
                    <span class="ml-2 text-sm text-gray-700">
                        Aceito o uso de cookies conforme descrito na 
                        <a href="{{ route('legal.cookies') }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium underline">Política de Cookies</a>
                        <span class="text-red-500">*</span>
                    </span>
                </label>
                <x-input-error :messages="$errors->get('cookie_consent')" class="mt-2" />
            </div>
        </div>

        <!-- Age Confirmation -->
        <div>
            <label class="flex items-start">
                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 mt-1" name="age_confirmation" required>
                <span class="ml-2 text-sm text-gray-700">
                    Declaro que sou maior de 18 anos
                    <span class="text-red-500">*</span>
                </span>
            </label>
            <x-input-error :messages="$errors->get('age_confirmation')" class="mt-2" />
        </div>

        <!-- Register Button -->
        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-2.5 bg-blue-600 hover:bg-blue-700 focus:ring-blue-500">
                {{ __('Criar conta') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Login Link -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            Já tem uma conta? 
            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-800">
                Fazer login
            </a>
        </p>
    </div>
</x-guest-layout>
