<?php

namespace App\Services\Comptabilite;

use App\Models\ActivityLog;
use App\Models\Comptabilite\EcritureComptable;
use App\Models\Comptabilite\ExerciceComptable;
use App\Models\Comptabilite\PlanComptable;
use App\Models\Comptabilite\SoldeOuverture;
use Illuminate\Support\Facades\DB;

class ExerciceComptableService
{
    /**
     * Calcule le résultat net (Produits - Charges) sur la période d'un exercice,
     * en tenant compte du report à nouveau éventuel (aucun impact sur 6/7, qui ne se
     * reportent jamais — uniquement utile pour cohérence d'API).
     */
    public function calculerResultatNet(ExerciceComptable $exercice): float
    {
        $comptesCharges = PlanComptable::where('classe_ohada', '6')->pluck('numero_compte');
        $comptesProduits = PlanComptable::where('classe_ohada', '7')->pluck('numero_compte');

        $totalCharges = EcritureComptable::whereIn('numero_compte', $comptesCharges)
            ->whereHas('journal', fn ($q) => $q->whereDate('date_ecriture', '>=', $exercice->date_debut)->whereDate('date_ecriture', '<=', $exercice->date_fin))
            ->selectRaw('COALESCE(SUM(debit),0) - COALESCE(SUM(credit),0) as solde')->value('solde');

        $totalProduits = EcritureComptable::whereIn('numero_compte', $comptesProduits)
            ->whereHas('journal', fn ($q) => $q->whereDate('date_ecriture', '>=', $exercice->date_debut)->whereDate('date_ecriture', '<=', $exercice->date_fin))
            ->selectRaw('COALESCE(SUM(credit),0) - COALESCE(SUM(debit),0) as solde')->value('solde');

        return round((float) $totalProduits - (float) $totalCharges, 2);
    }

    /**
     * Étape 1 (Comptable) : propose la clôture — calcule et fige le résultat net,
     * passe l'exercice en attente de validation. Aucune donnée n'est encore verrouillée.
     *
     * @param string|null $dateClotureEffective Permet une clôture ANTICIPÉE (avant la date de
     *   fin prévue). Si fournie et antérieure à date_fin, la période de l'exercice est RÉDUITE
     *   à cette date (date_fin devient cette date) — ceci évite tout "trou" sans exercice ouvert :
     *   le nouvel exercice démarrera exactement le lendemain de cette date.
     */
    public function proposerCloture(ExerciceComptable $exercice, string $matricule, ?string $dateClotureEffective = null): ExerciceComptable
    {
        if ($exercice->statut !== 'OUVERT') {
            throw new \RuntimeException("Seul un exercice OUVERT peut être proposé à la clôture.");
        }

        $dateEffective = $dateClotureEffective ? \Carbon\Carbon::parse($dateClotureEffective) : $exercice->date_fin;

        if ($dateEffective->lt($exercice->date_debut)) {
            throw new \RuntimeException("La date de clôture ne peut pas être antérieure au début de l'exercice ({$exercice->date_debut->format('d/m/Y')}).");
        }
        if ($dateEffective->gt(now())) {
            throw new \RuntimeException("La date de clôture ne peut pas être dans le futur.");
        }

        // Clôture anticipée : on réduit la période réelle de l'exercice à la date choisie.
        if ($dateEffective->ne($exercice->date_fin)) {
            $exercice->date_fin = $dateEffective;
        }

        $resultatNet = $this->calculerResultatNet($exercice);

        $exercice->statut = 'EN_ATTENTE_VALIDATION';
        $exercice->resultat_net_cloture = $resultatNet;
        $exercice->propose_par_matricule = $matricule;
        $exercice->propose_le = now();
        $exercice->save();

        ActivityLog::record(
            'COMPTABILITE',
            'EXERCICE_PROPOSE_CLOTURE',
            $exercice,
            (string) $exercice->annee,
            "Proposition de clôture de l'exercice {$exercice->annee} au {$exercice->date_fin->format('d/m/Y')} (résultat net calculé : " . number_format($resultatNet, 2, ',', ' ') . ")"
        );

        return $exercice;
    }

