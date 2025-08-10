@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="mb-4 flex items-center space-x-4">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }}>
                            <span>Administrador</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="blocked" value="1" {{ $user->blocked ? 'checked' : '' }}>
                            <span>Bloqueado</span>
                        </label>
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Salvar</button>
                    <a href="{{ route('admin.users') }}" class="ml-3 text-gray-600 hover:underline">Cancelar</a>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Ajustar Créditos</h3>
                <form action="{{ route('admin.users.credit', $user) }}" method="POST" class="flex space-x-3">
                    @csrf
                    <input type="number" name="amount" placeholder="Ex: 10 ou -5" class="border rounded px-3 py-2 w-full" required>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Aplicar</button>
                </form>
                <p class="text-xs text-gray-500 mt-2">Use valor positivo para adicionar e negativo para remover créditos.</p>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection 