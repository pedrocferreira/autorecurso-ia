<?php

namespace App\Services;

use App\Models\User;
use App\Models\Appeal;
use App\Models\CreditTransaction;
use App\Exceptions\InsufficientCreditsException;

class CreditService
{
    /**
     * Custo em créditos para gerar um recurso (valor padrão, caso não seja possível determinar o tipo da infração)
     */
    const APPEAL_GENERATION_COST = 1;

    /**
     * Custo em créditos para cada tipo de gravidade
     */
    const CREDIT_COST_BY_SEVERITY = [
        'light' => 1,      // Leve - 1 crédito
        'medium' => 3,     // Média - 3 créditos
        'serious' => 5,    // Grave - 5 créditos
        'very_serious' => 8, // Gravíssima - 8 créditos
    ];

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
     * @throws \App\Exceptions\InsufficientCreditsException Se o usuário não tiver créditos suficientes
     */
    public function consumeCreditsForAppeal(User $user, Appeal $appeal): CreditTransaction
    {
        // Obtém a gravidade da infração
        $ticket = $appeal->ticket;
        $infractionType = $ticket->infractionType;
        
        // Determina o custo em créditos com base na gravidade
        $creditCost = $this->getAppealCreditCost($infractionType);
        
        if (!$user->hasEnoughCredits($creditCost)) {
            $severityText = $infractionType ? ucfirst($infractionType->severity_text) : 'Não identificada';
            throw new \App\Exceptions\InsufficientCreditsException(
                "Você não possui créditos suficientes para gerar um recurso para esta infração. 
                Gravidade: {$severityText}. Custo: {$creditCost} créditos."
            );
        }

        $user->removeCredits($creditCost);

        return CreditTransaction::create([
            'user_id' => $user->id,
            'type' => 'consumption',
            'amount' => -$creditCost,
            'balance_after' => $user->credits,
            'description' => 'Geração de recurso #' . $appeal->id . ' - ' . ($infractionType ? $infractionType->severity_text : 'Infração'),
            'appeal_id' => $appeal->id,
        ]);
    }

    /**
     * Determina o custo em créditos com base no tipo de infração
     * 
     * @param \App\Models\InfractionType|null $infractionType Tipo de infração
     * @return int Custo em créditos
     */
    public function getAppealCreditCost($infractionType): int
    {
        if (!$infractionType) {
            return self::APPEAL_GENERATION_COST;
        }

        return self::CREDIT_COST_BY_SEVERITY[$infractionType->severity] ?? self::APPEAL_GENERATION_COST;
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
        $ticket = $appeal->ticket;
        $infractionType = $ticket ? $ticket->infractionType : null;
        
        // Determina o custo original que foi cobrado
        $creditCost = $this->getAppealCreditCost($infractionType);
        
        $user->addCredits($creditCost);

        return CreditTransaction::create([
            'user_id' => $user->id,
            'type' => 'refund',
            'amount' => $creditCost,
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
