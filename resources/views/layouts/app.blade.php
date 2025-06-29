<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @if(session('success'))
                    <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('warning') }}</span>
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>
            
            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 mt-16">
                <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Coluna 1: Informações da empresa -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">AutoRecurso</h3>
                            <p class="text-gray-600 text-sm mb-4">
                                Plataforma inteligente para geração de recursos administrativos contra multas de trânsito.
                            </p>
                            <div class="flex space-x-4">
                                <a href="mailto:contato@autorecurso.online" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-envelope"></i>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-gray-600">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-gray-600">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Coluna 2: Links úteis -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Links Úteis</h3>
                            <ul class="space-y-2">
                                <li><a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm">Dashboard</a></li>
                                <li><a href="{{ route('appeals.create_new') }}" class="text-gray-600 hover:text-gray-900 text-sm">Criar Recurso</a></li>
                                <li><a href="{{ route('credits.index') }}" class="text-gray-600 hover:text-gray-900 text-sm">Comprar Créditos</a></li>
                                <li><a href="mailto:suporte@autorecurso.online" class="text-gray-600 hover:text-gray-900 text-sm">Suporte</a></li>
                            </ul>
                        </div>

                        <!-- Coluna 3: Documentos legais -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Legal & Privacidade</h3>
                            <ul class="space-y-2">
                                <li><a href="{{ route('legal.privacy') }}" class="text-gray-600 hover:text-gray-900 text-sm">Política de Privacidade</a></li>
                                <li><a href="{{ route('legal.terms') }}" class="text-gray-600 hover:text-gray-900 text-sm">Termos de Serviço</a></li>
                                <li><a href="{{ route('legal.cookies') }}" class="text-gray-600 hover:text-gray-900 text-sm">Política de Cookies</a></li>
                                <li><a href="{{ route('privacy.request') }}" class="text-gray-600 hover:text-gray-900 text-sm">Solicitar Dados (LGPD)</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 mt-8 pt-8">
                        <div class="flex flex-col md:flex-row justify-between items-center">
                            <div class="text-sm text-gray-600">
                                © {{ date('Y') }} AutoRecurso. Todos os direitos reservados.
                            </div>
                            <div class="text-sm text-gray-600 mt-4 md:mt-0">
                                <span class="inline-flex items-center">
                                    <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                                    Protegido pela LGPD
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        
        @stack('scripts')
    </body>
</html>
