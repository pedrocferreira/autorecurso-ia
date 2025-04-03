@extends('layouts.blog')

@section('content')
<!-- Header profissional -->
<header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto">
        <!-- Barra de navegação principal -->
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center">
                        <span class="text-2xl font-bold text-blue-700">Auto<span class="text-gray-800">Recurso</span></span>
                    </a>
                    <nav class="hidden md:ml-10 md:flex md:space-x-8">
                        <a href="{{ url('/') }}" class="text-gray-500 hover:text-gray-900 px-3 py-2 text-sm font-medium">Início</a>
                        <a href="#" class="text-gray-500 hover:text-gray-900 px-3 py-2 text-sm font-medium">Serviços</a>
                        <a href="#" class="text-blue-700 hover:text-blue-800 px-3 py-2 text-sm font-medium border-b-2 border-blue-700">Blog</a>
                        <a href="#" class="text-gray-500 hover:text-gray-900 px-3 py-2 text-sm font-medium">Sobre Nós</a>
                        <a href="#" class="text-gray-500 hover:text-gray-900 px-3 py-2 text-sm font-medium">Contato</a>
                    </nav>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('appeals.create_new') }}" class="ml-8 inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Iniciar Recurso
                    </a>
                    <button type="button" id="mobile-menu-button" class="md:hidden ml-2 bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" aria-expanded="false">
                        <span class="sr-only">Abrir menu</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Menu móvel (inicialmente oculto) -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ url('/') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                    <i class="fas fa-home mr-1"></i> Início
                </a>
                <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                    <i class="fas fa-briefcase mr-1"></i> Serviços
                </a>
                <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-blue-600 bg-blue-50">
                    <i class="fas fa-book mr-1"></i> Blog
                </a>
                <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                    <i class="fas fa-users mr-1"></i> Sobre Nós
                </a>
                <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                    <i class="fas fa-envelope mr-1"></i> Contato
                </a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="px-2 space-y-1">
                    <a href="{{ route('appeals.create_new') }}" class="block w-full px-3 py-2 rounded-md text-base font-medium text-white bg-blue-600 hover:bg-blue-700 text-center">
                        <i class="fas fa-clipboard-list mr-1"></i> Iniciar Recurso
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Breadcrumbs -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ url('/') }}" class="hover:text-blue-600 transition-colors">Início</a>
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-gray-900">Blog</span>
        </div>
    </div>
</div>

