<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Bem-vindo(a), ') }} <span class="text-blue-600">{{ Auth::user()->name }}</span>!
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ now()->format('d/m/Y') }} - Painel de controle do AutoRecurso</p>
            </div>
            <div class="flex items-center">
                <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full flex items-center space-x-1 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <strong>{{ Auth::user()->credits }} créditos</strong>
                </span>
                @if(config('app.env') == 'local' || config('app.env') == 'development')
                    <a href="{{ route('credits.free') }}" class="ml-2 px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs rounded-full hover:from-blue-600 hover:to-blue-700 transition shadow-sm flex items-center">
                        <i class="fas fa-gift mr-1"></i> +5 créditos grátis
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagem de sucesso ou erro -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Banner informativo -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg mb-6 overflow-hidden">
                <div class="md:flex">
                    <div class="p-6 md:w-2/3 text-white">
                        <h3 class="text-xl font-bold mb-2">Bem-vindo ao AutoRecurso</h3>
                        <p class="mb-4">Use nossa inteligência artificial para contestar multas de trânsito com maior chance de sucesso. Economize tempo e dinheiro.</p>
                        <div class="flex space-x-3">
                            <a href="{{ route('appeals.create_new') }}" class="inline-block px-4 py-2 bg-white text-blue-600 rounded-lg font-medium shadow-sm hover:bg-gray-100 transition flex flex-col items-center">
                                <span><i class="fas fa-file-alt mr-1"></i> Gerar Recurso</span>
                                <span class="text-xs text-blue-400 mt-1">Custo: 1 crédito</span>
                            </a>
                            <a href="{{ route('credits.packages') }}" class="inline-block px-4 py-2 bg-blue-700 text-white rounded-lg font-medium shadow-sm hover:bg-blue-800 transition">
                                <i class="fas fa-coins mr-1"></i> Comprar Créditos
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:block md:w-1/3 relative">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-gavel text-white text-9xl opacity-20"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                        Seu Progresso
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 transition hover:shadow-md flex">
                            <div class="rounded-full w-12 h-12 bg-blue-100 flex items-center justify-center mr-3">
                                <i class="fas fa-ticket-alt text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-blue-600">{{ $stats['tickets_count'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Multas Cadastradas</div>
                            </div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200 transition hover:shadow-md flex">
                            <div class="rounded-full w-12 h-12 bg-green-100 flex items-center justify-center mr-3">
                                <i class="fas fa-file-alt text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-green-600">{{ $stats['appeals_count'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Recursos Gerados</div>
                            </div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 transition hover:shadow-md flex">
                            <div class="rounded-full w-12 h-12 bg-purple-100 flex items-center justify-center mr-3">
                                <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-purple-600">{{ $stats['appeals_successful'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Recursos com Sucesso</div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 transition hover:shadow-md flex">
                            <div class="rounded-full w-12 h-12 bg-yellow-100 flex items-center justify-center mr-3">
                                <i class="fas fa-coins text-yellow-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-yellow-600">{{ Auth::user()->credits }}</div>
                                <div class="text-sm text-gray-600">Créditos Disponíveis</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-bolt text-amber-500 mr-2"></i>
                        Ações Rápidas
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('tickets.create') }}" class="bg-blue-50 hover:bg-blue-100 p-5 rounded-lg border border-blue-200 flex items-center justify-between transition duration-300 group">
                            <div class="flex items-center">
                                <div class="rounded-full w-10 h-10 bg-blue-500 flex items-center justify-center mr-3 group-hover:bg-blue-600 transition">
                                    <i class="fas fa-plus text-white"></i>
                                </div>
                                <span class="font-medium text-blue-800">Cadastrar Nova Multa</span>
                            </div>
                            <i class="fas fa-chevron-right text-blue-300 group-hover:text-blue-500 transition"></i>
                        </a>
                        <a href="{{ route('appeals.create_new') }}" class="bg-green-50 hover:bg-green-100 p-5 rounded-lg border border-green-200 flex items-center justify-between transition duration-300 group">
                            <div class="flex items-center">
                                <div class="rounded-full w-10 h-10 bg-green-500 flex items-center justify-center mr-3 group-hover:bg-green-600 transition">
                                    <i class="fas fa-file-alt text-white"></i>
                                </div>
                                <div>
                                    <span class="font-medium text-green-800 block">Gerar Novo Recurso</span>
                                    <span class="text-xs text-green-600">Custo: 1 crédito por recurso</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-green-300 group-hover:text-green-500 transition"></i>
                        </a>
                        <a href="{{ route('credits.packages') }}" class="bg-yellow-50 hover:bg-yellow-100 p-5 rounded-lg border border-yellow-200 flex items-center justify-between transition duration-300 group">
                            <div class="flex items-center">
                                <div class="rounded-full w-10 h-10 bg-yellow-500 flex items-center justify-center mr-3 group-hover:bg-yellow-600 transition">
                                    <i class="fas fa-coins text-white"></i>
                                </div>
                                <span class="font-medium text-yellow-800">Comprar Créditos</span>
                            </div>
                            <i class="fas fa-chevron-right text-yellow-300 group-hover:text-yellow-500 transition"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Multas Recentes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-ticket-alt text-blue-600 mr-2"></i>
                                Multas Recentes
                            </h3>
                            <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition shadow-sm">
                                <i class="fas fa-plus mr-1"></i> Nova Multa
                            </a>
                        </div>

                        @if(count($recent_tickets ?? []) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="bg-gray-50 border-b">
                                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placa</th>
                                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recent_tickets as $ticket)
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="p-3 text-sm font-medium">
                                                    <a href="{{ route('tickets.show', $ticket->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center">
                                                        <i class="fas fa-car-side mr-1 text-gray-400"></i>
                                                        {{ $ticket->plate }}
                                                    </a>
                                                </td>
                                                <td class="p-3 text-sm text-gray-600">{{ $ticket->date->format('d/m/Y') }}</td>
                                                <td class="p-3 text-sm font-medium text-gray-800">R$ {{ number_format($ticket->amount, 2, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 text-right">
                                <a href="{{ route('tickets.index') }}" class="text-sm text-blue-600 hover:text-blue-800 hover:underline flex items-center justify-end">
                                    Ver todas as multas
                                    <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-6 text-center">
                                <div class="text-5xl text-gray-300 mb-3">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <p class="text-gray-600 mb-4">Nenhuma multa cadastrada ainda.</p>
                                <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition shadow-sm">
                                    <i class="fas fa-plus mr-1"></i> Cadastrar primeira multa
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recursos Recentes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-file-alt text-green-600 mr-2"></i>
                                Recursos Recentes
                            </h3>
                            @if(($stats['tickets_count'] ?? 0) > 0)
                                <div class="dropdown relative">
                                    <button class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition shadow-sm">
                                        <i class="fas fa-plus mr-1"></i> Novo Recurso
                                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                    </button>
                                    <div class="dropdown-menu hidden absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1">
                                            <div class="px-4 py-2 text-xs font-medium text-gray-500 bg-gray-50 border-b">
                                                Cada recurso custa 1 crédito
                                            </div>
                                            <a href="{{ route('tickets.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-search mr-2 text-green-500"></i> A partir de multa existente
                                            </a>
                                            <a href="{{ route('appeals.create_new') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-plus mr-2 text-green-500"></i> Cadastrar nova multa e recurso
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const dropdownButton = document.querySelector('.dropdown button');
                                        const dropdownMenu = document.querySelector('.dropdown-menu');

                                        if (dropdownButton && dropdownMenu) {
                                            dropdownButton.addEventListener('click', function() {
                                                dropdownMenu.classList.toggle('hidden');
                                            });

                                            document.addEventListener('click', function(event) {
                                                if (!event.target.closest('.dropdown')) {
                                                    dropdownMenu.classList.add('hidden');
                                                }
                                            });
                                        }
                                    });
                                </script>
                            @endif
                        </div>

                        @if(count($recent_appeals ?? []) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="bg-gray-50 border-b">
                                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Multa</th>
                                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recent_appeals as $appeal)
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="p-3 text-sm font-medium">
                                                    <a href="{{ route('appeals.show', $appeal->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center">
                                                        <i class="fas fa-file-alt mr-1 text-gray-400"></i>
                                                        {{ $appeal->ticket->plate }}
                                                    </a>
                                                </td>
                                                <td class="p-3 text-sm text-gray-600">{{ $appeal->created_at->format('d/m/Y') }}</td>
                                                <td class="p-3 text-sm">
                                                    @if($appeal->status == 'pending')
                                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs flex items-center w-fit">
                                                            <i class="fas fa-clock mr-1"></i> Pendente
                                                        </span>
                                                    @elseif($appeal->status == 'sent')
                                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs flex items-center w-fit">
                                                            <i class="fas fa-paper-plane mr-1"></i> Enviado
                                                        </span>
                                                    @elseif($appeal->status == 'successful')
                                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs flex items-center w-fit">
                                                            <i class="fas fa-check-circle mr-1"></i> Deferido
                                                        </span>
                                                    @elseif($appeal->status == 'rejected')
                                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs flex items-center w-fit">
                                                            <i class="fas fa-times-circle mr-1"></i> Indeferido
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 text-right">
                                <a href="{{ route('appeals.index') }}" class="text-sm text-green-600 hover:text-green-800 hover:underline flex items-center justify-end">
                                    Ver todos os recursos
                                    <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-6 text-center">
                                <div class="text-5xl text-gray-300 mb-3">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <p class="text-gray-600 mb-4">Nenhum recurso gerado ainda.</p>
                                @if(($stats['tickets_count'] ?? 0) > 0)
                                    <a href="{{ route('tickets.index') }}" class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition shadow-sm">
                                        <i class="fas fa-plus mr-1"></i> Gerar primeiro recurso
                                    </a>
                                @else
                                    <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition shadow-sm">
                                        <i class="fas fa-plus mr-1"></i> Cadastrar uma multa primeiro
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dicas úteis -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                        Dicas para Contestação de Multas
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="text-blue-600 text-lg mb-2 font-medium">Prazo de Recurso</div>
                            <p class="text-gray-700 text-sm">Lembre-se que você tem até 30 dias após o recebimento da notificação para contestar a multa.</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="text-blue-600 text-lg mb-2 font-medium">Documentação</div>
                            <p class="text-gray-700 text-sm">Reúna todos os documentos relevantes, como carteira de habilitação, documento do veículo e comprovantes.</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="text-blue-600 text-lg mb-2 font-medium">Protocolo</div>
                            <p class="text-gray-700 text-sm">Protocole seu recurso no órgão competente e guarde o número de protocolo para acompanhamento.</p>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="#" class="text-blue-600 hover:text-blue-800 hover:underline text-sm">
                            Ver mais dicas e artigos sobre contestação de multas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
