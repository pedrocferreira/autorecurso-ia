<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        
        <!-- Favicon -->
        <link rel="icon" href="/gavel-favicon.svg" type="image/svg+xml">
        <link rel="manifest" href="/site.webmanifest">
        <meta name="msapplication-TileColor" content="#4361ee">
        <meta name="theme-color" content="#4361ee">

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-16970325896"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'AW-16970325896');
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- AOS Animation Library -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <!-- Alpine.js CDN -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Script para inicializar AOS -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true
                });
            });
        </script>
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="min-h-screen flex">
            @auth
                @include('layouts.navigation')
            @endauth
            <div class="flex-1 flex flex-col min-h-screen">
                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-gray-200/50 sticky top-0 z-20">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="flex-1">
                    <!-- Flash Messages com animações -->
                    @if(session('success'))
                        <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8" data-aos="fade-down">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-lg flex items-center" role="alert">
                                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                                <span class="font-medium">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8" data-aos="fade-down">
                            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-400 text-red-700 px-6 py-4 rounded-lg shadow-lg flex items-center" role="alert">
                                <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                                <span class="font-medium">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8" data-aos="fade-down">
                            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-l-4 border-yellow-400 text-yellow-700 px-6 py-4 rounded-lg shadow-lg flex items-center" role="alert">
                                <i class="fas fa-exclamation-circle text-yellow-500 text-xl mr-3"></i>
                                <span class="font-medium">{{ session('warning') }}</span>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </main>
                
                <!-- Footer Modernizado -->
                <footer class="bg-white/80 backdrop-blur-sm border-t border-gray-200/50 mt-16">
                    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <!-- Coluna 1: Informações da empresa -->
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <i class="fas fa-shield-alt text-blue-600 text-2xl mr-3"></i>
                                    <h3 class="text-xl font-bold text-gray-900">AutoRecurso</h3>
                                </div>
                                <p class="text-gray-600 leading-relaxed">
                                    Plataforma inteligente para geração de recursos administrativos contra multas de trânsito usando IA avançada.
                                </p>
                                <div class="flex space-x-4">
                                    <a href="mailto:contato@autorecurso.online" class="text-gray-400 hover:text-blue-600 transition-colors duration-200">
                                        <i class="fas fa-envelope text-lg"></i>
                                    </a>
                                    <a href="#" class="text-gray-400 hover:text-blue-600 transition-colors duration-200">
                                        <i class="fab fa-twitter text-lg"></i>
                                    </a>
                                    <a href="#" class="text-gray-400 hover:text-blue-600 transition-colors duration-200">
                                        <i class="fab fa-linkedin text-lg"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Coluna 2: Links úteis -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-900">Links Úteis</h3>
                                <ul class="space-y-3">
                                    <li>
                                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200 flex items-center group">
                                            <i class="fas fa-home mr-2 group-hover:translate-x-1 transition-transform duration-200"></i>
                                            Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('appeals.create_new') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200 flex items-center group">
                                            <i class="fas fa-file-alt mr-2 group-hover:translate-x-1 transition-transform duration-200"></i>
                                            Criar Recurso
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('credits.index') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200 flex items-center group">
                                            <i class="fas fa-coins mr-2 group-hover:translate-x-1 transition-transform duration-200"></i>
                                            Comprar Créditos
                                        </a>
                                    </li>
                                    <li>
                                        <a href="mailto:suporte@autorecurso.online" class="text-gray-600 hover:text-blue-600 transition-colors duration-200 flex items-center group">
                                            <i class="fas fa-headset mr-2 group-hover:translate-x-1 transition-transform duration-200"></i>
                                            Suporte
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Coluna 3: Documentos legais -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-900">Legal & Privacidade</h3>
                                <ul class="space-y-3">
                                    <li>
                                        <a href="{{ route('legal.privacy') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200 flex items-center group">
                                            <i class="fas fa-shield-alt mr-2 group-hover:translate-x-1 transition-transform duration-200"></i>
                                            Política de Privacidade
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('legal.terms') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200 flex items-center group">
                                            <i class="fas fa-file-contract mr-2 group-hover:translate-x-1 transition-transform duration-200"></i>
                                            Termos de Serviço
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('legal.cookies') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200 flex items-center group">
                                            <i class="fas fa-cookie-bite mr-2 group-hover:translate-x-1 transition-transform duration-200"></i>
                                            Política de Cookies
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('privacy.request') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200 flex items-center group">
                                            <i class="fas fa-user-shield mr-2 group-hover:translate-x-1 transition-transform duration-200"></i>
                                            Solicitar Dados (LGPD)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 mt-8 pt-8">
                            <div class="flex flex-col md:flex-row justify-between items-center">
                                <div class="text-sm text-gray-600">
                                    © {{ date('Y') }} AutoRecurso. Todos os direitos reservados.
                                </div>
                                <div class="text-sm text-gray-600 mt-4 md:mt-0">
                                    <span class="inline-flex items-center bg-green-50 text-green-700 px-3 py-1 rounded-full">
                                        <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                                        Protegido pela LGPD
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        
        <!-- Inicializar AOS -->
        <script>
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true,
                offset: 100
            });
        </script>
        
        @stack('scripts')
    </body>
</html> 