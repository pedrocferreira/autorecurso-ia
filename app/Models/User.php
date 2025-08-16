<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'google_id',
        'avatar',
        'cpf',
        'cnh_category',
        'cnh_address',
        'phone',
        'premium',
        'onboarded',
        'blocked',
        'subscription_active',
        'subscription_ends_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'subscription_active' => 'boolean',
        'subscription_ends_at' => 'datetime',
    ];

    public function hasActiveSubscription(): bool
    {
        if ($this->subscription_active === true) {
            return true;
        }
        if ($this->subscription_ends_at && now()->lte($this->subscription_ends_at)) {
            return true;
        }
        return false;
    }

    /**
     * Get all tickets for the user.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get all appeals for the user.
     */
    public function appeals(): HasMany
    {
        return $this->hasMany(Appeal::class);
    }

    // Métodos e relações de créditos removidos (modelo de assinatura)
}
