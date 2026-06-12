<?php

namespace App\Listeners;

use App\Events\DepositOnRmbAccount;
use App\Models\Credit\CreditDemande;
use App\Models\Credit\CreditRemboursement;
use Illuminate\Support\Facades\Log;

class ProcessAutomaticCreditRepayment
{
    public function handle(DepositOnRmbAccount $event)
    {
        $compte = $event->compte;
        $montant = $event->montant;
        $transaction = $event->transaction;

        // Vérifier si le compte appartient à un client
        if (!$compte || !$compte->client) {
            return;
        }

        $client = $compte->client;

        // Trouver les dossiers crédit en cours pour ce client
        $dossiersEnCours = CreditDemande::where('client_matricule', $client->matricule)
            ->whereIn('statut_global', ['EN_REMBOURSEMENT', 'EN_RETARD'])
            ->with(['echeancier.echeances'])
            ->orderBy('created_at')
            ->get();

        if ($dossiersEnCours->isEmpty()) {
            return; // Aucun crédit en cours
        }

        // Traiter chaque dossier (du plus ancien au plus récent)
        $montantRestant = $montant;

        foreach ($dossiersEnCours as $dossier) {
            if ($montantRestant <= 0) {
                break;
            }

            // La méthode met à jour $montantRestant par référence
            $this->processerRemboursementDossier($dossier, $montantRestant, $transaction, $compte);
        }
    }

    private function processerRemboursementDossier($dossier, &$montantRestant, $transaction, $compte)
    {
        $aujourdhui = now()->toDateString();
        $autoDebitAnticipeAutorise = !empty($dossier->prelevement_auto_autorise);

        // Récupérer toutes les échéances impayées dans l'ordre chronologique
        $echeancesImpayees = $dossier->echeancier?->echeances()
            ->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD'])
            ->orderBy('numero_echeance')
            ->get();

        if (!$echeancesImpayees || $echeancesImpayees->isEmpty()) {
            return;
        }

        foreach ($echeancesImpayees as $echeance) {
            if ($montantRestant <= 0) {
                break; // Plus d'argent à appliquer
            }

            // Vérifier si on a le droit de prélever cette échéance
            $estEnRetardOuEcheance = $echeance->date_echeance <= $aujourdhui;
            
            if (!$estEnRetardOuEcheance && !$autoDebitAnticipeAutorise) {
                // On s'arrête ici : les échéances suivantes seront encore plus dans le futur
                Log::info('Arrêt du prélèvement auto : échéance future non autorisée', [
                    'dossier' => $dossier->numero_dossier,
                    'echeance_numero' => $echeance->numero_echeance,
                    'date_echeance' => $echeance->date_echeance
                ]);
                break;
            }

            // Calculer le montant manquant pour cette échéance
            $montantTotalEcheance = (float) $echeance->montant_capital + (float) $echeance->montant_interet;
            $montantDejaPaye = (float) $echeance->montant_paye;
            $montantManquant = max(0, $montantTotalEcheance - $montantDejaPaye);
            
            if ($montantManquant <= 0) {
                continue; // Déjà payée (sécurité)
            }

            // Montant à appliquer sur cette échéance
            $montantApplique = min($montantRestant, $montantManquant);
            
            // Répartition simplifiée : intérêt d'abord, puis capital
            $dontInteret = min($montantApplique, (float) $echeance->montant_interet);
            $dontCapital = $montantApplique - $dontInteret;

            // Créer le remboursement
            CreditRemboursement::create([
                'credit_demande_id'  => $dossier->id,
                'echeance_id'        => $echeance->id,
                'compte_id'          => $compte->code_compte,
                'devise'             => $compte->devise,
                'montant_recu'       => $montantApplique,
                'dont_capital'       => $dontCapital,
                'dont_interet'       => $dontInteret,
                'dont_penalite'      => 0,
                'recu_le'            => now(),
                'transaction_id'     => $transaction->id,
                'agent_matricule'    => $transaction->agent_matricule,
                'reference_caisse'   => $transaction->reference,
                'type_remboursement' => $montantApplique >= $montantManquant ? 'ECHEANCE' : 'PARTIEL',
                'observations'       => 'Prélèvement automatique (dépôt RMB)',
            ]);

            // Mettre à jour l'échéance
            $nouveauMontantPaye = $montantDejaPaye + $montantApplique;
            $nouveauStatut = $nouveauMontantPaye >= $montantTotalEcheance ? 'PAYE' : 'PARTIELLEMENT_PAYE';
            
            $echeance->update([
                'montant_paye'           => $nouveauMontantPaye,
                'statut'                 => $nouveauStatut,
                'date_paiement_effectif' => now(),
            ]);

            // Décrémenter le montant restant à traiter global
            $montantRestant -= $montantApplique;
        }

        // Vérifier si le dossier est soldé après ces traitements
        $toutesSoldees = $dossier->echeancier?->echeances()
            ->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD'])
            ->exists() === false;

        if ($toutesSoldees) {
            $dossier->update([
                'statut_global' => 'SOLDE',
                'date_cloture' => now(),
            ]);
            
            // Restitution de la caution (GTC)
            $this->restituerCaution($dossier, $transaction, $compte);
        } elseif ($dossier->statut_global === 'EN_RETARD') {
            // Si le dossier était en retard et qu'il y a encore des échéances, repasser en EN_REMBOURSEMENT
            $prochaineEcheanceImpayee = $dossier->echeancier?->echeances()
                ->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD'])
                ->orderBy('numero_echeance')
                ->first();
            
            if ($prochaineEcheanceImpayee && $prochaineEcheanceImpayee->date_echeance >= $aujourdhui) {
                $dossier->update(['statut_global' => 'EN_REMBOURSEMENT']);
            }
        }

        Log::info('Remboursement automatique traité', [
            'dossier' => $dossier->numero_dossier,
            'montant_restant_apres_traitement' => $montantRestant,
        ]);
    }

