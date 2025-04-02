<x-guest-layout>
    <div class="flex flex-col items-center">
        <div class="mb-8 text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center shadow-lg mb-4">
                <i class="fas fa-gavel text-white text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-blue-600">Verificar <span class="text-blue-800">Email</span></h1>
            <p class="text-gray-600 mt-2 text-lg">Confirme seu endereço de email para continuar</p>
        </div>

        <div class="w-full space-y-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-xl shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-envelope-open-text text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Verificação de Email</h3>
                        <p class="mt-1 text-sm text-blue-700">
                            Obrigado por se cadastrar! Antes de começar, por favor, verifique seu endereço de email clicando no link que enviamos.
                        </p>
                    </div>
                </div>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                Um novo link de verificação foi enviado para o endereço de email fornecido durante o registro.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex flex-col space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-primary-button class="w-full justify-center py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 transition duration-300">
                        <i class="fas fa-paper-plane mr-2"></i>
                        {{ __('Reenviar Link de Verificação') }}
                    </x-primary-button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Sair') }}
                    </button>
                </form>
            </div>

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
                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition duration-300">
                        <i class="fas fa-headset mr-2"></i>
                        Contatar Suporte
                    </a>
                </div>
            </div>

            <div class="mt-6 text-xs text-gray-500 text-center bg-gray-50 p-4 rounded-lg">
                <p class="mb-1">Se você não recebeu o email de verificação, verifique sua pasta de spam.</p>
                <p>Se o problema persistir, entre em contato com nosso suporte.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
