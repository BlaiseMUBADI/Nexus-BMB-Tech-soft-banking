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
    public function index(Request $request)
    {
        // Auto-réparation (même throttle que CreditController::index()) :
        // garantit que `statut_global` est à jour avant de construire la
        // liste et le compteur d'alertes de cette page.
        if (\Illuminate\Support\Facades\Cache::add('credit_sync_retards_lock', true, 60)) {
            \Illuminate\Support\Facades\Artisan::call('credit:marquer-retards');
        }

        // Requête Eloquent avec tri par priorité (CASE WHEN)
        // Affiche les dossiers avec au moins une échéance EN_RETARD, EN_ATTENTE
        // avec date dépassée, OU PARTIELLEMENT_PAYE avec date dépassée (règlement
        // partiel déjà reçu mais échéance pas encore soldée — reste réellement en
        // retard, cf. audit du 09/09/2026 : ces dossiers étaient comptés via le
        // repli sur statut_global mais leurs colonnes "Prochaine échéance"/"Jours
        // de retard" ignoraient l'échéance PARTIELLEMENT_PAYE et affichaient à
        // tort l'échéance suivante, non encore due).
        $today = Carbon::now()->toDateString();
        // SUSPENDU exclu (cf. CreditDemande::scopeEnRetardReel, 20/09/2026) :
        // un dossier suspendu suit un traitement spécial et ne doit pas
        // apparaître dans les listes de recouvrement (il reste dans l'onglet
        // "Alertes" de la liste des dossiers).
        $query = CreditDemande::whereNotIn('statut_global', ['SOLDE', 'ANNULE', 'SUSPENDU'])
            ->where(function ($query) use ($today) {
                $query->whereHas('echeancier.echeances', function ($q) use ($today) {
                        $q->where('statut', 'EN_RETARD')
                          ->orWhere(function ($sub) use ($today) {
                              $sub->whereIn('statut', ['EN_ATTENTE', 'PARTIELLEMENT_PAYE'])
                                  ->where('date_echeance', '<', $today);
                          });
                    })
                    ->orWhere('statut_global', 'EN_RETARD');
            });

        // Recherche : nom complet en ordre libre (« NOM POSTNOM Prénom »,
        // cf. Client::scopeSearchFullName), matricule ou n° de dossier.
        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('numero_dossier', 'like', "%{$search}%")
                  ->orWhere('client_matricule', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->searchFullName($search);
                  });
            });
        }

        $dossiers = $query->with(['client', 'echeancier.echeances' => function ($query) {
                $query->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD', 'PARTIELLEMENT_PAYE'])
                      ->orderBy('date_echeance', 'ASC');
            }])
            ->selectRaw('
                tb_credit_demandes.*,
                (
                    SELECT MIN(date_echeance) 
                    FROM tb_credit_echeances 
                    INNER JOIN tb_credit_echeanciers ON tb_credit_echeances.echeancier_id = tb_credit_echeanciers.id
                    WHERE tb_credit_echeanciers.credit_demande_id = tb_credit_demandes.id 
                      AND tb_credit_echeances.statut IN ("EN_ATTENTE", "EN_RETARD", "PARTIELLEMENT_PAYE")
                ) as prochaine_echeance_date,
                
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM tb_credit_echeances 
                        INNER JOIN tb_credit_echeanciers ON tb_credit_echeances.echeancier_id = tb_credit_echeanciers.id
                        WHERE tb_credit_echeanciers.credit_demande_id = tb_credit_demandes.id 
                        AND tb_credit_echeances.statut IN ("EN_ATTENTE", "EN_RETARD", "PARTIELLEMENT_PAYE") 
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
                        AND tb_credit_echeances.statut IN ("EN_ATTENTE", "EN_RETARD", "PARTIELLEMENT_PAYE")
                    ) THEN 4
                    
                    ELSE 5
                END as priorite_score
            ')
            ->orderBy('priorite_score', 'ASC')
            ->orderBy('prochaine_echeance_date', 'ASC')
            ->paginate(20)
            ->withQueryString();

        // scopeEnRetardReel() = source unique (cf. CreditDemande) — même
        // définition que DashboardController/AppServiceProvider/CreditController,
        // pour éviter toute nouvelle incohérence entre les compteurs affichés.
        $alerteRecouvrementCount = CreditDemande::enRetardReel()->count();

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

        $historique = $query->orderBy('date_operation', 'DESC')->paginate(20)->withQueryString();

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
            $totalRecupereParDevise = []; // ex: ['USD' => 1234.56, 'CDF' => 200000]
            $dossiersAvecPrelevement = 0; // dossiers où un montant a RÉELLEMENT été prélevé
            $today = Carbon::now()->toDateString();

            // Récupérer tous les dossiers autorisés ayant une échéance RÉELLEMENT
            // en retard (date dépassée) — PAS les échéances futures pas encore
            // dues. Sur demande explicite : si le client n'est pas en retard, le
            // recouvrement auto ne doit rien prélever et laisser le surplus du
            // RMB intact (pas de paiement en avance des mensualités futures).
            $dossiersCibles = CreditDemande::where('prelevement_auto_autorise', 1)
                // SUSPENDU exclu : pas de prélèvement automatique sur un
                // dossier suspendu (traitement spécial, cf. scopeEnRetardReel).
                ->whereNotIn('statut_global', ['SOLDE', 'ANNULE', 'SUSPENDU'])
                ->with(['echeancier.echeances' => function ($query) use ($today) {
                    $query->where(function ($q) use ($today) {
                        $q->where('statut', 'EN_RETARD')
                          ->orWhere(function ($q2) use ($today) {
                              $q2->whereIn('statut', ['EN_ATTENTE', 'PARTIELLEMENT_PAYE'])
                                 ->where('date_echeance', '<', $today);
                          });
                    })->orderBy('date_echeance', 'ASC');
                }, 'client'])
                ->get();

            foreach ($dossiersCibles as $dossier) {
                $compteRmb = Compte::where('client_matricule', $dossier->client_matricule)
                    ->where('type', 'RMB')
                    ->where('devise', $dossier->devise)
                    ->lockForUpdate()
                    ->first();

                if (!$compteRmb) continue;

                $preleveSurCeDossier = false;

                foreach ($dossier->echeancier->echeances as $echeance) {
                    $resteDu = max(0, (float)$echeance->total_echeance - (float)$echeance->montant_paye);
                    
                    if ($resteDu <= 0.01) continue;

                    // Vérifier si le solde est suffisant (même partiellement, Option A)
                    $montantAPrelever = min((float)$compteRmb->solde_reel, $resteDu);

                    if ($montantAPrelever > 0.01) {
                        // 1. Débiter le compte RMB (Option A : même partiel)
                        $compteRmb->decrement('solde_reel', $montantAPrelever);

                        // 2. Mettre à jour l'échéance
                        // BUG corrigé : un règlement partiel gardait l'ancien statut de
                        // l'échéance (ex: "EN_ATTENTE" si elle n'était pas encore en retard),
                        // au lieu de passer explicitement à PARTIELLEMENT_PAYE — ce qui
                        // faisait ensuite manquer cette échéance dans toutes les requêtes
                        // qui filtrent sur ['EN_ATTENTE','EN_RETARD','PARTIELLEMENT_PAYE'].
                        $nouveauMontantPaye = (float)$echeance->montant_paye + $montantAPrelever;
                        $nouveauStatut = $nouveauMontantPaye >= $echeance->total_echeance ? 'PAYE' : 'PARTIELLEMENT_PAYE';

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

                        // Cumul par devise : les dossiers ciblés peuvent mélanger
                        // USD et CDF, un total unique aurait été trompeur (bug
                        // constaté : le message affichait toujours la devise du
                        // PREMIER dossier de la liste pour la somme de TOUS).
                        $totalRecupereParDevise[$dossier->devise] =
                            ($totalRecupereParDevise[$dossier->devise] ?? 0) + $montantAPrelever;
                        $preleveSurCeDossier = true;

                        // Si le solde RMB tombe à 0, on arrête pour ce dossier et on passe au suivant
                        if ($compteRmb->solde_reel <= 0.01) break;
                    }
                }
                
                // Recalcule SOLDE / EN_RETARD / EN_REMBOURSEMENT à partir de
                // TOUTES les échéances fraîches en base (pas seulement le sous-
                // ensemble EN_ATTENTE/EN_RETARD chargé en mémoire avant le
                // traitement, qui ratait les échéances déjà PARTIELLEMENT_PAYE
                // lors d'une exécution précédente — même méthode centralisée
                // que le remboursement manuel et l'affichage du dossier, pour
                // que "Liste des dossiers" et "Recouvrement Auto" restent
                // toujours cohérents).
                $dossier->refresh();
                $dossier->load('echeancier.echeances');
                $dossier->refreshStatutRetard();

                if ($preleveSurCeDossier) {
                    $dossiersAvecPrelevement++;
                }
            }

            DB::commit();

            $nbDossiersCibles = $dossiersCibles->count();

            // BUG corrigé : le message affichait toujours "success" (vert) même
            // quand 0 dossier avait un prélèvement possible (ex : aucun dossier
            // n'a le consentement client "prelevement_auto_autorise", ou tous les
            // RMB sont à 0) — l'utilisateur ne pouvait pas distinguer "tout va
            // bien, rien à faire" de "rien n'a été fait alors que ça devrait".
            if (empty($totalRecupereParDevise)) {
                return redirect()->back()->with('warning',
                    "⚠️ Recouvrement terminé : {$nbDossiersCibles} dossier(s) examiné(s), mais AUCUN prélèvement "
                    . "n'a pu être effectué. Vérifiez que ces dossiers ont le prélèvement automatique autorisé "
                    . "(EBEN-PER113) et que le solde RMB du client n'est pas à 0."
                );
            }

            $montantsFormates = collect($totalRecupereParDevise)
                ->map(fn ($montant, $devise) => number_format($montant, 2, ',', ' ') . ' ' . $devise)
                ->implode(' + ');

            return redirect()->back()->with('success',
                "✅ Recouvrement terminé : {$dossiersAvecPrelevement}/{$nbDossiersCibles} dossier(s) avec prélèvement — {$montantsFormates} récupérés automatiquement."
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '❌ Erreur lors du recouvrement automatique : ' . $e->getMessage());
        }
    }
}