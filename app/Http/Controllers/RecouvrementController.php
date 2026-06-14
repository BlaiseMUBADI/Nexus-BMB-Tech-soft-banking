<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Credit\CreditDemande;
use App\Models\Clients\Compte;
use App\Models\Caisse\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RecouvrementController extends Controller
{
    public function __construct()
    {
        // Seuls l'Administrateur (EBEN-ROL1) et le Gérant (EBEN-ROL12) ont accès
        // via la permission EBEN-PER90 créée précédemment
        $this->middleware(['auth', 'permission:EBEN-PER90']);
    }

    /**
     * Affiche le tableau de bord de recouvrement avec le tri intelligent
     */
    public function index()
    {
        // Requête Eloquent avec tri par priorité (CASE WHEN)
        $dossiers = CreditDemande::whereNotIn('statut_global', ['SOLDE', 'ANNULE'])
            ->with(['client', 'echeancier.echeances' => function ($query) {
                $query->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD'])
                      ->orderBy('date_echeance', 'ASC');
            }])
            ->selectRaw('
                tb_credit_demandes.*,
                (
                    SELECT MIN(date_echeance) 
                    FROM tb_credit_echeances 
                    WHERE echeancier_id = tb_credit_echeanciers.id 
                      AND statut IN ("EN_ATTENTE", "EN_RETARD")
                ) as prochaine_echeance_date,
                
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM tb_credit_echeances 
                        WHERE echeancier_id = tb_credit_echeanciers.id 
                        AND statut IN ("EN_ATTENTE", "EN_RETARD") 
                        AND DATE(date_echeance) < CURDATE()
                    ) THEN 1  -- Priorité 1 : EN RETARD
                    
                    WHEN EXISTS (
                        SELECT 1 FROM tb_credit_echeances 
                        WHERE echeancier_id = tb_credit_echeanciers.id 
                        AND statut = "EN_ATTENTE" 
                        AND DATE(date_echeance) = CURDATE()
                    ) THEN 2  -- Priorité 2 : DU JOUR
                    
                    WHEN EXISTS (
                        SELECT 1 FROM tb_credit_echeances 
                        WHERE echeancier_id = tb_credit_echeanciers.id 
                        AND statut = "EN_ATTENTE" 
                        AND date_echeance BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 5 DAY)
                    ) THEN 3  -- Priorité 3 : PROCHE (5 jours)
                    
                    ELSE 4    -- Priorité 4 : À JOUR
                END as priorite_score
            ')
            ->orderBy('priorite_score', 'ASC')
            ->orderBy('prochaine_echeance_date', 'ASC')
            ->paginate(20);

        // Compteur pour le widget du tableau de bord principal
        $alerteRecouvrementCount = CreditDemande::where('prelevement_auto_autorise', 1)
            ->whereNotIn('statut_global', ['SOLDE', 'ANNULE'])
            ->whereHas('echeancier.echeances', function ($query) {
                $query->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD'])
                      ->whereDate('date_echeance', '<=', now()->addDays(1)); // Retard ou demain
            })
            ->count();

        return view('recouvrement.index', compact('dossiers', 'alerteRecouvrementCount'));
    }

    /**
     * Exécute le recouvrement automatique sur les dossiers éligibles
     */
    public function runAutoCollection(Request $request)
    {
        DB::beginTransaction();
        try {
            $totalRecupere = 0;
            $dossiersTraités = 0;

            // Récupérer tous les dossiers autorisés ayant une échéance en retard ou du jour
            $dossiersCibles = CreditDemande::where('prelevement_auto_autorise', 1)
                ->whereNotIn('statut_global', ['SOLDE', 'ANNULE'])
                ->with(['echeancier.echeances' => function ($query) {
                    $query->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD'])
                          ->orderBy('date_echeance', 'ASC');
                }, 'client'])
                ->get();

            foreach ($dossiersCibles as $dossier) {
                $compteRmb = Compte::where('client_matricule', $dossier->client_matricule)
                    ->where('type', 'RMB')
                    ->where('devise', $dossier->devise)
                    ->lockForUpdate()
                    ->first();

                if (!$compteRmb) continue;

                foreach ($dossier->echeancier->echeances as $echeance) {
                    $resteDu = max(0, (float)$echeance->total_echeance - (float)$echeance->montant_paye);
                    
                    if ($resteDu <= 0.01) continue;

                    // Vérifier si le solde est suffisant (même partiellement, Option A)
                    $montantAPrelever = min((float)$compteRmb->solde_reel, $resteDu);

                    if ($montantAPrelever > 0.01) {
                        // 1. Débiter le compte RMB (Option A : même partiel)
                        $compteRmb->decrement('solde_reel', $montantAPrelever);

                        // 2. Mettre à jour l'échéance
                        $nouveauMontantPaye = (float)$echeance->montant_paye + $montantAPrelever;
                        $nouveauStatut = $nouveauMontantPaye >= $echeance->total_echeance ? 'PAYE' : $echeance->statut;
                        
                        // Si c'était en retard et qu'on a payé en partie, ça reste en retard. 
                        // Si c'est payé totalement, ça passe à PAYE.
                        if ($nouveauStatut === 'PAYE') {
                            $echeance->date_paiement_effectif = now()->format('Y-m-d');
                        }
                        
                        $echeance->update([
                            'montant_paye' => $nouveauMontantPaye,
                            'statut' => $nouveauStatut,
                        ]);

                        // 3. Enregistrer la transaction de recouvrement auto
                        Transaction::create([
                            'compte_code'             => $compteRmb->code_compte,
                            'agent_matricule'         => 'SYSTEM-AUTO',
                            'guichet_id'              => null, // Pas de guichet, c'est un débit interne
                            'devise_code'             => $dossier->devise,
                            'type'                    => 'REMBOURSEMENT', // Ou un type spécifique 'RECOUVREMENT_AUTO'
                            'montant'                 => $montantAPrelever,
                            'montant_commission_total'=> 0,
                            'solde_compte_avant'      => (float)$compteRmb->solde_reel + $montantAPrelever,
                            'solde_compte_apres'      => (float)$compteRmb->solde_reel,
                            'montant_total_client'    => $montantAPrelever,
                            'montant_net_client'      => $montantAPrelever,
                            'reference'               => 'AUTO-REC-' . $dossier->numero_dossier . '-' . $echeance->numero_echeance . '-' . now()->format('dmyHis'),
                            'observations'            => 'Recouvrement automatique (Ech. ' . $echeance->numero_echeance . ')',
                            'statut'                  => 'CONFIRME',
                            'date_operation'          => now(),
                        ]);

                        $totalRecupere += $montantAPrelever;
                        
                        // Si le solde RMB tombe à 0, on arrête pour ce dossier et on passe au suivant
                        if ($compteRmb->solde_reel <= 0.01) break;
                    }
                }
                
                // Vérifier si le dossier est maintenant soldé
                $toutesSoldees = $dossier->echeancier->echeances->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD'])->isEmpty();
                if ($toutesSoldees && $dossier->statut_global !== 'SOLDE') {
                    $dossier->update(['statut_global' => 'SOLDE', 'date_cloture' => now()]);
                }

                $dossiersTraités++;
            }

            DB::commit();

            return redirect()->back()->with('success', 
                "✅ Recouvrement terminé : {$dossiersTraités} dossiers vérifiés, {$totalRecupere} {$dossier->devise ?? 'CDF'} récupérés automatiquement."
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '❌ Erreur lors du recouvrement automatique : ' . $e->getMessage());
        }
    }
}