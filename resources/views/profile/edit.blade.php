<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    {{-- Mensagem de aviso (warning) --}}
    @if (session('warning'))
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 shadow sm:rounded-lg" role="alert">
                    <p class="font-bold">Atenção</p>
                    <p>{{ session('warning') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Mensagem de sucesso --}}
    @if (session('success'))
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 shadow sm:rounded-lg" role="alert">
                    <p class="font-bold">Sucesso!</p>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Mensagem informativa --}}
    @if (session('info'))
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 shadow sm:rounded-lg" role="alert">
                    <p class="font-bold">Informação</p>
                    <p>{{ session('info') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
