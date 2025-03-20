<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes da Multa') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Voltar
                </a>
                <a href="{{ route('tickets.edit', $ticket->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Editar
                </a>
                @if($ticket->appeals->count() == 0)
                    <a href="{{ route('appeals.create', ['ticket_id' => $ticket->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Gerar Recurso
                    </a>
                @endif
            </div>
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informações Básicas -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações da Multa</h3>

                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-500">Placa do Veículo</div>
                                <div class="mt-1 text-lg">{{ $ticket->plate }}</div>
                            </div>

                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-500">Data da Multa</div>
                                <div class="mt-1 text-lg">{{ $ticket->date->format('d/m/Y') }}</div>
                            </div>

                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-500">Local da Infração</div>
                                <div class="mt-1 text-lg">{{ $ticket->location }}</div>
                            </div>

                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-500">Valor da Multa</div>
                                <div class="mt-1 text-lg font-semibold text-red-600">R$ {{ number_format($ticket->amount, 2, ',', '.') }}</div>
                            </div>

                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-500">Motivo da Autuação</div>
                                <div class="mt-1 text-lg">{{ $ticket->reason }}</div>
                            </div>
                        </div>

                        <!-- Informações Complementares -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Complementares</h3>

                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-500">Número da Autuação</div>
                                <div class="mt-1 text-lg">{{ $ticket->citation_number ?: 'Não informado' }}</div>
                            </div>

                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-500">Modelo do Veículo</div>
                                <div class="mt-1 text-lg">{{ $ticket->vehicle_model ?: 'Não informado' }}</div>
                            </div>

                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-500">Ano do Veículo</div>
                                <div class="mt-1 text-lg">{{ $ticket->vehicle_year ?: 'Não informado' }}</div>
                            </div>

                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-500">CNH do Motorista</div>
                                <div class="mt-1 text-lg">{{ $ticket->driver_license ?: 'Não informado' }}</div>
                            </div>

                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-500">Data de Cadastro</div>
                                <div class="mt-1 text-lg">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recursos Relacionados -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recursos Gerados</h3>

                    @if($ticket->appeals->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b">
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">ID</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Data de Geração</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Status</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ticket->appeals as $appeal)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="p-3 text-sm">#{{ $appeal->id }}</td>
                                            <td class="p-3 text-sm">{{ $appeal->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="p-3 text-sm">
                                                @if($appeal->status == 'pending')
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pendente</span>
                                                @elseif($appeal->status == 'sent')
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Enviado</span>
                                                @elseif($appeal->status == 'successful')
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Deferido</span>
                                                @elseif($appeal->status == 'rejected')
                                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Indeferido</span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-sm">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('appeals.show', $appeal->id) }}" class="text-blue-600 hover:text-blue-900" title="Visualizar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('appeals.download', $appeal->id) }}" class="text-green-600 hover:text-green-900" title="Baixar PDF">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-gray-500 text-center py-6">
                            <p class="mb-2">Nenhum recurso foi gerado para esta multa ainda.</p>
                            <a href="{{ route('appeals.create', ['ticket_id' => $ticket->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Gerar Recurso Agora
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
