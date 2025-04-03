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
                        <a href="{{ url('/') }}" class="text-gray-500 hover:text-gray-900 px-3 py-2 text-sm font-medium">Serviços</a>
                        <a href="{{ url('/blog') }}" class="text-blue-700 hover:text-blue-800 px-3 py-2 text-sm font-medium border-b-2 border-blue-700">Blog</a>
                        <a href="{{ url('/') }}" class="text-gray-500 hover:text-gray-900 px-3 py-2 text-sm font-medium">Sobre Nós</a>
                        <a href="{{ url('/') }}" class="text-gray-500 hover:text-gray-900 px-3 py-2 text-sm font-medium">Contato</a>
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
                <a href="{{ url('/') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                    <i class="fas fa-briefcase mr-1"></i> Serviços
                </a>
                <a href="{{ url('/blog') }}" class="block px-3 py-2 rounded-md text-base font-medium text-blue-600 bg-blue-50">
                    <i class="fas fa-book mr-1"></i> Blog
                </a>
                <a href="{{ url('/') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                    <i class="fas fa-users mr-1"></i> Sobre Nós
                </a>
                <a href="{{ url('/') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600">
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

<!-- Header do Post -->
<div class="container mt-5 pt-5">
    <div class="row">
        <div class="col-md-8 mb-4">
            <!-- Breadcrumbs -->
            <nav class="d-flex mb-4">
                <h6 class="mb-0">
                    <a href="{{ url('/') }}" class="text-reset text-decoration-none">Início</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('blog.index') }}" class="text-reset text-decoration-none">Blog</a>
                    <span class="mx-1">/</span>
                    <span class="text-primary">{{ $post->title }}</span>
                </h6>
            </nav>

            <!-- Post Content -->
            <div class="card mb-4 border-0 shadow-sm">
                <!-- Featured Image -->
                @if($post->featured_image)
                    <div class="bg-image hover-overlay ripple" data-mdb-ripple-color="light">
                        <img src="{{ asset('storage/' . $post->featured_image) }}" 
                             alt="{{ $post->title }}" 
                             class="img-fluid rounded-top" />
                        <a href="#">
                            <div class="mask" style="background-color: rgba(251, 251, 251, 0.15);"></div>
                        </a>
                    </div>
                @else
                    <div class="bg-primary text-white d-flex align-items-center justify-content-center py-5">
                        <i class="fas fa-newspaper fa-4x opacity-70"></i>
                    </div>
                @endif

                <div class="card-body">
                    <!-- Post metadata -->
                    <div class="d-flex mb-3">
                        <img src="https://ui-avatars.com/api/?name=Admin&background=E3F2FD&color=1E88E5&size=256" 
                             class="rounded-circle me-3" style="width: 60px; height: 60px" alt="Autor" />
                        <div>
                            <a href="#" class="text-dark mb-0">
                                <strong>Admin</strong>
                            </a>
                            <div class="text-muted small">
                                <i class="far fa-clock pe-1"></i> {{ $post->published_at->format('d/m/Y') }} · 
                                {{ $post->published_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    <!-- Post title -->
                    <h1 class="card-title mb-4 fw-bold">{{ $post->title }}</h1>

                    <!-- Post excerpt -->
                    @if($post->excerpt)
                        <div class="border-start border-5 border-primary ps-3 mb-4">
                            <p class="lead mb-0 font-italic">{{ $post->excerpt }}</p>
                        </div>
                    @endif

                    <!-- Post content -->
                    <div class="post-content">
                        {!! $post->content !!}
                    </div>

                    <!-- Post tags -->
                    <div class="mt-4 mb-4">
                        <span class="badge bg-primary me-1">Recursos</span>
                        <span class="badge bg-primary me-1">Administrativos</span>
                        <span class="badge bg-primary me-1">Jurídicos</span>
                    </div>

                    <!-- Share buttons -->
                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Compartilhar:</h5>
                        <div>
                            <button type="button" class="btn btn-primary btn-sm me-1">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button type="button" class="btn btn-info btn-sm text-white me-1">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button type="button" class="btn btn-success btn-sm me-1">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button type="button" class="btn btn-light btn-sm">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Author box -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="col-md-2 col-4 text-center">
                            <img src="https://ui-avatars.com/api/?name=Admin&background=E3F2FD&color=1E88E5&size=256" 
                                 class="rounded-circle img-fluid" alt="Autor" />
                        </div>
                        <div class="col-md-10 col-8">
                            <h5 class="fw-bold mb-2">Administrador</h5>
                            <p class="text-muted mb-0">
                                Editor do blog AutoRecurso com experiência em recursos administrativos e jurídicos.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Similar Posts -->
            @if(count($relatedPosts) > 0)
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Posts Similares</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($relatedPosts as $relatedPost)
                        <div class="col-lg-6 mb-4">
                            <div class="row g-0">
                                <div class="col-md-4 col-4">
                                    @if($relatedPost->featured_image)
                                        <img src="{{ asset('storage/' . $relatedPost->featured_image) }}" 
                                             class="img-fluid rounded" alt="{{ $relatedPost->title }}">
                                    @else
                                        <div class="bg-primary rounded text-white d-flex align-items-center justify-content-center" style="height: 100%;">
                                            <i class="fas fa-newspaper"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-8 col-8">
                                    <div class="ps-3">
                                        <a href="{{ route('blog.show', $relatedPost->slug) }}" class="text-dark text-decoration-none">
                                            <h6 class="fw-bold mb-1">{{ \Illuminate\Support\Str::limit($relatedPost->title, 50) }}</h6>
                                        </a>
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>{{ $relatedPost->published_at->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Navegação -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('blog.index') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i>Voltar ao blog</a>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4 mb-4">
            <!-- Search -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Buscar no Blog</h5>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar..." />
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Categories -->
            <div class="card mb-4 border-0 shadow-sm">
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

            <!-- Banner de Contato -->
            <div class="card text-white bg-primary border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold">Precisa de ajuda com recursos?</h5>
                    <p class="mb-4 opacity-75">A equipe do AutoRecurso está pronta para ajudar você a resolver seus processos administrativos.</p>
                    <a href="#" class="btn btn-light text-primary fw-bold">
                        <i class="fas fa-comment-alt me-2"></i>Fale Conosco
                    </a>
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