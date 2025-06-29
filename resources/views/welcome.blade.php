<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="AutoRecurso - A maneira mais inteligente de gerar recursos de multas de trânsito usando Inteligência Artificial. Economize tempo e aumente suas chances de sucesso.">

        <title>AutoRecurso - Recursos de Multas com Inteligência Artificial</title>

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#4361ee">
        <meta name="msapplication-TileColor" content="#4361ee">
        <meta name="theme-color" content="#4361ee">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- AOS Animations -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

        <!-- Swiper CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

        <!-- Styles -->
        @vite(['resources/css/landing.css'])
    </head>
    <body class="antialiased">
        <!-- Header -->
        <header class="header">
            <div class="header-content">
                <a href="/" class="logo">
                    <i class="fas fa-shield-alt"></i>
                    AutoRecurso
                </a>
                
                <!-- Desktop Navigation -->
                <nav class="nav-links">
                    <a href="#como-funciona" class="nav-link">Como Funciona</a>
                    <a href="#precos" class="nav-link">Preços</a>
                    <a href="#faq" class="nav-link">FAQ</a>
                    <a href="{{ route('login') }}" class="nav-link">Entrar</a>
                    <a href="{{ route('register') }}" class="nav-link primary">Começar Grátis</a>
                </nav>

                <!-- Mobile Menu Toggle -->
                <div class="menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div class="mobile-menu">
                <nav class="mobile-nav-links">
                    <a href="#como-funciona" class="mobile-nav-link">Como Funciona</a>
                    <a href="#precos" class="mobile-nav-link">Preços</a>
                    <a href="#faq" class="mobile-nav-link">FAQ</a>
                    <a href="{{ route('login') }}" class="mobile-nav-link">Entrar</a>
                    <a href="{{ route('register') }}" class="mobile-nav-link">Começar Grátis</a>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="max-w-7xl mx-auto px-4">
                <!-- Trust Badge -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-lime-400/20 text-lime-100 rounded-full text-sm font-medium">
                        <i class="fas fa-star text-lime-400"></i>
                        Mais de 10.000 recursos aprovados
                        <i class="fas fa-star text-lime-400"></i>
                    </div>
                </div>

                <!-- Hero Content -->
                <div class="text-center">
                    <h1 class="hero-title" data-aos="fade-up">
                        Cancele suas multas<br>
                        <span class="bg-gradient-to-r from-lime-400 to-green-400 bg-clip-text text-transparent">sem sair de casa</span>
                    </h1>
                    <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100">
                        Gere recursos jurídicos personalizados em 5 minutos usando nossa IA especializada. 
                        <strong>95% de taxa de aprovação</strong> comprovada por mais de 10.000 motoristas.
                    </p>

                    <!-- Social Proof -->
                    <div class="flex justify-center items-center gap-6 mb-8" data-aos="fade-up" data-aos-delay="150">
                        <div class="flex items-center gap-2 text-white/90">
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 border-2 border-white"></div>
                                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-green-400 to-blue-500 border-2 border-white"></div>
                                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-400 to-pink-500 border-2 border-white"></div>
                            </div>
                            <span class="text-sm font-medium">+10.000 usuários</span>
                        </div>
                        <div class="flex items-center gap-1 text-lime-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span class="text-white/90 ml-1 text-sm">4.9/5 (2.847 avaliações)</span>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8" data-aos="fade-up" data-aos-delay="200">
                        <a href="{{ route('register') }}" class="cta-button cta-primary">
                            <i class="fas fa-rocket"></i>
                            Começar Agora - Grátis
                        </a>
                        <a href="#como-funciona" class="cta-button cta-secondary">
                            <i class="fas fa-play"></i>
                            Ver Demonstração
                        </a>
                    </div>

                    <!-- Badges -->
                    <div class="flex flex-wrap justify-center gap-3" data-aos="fade-up" data-aos-delay="250">
                        <div class="hero-badge">
                            <i class="fas fa-shield-check"></i>
                            100% Seguro
                        </div>
                        <div class="hero-badge">
                            <i class="fas fa-clock"></i>
                            Resultado em 5min
                        </div>
                        <div class="hero-badge">
                            <i class="fas fa-money-bill-wave"></i>
                            Garantia 7 dias
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <div class="max-w-7xl mx-auto px-6">
            <div class="stats-grid">
                <div class="stat-card" data-aos="fade-up">
                    <div class="stat-number">+10.000</div>
                    <div class="stat-label">Recursos Gerados</div>
                    <p class="text-sm text-gray-500 mt-2">Recursos aprovados nos últimos 12 meses</p>
                </div>
                <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-number">95%</div>
                    <div class="stat-label">Taxa de Aprovação</div>
                    <p class="text-sm text-gray-500 mt-2">Comprovado por dados reais de usuários</p>
                </div>
                <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-number">5 min</div>
                    <div class="stat-label">Tempo Médio</div>
                    <p class="text-sm text-gray-500 mt-2">Do cadastro ao recurso pronto para envio</p>
                </div>
            </div>
        </div>

        <!-- Como Funciona Section -->
        <section id="como-funciona" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <div class="section-label">Processo Simples</div>
                    <h2 class="section-heading">Como funciona em 3 passos</h2>
                    <p class="section-subheading">
                        Nosso processo foi otimizado para ser o mais rápido e eficiente possível
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center" data-aos="fade-up">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-700 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-6 shadow-lg">
                            1
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Cadastre sua multa</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Insira os dados da sua multa em nosso formulário inteligente. Levam apenas 2 minutos.
                        </p>
                    </div>

                    <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                        <div class="w-16 h-16 bg-gradient-to-br from-lime-500 to-lime-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-6 shadow-lg">
                            2
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">IA gera o recurso</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Nossa IA analisa seu caso e gera argumentos jurídicos personalizados e fundamentados.
                        </p>
                    </div>

                    <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-6 shadow-lg">
                            3
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Envie e aguarde</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Baixe o PDF pronto e envie para o órgão competente. Acompanhe o resultado.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <div class="section-label">Nossa tecnologia</div>
                    <h2 class="section-heading">Por que escolher o AutoRecurso?</h2>
                    <p class="section-subheading">
                        Combinamos inteligência artificial com conhecimento jurídico especializado
                    </p>
                </div>

                <div class="features-grid">
                    <div class="feature-card" data-aos="fade-up">
                        <div class="feature-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h3 class="feature-title">IA Especializada</h3>
                        <p class="feature-description">
                            Nossa IA foi treinada com milhares de recursos bem-sucedidos e conhece profundamente o Código de Trânsito Brasileiro.
                        </p>
                    </div>

                    <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h3 class="feature-title">Rápido e Preciso</h3>
                        <p class="feature-description">
                            Em menos de 5 minutos você tem um recurso completo, com argumentos técnicos e fundamentação jurídica sólida.
                        </p>
                    </div>

                    <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">95% de Sucesso</h3>
                        <p class="feature-description">
                            Taxa de aprovação comprovada de 95%. Nossos recursos são aceitos pelos órgãos de trânsito em todo o Brasil.
                        </p>
                    </div>

                    <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">100% Seguro</h3>
                        <p class="feature-description">
                            Seus dados são protegidos com criptografia de nível bancário. Nunca compartilhamos informações pessoais.
                        </p>
                    </div>

                    <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="feature-title">Suporte Especializado</h3>
                        <p class="feature-description">
                            Equipe de suporte especializada em direito de trânsito disponível para esclarecer dúvidas.
                        </p>
                    </div>

                    <div class="feature-card" data-aos="fade-up" data-aos-delay="500">
                        <div class="feature-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h3 class="feature-title">Economia Garantida</h3>
                        <p class="feature-description">
                            Economize até 90% comparado a contratar um advogado. Preço justo e transparente.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="precos" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <div class="section-label">Preços Transparentes</div>
                    <h2 class="section-heading">Escolha o plano ideal para você</h2>
                    <p class="section-subheading">
                        Sem mensalidades, sem surpresas. Pague apenas pelos recursos que usar.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <!-- Plano Básico -->
                    <div class="pricing-card" data-aos="fade-up">
                        <div class="pricing-header">
                            <h3 class="pricing-title">Teste Grátis</h3>
                            <div class="pricing-price">
                                <span class="text-4xl font-bold">R$ 0</span>
                                <span class="text-gray-500">/recurso</span>
                            </div>
                        </div>
                        <ul class="pricing-features">
                            <li><i class="fas fa-check text-green-500"></i> 1 recurso gratuito</li>
                            <li><i class="fas fa-check text-green-500"></i> IA básica</li>
                            <li><i class="fas fa-check text-green-500"></i> PDF para download</li>
                            <li><i class="fas fa-check text-green-500"></i> Suporte por email</li>
                        </ul>
                        <a href="{{ route('register') }}" class="pricing-button pricing-button-secondary">
                            Começar Grátis
                        </a>
                    </div>

                    <!-- Plano Popular -->
                    <div class="pricing-card pricing-card-popular" data-aos="fade-up" data-aos-delay="100">
                        <div class="pricing-badge">Mais Popular</div>
                        <div class="pricing-header">
                            <h3 class="pricing-title">Pacote Pro</h3>
                            <div class="pricing-price">
                                <span class="text-4xl font-bold">R$ 29</span>
                                <span class="text-gray-500">/5 recursos</span>
                            </div>
                            <div class="text-sm text-gray-500">R$ 5,80 por recurso</div>
                        </div>
                        <ul class="pricing-features">
                            <li><i class="fas fa-check text-green-500"></i> 5 recursos inclusos</li>
                            <li><i class="fas fa-check text-green-500"></i> IA avançada</li>
                            <li><i class="fas fa-check text-green-500"></i> Argumentos personalizados</li>
                            <li><i class="fas fa-check text-green-500"></i> Suporte prioritário</li>
                            <li><i class="fas fa-check text-green-500"></i> Garantia 30 dias</li>
                        </ul>
                        <a href="{{ route('register') }}" class="pricing-button pricing-button-primary">
                            Escolher Pro
                        </a>
                    </div>

                    <!-- Plano Premium -->
                    <div class="pricing-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="pricing-header">
                            <h3 class="pricing-title">Pacote Premium</h3>
                            <div class="pricing-price">
                                <span class="text-4xl font-bold">R$ 49</span>
                                <span class="text-gray-500">/10 recursos</span>
                            </div>
                            <div class="text-sm text-gray-500">R$ 4,90 por recurso</div>
                        </div>
                        <ul class="pricing-features">
                            <li><i class="fas fa-check text-green-500"></i> 10 recursos inclusos</li>
                            <li><i class="fas fa-check text-green-500"></i> IA premium</li>
                            <li><i class="fas fa-check text-green-500"></i> Revisão jurídica</li>
                            <li><i class="fas fa-check text-green-500"></i> Suporte WhatsApp</li>
                            <li><i class="fas fa-check text-green-500"></i> Garantia 60 dias</li>
                        </ul>
                        <a href="{{ route('register') }}" class="pricing-button pricing-button-secondary">
                            Escolher Premium
                        </a>
                    </div>
                </div>

                <div class="text-center mt-12">
                    <p class="text-gray-600 mb-4">💳 Aceitamos PIX, cartão de crédito e débito</p>
                    <p class="text-gray-600">🔒 Pagamento 100% seguro com criptografia SSL</p>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="testimonials-section">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <div class="section-label">Depoimentos</div>
                    <h2 class="section-heading">O que nossos clientes dizem</h2>
                    <p class="section-subheading">
                        Mais de 10.000 motoristas já economizaram tempo e dinheiro conosco
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="testimonial-card" data-aos="fade-up">
                        <div class="flex items-center gap-1 text-yellow-400 mb-4">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-content">
                            "Incrível! Consegui cancelar minha multa de R$ 293 em menos de 5 minutos. O recurso foi aprovado em 15 dias. Economizei tempo e dinheiro!"
                        </p>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                CS
                            </div>
                            <div class="author-info">
                                <div class="author-name">Carlos Silva</div>
                                <div class="author-location">São Paulo, SP</div>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="flex items-center gap-1 text-yellow-400 mb-4">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-content">
                            "A plataforma é muito fácil de usar e os argumentos gerados são extremamente bem fundamentados. Já usei 3 vezes e todas foram aprovadas!"
                        </p>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                AS
                            </div>
                            <div class="author-info">
                                <div class="author-name">Ana Santos</div>
                                <div class="author-location">Rio de Janeiro, RJ</div>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="flex items-center gap-1 text-yellow-400 mb-4">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-content">
                            "Recomendo para todos! Já economizei mais de R$ 800 em multas. O suporte é excelente e a taxa de aprovação é realmente alta."
                        </p>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                PO
                            </div>
                            <div class="author-info">
                                <div class="author-name">Pedro Oliveira</div>
                                <div class="author-location">Curitiba, PR</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="py-20 bg-white">
            <div class="max-w-4xl mx-auto px-6">
                <div class="text-center mb-16">
                    <div class="section-label">Dúvidas Frequentes</div>
                    <h2 class="section-heading">Perguntas e Respostas</h2>
                    <p class="section-subheading">
                        Esclarecemos as principais dúvidas sobre nosso serviço
                    </p>
                </div>

                <div class="space-y-6">
                    <div class="faq-item" data-aos="fade-up">
                        <button class="faq-question">
                            <span>Como funciona a garantia de aprovação?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <p>Nossa taxa de aprovação é de 95% baseada em dados reais dos últimos 12 meses. Caso seu recurso não seja aprovado por falha em nossa análise, devolvemos 100% do valor pago.</p>
                        </div>
                    </div>

                    <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                        <button class="faq-question">
                            <span>Quanto tempo demora para gerar o recurso?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <p>O processo completo leva em média 5 minutos. Você preenche os dados da multa e nossa IA gera instantaneamente um recurso personalizado pronto para envio.</p>
                        </div>
                    </div>

                    <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                        <button class="faq-question">
                            <span>É seguro compartilhar meus dados?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <p>Sim, completamente seguro. Utilizamos criptografia de nível bancário e nunca compartilhamos seus dados com terceiros. Somos compliance com a LGPD.</p>
                        </div>
                    </div>

                    <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                        <button class="faq-question">
                            <span>Funciona para qualquer tipo de multa?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <p>Nossa IA é especializada em multas de trânsito comuns como velocidade, estacionamento, semáforo, etc. Para infrações mais complexas, recomendamos consultar um advogado.</p>
                        </div>
                    </div>

                    <div class="faq-item" data-aos="fade-up" data-aos-delay="400">
                        <button class="faq-question">
                            <span>Preciso de conhecimento jurídico?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <p>Não! Nossa plataforma foi criada para pessoas sem conhecimento jurídico. Você só precisa inserir os dados da multa e nós cuidamos de todo o resto.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Section -->
        <section class="final-cta">
            <div class="max-w-4xl mx-auto px-6 text-center">
                <h2 class="final-cta-title">
                    Pronto para cancelar suas multas?
                </h2>
                <p class="final-cta-subtitle">
                    Junte-se a mais de 10.000 motoristas que já economizaram tempo e dinheiro com nossa plataforma. 
                    Comece gratuitamente agora mesmo!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                    <div class="floating">
                        <a href="{{ route('register') }}" class="cta-button cta-primary inline-flex">
                            <i class="fas fa-rocket"></i>
                            Começar Gratuitamente
                        </a>
                    </div>
                    <a href="#precos" class="cta-button cta-secondary inline-flex">
                        <i class="fas fa-tags"></i>
                        Ver Preços
                    </a>
                </div>
                
                <!-- Trust Indicators -->
                <div class="flex flex-wrap justify-center items-center gap-6 text-white/80 text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-shield-check"></i>
                        <span>Pagamento Seguro</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-undo"></i>
                        <span>Garantia 30 dias</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-headset"></i>
                        <span>Suporte Especializado</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <i class="fas fa-shield-alt text-2xl"></i>
                            <span class="text-xl font-bold">AutoRecurso</span>
                        </div>
                        <p class="text-gray-400 text-sm">
                            A maneira mais inteligente de gerar recursos de multas de trânsito usando Inteligência Artificial.
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-4">Produto</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><a href="#como-funciona" class="hover:text-white">Como Funciona</a></li>
                            <li><a href="#precos" class="hover:text-white">Preços</a></li>
                            <li><a href="#faq" class="hover:text-white">FAQ</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-4">Empresa</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><a href="#" class="hover:text-white">Sobre Nós</a></li>
                            <li><a href="#" class="hover:text-white">Contato</a></li>
                            <li><a href="#" class="hover:text-white">Blog</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-4">Legal</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><a href="#" class="hover:text-white">Termos de Uso</a></li>
                            <li><a href="#" class="hover:text-white">Política de Privacidade</a></li>
                            <li><a href="#" class="hover:text-white">LGPD</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <div class="text-sm text-gray-400">
                        © {{ date('Y') }} AutoRecurso. Todos os direitos reservados.
                    </div>
                    <div class="text-sm text-gray-400 mt-4 md:mt-0">
                        Desenvolvido com <i class="fas fa-heart text-red-500"></i> no Brasil
                    </div>
                </div>
            </div>
        </footer>

        <!-- Scripts -->
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({
                duration: 800,
                once: true,
            });

            // Mobile menu
            document.addEventListener('DOMContentLoaded', function() {
                const menuToggle = document.querySelector('.menu-toggle');
                const mobileMenu = document.querySelector('.mobile-menu');
                
                menuToggle.addEventListener('click', function() {
                    this.classList.toggle('active');
                    mobileMenu.classList.toggle('active');
                });

                document.querySelectorAll('.mobile-nav-link').forEach(link => {
                    link.addEventListener('click', () => {
                        menuToggle.classList.remove('active');
                        mobileMenu.classList.remove('active');
                    });
                });

                // FAQ Accordion
                document.querySelectorAll('.faq-question').forEach(button => {
                    button.addEventListener('click', () => {
                        const faqItem = button.parentElement;
                        const isActive = faqItem.classList.contains('active');
                        
                        // Close all FAQ items
                        document.querySelectorAll('.faq-item').forEach(item => {
                            item.classList.remove('active');
                        });
                        
                        // Open clicked item if it wasn't active
                        if (!isActive) {
                            faqItem.classList.add('active');
                        }
                    });
                });
            });
        </script>
    </body>
</html>

