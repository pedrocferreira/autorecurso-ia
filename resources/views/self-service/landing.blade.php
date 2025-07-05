@extends('self-service.layout')

@section('title', 'AutoRecurso - Anule Suas Multas Automaticamente')

@push('styles')
<style>
/* Animações e efeitos personalizados */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 1;
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

.animate-bounce-in {
    animation: bounceIn 0.8s ease-out;
}

.gradient-animate {
    background-size: 200% 200%;
    animation: gradient 8s ease infinite;
}

.hero-bg {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    background-size: 200% 200%;
    animation: gradient 8s ease infinite;
}

/* Efeitos de hover */
.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.btn-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

/* Contadores */
.counter-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #3b82f6;
}

/* Testimonials */
.testimonial-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* FAQ */
.faq-item {
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem 0;
}

.faq-item:last-child {
    border-bottom: none;
}

/* Seção de preços */
.price-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.price-card:hover {
    border-color: #3b82f6;
    transform: translateY(-5px);
}

.price-card.featured {
    border-color: #3b82f6;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

/* Seção de como funciona */
.step-number {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.5rem;
    margin: 0 auto 1rem;
}

/* Badges de benefícios */
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

/* Seção de estatísticas */
.stats-bg {
    background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%);
}

/* Botões */
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
    box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
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

/* Responsividade */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.125rem;
    }
    
    .counter-number {
        font-size: 2rem;
    }
}
</style>
@endpush

