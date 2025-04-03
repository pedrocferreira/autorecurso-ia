@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Editar Post</h1>
    </div>

    <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Título</label>
            <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            @error('title')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="excerpt" class="block text-gray-700 text-sm font-bold mb-2">Resumo</label>
            <textarea name="excerpt" id="excerpt" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('excerpt', $post->excerpt) }}</textarea>
            @error('excerpt')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Conteúdo</label>
            <textarea name="content" id="content" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ old('content', $post->content) }}</textarea>
            @error('content')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="featured_image" class="block text-gray-700 text-sm font-bold mb-2">Imagem Destacada</label>
            @if($post->featured_image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="Imagem atual" class="h-32 w-auto">
                </div>
            @endif
            <input type="file" name="featured_image" id="featured_image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('featured_image')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="meta_title" class="block text-gray-700 text-sm font-bold mb-2">Meta Título (SEO)</label>
            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $post->meta_title) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('meta_title')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="meta_description" class="block text-gray-700 text-sm font-bold mb-2">Meta Descrição (SEO)</label>
            <textarea name="meta_description" id="meta_description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('meta_description', $post->meta_description) }}</textarea>
            @error('meta_description')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="published_at" class="block text-gray-700 text-sm font-bold mb-2">Data de Publicação</label>
            <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('published_at')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.posts.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cancelar
            </a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Atualizar Post
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.tiny.cloud/1/b9u9tvz9pe4cp3czwxhuv3zg9x1cprmyzsrum9e2tesamsfg/tinymce/5/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 500,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic backcolor forecolor | \
                alignleft aligncenter alignright alignjustify | \
                bullist numlist outdent indent | removeformat | help',
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
            
            // Definir cores padrão e estilos
            editor.on('init', function() {
                editor.getBody().style.backgroundColor = '#ffffff';
                editor.getBody().style.color = '#000000';
                editor.getBody().style.fontFamily = 'Arial, Helvetica, sans-serif';
            });
        },
        // Configurações adicionais para melhorar a formatação
        content_style: 'body { font-family: Arial, Helvetica, sans-serif; color: #000000; background-color: #ffffff; } p, div, span, li { color: #000000; } h1, h2, h3, h4, h5, h6 { color: #000000; }',
        formats: {
            bold: { inline: 'strong', styles: { color: '#000000' } },
            italic: { inline: 'em', styles: { color: '#000000' } }
        },
        // Limitar as cores disponíveis para o editor
        color_map: [
            "#000000", "Preto", 
            "#4a4a4a", "Cinza escuro",
            "#666666", "Cinza médio",
            "#808080", "Cinza",
            "#1d4ed8", "Azul (link)",
            "#333333", "Texto escuro"
        ],
        // Configurações para pastas de cores
        color_cols: 6,
        // Limpa código HTML desnecessário
        cleanup: true,
        // Remove classes e estilos indesejados
        valid_classes: {
            '*': '' // Não permite classes personalizadas
        },
        // Remove estilos indesejados
        valid_styles: {
            '*': 'color,background-color,font-family,font-size,text-align,margin,padding,font-weight,font-style,text-decoration'
        }
    });
</script>
@endpush
@endsection 