<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'AutoRecurso - Geração de Recursos de Multas com IA')</title>
        <meta name="description" content="@yield('meta_description', 'AutoRecurso - Sistema inteligente para geração de recursos de multas de trânsito. Recursos personalizados com alta taxa de aprovação.')">
        
        <!-- Meta Tags Adicionais -->
        <meta name="author" content="AutoRecurso">
        <meta name="robots" content="index, follow">
        <meta name="keywords" content="recurso de multa, multa de trânsito, recurso detran, recurso online, contestar multa">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="@yield('title', 'AutoRecurso - Geração de Recursos de Multas com IA')">
        <meta property="og:description" content="@yield('meta_description', 'AutoRecurso - Sistema inteligente para geração de recursos de multas de trânsito. Recursos personalizados com alta taxa de aprovação.')">
        <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="@yield('title', 'AutoRecurso - Geração de Recursos de Multas com IA')">
        <meta name="twitter:description" content="@yield('meta_description', 'AutoRecurso - Sistema inteligente para geração de recursos de multas de trânsito. Recursos personalizados com alta taxa de aprovação.')">
        <meta name="twitter:image" content="{{ asset('images/og-image.jpg') }}">

        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <meta name="theme-color" content="#2563eb">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Schema.org / JSON-LD -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "AutoRecurso",
            "description": "Sistema inteligente para geração de recursos de multas de trânsito",
            "url": "{{ url('/') }}",
            "applicationCategory": "LegalServices",
            "operatingSystem": "All",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "BRL"
            },
            "creator": {
                "@type": "Organization",
                "name": "AutoRecurso",
                "url": "{{ url('/') }}"
            }
        }
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Navigation -->
            <nav class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-center h-20 px-4 sm:px-6 lg:px-8">
                        <!-- Container da Esquerda -->
                        <div class="flex-1 flex justify-start">
                            <a href="{{ route('welcome') }}" class="flex items-center group">
                                <div class="w-12 h-12 flex items-center justify-center bg-blue-50 rounded-xl group-hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-gavel text-blue-600 text-2xl"></i>
                                </div>
                                <span class="ml-4 text-xl font-semibold text-gray-900">AutoRecurso</span>
                            </a>
                        </div>

                        <!-- Container Central - pode ser usado para menu no futuro -->
                        <div class="flex-1 flex justify-center">
                            
                        </div>

                        <!-- Container da Direita -->
                        <div class="flex-1 flex justify-end items-center gap-6">
                            @auth
                                <a href="{{ url('/dashboard') }}" 
                                   class="inline-flex items-center px-6 py-2.5 bg-blue-600 border border-transparent rounded-xl text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                                    <i class="fas fa-tachometer-alt mr-2.5 text-blue-200"></i>
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none transition-colors">
                                    <i class="fas fa-sign-in-alt mr-2.5"></i>
                                    Entrar
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" 
                                       class="inline-flex items-center px-6 py-2.5 bg-blue-600 border border-transparent rounded-xl text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                                        <i class="fas fa-user-plus mr-2.5 text-blue-200"></i>
                                        Registrar
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            © {{ date('Y') }} AutoRecurso - Todos os direitos reservados
                        </div>
                        <div class="flex space-x-6">
                            <a href="{{ route('terms') }}" class="text-sm text-gray-500 hover:text-gray-700">
                                Termos de Serviço
                            </a>
                            <a href="{{ route('privacy') }}" class="text-sm text-gray-500 hover:text-gray-700">
                                Política de Privacidade
                            </a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html> 