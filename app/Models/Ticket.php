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
        // Dados Pessoais
        'name',
        'cpf',
        'phone',
        'email',
        'address',
        'birth_date',
        'cnh_number',
        'cnh_category',
        'cnh_expiration',

        // Dados do Veículo
        'vehicle_plate',
        'vehicle_chassi',
        'vehicle_renavam',
        'vehicle_brand',
        'vehicle_model',
        'vehicle_year',
        'vehicle_color',
        'vehicle_owner',

        // Dados da Infração
        'infraction_type_id',
        'infraction_date',
        'infraction_location',
        'infraction_agent',
        'infraction_equipment',
        'infraction_observation',
        'infraction_points',
        'infraction_amount',
        'client_justification',
        'user_id',
        'status',
        'process_number',
        'notification_number'
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'infraction_date' => 'datetime',
        'infraction_amount' => 'decimal:2',
        'infraction_points' => 'integer',
        'vehicle_year' => 'integer',
        'birth_date' => 'date',
        'cnh_expiration' => 'date'
    ];

    /**
     * Os atributos que devem ser tratados como datas.
     *
     * @var array<string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'infraction_date',
        'birth_date',
        'cnh_expiration'
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
     * Obtém os recursos relacionados a esta multa.
     */
    public function appeals(): HasMany
    {
        return $this->hasMany(Appeal::class);
    }

    /**
     * Obtém o valor formatado da multa.
     */
    public function getFormattedAmountAttribute()
    {
        return 'R$ ' . number_format($this->infraction_amount, 2, ',', '.');
    }

    /**
     * Obtém o texto do status da multa.
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'paid' => 'Paga',
            'cancelled' => 'Cancelada',
            'contested' => 'Contestada',
            default => 'Desconhecido'
        };
    }

    /**
     * Obtém a placa formatada do veículo.
     */
    public function getFormattedPlateAttribute()
    {
        return strtoupper($this->vehicle_plate);
    }

    /**
     * Obtém o CPF formatado.
     */
    public function getFormattedCpfAttribute()
    {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf);
    }

    /**
     * Obtém o telefone formatado.
     */
    public function getFormattedPhoneAttribute()
    {
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $this->phone);
    }

    /**
     * Obtém a placa do veículo.
     */
    public function getPlateAttribute()
    {
        return $this->vehicle_plate;
    }

    /**
     * Obtém a data da infração.
     */
    public function getDateAttribute()
    {
        return $this->infraction_date;
    }

    /**
     * Obtém o local da infração.
     */
    public function getLocationAttribute()
    {
        return $this->infraction_location;
    }

    /**
     * Obtém o número da CNH.
     */
    public function getDriverLicenseAttribute()
    {
        return $this->cnh_number;
    }

    /**
     * Obtém os pontos da infração.
     */
    public function getPointsAttribute()
    {
        return $this->infraction_points;
    }

    /**
     * Obtém o valor da multa.
     */
    public function getAmountAttribute()
    {
        return $this->infraction_amount;
    }

    /**
     * Obtém a categoria da CNH.
     */
    public function getDriverLicenseCategoryAttribute()
    {
        return $this->cnh_category;
    }

    /**
     * Obtém o número da notificação.
     */
    public function getNotificationNumberAttribute()
    {
        return $this->notification_number;
    }

    /**
     * Obtém a justificativa do cliente (mantém compatibilidade com o campo reason).
     */
    public function getReasonAttribute()
    {
        return $this->client_justification;
    }
}
