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
     * @return CreditTransaction
     */
    public function addCredits(User $user, int $amount, string $description, array $metadata = []): CreditTransaction
    {
        $user->addCredits($amount);

        return CreditTransaction::create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'amount' => $amount,
            'balance_after' => $user->credits,
            'description' => $description,
            'metadata' => $metadata,
        ]);
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
            'amount' => $amount,
            'balance_after' => $user->credits,
            'description' => 'Ajuste administrativo: ' . $reason,
        ]);
    }
}
