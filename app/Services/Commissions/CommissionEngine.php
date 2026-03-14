<?php

namespace App\Services\Commissions;

use App\Models\Caisse\Transaction;
use App\Models\Caisse\TransactionCommission;
use App\Models\Tresorerie\CommissionRule;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

class CommissionEngine
{
    public function resolveRule(array $context, CarbonInterface|string|null $appliedAt = null): ?CommissionRule
    {
        $appliedAt = $appliedAt instanceof CarbonInterface
            ? $appliedAt->toDateString()
            : ($appliedAt ?: now()->toDateString());

        $operationCode = (string) ($context['code_operation'] ?? CommissionRule::ALL);
        $accountType = (string) ($context['type_compte'] ?? CommissionRule::TYPE_NO_ACCOUNT);
        $guichetType = (string) ($context['type_guichet'] ?? CommissionRule::ALL);
        $deviseCode = $context['devise_code'] ?? null;
        $zoneCode = $context['code_zone'] ?? null;
        $portefeuilleId = $context['portefeuille_id'] ?? null;
        $amount = (float) ($context['montant'] ?? 0);

        return CommissionRule::query()
            ->activeOn($appliedAt)
            ->whereIn('code_operation', [$operationCode, CommissionRule::ALL])
            ->whereIn('type_compte', [$accountType, CommissionRule::ALL])
            ->whereIn('type_guichet', [$guichetType, CommissionRule::ALL])
            ->where(function (Builder $query) use ($deviseCode) {
                if ($deviseCode) {
                    $query->where('devise_code', $deviseCode)
                        ->orWhereNull('devise_code');
                    return;
                }

                $query->whereNull('devise_code');
            })
            ->where(function (Builder $query) use ($zoneCode) {
                if ($zoneCode) {
                    $query->where('code_zone', $zoneCode)
                        ->orWhereNull('code_zone');
                    return;
                }

                $query->whereNull('code_zone');
            })
            ->where(function (Builder $query) use ($portefeuilleId) {
                if ($portefeuilleId) {
                    $query->where('portefeuille_id', $portefeuilleId)
                        ->orWhereNull('portefeuille_id');
                    return;
                }

                $query->whereNull('portefeuille_id');
            })
            ->where(function (Builder $query) use ($amount) {
                $query->whereNull('montant_min')
                    ->orWhere('montant_min', '<=', $amount);
            })
            ->where(function (Builder $query) use ($amount) {
                $query->whereNull('montant_max')
                    ->orWhere('montant_max', '>=', $amount);
            })
            ->orderByDesc('priorite')
            ->orderByRaw('CASE WHEN code_operation = ? THEN 1 ELSE 0 END DESC', [$operationCode])
            ->orderByRaw('CASE WHEN type_compte = ? THEN 1 ELSE 0 END DESC', [$accountType])
            ->orderByRaw('CASE WHEN type_guichet = ? THEN 1 ELSE 0 END DESC', [$guichetType])
            ->orderByRaw('CASE WHEN devise_code = ? THEN 1 ELSE 0 END DESC', [$deviseCode])
            ->orderByRaw('CASE WHEN code_zone = ? THEN 1 ELSE 0 END DESC', [$zoneCode])
            ->orderByRaw('CASE WHEN portefeuille_id = ? THEN 1 ELSE 0 END DESC', [$portefeuilleId])
            ->orderByDesc('date_debut')
            ->orderByDesc('id')
            ->first();
    }

    public function calculateCommission(CommissionRule $rule, float $baseAmount): float
    {
        $baseAmount = max(0, $baseAmount);

        if ($rule->mode_calcul === CommissionRule::MODE_PERCENTAGE) {
            return round(($baseAmount * (float) $rule->valeur) / 100, 2);
        }

        return round((float) $rule->valeur, 2);
    }

    public function applyToTransaction(Transaction $transaction, array $context): ?TransactionCommission
    {
        $rule = $this->resolveRule($context, $transaction->date_operation ?? now());
        $baseAmount = (float) ($context['montant'] ?? $transaction->montant ?? 0);

        if (!$rule) {
            $snapshot = TransactionCommission::create([
                'transaction_id' => $transaction->id,
                'commission_rule_id' => null,
                'libelle' => 'Aucune regle de commission applicable',
                'code_operation' => $context['code_operation'] ?? $transaction->type,
                'type_compte' => $context['type_compte'] ?? null,
                'type_guichet' => $context['type_guichet'] ?? null,
                'devise_code' => $context['devise_code'] ?? $transaction->devise_code,
                'code_zone' => $context['code_zone'] ?? null,
                'portefeuille_id' => $context['portefeuille_id'] ?? null,
                'mode_calcul' => CommissionRule::MODE_FIXED,
                'valeur_snapshot' => 0,
                'base_calcul' => $baseAmount,
                'montant_commission' => 0,
                'date_application' => $transaction->date_operation ?? now(),
                'agent_matricule' => $context['agent_matricule'] ?? $transaction->agent_matricule,
                'guichet_id' => $context['guichet_id'] ?? $transaction->guichet_id,
            ]);

            $transaction->forceFill(['montant_commission_total' => 0])->save();

            return $snapshot;
        }

        $commissionAmount = $this->calculateCommission($rule, $baseAmount);

        $snapshot = TransactionCommission::create([
            'transaction_id' => $transaction->id,
            'commission_rule_id' => $rule->id,
            'libelle' => $rule->libelle,
            'code_operation' => $context['code_operation'] ?? $transaction->type,
            'type_compte' => $context['type_compte'] ?? null,
            'type_guichet' => $context['type_guichet'] ?? null,
            'devise_code' => $context['devise_code'] ?? $transaction->devise_code,
            'code_zone' => $context['code_zone'] ?? null,
            'portefeuille_id' => $context['portefeuille_id'] ?? null,
            'mode_calcul' => $rule->mode_calcul,
            'valeur_snapshot' => (float) $rule->valeur,
            'base_calcul' => $baseAmount,
            'montant_commission' => $commissionAmount,
            'date_application' => $transaction->date_operation ?? now(),
            'agent_matricule' => $context['agent_matricule'] ?? $transaction->agent_matricule,
            'guichet_id' => $context['guichet_id'] ?? $transaction->guichet_id,
        ]);

        $transaction->forceFill(['montant_commission_total' => $commissionAmount])->save();

        return $snapshot;
    }
}