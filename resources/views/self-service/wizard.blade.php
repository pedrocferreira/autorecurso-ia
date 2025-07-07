@extends('self-service.layout')

@section('title', 'Wizard de Recurso - AutoRecurso')

@push('styles')
<link rel="stylesheet" href="/css/mobile-chat.css">
<style>
/* Scrollbar personalizada */
#chat-body::-webkit-scrollbar {
    width: 8px;
}

#chat-body::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

#chat-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

#chat-body::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Animações suaves */
.message-enter {
    opacity: 0;
    transform: translateY(10px);
}

.message-enter-active {
    opacity: 1;
    transform: translateY(0);
    transition: opacity 300ms, transform 300ms;
}

/* Efeito de sombra suave */
.chat-shadow {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Efeito de gradiente animado */
@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.gradient-animate {
    background-size: 200% 200%;
    animation: gradient 15s ease infinite;
}

/* Animação de digitação */
.typing-indicator {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    background: #f3f4f6;
    border-radius: 15px;
}

.typing-indicator span {
    width: 6px;
    height: 6px;
    background: #6b7280;
    border-radius: 50%;
    margin: 0 2px;
    display: inline-block;
    animation: typing 1.4s infinite ease-in-out;
}

.typing-indicator span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-4px); }
}

/* Estilo para mensagens do bot */
.bot-message {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

/* Estilo para mensagens do usuário */
.user-message {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border: 1px solid #2563eb;
    box-shadow: 0 2px 4px rgba(59,130,246,0.2);
}

/* Avatar da Ana com animação */
.ana-avatar {
    position: relative;
    transition: transform 0.3s ease;
}

.ana-avatar:hover {
    transform: scale(1.05);
}

.ana-avatar::after {
    content: '';
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 12px;
    height: 12px;
    background-color: #22c55e;
    border: 2px solid white;
    border-radius: 50%;
}

/* Estilos para a seleção de infrações */
.infraction-search {
    position: relative;
}

.infraction-search input {
    padding-left: 40px;
}

.infraction-search i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
}

.infraction-categories {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
    -webkit-overflow-scrolling: touch;
}

.category-tag {
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.category-tag.active {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.category-tag:not(.active) {
    background: #f3f4f6;
    color: #4b5563;
}

.category-tag:not(.active):hover {
    background: #e5e7eb;
}

.infraction-card {
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.infraction-card:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
}

.infraction-code {
    font-family: monospace;
    background: #f3f4f6;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
}

.infraction-points {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    background: #fee2e2;
    color: #991b1b;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.infraction-amount {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    background: #dbeafe;
    color: #1e40af;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.severity-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.severity-light {
    background: #ecfdf5;
    color: #065f46;
}

.severity-medium {
    background: #fffbeb;
    color: #92400e;
}

.severity-severe {
    background: #fff1f2;
    color: #9f1239;
}

.severity-very_severe {
    background: #fef2f2;
    color: #991b1b;
}

.no-results {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.no-results i {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #9ca3af;
}

/* ===== RESPONSIVIDADE MOBILE ===== */

/* Ajustes gerais para mobile */
@media (max-width: 768px) {
    /* Header mais compacto */
    .header-mobile {
        padding: 0.75rem 1rem;
    }
    
    .header-mobile .ana-avatar img {
        width: 2.5rem;
        height: 2.5rem;
    }
    
    .header-mobile h1 {
        font-size: 1rem;
        line-height: 1.2;
    }
    
    .header-mobile p {
        font-size: 0.75rem;
    }
    
    /* Progress bar menor */
    .progress-mobile {
        width: 6rem;
        height: 0.375rem;
    }
    
    /* Chat body com padding menor */
    #chat-body {
        padding: 0.75rem;
        padding-bottom: 1rem;
    }
    
    /* Mensagens mais compactas */
    .message-mobile {
        max-width: 85vw !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.875rem;
        line-height: 1.4;
    }
    
    /* Avatars menores */
    .avatar-mobile {
        width: 2rem !important;
        height: 2rem !important;
        flex-shrink: 0;
    }
    
    /* Input area mais compacta */
    .input-mobile {
        padding: 0.75rem;
        padding-bottom: calc(0.75rem + env(safe-area-inset-bottom));
    }
    
    /* Botões de opção mais altos para touch */
    .option-mobile {
        min-height: 3.5rem;
        padding: 1rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.3;
    }
    
    /* Categorias de infração com scroll horizontal melhor */
    .categories-mobile {
        gap: 0.375rem;
        padding: 0 0.5rem 0.75rem 0.5rem;
        margin: 0 -0.5rem 1rem -0.5rem;
    }
    
    .category-tag-mobile {
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
        min-width: fit-content;
    }
    
    /* Cards de infração mais compactos */
    .infraction-card-mobile {
        padding: 0.75rem;
        margin-bottom: 0.5rem;
    }
    
    .infraction-card-mobile .infraction-code {
        font-size: 0.8rem;
        padding: 0.2rem 0.4rem;
    }
    
    .infraction-card-mobile .infraction-points,
    .infraction-card-mobile .infraction-amount {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
    
    /* Search input mais alto para touch */
    .search-mobile {
        height: 3.25rem;
        font-size: 1rem;
    }
    
    /* Textarea mais alta */
    .textarea-mobile {
        min-height: 4rem;
        font-size: 1rem;
    }
    
    /* Botão de envio mais alto */
    .send-button-mobile {
        height: 3.25rem;
        font-size: 1rem;
        font-weight: 600;
    }
    
    /* Espaçamento entre mensagens menor */
    .message-spacing-mobile {
        margin-bottom: 0.75rem;
    }
    
    /* Container de pagamento responsivo */
    .payment-container-mobile {
        padding: 1rem;
        margin: 0.5rem 0;
    }
    
    .payment-button-mobile {
        width: 100%;
        min-height: 3.5rem;
        font-size: 1rem;
        font-weight: 600;
    }
}

/* Ajustes para telas muito pequenas */
@media (max-width: 480px) {
    .message-mobile {
        max-width: 90vw !important;
        padding: 0.625rem 0.875rem !important;
        font-size: 0.8rem;
    }
    
    .header-mobile {
        padding: 0.5rem 0.75rem;
    }
    
    .input-mobile {
        padding: 0.5rem;
        padding-bottom: calc(0.5rem + env(safe-area-inset-bottom));
    }
    
    .option-mobile {
        min-height: 3rem;
        padding: 0.875rem 0.625rem;
        font-size: 0.8rem;
    }
    
    .categories-mobile {
        gap: 0.25rem;
        padding: 0 0.375rem 0.5rem 0.375rem;
        margin: 0 -0.375rem 0.75rem -0.375rem;
    }
    
    .category-tag-mobile {
        padding: 0.375rem 0.625rem;
        font-size: 0.75rem;
    }
}

/* Ajustes para landscape em mobile */
@media (max-width: 768px) and (orientation: landscape) {
    .header-mobile {
        padding: 0.5rem 1rem;
    }
    
    .header-mobile .ana-avatar img {
        width: 2rem;
        height: 2rem;
    }
    
    .header-mobile h1 {
        font-size: 0.875rem;
    }
    
    .header-mobile p {
        font-size: 0.7rem;
    }
    
    #chat-body {
        padding: 0.5rem;
    }
    
    .input-mobile {
        padding: 0.5rem;
    }
}

/* Melhorias para touch */
@media (hover: none) and (pointer: coarse) {
    .category-tag:hover,
    .infraction-card:hover,
    .option-mobile:hover {
        transform: none;
    }
    
    .category-tag:active,
    .infraction-card:active,
    .option-mobile:active {
        transform: scale(0.98);
        opacity: 0.8;
    }
    
    /* Área de toque maior para botões */
    .option-mobile,
    .send-button-mobile,
    .payment-button-mobile {
        min-height: 44px;
    }
    
    /* Input com tamanho mínimo para touch */
    input[type="text"],
    input[type="date"],
    textarea {
        min-height: 44px;
        font-size: 16px; /* Evita zoom no iOS */
    }
}

/* Suporte para safe area (iPhone X e similares) */
@supports (padding: max(0px)) {
    .input-mobile {
        padding-bottom: max(0.75rem, env(safe-area-inset-bottom));
    }
    
    .header-mobile {
        padding-top: max(0.75rem, env(safe-area-inset-top));
    }
}

/* Melhorias de acessibilidade */
@media (prefers-reduced-motion: reduce) {
    .message-enter,
    .message-enter-active,
    .typing-indicator span,
    .ana-avatar,
    .category-tag,
    .infraction-card {
        animation: none;
        transition: none;
    }
}

/* Modo escuro (opcional) */
@media (prefers-color-scheme: dark) {
    .bot-message {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        border-color: #374151;
        color: #f9fafb;
    }
    
    .user-message {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        border-color: #1e40af;
    }
    
    .typing-indicator {
        background: #374151;
    }
    
    .typing-indicator span {
        background: #9ca3af;
    }
}
</style>
@endpush

@section('content')
<!-- Meta tags para mobile -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="theme-color" content="#3b82f6">
<!-- CSRF Token Meta Tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="fixed inset-0 bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 overflow-hidden gradient-animate" x-data="wizardApp()">
    <!-- Chat Container -->
    <div class="h-full flex flex-col">
        <!-- Header com Avatar e Progresso -->
        <div class="bg-white border-b px-4 py-3 shadow-sm header-mobile">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center">
                    <div class="ana-avatar">
                        <img src="/images/ana-avatar.png" alt="Ana" class="w-12 h-12 rounded-full border-2 border-blue-200 avatar-mobile">
                    </div>
                    <div class="ml-3">
                        <h1 class="text-lg font-semibold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Ana - Assistente Virtual</h1>
                        <p class="text-xs text-gray-500">Especialista em Recursos de Multas</p>
                    </div>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-sm font-medium text-blue-600" x-text="`${progress.toFixed(0)}% completo`"></span>
                    <div class="w-32 bg-gray-100 rounded-full h-2 mt-1 progress-mobile">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-700 ease-in-out"
                             :style="`width: ${progress}%`"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div id="chat-body" class="flex-1 overflow-y-auto p-4 space-y-4">
            <!-- Messages -->
            <template x-for="(message, index) in messages" :key="index">
                <div class="flex items-end space-x-2 message-enter message-spacing-mobile" :class="message.type === 'user' ? 'justify-end' : 'justify-start'">
                    <!-- Avatar para mensagens da Ana -->
                    <template x-if="message.type !== 'user'">
                        <div class="ana-avatar flex-shrink-0">
                            <img src="/images/ana-avatar.png" alt="Ana" class="w-8 h-8 rounded-full border-2 border-white shadow-sm avatar-mobile">
                        </div>
                    </template>
                    
                    <div class="max-w-xs lg:max-w-md px-4 py-3 rounded-2xl shadow-sm message-mobile" 
                         :class="message.type === 'user' ? 
                            'user-message text-white rounded-br-sm' : 
                            'bot-message rounded-bl-sm'">
                        <div x-html="message.content" class="text-sm"></div>
                    </div>

                    <!-- Avatar do usuário -->
                    <template x-if="message.type === 'user'">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center text-blue-600 text-sm font-medium border-2 border-white shadow-sm flex-shrink-0 avatar-mobile">
                            <i class="fas fa-user"></i>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Typing Indicator -->
            <div x-show="isTyping" class="flex items-start space-x-2">
                <div class="ana-avatar flex-shrink-0">
                    <img src="/images/ana-avatar.png" alt="Ana" class="w-8 h-8 rounded-full border-2 border-white shadow-sm avatar-mobile">
                </div>
                <div class="typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div x-show="showInput" class="bg-white border-t p-4 input-mobile">
            <!-- Text Input -->
            <div x-show="inputType === 'text'" class="flex space-x-2">
                <div class="flex-1 relative">
                    <input 
                        id="user-input"
                        type="text" 
                        x-model="userInput"
                        @keydown.enter="handleInput(userInput)"
                        class="w-full pl-4 pr-12 py-3 border-2 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all shadow-sm search-mobile"
                        placeholder="Digite sua resposta...">
                    <button 
                        @click="handleInput(userInput)"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 w-10 h-10 flex items-center justify-center bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-sm send-button-mobile">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>

            <!-- Date Input -->
            <div x-show="inputType === 'date'" class="flex space-x-2">
                <input 
                    type="date" 
                    x-model="userInput"
                    @change="handleInput(userInput)"
                    class="w-full px-4 py-3 border-2 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all shadow-sm search-mobile">
            </div>

            <!-- Textarea Input -->
            <div x-show="inputType === 'textarea'" class="space-y-3">
                <textarea 
                    x-model="userInput"
                    rows="3"
                    class="w-full px-4 py-3 border-2 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all shadow-sm resize-none textarea-mobile"
                    placeholder="Descreva os detalhes da situação..."></textarea>
                <button 
                    @click="handleInput(userInput)"
                    class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-sm font-medium send-button-mobile">
                    Enviar Resposta
                </button>
            </div>

            <!-- Options Input -->
            <div x-show="inputType === 'options'" class="space-y-2">
                <template x-if="currentQuestion === 'infraction_type'">
                    <div class="space-y-4">
                        <div class="infraction-search">
                            <i class="fas fa-search"></i>
                            <input 
                                type="text" 
                                x-model="searchInfraction" 
                                placeholder="Busque por código ou descrição da infração..."
                                class="w-full px-4 py-3 pl-12 border-2 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all shadow-sm search-mobile">
                        </div>

                        <div class="infraction-categories categories-mobile">
                            <div 
                                @click="selectedCategory = 'all'" 
                                :class="{'active': selectedCategory === 'all'}"
                                class="category-tag category-tag-mobile">
                                Todas
                            </div>
                            <div 
                                @click="selectedCategory = 'velocidade'" 
                                :class="{'active': selectedCategory === 'velocidade'}"
                                class="category-tag category-tag-mobile">
                                Velocidade
                            </div>
                            <div 
                                @click="selectedCategory = 'documentacao'" 
                                :class="{'active': selectedCategory === 'documentacao'}"
                                class="category-tag category-tag-mobile">
                                Documentação
                            </div>
                            <div 
                                @click="selectedCategory = 'sinalizacao'" 
                                :class="{'active': selectedCategory === 'sinalizacao'}"
                                class="category-tag category-tag-mobile">
                                Sinalização
                            </div>
                            <div 
                                @click="selectedCategory = 'estacionamento'" 
                                :class="{'active': selectedCategory === 'estacionamento'}"
                                class="category-tag category-tag-mobile">
                                Estacionamento
                            </div>
                            <div 
                                @click="selectedCategory = 'conducao'" 
                                :class="{'active': selectedCategory === 'conducao'}"
                                class="category-tag category-tag-mobile">
                                Condução
                            </div>
                        </div>

                        <div class="max-h-96 overflow-y-auto space-y-3 pr-2">
                            <template x-if="filteredInfractions.length > 0">
                                <template x-for="option in filteredInfractions" :key="option.value">
                                    <button 
                                        @click="handleInput(option.value)"
                                        class="w-full text-left p-4 bg-white border-2 rounded-xl hover:border-blue-500 transition-all shadow-sm infraction-card infraction-card-mobile">
                                        <div class="flex items-start justify-between">
                                            <div class="space-y-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="infraction-code" x-text="option.code"></span>
                                                    <span 
                                                        :class="{
                                                            'severity-light': option.severity === 'light',
                                                            'severity-medium': option.severity === 'medium',
                                                            'severity-severe': option.severity === 'severe',
                                                            'severity-very_severe': option.severity === 'very_severe'
                                                        }"
                                                        class="severity-badge"
                                                        x-text="getSeverityText(option.severity)">
                                                    </span>
                                                </div>
                                                <p class="text-gray-700" x-text="option.description"></p>
                                                <div class="flex items-center gap-3 mt-2">
                                                    <span class="infraction-points">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        <span x-text="`${option.points} pontos`"></span>
                                                    </span>
                                                    <span class="infraction-amount">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                        <span x-text="`R$ ${option.base_amount}`"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <i class="fas fa-chevron-right"></i>
                                            </div>
                                        </div>
                                    </button>
                                </template>
                            </template>
                            <template x-if="filteredInfractions.length === 0">
                                <div class="no-results">
                                    <i class="fas fa-search"></i>
                                    <p>Nenhuma infração encontrada com os critérios informados.</p>
                                    <p class="text-sm mt-2">Tente usar palavras diferentes ou limpe o filtro.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="currentQuestion !== 'infraction_type'">
                    <div class="grid grid-cols-1 gap-2">
                        <template x-for="option in inputOptions" :key="option.value">
                            <button 
                                @click="handleInput(option.value)"
                                class="w-full text-left px-6 py-4 border-2 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition-all shadow-sm group option-mobile">
                                <span x-text="option.label" class="group-hover:text-blue-700 font-medium"></span>
                            </button>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Dados de infrações disponíveis globalmente para o Alpine.js
    window.infractionOptions = @json($infractionOptions);
</script>
<script src="/js/chat-wizard.js"></script>
@endpush