<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditTransaction extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'status',
        'payment_method',
        'reference',
        'paid_at',
        'amount',
        'balance_after',
        'description',
        'metadata',
        'appeal_id',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'integer',
        'balance_after' => 'integer',
        'metadata' => 'json',
        'paid_at' => 'datetime',
    ];

    /**
     * Obtém o usuário da transação.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtém o recurso associado à transação, se houver.
     */
    public function appeal(): BelongsTo
    {
        return $this->belongsTo(Appeal::class);
    }

    /**
     * Verifica se a transação está pendente.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Verifica se a transação foi completada.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Verifica se a transação falhou.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Verifica se a transação foi cancelada.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Verifica se a transação expirou.
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Obtém o nome amigável do método de pagamento.
     */
    public function getPaymentMethodNameAttribute(): string
    {
        return match($this->payment_method) {
            'stripe' => 'Cartão de Crédito',
            'pix' => 'PIX',
            'boleto' => 'Boleto',
            'admin' => 'Administrador',
            default => 'Não informado'
        };
    }

    /**
     * Obtém o nome amigável do status.
     */
    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'completed' => 'Completado',
            'failed' => 'Falhou',
            'cancelled' => 'Cancelado',
            'expired' => 'Expirado',
            default => 'Desconhecido'
        };
    }
}