    /**
     * Étape 2 (Gérant/Directeur) : valide définitivement la clôture.
     * - Verrouille l'exercice (CLOTURE)
     * - Calcule le solde de report à nouveau de chaque compte de bilan (classes 1-5)
     * - Crée le nouvel exercice, statut OUVERT, démarrant le LENDEMAIN EXACT (aucun trou possible),
     *   avec la durée choisie par le validateur (1 mois, 1 trimestre, 1 an... au choix).
     *
     * @param string|null $dateFinNouvelExercice Date de fin du nouvel exercice. Si non fournie,
     *   défaut = 1 an après son début (comportement annuel classique).
     */
    public function validerCloture(ExerciceComptable $exercice, string $matricule, ?string $dateFinNouvelExercice = null): ExerciceComptable
    {
        if ($exercice->statut !== 'EN_ATTENTE_VALIDATION') {
            throw new \RuntimeException("Cet exercice n'est pas en attente de validation.");
        }

        return DB::transaction(function () use ($exercice, $matricule, $dateFinNouvelExercice) {
            // 1. Verrouiller l'exercice actuel
            $exercice->update([
                'statut' => 'CLOTURE',
                'valide_par_matricule' => $matricule,
                'valide_le' => now(),
            ]);

            // 2. Calculer le solde cumulé (report à nouveau des exercices précédents + mouvements
            //    de cet exercice) de chaque compte de bilan (classes 1-5), à la date de fin d'exercice.
            $comptesBilan = PlanComptable::whereIn('classe_ohada', ['1', '2', '3', '4', '5'])
                ->where('est_mouvementable', true)
                ->pluck('numero_compte');

            $mouvements = EcritureComptable::whereIn('numero_compte', $comptesBilan)
                ->whereHas('journal', fn ($q) => $q->whereDate('date_ecriture', '<=', $exercice->date_fin))
                ->selectRaw('numero_compte, SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->groupBy('numero_compte')
                ->get();

            // 3. Créer le nouvel exercice — démarre le LENDEMAIN EXACT de la fin de l'ancien
            //    (aucun trou de couverture possible, quelle que soit la durée choisie).
            $nouvelleDateDebut = $exercice->date_fin->copy()->addDay();
            $nouvelleDateFin = $dateFinNouvelExercice
                ? \Carbon\Carbon::parse($dateFinNouvelExercice)
                : $nouvelleDateDebut->copy()->addYear()->subDay();

            if ($nouvelleDateFin->lt($nouvelleDateDebut)) {
                throw new \RuntimeException("La date de fin du nouvel exercice doit être postérieure à son début ({$nouvelleDateDebut->format('d/m/Y')}).");
            }

            $nouvelExercice = ExerciceComptable::create([
                'annee' => $nouvelleDateDebut->year,
                'date_debut' => $nouvelleDateDebut,
                'date_fin' => $nouvelleDateFin,
                'statut' => 'OUVERT',
            ]);

            // 4. Reporter le solde final de chaque compte de bilan comme solde d'ouverture du nouvel exercice
            foreach ($mouvements as $m) {
                $soldeFinal = round((float) $m->total_debit - (float) $m->total_credit, 2);
                if ($soldeFinal == 0.0) {
                    continue;
                }
                SoldeOuverture::updateOrCreate(
                    ['exercice_id' => $nouvelExercice->id, 'numero_compte' => $m->numero_compte],
                    ['solde_ouverture' => $soldeFinal]
                );
            }

            ActivityLog::record(
                'COMPTABILITE',
                'EXERCICE_CLOTURE_VALIDEE',
                $exercice,
                (string) $exercice->annee,
                "Clôture définitive de l'exercice {$exercice->annee} (jusqu'au {$exercice->date_fin->format('d/m/Y')}) validée. Résultat net : " .
                    number_format((float) $exercice->resultat_net_cloture, 2, ',', ' ') .
                    ". Nouvel exercice ouvert du {$nouvelleDateDebut->format('d/m/Y')} au {$nouvelleDateFin->format('d/m/Y')}, avec report à nouveau sur " . $mouvements->count() . ' compte(s).'
            );

            return $exercice;
        });
    }

    /**
     * Rejette une proposition de clôture : remet l'exercice en OUVERT (le Comptable pourra
     * corriger des écritures puis re-proposer).
     */
    public function rejeterCloture(ExerciceComptable $exercice, string $matricule, ?string $motif = null): ExerciceComptable
    {
        if ($exercice->statut !== 'EN_ATTENTE_VALIDATION') {
            throw new \RuntimeException("Cet exercice n'est pas en attente de validation.");
        }

        $exercice->update([
            'statut' => 'OUVERT',
            'rejete_par_matricule' => $matricule,
            'rejete_le' => now(),
            'resultat_net_cloture' => null,
            'propose_par_matricule' => null,
            'propose_le' => null,
            'observations' => $motif,
        ]);

        ActivityLog::record(
            'COMPTABILITE',
            'EXERCICE_CLOTURE_REJETEE',
            $exercice,
            (string) $exercice->annee,
            "Rejet de la proposition de clôture de l'exercice {$exercice->annee}" . ($motif ? " — motif : {$motif}" : '')
        );

        return $exercice;
    }
}
