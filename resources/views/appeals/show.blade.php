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
                <a href="{{ route('appeals.download', $appeal->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">Baixar PDF</a>
                <a href="{{ route('appeals.download', ['appeal' => $appeal->id, 'format' => 'doc']) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">Baixar DOC</a>
                <a href="{{ route('appeals.download', ['appeal' => $appeal->id, 'format' => 'docx']) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">Baixar DOCX</a>
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
                    <!-- Cabeçalho do Recurso -->
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Recurso #{{ $appeal->id }}</h3>
                            <p class="text-sm text-gray-600">Criado em {{ $appeal->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="space-x-2">
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

                    <!-- Resumo Rápido -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="p-4 bg-gray-50 rounded-lg border">
                            <div class="text-xs text-gray-500">Placa</div>
                            <div class="text-base font-semibold">{{ $appeal->ticket->plate }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg border">
                            <div class="text-xs text-gray-500">Data</div>
                            <div class="text-base font-semibold">{{ $appeal->ticket->date->format('d/m/Y') }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg border">
                            <div class="text-xs text-gray-500">Valor</div>
                            <div class="text-base font-semibold text-red-600">R$ {{ number_format($appeal->ticket->amount, 2, ',', '.') }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg border">
                            <div class="text-xs text-gray-500">Tipo de Infração</div>
                            <div class="text-base font-semibold">{{ optional($appeal->ticket->infractionType)->code }} - {{ optional($appeal->ticket->infractionType)->description }}</div>
                        </div>
                    </div>

                    <!-- Detalhes -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-3">Dados da Multa</h4>
                            <div class="space-y-2 text-sm">
                                <div><span class="text-gray-500">Auto/Notificação:</span> <span class="font-medium">{{ $appeal->ticket->citation_number ?? '—' }}</span></div>
                                <div><span class="text-gray-500">Local:</span> <span class="font-medium">{{ $appeal->ticket->location }}</span></div>
                                <div><span class="text-gray-500">Motivo:</span> <span class="font-medium">{{ $appeal->ticket->reason }}</span></div>
                                <div><span class="text-gray-500">Modelo:</span> <span class="font-medium">{{ $appeal->ticket->vehicle_model }} • {{ $appeal->ticket->vehicle_color }}</span></div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-3">Ações</h4>
                            <div class="flex flex-wrap gap-2">
                                <button id="btnCopyText" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md text-xs font-semibold">Copiar texto</button>
                                <a href="{{ route('appeals.download', $appeal->id) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-xs font-semibold">Baixar PDF</a>
                                <a href="{{ route('appeals.download', ['appeal' => $appeal->id, 'format' => 'doc']) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold">Baixar DOC</a>
                                <a href="{{ route('appeals.download', ['appeal' => $appeal->id, 'format' => 'docx']) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-xs font-semibold">Baixar DOCX</a>
                            </div>
                        </div>
                    </div>

                    <!-- Texto do Recurso -->
                    <div class="mt-6">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Texto do Recurso</h4>
                        <div id="appeal-text" class="bg-gray-50 p-4 rounded-md whitespace-pre-line border">{{ $appeal->generated_text }}</div>
                    </div>

                    @if($appeal->notes)
                        <div class="mt-6">
                            <h4 class="text-md font-medium text-gray-900 mb-3">Anotações</h4>
                            <div class="bg-gray-50 p-4 rounded-md border">{{ $appeal->notes }}</div>
                        </div>
                    @endif

                    <div class="mt-8 flex justify-end gap-2">
                        <a href="{{ route('tickets.show', $appeal->ticket->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">Ver Multa</a>
                        <a href="{{ route('appeals.download', $appeal->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">Baixar PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('btnCopyText');
            if (btn) {
                btn.addEventListener('click', function() {
                    var el = document.getElementById('appeal-text');
                    if (!el) return;
                    var range = document.createRange();
                    range.selectNodeContents(el);
                    var sel = window.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(range);
                    try { document.execCommand('copy'); } catch(e) {}
                    sel.removeAllRanges();
                    btn.textContent = 'Copiado!';
                    setTimeout(function(){ btn.textContent = 'Copiar texto'; }, 1500);
                });
            }
        });
    </script>
</x-app-layout>
