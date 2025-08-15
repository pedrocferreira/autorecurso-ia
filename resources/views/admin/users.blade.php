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

            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg p-4 mb-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Buscar por nome ou email" class="w-full px-3 py-2 border rounded">
                    <select name="subscription" class="w-full px-3 py-2 border rounded">
                        <option value="">Assinatura</option>
                        <option value="active" @selected(($filters['subscription'] ?? '')==='active')>Ativa</option>
                        <option value="inactive" @selected(($filters['subscription'] ?? '')==='inactive')>Inativa</option>
                    </select>
                    <select name="blocked" class="w-full px-3 py-2 border rounded">
                        <option value="">Bloqueio</option>
                        <option value="yes" @selected(($filters['blocked'] ?? '')==='yes')>Bloqueado</option>
                        <option value="no" @selected(($filters['blocked'] ?? '')==='no')>Ativo</option>
                    </select>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded">Filtrar</button>
                    <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-gray-200 rounded text-center">Limpar</a>
                </form>
            </div>

            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Assinatura</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @if($user->subscription_active || ($user->subscription_ends_at && $user->subscription_ends_at->isFuture()))
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Ativa</span>
                                        @if($user->subscription_ends_at)
                                            <div class="text-xs text-gray-500 mt-1">até {{ $user->subscription_ends_at->format('d/m/Y') }}</div>
                                        @endif
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Inativa</span>
                                    @endif
                                </td>
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
                                        <a href="{{ route('admin.users.edit', $user) }}#subscription" class="inline-flex items-center px-3 py-1 bg-purple-600 text-white text-xs font-semibold rounded hover:bg-purple-700">Gerir Assinatura</a>
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