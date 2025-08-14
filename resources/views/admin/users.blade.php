@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Créditos</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">{{ $user->credits }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @if($user->blocked)
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Bloqueado</span>
                                    @else
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Ativo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <div class="flex items-center justify-center gap-2 flex-wrap">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Editar</a>
                                        <form action="{{ route('admin.users.toggle_block', $user) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700">
                                                {{ $user->blocked ? 'Desbloquear' : 'Bloquear' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.credit', $user) }}" method="POST" class="inline-flex items-center gap-2">
                                            @csrf
                                            <input type="number" name="amount" class="w-24 px-2 py-1 border rounded text-xs" placeholder="+/- créditos" required>
                                            <input type="text" name="reason" class="w-40 px-2 py-1 border rounded text-xs" placeholder="Motivo" required>
                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-semibold rounded hover:bg-green-700">Ajustar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
            </div>
        </div>
    </div>
</div>
@endsection 