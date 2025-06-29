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
                    📁 Baixar PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagem de sucesso ou erro -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded animate-pulse">
                    <div class="flex items-center">
                        <span class="text-2xl mr-2">🎉</span>
                    {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <div class="flex items-center">
                        <span class="text-2xl mr-2">❌</span>
                    {{ session('error') }}
                    </div>
                </div>
            @endif

            <!-- Análise da Inteligência Híbrida -->
            @php
                $metadata = $appeal->metadata ? json_decode($appeal->metadata, true) : null;
            @endphp
            
            @if($metadata && isset($metadata['generated_with_all_models']) && $metadata['generated_with_all_models'])
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <span class="text-3xl mr-3">🧠</span>
                            <div>
                                <h3 class="text-xl font-bold text-purple-800">Inteligência Híbrida Aplicada</h3>
                                <p class="text-sm text-purple-600">Resultado da análise automática das 3 IAs especializadas</p>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-white p-4 rounded-lg border border-purple-200">
                                <h4 class="font-semibold text-purple-800 mb-2">🎯 Versão Selecionada</h4>
                                <p class="text-lg font-bold text-purple-700">{{ $metadata['selected_model'] ?? 'Não especificado' }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $metadata['selection_reason'] ?? 'Motivo não disponível' }}</p>
                            </div>
                            
                            <div class="bg-white p-4 rounded-lg border border-blue-200">
                                <h4 class="font-semibold text-blue-800 mb-2">⚙️ Modelos Utilizados</h4>
                                <div class="space-y-1">
                                    <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">💎 Google Gemini Pro</span>
                                    <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs">🇧🇷 RoBERTaLexPT</span>
                                    <span class="inline-block bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs">🔥 GPT-4 Turbo</span>
                                </div>
                            </div>
                        </div>
                        
                        @if(isset($metadata['detailed_analysis']))
                            <div class="mt-6">
                                <button onclick="toggleAnalysis()" class="w-full bg-purple-100 hover:bg-purple-200 text-purple-800 font-medium py-2 px-4 rounded-lg transition-colors">
                                    <span id="analysis-toggle-text">📊 Ver Análise Comparativa Detalhada</span>
                                    <span class="ml-2">▼</span>
                                </button>
                                
                                <div id="detailed-analysis" class="hidden mt-4 bg-white p-4 rounded-lg border border-gray-200">
                                    <h5 class="font-semibold text-gray-800 mb-3">🔍 Análise Comparativa das Versões</h5>
                                    <div class="prose max-w-none text-sm">
                                        <div class="whitespace-pre-wrap">{{ $metadata['detailed_analysis'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Status do Recurso -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">📋 Recurso #{{ $appeal->id }}</h3>
                        <div class="flex items-center">
                            <span class="px-4 py-2 text-sm font-medium rounded-full 
                                @if($appeal->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($appeal->status === 'approved') bg-green-100 text-green-800
                                @elseif($appeal->status === 'rejected') bg-red-100 text-red-800
                                @endif">
                                @if($appeal->status === 'pending') ⏳ Pendente
                                @elseif($appeal->status === 'approved') ✅ Aprovado
                                @elseif($appeal->status === 'rejected') ❌ Rejeitado
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Informações do Recurso -->
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <span class="text-2xl">ℹ️</span>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Informações Importantes:</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="space-y-1">
                                        <li>📅 Gerado em: {{ $appeal->created_at->format('d/m/Y H:i') }}</li>
                                        <li>⏰ Prazo para protocolo: {{ $appeal->created_at->addDays(30)->format('d/m/Y') }}</li>
                                        <li>📄 PDF limpo disponível para download</li>
                                        @if($metadata && isset($metadata['generated_with_all_models']) && $metadata['generated_with_all_models'])
                                            <li>🧠 Gerado com Inteligência Híbrida (3 créditos)</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Próximos Passos -->
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <span class="text-2xl">✅</span>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">Próximos Passos:</h3>
                                <div class="mt-2 text-sm text-green-700">
                                    <ol class="list-decimal pl-5 space-y-1">
                                        <li>Faça o download do PDF do recurso</li>
                                        <li>Imprima o documento</li>
                                        <li>Assine no local indicado</li>
                                        <li>Protocole no órgão de trânsito dentro do prazo</li>
                                        <li>Guarde o protocolo de entrega</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex justify-center space-x-4 mb-6">
                        <a href="{{ route('appeals.download', $appeal) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition shadow-lg hover:shadow-xl">
                            📁 Baixar PDF
                        </a>
                        <button onclick="copyToClipboard()" class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition shadow-lg hover:shadow-xl">
                            📋 Copiar Texto
                        </button>
                    </div>

                    <!-- Texto do Recurso Limpo -->
                    <div class="mt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">📄 Recurso Final (Texto Limpo)</h4>
                        
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <h5 class="text-sm font-medium text-gray-900">
                                    📝 Texto Completo do Recurso
                                </h5>
                                <div class="space-x-2">
                                <button onclick="copyToClipboard()" class="text-sm text-blue-600 hover:text-blue-800">
                                        📋 Copiar Texto
                                </button>
                                    <span class="text-xs text-gray-500">|</span>
                                    <span class="text-xs text-green-600">✨ Texto limpo e pronto para protocolo</span>
                                </div>
                            </div>
                            <div class="prose max-w-none">
                                <div class="whitespace-pre-wrap font-mono text-sm bg-white p-4 rounded border">{{ $appeal->text }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Dicas de Protocolo -->
                    <div class="mt-6">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h5 class="text-sm font-medium text-green-800 mb-2">
                                📋 Dicas para Protocolo
                            </h5>
                            <ul class="list-disc list-inside text-sm text-green-700 space-y-2">
                                <li>✅ Imprima o recurso em papel A4 de boa qualidade</li>
                                <li>✏️ Assine no local indicado com caneta azul</li>
                                <li>📎 Anexe cópias dos documentos mencionados no recurso</li>
                                <li>⏰ Protocole dentro do prazo legal ({{ $appeal->created_at->addDays(30)->format('d/m/Y') }})</li>
                                <li>🗃️ Guarde o protocolo de recebimento em local seguro</li>
                                <li>📄 Faça uma cópia do recurso protocolado para seu controle</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function copyToClipboard() {
                    const text = `{{ str_replace(["\r\n", "\n", "\r"], "\\n", addslashes($appeal->text)) }}`;
                    navigator.clipboard.writeText(text).then(() => {
                        alert('📋 Texto copiado para a área de transferência!');
                    }).catch(() => {
                        // Fallback para navegadores mais antigos
                        const textArea = document.createElement('textarea');
                        textArea.value = text;
                        document.body.appendChild(textArea);
                        textArea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textArea);
                        alert('📋 Texto copiado para a área de transferência!');
                    });
                }

                function toggleAnalysis() {
                    const analysis = document.getElementById('detailed-analysis');
                    const toggleText = document.getElementById('analysis-toggle-text');
                    
                    if (analysis.classList.contains('hidden')) {
                        analysis.classList.remove('hidden');
                        toggleText.textContent = '📊 Ocultar Análise Comparativa';
                    } else {
                        analysis.classList.add('hidden');
                        toggleText.textContent = '📊 Ver Análise Comparativa Detalhada';
                    }
                }
            </script>
        </div>
    </div>
</x-app-layout>
