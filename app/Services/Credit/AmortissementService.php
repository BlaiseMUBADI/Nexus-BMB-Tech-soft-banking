<?php

namespace App\Services\Credit;

use App\Models\Credit\CreditDemande;
use App\Models\Credit\CreditEcheancier;
use App\Models\Credit\CreditEcheance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Service de calcul des échéanciers de remboursement.
 *
 * Méthode DÉGRESSIVE (capital constant) :
 *   - Capital mensuel = montant / durée  (constant)
 *   - Intérêt mensuel = capital_restant_début × taux_mensuel  (décroissant)
 *   - Total mensuel   = capital + intérêt  (décroissant)
 *
 * Exemple : 1 200$ / 6 mois / 5,5%
 *   Mois 1 : capital=200, intérêt=66,00, total=266,00
 *   Mois 6 : capital=200, intérêt=11,00, total=211,00
 *   TOTAL capital=1 200, intérêts=231,00, général=1 431,00
 */
class AmortissementService
{
    // ------------------------------------------------------------------
    // Calculer l'échéancier (tableau en mémoire)
    // ------------------------------------------------------------------

    /**
     * @param  float        $montant          Capital emprunté
     * @param  float        $tauxMensuel      Taux mensuel en % (ex: 5.5 pour 5,5 %)
     * @param  int          $dureeMois        Nombre d'échéances
     * @param  Carbon|null  $datePremier      Date 1ère échéance (défaut: mois prochain)
     * @param  float        $commissionTotale Commission totale à répartir sur les échéances (défaut: 0)
     * @return array{
     *   echeances: array<int, array{
     *     numero: int,
     *     date: Carbon,
     *     capital_restant_debut: float,
     *     capital: float,
     *     interet: float,
     *     commission: float,
     *     total: float,
     *     capital_restant_fin: float,
     *   }>,
     *   total_capital: float,
     *   total_interets: float,
     *   total_commission: float,
     *   total_general: float,
     * }
     */
    public function calculer(
        float $montant,
        float $tauxMensuel,
        int   $dureeMois,
        ?Carbon $datePremier = null,
        float $commissionTotale = 0.0
    ): array {
        $datePremier = $datePremier ?? Carbon::now()->addMonth()->startOfMonth();
        $tauxDecimal = $tauxMensuel / 100;

        $capitalMensuel  = round($montant / $dureeMois, 2);
        $capitalRestant  = $montant;

        // Répartition linéaire de la commission sur les échéances
        $commissionParEcheance = $dureeMois > 0 ? round($commissionTotale / $dureeMois, 2) : 0.0;

        $echeances      = [];
        $totalCapital   = 0.0;
        $totalInterets  = 0.0;
        $totalCommission = 0.0;

        for ($i = 1; $i <= $dureeMois; $i++) {
            $capitalRestantDebut = $capitalRestant;

            // Dernier mois : ajustement pour absorber les arrondis
            if ($i === $dureeMois) {
                $capital = round($capitalRestant, 2);
                // Ajuster la commission du dernier mois pour absorber les arrondis
                $commission = round($commissionTotale - ($commissionParEcheance * ($dureeMois - 1)), 2);
            } else {
                $capital = $capitalMensuel;
                $commission = $commissionParEcheance;
            }

            $interet           = round($capitalRestantDebut * $tauxDecimal, 2);
            $total             = round($capital + $interet + $commission, 2);
            $capitalRestantFin = round($capitalRestant - $capital, 2);

            $echeances[] = [
                'numero'                  => $i,
                'date'                    => $datePremier->copy()->addMonths($i - 1),
                'capital_restant_debut'   => $capitalRestantDebut,
                'capital'                 => $capital,
                'interet'                 => $interet,
                'commission'              => $commission,
                'total'                   => $total,
                'capital_restant_fin'     => max(0, $capitalRestantFin),
            ];

            $totalCapital  += $capital;
            $totalInterets += $interet;
            $totalCommission += $commission;
            $capitalRestant = max(0, $capitalRestantFin);
        }

        return [
            'echeances'       => $echeances,
            'total_capital'   => round($totalCapital, 2),
            'total_interets'  => round($totalInterets, 2),
            'total_commission'=> round($totalCommission, 2),
            'total_general'   => round($totalCapital + $totalInterets + $totalCommission, 2),
        ];
    }

    // ------------------------------------------------------------------
    // Persister l'échéancier en base de données
    // ------------------------------------------------------------------

    /**
     * Génère et sauvegarde l'échéancier complet pour un dossier.
     * Crée tb_credit_echeanciers + N lignes tb_credit_echeances.
     *
     * @throws \Exception Si un échéancier existe déjà pour ce dossier.
     */
    public function genererEtSauvegarder(CreditDemande $demande, Carbon $datePremier): CreditEcheancier
    {
        if ($demande->echeancier()->exists()) {
            throw new \Exception("Un échéancier existe déjà pour le dossier {$demande->numero_dossier}.");
        }

        $conditionsRetenues = $demande->conditions_retenues;
        $montant     = (float) $conditionsRetenues['montant'];
        $taux        = (float) $demande->taux_interet_mensuel;
        $duree       = (int) $conditionsRetenues['duree_mois'];
        $commission  = (float) ($demande->commission_totale ?? 0);

        $calcul = $this->calculer($montant, $taux, $duree, $datePremier, $commission);

        return DB::transaction(function () use ($demande, $montant, $taux, $duree, $datePremier, $calcul, $commission) {
            // En-tête
            $echeancier = CreditEcheancier::create([
                'credit_demande_id'           => $demande->id,
                'montant_capital'             => $montant,
                'taux_mensuel'                => $taux,
                'duree_mois'                  => $duree,
                'date_premier_remboursement'  => $datePremier,
                'type_amortissement'          => 'DEGRESSIF',
                'total_capital'               => $calcul['total_capital'],
                'total_interets'              => $calcul['total_interets'],
                'total_commission'            => $calcul['total_commission'],
                'total_general'               => $calcul['total_general'],
            ]);

            // Lignes
            foreach ($calcul['echeances'] as $e) {
                CreditEcheance::create([
                    'echeancier_id'          => $echeancier->id,
                    'numero_echeance'        => $e['numero'],
                    'date_echeance'          => $e['date'],
                    'capital_restant_debut'  => $e['capital_restant_debut'],
                    'capital_echeance'       => $e['capital'],
                    'interet_echeance'       => $e['interet'],
                    'commission_echeance'    => $e['commission'],
                    'total_echeance'         => $e['total'],
                    'capital_restant_fin'    => $e['capital_restant_fin'],
                    'statut'                 => 'EN_ATTENTE',
                    'montant_paye'           => 0,
                ]);
            }

            // Mettre à jour les totaux sur la demande
            $demande->update([
                'montant_total_echeances' => $calcul['total_general'],
                'total_interets'          => $calcul['total_interets'],
                'commission_totale'       => $commission,
            ]);

            return $echeancier->load('echeances');
        });
    }

    // ------------------------------------------------------------------
    // Simulation rapide (sans sauvegarde) — utilisée par AJAX/preview
    // ------------------------------------------------------------------
    public function simuler(float $montant, float $tauxMensuel, int $dureeMois, float $commissionTotale = 0.0): array
    {
        return $this->calculer($montant, $tauxMensuel, $dureeMois, null, $commissionTotale);
    }
}
