<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'cpf',
        'driver_license',
        'driver_license_category',
        'address',
        'phone',
        'email',
        'plate',
        'vehicle_model',
        'vehicle_year',
        'vehicle_color',
        'vehicle_chassi',
        'vehicle_renavam',
        'date',
        'amount',
        'points',
        'reason',
        'user_id',
        'infraction_type_id',
        'status'
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:2',
        'points' => 'integer',
        'vehicle_year' => 'integer'
    ];

    /**
     * Obtém o usuário que possui esta multa.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtém o tipo de infração desta multa.
     */
    public function infractionType(): BelongsTo
    {
        return $this->belongsTo(InfractionType::class);
    }

    /**
     * Obtém todos os recursos para esta multa.
     */
    public function appeals(): HasMany
    {
        return $this->hasMany(Appeal::class);
    }
}
