<?php

namespace App\Services;

use App\Models\User;
use App\Models\Appeal;
use App\Models\CreditTransaction;

class CreditService
{
    /**
     * Custo em créditos para gerar um recurso
     */
    const APPEAL_GENERATION_COST = 1;

    /**
     * Adiciona créditos a um usuário e registra a transação
     *
     * @param User $user Usuário que receberá os créditos
     * @param int $amount Quantidade de créditos a adicionar
     * @param string $description Descrição da transação
     * @param array $metadata Metadados adicionais (opcional)
     * @param string|null $paymentMethod Método de pagamento usado
     * @param string|null $reference Referência externa (ID do gateway)
     * @return CreditTransaction
     */
    public function addCredits(
        User $user, 
        int $amount, 
        string $description, 
        array $metadata = [],
        ?string $paymentMethod = null,
        ?string $reference = null
    ): CreditTransaction {
        $user->addCredits($amount);

        return CreditTransaction::create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'status' => 'completed',
            'payment_method' => $paymentMethod,
            'reference' => $reference,
            'paid_at' => now(),
            'amount' => $amount,
            'balance_after' => $user->credits,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Cria uma transação pendente (para pagamentos que ainda não foram confirmados)
     *
     * @param User $user Usuário que receberá os créditos
     * @param int $amount Quantidade de créditos a adicionar
     * @param string $description Descrição da transação
     * @param string $paymentMethod Método de pagamento
     * @param array $metadata Metadados adicionais (opcional)
     * @param string|null $reference Referência externa (ID do gateway)
     * @return CreditTransaction
     */
    public function createPendingTransaction(
        User $user,
        int $amount,
        string $description,
        string $paymentMethod,
        array $metadata = [],
        ?string $reference = null
    ): CreditTransaction {
        return CreditTransaction::create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'status' => 'pending',
            'payment_method' => $paymentMethod,
            'reference' => $reference,
            'amount' => $amount,
            'balance_after' => $user->credits, // Saldo atual, sem alterar ainda
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Confirma uma transação pendente e adiciona os créditos
     *
     * @param CreditTransaction $transaction Transação a ser confirmada
     * @return CreditTransaction
     */
    public function confirmTransaction(CreditTransaction $transaction): CreditTransaction
    {
        if ($transaction->status !== 'pending') {
            throw new \Exception('Apenas transações pendentes podem ser confirmadas');
        }

        $user = $transaction->user;
        $user->addCredits($transaction->amount);

        $transaction->update([
            'status' => 'completed',
            'paid_at' => now(),
            'balance_after' => $user->credits,
        ]);

        return $transaction;
    }

    /**
     * Consome créditos de um usuário para geração de recurso
     *
     * @param User $user Usuário que terá os créditos consumidos
     * @param Appeal $appeal Recurso gerado
     * @return CreditTransaction
     * @throws \Exception Se o usuário não tiver créditos suficientes
     */
    public function consumeCreditsForAppeal(User $user, Appeal $appeal): CreditTransaction
    {
        if (!$user->hasEnoughCredits(self::APPEAL_GENERATION_COST)) {
            throw new \Exception('Créditos insuficientes para gerar o recurso');
        }

        $user->removeCredits(self::APPEAL_GENERATION_COST);

        return CreditTransaction::create([
            'user_id' => $user->id,
            'type' => 'consumption',
            'status' => 'completed',
            'amount' => -self::APPEAL_GENERATION_COST,
            'balance_after' => $user->credits,
            'description' => 'Geração de recurso #' . $appeal->id,
            'appeal_id' => $appeal->id,
        ]);
    }

    /**
     * Reembolsa créditos de um recurso para um usuário
     *
     * @param Appeal $appeal Recurso a ser reembolsado
     * @param string $reason Motivo do reembolso
     * @return CreditTransaction
     */
    public function refundAppealCredits(Appeal $appeal, string $reason): CreditTransaction
    {
        $user = $appeal->user;
        $user->addCredits(self::APPEAL_GENERATION_COST);

        return CreditTransaction::create([
            'user_id' => $user->id,
            'type' => 'refund',
            'status' => 'completed',
            'paid_at' => now(),
            'amount' => self::APPEAL_GENERATION_COST,
            'balance_after' => $user->credits,
            'description' => 'Reembolso do recurso #' . $appeal->id . ': ' . $reason,
            'appeal_id' => $appeal->id,
        ]);
    }

    /**
     * Ajusta o saldo de créditos de um usuário (uso administrativo)
     *
     * @param User $user Usuário que terá os créditos ajustados
     * @param int $amount Quantidade de créditos a ajustar (positivo para adicionar, negativo para remover)
     * @param string $reason Motivo do ajuste
     * @return CreditTransaction
     * @throws \Exception Se a remoção de créditos resultar em saldo negativo
     */
    public function adjustCredits(User $user, int $amount, string $reason): CreditTransaction
    {
        if ($amount < 0 && !$user->hasEnoughCredits(abs($amount))) {
            throw new \Exception('Saldo insuficiente para o ajuste');
        }

        if ($amount > 0) {
            $user->addCredits($amount);
        } else {
            $user->removeCredits(abs($amount));
        }

        return CreditTransaction::create([
            'user_id' => $user->id,
            'type' => 'admin_adjustment',
            'status' => 'completed',
            'payment_method' => 'admin',
            'paid_at' => now(),
            'amount' => $amount,
            'balance_after' => $user->credits,
            'description' => 'Ajuste administrativo: ' . $reason,
        ]);
    }

    /**
     * Cancela uma transação pendente
     *
     * @param CreditTransaction $transaction Transação a ser cancelada
     * @param string $reason Motivo do cancelamento
     * @return CreditTransaction
     */
    public function cancelTransaction(CreditTransaction $transaction, string $reason = 'Cancelado pelo sistema'): CreditTransaction
    {
        if ($transaction->status !== 'pending') {
            throw new \Exception('Apenas transações pendentes podem ser canceladas');
        }

        $transaction->update([
            'status' => 'cancelled',
            'metadata' => array_merge($transaction->metadata ?? [], [
                'cancelled_reason' => $reason,
                'cancelled_at' => now()->toISOString()
            ])
        ]);

        return $transaction;
    }

    /**
     * Marca uma transação como expirada
     *
     * @param CreditTransaction $transaction Transação a ser marcada como expirada
     * @return CreditTransaction
     */
    public function expireTransaction(CreditTransaction $transaction): CreditTransaction
    {
        if ($transaction->status !== 'pending') {
            throw new \Exception('Apenas transações pendentes podem expirar');
        }

        $transaction->update([
            'status' => 'expired',
            'metadata' => array_merge($transaction->metadata ?? [], [
                'expired_at' => now()->toISOString()
            ])
        ]);

        return $transaction;
    }

    /**
     * Falha uma transação
     *
     * @param CreditTransaction $transaction Transação a ser marcada como falhada
     * @param string $reason Motivo da falha
     * @return CreditTransaction
     */
    public function failTransaction(CreditTransaction $transaction, string $reason = 'Falha no processamento'): CreditTransaction
    {
        $transaction->update([
            'status' => 'failed',
            'metadata' => array_merge($transaction->metadata ?? [], [
                'failed_reason' => $reason,
                'failed_at' => now()->toISOString()
            ])
        ]);

        return $transaction;
    }
}
