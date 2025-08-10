<x-guest-layout>
    <div class="flex flex-col items-center">
        <div class="mb-4 text-center">
            <h1 class="text-2xl font-bold text-blue-600">Crie sua conta <span class="text-blue-800">Auto</span>Recurso</h1>
            <p class="text-gray-600 mt-2">Comece a gerar recursos de multas em minutos</p>
        </div>

        <div class="mt-2 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg text-sm text-center mb-4 max-w-sm">
            <p class="text-blue-800 dark:text-blue-200">
                <i class="fas fa-shield-alt mr-1"></i> Registro rápido e seguro. Suas informações estão protegidas.
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nome')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label class="inline-flex items-center">
                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="terms" required>
                <span class="ml-2 text-sm text-gray-600">Eu concordo com os <a href="#" class="text-blue-600 hover:underline">Termos de Serviço</a> e <a href="#" class="text-blue-600 hover:underline">Política de Privacidade</a></span>
            </label>
            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('login') }}">
                {{ __('Já tem uma conta?') }}
            </a>

            <x-primary-button class="ml-4 bg-blue-600 hover:bg-blue-700">
                {{ __('Registrar') }}
            </x-primary-button>
        </div>

        <div class="mt-6 text-xs text-gray-500 text-center">
            <p>Ao se registrar, você receberá atualizações e promoções por email.</p>
            <p>Você pode cancelar sua inscrição a qualquer momento.</p>
        </div>
    </form>
</x-guest-layout>
