<x-guest-layout>
    <div class="flex flex-col items-center">
        <div class="mb-8 text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center shadow-lg mb-4">
                <i class="fas fa-gavel text-white text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-blue-600">Redefinir <span class="text-blue-800">Senha</span></h1>
            <p class="text-gray-600 mt-2 text-lg">Crie uma nova senha segura para sua conta</p>
        </div>

        <div class="w-full space-y-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-xl shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-lock text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Nova senha</h3>
                        <p class="mt-1 text-sm text-blue-700">
                            Digite sua nova senha e confirme para finalizar a redefinição.
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="seu@email.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Nova Senha')" class="text-gray-700" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                    <p class="mt-1 text-sm text-gray-500">A senha deve ter pelo menos 8 caracteres</p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirmar Nova Senha')" class="text-gray-700" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div>
                    <x-primary-button class="w-full justify-center py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 transition duration-300">
                        <i class="fas fa-save mr-2"></i>
                        {{ __('Redefinir Senha') }}
                    </x-primary-button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Precisa de ajuda?</span>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-blue-700 text-base font-bold rounded-lg text-white hover:bg-blue-800 shadow-md hover:shadow-lg transition duration-300">
                        <i class="fas fa-headset mr-2"></i>
                        Contatar Suporte
                    </a>
                </div>
            </div>

            <div class="mt-6 text-xs text-gray-500 text-center bg-gray-50 p-4 rounded-lg">
                <p class="mb-1">Certifique-se de escolher uma senha forte e única.</p>
                <p>Recomendamos usar uma combinação de letras maiúsculas, minúsculas, números e caracteres especiais.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
