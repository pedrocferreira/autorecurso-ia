<x-guest-layout>
    <div class="flex flex-col items-center">
        <div class="mb-4 text-center">
            <h1 class="text-2xl font-bold text-blue-600">Bem-vindo ao <span class="text-blue-800">Auto</span>Recurso</h1>
            <p class="text-gray-600 mt-2">Sua solução para recursos de multas automatizados</p>
        </div>

        <div class="mt-2 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg text-sm text-center mb-4 max-w-sm">
            <p class="text-blue-800 dark:text-blue-200">
                <i class="fas fa-info-circle mr-1"></i> Contestar multas nunca foi tão fácil! Faça login para acessar sua conta.
            </p>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('Lembrar-me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('password.request') }}">
                    {{ __('Esqueceu sua senha?') }}
                </a>
            @endif

            <x-primary-button class="ml-3 bg-blue-600 hover:bg-blue-700">
                {{ __('Entrar') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">Ainda não tem uma conta?</p>
        <a href="{{ url('/register') }}" class="inline-block mt-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition duration-300 text-sm">
            Registre-se gratuitamente
        </a>
    </div>
</x-guest-layout>
