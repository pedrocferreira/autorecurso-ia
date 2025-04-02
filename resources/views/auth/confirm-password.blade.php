<x-guest-layout>
    <div class="flex flex-col items-center">
        <div class="mb-8 text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center shadow-lg mb-4">
                <i class="fas fa-gavel text-white text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-blue-600">Confirmar <span class="text-blue-800">Senha</span></h1>
            <p class="text-gray-600 mt-2 text-lg">Por favor, confirme sua senha para continuar</p>
        </div>

        <div class="w-full space-y-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-xl shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Área Segura</h3>
                        <p class="mt-1 text-sm text-blue-700">
                            Esta é uma área segura da aplicação. Por favor, confirme sua senha antes de continuar.
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                @csrf

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Senha')" class="text-gray-700" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-primary-button class="w-full justify-center py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 transition duration-300">
                        <i class="fas fa-check mr-2"></i>
                        {{ __('Confirmar') }}
                    </x-primary-button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Esqueceu sua senha?</span>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('password.request') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition duration-300">
                        <i class="fas fa-key mr-2"></i>
                        Recuperar Senha
                    </a>
                </div>
            </div>

            <div class="mt-6 text-xs text-gray-500 text-center bg-gray-50 p-4 rounded-lg">
                <p class="mb-1">Esta confirmação é necessária para acessar áreas sensíveis do sistema.</p>
                <p>Se você não solicitou esta confirmação, por favor, ignore esta mensagem.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
