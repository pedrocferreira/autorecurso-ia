@extends('self-service.layout')

@section('title', 'AutoRecurso - Anule Suas Multas Automaticamente')

@push('styles')
<style>
.hero-bg {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border: none;
    padding: 1rem 2rem;
    color: white;
    font-weight: 600;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    transform: translateY(-2px);
}

.btn-secondary {
    background: white;
    border: 2px solid #3b82f6;
    padding: 1rem 2rem;
    color: #3b82f6;
    font-weight: 600;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-secondary:hover {
    background: #3b82f6;
    color: white;
}

.benefit-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
}

.stats-bg {
    background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%);
}
</style>
@endpush

@section('content')
<div class="min-h-screen">
    <!-- Hero Section -->
    <section class="hero-bg text-white py-20 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-6xl font-bold mb-6">
                Anule Suas Multas<br>
                <span class="text-yellow-300">Automaticamente</span>
            </h1>
            <p class="text-xl mb-8 max-w-3xl mx-auto opacity-90">
                Inteligência artificial especializada em recursos de multas de trânsito. 
                Mais de <strong>85% de sucesso</strong> em anulações. Recurso gerado em menos de <strong>10&nbsp;minutos</strong>.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                <a href="{{ route('cliente.wizard') }}" class="btn-primary text-lg">
                    <i class="fas fa-robot"></i>
                    Iniciar Análise Gratuita
                </a>
                <a href="#como-funciona" class="btn-secondary text-lg">
                    <i class="fas fa-play"></i>
                    Como Funciona
                </a>
            </div>
            
            <div class="flex flex-wrap justify-center gap-8 text-sm">
                <div class="benefit-badge">
                    <i class="fas fa-shield-check"></i>
                    100% Seguro
                </div>
                <div class="benefit-badge">
                    <i class="fas fa-clock"></i>
                    Recurso em 10&nbsp;min
                </div>
                <div class="benefit-badge">
                    <i class="fas fa-money-bill-wave"></i>
                    Pague Só Se Anular
                </div>
            </div>
        </div>
    </section>

    <!-- Estatísticas -->
    <section class="stats-bg py-16 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center text-white">
                <div>
                    <div class="text-4xl font-bold text-yellow-300">12.543</div>
                    <p class="text-lg opacity-90">Multas Anuladas</p>
                </div>
                <div>
                    <div class="text-4xl font-bold text-green-300">85%</div>
                    <p class="text-lg opacity-90">Taxa de Sucesso</p>
                </div>
                <div>
                    <div class="text-4xl font-bold text-blue-300">10&nbsp;min</div>
                    <p class="text-lg opacity-90">Tempo Médio</p>
                </div>
                <div>
                    <div class="text-4xl font-bold text-purple-300">R$ 2.8M</div>
                    <p class="text-lg opacity-90">Economizados</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Como Funciona -->
    <section id="como-funciona" class="py-20 px-4 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Como Funciona</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Em apenas 3 passos simples, nossa IA analisa sua multa e gera o recurso pronto em PDF para você imprimir e enviar
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-2xl font-bold">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Envie Sua Multa</h3>
                    <p class="text-gray-600">
                        Foto ou escaneie sua multa. Nossa IA extrai automaticamente todas as informações necessárias
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-2xl font-bold">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Análise Inteligente</h3>
                    <p class="text-gray-600">
                        Nossa IA analisa 150+ critérios técnicos e jurídicos para identificar falhas na autuação
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-2xl font-bold">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Baixe o Recurso</h3>
                    <p class="text-gray-600">
                        Você recebe o recurso em PDF pronto para imprimir e protocolar no órgão competente
                    </p>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('cliente.wizard') }}" class="btn-primary text-lg">
                    <i class="fas fa-play"></i>
                    Começar Agora - É Grátis
                </a>
            </div>
        </div>
    </section>

    <!-- Preços -->
    <section class="py-20 px-4 bg-gray-50">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Preço Único e Transparente</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Pague apenas pelo resultado. Se não anularmos sua multa, você não paga nada
                </p>
            </div>

            <div class="max-w-lg mx-auto">
                <div class="bg-gradient-to-br from-blue-600 to-purple-600 p-8 rounded-3xl shadow-xl text-white relative overflow-hidden">
                    <!-- Badge de Destaque -->
                    <div class="absolute top-4 right-4">
                        <div class="bg-yellow-400 text-blue-900 px-3 py-1 rounded-full text-sm font-bold">
                            MELHOR PREÇO
                        </div>
                    </div>
                    
                    <div class="text-center mb-8">
                        <h3 class="text-3xl font-bold mb-4">Recurso de Multa</h3>
                        <p class="opacity-90 mb-6">Para todos os tipos de multa de trânsito</p>
                        
                        <div class="mb-6">
                            <div class="text-6xl font-bold mb-2">R$ 29,90</div>
                            <p class="text-lg opacity-90">Só paga se conseguirmos anular</p>
                        </div>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-300 mr-3"></i>
                            <span>Análise completa por IA</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-300 mr-3"></i>
                            <span>PDF pronto para imprimir</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-300 mr-3"></i>
                            <span>Recurso em 10 minutos</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-300 mr-3"></i>
                            <span>Suporte técnico especializado</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-300 mr-3"></i>
                            <span>Garantia: Não anulou = Não paga</span>
                        </div>
                    </div>

                    <a href="{{ route('cliente.wizard') }}" class="btn-secondary w-full text-center justify-center text-lg py-4 mb-4">
                        <i class="fas fa-rocket mr-2"></i>
                        Iniciar Análise Gratuita
                    </a>
                    
                    <div class="text-center text-sm opacity-75">
                        <p>✅ Sem taxa de adesão • ✅ Sem mensalidade • ✅ Sem risco</p>
                    </div>
                </div>
            </div>

            <!-- Seção de benefícios do preço único -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
                <div class="bg-white p-6 rounded-2xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-check text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Zero Risco</h3>
                    <p class="text-sm text-gray-600">
                        Só cobramos se conseguirmos anular sua multa. Simples assim!
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calculator text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Economia Garantida</h3>
                    <p class="text-sm text-gray-600">
                        Com multas de R$ 130 a R$ 2.000+, você sempre economiza muito mais
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Resultado Rápido</h3>
                    <p class="text-sm text-gray-600">
                        Análise completa e geração do PDF do recurso em menos de 10 minutos
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section class="hero-bg text-white py-20 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl font-bold mb-6">Pronto Para Anular Sua Multa?</h2>
            <p class="text-xl mb-8 opacity-90">
                Mais de 12.000 clientes já economizaram milhões em multas. 
                Análise gratuita em 2 minutos.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('cliente.wizard') }}" class="btn-primary text-lg">
                    <i class="fas fa-rocket"></i>
                    Começar Análise Gratuita
                </a>
            </div>
            
            <div class="mt-8 flex justify-center gap-8 text-sm opacity-75">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Análise Gratuita</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Sem Risco</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Recurso em 10&nbsp;min</span>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection 