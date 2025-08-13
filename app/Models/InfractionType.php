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
        'name',
        'description',
        'article',
        'base_amount',
        'points',
        'active'
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'base_amount' => 'float',
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
}
