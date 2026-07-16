<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Credit\CreditDemande;
use App\Models\Clients\Compte;
use App\Models\Clients\Client;
use App\Models\Caisse\Transaction;
use App\Models\RH\Agent;
use App\Models\Zone;
use App\Models\Tresorerie\Portefeuille;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RecouvrementController extends Controller
{
    /**
     * Affiche le tableau de bord de recouvrement avec le tri intelligent
     */
    public function index()
    {
        // Requête Eloquent avec tri par priorité (CASE WHEN)
        // Affiche uniquement les dossiers avec au moins une échéance EN_RETARD ou EN_ATTENTE avec date dépassée
        $today = Carbon::now()->toDateString();
        $dossiers = CreditDemande::whereNotIn('statut_global', ['SOLDE', 'ANNULE'])
            ->where(function ($query) use ($today) {
                $query->whereHas('echeancier.echeances', function ($q) use ($today) {
                        $q->where('statut', 'EN_RETARD')
                          ->orWhere(function ($sub) use ($today) {
                              $sub->where('statut', 'EN_ATTENTE')
                                  ->where('date_echeance', '<', $today);
                          });
                    })
                    ->orWhere('statut_global', 'EN_RETARD');
            })
            ->with(['client', 'echeancier.echeances' => function ($query) {
                $query->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD'])
                      ->orderBy('date_echeance', 'ASC');
            }])
            ->selectRaw('
                tb_credit_demandes.*,
                (
                    SELECT MIN(date_echeance) 
                    FROM tb_credit_echeances 
                    INNER JOIN tb_credit_echeanciers ON tb_credit_echeances.echeancier_id = tb_credit_echeanciers.id
                    WHERE tb_credit_echeanciers.credit_demande_id = tb_credit_demandes.id 
                      AND tb_credit_echeances.statut IN ("EN_ATTENTE", "EN_RETARD")
                ) as prochaine_echeance_date,
                
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM tb_credit_echeances 
                        INNER JOIN tb_credit_echeanciers ON tb_credit_echeances.echeancier_id = tb_credit_echeanciers.id
                        WHERE tb_credit_echeanciers.credit_demande_id = tb_credit_demandes.id 
                        AND tb_credit_echeances.statut IN ("EN_ATTENTE", "EN_RETARD") 
                        AND DATE(tb_credit_echeances.date_echeance) < CURDATE()
                    ) THEN 1
                    
                    WHEN EXISTS (
                        SELECT 1 FROM tb_credit_echeances 
                        INNER JOIN tb_credit_echeanciers ON tb_credit_echeances.echeancier_id = tb_credit_echeanciers.id
                        WHERE tb_credit_echeanciers.credit_demande_id = tb_credit_demandes.id 
                        AND tb_credit_echeances.statut = "EN_ATTENTE" 
                        AND DATE(tb_credit_echeances.date_echeance) = CURDATE()
                    ) THEN 2
                    
                    WHEN EXISTS (
                        SELECT 1 FROM tb_credit_echeances 
                        INNER JOIN tb_credit_echeanciers ON tb_credit_echeances.echeancier_id = tb_credit_echeanciers.id
                        WHERE tb_credit_echeanciers.credit_demande_id = tb_credit_demandes.id 
                        AND tb_credit_echeances.statut = "EN_ATTENTE" 
                        AND tb_credit_echeances.date_echeance BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 5 DAY)
                    ) THEN 3
                    
                    WHEN EXISTS (
                        SELECT 1 FROM tb_credit_echeances 
                        INNER JOIN tb_credit_echeanciers ON tb_credit_echeances.echeancier_id = tb_credit_echeanciers.id
                        WHERE tb_credit_echeanciers.credit_demande_id = tb_credit_demandes.id 
                        AND tb_credit_echeances.statut IN ("EN_ATTENTE", "EN_RETARD")
                    ) THEN 4
                    
                    ELSE 5
                END as priorite_score
            ')
            ->orderBy('priorite_score', 'ASC')
            ->orderBy('prochaine_echeance_date', 'ASC')
            ->paginate(20);

        // Compteur pour le widget du tableau de bord principal
        // Compte les dossiers ayant au moins une échéance dépassée (EN_ATTENTE ou EN_RETARD avec date < aujourd'hui)
        $today = Carbon::now()->toDateString();
        $alerteRecouvrementCount = CreditDemande::whereNotIn('statut_global', ['SOLDE', 'ANNULE'])
            ->whereHas('echeancier.echeances', function ($query) use ($today) {
                $query->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD'])
                      ->where('date_echeance', '<', $today);
            })
            ->count();

        return view('recouvrement.index', compact('dossiers', 'alerteRecouvrementCount'));
    }

    /**
     * Historique des recouvrements automatiques avec filtres
     */
    public function historique(Request $request)
    {
        $query = Transaction::where('reference', 'LIKE', 'AUTO-REC-%')
            ->where('statut', 'CONFIRME')
            ->with(['compte.client', 'compte.client.zone', 'compte.portefeuille']);

        if ($request->filled('date_debut')) {
            $query->whereDate('date_operation', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_operation', '<=', $request->date_fin);
        }
        if ($request->filled('devise')) {
            $query->where('devise_code', $request->devise);
        }
        if ($request->filled('zone')) {
            $query->whereHas('compte.client', function ($q) use ($request) {
                $q->where('code_zone', $request->zone);
            });
        }
        if ($request->filled('portefeuille')) {
            $query->whereHas('compte', function ($q) use ($request) {
                $q->where('portefeuille_id', $request->portefeuille);
            });
        }
        if ($request->filled('agent_declencheur')) {
            $query->where('agent_matricule', $request->agent_declencheur);
        }
        if ($request->filled('statut_recouvrement')) {
            if ($request->statut_recouvrement === 'total') {
                $query->whereRaw('observations LIKE ?', ['%total%']);
            } elseif ($request->statut_recouvrement === 'partiel') {
                $query->whereRaw('observations LIKE ?', ['%partiel%']);
            }
        }

        $historique = $query->orderBy('date_operation', 'DESC')->paginate(20);

        $agents = Agent::where('statut', 'actif')->orderBy('nom')->get();
        $zones = Zone::orderBy('nom')->get();
        $portefeuilles = Portefeuille::orderBy('nom_portefeuille')->get();

        $totalRecupere = Transaction::where('reference', 'LIKE', 'AUTO-REC-%')
            ->where('statut', 'CONFIRME')
            ->sum('montant');

        return view('recouvrement.historique', compact(
            'historique', 'agents', 'zones', 'portefeuilles', 'totalRecupere'
        ));
    }

    /**
     * Impression de l'historique des recouvrements (PDF ou CSV)
     */
    public function printHistorique(Request $request)
    {
        ini_set('memory_limit', '768M');

        $query = Transaction::where('reference', 'LIKE', 'AUTO-REC-%')
            ->where('statut', 'CONFIRME')
            ->with(['compte.client', 'compte.client.zone', 'compte.portefeuille']);

        if ($request->filled('date_debut')) {
            $query->whereDate('date_operation', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_operation', '<=', $request->date_fin);
        }
        if ($request->filled('devise')) {
            $query->where('devise_code', $request->devise);
        }
        if ($request->filled('zone')) {
            $query->whereHas('compte.client', function ($q) use ($request) {
                $q->where('code_zone', $request->zone);
            });
        }
        if ($request->filled('portefeuille')) {
            $query->whereHas('compte', function ($q) use ($request) {
                $q->where('portefeuille_id', $request->portefeuille);
            });
        }
        if ($request->filled('agent_declencheur')) {
            $query->where('agent_matricule', $request->agent_declencheur);
        }

        $query->orderBy('date_operation', 'DESC');

        $exportFormat = strtolower((string) $request->input('export_format', 'pdf'));
        $outputMode = strtolower((string) $request->input('output', 'stream'));

        if ($exportFormat === 'csv') {
            $filename = 'Historique_recouvrement_' . now()->format('Ymd_His') . '.csv';

            return response()->streamDownload(function () use ($query) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($handle, [
                    'Date/Heure', 'Dossier', 'Client', 'Zone', 'Portefeuille',
                    'Montant prélevé', 'Devise', 'Solde avant', 'Solde après',
                    'Échéance', 'Statut', 'Référence'
                ], ';');

                $query->chunk(1000, function ($transactions) use ($handle) {
                    foreach ($transactions as $tx) {
                        $client = $tx->compte->client ?? null;
                        $zone = $client->zone ?? null;
                        $portefeuille = $tx->compte->portefeuille ?? null;
                        $echNumero = '';
                        if (preg_match('/Ech\.\s*(\d+)/', $tx->observations ?? '', $m)) {
                            $echNumero = $m[1];
                        }
                        $isPartiel = stripos($tx->observations ?? '', 'partiel') !== false;
                        $numeroDossier = '';
                        if (preg_match('/AUTO-REC-(.+?)-\d+-/', $tx->reference ?? '', $m)) {
                            $numeroDossier = $m[1];
                        }

                        fputcsv($handle, [
                            $tx->date_operation->format('d/m/Y H:i'),
                            $numeroDossier,
                            $client ? $client->full_name : '-',
                            $zone ? $zone->nom : '-',
                            $portefeuille ? $portefeuille->nom_portefeuille : '-',
                            $tx->montant,
                            $tx->devise_code,
                            $tx->solde_compte_avant,
                            $tx->solde_compte_apres,
                            $echNumero ?: '-',
                            $isPartiel ? 'Partiel' : 'Total',
                            $tx->reference,
                        ], ';');
                    }
                });

                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        $historique = $query->get();
        $totalRecupere = $historique->sum('montant');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('impressions.recouvrement.historique', compact(
            'historique', 'totalRecupere'
        ))->setPaper('a4', 'landscape');

        if ($outputMode === 'download') {
            return $pdf->download('Historique_recouvrement.pdf');
        }

        return $pdf->stream('Historique_recouvrement.pdf');
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

            $devise = $dossiersCibles->isNotEmpty() ? $dossiersCibles->first()->devise : 'CDF';
            
            DB::commit();

            return redirect()->back()->with('success', 
                "✅ Recouvrement terminé : {$dossiersTraités} dossiers vérifiés, {$totalRecupere} {$devise} récupérés automatiquement."
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '❌ Erreur lors du recouvrement automatique : ' . $e->getMessage());
        }
    }
}