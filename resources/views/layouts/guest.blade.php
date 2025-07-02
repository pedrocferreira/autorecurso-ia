<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AutoRecurso') }}</title>

        <!-- Favicon -->
        <link rel="icon" href="/gavel-favicon.svg" type="image/svg+xml">
        <link rel="alternate icon" href="/favicon.ico">
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

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-slate-50 to-blue-50">
            <!-- Logo Section -->
            <div class="flex flex-col items-center mb-8">
                <a href="/" class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl flex items-center justify-center text-white shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <i class="fas fa-gavel text-2xl"></i>
                    </div>
                </a>
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-800 mb-1">
                        <span class="text-blue-600">Auto</span>Recurso
                    </h1>
                    <p class="text-sm text-gray-500">Contestação inteligente de multas</p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="w-full sm:max-w-md">
                <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
                {{ $slot }}
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <div class="flex items-center justify-center space-x-8 mb-4">
                    <div class="flex items-center text-xs text-gray-500">
                        <i class="fas fa-shield-alt text-green-500 mr-1"></i>
                        <span>Seguro</span>
                    </div>
                    <div class="flex items-center text-xs text-gray-500">
                        <i class="fas fa-bolt text-blue-500 mr-1"></i>
                        <span>Rápido</span>
                    </div>
                    <div class="flex items-center text-xs text-gray-500">
                        <i class="fas fa-star text-yellow-500 mr-1"></i>
                        <span>Confiável</span>
                    </div>
                </div>
                <p class="text-xs text-gray-400">
                &copy; {{ date('Y') }} AutoRecurso - Todos os direitos reservados
                </p>
            </div>
        </div>
    </body>
</html>