@section('content')
<div x-data="landingPage()" class="min-h-screen">
    <!-- Hero Section -->
    <section class="hero-bg text-white py-20 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <div class="animate-fade-in-up">
                <h1 class="hero-title text-6xl font-bold mb-6">
                    Anule Suas Multas<br>
                    <span class="text-yellow-300">Automaticamente</span>
                </h1>
                <p class="hero-subtitle text-xl mb-8 max-w-3xl mx-auto opacity-90">
                    Inteligência artificial especializada em recursos de multas de trânsito. 
                    Mais de <strong>85% de sucesso</strong> em anulações. Resultado em até 48h.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                    <a href="{{ route('cliente.wizard') }}" class="btn-primary text-lg btn-pulse">
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
                        Resultado em 48h
                    </div>
                    <div class="benefit-badge">
                        <i class="fas fa-money-bill-wave"></i>
                        Pague Só Se Anular
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Estatísticas -->
    <section class="stats-bg py-16 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center text-white">
                <div class="animate-bounce-in">
                    <div class="counter-number text-yellow-300" x-text="animateCounter(12543)"></div>
                    <p class="text-lg opacity-90">Multas Anuladas</p>
                </div>
                <div class="animate-bounce-in" style="animation-delay: 0.2s">
                    <div class="counter-number text-green-300" x-text="'85%'"></div>
                    <p class="text-lg opacity-90">Taxa de Sucesso</p>
                </div>
                <div class="animate-bounce-in" style="animation-delay: 0.4s">
                    <div class="counter-number text-blue-300" x-text="'48h'"></div>
                    <p class="text-lg opacity-90">Tempo Médio</p>
                </div>
                <div class="animate-bounce-in" style="animation-delay: 0.6s">
                    <div class="counter-number text-purple-300" x-text="'R$ 2.8M'"></div>
                    <p class="text-lg opacity-90">Economizados</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefícios -->
    <section class="py-20 px-4 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Por Que Escolher a AutoRecurso?</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Nossa inteligência artificial analisa milhares de casos e encontra as melhores estratégias para anular sua multa
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card-hover bg-white p-8 rounded-2xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-brain text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Inteligência Artificial</h3>
                    <p class="text-gray-600">
                        Nossa IA analisa mais de 150 critérios técnicos e jurídicos para encontrar falhas na sua multa
                    </p>
                </div>

                <div class="card-hover bg-white p-8 rounded-2xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-gavel text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Especialistas Jurídicos</h3>
                    <p class="text-gray-600">
                        Equipe de advogados especializados em direito de trânsito validam e refinam cada recurso
                    </p>
                </div>

                <div class="card-hover bg-white p-8 rounded-2xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-rocket text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Resultado Rápido</h3>
                    <p class="text-gray-600">
                        Processo 100% automatizado garante análise e protocolo do seu recurso em até 48 horas
                    </p>
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
                    Em apenas 3 passos simples, nossa IA analisa sua multa e protocola o recurso automaticamente
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="text-center">
                    <div class="step-number">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Envie Sua Multa</h3>
                    <p class="text-gray-600">
                        Foto ou escaneie sua multa. Nossa IA extrai automaticamente todas as informações necessárias
                    </p>
                </div>

                <div class="text-center">
                    <div class="step-number">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Análise Inteligente</h3>
                    <p class="text-gray-600">
                        Nossa IA analisa 150+ critérios técnicos e jurídicos para identificar falhas na autuação
                    </p>
                </div>

                <div class="text-center">
                    <div class="step-number">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Recurso Automático</h3>
                    <p class="text-gray-600">
                        Protocolo automático do recurso nos órgãos competentes. Você recebe o resultado em até 48h
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
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Preços Transparentes</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Pague apenas pelo resultado. Se não anularmos sua multa, você não paga nada
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="price-card bg-white p-8 rounded-2xl shadow-lg">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Multa Leve</h3>
                        <p class="text-gray-600">Até R$ 130</p>
                    </div>
                    <div class="text-center mb-8">
                        <div class="text-4xl font-bold text-blue-600">R$ 39</div>
                        <p class="text-sm text-gray-600 mt-2">Só paga se anular</p>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Análise por IA</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Protocolo automático</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Resultado em 48h</span>
                        </li>
                    </ul>
                    <a href="{{ route('cliente.wizard') }}" class="btn-primary w-full text-center">
                        Escolher Plano
                    </a>
                </div>

                <div class="price-card featured bg-blue-600 p-8 rounded-2xl shadow-lg text-white">
                    <div class="text-center mb-6">
                        <div class="bg-yellow-400 text-blue-900 px-3 py-1 rounded-full text-sm font-bold mb-4">
                            MAIS POPULAR
                        </div>
                        <h3 class="text-2xl font-bold mb-2">Multa Média</h3>
                        <p class="opacity-90">R$ 131 - R$ 300</p>
                    </div>
                    <div class="text-center mb-8">
                        <div class="text-4xl font-bold">R$ 79</div>
                        <p class="text-sm opacity-90 mt-2">Só paga se anular</p>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-300 mr-3"></i>
                            <span>Análise por IA</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-300 mr-3"></i>
                            <span>Protocolo automático</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-300 mr-3"></i>
                            <span>Resultado em 48h</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-300 mr-3"></i>
                            <span>Suporte prioritário</span>
                        </li>
                    </ul>
                    <a href="{{ route('cliente.wizard') }}" class="btn-secondary w-full text-center">
                        Escolher Plano
                    </a>
                </div>

                <div class="price-card bg-white p-8 rounded-2xl shadow-lg">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Multa Grave</h3>
                        <p class="text-gray-600">Acima de R$ 300</p>
                    </div>
                    <div class="text-center mb-8">
                        <div class="text-4xl font-bold text-blue-600">R$ 129</div>
                        <p class="text-sm text-gray-600 mt-2">Só paga se anular</p>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Análise por IA</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Protocolo automático</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Resultado em 48h</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Suporte prioritário</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Consultor jurídico</span>
                        </li>
                    </ul>
                    <a href="{{ route('cliente.wizard') }}" class="btn-primary w-full text-center">
                        Escolher Plano
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Garantias -->
    <section class="py-20 px-4 bg-blue-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Suas Garantias</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Trabalhamos com total transparência e oferecemos as melhores garantias do mercado
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white p-6 rounded-2xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-check text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Pague Só Se Anular</h3>
                    <p class="text-sm text-gray-600">
                        Garantia de resultado: só cobramos se conseguirmos anular sua multa
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Prazo Garantido</h3>
                    <p class="text-sm text-gray-600">
                        Análise e protocolo em até 48h ou devolvemos seu dinheiro
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-shield text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Dados Protegidos</h3>
                    <p class="text-sm text-gray-600">
                        Seus dados são criptografados e protegidos conforme a LGPD
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Suporte 24/7</h3>
                    <p class="text-sm text-gray-600">
                        Atendimento especializado disponível todos os dias da semana
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Depoimentos -->
    <section class="py-20 px-4 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">O Que Nossos Clientes Dizem</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Mais de 12.000 clientes já economizaram milhões em multas
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="testimonial-card p-8 rounded-2xl">
                    <div class="flex items-center mb-6">
                        <img src="/images/avatar1.jpg" alt="Cliente" class="w-12 h-12 rounded-full mr-4" onerror="this.src='https://ui-avatars.com/api/?name=Maria+Silva&background=3b82f6&color=fff'">
                        <div>
                            <h4 class="font-bold text-gray-900">Maria Silva</h4>
                            <p class="text-sm text-gray-600">Empresária</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-700 italic">
                        "Incrível! Anularam minha multa de R$ 880 em apenas 36 horas. O processo foi super simples e transparente. Recomendo!"
                    </p>
                </div>

                <div class="testimonial-card p-8 rounded-2xl">
                    <div class="flex items-center mb-6">
                        <img src="/images/avatar2.jpg" alt="Cliente" class="w-12 h-12 rounded-full mr-4" onerror="this.src='https://ui-avatars.com/api/?name=Carlos+Santos&background=3b82f6&color=fff'">
                        <div>
                            <h4 class="font-bold text-gray-900">Carlos Santos</h4>
                            <p class="text-sm text-gray-600">Advogado</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-700 italic">
                        "Como advogado, fiquei impressionado com a qualidade técnica dos recursos. A IA encontrou falhas que eu não havia notado."
                    </p>
                </div>

                <div class="testimonial-card p-8 rounded-2xl">
                    <div class="flex items-center mb-6">
                        <img src="/images/avatar3.jpg" alt="Cliente" class="w-12 h-12 rounded-full mr-4" onerror="this.src='https://ui-avatars.com/api/?name=Ana+Costa&background=3b82f6&color=fff'">
                        <div>
                            <h4 class="font-bold text-gray-900">Ana Costa</h4>
                            <p class="text-sm text-gray-600">Professora</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-700 italic">
                        "Já usei 3 vezes e sempre tive sucesso. Economizei mais de R$ 1.200 em multas. Serviço excepcional!"
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Processo Legal -->
    <section class="py-20 px-4 bg-gradient-to-br from-blue-900 to-indigo-900 text-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Nosso Processo Legal</h2>
                <p class="text-xl opacity-90 max-w-3xl mx-auto">
                    Utilizamos as melhores práticas jurídicas e seguimos rigorosamente a legislação de trânsito
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white bg-opacity-10 p-8 rounded-2xl backdrop-blur-sm">
                    <div class="text-center mb-6">
                        <i class="fas fa-balance-scale text-4xl text-blue-300 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Base Legal Sólida</h3>
                    </div>
                    <ul class="space-y-3 text-sm opacity-90">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                            <span>Código de Trânsito Brasileiro (CTB)</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                            <span>Resoluções do CONTRAN</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                            <span>Jurisprudência atualizada</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                            <span>Precedentes dos tribunais</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white bg-opacity-10 p-8 rounded-2xl backdrop-blur-sm">
                    <div class="text-center mb-6">
                        <i class="fas fa-search text-4xl text-blue-300 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Análise Minuciosa</h3>
                    </div>
                    <ul class="space-y-3 text-sm opacity-90">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                            <span>Vícios de forma e conteúdo</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                            <span>Falhas na sinalização</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                            <span>Erros de calibração</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                            <span>Irregularidades procedimentais</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white bg-opacity-10 p-8 rounded-2xl backdrop-blur-sm">
                    <div class="text-center mb-6">
                        <i class="fas fa-file-alt text-4xl text-blue-300 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Documentação Completa</h3>
                    </div>
                    <ul class="space-y-3 text-sm opacity-90">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                            <span>Petição fundamentada</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                            <span>Provas técnicas</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                            <span>Laudo pericial quando necessário</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                            <span>Protocolo nos prazos legais</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white bg-opacity-10 p-8 rounded-2xl backdrop-blur-sm mt-12">
                <div class="flex items-center justify-center mb-6">
                    <i class="fas fa-certificate text-4xl text-yellow-300 mr-4"></i>
                    <div>
                        <h3 class="text-2xl font-bold">Certificações e Licenças</h3>
                        <p class="opacity-90">Nosso time é formado por profissionais certificados</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <div>
                        <i class="fas fa-graduation-cap text-2xl text-blue-300 mb-2"></i>
                        <p class="text-sm opacity-90">Advogados especializados em Direito de Trânsito</p>
                    </div>
                    <div>
                        <i class="fas fa-shield-alt text-2xl text-green-300 mb-2"></i>
                        <p class="text-sm opacity-90">Empresa registrada no CREA e OAB</p>
                    </div>
                    <div>
                        <i class="fas fa-award text-2xl text-yellow-300 mb-2"></i>
                        <p class="text-sm opacity-90">Certificação ISO 9001 em processos</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-20 px-4 bg-gray-50">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Dúvidas Frequentes</h2>
                <p class="text-xl text-gray-600">
                    Tire suas dúvidas sobre nosso serviço
                </p>
            </div>

            <div class="space-y-6">
                <div class="faq-item" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full text-left flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Como funciona a garantia "Pague só se anular"?</h3>
                        <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-transition class="mt-4 text-gray-600">
                        <p>Simples: você só paga se conseguirmos anular sua multa. Se o recurso for negado, você não paga nada. É nossa garantia de qualidade e confiança no nosso serviço.</p>
                    </div>
                </div>

                <div class="faq-item" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full text-left flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Qual é a taxa de sucesso de vocês?</h3>
                        <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-transition class="mt-4 text-gray-600">
                        <p>Nossa taxa de sucesso é de 85% em anulações. Isso é possível graças à nossa IA que analisa mais de 150 critérios técnicos e jurídicos para identificar falhas na autuação.</p>
                    </div>
                </div>

                <div class="faq-item" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full text-left flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Quanto tempo demora para ter resultado?</h3>
                        <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-transition class="mt-4 text-gray-600">
                        <p>Nosso processo é 100% automatizado. Em até 48 horas, analisamos sua multa e protocolamos o recurso. O resultado do órgão competente sai em até 45 dias.</p>
                    </div>
                </div>

                <div class="faq-item" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full text-left flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Vocês trabalham com todos os tipos de multa?</h3>
                        <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-transition class="mt-4 text-gray-600">
                        <p>Sim! Trabalhamos com todos os tipos de multa de trânsito: velocidade, documentação, sinalização, estacionamento, condução inadequada, entre outras.</p>
                    </div>
                </div>

                <div class="faq-item" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full text-left flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">É seguro enviar meus dados?</h3>
                        <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-transition class="mt-4 text-gray-600">
                        <p>Totalmente seguro! Utilizamos criptografia SSL e seguimos todas as normas da LGPD. Seus dados são protegidos e jamais compartilhados com terceiros.</p>
                    </div>
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
                <a href="{{ route('cliente.wizard') }}" class="btn-primary text-lg btn-pulse">
                    <i class="fas fa-rocket"></i>
                    Começar Análise Gratuita
                </a>
                <a href="#" class="btn-secondary text-lg" @click="$refs.contact.scrollIntoView()">
                    <i class="fas fa-phone"></i>
                    Falar com Consultor
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
                    <span>Resultado em 48h</span>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="/js/landing.js"></script>
<script>
function landingPage() {
    return {
        animateCounter(target) {
            // Simula um contador animado
            return target.toLocaleString('pt-BR');
        }
    }
}
</script>
@endpush 