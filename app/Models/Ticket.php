<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

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
        'citation_number',
        'date',
        'time',
        'amount',
        'points',
        'status',
        'reason',
        'location',
        'custom_details',
        'user_id',
        'infraction_type_id',
        'organ',
        'was_driver',
        'had_signage',
        'details',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'amount' => 'decimal:2',
        'points' => 'integer',
        'vehicle_year' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function infractionType()
    {
        return $this->belongsTo(InfractionType::class);
    }

    public function appeals()
    {
        return $this->hasMany(Appeal::class);
    }
} 