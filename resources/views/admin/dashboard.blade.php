@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Estatísticas rápidas -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Usuários</div>
                    <div class="text-2xl font-semibold">{{ $stats['users_count'] }}</div>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Multas</div>
                    <div class="text-2xl font-semibold">{{ $stats['tickets_count'] }}</div>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Recursos</div>
                    <div class="text-2xl font-semibold">{{ $stats['appeals_count'] }}</div>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Pendentes</div>
                    <div class="text-2xl font-semibold">{{ $stats['appeals_pending'] }}</div>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Enviados</div>
                    <div class="text-2xl font-semibold">{{ $stats['appeals_sent'] }}</div>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Aprovados</div>
                    <div class="text-2xl font-semibold">{{ $stats['appeals_successful'] }}</div>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <div class="text-sm text-gray-500">Rejeitados</div>
                    <div class="text-2xl font-semibold">{{ $stats['appeals_rejected'] }}</div>
                </div>
            </div>

            <!-- Top usuários -->
            <div class="bg-white shadow rounded p-6">
                <h3 class="text-lg font-semibold mb-4">Usuários mais ativos</h3>
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 text-xs text-gray-500 uppercase">
                            <th class="px-4 py-2 text-left">Nome</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-center">Recursos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @forelse($top_users as $u)
                            <tr>
                                <td class="px-4 py-2">{{ $u->name }}</td>
                                <td class="px-4 py-2">{{ $u->email }}</td>
                                <td class="px-4 py-2 text-center">{{ $u->appeals_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-center text-gray-500">Nenhum dado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Estatísticas mensais -->
            <div class="bg-white shadow rounded p-6">
                <h3 class="text-lg font-semibold mb-4">Recursos por mês ({{ date('Y') }})</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($monthly_stats as $stat)
                        <div class="border rounded p-4 text-center">
                            <div class="text-sm text-gray-500">{{ str_pad($stat->month, 2, '0', STR_PAD_LEFT) }}/{{ $stat->year }}</div>
                            <div class="text-xl font-semibold">{{ $stat->total }}</div>
                        </div>
                    @endforeach
                    @if($monthly_stats->isEmpty())
                        <p class="text-gray-500">Sem dados para o ano atual.</p>
                    @endif
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection 