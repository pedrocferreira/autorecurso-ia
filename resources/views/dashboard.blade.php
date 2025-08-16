@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header do Dashboard -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
            <div>
                    <h2 class="font-bold text-3xl lg:text-4xl text-gray-800 leading-tight">
                        Bem-vindo(a), <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">{{ Auth::user()->name }}</span>!
                </h2>
                    <p class="text-sm text-gray-600 mt-2 flex items-center">
                    <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                    {{ now()->format('d/m/Y') }} - Painel de controle do AutoRecurso
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-3 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    @if(Auth::user()->hasActiveSubscription())
                        <div class="font-bold">Assinatura Ativa</div>
                        <div class="text-blue-100 text-sm">Até {{ Auth::user()->subscription_ends_at?->format('d/m/Y') }}</div>
                    @else
                        <a href="{{ route('subscription.index') }}" class="font-bold underline">Assinar por R$ 150/mês</a>
                    @endif
                </div>
            </div>
        </div>
        </div>

            <!-- Banner informativo modernizado -->
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl shadow-2xl mb-8 overflow-hidden relative" data-aos="fade-up">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-12 -translate-x-12"></div>
                
                <div class="relative md:flex items-center p-8">
                    <div class="md:w-2/3 text-white">
                    <h3 class="text-2xl font-bold mb-3 flex items-center">
                        <i class="fas fa-rocket mr-2 text-yellow-300"></i>
                        Bem-vindo ao AutoRecurso
                    </h3>
                        <p class="mb-6 text-blue-100 leading-relaxed">
                            Use nossa inteligência artificial avançada para contestar multas de trânsito com maior chance de sucesso. 
                            Economize tempo e dinheiro com recursos personalizados e eficazes.
                        </p>
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                            <a href="{{ route('appeals.create_new') }}" id="btn-generate-appeal" 
                               class="inline-flex items-center px-6 py-3 bg-white text-blue-600 rounded-xl font-bold shadow-lg hover:bg-blue-50 transition-all duration-300 transform hover:scale-105 ring-2 ring-white/50 group">
                                <i class="fas fa-rocket mr-3 text-blue-500 group-hover:rotate-12 transition-transform duration-300"></i>
                                <div class="text-left">
                                    <div class="uppercase font-bold">Gerar Recurso</div>
                                    <div class="text-xs text-blue-400">Assinatura ativa necessária</div>
                                </div>
                            </a>
                            <a href="{{ route('subscription.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl font-medium shadow-lg border border-white/20 hover:bg-blue-700">
                                <i class="fas fa-id-card-alt mr-2"></i>
                                Assinar Plano
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:block md:w-1/3 relative">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="relative">
                                <i class="fas fa-gavel text-white text-8xl opacity-20 animate-pulse"></i>
                                <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-400 rounded-full blur-3xl opacity-30 animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas com cards modernos -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 mb-8 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                <div class="p-8">
                    <h3 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        Seu Progresso
                    </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="stats-grid">
                        <div class="group bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-ticket-alt text-white text-xl"></i>
                                </div>
                                <div class="text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <i class="fas fa-arrow-up text-sm"></i>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-blue-600 mb-1">{{ $stats['tickets_count'] ?? 0 }}</div>
                            <div class="text-sm text-blue-700 font-medium">Multas Cadastradas</div>
                        </div>
                        
                        <div class="group bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-file-alt text-white text-xl"></i>
                                </div>
                                <div class="text-green-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <i class="fas fa-arrow-up text-sm"></i>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-green-600 mb-1">{{ $stats['appeals_count'] ?? 0 }}</div>
                            <div class="text-sm text-green-700 font-medium">Recursos Gerados</div>
                        </div>
                        
                        <div class="group bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl border border-purple-200 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-check-circle text-white text-xl"></i>
                                </div>
                                <div class="text-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <i class="fas fa-arrow-up text-sm"></i>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-purple-600 mb-1">{{ $stats['appeals_successful'] ?? 0 }}</div>
                            <div class="text-sm text-purple-700 font-medium">Recursos com Sucesso</div>
                        </div>
                        
                        <div class="group bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-xl border border-yellow-200 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-coins text-white text-xl"></i>
                                </div>
                                <div class="text-yellow-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <i class="fas fa-arrow-up text-sm"></i>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-yellow-600 mb-1">{{ Auth::user()->hasActiveSubscription() ? 'Ativa' : 'Inativa' }}</div>
                            <div class="text-sm text-yellow-700 font-medium">Assinatura</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas modernizadas -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 mb-8 overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                <div class="p-8">
                    <h3 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                        <div class="w-10 h-10 bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-bolt text-white"></i>
                        </div>
                        Ações Rápidas
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <a href="{{ route('appeals.create_new') }}" 
                           class="group bg-gradient-to-br from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 p-6 rounded-xl border-2 border-green-300 hover:border-green-400 transition-all duration-300 hover:shadow-xl hover:-translate-y-2 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-green-400/20 rounded-full -translate-y-10 translate-x-10"></div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center group-hover:bg-green-600 transition-all duration-300 shadow-lg">
                                    <i class="fas fa-rocket text-white text-lg"></i>
                                </div>
                                <i class="fas fa-chevron-right text-green-400 group-hover:text-green-600 transition-colors duration-300"></i>
                            </div>
                            <h4 class="font-bold text-green-800 text-lg mb-2">Gerar Novo Recurso</h4>
                            <p class="text-green-600 text-sm">Incluso no plano</p>
                        </a>
                        
                        <a href="{{ route('subscription.index') }}" class="group bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-xl border-2 border-gray-200 transition-all duration-300 hover:shadow-lg">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-id-card-alt text-white text-lg"></i>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 text-lg"></i>
                            </div>
                            <h4 class="font-bold text-gray-700 text-lg mb-2">Assinar Plano</h4>
                            <p class="text-gray-500 text-sm">A partir de R$ 150/mês (descontos para mais meses)</p>
                        </a>
                    
                    <a href="{{ route('profile.edit') }}" 
                       class="group bg-gradient-to-br from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 p-6 rounded-xl border-2 border-blue-200 hover:border-blue-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center group-hover:bg-blue-600 transition-all duration-300 shadow-lg">
                                <i class="fas fa-user-cog text-white text-lg"></i>
                            </div>
                            <i class="fas fa-chevron-right text-blue-300 group-hover:text-blue-500 transition-colors duration-300"></i>
                        </div>
                        <h4 class="font-bold text-blue-800 text-lg mb-2">Configurar Perfil</h4>
                        <p class="text-blue-600 text-sm">Atualize seus dados pessoais</p>
                    </a>
                    </div>
                </div>
            </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Multas Recentes modernizadas -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden" data-aos="fade-up" data-aos-delay="300">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold flex items-center text-gray-800">
                                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-ticket-alt text-white"></i>
                                </div>
                                Multas Recentes
                            </h3>
                        <a href="{{ route('appeals.create_new') }}" 
                               class="inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="fas fa-rocket mr-2"></i> Gerar Recurso
                            </a>
                        </div>

                        @if(count($recent_tickets ?? []) > 0)
                            <div class="space-y-3">
                                @foreach($recent_tickets as $ticket)
                                    <div class="bg-gray-50 hover:bg-blue-50 rounded-lg p-4 transition-all duration-300 border border-gray-200 hover:border-blue-200 group">
                                    <a href="{{ route('appeals.create_new') }}?ticket_id={{ $ticket->id }}" class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200 transition-colors duration-300">
                                                    <i class="fas fa-car-side text-blue-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-800 group-hover:text-blue-600 transition-colors duration-300">
                                                        {{ $ticket->plate }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">{{ $ticket->date->format('d/m/Y') }}</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-bold text-gray-800">R$ {{ number_format($ticket->amount, 2, ',', '.') }}</div>
                                                <div class="text-xs text-gray-500">Valor da multa</div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4 text-right">
                            <a href="{{ route('appeals.create_new') }}" 
                                   class="text-sm text-blue-600 hover:text-blue-800 hover:underline flex items-center justify-end group">
                                Gerar recurso para todas
                                    <i class="fas fa-arrow-right ml-1 group-hover:translate-x-1 transition-transform duration-200"></i>
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-ticket-alt text-blue-500 text-2xl"></i>
                                </div>
                                <h4 class="text-gray-600 font-medium mb-2">Nenhuma multa cadastrada</h4>
                            <p class="text-gray-500 text-sm mb-4">Comece gerando seu primeiro recurso</p>
                            <a href="{{ route('appeals.create_new') }}" 
                                   class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-300">
                                <i class="fas fa-rocket mr-2"></i> Gerar Recurso
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recursos Recentes modernizados -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 overflow-hidden" data-aos="fade-up" data-aos-delay="400">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold flex items-center text-gray-800">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-file-alt text-white"></i>
                                </div>
                                Recursos Recentes
                            </h3>
                            <a href="{{ route('appeals.create_new') }}" 
                               class="inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <i class="fas fa-rocket mr-2"></i> Gerar Recurso
                            </a>
                        </div>

                        @if(count($recent_appeals ?? []) > 0)
                            <div class="space-y-3">
                                @foreach($recent_appeals as $appeal)
                                    <div class="bg-gray-50 hover:bg-green-50 rounded-lg p-4 transition-all duration-300 border border-gray-200 hover:border-green-200 group">
                                        <a href="{{ route('appeals.show', $appeal->id) }}" class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200 transition-colors duration-300">
                                                    <i class="fas fa-file-alt text-green-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-800 group-hover:text-green-600 transition-colors duration-300">
                                                        Recurso #{{ $appeal->id }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">{{ $appeal->created_at->format('d/m/Y') }}</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                            <div class="font-bold text-gray-800">
                                                @switch($appeal->status)
                                                    @case('pending')
                                                        <span class="text-yellow-600">Pendente</span>
                                                        @break
                                                    @case('sent')
                                                        <span class="text-blue-600">Enviado</span>
                                                        @break
                                                    @case('successful')
                                                        <span class="text-green-600">Aprovado</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="text-red-600">Rejeitado</span>
                                                        @break
                                                    @default
                                                        <span class="text-gray-600">{{ ucfirst($appeal->status) }}</span>
                                                @endswitch
                                            </div>
                                            <div class="text-xs text-gray-500">Status</div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4 text-right">
                            <a href="{{ route('appeals.create_new') }}" 
                                   class="text-sm text-green-600 hover:text-green-800 hover:underline flex items-center justify-end group">
                                Gerar novo recurso
                                    <i class="fas fa-arrow-right ml-1 group-hover:translate-x-1 transition-transform duration-200"></i>
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-file-alt text-green-500 text-2xl"></i>
                                </div>
                                <h4 class="text-gray-600 font-medium mb-2">Nenhum recurso gerado</h4>
                                <p class="text-gray-500 text-sm mb-4">Comece gerando seu primeiro recurso</p>
                                <a href="{{ route('appeals.create_new') }}" 
                                   class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-300">
                                    <i class="fas fa-rocket mr-2"></i> Gerar Recurso
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Script para animações e interações -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animar cards de estatísticas
    const statsCards = document.querySelectorAll('#stats-grid > div');
    statsCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('animate-fade-in');
    });

    // Efeito hover nos botões principais
    const mainButtons = document.querySelectorAll('#btn-generate-appeal');
    mainButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Removido contador de créditos
});
</script>

<style>
.animate-fade-in {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.group:hover .group-hover\:translate-y-1 {
    transform: translateY(-4px);
}

.group:hover .group-hover\:translate-y-2 {
    transform: translateY(-8px);
}
</style>
@endsection 