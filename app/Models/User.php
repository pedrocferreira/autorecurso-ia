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
        'cpf',
        'cnh_category',
        'cnh_address',
        'phone',
        'is_admin',
        'credits',
        'google_id',
        'avatar',
        'premium',
        'onboarded',
        'blocked',
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
        'onboarded' => 'boolean',
        'blocked' => 'boolean',
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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            if ($user->isDirty('cpf')) {
                $cpf = preg_replace('/[^0-9]/', '', $user->cpf);
                
                // Valida tamanho
                if (strlen($cpf) !== 11) {
                    throw new \Exception('CPF deve ter 11 dígitos');
                }

                // Valida se todos os dígitos são iguais
                if (preg_match('/^(\d)\1+$/', $cpf)) {
                    throw new \Exception('CPF inválido');
                }

                // Calcula primeiro dígito verificador
                $soma = 0;
                for ($i = 0; $i < 9; $i++) {
                    $soma += $cpf[$i] * (10 - $i);
                }
                $resto = $soma % 11;
                $digito1 = ($resto < 2) ? 0 : 11 - $resto;

                // Calcula segundo dígito verificador
                $soma = 0;
                for ($i = 0; $i < 9; $i++) {
                    $soma += $cpf[$i] * (11 - $i);
                }
                $soma += $digito1 * 2;
                $resto = $soma % 11;
                $digito2 = ($resto < 2) ? 0 : 11 - $resto;

                // Verifica se os dígitos calculados são iguais aos informados
                if ($cpf[9] != $digito1 || $cpf[10] != $digito2) {
                    throw new \Exception('CPF inválido');
                }

                // Salva o CPF sem formatação
                $user->cpf = $cpf;
            }
        });
    }
}
