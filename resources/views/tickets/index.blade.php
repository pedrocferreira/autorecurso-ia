<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Minhas Multas') }}
            </h2>
            <a href="{{ route('tickets.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Nova Multa
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagem de sucesso ou erro -->
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(count($tickets) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b">
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Placa</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Data</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Local</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Motivo</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Valor</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Recursos</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="p-3 text-sm">{{ $ticket->plate }}</td>
                                            <td class="p-3 text-sm">{{ $ticket->date->format('d/m/Y') }}</td>
                                            <td class="p-3 text-sm">{{ Str::limit($ticket->location, 30) }}</td>
                                            <td class="p-3 text-sm">{{ Str::limit($ticket->reason, 30) }}</td>
                                            <td class="p-3 text-sm">R$ {{ number_format($ticket->amount, 2, ',', '.') }}</td>
                                            <td class="p-3 text-sm">{{ $ticket->appeals->count() }}</td>
                                            <td class="p-3 text-sm">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-800">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('tickets.edit', $ticket) }}" class="text-yellow-600 hover:text-yellow-800">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if(!$ticket->appeals()->exists())
                                                        <a href="{{ route('appeals.create', $ticket) }}" class="text-green-600 hover:text-green-800" title="Gerar Recurso">
                                                            <i class="fas fa-file-alt"></i>
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Tem certeza que deseja excluir esta multa?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $tickets->links() }}
                        </div>
                    @else
                        <div class="text-gray-500 text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-lg">Nenhuma multa cadastrada ainda.</p>
                            <p class="mt-2">
                                <a href="{{ route('tickets.create') }}" class="text-blue-600 hover:underline">Cadastre sua primeira multa</a>
                                para gerar um recurso.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
