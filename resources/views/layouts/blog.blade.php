<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ isset($post) ? $post->title . ' - ' : '' }}Blog AutoRecurso</title>
    <meta name="description" content="{{ isset($post) && $post->meta_description ? $post->meta_description : 'Artigos e dicas sobre recursos administrativos e jurídicos' }}">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Estilos -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- MDB CSS para ripple e efeitos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    
    <!-- Estilos de correção para o blog -->
    <link rel="stylesheet" href="{{ asset('css/blog-fix.css') }}">
    
    <style>
        /* Estilos personalizados */
        .prose img {
            border-radius: 0.5rem;
            margin: 2rem 0;
            max-width: 100%;
            height: auto;
        }
        
        .prose h2 {
            margin-top: 2rem;
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e40af;
        }
        
        .prose p {
            margin-bottom: 1.25rem;
            font-size: 1.125rem;
            line-height: 1.75;
        }
        
        .prose ul, .prose ol {
            margin-left: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .prose a {
            color: #2563eb;
            text-decoration: underline;
        }
        
        .prose a:hover {
            color: #1e40af;
        }
        
        .blog-nav-link {
            color: #1f2937;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }
        
        .blog-nav-link:hover {
            color: #2563eb;
            background-color: #f3f4f6;
        }
        
        .blog-nav-link.active {
            color: #2563eb;
            background-color: #eff6ff;
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .post-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        
        .post-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Estilos globais para o conteúdo do post */
        .post-content {
            color: #000000 !important;
            background-color: #ffffff !important;
        }
        
        .post-content * {
            color: #000000 !important;
            background-color: transparent !important;
            text-shadow: none !important;
            font-family: Arial, Helvetica, sans-serif !important;
        }
        
        .post-content a {
            color: #1d4ed8 !important;
            text-decoration: underline !important;
        }
        
        /* Fixação de cores para garantir legibilidade */
        [class*="text-white"], [class*="text-gray"], [class*="text-slate"] {
            color: #000000 !important;
        }
        
        /* Garantir estilos adequados para elementos do blog */
        article p, article div, article span, article li {
            color: #000000 !important;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Conteúdo principal -->
    <main>
        @yield('content')
    </main>
    
    <!-- Rodapé -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:flex lg:items-center lg:justify-between lg:px-8">
            <div class="mb-8 lg:mb-0">
                <h2 class="text-2xl font-bold">AutoRecurso</h2>
                <p class="mt-2 text-gray-400">
                    Ajudando pessoas com recursos administrativos e jurídicos
                </p>
                <div class="mt-4 flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-facebook-f text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-twitter text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-instagram text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-linkedin-in text-lg"></i>
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-8 lg:col-span-2">
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-wider uppercase">
                        Navegação
                    </h3>
                    <ul class="mt-4 space-y-4">
                        <li>
                            <a href="{{ url('/') }}" class="text-base text-gray-400 hover:text-white flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i> Início
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('blog.index') }}" class="text-base text-gray-400 hover:text-white flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i> Blog
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-base text-gray-400 hover:text-white flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i> Contato
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-wider uppercase">
                        Legal
                    </h3>
                    <ul class="mt-4 space-y-4">
                        <li>
                            <a href="{{ route('terms') }}" class="text-base text-gray-400 hover:text-white flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i> Termos de Uso
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('privacy') }}" class="text-base text-gray-400 hover:text-white flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2"></i> Política de Privacidade
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-400">
                &copy; {{ date('Y') }} AutoRecurso. Todos os direitos reservados.
            </p>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS Bundle com Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- MDB JS para ripple e efeitos -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.js"></script>
    
    <script>
        // Script para correção de conteúdo de posts
        document.addEventListener('DOMContentLoaded', function() {
            // Fixação global de conteúdo de posts para garantir legibilidade
            function fixPostContentStyles() {
                const postContent = document.querySelector('.post-content');
                if (postContent) {
                    // Fixa cor de fundo
                    postContent.style.backgroundColor = '#ffffff';
                    postContent.style.color = '#000000';
                    
                    // Seleciona todos os elementos dentro do conteúdo
                    const elements = postContent.querySelectorAll('*');
                    elements.forEach(function(el) {
                        if (el.tagName === 'A') {
                            el.style.color = '#1d4ed8';
                        } else {
                            el.style.color = '#000000';
                        }
                        el.style.backgroundColor = 'transparent';
                        el.style.textShadow = 'none';
                    });
                }
            }
            
            // Executa a função quando a página é carregada
            fixPostContentStyles();
            
            // Executa novamente após 1 segundo para garantir que pegou todos os elementos
            setTimeout(fixPostContentStyles, 1000);
        });
    </script>
    
    <!-- Script de correção de legibilidade do blog -->
    <script src="{{ asset('js/blog-fix.js') }}"></script>
</body>
</html> 