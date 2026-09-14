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

        // Récupérer toutes les échéances impayées dans l'ordre chronologique.
        // PARTIELLEMENT_PAYE inclus : sinon un dépôt qui devrait compléter une
        // échéance déjà partiellement réglée sautait directement à l'échéance
        // suivante (voire une future si prélèvement anticipé autorisé), et le
        // dossier pouvait ensuite être marqué SOLDE à tort plus bas alors qu'il
        // restait une échéance PARTIELLEMENT_PAYE non soldée.
        $echeancesImpayees = $dossier->echeancier?->echeances()
            ->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD', 'PARTIELLEMENT_PAYE'])
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
            // IMPORTANT : total_echeance = capital + intérêt + commission (voir
            // AmortissementService::calculer()). Utiliser uniquement
            // montant_capital + montant_interet ici ignorait la commission de
            // l'échéance et faisait passer celle-ci en "PAYE" alors qu'il
            // manquait encore la commission (partout ailleurs dans l'app —
            // CreditController::storeRemboursement, RecouvrementController —
            // le "reste dû" est calculé sur total_echeance, d'où l'incohérence
            // "PARTIELLEMENT_PAYE" persistante malgré un dépôt du montant complet).
            $montantTotalEcheance = round((float) $echeance->total_echeance, 2);
            $montantDejaPaye = (float) $echeance->montant_paye;
            $montantManquant = max(0, round($montantTotalEcheance - $montantDejaPaye, 2));
            
            if ($montantManquant <= 0) {
                continue; // Déjà payée (sécurité)
            }

            // Montant à appliquer sur cette échéance
            $montantApplique = min($montantRestant, $montantManquant);

            // Répartition (même ordre d'imputation que storeRemboursement()) :
            // intérêt d'abord, puis commission, puis capital.
            $interetEcheance    = (float) $echeance->montant_interet;
            $commissionEcheance = (float) $echeance->commission_echeance;
            $interetRestant     = max(0, round($interetEcheance - ($montantDejaPaye * ($interetEcheance / max($montantTotalEcheance, 1))), 2));
            $commissionRestant  = max(0, round($commissionEcheance - ($montantDejaPaye * ($commissionEcheance / max($montantTotalEcheance, 1))), 2));

            $dontInteret = min($montantApplique, $interetRestant);
            $resteApresInteret = round($montantApplique - $dontInteret, 2);
            $dontCommission = min($resteApresInteret, $commissionRestant);
            $dontCapital = round($resteApresInteret - $dontCommission, 2);

            // Créer le remboursement
            CreditRemboursement::create([
                'credit_demande_id'  => $dossier->id,
                'echeance_id'        => $echeance->id,
                'compte_id'          => $compte->code_compte,
                'devise'             => $compte->devise,
                'montant_recu'       => $montantApplique,
                'dont_capital'       => $dontCapital,
                'dont_interet'       => $dontInteret,
                'dont_commission'    => $dontCommission,
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

        // Recalcule SOLDE / EN_RETARD / EN_REMBOURSEMENT via la méthode
        // centralisée (CreditDemande::refreshStatutRetard()) — même logique
        // que le remboursement manuel et le recouvrement automatique, pour
        // que ce 3e chemin de paiement (dépôt RMB déclenchant un règlement
        // immédiat) reste toujours cohérent avec les deux autres. Corrige au
        // passage le bug où un dossier avec une échéance PARTIELLEMENT_PAYE
        // restante pouvait être marqué SOLDE à tort (ancien code ne vérifiait
        // que EN_ATTENTE/EN_RETARD pour "toutes soldées").
        $statutAvant = $dossier->statut_global;
        $dossier->refresh();
        $dossier->load('echeancier.echeances');
        $dossier->refreshStatutRetard();

        if ($dossier->statut_global === 'SOLDE' && $statutAvant !== 'SOLDE') {
            // Restitution de la caution (GTC)
            $this->restituerCaution($dossier, $transaction, $compte);
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
