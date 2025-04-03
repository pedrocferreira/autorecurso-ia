<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::where('published', true)
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        return view('blog.index', compact('posts'));
    }

    public function adminIndex()
    {
        $posts = Post::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.blog.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.blog.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|max:255',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable|max:255',
            'featured_image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
        ]);

        $post = new Post($validated);
        $post->slug = Str::slug($request->title);
        
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('blog', 'public');
            $post->featured_image = $path;
        }

        $post->save();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        if (!$post->published || $post->published_at > now()) {
            abort(404);
        }

        // Processar o conteúdo para garantir formatação correta
        $post->content = $this->processContent($post->content);

        // Busca posts relacionados (mesma categoria ou tags)
        $relatedPosts = Post::where('published', true)
            ->where('published_at', '<=', now())
            ->where('id', '!=', $post->id)
            ->orderBy('published_at', 'desc')
            ->take(2)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('admin.blog.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|max:255',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable|max:255',
            'featured_image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
        ]);

        $post->fill($validated);
        $post->slug = Str::slug($request->title);
        
        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $path = $request->file('featured_image')->store('blog', 'public');
            $post->featured_image = $path;
        }

        $post->save();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post excluído com sucesso!');
    }

    public function publish(Post $post)
    {
        $post->update([
            'published' => true,
            'published_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Post publicado com sucesso!');
    }

    public function unpublish(Post $post)
    {
        $post->update(['published' => false]);

        return redirect()->back()->with('success', 'Post despublicado com sucesso!');
    }

    /**
     * Processa o conteúdo do post para garantir formatação correta
     */
    private function processContent($content)
    {
        // Se o conteúdo estiver vazio, retorna um texto padrão
        if (empty($content)) {
            return '<p>Conteúdo em breve...</p>';
        }

        // Remove tags script potencialmente perigosas
        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);

        // Verificar se o conteúdo tem HTML
        if (strip_tags($content) === $content) {
            // Se não contém tags HTML, adiciona tags de parágrafo
            $paragraphs = explode("\n\n", $content);
            $formattedContent = '';
            
            foreach ($paragraphs as $paragraph) {
                $paragraph = trim($paragraph);
                if (!empty($paragraph)) {
                    $formattedContent .= '<p>' . nl2br($paragraph) . '</p>';
                }
            }
            
            return $formattedContent;
        }
        
        // Se o conteúdo já contém HTML, mas não tem parágrafos
        if (strpos($content, '<p>') === false && strpos($content, '<div') === false && strpos($content, '<section') === false) {
            // Divide por quebras de linha duplas, mas preserva tags HTML
            $parts = preg_split('/\n\s*\n/', $content);
            $formattedContent = '';
            
            foreach ($parts as $part) {
                $part = trim($part);
                if (!empty($part)) {
                    // Verifica se a parte já começa com uma tag HTML de bloco
                    if (!preg_match('/^<(h[1-6]|ul|ol|li|blockquote|pre|table|div)/i', $part)) {
                        $part = '<p>' . nl2br($part) . '</p>';
                    }
                    $formattedContent .= $part;
                }
            }
            
            return $formattedContent;
        }

        // Se já tem estrutura de parágrafos, retorna como está
        return $content;
    }
}
