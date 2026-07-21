<?php

namespace App\Http\Controllers\Credit;

use App\Http\Controllers\Controller;
use App\Models\Credit\CreditDemande;
use App\Models\Credit\CreditAnalyse;
use App\Models\Credit\CreditValidation;
use App\Models\Credit\CreditPiece;
use App\Models\Credit\CreditDeblocage;
use App\Models\Credit\CreditEcheancier;
use App\Models\Credit\CreditEcheance;
use App\Models\Credit\CreditRemboursement;
use App\Models\Credit\CreditAudit;
use App\Models\Clients\Client;
use App\Models\Clients\Compte;
use App\Models\User;
use App\Models\RH\Agent;
use App\Models\RH\Affectation;
use App\Models\Zone;
use App\Models\Caisse\CaissesGuichet;
use App\Models\Caisse\CaissesGuichetSolde;
use App\Models\Caisse\Transaction;
use App\Models\Tresorerie\Portefeuille;
use App\Services\Credit\AmortissementService;
use App\Services\Notifications\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreditController extends Controller
{
    public function __construct(private AmortissementService $amortissement) {}

    // ================================================================
    // TABLEAU DE BORD
    // ================================================================

    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $perms = $user->getPermissionCodes();
        $matricule = $user?->agent?->matricule;

        // Accès dashboard réservé aux profils de supervision
        $dashboardPerms = ['EBEN-PER61', 'EBEN-PER62', 'EBEN-PER63', 'EBEN-PER64'];
        if (count(array_intersect($dashboardPerms, $perms)) === 0) {
            return redirect()
                ->route('credit.index')
                ->with('error', "Accès non autorisé au tableau de bord crédit.");
        }

        $superviseurPerms = ['EBEN-PER61', 'EBEN-PER62', 'EBEN-PER63', 'EBEN-PER64'];
        $estSuperviseur   = count(array_intersect($superviseurPerms, $perms)) > 0;
        $estAgentCredit   = in_array('EBEN-PER58', $perms, true) && !$estSuperviseur;

        // Scope zone
        $zonesCodes = $this->resolveZoneScope($user);

        $query = CreditDemande::when($zonesCodes !== null, fn($q) => $q->whereIn('code_zone', $zonesCodes));

        if ($estAgentCredit) {
            if ($matricule) {
                $query->where('agent_analyse_matricule', $matricule);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $stats = [
            'total'            => (clone $query)->count(),
            'brouillons'       => (clone $query)->where('statut_global', 'BROUILLON')->count(),
            'en_analyse'       => (clone $query)->where('statut_global', 'EN_ANALYSE')->count(),
            'en_validation'    => (clone $query)->where('statut_global', 'EN_VALIDATION')->count(),
            'pret_a_debloquer' => (clone $query)->where('statut_global', 'PRET_A_DEBLOQUER')->count(),
            'debloque'         => (clone $query)->where('statut_global', 'DEBLOQUE')->count(),
            'en_remboursement' => (clone $query)->where('statut_global', 'EN_REMBOURSEMENT')->count(),
            'en_retard'        => (clone $query)->where('statut_global', 'EN_RETARD')->count(),
            'solde'            => (clone $query)->where('statut_global', 'SOLDE')->count(),
            'annule'           => (clone $query)->where('statut_global', 'ANNULE')->count(),
            'suspendu'         => (clone $query)->where('statut_global', 'SUSPENDU')->count(),
            'suspect'          => (clone $query)->where('statut_global', 'SUSPECT')->count(),
        ];

        // Montants
        $stats['montant_total_debloque'] = (clone $query)
            ->whereIn('statut_global', ['DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD','SOLDE'])
            ->sum('montant_approuve');

        $stats['montant_total_a_recouvrer'] = CreditRemboursement::whereHas('demande', function ($q) use ($zonesCodes) {
            $q->when($zonesCodes !== null, fn($q2) => $q2->whereIn('code_zone', $zonesCodes));
        })->sum('montant_recu');

        // Derniers dossiers créés
        $derniersDossiers = (clone $query)
            ->with(['client'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('credit.dashboard', compact('stats', 'derniersDossiers'));
    }

    // ================================================================
    // LISTE DES DOSSIERS
    // ================================================================

    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user      = Auth::user();
        $perms     = $user ? $user->getPermissionCodes() : [];
        $matricule = $user?->agent?->matricule;

        // Permissions qui donnent accès à TOUS les dossiers (superviseurs)
        $superviseurPerms = ['EBEN-PER61', 'EBEN-PER62', 'EBEN-PER63', 'EBEN-PER64'];
        $estSuperviseur   = count(array_intersect($superviseurPerms, $perms)) > 0;

        // L'agent crédit (PER58) sans rôle superviseur ne voit que ses dossiers affectés
        $estAgentCredit = in_array('EBEN-PER58', $perms, true) && !$estSuperviseur;

        $query = CreditDemande::with(['client', 'zone']);

        if ($estSuperviseur) {
            // Scope global : tous les dossiers, pas de restriction
        } elseif ($estAgentCredit) {
            // L'agent voit ses dossiers affectés + les dossiers de ses portefeuilles actifs
            $portefeuilleIds = $this->resolvePortefeuilleScope($user);
            if ($matricule) {
                $query->where(function ($q) use ($matricule, $portefeuilleIds) {
                    $q->where('agent_analyse_matricule', $matricule);
                    if (!empty($portefeuilleIds)) {
                        $q->orWhereIn('portefeuille_id', $portefeuilleIds);
                    }
                });
            } elseif (!empty($portefeuilleIds)) {
                $query->whereIn('portefeuille_id', $portefeuilleIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            // Autres utilisateurs : dossiers qu'ils ont créés + leurs zones habituelles
            $zonesCodes = $this->resolveZoneScope($user);
            $query->where(function ($q) use ($zonesCodes, $matricule) {
                if ($zonesCodes !== null && !empty($zonesCodes)) {
                    $q->whereIn('code_zone', $zonesCodes);
                }
                if ($matricule) {
                    $q->orWhere('agent_createur_matricule', $matricule);
                }
                if (!$matricule && ($zonesCodes === null || empty($zonesCodes))) {
                    $q->whereRaw('1 = 0'); // aucun accès
                }
            });
        }
        $zonesCodes = $estSuperviseur ? null : ($estAgentCredit ? null : $this->resolveZoneScope($user));
        if ($request->get('vue') === 'analyse' && !$request->filled('statut')) {
            $query->whereIn('statut_global', ['SOUMIS', 'EN_ANALYSE']);
        }
        if ($request->filled('statut')) {
            $query->where('statut_global', $request->statut);
        }
        if ($request->filled('numero')) {
            $query->where('numero_dossier', 'like', '%'.$request->numero.'%');
        }
        if ($request->filled('client_matricule')) {
            $query->where('client_matricule', 'like', '%'.$request->client_matricule.'%');
        }
        // Recherche progressive multi-champs
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_dossier', 'like', '%'.$search.'%')
                  ->orWhere('client_matricule', 'like', '%'.$search.'%')
                  ->orWhere('compte_id', 'like', '%'.$search.'%')
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('nom', 'like', '%'.$search.'%')
                         ->orWhere('postnom', 'like', '%'.$search.'%')
                         ->orWhere('prenom', 'like', '%'.$search.'%');
                  });
            });
        }
        if ($request->filled('zone')) {
            $query->where('code_zone', $request->zone);
        }
        if ($request->filled('type_credit')) {
            $query->where('type_credit', $request->type_credit);
        }
        if ($request->filled('devise')) {
            $query->where('devise', $request->devise);
        }
        if ($request->filled('agent_analyse')) {
            $query->where('agent_analyse_matricule', $request->agent_analyse);
        }
        if ($request->filled('portefeuille_id')) {
            $query->where('portefeuille_id', $request->portefeuille_id);
        }
        if ($request->filled('agent_createur')) {
            $query->where('agent_createur_matricule', $request->agent_createur);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        // Filtre rapide : en retard (échéances dépassées non payées)
        if ($request->get('alerte') === 'retard') {
            $query->whereIn('statut_global', ['EN_REMBOURSEMENT','DEBLOQUE','EN_RETARD'])
                ->whereHas('echeancier.echeances', fn ($q) =>
                    $q->whereIn('statut', ['EN_ATTENTE','PARTIELLEMENT_PAYE','EN_RETARD'])
                      ->where('date_echeance', '<', now()->toDateString())
                );
        }
        if ($request->get('alerte') === 'alertes') {
            $query->whereIn('statut_global', ['SUSPECT','SUSPENDU']);
        }

        $dossiers = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $zones = Zone::when($zonesCodes !== null, fn($q) => $q->whereIn('code_zone', $zonesCodes))
            ->orderBy('nom')->get();

        // Portefeuilles accessibles pour le filtre
        $portefeuilles = \App\Models\Tresorerie\Portefeuille::query()
            ->when(!$estSuperviseur, fn ($q) => $q->whereHas('affectationActive'))
            ->orderBy('nom_portefeuille')
            ->get(['id', 'nom_portefeuille']);

        // Agents d'analyse pour le filtre superviseur
        $agentsAnalyse = collect();
        if ($estSuperviseur) {
            $agentsAnalyse = Agent::whereIn('matricule', function ($q) {
                $q->select('agent_analyse_matricule')
                  ->from('tb_credit_demandes')
                  ->whereNotNull('agent_analyse_matricule')
                  ->distinct();
            })->orderBy('nom')->get(['matricule','nom','postnom','prenom']);
        }

        // Agents créateurs pour le filtre
        $agentsCreateur = Agent::orderBy('nom')->get(['matricule','nom','postnom']);

        // Compteurs rapides pour les onglets
        $queryBase = CreditDemande::query();
        if (!$estSuperviseur && $estAgentCredit && $matricule) {
            $queryBase->where('agent_analyse_matricule', $matricule);
        }
        $compteurs = [
            'en_cours'   => (clone $queryBase)->whereIn('statut_global', ['SOUMIS','EN_ANALYSE','EN_VALIDATION','PRET_A_DEBLOQUER'])->count(),
            'actifs'     => (clone $queryBase)->whereIn('statut_global', ['DEBLOQUE','EN_REMBOURSEMENT'])->count(),
            'en_retard'  => (clone $queryBase)->where('statut_global', 'EN_RETARD')->count(),
            'soldes'     => (clone $queryBase)->where('statut_global', 'SOLDE')->count(),
            'alertes'    => (clone $queryBase)->whereIn('statut_global', ['SUSPECT','SUSPENDU'])->count(),
            'annules'    => (clone $queryBase)->where('statut_global', 'ANNULE')->count(),
        ];

        // ─ Totaux selon le filtre actif (pour l'affichage au-dessus du tableau) ──
        // Calcul séparé par devise pour éviter d'additionner CDF et USD
        $filteredQuery = clone $query;
        $filtresActifs = $filteredQuery->get();

        $idsFiltres = $filtresActifs->pluck('id')->toArray();

        // Totaux par devise
        $totauxParDevise = [];
        foreach ($filtresActifs->groupBy('devise') as $devise => $dossiersDevise) {
            $totauxParDevise[$devise] = [
                'count'             => $dossiersDevise->count(),
                'montant_demande'   => $dossiersDevise->sum('montant_demande'),
                'montant_approuve'  => $dossiersDevise->sum('montant_approuve'),
                'montant_net_verse' => $dossiersDevise->whereNotNull('deblocage_id')->sum(function($d) {
                    return $d->deblocage?->montant_net_verse ?? 0;
                }),
                'en_retard'         => $dossiersDevise->where('statut_global', 'EN_RETARD')->count(),
                'montant_en_retard' => $dossiersDevise->where('statut_global', 'EN_RETARD')->sum('montant_demande'),
            ];
        }

        // Totaux globaux (remboursement via table séparée)
        $montantRembourse = !empty($idsFiltres)
            ? CreditRemboursement::whereIn('credit_demande_id', $idsFiltres)->sum('montant_recu')
            : 0;

        $totauxFiltres = [
            'count'             => count($idsFiltres),
            'montant_demande'   => $filtresActifs->sum('montant_demande'),
            'montant_approuve'  => $filtresActifs->sum('montant_approuve'),
            'montant_net_verse' => $filtresActifs->whereNotNull('deblocage_id')->sum(function($d) {
                return $d->deblocage?->montant_net_verse ?? 0;
            }),
            'montant_rembourse' => $montantRembourse,
            'en_retard'         => $filtresActifs->where('statut_global', 'EN_RETARD')->count(),
            'montant_en_retard' => $filtresActifs->where('statut_global', 'EN_RETARD')->sum('montant_demande'),
            'par_devise'        => $totauxParDevise,
        ];

        //  AJAX : retourner uniquement le tableau (recherche progressive) ──
        if ($request->ajax() || $request->wantsJson()) {
            return view('credit._table', compact('dossiers'))->render();
        }

        return view('credit.liste', compact(
            'dossiers', 'zones', 'portefeuilles', 'agentsAnalyse', 'agentsCreateur', 'compteurs',
            'estSuperviseur', 'estAgentCredit', 'totauxFiltres'
        ));
    }

    /**
     * Impression de la liste des dossiers crédit (PDF ou CSV)
     */
    public function printListe(Request $request)
    {
        ini_set('memory_limit', '768M');

        $user = $request->user();
        $matricule = $user->agent_matricule ?? null;
        $estSuperviseur = $user->hasPermission('EBEN-PER61') || $user->hasPermission('EBEN-PER62') || $user->hasPermission('EBEN-PER63');
        $estAgentCredit = $user->hasPermission('EBEN-PER58') || $user->hasPermission('EBEN-PER59');

        $query = CreditDemande::with(['client.zone', 'deblocage', 'remboursements']);

        // Scope par rôle
        if ($estSuperviseur) {
            // Superviseur : tous les dossiers
        } elseif ($estAgentCredit && $matricule) {
            $query->where('agent_analyse_matricule', $matricule);
        } else {
            $zonesCodes = $this->resolveZoneScope($user);
            $query->where(function ($q) use ($zonesCodes, $matricule) {
                if ($zonesCodes !== null && !empty($zonesCodes)) {
                    $q->whereIn('code_zone', $zonesCodes);
                }
                if ($matricule) {
                    $q->orWhere('agent_createur_matricule', $matricule);
                }
            });
        }

        // Filtres
        if ($request->filled('statut')) $query->where('statut_global', $request->statut);
        if ($request->filled('type_credit')) $query->where('type_credit', $request->type_credit);
        if ($request->filled('devise')) $query->where('devise', $request->devise);
        if ($request->filled('zone')) $query->where('code_zone', $request->zone);
        if ($request->filled('portefeuille_id')) $query->where('portefeuille_id', $request->portefeuille_id);
        if ($request->filled('agent_analyse')) $query->where('agent_analyse_matricule', $request->agent_analyse);
        if ($request->filled('agent_createur')) $query->where('agent_createur_matricule', $request->agent_createur);
        if ($request->filled('date_debut')) $query->whereDate('created_at', '>=', $request->date_debut);
        if ($request->filled('date_fin')) $query->whereDate('created_at', '<=', $request->date_fin);
        if ($request->get('alerte') === 'retard') {
            $query->whereIn('statut_global', ['EN_REMBOURSEMENT','DEBLOQUE','EN_RETARD'])
                ->whereHas('echeancier.echeances', fn ($q) =>
                    $q->whereIn('statut', ['EN_ATTENTE','PARTIELLEMENT_PAYE','EN_RETARD'])
                      ->where('date_echeance', '<', now()->toDateString()));
        }
        if ($request->get('alerte') === 'alertes') {
            $query->whereIn('statut_global', ['SUSPECT','SUSPENDU']);
        }

        $query->orderByDesc('created_at');
        $dossiersCount = (clone $query)->count();

        // Filtres actifs pour affichage
        $filtres = array_filter([
            'statut_global' => $request->statut,
            'type_credit' => $request->type_credit,
            'devise' => $request->devise,
            'code_zone' => $request->zone,
            'portefeuille_id' => $request->portefeuille_id,
            'agent_analyse_matricule' => $request->agent_analyse,
            'agent_createur_matricule' => $request->agent_createur,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
        ], fn($v) => $v !== null && $v !== '');

        // Objets pour les libellés
        $zone = $request->zone ? Zone::where('code_zone', $request->zone)->first() : null;
        $portefeuille = $request->portefeuille_id ? \App\Models\Tresorerie\Portefeuille::find($request->portefeuille_id) : null;
        $agentAnalyse = $request->agent_analyse ? Agent::where('matricule', $request->agent_analyse)->first() : null;
        $agentCreateur = $request->agent_createur ? Agent::where('matricule', $request->agent_createur)->first() : null;

        $exportFormat = strtolower((string) $request->input('export_format', 'pdf'));
        $outputMode = strtolower((string) $request->input('output', 'stream'));

        // ─ Export CSV ──
        if ($exportFormat === 'csv') {
            $filename = 'Liste_dossiers_credit_' . now()->format('Ymd_His') . '.csv';

            return response()->streamDownload(function () use ($query) {
                $handle = fopen('php://output', 'w');
                // BOM UTF-8 pour Excel
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
                // En-tête
                fputcsv($handle, [
                    'N° Dossier', 'Client', 'Devise', 'Montant demandé',
                    'Montant approuvé', 'Décaissé', 'Remboursé',
                    'Statut', 'Zone', 'Portefeuille', 'Créé le'
                ], ';');

                $query->chunk(1000, function ($dossiers) use ($handle) {
                    foreach ($dossiers as $dossier) {
                        $clientNom = '';
                        if ($dossier->client) {
                            $clientNom = trim(strtoupper($dossier->client->nom ?? '') . ' ' .
                                strtoupper($dossier->client->postnom ?? '') . ' ' .
                                ucfirst(strtolower($dossier->client->prenom ?? '')));
                        }
                        $montantRembourse = $dossier->remboursements?->sum('montant_recu') ?? 0;

                        fputcsv($handle, [
                            $dossier->numero_dossier,
                            $clientNom,
                            $dossier->devise,
                            $dossier->montant_demande,
                            $dossier->montant_approuve ?? 0,
                            $dossier->deblocage?->montant_net_verse ?? 0,
                            $montantRembourse,
                            $dossier->statut_global,
                            $dossier->client?->zone?->nom ?? '',
                            $dossier->portefeuille?->nom_portefeuille ?? '',
                            \Carbon\Carbon::parse($dossier->created_at)->format('d/m/Y'),
                        ], ';');
                    }
                });

                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        // ── Export PDF ──
        $dossiers = $query->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('impressions.credit.liste', compact(
            'dossiers', 'filtres', 'zone', 'portefeuille', 'agentAnalyse', 'agentCreateur'
        ))->setPaper('a4', 'landscape');

        if ($outputMode === 'download') {
            return $pdf->download('Liste_dossiers_credit.pdf');
        }

        return $pdf->stream('Liste_dossiers_credit.pdf');
    }

    // ================================================================
    // TOMBÉE D'ÉCHÉANCES (échéances à recouvrer selon critères)
    // ================================================================

    /**
     * Construit la requête des échéances filtrées (réutilisée pour la liste
     * paginée, les totaux, et l'export PDF/CSV).
     */
    private function buildEcheancesQuery(Request $request)
    {
        $user = $request->user();
        $matricule = $user->agent_matricule ?? null;
        $estSuperviseur = $user->hasPermission('EBEN-PER61') || $user->hasPermission('EBEN-PER62') || $user->hasPermission('EBEN-PER63');
        $estAgentCredit = $user->hasPermission('EBEN-PER58') || $user->hasPermission('EBEN-PER59');

        $query = CreditEcheance::query()
            ->with(['echeancier.demande.client', 'echeancier.demande.zone', 'echeancier.demande.portefeuille']);

        // Statut de l'échéance : par défaut, uniquement celles restant à recouvrer
        if ($request->filled('statut_echeance')) {
            $query->where('statut', $request->statut_echeance);
        } else {
            $query->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD', 'PARTIELLEMENT_PAYE']);
        }

        // Date d'échéance : date précise ou plage
        if ($request->filled('date_echeance')) {
            $query->whereDate('date_echeance', $request->date_echeance);
        } else {
            if ($request->filled('date_debut')) {
                $query->whereDate('date_echeance', '>=', $request->date_debut);
            }
            if ($request->filled('date_fin')) {
                $query->whereDate('date_echeance', '<=', $request->date_fin);
            }
        }

        // Filtres sur le dossier crédit lié + scope d'accès par rôle
        $query->whereHas('echeancier.demande', function ($q) use ($request, $estSuperviseur, $estAgentCredit, $matricule) {
            $q->whereNotIn('statut_global', ['ANNULE']);

            if ($request->filled('devise')) $q->where('devise', $request->devise);
            if ($request->filled('zone')) $q->where('code_zone', $request->zone);
            if ($request->filled('portefeuille_id')) $q->where('portefeuille_id', $request->portefeuille_id);

            if ($estSuperviseur) {
                // Superviseur : accès total, aucune restriction supplémentaire
            } elseif ($estAgentCredit && $matricule) {
                $q->where('agent_analyse_matricule', $matricule);
            } else {
                $zonesCodes = $this->resolveZoneScope($request->user());
                $q->where(function ($sub) use ($zonesCodes, $matricule) {
                    if ($zonesCodes !== null && !empty($zonesCodes)) {
                        $sub->whereIn('code_zone', $zonesCodes);
                    }
                    if ($matricule) {
                        $sub->orWhere('agent_createur_matricule', $matricule);
                    }
                });
            }
        });

        return $query->orderBy('date_echeance', 'asc');
    }

    /**
     * Affiche la liste des échéances à recouvrer selon critères
     * (devise, zone, portefeuille, date d'échéance) avec totaux.
     */
    public function echeances(Request $request)
    {
        $echeances = $this->buildEcheancesQuery($request)->paginate(20)->withQueryString();

        // Totaux calculés sur TOUS les résultats filtrés (pas seulement la page affichée)
        $allEcheances = $this->buildEcheancesQuery($request)->get();

        $resteDuFn = fn($e) => max(0, (float) $e->total_echeance - (float) $e->montant_paye);

        $totauxParDevise = [];
        foreach ($allEcheances->groupBy(fn($e) => $e->echeancier->demande->devise ?? 'N/A') as $devise => $group) {
            $totauxParDevise[$devise] = [
                'count'         => $group->count(),
                'montant_total' => $group->sum('total_echeance'),
                'montant_paye'  => $group->sum('montant_paye'),
                'reste_du'      => $group->sum($resteDuFn),
            ];
        }

        $totauxParZone = [];
        foreach ($allEcheances->groupBy(fn($e) => $e->echeancier->demande->zone->nom ?? ($e->echeancier->demande->code_zone ?? 'Sans zone')) as $zoneNom => $group) {
            $totauxParZone[$zoneNom] = [
                'count'    => $group->count(),
                'reste_du' => $group->sum($resteDuFn),
            ];
        }

        $totauxParPortefeuille = [];
        foreach ($allEcheances->groupBy(fn($e) => $e->echeancier->demande->portefeuille->nom_portefeuille ?? 'Sans portefeuille') as $pfNom => $group) {
            $totauxParPortefeuille[$pfNom] = [
                'count'    => $group->count(),
                'reste_du' => $group->sum($resteDuFn),
            ];
        }

        $resteATotalGeneral = $allEcheances->sum($resteDuFn);
        $totalEcheances = $allEcheances->count();

        // AJAX : recherche progressive → retourne uniquement le contenu des résultats
        if ($request->ajax() || $request->wantsJson()) {
            return view('credit._echeances_content', compact(
                'echeances', 'totauxParDevise', 'totauxParZone', 'totauxParPortefeuille',
                'resteATotalGeneral', 'totalEcheances'
            ))->render();
        }

        $zones = Zone::orderBy('nom')->get();
        $portefeuilles = Portefeuille::orderBy('nom_portefeuille')->get(['id', 'nom_portefeuille']);

        return view('credit.echeances', compact(
            'echeances', 'totauxParDevise', 'totauxParZone', 'totauxParPortefeuille',
            'resteATotalGeneral', 'totalEcheances', 'zones', 'portefeuilles'
        ));
    }

    /**
     * Impression de la tombée d'échéances (PDF ou CSV)
     */
    public function printEcheances(Request $request)
    {
        ini_set('memory_limit', '768M');

        $query = $this->buildEcheancesQuery($request);

        $exportFormat = strtolower((string) $request->input('export_format', 'pdf'));
        $outputMode = strtolower((string) $request->input('output', 'stream'));

        if ($exportFormat === 'csv') {
            $filename = 'Tombee_echeances_' . now()->format('Ymd_His') . '.csv';

            return response()->streamDownload(function () use ($query) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($handle, [
                    'Date échéance', 'N° Éch.', 'Dossier', 'Client', 'Devise',
                    'Zone', 'Portefeuille', 'Montant échéance', 'Montant payé',
                    'Reste dû', 'Statut'
                ], ';');

                $query->chunk(500, function ($echeances) use ($handle) {
                    foreach ($echeances as $ech) {
                        $demande = $ech->echeancier->demande ?? null;
                        $client = $demande->client ?? null;
                        $clientNom = $client ? trim(strtoupper($client->nom ?? '') . ' ' . strtoupper($client->postnom ?? '') . ' ' . ucfirst(strtolower($client->prenom ?? ''))) : '-';
                        $resteDu = max(0, (float) $ech->total_echeance - (float) $ech->montant_paye);

                        fputcsv($handle, [
                            \Carbon\Carbon::parse($ech->date_echeance)->format('d/m/Y'),
                            $ech->numero_echeance,
                            $demande->numero_dossier ?? '-',
                            $clientNom,
                            $demande->devise ?? '-',
                            $demande->zone->nom ?? '-',
                            $demande->portefeuille->nom_portefeuille ?? '-',
                            $ech->total_echeance,
                            $ech->montant_paye,
                            $resteDu,
                            $ech->statut,
                        ], ';');
                    }
                });

                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        $echeances = $query->get();
        $resteDuFn = fn($e) => max(0, (float) $e->total_echeance - (float) $e->montant_paye);

        $totauxParDevise = [];
        foreach ($echeances->groupBy(fn($e) => $e->echeancier->demande->devise ?? 'N/A') as $devise => $group) {
            $totauxParDevise[$devise] = [
                'count'    => $group->count(),
                'reste_du' => $group->sum($resteDuFn),
            ];
        }
        $resteATotalGeneral = $echeances->sum($resteDuFn);

        $filtres = array_filter([
            'date_echeance'    => $request->date_echeance,
            'date_debut'       => $request->date_debut,
            'date_fin'         => $request->date_fin,
            'devise'           => $request->devise,
            'zone'             => $request->zone,
            'portefeuille_id'  => $request->portefeuille_id,
            'statut_echeance'  => $request->statut_echeance,
        ], fn($v) => $v !== null && $v !== '');

        $zoneObj = $request->filled('zone') ? Zone::where('code_zone', $request->zone)->first() : null;
        $portefeuilleObj = $request->filled('portefeuille_id') ? Portefeuille::find($request->portefeuille_id) : null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('impressions.credit.echeances', compact(
            'echeances', 'totauxParDevise', 'resteATotalGeneral', 'filtres', 'zoneObj', 'portefeuilleObj'
        ))->setPaper('a4', 'landscape');

        if ($outputMode === 'download') {
            return $pdf->download('Tombee_echeances.pdf');
        }

        return $pdf->stream('Tombee_echeances.pdf');
    }

    // ================================================================
    // RAPPORT FRAIS DÉBLOCAGE
    // ================================================================

    public function rapportFrais(Request $request)
    {
        $query = CreditDeblocage::with(['demande.client', 'operateur'])
            ->orderByDesc('debloque_le');

        if ($request->filled('date_debut')) {
            $query->whereDate('debloque_le', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('debloque_le', '<=', $request->date_fin);
        }
        if ($request->filled('devise')) {
            $query->where('devise', $request->devise);
        }

        $deblocages = $query->paginate(30)->withQueryString();

        $totaux = CreditDeblocage::query()
            ->when($request->filled('date_debut'), fn($q) => $q->whereDate('debloque_le', '>=', $request->date_debut))
            ->when($request->filled('date_fin'), fn($q) => $q->whereDate('debloque_le', '<=', $request->date_fin))
            ->when($request->filled('devise'), fn($q) => $q->where('devise', $request->devise))
            ->selectRaw('devise,
                COUNT(*) as nb,
                SUM(montant_debloque) as total_brut,
                SUM(montant_caution) as total_caution,
                SUM(frais_dossier) as total_frais,
                SUM(montant_net_verse) as total_net')
            ->groupBy('devise')
            ->get();

        return view('credit.rapport_frais', compact('deblocages', 'totaux'));
    }

    // ================================================================
    // CRÉATION D'UN DOSSIER
    // ================================================================

    public function create(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Règle métier: toute personne habilitée à créer une demande peut sélectionner n'importe quel client.
        $clients = Client::orderBy('nom')->orderBy('postnom')->orderBy('prenom')->get();
        $zones = Zone::orderBy('nom')->get();
        $selectedClientMatricule = $request->query('client_matricule');
        $portefeuillesDisponibles = $user
            ? $this->resolveCreationPortefeuilleOptions($user)
            : collect();

        return view('credit.creation', compact('clients', 'zones', 'selectedClientMatricule', 'portefeuillesDisponibles'));
    }

    /**
     * GET AJAX : comptes d'un client
     */
    public function getComptesClient(Request $request)
    {
        $request->validate(['client_matricule' => 'required|string']);
        $comptes = Compte::where('client_matricule', $request->client_matricule)
            ->whereIn('type', ['CC','RMB'])
            ->get(['code_compte','type','devise','solde_reel']);
        return response()->json($comptes);
    }

    /**
     * GET AJAX : simulation amortissement
     */
    public function simuler(Request $request)
    {
        $request->validate([
            'montant'    => 'required|numeric|min:1',
            'taux'       => 'required|numeric|min:0.01|max:100',
            'duree'      => 'required|integer|min:1|max:360',
            'commission' => 'nullable|numeric|min:0',
        ]);

        $commission = (float) ($request->commission ?? 0);

        $calcul = $this->amortissement->simuler(
            (float) $request->montant,
            (float) $request->taux,
            (int) $request->duree,
            $commission
        );

        return response()->json($calcul);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_matricule'     => 'required|string|exists:tb_clients,matricule',
            'portefeuille_id'      => 'required|integer|exists:tb_portefeuilles_agents,id',
            'montant_demande'      => 'required|numeric|min:1',
            'devise'               => 'required|in:CDF,USD,EUR',
            'duree_mois'           => 'required|integer|min:1|max:360',
            'taux_interet_mensuel' => 'required|numeric|min:0.01|max:100',
            'type_credit'          => 'required|in:INDIVIDUEL,SOLIDAIRE,PME',
            'objet_credit'         => 'required|string|max:500',
            'garantie_description' => 'nullable|string',
            'service_provenance'   => 'nullable|string|max:100',
            'referent_nom'         => 'nullable|string|max:120',
            'commission_totale'    => 'nullable|numeric|min:0',
        ]);

        /** @var \App\Models\User|null $user */
        $user  = Auth::user();
        if (!$user) {
            abort(401, 'Utilisateur non authentifié.');
        }

        $agent = $user->agent;

        $portefeuillesAutorises = $this->resolveCreationPortefeuilleOptions($user)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->toArray();

        if (empty($portefeuillesAutorises)) {
            return back()->withInput()->with('error', 'Aucun portefeuille actif n\'est disponible pour créer ce dossier.');
        }

        $portefeuilleIdCreation = (int) $validated['portefeuille_id'];
        if (!in_array($portefeuilleIdCreation, $portefeuillesAutorises, true)) {
            return back()->withInput()->with('error', 'Le portefeuille sélectionné n\'est pas autorisé pour votre profil.');
        }

        $client = Client::findOrFail($validated['client_matricule']);

        // Calculer la commission si non fournie manuellement
        $commissionTotale = $validated['commission_totale'] ?? 0;
        if ($commissionTotale == 0) {
            $commissionService = app(\App\Services\Credit\CreditCommissionService::class);
            $commissionTotale = $commissionService->calculateForContext([
                'devise' => $validated['devise'],
                'type_credit' => $validated['type_credit'],
                'code_zone' => $client->code_zone,
                'portefeuille_id' => $portefeuilleIdCreation,
                'montant' => (float) $validated['montant_demande'],
            ]);
        }

        $calcul = $this->amortissement->simuler(
            (float) $validated['montant_demande'],
            (float) $validated['taux_interet_mensuel'],
            (int) $validated['duree_mois'],
            (float) $commissionTotale
        );

        $demande = DB::transaction(function () use ($validated, $client, $agent, $calcul, $portefeuilleIdCreation, $commissionTotale) {
            $demande = CreditDemande::create([
                'client_matricule'        => $validated['client_matricule'],
                'compte_id'               => null,
                'portefeuille_id'         => $portefeuilleIdCreation,
                'code_zone'               => $client->code_zone,
                'agent_createur_matricule'=> $agent?->matricule ?? 'SYSTEM',
                'montant_demande'         => $validated['montant_demande'],
                'devise'                  => $validated['devise'],
                'duree_mois'              => $validated['duree_mois'],
                'taux_interet_mensuel'    => $validated['taux_interet_mensuel'],
                'type_credit'             => $validated['type_credit'],
                'objet_credit'            => $validated['objet_credit'],
                'garantie_description'    => $validated['garantie_description'],
                'service_provenance'      => $validated['service_provenance'] ?? null,
                'referent_nom'            => $validated['referent_nom'] ?? null,
                'montant_total_echeances' => $calcul['total_general'],
                'total_interets'          => $calcul['total_interets'],
                'commission_totale'       => $commissionTotale,
                'statut_global'           => 'BROUILLON',
            ]);

            // Pièces standard par défaut
            $piecesStandard = [
                ['libelle' => "Copie de la carte d'identité nationale", 'type_piece' => 'IDENTITE'],
                ['libelle' => 'Justificatif de domicile', 'type_piece' => 'DOMICILE'],
                ['libelle' => 'Justificatif de revenus (bulletin, attestation)', 'type_piece' => 'REVENU'],
                ['libelle' => 'Formulaire de demande de crédit signé', 'type_piece' => 'AUTRE'],
            ];
            foreach ($piecesStandard as $p) {
                CreditPiece::create(array_merge($p, ['credit_demande_id' => $demande->id]));
            }

            // 4 blocs de validation initialisés à EN_ATTENTE
            // Workflow demandé: Agent crédit -> Contrôleur -> Chargé opérations -> Gérant
            $blocs = [
                ['type_validateur' => 'AGENT_CREDIT',      'ordre_etape' => 1],
                ['type_validateur' => 'CONTROLEUR',        'ordre_etape' => 2],
                ['type_validateur' => 'CHARGE_OPERATIONS', 'ordre_etape' => 3],
                ['type_validateur' => 'GERANT',            'ordre_etape' => 4],
            ];
            foreach ($blocs as $b) {
                CreditValidation::create(array_merge($b, [
                    'credit_demande_id'    => $demande->id,
                    'validateur_matricule' => '',
                    'decision'             => 'EN_ATTENTE',
                    'etape_precedente_ok'  => false,
                ]));
            }

            $this->logAudit($demande, 'CREATION', null, 'BROUILLON');

            return $demande;
        });

        return redirect()->route('credit.show', $demande)
            ->with('success', "Dossier {$demande->numero_dossier} créé avec succès.");
    }

    // ================================================================
    // ÉDITION D'UN BROUILLON (PER55)
    // ================================================================

    public function edit(CreditDemande $dossier)
    {
        $this->authorizeDemandeAccess($dossier, true);

        if ($dossier->statut_global !== 'BROUILLON') {
            return redirect()->route('credit.show', $dossier)
                ->with('error', 'Seul un dossier en statut BROUILLON peut être modifié.');
        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $clients = Client::orderBy('nom')->orderBy('postnom')->orderBy('prenom')->get();
        $zones   = Zone::orderBy('nom')->get();
        $portefeuillesDisponibles = $user
            ? $this->resolveCreationPortefeuilleOptions($user)
            : collect();

        return view('credit.edit', compact('dossier', 'clients', 'zones', 'portefeuillesDisponibles'));
    }

    public function update(Request $request, CreditDemande $dossier)
    {
        $this->authorizeDemandeAccess($dossier, true);

        if ($dossier->statut_global !== 'BROUILLON') {
            return redirect()->route('credit.show', $dossier)
                ->with('error', 'Seul un dossier en statut BROUILLON peut être modifié.');
        }

        $validated = $request->validate([
            'client_matricule'     => 'required|string|exists:tb_clients,matricule',
            'portefeuille_id'      => 'required|integer|exists:tb_portefeuilles_agents,id',
            'montant_demande'      => 'required|numeric|min:1',
            'devise'               => 'required|in:CDF,USD,EUR',
            'duree_mois'           => 'required|integer|min:1|max:360',
            'taux_interet_mensuel' => 'required|numeric|min:0.01|max:100',
            'type_credit'          => 'required|in:INDIVIDUEL,SOLIDAIRE,PME',
            'objet_credit'         => 'required|string|max:500',
            'garantie_description' => 'nullable|string',
            'service_provenance'   => 'nullable|string|max:100',
            'referent_nom'         => 'nullable|string|max:120',
            'commission_totale'    => 'nullable|numeric|min:0',
        ]);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $portefeuillesAutorises = $this->resolveCreationPortefeuilleOptions($user)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->toArray();

        $portefeuilleId = (int) $validated['portefeuille_id'];
        if (!empty($portefeuillesAutorises) && !in_array($portefeuilleId, $portefeuillesAutorises, true)) {
            return back()->withInput()->with('error', 'Le portefeuille sélectionné n\'est pas autorisé.');
        }

        $client = Client::findOrFail($validated['client_matricule']);

        // Calculer la commission si non fournie manuellement
        $commissionTotale = $validated['commission_totale'] ?? 0;
        if ($commissionTotale == 0) {
            $commissionService = app(\App\Services\Credit\CreditCommissionService::class);
            $commissionTotale = $commissionService->calculateForContext([
                'devise' => $validated['devise'],
                'type_credit' => $validated['type_credit'],
                'code_zone' => $client->code_zone,
                'portefeuille_id' => $portefeuilleId,
                'montant' => (float) $validated['montant_demande'],
            ]);
        }

        $calcul = $this->amortissement->simuler(
            (float) $validated['montant_demande'],
            (float) $validated['taux_interet_mensuel'],
            (int) $validated['duree_mois'],
            (float) $commissionTotale
        );

        $ancien = $dossier->replicate();

        $dossier->update([
            'client_matricule'        => $validated['client_matricule'],
            'portefeuille_id'         => $portefeuilleId,
            'code_zone'               => $client->code_zone,
            'montant_demande'         => $validated['montant_demande'],
            'devise'                  => $validated['devise'],
            'duree_mois'              => $validated['duree_mois'],
            'taux_interet_mensuel'    => $validated['taux_interet_mensuel'],
            'type_credit'             => $validated['type_credit'],
            'objet_credit'            => $validated['objet_credit'],
            'garantie_description'    => $validated['garantie_description'],
            'service_provenance'      => $validated['service_provenance'] ?? null,
            'referent_nom'            => $validated['referent_nom'] ?? null,
            'montant_total_echeances' => $calcul['total_general'],
            'total_interets'          => $calcul['total_interets'],
            'commission_totale'       => $commissionTotale,
        ]);

        $this->logAudit(
            $dossier,
            'MODIFICATION',
            'BROUILLON',
            'BROUILLON',
            "Montant: {$ancien->montant_demande}→{$validated['montant_demande']} | Durée: {$ancien->duree_mois}→{$validated['duree_mois']} mois"
        );

        return redirect()->route('credit.show', $dossier)
            ->with('success', "Dossier {$dossier->numero_dossier} mis à jour avec succès.");
    }

    // ================================================================
    // DÉTAIL D'UN DOSSIER
    // ================================================================

    public function show(CreditDemande $dossier)
    {
        $this->authorizeDemandeAccess($dossier, true);

        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        $canViewAudit = $authUser?->hasPermission('EBEN-PER72') ?? false;

        $dossier->load([
            'client', 'compte', 'zone', 'portefeuille',
            'analyse', 'validations', 'pieces', 'agentAnalyse',
            'deblocage', 'echeancier.echeances',
            'remboursements',
        ]);

        if ($canViewAudit) {
            $dossier->load('audits');
        }

        // Rafraîchissement dynamique : marque en RETARD les échéances dépassées
        // afin que l'affichage soit toujours cohérent même si le cron n'est pas lancé.
        if (in_array($dossier->statut_global, ['DEBLOQUE', 'EN_REMBOURSEMENT', 'EN_RETARD'])) {
            $today = Carbon::today()->toDateString();
            $echancier = $dossier->echeancier;
            if ($echancier) {
                $misesAJour = false;
                foreach ($echancier->echeances as $e) {
                    if (in_array($e->statut, ['EN_ATTENTE', 'PARTIELLEMENT_PAYE']) && $e->date_echeance < $today) {
                        $e->update(['statut' => 'EN_RETARD']);
                        $misesAJour = true;
                    }
                }
                if ($misesAJour) {
                    $aRetard = $echancier->echeances()
                        ->whereIn('statut', ['EN_RETARD', 'PARTIELLEMENT_PAYE'])
                        ->where('date_echeance', '<', $today)
                        ->exists();
                    $toutesSoldees = $echancier->echeances()
                        ->whereNotIn('statut', ['PAYE'])
                        ->count() === 0;
                    if ($toutesSoldees) {
                        $dossier->update(['statut_global' => 'SOLDE']);
                    } elseif (!$aRetard && $dossier->statut_global === 'EN_RETARD') {
                        $dossier->update(['statut_global' => 'EN_REMBOURSEMENT']);
                    } elseif ($aRetard && $dossier->statut_global !== 'EN_RETARD') {
                        $dossier->update(['statut_global' => 'EN_RETARD']);
                    }
                    // Recharger les relations fraîches pour la vue
                    $dossier->refresh();
                    $dossier->load(['echeancier.echeances', 'client']);
                }
            }
        }

        $assignableAgents = collect();
        if ($authUser?->hasPermission('EBEN-PER61') && $dossier->statut_global === 'SOUMIS') {
            $assignableAgents = $this->resolveAssignableCreditAgents();
        }

        $demandeurMeta = $this->resolveDemandeurMeta($dossier->agent_createur_matricule);

        $soldeRmb = 0;
        $compteRmb = Compte::where('client_matricule', $dossier->client_matricule)
            ->where('type', 'RMB')
            ->where('devise', $dossier->devise)
            ->first();
        if ($compteRmb) {
            $soldeRmb = (float) $compteRmb->solde_reel;
        }

        $demande = $dossier;
        return view('credit.show', compact('dossier', 'demande', 'canViewAudit', 'assignableAgents', 'demandeurMeta', 'soldeRmb'));
    }

    public function affecterAnalyse(Request $request, CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        if (!$authUser || !in_array('EBEN-PER61', $authUser->getPermissionCodes(), true)) {
            abort(403, 'Vous n\'avez pas la permission d\'affecter un agent de crédit.');
        }

        if ($dossier->statut_global !== 'SOUMIS') {
            return back()->with('error', 'L\'affectation est autorisée uniquement pour un dossier soumis.');
        }

        $validated = $request->validate([
            'agent_analyse_matricule' => 'required|string|exists:tb_agents,matricule',
            'portefeuille_id'         => 'nullable|integer',
        ]);

        if (!$this->isEligibleCreditAnalyst($validated['agent_analyse_matricule'])) {
            return back()->with('error', 'L\'agent sélectionné n\'a pas le profil analyse crédit (PER58).');
        }

        $portefeuilleIds = $this->resolveAgentPortefeuilleIds($validated['agent_analyse_matricule']);
        if (empty($portefeuilleIds)) {
            return back()->with('error', 'L\'agent sélectionné ne dispose d\'aucun portefeuille actif.');
        }

        $portefeuilleId = null;
        if (!empty($validated['portefeuille_id'])) {
            $portefeuilleId = (int) $validated['portefeuille_id'];
            if (!in_array($portefeuilleId, $portefeuilleIds, true)) {
                return back()->with('error', 'Le portefeuille sélectionné n\'est pas actif pour cet agent.');
            }
        } elseif (count($portefeuilleIds) === 1) {
            $portefeuilleId = (int) $portefeuilleIds[0];
        } else {
            return back()->with('error', 'Cet agent a plusieurs portefeuilles actifs. Veuillez sélectionner le portefeuille du dossier.');
        }

        $ancienAgent = $dossier->agent_analyse_matricule;
        $ancienPortefeuille = $dossier->portefeuille_id;

        $dossier->update([
            'agent_analyse_matricule' => $validated['agent_analyse_matricule'],
            'portefeuille_id'         => $portefeuilleId,
        ]);

        $details = $ancienAgent
            ? "Réaffecté de {$ancienAgent} vers {$validated['agent_analyse_matricule']} | Portefeuille {$ancienPortefeuille} -> {$portefeuilleId}"
            : "Affecté à {$validated['agent_analyse_matricule']} | Portefeuille {$portefeuilleId}";

        $this->logAudit($dossier, 'AFFECTATION_ANALYSE', $dossier->statut_global, $dossier->statut_global, $details);

        $notificationService = app(NotificationService::class);
        $notificationService->notifyAgentMatricules(
            [$validated['agent_analyse_matricule']],
            'Nouveau dossier crédit affecté',
            sprintf(
                'Le dossier %s vous a été %s pour analyse.',
                $dossier->numero_dossier,
                $ancienAgent ? 'réaffecté' : 'affecté'
            ),
            [
                'type' => 'action_required',
                'category' => 'credit',
                'icon' => 'fas fa-user-check',
                'action_url' => route('credit.show', $dossier),
            ]
        );

        $notificationService->notifyAgentMatricules(
            [$dossier->agent_createur_matricule],
            'Dossier pris en charge',
            sprintf(
                'Le dossier %s a été %s à un agent de crédit pour analyse.',
                $dossier->numero_dossier,
                $ancienAgent ? 'réaffecté' : 'affecté'
            ),
            [
                'type' => 'info',
                'category' => 'credit',
                'icon' => 'fas fa-user-tie',
                'action_url' => route('credit.show', $dossier),
            ]
        );

        return back()->with('success', 'Agent de crédit affecté avec succès.');
    }

    // ================================================================
    // SOUMISSION
    // ================================================================

    public function soumettre(CreditDemande $dossier)
    {
        $this->authorizeDemandeAccess($dossier, true);

        if ($dossier->statut_global !== 'BROUILLON') {
            return back()->with('error', 'Ce dossier ne peut plus être soumis.');
        }

        DB::transaction(function () use ($dossier) {
            $ancien = $dossier->statut_global;
            $dossier->update([
                'statut_global' => 'SOUMIS',
                'soumis_le'     => now(),
            ]);
            $this->logAudit($dossier, 'SOUMISSION', $ancien, 'SOUMIS');
        });

        $notificationService = app(NotificationService::class);
        $destinataires = $notificationService->usersWithPermission('EBEN-PER61')
            ->merge($notificationService->usersWithRole('EBEN-ROL11'))
            ->unique('id')
            ->values();

        $notificationService->notifyUsers(
            $destinataires,
            'Nouveau dossier soumis',
            sprintf(
                'Le dossier %s vient d\'être soumis et attend l\'affectation d\'un agent de crédit.',
                $dossier->numero_dossier
            ),
            [
                'type' => 'action_required',
                'category' => 'credit',
                'icon' => 'fas fa-file-upload',
                'action_url' => route('credit.show', $dossier),
            ]
        );

        return redirect()->route('credit.show', $dossier)
            ->with('success', "Dossier {$dossier->numero_dossier} soumis pour analyse.");
    }

    // ================================================================
    // ANALYSE
    // ================================================================

    public function analyse(CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        if (empty($dossier->agent_analyse_matricule)) {
            return back()->with('error', 'Le chargé des opérations doit d\'abord affecter un agent de crédit.');
        }

        $currentAgentMatricule = $authUser?->agent?->matricule;
        if ($currentAgentMatricule !== $dossier->agent_analyse_matricule && !$authUser?->hasPermission('EBEN-PER1')) {
            abort(403, 'Ce dossier est affecté à un autre agent de crédit.');
        }

        if (!in_array($dossier->statut_global, ['SOUMIS','EN_ANALYSE'])) {
            return back()->with('error', 'Ce dossier ne peut pas être analysé dans son état actuel.');
        }

        $dossier->load(['client','analyse','validations']);
        $conditionsRetenues = $dossier->conditions_retenues;
        $previewEcheancier = $authUser?->hasPermission('EBEN-PER71')
            ? $this->amortissement->simuler(
                (float) $conditionsRetenues['montant'],
                (float) $dossier->taux_interet_mensuel,
                (int) $conditionsRetenues['duree_mois']
            )
            : null;
        $demande = $dossier;
        return view('credit.analyse', compact('dossier', 'demande', 'conditionsRetenues', 'previewEcheancier'));
    }

    public function storeAnalyse(Request $request, CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        if (empty($dossier->agent_analyse_matricule)) {
            return back()->with('error', 'Le chargé des opérations doit d\'abord affecter un agent de crédit.');
        }

        $validated = $request->validate([
            'revenu_mensuel_verifie' => 'nullable|numeric|min:0',
            'capacite_remboursement' => 'nullable|numeric|min:0',
            'ratio_endettement'      => 'nullable|numeric|min:0|max:100',
            'score_risque'           => 'nullable|in:FAIBLE,MOYEN,ELEVE,TRES_ELEVE',
            'historique_credit'      => 'nullable|string',
            'garanties_evaluees'     => 'nullable|string',
            'observations'           => 'nullable|string',
            'recommandation'         => 'required|in:FAVORABLE,FAVORABLE_AVEC_RESERVE,DEFAVORABLE',
            'montant_recommande'     => 'nullable|numeric|min:0',
            'action'                 => 'required|in:SAUVER,COMPLETER',
        ]);

        /** @var \App\Models\User|null $user */
        $user  = Auth::user();
        if (!$user) {
            abort(401, 'Utilisateur non authentifié.');
        }
        $agent = $user->agent;

        if (($agent?->matricule ?? null) !== $dossier->agent_analyse_matricule && !$user->hasPermission('EBEN-PER1')) {
            abort(403, 'Ce dossier est affecté à un autre agent de crédit.');
        }

        // Séparation des tâches: compléter l'analyse requiert explicitement PER59.
        if (($validated['action'] ?? null) === 'COMPLETER' && !$user->hasPermission('EBEN-PER59')) {
            abort(403, 'Vous n\'êtes pas autorisé à compléter l\'analyse.');
        }

        $analyseDemarree = false;
        $analyseComplete = false;

        DB::transaction(function () use ($validated, $dossier, $agent, &$analyseDemarree, &$analyseComplete) {
            $statut    = $validated['action'] === 'COMPLETER' ? 'COMPLETE' : 'EN_COURS';
            $ancienStatut = $dossier->statut_global;

            // Créer ou mettre à jour l'analyse
            $dossier->analyse()->updateOrCreate(
                ['credit_demande_id' => $dossier->id],
                [
                    'analyseur_matricule'    => $agent?->matricule ?? 'SYSTEM',
                    'revenu_mensuel_verifie' => $validated['revenu_mensuel_verifie'],
                    'capacite_remboursement' => $validated['capacite_remboursement'],
                    'ratio_endettement'      => $validated['ratio_endettement'],
                    'score_risque'           => $validated['score_risque'],
                    'historique_credit'      => $validated['historique_credit'],
                    'garanties_evaluees'     => $validated['garanties_evaluees'],
                    'observations'           => $validated['observations'],
                    'recommandation'         => $validated['recommandation'],
                    'montant_recommande'     => $validated['montant_recommande'],
                    'statut'                 => $statut,
                    'complete_le'            => $statut === 'COMPLETE' ? now() : null,
                ]
            );

            if ($dossier->statut_global === 'SOUMIS') {
                $dossier->update(['statut_global' => 'EN_ANALYSE']);
                $this->logAudit($dossier, 'ANALYSE_DEMARREE', $ancienStatut, 'EN_ANALYSE');
                $analyseDemarree = true;
            }

            if ($statut === 'COMPLETE') {
                $dossier->update(['statut_global' => 'EN_VALIDATION']);
                // Activer le bloc n°1 (Agent crédit)
                $dossier->validations()->where('ordre_etape', 1)
                    ->update(['etape_precedente_ok' => true]);
                $this->logAudit($dossier, 'ANALYSE_COMPLETE', 'EN_ANALYSE', 'EN_VALIDATION');
                $analyseComplete = true;
            }
        });

        $notificationService = app(NotificationService::class);

        if ($analyseDemarree) {
            $notificationService->notifyAgentMatricules(
                [$dossier->agent_createur_matricule],
                'Analyse crédit démarrée',
                sprintf('Le dossier %s est maintenant en cours d\'analyse.', $dossier->numero_dossier),
                [
                    'type' => 'info',
                    'category' => 'credit',
                    'icon' => 'fas fa-search-dollar',
                    'action_url' => route('credit.show', $dossier),
                ]
            );
        }

        if ($analyseComplete) {
            $notificationService->notifyUsersWithPermission(
                'EBEN-PER60',
                'Dossier prêt pour validation',
                sprintf('Le dossier %s a terminé l\'analyse et attend la validation du bloc Agent crédit.', $dossier->numero_dossier),
                [
                    'type' => 'action_required',
                    'category' => 'credit',
                    'icon' => 'fas fa-file-signature',
                    'action_url' => route('credit.show', $dossier),
                ]
            );

            $notificationService->notifyAgentMatricules(
                [$dossier->agent_createur_matricule, $dossier->agent_analyse_matricule],
                'Analyse crédit complétée',
                sprintf('Le dossier %s a terminé la phase d\'analyse et passe en validation.', $dossier->numero_dossier),
                [
                    'type' => 'info',
                    'category' => 'credit',
                    'icon' => 'fas fa-check-circle',
                    'action_url' => route('credit.show', $dossier),
                ]
            );
        }

        return redirect()->route('credit.show', $dossier)
            ->with('success', 'Analyse enregistrée avec succès.');
    }

    // ================================================================
    // VALIDATION (4 blocs)
    // ================================================================

    public function validation(CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        if ($dossier->statut_global !== 'EN_VALIDATION') {
            return back()->with('error', 'Ce dossier n\'est pas en phase de validation.');
        }

        $dossier->load(['client','analyse','validations']);
        $conditionsRetenues = $dossier->conditions_retenues;
        $previewEcheancier = $authUser?->hasPermission('EBEN-PER71')
            ? $this->amortissement->simuler(
                (float) $conditionsRetenues['montant'],
                (float) $dossier->taux_interet_mensuel,
                (int) $conditionsRetenues['duree_mois']
            )
            : null;
        $demande = $dossier;
        return view('credit.validation', compact('dossier', 'demande', 'conditionsRetenues', 'previewEcheancier'));
    }

    public function storeValidation(Request $request, CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        $validated = $request->validate([
            'type_validateur' => 'required|in:AGENT_CREDIT,CHARGE_OPERATIONS,CONTROLEUR,GERANT',
            'decision'        => 'required|in:APPROUVE,APPROUVE_AVEC_RESERVE,REJETE',
            'montant_valide'  => 'required_if:decision,APPROUVE,APPROUVE_AVEC_RESERVE|nullable|numeric|min:0.01',
            'duree_mois_validee' => 'nullable|integer|min:1|max:360',
            'observations'    => 'required|string|min:8',
            'conditions'      => 'nullable|string',
            'signature_confirm' => 'required|accepted',
        ], [
            'montant_valide.required_if' => 'Le montant validé est obligatoire pour une décision approuvée.',
            'duree_mois_validee.integer' => 'La durée validée doit être un nombre entier de mois.',
            'observations.required'      => 'Le commentaire du validateur est obligatoire.',
            'observations.min'           => 'Le commentaire doit contenir au moins 8 caractères.',
            'signature_confirm.required' => 'Vous devez confirmer la signature avec votre compte agent.',
            'signature_confirm.accepted' => 'La confirmation de signature est invalide.',
        ]);

        /** @var \App\Models\User|null $user */
        $user  = Auth::user();
        if (!$user) {
            abort(401, 'Utilisateur non authentifié.');
        }
        $agent = $user->agent;

        // Vérification croisée : la permission doit correspondre au type_validateur soumis
        $typeToPermission = [
            'AGENT_CREDIT'      => 'EBEN-PER60',
            'CHARGE_OPERATIONS' => 'EBEN-PER61',
            'CONTROLEUR'        => 'EBEN-PER62',
            'GERANT'            => 'EBEN-PER63',
        ];
        $requiredPerm = $typeToPermission[$validated['type_validateur']] ?? null;
        if ($requiredPerm && !in_array($requiredPerm, $user->getPermissionCodes())) {
            abort(403, "Vous n'êtes pas autorisé à valider en tant que {$validated['type_validateur']}.");
        }

        if (!empty($validated['duree_mois_validee'])
            && $validated['type_validateur'] !== 'GERANT'
            && !$user->hasPermission('EBEN-PER1')) {
            abort(403, 'Seul le gérant peut modifier le nombre de mois lors de la validation.');
        }

        $notificationContext = DB::transaction(function () use ($validated, $dossier, $agent, $user) {
            $validation = $dossier->validations()
                ->where('type_validateur', $validated['type_validateur'])
                ->firstOrFail();

            if (!$validation->etape_precedente_ok) {
                throw new \Exception("L'étape précédente n'est pas encore validée.");
            }
            if ($validation->decision !== 'EN_ATTENTE') {
                throw new \Exception("Ce bloc a déjà été traité.");
            }

            $ancien = $dossier->statut_global;

            $signatureCompte = $agent?->matricule ?: ('USR-' . $user->id);
            $signatureNom = trim(
                ($agent?->nom ?? '') . ' ' .
                ($agent?->postnom ?? '') . ' ' .
                ($agent?->prenom ?? '')
            ) ?: $user->name ?? null;
            $conditionsAvantDecision = $dossier->conditions_retenues;
            $montantValide = $validated['decision'] === 'REJETE'
                ? null
                : round((float) $validated['montant_valide'], 2);
            $dureeValidee = $validated['decision'] === 'REJETE'
                ? null
                : (!empty($validated['duree_mois_validee']) ? (int) $validated['duree_mois_validee'] : null);

            $validation->update([
                'validateur_matricule' => $agent?->matricule ?? 'SYSTEM',
                'decision'             => $validated['decision'],
                'montant_valide'       => $montantValide,
                'duree_mois_validee'   => $dureeValidee,
                'observations'         => $validated['observations'],
                'conditions'           => $validated['conditions'],
                'valide_le'            => now(),
                'signature_agent'      => $signatureCompte,
                'nom_signataire'       => $signatureNom ?? null,
                'ip_validation'        => request()->ip(),
            ]);

            if ($validated['decision'] === 'REJETE') {
                $dossier->update(['statut_global' => 'ANNULE', 'est_annule' => true,
                    'motif_annulation' => 'Rejeté lors de la validation par '.$validated['type_validateur'],
                    'annule_par_matricule' => $agent?->matricule,
                    'annule_le' => now()]);
                $this->logAudit(
                    $dossier,
                    'REJET',
                    $ancien,
                    'ANNULE',
                    "Validation rejetée par {$validated['type_validateur']} | Signataire: {$signatureCompte}"
                );
                return [
                    'event' => 'REJECTED',
                    'next_validator' => null,
                ];
            }

            $montantRetenu = round((float) ($montantValide ?? $conditionsAvantDecision['montant']), 2);
            $dureeRetenue = $dureeValidee ?? $conditionsAvantDecision['duree_mois'];
            $calculRetenu = $this->amortissement->simuler(
                (float) $montantRetenu,
                (float) $dossier->taux_interet_mensuel,
                (int) $dureeRetenue
            );

            $dossier->update([
                'montant_approuve' => $montantRetenu,
                'duree_mois' => $dureeRetenue,
                'montant_total_echeances' => $calculRetenu['total_general'],
                'total_interets' => $calculRetenu['total_interets'],
            ]);

            // Activer le bloc suivant
            $prochainOrdre = $validation->ordre_etape + 1;
            $prochaineValidation = $dossier->validations()->where('ordre_etape', $prochainOrdre)->first();

            if ($prochaineValidation) {
                $prochaineValidation->update(['etape_precedente_ok' => true]);
                $this->logAudit($dossier, 'VALIDATION_PARTIELLE', $ancien, 'EN_VALIDATION',
                    "Bloc {$validated['type_validateur']} validé | Signataire: {$signatureCompte} | Montant: {$montantRetenu} | Durée: {$dureeRetenue} mois");
                return [
                    'event' => 'STEP_VALIDATED',
                    'next_validator' => $prochaineValidation->type_validateur,
                ];
            } else {
                // Tous les blocs sont validés
                $dossier->update([
                    'montant_approuve' => $montantRetenu,
                    'duree_mois' => $dureeRetenue,
                    'montant_total_echeances' => $calculRetenu['total_general'],
                    'total_interets' => $calculRetenu['total_interets'],
                ]);
                $dossier->update(['statut_global' => 'PRET_A_DEBLOQUER']);
                $this->logAudit(
                    $dossier,
                    'VALIDATION_COMPLETE',
                    $ancien,
                    'PRET_A_DEBLOQUER',
                    "Validation finale signée par {$signatureCompte} | Montant retenu: {$montantRetenu} | Durée retenue: {$dureeRetenue} mois"
                );
                return [
                    'event' => 'READY_FOR_DISBURSEMENT',
                    'next_validator' => null,
                ];
            }
        });

        $notificationService = app(NotificationService::class);
        $validatorLabelMap = [
            'AGENT_CREDIT' => 'Agent crédit',
            'CHARGE_OPERATIONS' => 'Chargé des opérations',
            'CONTROLEUR' => 'Contrôleur',
            'GERANT' => 'Gérant',
        ];

        $actorName = trim(implode(' ', array_filter([
            $agent?->prenom,
            $agent?->nom,
        ])));
        $actorName = $actorName !== '' ? $actorName : ($user->name ?? 'Système');

        $targetUsers = User::query()
            ->whereIn('agent_matricule', array_values(array_filter([
                $dossier->agent_createur_matricule,
                $dossier->agent_analyse_matricule,
            ])))
            ->get();

        if (($notificationContext['event'] ?? null) === 'STEP_VALIDATED' && !empty($notificationContext['next_validator'])) {
            $nextValidatorType = $notificationContext['next_validator'];
            $nextPerm = $typeToPermission[$nextValidatorType] ?? null;

            if ($nextPerm) {
                $notificationService->notifyUsersWithPermission(
                    $nextPerm,
                    'Validation crédit en cours',
                    sprintf(
                        'Le dossier %s attend maintenant la validation de type %s. Action de %s.',
                        $dossier->numero_dossier,
                        $validatorLabelMap[$nextValidatorType] ?? $nextValidatorType,
                        $actorName
                    ),
                    [
                        'type' => 'action_required',
                        'category' => 'credit',
                        'icon' => 'fas fa-file-signature',
                        'action_url' => route('credit.show', $dossier),
                    ]
                );
            }
        }

        if (($notificationContext['event'] ?? null) === 'REJECTED') {
            $notificationService->notifyUsers(
                $targetUsers,
                'Dossier crédit rejeté',
                sprintf('Le dossier %s a été rejeté pendant la phase de validation par %s.', $dossier->numero_dossier, $actorName),
                [
                    'type' => 'danger',
                    'category' => 'credit',
                    'icon' => 'fas fa-times-circle',
                    'action_url' => route('credit.show', $dossier),
                ]
            );
        }

        if (($notificationContext['event'] ?? null) === 'READY_FOR_DISBURSEMENT') {
            $notificationService->notifyUsersWithPermission(
                'EBEN-PER64',
                'Crédit prêt à débloquer',
                sprintf('Le dossier %s est prêt à la phase de déblocage.', $dossier->numero_dossier),
                [
                    'type' => 'warning',
                    'category' => 'credit',
                    'icon' => 'fas fa-hand-holding-usd',
                    'action_url' => route('credit.deblocage', $dossier),
                ]
            );

            $notificationService->notifyUsers(
                $targetUsers,
                'Validation crédit terminée',
                sprintf('Le dossier %s is validé et prêt à débloquer.', $dossier->numero_dossier),
                [
                    'type' => 'info',
                    'category' => 'credit',
                    'icon' => 'fas fa-check-circle',
                    'action_url' => route('credit.show', $dossier),
                ]
            );
        }

        return redirect()->route('credit.show', $dossier)
            ->with('success', 'Validation enregistrée.');
    }

    // ================================================================
    // DÉBLOCAGE
    // ================================================================

    public function deblocage(CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        if (!$dossier->peutEtreDebloque()) {
            return back()->with('error', 'Ce dossier ne remplit pas les conditions de déblocage.');
        }

        $dossier->load(['client','compte','validations']);

        // Soldes du coffre central disponibles pour le déblocage
        $coffreCentral = CaissesGuichet::central()->first();
        $comptesDebit = collect();
        if ($coffreCentral) {
            $comptesDebit = CaissesGuichetSolde::where('guichet_id', $coffreCentral->id)
                ->where('solde_en_caisse', '>', 0)
                ->with('guichet')
                ->get();
        }

        $demande = $dossier;

        // ── Répartition automatique du montant approuvé ──────────────────
        $montantTotal = (float) $dossier->montant_approuve;
        $netVerse     = round($montantTotal * 0.80, 2);
        $caution      = round($montantTotal * 0.20, 2);
        $fraisDossier = round($montantTotal * 0.01, 2);
        $fraisEtude   = round($montantTotal * 0.03, 2);
        $fraisTotal   = round($fraisDossier + $fraisEtude, 2);

        // ── Précondition RMB : 24% (20% caution + 4% frais) ─────────────
        $provisionRmbMin = round($montantTotal * 0.24, 2);

        $compteRmb = Compte::where('client_matricule', $dossier->client_matricule)
            ->where('type', 'RMB')
            ->where('devise', $dossier->devise)
            ->first();

        $rmbCompteExiste   = $compteRmb !== null;
        $rmbSoldeActuel    = $rmbCompteExiste ? (float) $compteRmb->solde_reel : 0.0;
        $rmbMontantManquant = max(0, $provisionRmbMin - $rmbSoldeActuel);
        $rmbPreconditionOk  = $rmbCompteExiste && $rmbSoldeActuel >= $provisionRmbMin;

        return view('credit.deblocage', compact(
            'dossier', 'demande', 'comptesDebit',
            'montantTotal', 'netVerse', 'caution', 'fraisDossier', 'fraisEtude', 'fraisTotal',
            'provisionRmbMin', 'rmbCompteExiste', 'rmbSoldeActuel', 'rmbMontantManquant', 'rmbPreconditionOk'
        ));
    }

    public function storeDeblocage(Request $request, CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        if ($dossier->deblocage()->exists()) {
            return back()->with('error', 'Ce dossier a deja ete debloque. Aucune seconde execution n\'est autorisee.');
        }

        if (!$dossier->peutEtreDebloque()) {
            return back()->with('error', 'Les conditions de déblocage ne sont pas remplies.');
        }

        $validated = $request->validate([
            'coffre_solde_id'            => 'required|integer|exists:tb_caisses_guichets_soldes,id',
            'montant_debloque'           => 'required|numeric|min:1',
            'date_deblocage'             => 'required|date',
            'date_premier_remboursement' => 'required|date|after:today',
            'frais_dossier'             => 'nullable|numeric|min:0',
            'commission_totale'         => 'nullable|numeric|min:0',
            'reference_comptable'        => 'nullable|string|max:100',
            'observations'              => 'nullable|string',
        ]);

        $user   = Auth::user();
        $agentMatricule = $user?->agent?->matricule
            ?? $dossier->agent_analyse_matricule
            ?? Agent::query()->value('matricule');

        if (empty($agentMatricule)) {
            return back()->withInput()->with('error', 'Aucun agent valide n\'est disponible pour tracer le deblocage.');
        }

        $montant = (float)$validated['montant_debloque'];

        $coffreSolde = CaissesGuichetSolde::findOrFail($validated['coffre_solde_id']);

        $alreadyDebloque = false;
        $deblocageRefs = [];

        DB::transaction(function () use ($dossier, $validated, $agentMatricule, $montant, $coffreSolde, &$alreadyDebloque, &$deblocageRefs) {
            $dossier = CreditDemande::whereKey($dossier->id)->lockForUpdate()->firstOrFail();

            if ($dossier->deblocage()->exists()) {
                $alreadyDebloque = true;
                return;
            }

            $coffreSolde = CaissesGuichetSolde::with('guichet')
                ->whereKey($coffreSolde->id)
                ->lockForUpdate()
                ->firstOrFail();

            $ancien = $dossier->statut_global;
            $compteCredit = $this->resolveCompteCreditClient($dossier);
            $compteGtc = $this->resolveCompteGtcClient($dossier);

            $montantBrut = round($montant, 2);
            $caution     = round($montantBrut * 0.20, 2);
            $fraisReel   = round($montantBrut * 0.04, 2);
            $netVerse    = round($montantBrut - $caution - $fraisReel, 2);

            $soldeAvantRmb = (float) $compteCredit->solde_reel;

            // 1. Créditer le compte RMB client (100% brut)
            $compteCredit->increment('solde_reel', $montantBrut);
            $soldeApresDepotBrutRmb = round($soldeAvantRmb + $montantBrut, 2);

            // 2. Prélever la caution (20%) du RMB → GTC
            $compteCredit->decrement('solde_reel', $caution);
            $soldeApresTransfertRmb = round($soldeApresDepotBrutRmb - $caution, 2);

            $soldeAvantGtc = (float) $compteGtc->solde_reel;
            $compteGtc->increment('solde_reel', $caution);
            $compteGtc->increment('solde_bloque', $caution);
            $soldeApresGtc = round($soldeAvantGtc + $caution, 2);

            // 3. Prélever les frais (4%) du RMB → coffre central
            $compteCredit->decrement('solde_reel', $fraisReel);

            $compteCredit->refresh();
            $soldeApresRmb = (float) $compteCredit->solde_reel;

            $referenceBase    = 'DEB-' . $dossier->numero_dossier . '-' . now()->format('YmdHis');
            $referenceDepot   = $referenceBase . '-D';
            $referenceCaution = $referenceBase . '-C';
            $referenceGtc     = $referenceBase . '-C-G';
            $referenceFrais   = $referenceBase . '-F';

            // 5. Historique RMB: dépôt brut de déblocage (100%)
            $transactionDepot = Transaction::create([
                'compte_code'             => $compteCredit->code_compte,
                'agent_matricule'         => $agentMatricule,
                'guichet_id'              => $coffreSolde->guichet_id,
                'devise_code'             => $dossier->devise,
                'type'                    => Transaction::DEPOT,
                'montant'                 => $montantBrut,
                'montant_commission_total'=> 0,
                'solde_compte_avant'      => $soldeAvantRmb,
                'solde_compte_apres'      => $soldeApresDepotBrutRmb,
                'montant_total_client'    => $montantBrut,
                'montant_net_client'      => $montantBrut,
                'reference'               => $referenceDepot,
                'observations'            => 'Deblocage credit ' . $dossier->numero_dossier . ' (100% brut sur RMB)',
                'statut'                  => Transaction::CONFIRME,
                'date_operation'          => Carbon::parse($validated['date_deblocage']),
            ]);

            // 6. Historique RMB: retrait 20% caution → transfert GTC
            $transactionCautionRmb = Transaction::create([
                'compte_code'             => $compteCredit->code_compte,
                'agent_matricule'         => $agentMatricule,
                'guichet_id'              => $coffreSolde->guichet_id,
                'devise_code'             => $dossier->devise,
                'type'                    => Transaction::RETRAIT,
                'montant'                 => $caution,
                'montant_commission_total'=> 0,
                'solde_compte_avant'      => $soldeApresDepotBrutRmb,
                'solde_compte_apres'      => $soldeApresTransfertRmb,
                'montant_total_client'    => $caution,
                'montant_net_client'      => $caution,
                'reference'               => $referenceCaution,
                'observations'            => 'Transfert caution GTC credit ' . $dossier->numero_dossier . ' (20% RMB -> GTC bloque)',
                'statut'                  => Transaction::CONFIRME,
                'date_operation'          => Carbon::parse($validated['date_deblocage']),
            ]);

            // 6-bis. Historique GTC: depot de la caution bloquee (20%)
            $transactionCautionGtc = Transaction::create([
                'compte_code'             => $compteGtc->code_compte,
                'agent_matricule'         => $agentMatricule,
                'guichet_id'              => $coffreSolde->guichet_id,
                'devise_code'             => $dossier->devise,
                'type'                    => Transaction::DEPOT,
                'montant'                 => $caution,
                'montant_commission_total'=> 0,
                'solde_compte_avant'      => $soldeAvantGtc,
                'solde_compte_apres'      => $soldeApresGtc,
                'montant_total_client'    => $caution,
                'montant_net_client'      => $caution,
                'reference'               => $referenceGtc,
                'observations'            => 'Depot caution GTC credit ' . $dossier->numero_dossier . ' (20% bloque)',
                'statut'                  => Transaction::CONFIRME,
                'date_operation'          => Carbon::parse($validated['date_deblocage']),
            ]);

            // 7. Historique RMB: frais non remboursables 4% → coffre central
            $transactionFrais = Transaction::create([
                'compte_code'             => $compteCredit->code_compte,
                'agent_matricule'         => $agentMatricule,
                'guichet_id'              => $coffreSolde->guichet_id,
                'devise_code'             => $dossier->devise,
                'type'                    => Transaction::RETRAIT,
                'montant'                 => $fraisReel,
                'montant_commission_total'=> 0,
                'solde_compte_avant'      => $soldeApresTransfertRmb,
                'solde_compte_apres'      => $soldeApresRmb,
                'montant_total_client'    => $fraisReel,
                'montant_net_client'      => $fraisReel,
                'reference'               => $referenceFrais,
                'observations'            => 'Frais deblocage credit ' . $dossier->numero_dossier . ' (4% non remboursables)',
                'statut'                  => Transaction::CONFIRME,
                'date_operation'          => Carbon::parse($validated['date_deblocage']),
            ]);

            // 9. Verser 20% (caution) + 4% (frais) dans le coffre central
            $coffreGeneral = CaissesGuichet::central()->lockForUpdate()->first();
            $coffreGeneralId = $coffreGeneral?->id ?? $coffreSolde->guichet_id;

            $soldeCoffreGeneral = CaissesGuichetSolde::where('guichet_id', $coffreGeneralId)
                ->where('devise_code', $dossier->devise)
                ->lockForUpdate()
                ->first();

            if (!$soldeCoffreGeneral) {
                $soldeCoffreGeneral = CaissesGuichetSolde::create([
                    'guichet_id'      => $coffreGeneralId,
                    'devise_code'     => $dossier->devise,
                    'solde_en_caisse' => 0,
                ]);
            }

            $totalCoffre = round($caution + $fraisReel, 2); // 24% total
            $soldeCoffreGeneral->increment('solde_en_caisse', $totalCoffre);
            $soldeCoffreGeneralNouveau = (float) $soldeCoffreGeneral->fresh()->solde_en_caisse;

            CreditDeblocage::create([
                'credit_demande_id'     => $dossier->id,
                'agent_matricule'       => $agentMatricule,
                'compte_debit_id'       => $coffreSolde->guichet->code_guichet ?? ('GUICHET-' . $coffreSolde->guichet_id),
                'guichet_solde_id'      => $coffreSolde->id,
                'compte_credit_id'      => $compteCredit->code_compte,
                'montant_debloque'     => $montantBrut,
                'montant_caution'      => $caution,
                'devise'               => $dossier->devise,
                'frais_dossier'        => $fraisReel,
                'montant_net_verse'    => $netVerse,
                'reference_transaction' => $referenceDepot,
                'numero_ordre'         => $referenceCaution,
                'observations'         => trim((string) ($validated['observations'] ?? '')) . ' | refs: depot=' . $referenceDepot . ', caution_rmb=' . $referenceCaution . ', caution_gtc=' . $referenceGtc . ', frais=' . $referenceFrais,
                'debloque_le'          => Carbon::parse($validated['date_deblocage']),
            ]);

            // Générer l'échéancier
            $datePremier = Carbon::parse($validated['date_premier_remboursement']);
            
            // Mettre à jour la commission si fournie dans le formulaire
            if (isset($validated['commission_totale'])) {
                $dossier->update(['commission_totale' => $validated['commission_totale']]);
            }
            
            $this->amortissement->genererEtSauvegarder($dossier, $datePremier);

            $dossier->update([
                'compte_id' => $compteCredit->code_compte,
                'portefeuille_id' => $dossier->portefeuille_id ?? $compteCredit->portefeuille_id,
                'statut_global' => 'DEBLOQUE',
            ]);
            $this->logAudit($dossier, 'DEBLOCAGE', $ancien, 'DEBLOQUE',
                "Montant débloqué : {$montant} {$dossier->devise}");

            $deblocageRefs = [
                'reference_deblocage'      => $transactionDepot->reference,
                'reference_caution'        => $transactionCautionRmb->reference,
                'reference_transfert_gtc'  => $transactionCautionGtc->reference,
                'reference_frais'          => $transactionFrais->reference,
                'transaction_deblocage_id' => $transactionDepot->id,
                'transaction_caution_id'   => $transactionCautionRmb->id,
                'transaction_gtc_id'       => $transactionCautionRmb->id,
                'transaction_gtc_depot_id' => $transactionCautionGtc->id,
                'transaction_frais_id'     => $transactionFrais->id,
                'compte_rmb_code'          => $compteCredit->code_compte,
                'compte_gtc_code'          => $compteGtc->code_compte,
                'coffre_general_id'        => $coffreGeneralId,
                'coffre_general_solde'     => $soldeCoffreGeneralNouveau,
            ];
        });

        if ($alreadyDebloque) {
            return back()->with('error', 'Ce dossier a deja ete debloque par un autre traitement.');
        }

        $notificationService = app(NotificationService::class);
        $targetUsers = User::query()
            ->whereIn('agent_matricule', array_values(array_filter([
                $dossier->agent_createur_matricule,
                $dossier->agent_analyse_matricule,
            ])))
            ->get();

        $notificationService->notifyUsers(
            $targetUsers,
            'Crédit débloqué',
            sprintf('Le dossier %s a été débloqué avec succès.', $dossier->numero_dossier),
            [
                'type' => 'info',
                'category' => 'credit',
                'icon' => 'fas fa-money-check-alt',
                'action_url' => route('credit.show', $dossier),
            ]
        );

        $notificationService->notifyUsersWithPermission(
            'EBEN-PER111',
            'Crédit en remboursement',
            sprintf('Le dossier %s est désormais débloqué et prêt pour le suivi des remboursements.', $dossier->numero_dossier),
            [
                'type' => 'info',
                'category' => 'credit',
                'icon' => 'fas fa-calendar-check',
                'action_url' => route('credit.remboursement', $dossier),
            ]
        );

        return redirect()->route('credit.show', $dossier)
            ->with('success', "Deblocage de {$dossier->numero_dossier} effectue (100% en RMB, transfert 20% vers GTC, frais 4% non remboursables).")
            ->with('deblocage_refs', $deblocageRefs);
    }

    private function resolveCompteCreditClient(CreditDemande $dossier): Compte
    {
        if (!empty($dossier->compte_id)) {
            $compteExistant = Compte::find($dossier->compte_id);
            if ($compteExistant) {
                return $compteExistant;
            }
        }

        $compteRmb = Compte::where('client_matricule', $dossier->client_matricule)
            ->where('type', 'RMB')
            ->where('devise', $dossier->devise)
            ->first();

        if ($compteRmb) {
            return $compteRmb;
        }

        return Compte::create([
            'client_matricule' => $dossier->client_matricule,
            'type' => 'RMB',
            'solde_reel' => 0,
            'solde_bloque' => 0,
            'devise' => $dossier->devise,
            // Bug corrigé : reprendre le portefeuille du dossier crédit, jamais null
            'portefeuille_id' => $dossier->portefeuille_id,
        ]);
    }

    private function resolveCompteGtcClient(CreditDemande $dossier): Compte
    {
        $compteGtc = Compte::where('client_matricule', $dossier->client_matricule)
            ->where('type', 'GTC')
            ->where('devise', $dossier->devise)
            ->first();

        if ($compteGtc) {
            return $compteGtc;
        }

        return Compte::create([
            'client_matricule' => $dossier->client_matricule,
            'type' => 'GTC',
            'solde_reel' => 0,
            'solde_bloque' => 0,
            'devise' => $dossier->devise,
            // Bug corrigé : reprendre le portefeuille du dossier crédit, jamais null
            'portefeuille_id' => $dossier->portefeuille_id,
        ]);
    }

    // ================================================================
    // REMBOURSEMENT
    // ================================================================

    public function remboursement(CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        if (!in_array($dossier->statut_global, ['DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD'])) {
            return back()->with('error', 'Ce dossier n\'est pas en phase de remboursement.');
        }

        $dossier->load(['client','echeancier.echeances','remboursements']);

        $prochaineEcheance = $dossier->echeancier?->echeances()
            ->whereIn('statut', ['EN_ATTENTE','EN_RETARD'])
            ->orderBy('numero_echeance')
            ->first();

        $demande = $dossier;
        $echeancier = $dossier->echeancier;
        $comptesInstitution = collect();

        // Vérification : l'agent doit avoir un guichet fixe OUVERT
        $user = Auth::user();
        $matricule = $user?->agent?->matricule;
        if (!$matricule) {
            return back()->with('error', 'Aucun profil agent associé à ce compte.');
        }

        $affectation = \App\Models\RH\Affectation::with('guichet')
            ->where('agent_matricule', $matricule)
            ->where('Etat', 'ACTIF')
            ->whereNotNull('guichet_id')
            ->latest('date_debut')
            ->first();

        if (!$affectation || !$affectation->guichet) {
            return back()->with('error', 'Accès refusé : Vous devez être titulaire d\'un guichet fixe pour accéder à cette page.');
        }

        if ($affectation->guichet->statut_operationnel !== 'OUVERT') {
            return back()->with('error', 'Accès refusé : Votre guichet (' . $affectation->guichet->code_guichet . ') est ' . $affectation->guichet->statut_operationnel . '. Veuillez l\'ouvrir avant d\'accéder aux remboursements.');
        }

        $guichet = $affectation->guichet;

        // Récupérer le solde RMB actuel du client pour l'affichage et les calculs
        $soldeRmbActuel = 0;
        $compteRmb = \App\Models\Clients\Compte::where('client_matricule', $dossier->client_matricule)
            ->where('type', 'RMB')
            ->where('devise', $dossier->devise)
            ->first();
        if ($compteRmb) {
            $soldeRmbActuel = (float) $compteRmb->solde_reel;
        }

        // Récupérer la liste des échéances impayées pour la logique d'anticipation
        $echeancier = $dossier->echeancier; // S'assurer que l'échéancier est bien chargé
        $echeancesImpayees = $echeancier ? $echeancier->echeances()
            ->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD', 'PARTIELLEMENT_PAYE'])
            ->orderBy('numero_echeance')
            ->get() : collect();

        return view('credit.remboursement', compact(
            'dossier', 'demande', 'prochaineEcheance', 'echeancier', 
            'comptesInstitution', 'guichet', 'soldeRmbActuel', 'echeancesImpayees'
        ));
    }

    public function storeRemboursement(Request $request, CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        $validated = $request->validate([
            'echeance_id'         => 'nullable|integer|exists:tb_credit_echeances,id',
            'montant_recu'        => 'required|numeric|min:0.01',
            'dont_capital'        => 'required|numeric|min:0',
            'dont_interet'        => 'required|numeric|min:0',
            'dont_penalite'       => 'nullable|numeric|min:0',
            'type_remboursement'  => 'required|in:ECHEANCE,PARTIEL,ANTICIPE,PENALITE',
            'date_paiement'       => 'required|date',
            'reference_caisse'    => 'nullable|string|max:50',
            'observations'        => 'nullable|string',
        ]);

        $user  = Auth::user();
        $agent = $user->agent;

        $transactionId = null;

        DB::transaction(function () use ($validated, $dossier, $agent, &$transactionId) {
            // Verrou optimiste sur le dossier
            $dossier = CreditDemande::whereKey($dossier->id)->lockForUpdate()->firstOrFail();

            $montantRecu   = round((float) $validated['montant_recu'], 2);
            $montantAAppliquer = round((float) request()->input('montant_a_appliquer', $montantRecu), 2);
            $dontCapital   = round((float) $validated['dont_capital'], 2);
            $dontInteret   = round((float) $validated['dont_interet'], 2);
            $dontPenalite  = round((float) ($validated['dont_penalite'] ?? 0), 2);
            $datePaiement  = Carbon::parse($validated['date_paiement']);
            $agentMatricule = $agent?->matricule ?? 'SYSTEM';

            // ── 1. Mise à jour des échéances avec report de surplus ──────
            $echeances = $dossier->echeancier->echeances()
                ->orderBy('numero_echeance')
                ->lockForUpdate()
                ->get();

            // Trouver l'index de l'échéance ciblée (ou la première non soldée)
            $startIndex = 0;
            if (!empty($validated['echeance_id'])) {
                foreach ($echeances as $index => $ech) {
                    if ($ech->id == $validated['echeance_id']) {
                        $startIndex = $index;
                        break;
                    }
                }
            } else {
                foreach ($echeances as $index => $ech) {
                    if (in_array($ech->statut, ['EN_ATTENTE', 'EN_RETARD', 'PARTIELLEMENT_PAYE'])) {
                        $startIndex = $index;
                        break;
                    }
                }
            }

            // ── 2. Récupérer le compte RMB AVANT la boucle pour calculer le total disponible ──
            $compteRmb = Compte::where('client_matricule', $dossier->client_matricule)
                ->where('type', 'RMB')
                ->where('devise', $dossier->devise)
                ->lockForUpdate()
                ->first();

            if (!$compteRmb) {
                throw new \Exception('Compte RMB du client introuvable.');
            }

            $soldeRmbActuel = (float) $compteRmb->solde_reel;
            // Le total disponible pour le prêt est EXACTEMENT le montant que l'utilisateur a accepté d'appliquer
            // (qui inclut déjà le solde RMB si le frontend l'a ajouté)
            $totalDisponible = $montantAAppliquer;

            $surplus = $totalDisponible;
            $echeanceTraitee = null;
            $montantTotalApplique = 0;
            $totalCapitalPaye = 0;
            $totalInteretPaye = 0;
            $totalPenalitePaye = 0;

            for ($i = $startIndex; $i < count($echeances) && $surplus > 0.01; $i++) {
                $ech = $echeances[$i];
                $totalDu   = round((float) $ech->total_echeance, 2);
                $dejaPaye  = round((float) $ech->montant_paye, 2);
                $resteDu   = max(0, round($totalDu - $dejaPaye, 2));

                if ($resteDu <= 0) {
                    continue;
                }

                $montantApplique = min($surplus, $resteDu);
                $nouveauMontantPaye = round($dejaPaye + $montantApplique, 2);
                $nouveauStatut = $nouveauMontantPaye >= $totalDu ? 'PAYE' : 'PARTIELLEMENT_PAYE';

                // Calculer la répartition capital/intérêt pour CETTE échéance
                $capitalEcheance = round((float) $ech->capital_echeance, 2);
                $interetEcheance = round((float) $ech->interet_echeance, 2);
                $capitalDejaPaye = round((float) ($ech->montant_paye ?? 0) * ($capitalEcheance / max($totalDu, 1)), 2);
                $interetDejaPaye = round((float) ($ech->montant_paye ?? 0) * ($interetEcheance / max($totalDu, 1)), 2);
                
                $capitalRestant = max(0, $capitalEcheance - $capitalDejaPaye);
                $interetRestant = max(0, $interetEcheance - $interetDejaPaye);
                
                // Répartir le montant appliqué : intérêt d'abord, puis capital
                $dontInteretThis = min($montantApplique, $interetRestant);
                $dontCapitalThis = $montantApplique - $dontInteretThis;

                $ech->update([
                    'montant_paye'           => $nouveauMontantPaye,
                    'statut'                 => $nouveauStatut,
                    'date_paiement_effectif' => $datePaiement->toDateString(),
                ]);

                // Créer un enregistrement de remboursement pour CETTE échéance
                CreditRemboursement::create([
                    'credit_demande_id'  => $dossier->id,
                    'echeance_id'        => $ech->id,
                    'agent_matricule'    => $agentMatricule,
                    'compte_id'          => $dossier->compte_id,
                    'montant_recu'       => $montantApplique,
                    'dont_capital'       => $dontCapitalThis,
                    'dont_interet'       => $dontInteretThis,
                    'dont_penalite'      => 0,
                    'devise'             => $dossier->devise,
                    'type_remboursement' => $nouveauStatut === 'PAYE' ? 'ECHEANCE' : 'PARTIEL',
                    'reference_caisse'   => $reference ?? null,
                    'observations'       => sprintf(
                        'Remboursement éch. #%s – Capital: %s, Intérêt: %s',
                        $ech->numero_echeance,
                        number_format($dontCapitalThis, 2),
                        number_format($dontInteretThis, 2)
                    ),
                    'recu_le'            => $datePaiement,
                    'transaction_id'     => null, // Sera mis à jour après création de la transaction
                ]);

                $totalCapitalPaye += $dontCapitalThis;
                $totalInteretPaye += $dontInteretThis;

                $echeanceTraitee = $ech;
                $surplus = round($surplus - $montantApplique, 2);
                $montantTotalApplique += $montantApplique;
            }

            // ── 3. Comptabilisation Caisse et Compte Client ──────────────
            $guichet = $this->getGuichetAgent();
            if (!$guichet) {
                throw new \Exception('Aucun guichet affecté à votre compte.');
            }
            $soldeGuichet = CaissesGuichetSolde::where('guichet_id', $guichet->id)
                ->where('devise_code', $dossier->devise)
                ->lockForUpdate()
                ->first();

            if (!$soldeGuichet) {
                throw new \Exception("La devise {$dossier->devise} n'est pas disponible sur votre guichet.");
            }

            // $compteRmb est déjà récupéré plus haut avec lockForUpdate()

            // Logique comptable correcte :
            // 1. L'argent liquide ($montantRecu) entre dans la caisse du guichet.
            // 2. Le montant total appliqué au prêt est $montantTotalApplique.
            // 3. Si $montantTotalApplique > $montantRecu, la différence est PRÉLEVÉE (débitée) du compte RMB.
            // 4. Si $montantRecu > $montantTotalApplique, l'excédent est DÉPOSÉ (crédité) sur le compte RMB.
            
            $montantPreleveSurRmb = max(0, $montantTotalApplique - $montantRecu);
            $montantDeposeSurRmb = max(0, $montantRecu - $montantTotalApplique);
            
            $soldeRmbAvant = (float) $compteRmb->solde_reel;
            $soldeGuichetAvant = (float) $soldeGuichet->solde_en_caisse;

            if ($montantPreleveSurRmb > 0) {
                $compteRmb->decrement('solde_reel', $montantPreleveSurRmb);
            } elseif ($montantDeposeSurRmb > 0) {
                $compteRmb->increment('solde_reel', $montantDeposeSurRmb);
            }
            
            // La caisse du guichet reçoit TOUJOURS le montant liquide versé par le client
            $soldeGuichet->increment('solde_en_caisse', $montantRecu);
            
            $soldeRmbApres = (float) $compteRmb->solde_reel;
            $soldeGuichetApres = round($soldeGuichetAvant + $montantRecu, 2);

            $reference = $validated['reference_caisse'] ?? 'REM-EBEN-' . $dossier->id . '-' . now()->format('His') . rand(10, 99);

            // 3. Enregistrer la transaction comme un DÉPÔT (pour qu'elle figure dans les entrées de caisse du rapport)
            $transaction = Transaction::create([
                'compte_code'             => $compteRmb->code_compte,
                'agent_matricule'         => $agentMatricule,
                'guichet_id'              => $guichet->id,
                'devise_code'             => $dossier->devise,
                'type'                    => Transaction::REMBOURSEMENT,
                'montant'                 => $montantRecu,
                'montant_commission_total'=> 0,
                'solde_compte_avant'      => $soldeRmbAvant,
                'solde_compte_apres'      => $soldeRmbApres,
                'montant_total_client'    => $montantRecu,
                'montant_net_client'      => $montantRecu,
                'reference'               => $reference,
                'observations'            => sprintf(
                    'Remboursement crédit %s – Capital total: %s, Intérêt total: %s',
                    $dossier->numero_dossier,
                    number_format($totalCapitalPaye, 2),
                    number_format($totalInteretPaye, 2)
                ),
                'statut'                  => Transaction::CONFIRME,
                'date_operation'          => $datePaiement,
            ]);
            
            $transactionId = $transaction->id;

            // Mettre à jour le transaction_id dans tous les CreditRemboursement créés dans la boucle
            if ($transactionId) {
                CreditRemboursement::where('credit_demande_id', $dossier->id)
                    ->where('recu_le', $datePaiement)
                    ->whereNull('transaction_id')
                    ->update(['transaction_id' => $transactionId]);
            }

            // ── 4. Transition de statut du dossier ───────────────────────
            $statutActuel = $dossier->statut_global;

            if ($statutActuel === 'DEBLOQUE') {
                $dossier->update(['statut_global' => 'EN_REMBOURSEMENT']);
                $statutActuel = 'EN_REMBOURSEMENT';
            }

            // ── 5. Vérification clôture totale ───────────────────────────
            $echeancier = $dossier->echeancier()->with('echeances')->first();
            $totalEcheances = $echeancier?->echeances->count() ?? 0;
            $toutes_soldees = false;

            if ($totalEcheances > 0 && $echeancier) {
                $toutes_soldees = $echeancier->echeances->every(
                    fn ($e) => round((float) $e->montant_paye, 2) >= round((float) $e->total_echeance, 2)
                );
            }

            if ($toutes_soldees) {
                $dossier->update(['statut_global' => 'SOLDE']);
                $this->logAudit($dossier, 'CLOTURE_CREDIT', $statutActuel, 'SOLDE', 'Crédit entièrement soldé par remboursements.');

                // Restituer la caution (20%) au client
                $deblocage = $dossier->deblocage()->first();
                $cautionARestituer = round((float) ($deblocage?->montant_caution ?? 0), 2);

                if ($cautionARestituer > 0) {
                    $compteGtc = Compte::where('client_matricule', $dossier->client_matricule)
                        ->where('type', 'GTC')
                        ->where('devise', $dossier->devise)
                        ->lockForUpdate()
                        ->first();

                    if ($compteGtc) {
                        $montantCaution = min($cautionARestituer, round((float) ($compteGtc->solde_bloque ?? 0), 2));
                        
                        if ($montantCaution > 0) {
                            $soldeGtcAvant  = (float) $compteGtc->solde_reel;
                            $bloqueGtcAvant = (float) $compteGtc->solde_bloque;

                            $compteGtc->update([
                                'solde_reel'   => max(0, round($soldeGtcAvant - $montantCaution, 2)),
                                'solde_bloque' => max(0, round($bloqueGtcAvant - $montantCaution, 2)),
                            ]);

                            $compteRmbClient = Compte::where('client_matricule', $dossier->client_matricule)
                                ->where('type', 'RMB')
                                ->where('devise', $dossier->devise)
                                ->lockForUpdate()
                                ->first();

                            $coffreGeneral = CaissesGuichet::central()->lockForUpdate()->first();

                            if ($compteRmbClient) {
                                $soldeRmbAvantRestit = (float) $compteRmbClient->solde_reel;
                                $compteRmbClient->increment('solde_reel', $montantCaution);

                                Transaction::create([
                                    'compte_code'             => $compteRmbClient->code_compte,
                                    'agent_matricule'         => $agentMatricule,
                                    'guichet_id'              => $coffreGeneral?->id ?? $guichet->id,
                                    'devise_code'             => $dossier->devise,
                                    'type'                    => Transaction::DEPOT,
                                    'montant'                 => $montantCaution,
                                    'montant_commission_total'=> 0,
                                    'solde_compte_avant'      => $soldeRmbAvantRestit,
                                    'solde_compte_apres'      => round($soldeRmbAvantRestit + $montantCaution, 2),
                                    'montant_total_client'    => $montantCaution,
                                    'montant_net_client'      => $montantCaution,
                                    'reference'               => 'CAUTION-RESTIT-' . $dossier->numero_dossier . '-' . substr(uniqid(), -6),
                                    'observations'            => sprintf(
                                        'Restitution caution 20%% (%s %s) au client – crédit %s soldé intégralement.',
                                        number_format($montantCaution, 2),
                                        $dossier->devise,
                                        $dossier->numero_dossier
                                    ),
                                    'statut'                  => Transaction::CONFIRME,
                                    'date_operation'          => $datePaiement,
                                ]);
                            }

                            if ($coffreGeneral) {
                                $soldeCoffreGd = CaissesGuichetSolde::where('guichet_id', $coffreGeneral->id)
                                    ->where('devise_code', $dossier->devise)
                                    ->lockForUpdate()
                                    ->first();

                                if ($soldeCoffreGd) {
                                    $soldeCoffreGd->decrement('solde_en_caisse', $montantCaution);
                                }
                            }
                        }
                    }
                }
            }
        });

        return redirect()->route('caisses.remboursements.liste')
            ->with('success', 'Remboursement enregistré avec succès.')
            ->with('transaction_id', $transactionId);
    }

    public function listeRemboursementCaissier(Request $request)
    {
        $dossiers = CreditDemande::where('statut_global', 'EN_REMBOURSEMENT')
            ->with(['client'])
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('credit.liste_remboursement_caissier', compact('dossiers'));
    }

    /**
        * Génère la fiche technique complète du dossier de crédit au format PDF.
    */
    public function pdfFiche(CreditDemande $dossier)
    {
        $this->authorizeDemandeAccess($dossier, true);
        $this->authorizeZoneAccess($dossier);
        $dossier->load([
                       'client',
                       'zone',
                        'analyse',
                        'validations.validateur',
                        'pieces',
                        'deblocage',
                        'deblocages.operateur',
                        'deblocages.guichetSolde',
                        'deblocages.compteCredit',
                        'echeancier.echeances'
                        ]);

        $pdf = Pdf::loadView('impressions.credit.fiche_credit', ['demande' => $dossier])
                    ->setPaper('a4', 'portrait');

        return $pdf->stream("Fiche_Credit_{$dossier->numero_dossier}.pdf");

   }

    // ================================================================
    // PDF ÉCHÉANCIER
    // ================================================================

    public function releveCredit(CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);
        $dossier->load(['client', 'echeancier.echeances', 'remboursements', 'deblocage']);
        $client = optional($dossier)->client;
        $clientFullName = trim(($client->nom ?? '') . ' ' . ($client->postnom ?? '') . ' ' . ($client->prenom ?? ''));
        $clientPhotoBase64 = null;
        if (!empty($client?->photo)) {
            $photoPath = base_path('images_projet/clients/' . basename($client->photo));
            if (file_exists($photoPath)) {
                $mime = mime_content_type($photoPath) ?: 'image/jpeg';
                $clientPhotoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($photoPath));
            }
        }
        $mouvements = [];
        if ($dossier->frais_dossier > 0) {
            $mouvements[] = ['date' => $dossier->created_at ?? now(), 'libelle' => 'Frais analyse dossier (4%)', 'debit' => $dossier->frais_dossier, 'credit' => 0, 'type' => 'frais'];
        }
        if ($dossier->deblocage && $dossier->deblocage->montant_caution > 0) {
            $mouvements[] = ['date' => $dossier->deblocage->created_at ?? now(), 'libelle' => 'Transfert caution 20% -> GTC (bloquee)', 'debit' => $dossier->deblocage->montant_caution, 'credit' => 0, 'type' => 'caution'];
        }
        if ($dossier->deblocage && $dossier->deblocage->montant_decaisse > 0) {
            $mouvements[] = ['date' => $dossier->deblocage->created_at ?? now(), 'libelle' => 'Deblocage credit - versement au client', 'debit' => 0, 'credit' => $dossier->deblocage->montant_decaisse, 'type' => 'deblocage'];
        }
        $soldeOuverture = 0;
        $remboursements = $dossier->remboursements->sortBy('date_paiement');
        foreach ($remboursements as $remb) {
            $mouvements[] = ['date' => $remb->date_paiement ?? now(), 'libelle' => 'Remboursement echeance #' . ($remb->echeance?->numero_echeance ?? '?') . ' (Cap: ' . number_format($remb->dont_capital, 2, ',', ' ') . ' | Int: ' . number_format($remb->dont_interet, 2, ',', ' ') . ')', 'debit' => 0, 'credit' => $remb->montant_recu, 'type' => 'remboursement'];
        }
        if ($dossier->statut_global === 'SOLDE' && $dossier->deblocage && $dossier->deblocage->montant_caution > 0) {
            $mouvements[] = ['date' => $dossier->date_cloture ?? now(), 'libelle' => 'Restitution caution 20% depuis GTC', 'debit' => 0, 'credit' => $dossier->deblocage->montant_caution, 'type' => 'restitution'];
        }
        usort($mouvements, function ($a, $b) { return $a['date'] <=> $b['date']; });
        $soldeCourant = $soldeOuverture;
        foreach ($mouvements as &$mvt) { $soldeCourant = $soldeCourant + $mvt['credit'] - $mvt['debit']; $mvt['solde'] = $soldeCourant; }
        unset($mvt);
        $soldeCloture = $soldeCourant;
        $totalDebits = array_sum(array_column($mouvements, 'debit'));
        $totalCredits = array_sum(array_column($mouvements, 'credit'));
        $capitalRestant = $dossier->capital_restant ?? 0;
        $cautionBloquee = ($dossier->deblocage && $dossier->statut_global !== 'SOLDE') ? $dossier->deblocage->montant_caution : 0;
        $totalInteretsPayes = $remboursements->sum('dont_interet');
        $totalCapitalPaye = $remboursements->sum('dont_capital');
        $echeancesRestantes = $dossier->echeancier?->echeances()->where('statut', 'EN_ATTENTE')->orderBy('numero_echeance')->get() ?? collect();
        $prochaineEcheance = $echeancesRestantes->first();
        $pdf = Pdf::loadView('impressions.credit.releve_credit', compact('dossier', 'client', 'clientFullName', 'clientPhotoBase64', 'mouvements', 'soldeOuverture', 'soldeCloture', 'totalDebits', 'totalCredits', 'capitalRestant', 'cautionBloquee', 'totalInteretsPayes', 'totalCapitalPaye', 'echeancesRestantes', 'prochaineEcheance'))->setPaper('A4', 'portrait');
        $filename = 'Releve_Credit_' . $dossier->numero_dossier . '_' . now()->format('Ymd') . '.pdf';
        return $pdf->stream($filename);
    }

    public function pdfEcheancier(CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        $dossier->load(['client','echeancier.echeances','deblocage']);

        if (!$dossier->echeancier) {
            return back()->with('error', "L'échéancier n'a pas encore été généré.");
        }

        $demande = $dossier;
        $echeancier = $dossier->echeancier;

        $pdf = Pdf::loadView('impressions.credit.echeancier', compact('dossier', 'demande', 'echeancier'))
            ->setPaper('A4', 'portrait');

        $filename = 'echeancier_' . $dossier->numero_dossier . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->stream($filename);
    }

    // ================================================================
    // SUPERVISION (vue globale)
    // ================================================================

    public function supervision(Request $request)
    {
        $user       = Auth::user();
        $zonesCodes = $this->resolveZoneScope($user);

        $query = CreditDemande::with(['client','zone'])
            ->when($zonesCodes !== null, fn($q) => $q->whereIn('code_zone', $zonesCodes));

        // Dossiers en retard
        $enRetard = (clone $query)
            ->whereIn('statut_global', ['EN_REMBOURSEMENT','DEBLOQUE'])
            ->whereHas('echeancier.echeances', fn($q) =>
                $q->where('statut', 'EN_ATTENTE')->where('date_echeance', '<', now()->toDateString())
            )->get();

        // Dossiers suspects / suspendus
        $alertes = (clone $query)
            ->whereIn('statut_global', ['SUSPECT','SUSPENDU'])
            ->get();

        // Dossiers prêts à débloquer
        $prets = (clone $query)
            ->where('statut_global', 'PRET_A_DEBLOQUER')
            ->get();

        // Statistiques par zone
        $statsZone = (clone $query)
            ->select('code_zone', DB::raw('count(*) as total'), DB::raw('sum(montant_demande) as montant_total'))
            ->groupBy('code_zone')
            ->get();

        $dossiers_retard = $enRetard;
        $dossiers_alertes = $alertes;
        $dossiers_pret_debloquer = $prets;
        $stats_zones = $statsZone->map(function ($z) {
            $z->total_dossiers = $z->total;
            $z->dossiers_actifs = 0;
            $z->en_retard = 0;
            $z->encours = $z->montant_total;
            $z->impayes = 0;
            return $z;
        });
        $stats = [
            'total_retard' => $enRetard->count(),
            'total_suspects' => $alertes->where('statut_global', 'SUSPECT')->count(),
            'total_suspendus' => $alertes->where('statut_global', 'SUSPENDU')->count(),
            'total_pret_debloquer' => $prets->count(),
        ];

        return view('credit.supervision', compact(
            'enRetard',
            'alertes',
            'prets',
            'statsZone',
            'dossiers_retard',
            'dossiers_alertes',
            'dossiers_pret_debloquer',
            'stats_zones',
            'stats'
        ));
    }

    // ================================================================
    // RÈGLEMENT AUTO D'UNE ÉCHÉANCE VIA RMB
    // ================================================================
    public function reglementAutoEcheance(Request $request, CreditDemande $dossier)
    {
        $echeanceId = $request->input('echeance_id');
        $echeance = CreditEcheance::findOrFail($echeanceId);

        // Vérifier que le solde RMB est suffisant
        $montantRestantDu = (float)$echeance->total_echeance - (float)$echeance->montant_paye;
        $compteRmb = Compte::where('client_matricule', $dossier->client_matricule)
            ->where('type', 'RMB')
            ->where('devise', $dossier->devise)
            ->first();
        
        if (!$compteRmb || $compteRmb->solde_reel < $montantRestantDu) {
            return redirect()->route('credit.show', $dossier)->with('error', 'Solde RMB insuffisant pour régler cette échéance.')->withFragment('tab_echeancier');
        }

        DB::transaction(function () use ($dossier, $echeance, $compteRmb, $montantRestantDu) {
            $soldeAvant = (float)$compteRmb->solde_reel;
            $soldeApres = $soldeAvant - $montantRestantDu;

            $compteRmb->decrement('solde_reel', $montantRestantDu);

            $echeance->increment('montant_paye', $montantRestantDu);
            
            if ((float)$echeance->montant_paye >= (float)$echeance->total_echeance) {
                $echeance->statut = 'PAYE';
                $echeance->date_paiement_effectif = now()->format('Y-m-d');
            } else {
                $echeance->statut = 'PARTIELLEMENT_PAYE';
            }
            $echeance->save();

            $agent = Auth::user()?->agent;
            $affectation = $agent?->affectations()->where('Etat', 'ACTIF')->whereNotNull('guichet_id')->first();
            $transaction = Transaction::create([
                'compte_code'              => $compteRmb->code_compte,
                'agent_matricule'          => $agent?->matricule ?? 'SYSTEM',
                'guichet_id'               => $affectation?->guichet_id,
                'devise_code'              => $dossier->devise,
                'type'                     => 'REMBOURSEMENT',
                'montant'                  => $montantRestantDu,
                'montant_commission_total' => 0,
                'solde_compte_avant'       => $soldeAvant,
                'solde_compte_apres'       => $soldeApres,
                'montant_total_client'     => $montantRestantDu,
                'montant_net_client'       => $montantRestantDu,
                'statut'                   => 'CONFIRME',
                'reference'                => 'AUTO-REG-' . $dossier->numero_dossier . '-' . $echeance->numero_echeance . '-' . now()->format('dmyHis'),
                'observations'             => 'Règlement automatique échéance ' . $echeance->numero_echeance . ' via RMB',
                'date_operation'           => now(),
            ]);

            $ratio = (float)$echeance->montant_paye / (float)$echeance->total_echeance;
            $dontCapital = (float)$echeance->capital_echeance * $ratio;
            $dontInteret = (float)$echeance->interet_echeance * $ratio;

            CreditRemboursement::create([
                'credit_demande_id' => $dossier->id,
                'echeance_id'       => $echeance->id,
                'agent_matricule'   => $agent?->matricule ?? 'SYSTEM',
                'compte_id'         => $compteRmb->code_compte,
                'montant_recu'      => $montantRestantDu,
                'dont_capital'      => $dontCapital,
                'dont_interet'      => $dontInteret,
                'dont_penalite'     => 0,
                'devise'            => $dossier->devise,
                'type_remboursement'=> 'ECHEANCE',
                'reference_caisse'  => $transaction->reference,
                'observations'      => 'Règlement automatique échéance ' . $echeance->numero_echeance . ' via RMB',
                'recu_le'           => now(),
                'transaction_id'    => $transaction->id,
            ]);
        });

        return redirect()->route('credit.show', $dossier)->with('success', '✅ Échéance réglée automatiquement avec succès via le compte RMB.')->withFragment('tab_echeancier');
    }

    // ================================================================
    // LOGS ET SECURITY HELPER STUBS
    // ================================================================

    private function logAudit(
        CreditDemande $dossier,
        string $action,
        ?string $ancienStatut,
        ?string $nouveauStatut,
        ?string $details = null
    ): void {
        try {
            CreditAudit::create([
                'credit_demande_id' => $dossier->id,
                'acteur_matricule'  => Auth::user()?->agent?->matricule,
                'type_action'       => $action,
                'ancien_statut'     => $ancienStatut,
                'nouveau_statut'    => $nouveauStatut,
                'details'           => $details,
                'ip_address'        => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('[Credit] Audit log failed: ' . $e->getMessage());
        }
    }

    private function resolveZoneScope($user): ?array
    {
        return $user?->hasPermission('EBEN-PER61') ? null : [$user?->agent?->code_zone ?? 'ZONE-01'];
    }

    private function resolvePortefeuilleScope($user): array
    {
        return DB::table('tb_portefeuilles_agents')->where('agent_matricule', $user?->agent?->matricule)->pluck('id')->toArray();
    }

    private function resolveCreationPortefeuilleOptions($user)
    {
        return Portefeuille::query()->get(['id', 'nom_portefeuille']);
    }

    private function authorizeDemandeAccess(CreditDemande $dossier, bool $throw = true): bool
    {
        return true;
    }

    private function authorizeZoneAccess(CreditDemande $dossier): void
    {
        // Guard de sécurité d'agence locale
    }

    private function resolveAssignableCreditAgents()
    {
        return Agent::orderBy('nom')->get(['matricule','nom','postnom','prenom']);
    }

    private function resolveDemandeurMeta($matricule): array
    {
        $agent = Agent::where('matricule', $matricule)->first();
        return [
            'nom' => $agent ? "{$agent->nom} {$agent->prenom}" : 'SYSTÈME',
            'matricule' => $matricule
        ];
    }

    private function isEligibleCreditAnalyst(string $matricule): bool
    {
        return true;
    }

    private function resolveAgentPortefeuilleIds(string $matricule): array
    {
        return DB::table('tb_portefeuilles_agents')->where('agent_matricule', $matricule)->pluck('id')->toArray();
    }

    private function getGuichetAgent()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $affectation = \App\Models\RH\Affectation::with('guichet')
            ->where('agent_matricule', $user->agent_matricule)
            ->where('Etat', 'ACTIF')
            ->whereNotNull('guichet_id')
            ->latest('date_debut')
            ->first();

        return $affectation?->guichet;
    }
}