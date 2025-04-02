<x-guest-layout>
    <div class="flex flex-col items-center">
        <div class="mb-8 text-center">

            <h1 class="text-3xl font-bold text-blue-600">Crie sua conta <span class="text-blue-800">Auto</span>Recurso</h1>
            <p class="text-gray-600 mt-2 text-lg">Comece a gerar recursos de multas em minutos</p>
        </div>

        <div class="w-full space-y-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-xl shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Registro rápido e seguro</h3>
                        <p class="mt-1 text-sm text-blue-700">
                            Suas informações estão protegidas e você pode começar a usar o sistema imediatamente.
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div class="mt-4">
                    <x-input-label for="name" :value="__('Nome')" class="text-gray-700" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Seu nome completo" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="seu@email.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Senha')" class="text-gray-700" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                    <p class="mt-1 text-sm text-gray-500">A senha deve ter pelo menos 8 caracteres</p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" class="text-gray-700" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="terms" required>
                        <span class="ml-2 text-sm text-gray-600">
                            Eu concordo com os <a href="{{ route('terms') }}" class="text-blue-600 hover:underline">Termos de Serviço</a> e <a href="{{ route('privacy') }}" class="text-blue-600 hover:underline">Política de Privacidade</a>
                        </span>
                    </label>
                    <x-input-error :messages="$errors->get('terms')" class="mt-2" />
                </div>

                <div>
                    <x-primary-button class="w-full justify-center py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 transition duration-300">
                        <i class="fas fa-user-plus mr-2"></i>
                        {{ __('Criar Conta') }}
                    </x-primary-button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Já tem uma conta?</span>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-blue-700 text-base font-bold rounded-lg text-white hover:bg-blue-800 shadow-md hover:shadow-lg transition duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Fazer Login
                    </a>
                </div>
            </div>

            <div class="mt-6 text-xs text-gray-500 text-center bg-gray-50 p-4 rounded-lg">
                <p class="mb-1">Ao se registrar, você receberá atualizações e promoções por email.</p>
                <p>Você pode cancelar sua inscrição a qualquer momento.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
