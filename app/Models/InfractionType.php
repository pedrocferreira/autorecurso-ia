<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfractionType extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'law_article',
        'description',
        'base_amount',
        'points',
        'severity',
        'active'
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'base_amount' => 'decimal:2',
        'points' => 'integer',
        'active' => 'boolean'
    ];

    /**
     * Obtém todos os tickets que usam este tipo de infração.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Scope para infrações ativas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para filtrar por gravidade
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Retorna o texto da gravidade formatado
     */
    public function getSeverityTextAttribute()
    {
        return match($this->severity) {
            'light' => 'Leve',
            'medium' => 'Média',
            'serious' => 'Grave',
            'very_serious' => 'Gravíssima',
            default => $this->severity
        };
    }

    /**
     * Retorna o valor formatado em reais
     */
    public function getFormattedAmountAttribute()
    {
        return 'R$ ' . number_format($this->base_amount, 2, ',', '.');
    }
}
