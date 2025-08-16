<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Busca Automática de Multas') }}
            </h2>
            <div class="flex items-center space-x-4">
                <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full">
                    <strong>Assinatura:</strong> {{ Auth::user()->hasActiveSubscription() ? 'Ativa' : 'Inativa' }}
                </span>
                <a href="{{ route('appeals.create_new') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-rocket mr-2"></i> Gerar Recurso
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Mensagens de sucesso/erro -->
            <div id="message-container" class="mb-6"></div>

            <!-- Cards de busca -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <!-- Busca por CPF -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-id-card text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Buscar por CPF</h3>
                                <p class="text-sm text-gray-600">Encontre multas pelo seu CPF</p>
                            </div>
                        </div>
                        
                        <form id="cpf-search-form" class="space-y-4">
                            @csrf
                            <div>
                                <label for="cpf" class="block text-sm font-medium text-gray-700 mb-2">CPF</label>
                                <input type="text" id="cpf" name="cpf" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="000.000.000-00" maxlength="14" required>
                            </div>
                            
                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i>
                                <span id="cpf-search-text">Buscar Multas</span>
                                <div id="cpf-loading" class="hidden">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Busca por CNH -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-id-badge text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Buscar por CNH</h3>
                                <p class="text-sm text-gray-600">Encontre multas pela sua CNH</p>
                            </div>
                        </div>
                        
                        <form id="cnh-search-form" class="space-y-4">
                            @csrf
                            <div>
                                <label for="cnh" class="block text-sm font-medium text-gray-700 mb-2">Número da CNH</label>
                                <input type="text" id="cnh" name="cnh" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                       placeholder="00000000000" maxlength="11" required>
                            </div>
                            
                            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i>
                                <span id="cnh-search-text">Buscar Multas</span>
                                <div id="cnh-loading" class="hidden">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Busca por Placa -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-car text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Buscar por Placa</h3>
                                <p class="text-sm text-gray-600">Encontre multas pela placa</p>
                            </div>
                        </div>
                        
                        <form id="plate-search-form" class="space-y-4">
                            @csrf
                            <div>
                                <label for="plate" class="block text-sm font-medium text-gray-700 mb-2">Placa do Veículo</label>
                                <input type="text" id="plate" name="plate" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                       placeholder="ABC-1234" maxlength="8" required>
                            </div>
                            
                            <button type="submit" class="w-full bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i>
                                <span id="plate-search-text">Buscar Multas</span>
                                <div id="plate-loading" class="hidden">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Busca Completa -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-8 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">
                            <i class="fas fa-rocket mr-2"></i>
                            Busca Completa Automática
                        </h3>
                        <p class="text-blue-600 text-sm">
                            Busque multas por todos os seus documentos de uma vez (CPF, CNH e placas cadastradas)
                        </p>
                    </div>
                    <button id="search-all-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        <span id="search-all-text">Buscar Tudo</span>
                        <div id="search-all-loading" class="hidden ml-2">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Resultados da busca -->
            <div id="search-results" class="hidden">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-list mr-2"></i>
                                Resultados da Busca
                            </h3>
                            <div class="flex items-center space-x-2">
                                <span id="total-tickets" class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                    0 multas encontradas
                                </span>
                                <button id="clear-results" class="text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div id="results-content" class="space-y-4">
                            <!-- Resultados serão inseridos aqui -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Multas existentes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-history mr-2"></i>
                            Suas Multas Cadastradas
                        </h3>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                                {{ $existingTickets->count() }} multas
                            </span>
                            <a href="{{ route('tickets.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                <i class="fas fa-plus mr-1"></i> Nova Multa
                            </a>
                        </div>
                    </div>

                    @if($existingTickets->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placa</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pontos</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fonte</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($existingTickets as $ticket)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($ticket->date)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $ticket->plate }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                                {{ $ticket->reason }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                R$ {{ number_format($ticket->amount, 2, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="px-2 py-1 text-xs rounded-full {{ $ticket->points > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ $ticket->points }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                    {{ $ticket->source ?? 'Manual' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('appeals.create_new', ['ticket_id' => $ticket->id]) }}" 
                                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                                    <i class="fas fa-rocket"></i> Recurso
                                                </a>
                                                <a href="{{ route('tickets.edit', $ticket->id) }}" 
                                                   class="text-green-600 hover:text-green-900">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-ticket-alt text-gray-400 text-2xl"></i>
                            </div>
                            <h4 class="text-gray-600 font-medium mb-2">Nenhuma multa cadastrada</h4>
                            <p class="text-gray-500 text-sm mb-4">Use os campos acima para buscar multas automaticamente ou cadastre manualmente</p>
                            <a href="{{ route('tickets.create') }}" class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i> Cadastrar Multa
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Formatação de campos
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });

        document.getElementById('plate').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            if (value.length > 3) {
                value = value.substring(0, 3) + '-' + value.substring(3);
            }
            e.target.value = value;
        });

        // Busca por CPF
        document.getElementById('cpf-search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch('cpf', '/traffic-tickets/search-cpf', {
                cpf: document.getElementById('cpf').value
            });
        });

        // Busca por CNH
        document.getElementById('cnh-search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch('cnh', '/traffic-tickets/search-cnh', {
                cnh: document.getElementById('cnh').value
            });
        });

        // Busca por placa
        document.getElementById('plate-search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch('plate', '/traffic-tickets/search-plate', {
                plate: document.getElementById('plate').value
            });
        });

        // Busca completa
        document.getElementById('search-all-btn').addEventListener('click', function() {
            performSearch('all', '/traffic-tickets/search-all', {});
        });

        // Função para executar busca
        function performSearch(type, url, data) {
            const loadingElements = {
                cpf: { text: document.getElementById('cpf-search-text'), loading: document.getElementById('cpf-loading') },
                cnh: { text: document.getElementById('cnh-search-text'), loading: document.getElementById('cnh-loading') },
                plate: { text: document.getElementById('plate-search-text'), loading: document.getElementById('plate-loading') },
                all: { text: document.getElementById('search-all-text'), loading: document.getElementById('search-all-loading') }
            };

            // Mostrar loading
            if (loadingElements[type]) {
                loadingElements[type].text.classList.add('hidden');
                loadingElements[type].loading.classList.remove('hidden');
            }

            // Fazer requisição
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showMessage('success', result.message);
                    showResults(result.tickets || result.results, result.total_tickets);
                } else {
                    showMessage('error', result.message || 'Nenhuma multa encontrada');
                }
            })
            .catch(error => {
                console.error('Erro na busca:', error);
                showMessage('error', 'Erro ao realizar busca. Tente novamente.');
            })
            .finally(() => {
                // Ocultar loading
                if (loadingElements[type]) {
                    loadingElements[type].text.classList.remove('hidden');
                    loadingElements[type].loading.classList.add('hidden');
                }
            });
        }

        // Mostrar mensagem
        function showMessage(type, message) {
            const container = document.getElementById('message-container');
            const alertClass = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
            
            container.innerHTML = `
                <div class="border rounded-lg p-4 ${alertClass}">
                    <div class="flex items-center">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} mr-2"></i>
                        <span>${message}</span>
                    </div>
                </div>
            `;

            // Auto-remover após 5 segundos
            setTimeout(() => {
                container.innerHTML = '';
            }, 5000);
        }

        // Mostrar resultados
        function showResults(tickets, totalTickets) {
            const resultsContainer = document.getElementById('search-results');
            const totalElement = document.getElementById('total-tickets');
            const contentElement = document.getElementById('results-content');

            totalElement.textContent = `${totalTickets} multas encontradas`;
            
            if (Array.isArray(tickets)) {
                // Resultado simples
                contentElement.innerHTML = tickets.map(ticket => `
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-4">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                        ${ticket.source || 'API'}
                                    </span>
                                    <span class="text-sm text-gray-600">${ticket.date}</span>
                                    <span class="font-medium">${ticket.plate}</span>
                                </div>
                                <p class="text-gray-800 mt-1">${ticket.reason}</p>
                                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                                    <span>R$ ${ticket.amount}</span>
                                    <span>${ticket.points} pontos</span>
                                    <span>${ticket.location}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <a href="{{ route('appeals.create_new') }}?ticket_data=${encodeURIComponent(JSON.stringify(ticket))}" 
                                   class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                    <i class="fas fa-rocket mr-1"></i> Gerar Recurso
                                </a>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                // Resultado complexo (busca completa)
                contentElement.innerHTML = Object.entries(tickets).map(([source, result]) => `
                    <div class="border border-gray-200 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-${source === 'cpf' ? 'id-card' : source === 'cnh' ? 'id-badge' : 'car'} mr-2"></i>
                            ${source.toUpperCase()}: ${result.total_tickets} multas
                        </h4>
                        ${result.tickets.map(ticket => `
                            <div class="border-l-2 border-gray-200 pl-4 py-2 mb-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                                ${ticket.source || 'API'}
                                            </span>
                                            <span class="text-sm text-gray-600">${ticket.date}</span>
                                            <span class="font-medium">${ticket.plate}</span>
                                        </div>
                                        <p class="text-gray-800 mt-1">${ticket.reason}</p>
                                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                                            <span>R$ ${ticket.amount}</span>
                                            <span>${ticket.points} pontos</span>
                                            <span>${ticket.location}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('appeals.create_new') }}?ticket_data=${encodeURIComponent(JSON.stringify(ticket))}" 
                                           class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                            <i class="fas fa-rocket mr-1"></i> Gerar Recurso
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `).join('');
            }

            resultsContainer.classList.remove('hidden');
        }

        // Limpar resultados
        document.getElementById('clear-results').addEventListener('click', function() {
            document.getElementById('search-results').classList.add('hidden');
        });
    </script>
    @endpush
</x-app-layout>
