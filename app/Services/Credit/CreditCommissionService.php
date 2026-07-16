<?php

namespace App\Services\Credit;

use App\Models\Credit\CreditCommissionRule;
use Illuminate\Database\Eloquent\Builder;

class CreditCommissionService
{
    /**
     * Résout la règle de commission applicable selon le contexte du crédit
     */
    public function resolveRule(array $context): ?CreditCommissionRule
    {
        $deviseCode = $context['devise'] ?? null;
        $typeCredit = $context['type_credit'] ?? CreditCommissionRule::TOUS;
        $zoneCode = $context['code_zone'] ?? null;
        $portefeuilleId = $context['portefeuille_id'] ?? null;
        $montant = (float) ($context['montant'] ?? 0);

        return CreditCommissionRule::query()
            ->activeOn(now())
            ->where(function (Builder $query) use ($deviseCode) {
                if ($deviseCode) {
                    $query->where('devise_code', $deviseCode);
                }
            })
            ->where(function (Builder $query) use ($typeCredit) {
                $query->where('type_credit', $typeCredit)
                    ->orWhere('type_credit', CreditCommissionRule::TOUS);
            })
            ->where(function (Builder $query) use ($zoneCode) {
                if ($zoneCode) {
                    $query->where('code_zone', $zoneCode)
                        ->orWhereNull('code_zone');
                } else {
                    $query->whereNull('code_zone');
                }
            })
            ->where(function (Builder $query) use ($portefeuilleId) {
                if ($portefeuilleId) {
                    $query->where('portefeuille_id', $portefeuilleId)
                        ->orWhereNull('portefeuille_id');
                } else {
                    $query->whereNull('portefeuille_id');
                }
            })
            ->where(function (Builder $query) use ($montant) {
                $query->whereNull('montant_min')
                    ->orWhere('montant_min', '<=', $montant);
            })
            ->where(function (Builder $query) use ($montant) {
                $query->whereNull('montant_max')
                    ->orWhere('montant_max', '>=', $montant);
            })
            ->orderByDesc('priorite')
            ->orderByRaw('CASE WHEN type_credit = ? THEN 1 ELSE 0 END DESC', [$typeCredit])
            ->orderByRaw('CASE WHEN devise_code = ? THEN 1 ELSE 0 END DESC', [$deviseCode])
            ->orderByRaw('CASE WHEN code_zone = ? THEN 1 ELSE 0 END DESC', [$zoneCode])
            ->orderByRaw('CASE WHEN portefeuille_id = ? THEN 1 ELSE 0 END DESC', [$portefeuilleId])
            ->first();
    }

    /**
     * Calcule le montant total de commission selon la règle
     */
    public function calculateCommission(?CreditCommissionRule $rule, float $montant): float
    {
        if (!$rule) {
            return 0.0;
        }

        $montant = max(0, $montant);

        if ($rule->mode_calcul === CreditCommissionRule::MODE_PERCENTAGE) {
            return round(($montant * (float) $rule->valeur) / 100, 2);
        }

        return round((float) $rule->valeur, 2);
    }

    /**
     * Calcule la commission par échéance (répartition linéaire)
     */
    public function calculateCommissionParEcheance(float $commissionTotale, int $dureeMois): float
    {
        if ($dureeMois <= 0) {
            return 0.0;
        }

        return round($commissionTotale / $dureeMois, 2);
    }

    /**
     * Calcule la commission pour un contexte donné (méthode utilitaire)
     */
    public function calculateForContext(array $context): float
    {
        $rule = $this->resolveRule($context);
        $montant = (float) ($context['montant'] ?? 0);

        return $this->calculateCommission($rule, $montant);
    }
}
