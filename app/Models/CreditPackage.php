<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditPackage extends Model
{
    use HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'credits',
        'price',
        'discount',
        'active',
        'recommended',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'credits' => 'integer',
        'price' => 'decimal:2',
        'discount' => 'integer',
        'active' => 'boolean',
        'recommended' => 'boolean',
    ];

    /**
     * Retorna o preço formatado em Real.
     *
     * @return string
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }

    /**
     * Calcula e retorna o preço com desconto.
     *
     * @return float
     */
    public function getDiscountedPriceAttribute(): float
    {
        if ($this->discount > 0) {
            return $this->price * (1 - ($this->discount / 100));
        }
        return $this->price;
    }

    /**
     * Retorna o preço com desconto formatado em Real.
     *
     * @return string
     */
    public function getFormattedDiscountedPriceAttribute(): string
    {
        return 'R$ ' . number_format($this->getDiscountedPriceAttribute(), 2, ',', '.');
    }

    /**
     * Retorna o valor do desconto formatado (exemplo: "10%").
     *
     * @return string
     */
    public function getFormattedDiscountAttribute(): string
    {
        return $this->discount . '%';
    }

    /**
     * Calcula e retorna o preço por crédito.
     *
     * @return float
     */
    public function getPricePerCreditAttribute(): float
    {
        return $this->getDiscountedPriceAttribute() / $this->credits;
    }

    /**
     * Calcula os recursos possíveis para o pacote
     */
    public function getResourcesAttribute()
    {
        return [
            [
                'description' => 'Recursos Básicos',
                'count' => floor($this->credits / 2),
                'color' => 'blue'
            ],
            [
                'description' => 'Recursos Avançados',
                'count' => floor($this->credits / 4),
                'color' => 'green'
            ],
            [
                'description' => 'Recursos Premium',
                'count' => floor($this->credits / 8),
                'color' => 'purple'
            ]
        ];
    }
} 