<x-guest-layout>
    <div class="flex flex-col items-center">
        <div class="mb-8 text-center">

            <h1 class="text-3xl font-bold text-blue-600">Bem-vindo ao <span class="text-blue-800">Auto</span>Recurso</h1>
            <p class="text-gray-600 mt-2 text-lg">Sua solução para recursos de multas automatizados</p>
        </div>

        <div class="w-full space-y-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-xl shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Conteste multas com facilidade</h3>
                        <p class="mt-1 text-sm text-blue-700">
                            Faça login para acessar sua conta e começar a gerar recursos automaticamente.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6" autocomplete="on">
                @csrf

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
                    <x-text-input 
                        id="email" 
                        class="block mt-1 w-full" 
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        required 
                        autofocus 
                        autocomplete="email"
                        spellcheck="false"
                        inputmode="email"
                        placeholder="seu@email.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Senha')" class="text-gray-700" />
                    <x-text-input 
                        id="password" 
                        class="block mt-1 w-full" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        minlength="8"
                        placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" 
                            name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Lembrar-me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-blue-600 hover:text-blue-500" href="{{ route('password.request') }}">
                            {{ __('Esqueceu sua senha?') }}
                        </a>
                    @endif
                </div>

                <div>
                    <x-primary-button 
                        type="submit"
                        class="w-full justify-center py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 transition duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        {{ __('Entrar') }}
                    </x-primary-button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Ainda não tem uma conta?</span>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-blue-700 text-base font-bold rounded-lg text-white hover:bg-blue-800 shadow-md hover:shadow-lg transition duration-300">
                        <i class="fas fa-user-plus mr-3 text-lg"></i>
                        Registre-se gratuitamente
                    </a>
                </div>
            </div>

            <div class="mt-6 text-xs text-gray-500 text-center bg-gray-50 p-4 rounded-lg">
                <p class="mb-1">Ao fazer login, você concorda com nossos <a href="{{ route('terms') }}" class="text-blue-600 hover:underline">Termos de Serviço</a> e <a href="{{ route('privacy') }}" class="text-blue-600 hover:underline">Política de Privacidade</a>.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
