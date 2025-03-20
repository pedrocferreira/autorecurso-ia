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
        'credits',
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
        'credits' => 'integer',
    ];

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

    /**
     * Get all credit transactions for the user.
     */
    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    /**
     * Adiciona créditos ao usuário
     *
     * @param int $amount Quantidade de créditos a adicionar
     * @return bool
     */
    public function addCredits(int $amount): bool
    {
        $this->credits += $amount;
        return $this->save();
    }

    /**
     * Remove créditos do usuário
     *
     * @param int $amount Quantidade de créditos a remover
     * @return bool
     * @throws \Exception Se o usuário não tiver créditos suficientes
     */
    public function removeCredits(int $amount): bool
    {
        if ($this->credits < $amount) {
            throw new \Exception('Créditos insuficientes');
        }

        $this->credits -= $amount;
        return $this->save();
    }

    /**
     * Verifica se o usuário tem créditos suficientes
     *
     * @param int $amount Quantidade de créditos necessários
     * @return bool
     */
    public function hasEnoughCredits(int $amount): bool
    {
        return $this->credits >= $amount;
    }
}
