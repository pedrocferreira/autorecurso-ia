<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appeal extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'ticket_id',
        'generated_text',
        'pdf_path',
        'status',
        'notes',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Obtém o usuário que possui este recurso.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtém a multa relacionada a este recurso.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Extrai os argumentos técnicos do texto do recurso
     */
    public function getArguments(): array
    {
        $arguments = [];
        $text = $this->text;

        // Procura por argumentos na seção "DO DIREITO" ou "DO MÉRITO"
        if (preg_match('/(?:DO DIREITO|DO MÉRITO)(.*?)(?:DOS PRECEDENTES|DO PEDIDO)/s', $text, $matches)) {
            $section = $matches[1];
            
            // Extrai itens numerados ou com marcadores
            preg_match_all('/(?:\d+\.|•|\-)\s*([^.]*\.)/U', $section, $items);
            
            if (!empty($items[1])) {
                $arguments = array_map('trim', $items[1]);
            }
        }

        return array_filter($arguments);
    }

    /**
     * Extrai as citações de jurisprudência do texto do recurso
     */
    public function getJurisprudence(): array
    {
        $jurisprudence = [];
        $text = $this->text;

        // Procura por citações de tribunais
        preg_match_all('/((?:STF|STJ|TJ-[A-Z]{2}|TRF-?\d|TST)\s*[-–]\s*[^.]*\.)/i', $text, $matches);

        if (!empty($matches[1])) {
            $jurisprudence = array_map('trim', $matches[1]);
        }

        return array_filter($jurisprudence);
    }

    /**
     * Extrai os artigos de lei citados no recurso
     */
    public function getArticles(): array
    {
        $articles = [];
        $text = $this->text;

        // Procura por citações de artigos
        preg_match_all('/(art(?:igo)?\.?\s*\d+[^\.]*(CTB|Código de Trânsito Brasileiro|Lei)[^.]*\.)/i', $text, $matches);

        if (!empty($matches[1])) {
            $articles = array_map('trim', $matches[1]);
        }

        return array_filter($articles);
    }

    /**
     * Calcula o número aproximado de páginas do recurso
     */
    public function getPageCount(): int
    {
        $charactersPerPage = 3000; // Média de caracteres por página
        return max(1, ceil(strlen($this->text) / $charactersPerPage));
    }
}