    private function restituerCaution($dossier, $transaction, $compte)
    {
        $deblocage = $dossier->deblocage()->first();
        $cautionARestituer = round((float) ($deblocage?->montant_caution ?? 0), 2);

        if ($cautionARestituer > 0) {
            $compteGtc = \App\Models\Clients\Compte::where('client_matricule', $dossier->client_matricule)
                ->where('type', 'GTC')
                ->where('devise', $dossier->devise)
                ->lockForUpdate()
                ->first();

            if ($compteGtc) {
                $montantCaution = min($cautionARestituer, round((float) ($compteGtc->solde_bloque ?? 0), 2));
                
                if ($montantCaution > 0) {
                    $compteGtc->decrement('solde_bloque', $montantCaution);
                    $compteGtc->decrement('solde_reel', $montantCaution);
                    $compte->increment('solde_reel', $montantCaution);

                    \App\Models\Caisse\Transaction::create([
                        'compte_code'             => $compte->code_compte,
                        'agent_matricule'         => $transaction->agent_matricule,
                        'guichet_id'              => $transaction->guichet_id,
                        'devise_code'             => $dossier->devise,
                        'type'                    => \App\Models\Caisse\Transaction::DEPOT,
                        'montant'                 => $montantCaution,
                        'montant_commission_total'=> 0,
                        'solde_compte_avant'      => (float) $compte->solde_reel - $montantCaution,
                        'solde_compte_apres'      => (float) $compte->solde_reel,
                        'montant_total_client'    => $montantCaution,
                        'montant_net_client'      => $montantCaution,
                        'reference'               => 'CAUTION-RESTIT-' . $dossier->numero_dossier,
                        'observations'            => sprintf(
                            'Restitution auto caution 20%% (%s %s) – crédit %s soldé.',
                            number_format($montantCaution, 2),
                            $dossier->devise,
                            $dossier->numero_dossier
                        ),
                        'statut'                  => \App\Models\Caisse\Transaction::CONFIRME,
                        'date_operation'          => now(),
                    ]);
                }
            }
        }
    }
}
