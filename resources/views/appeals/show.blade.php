<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes do Recurso') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('appeals.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Voltar
                </a>
                @if($appeal->status == 'pending')
                    <a href="{{ route('appeals.edit', $appeal->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Editar Status
                    </a>
                @endif
                <a href="{{ route('appeals.download', $appeal->id) }}" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Baixar PDF
                </a>
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
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Recurso #{{ $appeal->id }}</h3>
                            <p class="text-sm text-gray-600">Criado em {{ $appeal->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <div class="mb-2">
                                @if($appeal->status == 'pending')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Pendente</span>
                                @elseif($appeal->status == 'sent')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Enviado</span>
                                @elseif($appeal->status == 'successful')
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Deferido</span>
                                @elseif($appeal->status == 'rejected')
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">Indeferido</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t pt-4">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Dados da Multa</h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-500">Placa do Veículo</div>
                                <div class="mt-1 text-md">{{ $appeal->ticket->plate }}</div>
                            </div>

                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-500">Data da Multa</div>
                                <div class="mt-1 text-md">{{ $appeal->ticket->date->format('d/m/Y') }}</div>
                            </div>

                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-500">Valor da Multa</div>
                                <div class="mt-1 text-md font-semibold text-red-600">R$ {{ number_format($appeal->ticket->amount, 2, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="text-sm font-medium text-gray-500">Local da Infração</div>
                            <div class="mt-1 text-md">{{ $appeal->ticket->location }}</div>
                        </div>

                        <div class="mb-4">
                            <div class="text-sm font-medium text-gray-500">Motivo da Autuação</div>
                            <div class="mt-1 text-md">{{ $appeal->ticket->reason }}</div>
                        </div>

                        @if($appeal->notes)
                            <div class="mt-6 border-t pt-4">
                                <h4 class="text-md font-medium text-gray-900 mb-3">Anotações</h4>
                                <div class="bg-gray-50 p-4 rounded-md">
                                    <p class="text-md">{{ $appeal->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 border-t pt-4">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Texto do Recurso</h4>
                        <div class="bg-gray-50 p-4 rounded-md whitespace-pre-line">
                            {{ $appeal->generated_text }}
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <a href="{{ route('tickets.show', $appeal->ticket->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Ver Multa
                        </a>
                        <a href="{{ route('appeals.download', $appeal->id) }}" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Baixar PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
