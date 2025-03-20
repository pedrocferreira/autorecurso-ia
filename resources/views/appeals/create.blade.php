<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gerar Novo Recurso') }}
        </h2>
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
                    <div class="mb-6">
                        <p class="text-lg text-gray-700">Selecione uma multa para gerar um recurso automaticamente utilizando Inteligência Artificial. Nosso sistema irá criar um documento formal com argumentos legais para contestar a autuação.</p>
                    </div>

                    <form action="{{ route('appeals.store') }}" method="POST">
                        @csrf

                        <!-- Seleção de Multa -->
                        <div class="mb-6">
                            <label for="ticket_id" class="block text-sm font-medium text-gray-700 mb-1">Selecione uma Multa</label>

                            @if(isset($ticket))
                                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                <div class="p-4 bg-gray-50 rounded-md border border-gray-200">
                                    <div class="flex justify-between">
                                        <div>
                                            <p class="font-semibold">{{ $ticket->plate }} - {{ $ticket->date->format('d/m/Y') }}</p>
                                            <p class="text-sm text-gray-600">{{ Str::limit($ticket->reason, 80) }}</p>
                                            <p class="text-sm text-gray-600 mt-1">Local: {{ Str::limit($ticket->location, 50) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-red-600">R$ {{ number_format($ticket->amount, 2, ',', '.') }}</p>
                                            <a href="{{ route('tickets.show', $ticket->id) }}" class="text-blue-600 hover:underline text-sm">Ver detalhes</a>
                                        </div>
                                    </div>
                                </div>
                            @elseif(isset($tickets) && $tickets->count() > 0)
                                <select name="ticket_id" id="ticket_id" required
                                    class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('ticket_id') border-red-500 @enderror">
                                    <option value="">Selecione uma multa...</option>
                                    @foreach($tickets as $ticket)
                                        <option value="{{ $ticket->id }}" {{ old('ticket_id') == $ticket->id ? 'selected' : '' }}>
                                            {{ $ticket->plate }} - {{ $ticket->date->format('d/m/Y') }} - R$ {{ number_format($ticket->amount, 2, ',', '.') }} - {{ Str::limit($ticket->reason, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ticket_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('ticket_id') }}</p>
                                @enderror
                            @else
                                <div class="text-center py-6 bg-gray-50 rounded-md border border-gray-200">
                                    <p class="text-gray-600 mb-2">Você ainda não possui multas cadastradas para gerar um recurso.</p>
                                    <a href="{{ route('tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Cadastrar Nova Multa
                                    </a>
                                </div>
                            @endif
                        </div>

                        @if(isset($ticket) || (isset($tickets) && $tickets->count() > 0))
                            <div class="mb-6">
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-700">
                                                A geração do recurso pode levar alguns instantes. O sistema utilizará os dados da multa para criar um recurso personalizado.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <a href="{{ route('tickets.index') }}" class="text-gray-600 hover:text-gray-900">
                                    Cancelar
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Gerar Recurso
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