<!-- Conteúdo Principal usando o estilo do Bootstrap News Feed -->
<div class="bg-light py-5">
    <div class="container">
        <!-- Post em Destaque - Estilo News Feed -->
        @if($posts->isNotEmpty() && $posts->first())
            <div class="row gx-5 mb-5">
                <div class="col-md-6 mb-4">
                    <div class="bg-image hover-overlay ripple shadow-2-strong rounded-5" data-mdb-ripple-color="light">
                        @if($posts->first()->featured_image)
                            <img src="{{ asset('storage/' . $posts->first()->featured_image) }}" 
                                alt="{{ $posts->first()->title }}" 
                                class="img-fluid rounded-5">
                        @else
                            <div class="bg-primary rounded-5 d-flex justify-content-center align-items-center" style="height: 320px;">
                                <i class="fas fa-newspaper fa-4x text-white opacity-70"></i>
                            </div>
                        @endif
                        <a href="{{ route('blog.show', $posts->first()->slug) }}">
                            <div class="mask" style="background-color: rgba(251, 251, 251, 0.15);"></div>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <span class="badge bg-danger px-2 py-1 shadow-1-strong mb-3">Destaque do dia</span>
                    <h4><strong>
                        <a href="{{ route('blog.show', $posts->first()->slug) }}" class="text-dark">
                            {{ $posts->first()->title }}
                        </a>
                    </strong></h4>
                    @if($posts->first()->excerpt)
                        <p class="text-muted">
                            {{ $posts->first()->excerpt }}
                        </p>
                    @endif
                    <div class="d-flex align-items-center text-muted mb-3 small">
                        <i class="far fa-calendar-alt me-1"></i>
                        <span>{{ $posts->first()->published_at->format('d/m/Y') }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $posts->first()->published_at->diffForHumans() }}</span>
                    </div>
                    <a href="{{ route('blog.show', $posts->first()->slug) }}" class="btn btn-primary">Ler mais</a>
                </div>
            </div>
        @endif

        <!-- Banner de Propaganda -->
        <div class="bg-light text-center py-3 mb-5 border rounded">
            <div class="text-muted">
                <small>Espaço para Propaganda (728x90)</small>
            </div>
        </div>

        <!-- Grade de Posts no estilo News Feed -->
        <div class="row">
            <!-- Coluna Principal -->
            <div class="col-md-8">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Últimos Artigos</h5>
                    </div>
                    
                    <div class="card-body pb-0">
                        @if($posts->skip(1)->count() > 0)
                            @foreach($posts->skip(1) as $post)
                                <div class="row mb-4 pb-4 border-bottom">
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <div class="bg-image hover-overlay ripple shadow-sm rounded" data-mdb-ripple-color="light">
                                            @if($post->featured_image)
                                                <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                    alt="{{ $post->title }}" 
                                                    class="img-fluid rounded">
                                            @else
                                                <div class="bg-primary rounded d-flex justify-content-center align-items-center" style="height: 180px;">
                                                    <i class="fas fa-newspaper fa-2x text-white opacity-70"></i>
                                                </div>
                                            @endif
                                            <a href="{{ route('blog.show', $post->slug) }}">
                                                <div class="mask" style="background-color: rgba(251, 251, 251, 0.15);"></div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h5 class="fw-bold mb-2">
                                            <a href="{{ route('blog.show', $post->slug) }}" class="text-dark text-decoration-none">
                                                {{ $post->title }}
                                            </a>
                                        </h5>
                                        @if($post->excerpt)
                                            <p class="text-muted mb-3">
                                                {{ \Illuminate\Support\Str::limit($post->excerpt, 120) }}
                                            </p>
                                        @endif
                                        <div class="d-flex align-items-center text-muted small mb-3">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <span>{{ $post->published_at->format('d/m/Y') }}</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $post->published_at->diffForHumans() }}</span>
                                        </div>
                                        <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-sm btn-outline-primary">Ler mais</a>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if($posts->isEmpty())
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="far fa-newspaper fa-3x text-primary opacity-70"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Nenhum artigo publicado</h5>
                                <p class="text-muted">Em breve publicaremos novos conteúdos.</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Paginação estilo Bootstrap -->
                    <div class="card-footer bg-white py-3">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
            
            <!-- Sidebar estilo Bootstrap News Feed -->
            <div class="col-md-4">
                <!-- Banner de Propaganda -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-3 bg-light text-center">
                        <small class="text-muted">Espaço para Propaganda (300x250)</small>
                    </div>
                </div>
                
                <!-- Newsletter -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Receba Atualizações</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Seu e-mail" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 d-block">Inscrever-se</button>
                        </form>
                    </div>
                </div>
                
                <!-- Posts Populares estilo Bootstrap News Feed -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Posts Populares</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($posts->take(5) as $post)
                                <li class="list-group-item bg-transparent">
                                    <div class="row g-0">
                                        <div class="col-4 col-md-3">
                                            @if($post->featured_image)
                                                <img src="{{ asset('storage/' . $post->featured_image) }}" class="img-fluid rounded" alt="{{ $post->title }}">
                                            @else
                                                <div class="bg-primary rounded text-center text-white d-flex align-items-center justify-content-center" style="height: 60px;">
                                                    <i class="fas fa-newspaper"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-8 col-md-9">
                                            <div class="ms-3">
                                                <a href="{{ route('blog.show', $post->slug) }}" class="text-dark text-decoration-none fw-bold small d-block mb-1">
                                                    {{ \Illuminate\Support\Str::limit($post->title, 45) }}
                                                </a>
                                                <small class="text-muted">{{ $post->published_at->format('d/m/Y') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                
                <!-- Banner de Propaganda -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-3 bg-light text-center" style="height: 300px;">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <small class="text-muted">Espaço para Propaganda (300x250)</small>
                        </div>
                    </div>
                </div>
                
                <!-- Categorias estilo Bootstrap -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Categorias</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-transparent border-bottom py-3">
                                <span>Recursos Administrativos</span>
                                <span class="badge bg-primary rounded-pill">12</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-transparent border-bottom py-3">
                                <span>Direito do Trabalho</span>
                                <span class="badge bg-primary rounded-pill">8</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-transparent border-bottom py-3">
                                <span>Direito Administrativo</span>
                                <span class="badge bg-primary rounded-pill">15</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-transparent">
                                <span>Dicas Jurídicas</span>
                                <span class="badge bg-primary rounded-pill">6</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para o menu mobile -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    });
</script>
@endsection 