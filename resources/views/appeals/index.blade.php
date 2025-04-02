<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Meus Recursos') }}
            </h2>
            <div class="relative inline-block text-left">
                <button type="button" id="dropdown-button" class="inline-flex justify-center items-center space-x-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    <span>Novo Recurso</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="dropdown-menu" class="hidden absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10" role="menu" aria-orientation="vertical" aria-labelledby="dropdown-button">
                    <div class="py-1" role="none">
                        <a href="{{ route('tickets.index') }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">
                            <i class="fas fa-search mr-2 text-green-500"></i> Gerar de uma multa existente
                        </a>
                        <a href="{{ route('appeals.create_new') }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">
                            <i class="fas fa-plus mr-2 text-green-500"></i> Cadastrar nova multa e recurso
                        </a>
                    </div>
                </div>
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
                    @if(count($appeals) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b">
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">ID</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Multa</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Data</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Status</th>
                                        <th class="p-3 text-left text-xs font-medium text-gray-500">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appeals as $appeal)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="p-3 text-sm">#{{ $appeal->id }}</td>
                                            <td class="p-3 text-sm">{{ $appeal->ticket->plate }} - {{ $appeal->ticket->date->format('d/m/Y') }}</td>
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
                                                    @if($appeal->status == 'pending')
                                                        <a href="{{ route('appeals.edit', $appeal->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Atualizar Status">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                                        <button @click="open = !open" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                            <i class="fas fa-download mr-2"></i>
                                                            Baixar
                                                        </button>
                                                        <div x-show="open" 
                                                             @click.away="open = false"
                                                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                                                             x-cloak>
                                                            <a href="{{ route('appeals.download', ['appeal' => $appeal->id, 'format' => 'pdf']) }}" 
                                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                <i class="fas fa-file-pdf mr-2"></i> PDF
                                                            </a>
                                                            <a href="{{ route('appeals.download', ['appeal' => $appeal->id, 'format' => 'docx']) }}" 
                                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                <i class="fas fa-file-word mr-2"></i> Word (DOCX)
                                                            </a>
                                                            <a href="{{ route('appeals.download', ['appeal' => $appeal->id, 'format' => 'txt']) }}" 
                                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                <i class="fas fa-file-alt mr-2"></i> Texto (TXT)
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $appeals->links() }}
                        </div>
                    @else
                        <div class="text-gray-500 text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-lg">Nenhum recurso gerado ainda.</p>
                            <p class="mt-2 flex flex-col items-center space-y-2">
                                <a href="{{ route('appeals.create') }}" class="text-blue-600 hover:underline">Gere um recurso a partir de uma multa existente</a>
                                <span class="text-gray-400">ou</span>
                                <a href="{{ route('appeals.create_new') }}" class="text-blue-600 hover:underline">Cadastre uma nova multa e gere um recurso</a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownButton = document.getElementById('dropdown-button');
        const dropdownMenu = document.getElementById('dropdown-menu');

        if (dropdownButton && dropdownMenu) {
            dropdownButton.addEventListener('click', function() {
                dropdownMenu.classList.toggle('hidden');
            });

            // Fechar ao clicar fora
            document.addEventListener('click', function(event) {
                if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        }
    });
</script>
