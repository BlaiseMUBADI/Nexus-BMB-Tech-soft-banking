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
            // enRetardReel (pas statut_global) : source unique, cf. CreditDemande::scopeEnRetardReel().
            'en_retard'        => (clone $query)->enRetardReel()->count(),
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

    /**
     * Tableau de bord "En Cours" : pour chaque devise, combien de capital est
     * actuellement chez les clients (décaissé - remboursé = restant dû) et
     * combien d'intérêts (+ commissions) ont déjà été perçus sur les dossiers
     * actifs (DEBLOQUE / EN_REMBOURSEMENT / EN_RETARD). Ces totaux augmentent
     * à chaque mensualité réglée (capital_rembourse, interet_percu, commission_percu).
     * Filtre dynamique : devise, zone, agent, portefeuille, type de crédit, période.
     */
    public function enCours(Request $request)
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $perms = $user->getPermissionCodes();

        $superviseurPerms = ['EBEN-PER61', 'EBEN-PER62', 'EBEN-PER63', 'EBEN-PER64'];
        if (count(array_intersect($superviseurPerms, $perms)) === 0) {
            return redirect()
                ->route('credit.index')
                ->with('error', "Accès non autorisé au tableau de bord En Cours.");
        }

        $zonesCodes = $this->resolveZoneScope($user);

        $query = CreditDemande::query()
            ->whereIn('statut_global', ['DEBLOQUE', 'EN_REMBOURSEMENT', 'EN_RETARD'])
            ->when($zonesCodes !== null, fn ($q) => $q->whereIn('code_zone', $zonesCodes));

        // ── Filtre dynamique ──────────────────────────────────────────
        if ($request->filled('devise')) {
            $query->where('devise', $request->devise);
        }
        if ($request->filled('zone')) {
            $query->where('code_zone', $request->zone);
        }
        if ($request->filled('agent_analyse')) {
            $query->where('agent_analyse_matricule', $request->agent_analyse);
        }
        if ($request->filled('portefeuille_id')) {
            $query->where('portefeuille_id', $request->portefeuille_id);
        }
        if ($request->filled('type_credit')) {
            $query->where('type_credit', $request->type_credit);
        }
        if ($request->filled('statut')) {
            $query->where('statut_global', $request->statut);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_dossier', 'like', "%{$search}%")
                   ->orWhere('client_matricule', 'like', "%{$search}%")
                   ->orWhereHas('client', function ($cq) use ($search) {
                       $cq->searchFullName($search);
                   });
            });
        }
        // Période de déblocage
        if ($request->filled('date_debut') || $request->filled('date_fin')) {
            $query->whereHas('deblocage', function ($q) use ($request) {
                if ($request->filled('date_debut')) {
                    $q->whereDate('debloque_le', '>=', $request->date_debut);
                }
                if ($request->filled('date_fin')) {
                    $q->whereDate('debloque_le', '<=', $request->date_fin);
                }
            });
        }

        $dossiersIds = (clone $query)->pluck('id');

        // ── Capital décaissé (dette contractuelle du client) par devise ───
        // IMPORTANT : basé sur `montant_approuve` (le capital que le client
        // doit REELLEMENT rembourser selon son échéancier), PAS sur
        // `montant_net_verse` du déblocage. Ce dernier n'est que l'argent
        // physiquement remis en main (montant approuvé − 20% caution − 4%
        // frais de dossier) : la caution reste la propriété du client
        // (compte GTC, restituée/imputée au solde final) et les frais sont un
        // revenu de la coopec — ni l'un ni l'autre ne réduit la dette de
        // capital du client. Utiliser le net versé sous-évaluait fortement
        // "l'encours" (l'argent réellement chez les clients).
        $decaisseParDevise = (clone $query)
            ->selectRaw('devise, SUM(montant_approuve) as total')
            ->groupBy('devise')
            ->pluck('total', 'devise');

        // Net réellement remis en main (info complémentaire, sans impact sur l'encours)
        $netVerseParDevise = CreditDeblocage::whereIn('credit_demande_id', $dossiersIds)
            ->selectRaw('devise, SUM(montant_net_verse) as total')
            ->groupBy('devise')
            ->pluck('total', 'devise');

        // ── Capital remboursé / intérêt perçu / commission perçue par devise ──
        $rembParDevise = CreditRemboursement::whereIn('credit_demande_id', $dossiersIds)
            ->selectRaw('devise, SUM(dont_capital) as capital, SUM(dont_interet) as interet, SUM(dont_commission) as commission, SUM(dont_penalite) as penalite')
            ->groupBy('devise')
            ->get()
            ->keyBy('devise');

        // ── Nombre de dossiers par devise ──
        $dossiersParDevise = (clone $query)
            ->selectRaw('devise, COUNT(*) as nb, SUM(montant_approuve) as montant_approuve')
            ->groupBy('devise')
            ->get()
            ->keyBy('devise');

        $devises = collect($dossiersParDevise->keys())
            ->merge($decaisseParDevise->keys())
            ->unique()
            ->values();

        $totauxParDevise = $devises->mapWithKeys(function ($devise) use ($decaisseParDevise, $netVerseParDevise, $rembParDevise, $dossiersParDevise) {
            $decaisse   = (float) ($decaisseParDevise[$devise] ?? 0);
            $netVerse   = (float) ($netVerseParDevise[$devise] ?? 0);
            $capitalReg = (float) ($rembParDevise[$devise]->capital ?? 0);
            $interet    = (float) ($rembParDevise[$devise]->interet ?? 0);
            $commission = (float) ($rembParDevise[$devise]->commission ?? 0);
            $penalite   = (float) ($rembParDevise[$devise]->penalite ?? 0);

            return [$devise => [
                'nombre_dossiers'   => (int) ($dossiersParDevise[$devise]->nb ?? 0),
                'capital_decaisse'  => $decaisse,
                'net_verse'         => $netVerse,
                'capital_rembourse' => $capitalReg,
                'capital_restant'   => max(0, $decaisse - $capitalReg),
                'interet_percu'     => $interet,
                'commission_percu'  => $commission,
                'penalite_percue'   => $penalite,
                'total_percu'       => $capitalReg + $interet + $commission + $penalite,
            ]];
        });

        // ── Liste détaillée (filtrable) des dossiers en cours ──────────
        $dossiersEnCours = (clone $query)
            ->with(['client', 'zone', 'deblocage', 'remboursements'])
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        // Options pour les selects du filtre
        $zonesDisponibles = \App\Models\Zone::orderBy('nom')->get(['code_zone', 'nom']);
        $agentsAnalystes = CreditDemande::whereNotNull('agent_analyse_matricule')
            ->distinct()
            ->pluck('agent_analyse_matricule');

        return view('credit.en_cours', compact(
            'totauxParDevise', 'dossiersEnCours', 'zonesDisponibles', 'agentsAnalystes'
        ));
    }

    // ================================================================
    // LISTE DES DOSSIERS
    // ================================================================

    public function index(Request $request)
    {
        // Auto-réparation : recalcule EN_RETARD/EN_REMBOURSEMENT/SOLDE pour
        // TOUS les dossiers actifs avant de construire les compteurs/badges de
        // cette page. Ne dépend donc plus du bon fonctionnement du cron
        // planifié (credit:marquer-retards, cf. bootstrap/app.php) — utile
        // tant que la tâche planifiée Windows (schedule:run/minute) n'est pas
        // confirmée opérationnelle sur le serveur. Throttle 60s (cache) pour
        // ne pas relancer ce recalcul à chaque clic/filtre de la même minute.
        if (\Illuminate\Support\Facades\Cache::add('credit_sync_retards_lock', true, 60)) {
            \Illuminate\Support\Facades\Artisan::call('credit:marquer-retards');
        }

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
            // Supporte aussi une liste séparée par virgules (ex: ?statut=EN_REMBOURSEMENT,EN_RETARD
            // utilisé par le sous-menu "Remboursement" du menu Crédits).
            $statuts = array_filter(array_map('trim', explode(',', $request->statut)));
            $query->whereIn('statut_global', $statuts);
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
                       $cq->searchFullName($search);
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
        // Filtre rapide : en retard (échéances dépassées non payées) —
        // scopeEnRetardReel() = source unique, cf. CreditDemande.
        if ($request->get('alerte') === 'retard') {
            $query->enRetardReel();
        }
        if ($request->get('alerte') === 'alertes') {
            $query->whereIn('statut_global', ['SUSPECT','SUSPENDU']);
        }

        // ─ Totaux selon le filtre actif (pour l'affichage au-dessus du tableau) ──
        // Cloné AVANT paginate() : paginate() applique limit/offset au builder,
        // donc un clone fait après ne totaliserait que la page courante (20 lignes).
        $filteredQuery = clone $query;

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
            // enRetardReel (pas statut_global) : source unique, cf. CreditDemande::scopeEnRetardReel().
            // Corrige l'écart observé (sidebar "Recouvrement Auto" à 7 vs "En retard" ici à 6) :
            // statut_global peut être temporairement désynchronisé sur un dossier précis.
            'en_retard'  => (clone $queryBase)->enRetardReel()->count(),
            'soldes'     => (clone $queryBase)->where('statut_global', 'SOLDE')->count(),
            'alertes'    => (clone $queryBase)->whereIn('statut_global', ['SUSPECT','SUSPENDU'])->count(),
            'annules'    => (clone $queryBase)->where('statut_global', 'ANNULE')->count(),
        ];

        // ─ Totaux selon le filtre actif (pour l'affichage au-dessus du tableau) ──
        // Calcul séparé par devise pour éviter d'additionner CDF et USD
        $filtresActifs = $filteredQuery->with('deblocage')->get();

        $idsFiltres = $filtresActifs->pluck('id')->toArray();

        // enRetardReel (pas statut_global) : source unique, cf. CreditDemande::
        // scopeEnRetardReel(). $filtresActifs n'a pas les échéances chargées
        // (pas de whereHas possible en mémoire) : on récupère le set d'IDs
        // réellement en retard via une requête ciblée sur les IDs déjà filtrés,
        // puis on filtre la collection en mémoire avec whereIn('id', ...).
        $idsEnRetardReel = !empty($idsFiltres)
            ? CreditDemande::whereIn('id', $idsFiltres)->enRetardReel()->pluck('id')->all()
            : [];

        // Remboursements par devise — jamais de total CDF+USD mélangé
        $rembourseParDevise = !empty($idsFiltres)
            ? CreditRemboursement::whereIn('credit_demande_id', $idsFiltres)
                ->selectRaw('devise, SUM(montant_recu) as total')
                ->groupBy('devise')
                ->pluck('total', 'devise')
            : collect();

        // Totaux par devise
        $totauxParDevise = [];
        foreach ($filtresActifs->groupBy('devise') as $devise => $dossiersDevise) {
            $totauxParDevise[$devise] = [
                'count'             => $dossiersDevise->count(),
                'montant_demande'   => $dossiersDevise->sum('montant_demande'),
                'montant_approuve'  => $dossiersDevise->sum('montant_approuve'),
                // Lien réel : tb_credit_deblocages.credit_demande_id (la colonne
                // deblocage_id n'existe pas sur tb_credit_demandes).
                'montant_net_verse' => $dossiersDevise->sum(fn ($d) => (float) ($d->deblocage?->montant_net_verse ?? 0)),
                'montant_rembourse' => (float) ($rembourseParDevise[$devise] ?? 0),
                'en_retard'         => $dossiersDevise->whereIn('id', $idsEnRetardReel)->count(),
                'montant_en_retard' => $dossiersDevise->whereIn('id', $idsEnRetardReel)->sum('montant_demande'),
            ];
        }

        $totauxFiltres = [
            'count'             => count($idsFiltres),
            'montant_demande'   => $filtresActifs->sum('montant_demande'),
            'montant_approuve'  => $filtresActifs->sum('montant_approuve'),
            'montant_net_verse' => $filtresActifs->sum(fn ($d) => (float) ($d->deblocage?->montant_net_verse ?? 0)),
            'en_retard'         => $filtresActifs->whereIn('id', $idsEnRetardReel)->count(),
            'montant_en_retard' => $filtresActifs->whereIn('id', $idsEnRetardReel)->sum('montant_demande'),
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

        // Totaux par zone ET par devise (jamais de somme CDF+USD mélangée :
        // avant, une zone affichait un seul montant additionnant les deux
        // devises, ce qui donnait des totaux incohérents avec l'en-tête).
        $totauxParZone = [];
        foreach ($allEcheances->groupBy(fn($e) => $e->echeancier->demande->zone->nom ?? ($e->echeancier->demande->code_zone ?? 'Sans zone')) as $zoneNom => $group) {
            $parDevise = [];
            foreach ($group->groupBy(fn($e) => $e->echeancier->demande->devise ?? 'N/A') as $devise => $g) {
                $parDevise[$devise] = ['count' => $g->count(), 'reste_du' => $g->sum($resteDuFn)];
            }
            $totauxParZone[$zoneNom] = [
                'count'      => $group->count(),
                'par_devise' => $parDevise,
            ];
        }

        $totauxParPortefeuille = [];
        foreach ($allEcheances->groupBy(fn($e) => $e->echeancier->demande->portefeuille->nom_portefeuille ?? 'Sans portefeuille') as $pfNom => $group) {
            $parDevise = [];
            foreach ($group->groupBy(fn($e) => $e->echeancier->demande->devise ?? 'N/A') as $devise => $g) {
                $parDevise[$devise] = ['count' => $g->count(), 'reste_du' => $g->sum($resteDuFn)];
            }
            $totauxParPortefeuille[$pfNom] = [
                'count'      => $group->count(),
                'par_devise' => $parDevise,
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
        // PERFORMANCE (21/09/2026) : plus de chargement des 2 200+ clients en
        // <option> (page très lourde) — le select client est en recherche AJAX
        // (credit.clients.search). Seul le client déjà choisi (préselection ou
        // ancienne saisie après erreur de validation) est rendu côté serveur.
        $selectedClientMatricule = (string) (old('client_matricule') ?? $request->query('client_matricule') ?? '');
        $selectedClient = $selectedClientMatricule !== ''
            ? Client::where('matricule', $selectedClientMatricule)->first()
            : null;
        $zones = Zone::orderBy('nom')->get();
        $portefeuillesDisponibles = $user
            ? $this->resolveCreationPortefeuilleOptions($user)
            : collect();

        return view('credit.creation', compact('zones', 'selectedClientMatricule', 'selectedClient', 'portefeuillesDisponibles'));
    }

    /**
     * GET AJAX : recherche de clients pour les selects d'autocomplétion
     * (création / modification de demande, import d'ancien dossier).
     * Remplace le rendu serveur des 2 200+ <option> (page lourde).
     */
    public function searchClientAjax(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $clients = Client::query()
            ->where(function ($query) use ($q) {
                $query->searchFullName($q)
                    ->orWhere('matricule', 'like', "%{$q}%")
                    ->orWhere('telephone', 'like', "%{$q}%");
            })
            ->orderBy('nom')->orderBy('postnom')->orderBy('prenom')
            ->limit(10)
            ->get()
            ->map(fn ($c) => [
                'matricule' => $c->matricule,
                'full_name' => $c->full_name,
                'nom'       => $c->full_name,           // nom + postnom + prénom (affichage modal)
                'prenom'    => $c->prenom ?? '',
                'telephone' => $c->telephone ?? '',
                'sexe'      => $c->sexe ?? '',
                'photo'     => $c->photo ? basename($c->photo) : '',
            ]);

        return response()->json($clients);
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
            // Le portefeuille n'est plus obligatoire à la création : il ne devient
            // pertinent (et requis) que lors de l'affectation d'un agent de crédit
            // au dossier (voir affecterAnalyse()), qui détermine automatiquement
            // le bon portefeuille selon l'agent choisi.
            'portefeuille_id'      => 'nullable|integer|exists:tb_portefeuilles_agents,id',
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

        $portefeuilleIdCreation = !empty($validated['portefeuille_id']) ? (int) $validated['portefeuille_id'] : null;

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

        try {
        $demande = DB::transaction(function () use ($validated, $client, $agent, $calcul, $portefeuilleIdCreation, $commissionTotale) {
            $demande = CreditDemande::creerAvecNumeroUnique([
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
        } catch (\Throwable $e) {
            return back()->withErrors(['numero_dossier' => "Erreur lors de la création du dossier : " . $e->getMessage()])->withInput();
        }

        // Rester sur le formulaire de demande (pas credit.show : une personne
        // qui crée des dossiers (PER54) n'a pas forcément PER57 « Voir détail »
        // et tombait sinon sur une page « pas d'autorisation » après l'envoi).
        // Le modal de succès s'affiche sur le formulaire (cf. credit.creation).
        return redirect()->route('credit.create')
            ->with('success', "Dossier {$demande->numero_dossier} créé avec succès.");
    }

    // ================================================================
    // IMPORT D'UN ANCIEN DOSSIER (historique, permission dédiée EBEN-PER127)
    // ================================================================

    public function importAncien(Request $request)
    {
        // PERFORMANCE (21/09/2026) : plus de chargement des 2 200+ clients —
        // select client en recherche AJAX (credit.clients.search).
        $selectedClientMatricule = (string) (old('client_matricule') ?? $request->query('client_matricule') ?? '');
        $selectedClient = $selectedClientMatricule !== ''
            ? Client::where('matricule', $selectedClientMatricule)->first()
            : null;
        $agentsAnalyse = $this->resolveAssignableCreditAgents();
        $agentsTous = Agent::orderBy('nom')->orderBy('postnom')->orderBy('prenom')->get(['matricule', 'nom', 'postnom', 'prenom']);
        $portefeuilles = Portefeuille::orderBy('nom_portefeuille')->get(['id', 'nom_portefeuille']);

        return view('credit.import_ancien', compact(
            'selectedClient', 'selectedClientMatricule', 'agentsAnalyse', 'agentsTous', 'portefeuilles'
        ));
    }

    public function storeImportAncien(Request $request)
    {
        $validated = $request->validate([
            'client_matricule'            => 'required|string|exists:tb_clients,matricule',
            'type_credit'                 => 'required|in:INDIVIDUEL,SOLIDAIRE,PME',
            'montant_demande'             => 'required|numeric|min:1',
            'devise'                      => 'required|in:CDF,USD,EUR',
            'duree_mois'                  => 'required|integer|min:1|max:360',
            'taux_interet_mensuel'        => 'required|numeric|min:0.01|max:100',
            'objet_credit'                => 'required|string|max:500',
            'garantie_description'        => 'nullable|string',
            'numero_dossier'              => 'nullable|string|max:30',
            'agent_matricule'             => 'nullable|string|exists:tb_agents,matricule',
            'agent_analyse_matricule'     => 'required|string|exists:tb_agents,matricule',
            'portefeuille_id'             => 'nullable|integer',
            'date_creation'               => 'required|date',
            'date_deblocage'              => 'required|date',
            'date_premier_remboursement'  => 'required|date|after_or_equal:date_deblocage',
            'montant_debloque'            => 'required|numeric|min:1',
            'pourcentage_caution'         => 'nullable|numeric|min:0|max:100',
            'pourcentage_frais_dossier'   => 'nullable|numeric|min:0|max:100',
            'pourcentage_frais_etude'     => 'nullable|numeric|min:0|max:100',
        ]);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $agent = $user?->agent;

        $client = Client::findOrFail($validated['client_matricule']);

        if (!$this->isEligibleCreditAnalyst($validated['agent_analyse_matricule'])) {
            return back()->withErrors(["L'agent sélectionné n'a pas le profil « Analyse crédit »."])->withInput();
        }

        $commissionService = app(\App\Services\Credit\CreditCommissionService::class);
        $commissionTotale = $commissionService->calculateForContext([
            'devise'          => $validated['devise'],
            'type_credit'     => $validated['type_credit'],
            'code_zone'       => $client->code_zone,
            'portefeuille_id' => null,
            'montant'         => (float) $validated['montant_demande'],
        ]);

        $dateCreation  = Carbon::parse($validated['date_creation']);
        $dateDeblocage = Carbon::parse($validated['date_deblocage']);
        $datePremier   = Carbon::parse($validated['date_premier_remboursement']);

        $agentMatricule = $validated['agent_matricule'] ?? $agent?->matricule ?? 'SYSTEM';
        $agentAnalyseMatricule = $validated['agent_analyse_matricule'];

        // Retenues au déblocage : par défaut 20% caution / 1% frais dossier /
        // 3% frais d'étude, mais modifiables par dossier (agences différentes).
        $pctCaution      = $validated['pourcentage_caution']       ?? 20.00;
        $pctFraisDossier = $validated['pourcentage_frais_dossier'] ?? 1.00;
        $pctFraisEtude   = $validated['pourcentage_frais_etude']   ?? 3.00;

        $portefeuilleId = null;
        if (!empty($validated['portefeuille_id'])) {
            $portefeuilleId = (int) $validated['portefeuille_id'];
        } else {
            $portefeuilleIds = $this->resolveAgentPortefeuilleIds($agentAnalyseMatricule);
            if (count($portefeuilleIds) === 1) {
                $portefeuilleId = (int) $portefeuilleIds[0];
            }
        }

        try {
        $demande = DB::transaction(function () use (
            $validated, $client, $agentMatricule, $agentAnalyseMatricule, $portefeuilleId,
            $commissionTotale, $dateCreation, $dateDeblocage, $datePremier,
            $pctCaution, $pctFraisDossier, $pctFraisEtude
        ) {
            // 1. Comptes RMB + GTC créés automatiquement si absents
            $compteRmb = Compte::firstOrCreate(
                [
                    'client_matricule' => $client->matricule,
                    'type'            => 'RMB',
                    'devise'          => $validated['devise'],
                ],
                [
                    'solde_reel'     => 0,
                    'solde_bloque'   => 0,
                    'portefeuille_id' => $portefeuilleId,
                ]
            );
            $compteGtc = Compte::firstOrCreate(
                [
                    'client_matricule' => $client->matricule,
                    'type'            => 'GTC',
                    'devise'          => $validated['devise'],
                ],
                [
                    'solde_reel'     => 0,
                    'solde_bloque'   => 0,
                    'portefeuille_id' => $portefeuilleId,
                ]
            );

            // 2. Dossier (déjà débloqué historiquement, agent + portefeuille
            //    rattachés comme si l'affectation d'analyse avait été faite normalement)
            $demande = CreditDemande::creerAvecNumeroUnique([
                'numero_dossier'           => $validated['numero_dossier'] ?: null,
                'client_matricule'         => $client->matricule,
                'compte_id'                => $compteRmb->code_compte,
                'portefeuille_id'          => $portefeuilleId,
                'code_zone'                => $client->code_zone,
                'agent_createur_matricule' => $agentMatricule,
                'agent_analyse_matricule'  => $agentAnalyseMatricule,
                'montant_demande'          => $validated['montant_demande'],
                'montant_approuve'         => $validated['montant_debloque'],
                'devise'                   => $validated['devise'],
                'duree_mois'               => $validated['duree_mois'],
                'taux_interet_mensuel'      => $validated['taux_interet_mensuel'],
                'type_credit'              => $validated['type_credit'],
                'objet_credit'             => $validated['objet_credit'],
                'garantie_description'      => $validated['garantie_description'] ?? null,
                'commission_totale'        => $commissionTotale,
                'pourcentage_caution'       => $pctCaution,
                'pourcentage_frais_dossier' => $pctFraisDossier,
                'pourcentage_frais_etude'   => $pctFraisEtude,
                'statut_global'            => 'DEBLOQUE',
                'soumis_le'                => $dateCreation,
                'created_at'               => $dateCreation,
                'updated_at'               => $dateCreation,
            ]);

            // 2bis. Analyse crédit (auto, comme si complétée par l'agent d'analyse)
            CreditAnalyse::create([
                'credit_demande_id'       => $demande->id,
                'analyseur_matricule'     => $agentAnalyseMatricule,
                'capacite_remboursement'  => round(((float) $validated['montant_demande']) / max((int) $validated['duree_mois'], 1), 2),
                'ratio_endettement'       => 0,
                'score_risque'            => 'FAIBLE',
                'historique_credit'       => 'Ancien dossier importé (historique)',
                'garanties_evaluees'      => $validated['garantie_description'] ?? 'Néant',
                'observations'            => "Analyse générée automatiquement lors de l'import du dossier historique.",
                'recommandation'          => 'FAVORABLE',
                'montant_recommande'      => $validated['montant_demande'],
                'statut'                  => 'COMPLETE',
                'complete_le'             => $dateCreation,
            ]);

            // 2ter. 4 blocs de validation, tous APPROUVE (import = dossier déjà traité).
            // montant_valide = montant réellement débloqué (pas le montant demandé) :
            // c'est ce montant qui pilote ensuite l'échéancier.
            $blocs = [
                ['type_validateur' => 'AGENT_CREDIT',      'ordre_etape' => 1],
                ['type_validateur' => 'CONTROLEUR',        'ordre_etape' => 2],
                ['type_validateur' => 'CHARGE_OPERATIONS', 'ordre_etape' => 3],
                ['type_validateur' => 'GERANT',            'ordre_etape' => 4],
            ];
            foreach ($blocs as $b) {
                CreditValidation::create(array_merge($b, [
                    'credit_demande_id'    => $demande->id,
                    'validateur_matricule' => $agentAnalyseMatricule,
                    'decision'             => 'APPROUVE',
                    'montant_valide'       => $validated['montant_debloque'],
                    'duree_mois_validee'   => $validated['duree_mois'],
                    'observations'         => 'Approuvé automatiquement (import dossier historique).',
                    'etape_precedente_ok'  => true,
                    'valide_le'            => $dateCreation,
                ]));
            }

            // 3. Déblocage historique (retenues configurables : caution + frais dossier + frais étude)
            $montantBrut = round((float) $validated['montant_debloque'], 2);
            $caution         = round($montantBrut * $pctCaution / 100, 2);
            $fraisDossierMnt = round($montantBrut * $pctFraisDossier / 100, 2);
            $fraisEtudeMnt   = round($montantBrut * $pctFraisEtude / 100, 2);
            $netVerse        = round($montantBrut - $caution - $fraisDossierMnt - $fraisEtudeMnt, 2);

            // La caution est bloquée sur le compte GTC du client, comme lors
            // d'un déblocage normal (transfert RMB -> GTC).
            $compteGtc->increment('solde_reel', $caution);
            $compteGtc->increment('solde_bloque', $caution);

            CreditDeblocage::create([
                'credit_demande_id'   => $demande->id,
                'agent_matricule'     => $agentMatricule,
                'compte_debit_id'     => 'IMPORT-HISTORIQUE',
                'guichet_solde_id'    => null,
                'compte_credit_id'    => $compteRmb->code_compte,
                'montant_debloque'    => $montantBrut,
                'montant_caution'     => $caution,
                'devise'              => $validated['devise'],
                'frais_dossier'       => $fraisDossierMnt,
                'frais_etude'         => $fraisEtudeMnt,
                'montant_net_verse'   => $netVerse,
                'reference_transaction' => 'IMP-' . $demande->numero_dossier,
                'numero_ordre'        => 'IMP-' . $demande->numero_dossier,
                'observations'         => 'Import dossier historique (date déblocage ' . $dateDeblocage->format('d/m/Y') . ')',
                'debloque_le'          => $dateDeblocage,
            ]);

            // 4. Échéancier généré à partir de la date de déblocage
            $echeancier = $this->amortissement->genererEtSauvegarder($demande, $datePremier);

            // 5. Les échéances passées sont marquées EN_RETARD jusqu'à aujourd'hui
            $today = Carbon::today();
            $hasRetard = false;
            foreach ($echeancier->echeances as $echeance) {
                if (Carbon::parse($echeance->date_echeance)->lt($today)) {
                    $echeance->update(['statut' => 'EN_RETARD']);
                    $hasRetard = true;
                }
            }

            $demande->update([
                'statut_global' => $hasRetard ? 'EN_RETARD' : 'EN_REMBOURSEMENT',
            ]);

            $this->logAudit($demande, 'IMPORT_HISTORIQUE', null, $demande->statut_global,
                "Import ancien dossier : débloqué le {$dateDeblocage->format('d/m/Y')}, caution {$pctCaution}%, frais dossier {$pctFraisDossier}%, frais étude {$pctFraisEtude}%.");

            return $demande;
        });
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            if (str_contains($e->getMessage(), 'numero_dossier')) {
                return back()->withErrors([
                    'numero_dossier' => "Le numéro de dossier fourni est déjà utilisé par un autre dossier. Laissez le champ vide pour une génération automatique, ou choisissez un autre numéro.",
                ])->withInput();
            }
            return back()->withErrors(['numero_dossier' => "Erreur d'import : " . $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            return back()->withErrors(['numero_dossier' => "Erreur lors de l'import du dossier : " . $e->getMessage()])->withInput();
        }

        // Rester sur le formulaire d'import (PER127), pas credit.show (PER57)
        return redirect()->route('credit.import_ancien')
            ->with('success', "Ancien dossier {$demande->numero_dossier} importé (déblocage au {$dateDeblocage->format('d/m/Y')}, échéancier généré).");
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

        // PERFORMANCE (21/09/2026) : plus de chargement des 2 200+ clients —
        // select client en recherche AJAX (credit.clients.search) ; seul le
        // client du dossier (ou l'ancienne saisie) est rendu côté serveur.
        $selectedClientMatricule = (string) (old('client_matricule') ?? $dossier->client_matricule ?? '');
        $selectedClient = $selectedClientMatricule !== ''
            ? (Client::where('matricule', $selectedClientMatricule)->first() ?? $dossier->client)
            : $dossier->client;
        $zones   = Zone::orderBy('nom')->get();
        $portefeuillesDisponibles = $user
            ? $this->resolveCreationPortefeuilleOptions($user)
            : collect();

        return view('credit.edit', compact('dossier', 'selectedClient', 'zones', 'portefeuillesDisponibles'));
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
            // Portefeuille optionnel ici aussi (voir store()) : il n'est requis
            // qu'au moment de l'affectation d'un agent de crédit.
            'portefeuille_id'      => 'nullable|integer|exists:tb_portefeuilles_agents,id',
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

        $portefeuilleId = !empty($validated['portefeuille_id']) ? (int) $validated['portefeuille_id'] : null;

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

        // Rester sur le formulaire d'édition (PER55), pas credit.show (PER57)
        return redirect()->route('credit.edit', $dossier)
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

        // Rafraîchissement dynamique : recalcule EN_RETARD/EN_REMBOURSEMENT/SOLDE
        // à partir des échéances RÉELLES, à CHAQUE affichage — pas seulement si
        // une NOUVELLE échéance vient de passer en retard (bug historique :
        // l'ancienne logique ne se déclenchait jamais quand un paiement RÉGLAIT
        // une échéance en retard sans qu'aucune nouvelle échéance ne devienne en
        // retard au même moment, laissant `statut_global` bloqué sur EN_RETARD
        // — d'où l'incohérence avec la liste des dossiers). Cf. CreditDemande::
        // refreshStatutRetard(), source unique de cette logique dans tout le module.
        if ($dossier->refreshStatutRetard()) {
            $dossier->refresh();
            $dossier->load(['echeancier.echeances', 'client']);
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

        // ── Taux de frais de déblocage — ajustables par l'agent avant validation ──
        // Valeurs par défaut : 20% caution, 3% frais d'étude, 1% frais de dossier.
        $tauxCaution      = (float) old('taux_caution', 20);
        $tauxFraisEtude   = (float) old('taux_frais_etude', 3);
        $tauxFraisDossier = (float) old('taux_frais_dossier', 1);
        $tauxFraisTotal   = round($tauxFraisEtude + $tauxFraisDossier, 2);

        // ── Répartition automatique du montant approuvé ──────────────────
        $montantTotal = (float) $dossier->montant_approuve;
        $caution      = round($montantTotal * $tauxCaution / 100, 2);
        $fraisDossier = round($montantTotal * $tauxFraisDossier / 100, 2);
        $fraisEtude   = round($montantTotal * $tauxFraisEtude / 100, 2);
        $fraisTotal   = round($fraisDossier + $fraisEtude, 2);
        $netVerse     = round($montantTotal - $caution - $fraisTotal, 2);

        // ── Précondition RMB : caution + frais (défaut 24% = 20% caution + 4% frais) ─────
        $provisionRmbMin = round($montantTotal * ($tauxCaution + $tauxFraisTotal) / 100, 2);

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
            'tauxCaution', 'tauxFraisEtude', 'tauxFraisDossier', 'tauxFraisTotal',
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
            'taux_caution'              => 'nullable|numeric|min:0|max:100',
            'taux_frais_dossier'        => 'nullable|numeric|min:0|max:100',
            'taux_frais_etude'          => 'nullable|numeric|min:0|max:100',
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

        // Taux de frais de déblocage — ajustables par l'agent, défaut 20%/3%/1%.
        $tauxCaution      = isset($validated['taux_caution']) ? (float) $validated['taux_caution'] : 20.0;
        $tauxFraisEtude   = isset($validated['taux_frais_etude']) ? (float) $validated['taux_frais_etude'] : 3.0;
        $tauxFraisDossier = isset($validated['taux_frais_dossier']) ? (float) $validated['taux_frais_dossier'] : 1.0;
        $tauxFraisTotal   = round($tauxFraisEtude + $tauxFraisDossier, 2);

        $coffreSolde = CaissesGuichetSolde::findOrFail($validated['coffre_solde_id']);

        $alreadyDebloque = false;
        $deblocageRefs = [];

        DB::transaction(function () use ($dossier, $validated, $agentMatricule, $montant, $coffreSolde, $tauxCaution, $tauxFraisTotal, &$alreadyDebloque, &$deblocageRefs) {
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
            $caution     = round($montantBrut * $tauxCaution / 100, 2);
            $fraisReel   = round($montantBrut * $tauxFraisTotal / 100, 2);
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
                'observations'            => 'Transfert caution GTC credit ' . $dossier->numero_dossier . ' (' . $tauxCaution . '% RMB -> GTC bloque)',
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
                'observations'            => 'Depot caution GTC credit ' . $dossier->numero_dossier . ' (' . $tauxCaution . '% bloque)',
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
                'observations'            => 'Frais deblocage credit ' . $dossier->numero_dossier . ' (' . $tauxFraisTotal . '% non remboursables)',
                'statut'                  => Transaction::CONFIRME,
                'date_operation'          => Carbon::parse($validated['date_deblocage']),
            ]);

            // 9. Caution (20%) et frais (4%) : mouvements PUREMENT comptables
            // entre comptes internes du client (RMB -> GTC, RMB -> résultat).
            // BUG CORRIGÉ : ce code créditait auparavant le coffre central
            // physique (`solde_en_caisse`) de ces 24%, alors qu'aucun cash
            // réel n'entre en caisse à cette étape (le montant débloqué
            // n'est lui-même qu'une écriture RMB créée pour le crédit, pas
            // un dépôt d'espèces). Cela gonflait artificiellement le solde
            // comptable du coffre par rapport au solde physique réel à
            // chaque déblocage (écart détecté aux clôtures de caisse).
            // Le seul argent réellement décaissé en espèces (le "net versé"
            // remis au client) transitera par un RETRAIT classique au
            // guichet le jour où le client le retire, qui décrémente déjà
            // correctement `solde_en_caisse` à ce moment-là — aucune
            // écriture caisse physique supplémentaire n'est donc nécessaire
            // ici.
            $coffreGeneral = CaissesGuichet::central()->first();
            $coffreGeneralId = $coffreGeneral?->id ?? $coffreSolde->guichet_id;

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
            ->with('success', "Deblocage de {$dossier->numero_dossier} effectue (100% en RMB, transfert {$tauxCaution}% vers GTC, frais {$tauxFraisTotal}% non remboursables).")
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

    /**
     * Active/désactive le consentement client au prélèvement automatique
     * (Recouvrement Auto) — nécessite EBEN-PER113, distinct de la simple
     * consultation. Un dossier non autorisé n'est jamais touché par
     * RecouvrementController::runAutoCollection(), même s'il a un solde RMB
     * suffisant et des échéances en retard (le client doit avoir donné son
     * accord explicite pour qu'on prélève automatiquement sur son épargne).
     */
    public function togglePrelevementAuto(CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        $nouvelEtat = !$dossier->prelevement_auto_autorise;
        $dossier->update(['prelevement_auto_autorise' => $nouvelEtat]);

        $this->logAudit(
            $dossier,
            'MODIFICATION',
            null,
            $dossier->statut_global,
            $nouvelEtat
                ? 'Prélèvement automatique AUTORISÉ par ' . (Auth::user()->agent?->matricule ?? Auth::user()->name)
                : 'Prélèvement automatique RÉVOQUÉ par ' . (Auth::user()->agent?->matricule ?? Auth::user()->name)
        );

        return back()->with('success', $nouvelEtat
            ? "Prélèvement automatique autorisé pour le dossier {$dossier->numero_dossier}."
            : "Prélèvement automatique désactivé pour le dossier {$dossier->numero_dossier}."
        );
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

        // PARTIELLEMENT_PAYE inclus : sinon une échéance en retard réglée
        // partiellement était ignorée ici, faisant sauter à tort la "prochaine
        // échéance" affichée vers l'échéance suivante (non encore due).
        $prochaineEcheance = $dossier->echeancier?->echeances()
            ->whereIn('statut', ['EN_ATTENTE','EN_RETARD','PARTIELLEMENT_PAYE'])
            ->orderBy('numero_echeance')
            ->first();

        $demande = $dossier;
        $echeancier = $dossier->echeancier;
        $comptesInstitution = collect();

        // Cette page règle les échéances UNIQUEMENT à partir du solde RMB déjà
        // déposé par le client (aucun argent liquide n'est encaissé ici) — un
        // guichet fixe n'est donc plus requis pour y accéder ni pour l'utiliser.
        $user = Auth::user();
        $guichet = null;

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

        // EBEN-PER128 : autorisation dédiée pour accorder la remise commerciale
        // du solde par anticipation (distincte du remboursement classique).
        $peutSolderAnticipe = $user->hasPermission('EBEN-PER128');

        return view('credit.remboursement', compact(
            'dossier', 'demande', 'prochaineEcheance', 'echeancier', 
            'comptesInstitution', 'guichet', 'soldeRmbActuel', 'echeancesImpayees',
            'peutSolderAnticipe'
        ));
    }

    public function storeRemboursement(Request $request, CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        $validated = $request->validate([
            'echeance_id'         => 'nullable|integer|exists:tb_credit_echeances,id',
            // 0 autorisé : règlement 100% depuis le solde RMB, sans argent liquide encaissé.
            'montant_recu'        => 'required|numeric|min:0',
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

        // Le solde par anticipation accorde une remise commerciale (50% sur
        // l'intérêt restant si >3 échéances dues) — réservé à EBEN-PER128,
        // distinct du remboursement classique (EBEN-PER10|EBEN-PER111) que
        // tout caissier possède déjà.
        if ($validated['type_remboursement'] === 'ANTICIPE' && !$user->hasPermission('EBEN-PER128')) {
            abort(403, "Vous n'avez pas l'autorisation d'accorder un solde par anticipation (remise d'intérêt).");
        }

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

            // ── 1bis. Remboursement ANTICIPE : remise de 50% sur l'intérêt restant, ──
            // MAIS uniquement si le client règle la totalité des échéances encore dues
            // ET qu'il reste STRICTEMENT PLUS DE 3 échéances impayées avant ce règlement.
            // S'il ne reste que 3 échéances ou moins, aucune remise : le client paie le
            // solde normalement (trop proche de l'échéance finale pour justifier un geste
            // commercial sur les intérêts).
            if ($validated['type_remboursement'] === 'ANTICIPE') {
                $echeancesRestantes = $echeances->filter(
                    fn ($e, $idx) => $idx >= $startIndex && $e->statut !== 'PAYE'
                )->values();

                if ($echeancesRestantes->count() > 3) {
                    $totalRestantAvantRemise = 0;

                    foreach ($echeancesRestantes as $ech) {
                        $totalDuOrig  = round((float) $ech->total_echeance, 2);
                        $capitalEch   = round((float) $ech->capital_echeance, 2);
                        $interetEch   = round((float) $ech->interet_echeance, 2);
                        $commEch      = round((float) $ech->commission_echeance, 2);
                        $dejaPayeOrig = round((float) $ech->montant_paye, 2);

                        // Répartition proportionnelle du déjà-payé (même logique que la boucle principale)
                        $interetDejaPaye = round($dejaPayeOrig * ($interetEch / max($totalDuOrig, 1)), 2);
                        $interetRestant  = max(0, $interetEch - $interetDejaPaye);

                        // Remise de 50% sur l'intérêt qui reste encore à percevoir
                        $nouvelInteret = round($interetEch - round($interetRestant / 2, 2), 2);
                        $nouveauTotal  = round($capitalEch + $nouvelInteret + $commEch, 2);

                        $ech->update([
                            'interet_echeance' => $nouvelInteret,
                            'total_echeance'   => $nouveauTotal,
                        ]);
                        $ech->refresh();

                        $totalRestantAvantRemise += round($nouveauTotal - $dejaPayeOrig, 2);
                    }

                    if ($montantAAppliquer + 0.01 < $totalRestantAvantRemise) {
                        throw new \Exception(
                            "Remboursement anticipé incomplet : le solde restant après remise (50% sur l'intérêt) est de "
                            . number_format($totalRestantAvantRemise, 2, ',', ' ') . ' ' . $dossier->devise
                            . '. Le montant fourni ne couvre pas ce solde total — la remise n\'est accordée que pour un règlement intégral.'
                        );
                    }

                    // Les échéances ont été modifiées en mémoire (refresh) : on les remet dans la
                    // collection utilisée par la boucle de règlement ci-dessous.
                    foreach ($echeancesRestantes as $echModifiee) {
                        $echeances[$echeances->search(fn ($e) => $e->id === $echModifiee->id)] = $echModifiee;
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

            if ($totalDisponible <= 0) {
                throw new \Exception('Aucun montant disponible à appliquer (solde RMB insuffisant et aucun montant reçu en espèces).');
            }

            $surplus = $totalDisponible;
            $echeanceTraitee = null;
            $montantTotalApplique = 0;
            $totalCapitalPaye = 0;
            $totalInteretPaye = 0;
            $totalCommissionPaye = 0;
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

                // Calculer la répartition capital/intérêt/commission pour CETTE échéance
                // Ordre d'imputation (bonne pratique microfinance) : intérêt d'abord,
                // puis commission, puis capital en dernier.
                $capitalEcheance    = round((float) $ech->capital_echeance, 2);
                $interetEcheance    = round((float) $ech->interet_echeance, 2);
                $commissionEcheance = round((float) $ech->commission_echeance, 2);
                $capitalDejaPaye    = round((float) ($ech->montant_paye ?? 0) * ($capitalEcheance / max($totalDu, 1)), 2);
                $interetDejaPaye    = round((float) ($ech->montant_paye ?? 0) * ($interetEcheance / max($totalDu, 1)), 2);
                $commissionDejaPaye = round((float) ($ech->montant_paye ?? 0) * ($commissionEcheance / max($totalDu, 1)), 2);
                
                $capitalRestant    = max(0, $capitalEcheance - $capitalDejaPaye);
                $interetRestant    = max(0, $interetEcheance - $interetDejaPaye);
                $commissionRestant = max(0, $commissionEcheance - $commissionDejaPaye);
                
                // Répartir le montant appliqué : intérêt d'abord, puis commission, puis capital
                $dontInteretThis = min($montantApplique, $interetRestant);
                $resteApresInteret = round($montantApplique - $dontInteretThis, 2);
                $dontCommissionThis = min($resteApresInteret, $commissionRestant);
                $dontCapitalThis = round($resteApresInteret - $dontCommissionThis, 2);

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
                    'dont_commission'    => $dontCommissionThis,
                    'dont_penalite'      => 0,
                    'devise'             => $dossier->devise,
                    'type_remboursement' => $nouveauStatut === 'PAYE' ? 'ECHEANCE' : 'PARTIEL',
                    'reference_caisse'   => $reference ?? null,
                    'observations'       => sprintf(
                        'Remboursement éch. #%s – Capital: %s, Intérêt: %s, Commission: %s',
                        $ech->numero_echeance,
                        number_format($dontCapitalThis, 2),
                        number_format($dontInteretThis, 2),
                        number_format($dontCommissionThis, 2)
                    ),
                    'recu_le'            => $datePaiement,
                    'transaction_id'     => null, // Sera mis à jour après création de la transaction
                ]);

                $totalCapitalPaye += $dontCapitalThis;
                $totalInteretPaye += $dontInteretThis;
                $totalCommissionPaye += $dontCommissionThis;

                $echeanceTraitee = $ech;
                $surplus = round($surplus - $montantApplique, 2);
                $montantTotalApplique += $montantApplique;
            }

            // ── 3. Comptabilisation Caisse et Compte Client ──────────────
            // Un guichet n'est requis QUE si de l'argent liquide est réellement encaissé
            // ($montantRecu > 0). Un règlement 100% depuis le solde RMB déjà déposé ne
            // touche jamais la caisse et ne nécessite donc aucun guichet fixe.
            $guichet = null;
            $soldeGuichet = null;
            if ($montantRecu > 0) {
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
            $soldeGuichetAvant = $soldeGuichet ? (float) $soldeGuichet->solde_en_caisse : null;

            if ($montantPreleveSurRmb > 0) {
                $compteRmb->decrement('solde_reel', $montantPreleveSurRmb);
            } elseif ($montantDeposeSurRmb > 0) {
                $compteRmb->increment('solde_reel', $montantDeposeSurRmb);
            }
            
            // La caisse du guichet ne reçoit quelque chose QUE si de l'argent liquide a été encaissé
            if ($soldeGuichet && $montantRecu > 0) {
                $soldeGuichet->increment('solde_en_caisse', $montantRecu);
            }
            
            $soldeRmbApres = (float) $compteRmb->solde_reel;
            $soldeGuichetApres = $soldeGuichet ? round($soldeGuichetAvant + $montantRecu, 2) : null;

            $reference = $validated['reference_caisse'] ?? 'REM-EBEN-' . $dossier->id . '-' . now()->format('His') . rand(10, 99);

            // Montant significatif de la transaction : le cash encaissé s'il y en a,
            // sinon le montant réellement prélevé sur le solde RMB (règlement 100% RMB,
            // pour éviter une transaction "fantôme" à 0 dans l'historique/les rapports).
            $montantTransaction = $montantRecu > 0 ? $montantRecu : $montantPreleveSurRmb;

            // 3. Enregistrer la transaction comme un DÉPÔT (pour qu'elle figure dans les entrées de caisse du rapport)
            $transaction = Transaction::create([
                'compte_code'             => $compteRmb->code_compte,
                'agent_matricule'         => $agentMatricule,
                'guichet_id'              => $guichet?->id,
                'devise_code'             => $dossier->devise,
                'type'                    => Transaction::REMBOURSEMENT,
                'montant'                 => $montantTransaction,
                'montant_commission_total'=> 0,
                'solde_compte_avant'      => $soldeRmbAvant,
                'solde_compte_apres'      => $soldeRmbApres,
                'montant_total_client'    => $montantTransaction,
                'montant_net_client'      => $montantTransaction,
                'reference'               => $reference,
                'observations'            => sprintf(
                    'Remboursement crédit %s – Capital total: %s, Intérêt total: %s, Commission totale: %s%s',
                    $dossier->numero_dossier,
                    number_format($totalCapitalPaye, 2),
                    number_format($totalInteretPaye, 2),
                    number_format($totalCommissionPaye, 2),
                    $montantRecu <= 0 ? ' (100% depuis le solde RMB, aucun argent liquide encaissé)' : ''
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

                            // BUG CORRIGÉ : ce bloc décrémentait auparavant le
                            // coffre central physique (`solde_en_caisse`) de
                            // la caution restituée, en miroir du crédit
                            // fictif fait au déblocage (voir storeDeblocage).
                            // Comme cette caution n'a jamais transité par la
                            // caisse physique (transfert 100% interne RMB ->
                            // GTC -> RMB), aucune écriture caisse physique
                            // n'est nécessaire ici non plus.
                        }
                    }
                }
            } else {
                // Dossier PAS encore entièrement soldé : si l'échéance qui
                // vient d'être réglée était celle qui mettait le dossier
                // EN_RETARD, il faut le repasser à EN_REMBOURSEMENT — sans
                // ça, `statut_global` reste bloqué sur EN_RETARD malgré le
                // paiement (bug historique : la liste des dossiers et le
                // badge "en retard" continuaient de compter ce dossier).
                $dossier->refresh();
                $dossier->load('echeancier.echeances');
                $dossier->refreshStatutRetard();
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
            $mouvements[] = ['date' => $remb->date_paiement ?? now(), 'libelle' => 'Remboursement echeance #' . ($remb->echeance?->numero_echeance ?? '?') . ' (Cap: ' . number_format($remb->dont_capital, 2, ',', ' ') . ' | Int: ' . number_format($remb->dont_interet, 2, ',', ' ') . ' | Com: ' . number_format($remb->dont_commission, 2, ',', ' ') . ')', 'debit' => 0, 'credit' => $remb->montant_recu, 'type' => 'remboursement'];
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
        $totalCommissionPayee = $remboursements->sum('dont_commission');
        $echeancesRestantes = $dossier->echeancier?->echeances()->where('statut', 'EN_ATTENTE')->orderBy('numero_echeance')->get() ?? collect();
        $prochaineEcheance = $echeancesRestantes->first();
        $pdf = Pdf::loadView('impressions.credit.releve_credit', compact('dossier', 'client', 'clientFullName', 'clientPhotoBase64', 'mouvements', 'soldeOuverture', 'soldeCloture', 'totalDebits', 'totalCredits', 'capitalRestant', 'cautionBloquee', 'totalInteretsPayes', 'totalCapitalPaye', 'totalCommissionPayee', 'echeancesRestantes', 'prochaineEcheance'))->setPaper('A4', 'portrait');
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

        // Dossiers en retard — scopeEnRetardReel() = source unique (cf.
        // CreditDemande) : couvre EN_RETARD, EN_ATTENTE et PARTIELLEMENT_PAYE
        // dépassés (l'ancienne condition ignorait les dossiers déjà marqués
        // EN_RETARD et les règlements partiels).
        $enRetard = (clone $query)
            ->enRetardReel()
            ->with(['client', 'echeancier.echeances'])
            ->get();

        // Détails réels par dossier (les colonnes "Échéances retard" /
        // "Montant impayé" / "Dernière éch. due" affichaient 0 / 0Fc / – car
        // ces valeurs n'étaient jamais calculées).
        $aujourdhui = now()->toDateString();

        $enRetard->each(function (CreditDemande $d) use ($aujourdhui) {
            $retard = collect($d->echeancier?->echeances ?? [])->filter(function ($e) use ($aujourdhui) {
                if ($e->statut === 'PAYE') {
                    return false;
                }
                $date = $e->date_echeance instanceof \Carbon\Carbon
                    ? $e->date_echeance->toDateString()
                    : (string) $e->date_echeance;

                return $date < $aujourdhui;
            });

            $d->nb_echeances_retard = $retard->count();
            $d->montant_impaye = round((float) $retard->sum(
                fn ($e) => max(0, (float) $e->total_echeance - (float) $e->montant_paye)
            ), 2);
            $d->date_derniere_echeance_due = $retard
                ->map(fn ($e) => $e->date_echeance instanceof \Carbon\Carbon
                    ? $e->date_echeance->toDateString()
                    : (string) $e->date_echeance)
                ->max();
        });

        // Dossiers suspects / suspendus
        $alertes = (clone $query)
            ->whereIn('statut_global', ['SUSPECT','SUSPENDU'])
            ->get();

        // Dossiers prêts à débloquer
        $prets = (clone $query)
            ->where('statut_global', 'PRET_A_DEBLOQUER')
            ->get();

        // ── Statistiques par zone : données RÉELLES par devise ──────────
        // (auparavant : total/actifs/retard/encours/impayés restaient à zéro
        // — champs jamais calculés — d'où une colonne de tirets, un nom de
        // zone vide et un taux de recouvrement figé à 100 %).
        $tousDossiers = (clone $query)->with(['echeancier.echeances'])->get();
        $nomsZones = Zone::pluck('nom', 'code_zone');

        $stats_zones = $tousDossiers->groupBy('code_zone')->map(function ($dossiers, $codeZone) use ($nomsZones, $aujourdhui) {
            $symboles = ['CDF' => 'Fc', 'USD' => '$', 'EUR' => '€'];
            $encoursParDevise = [];
            $impayesParDevise = [];
            $actifs = 0;
            $retard = 0;

            foreach ($dossiers as $d) {
                $devise = $d->devise ?: 'CDF';

                $echeancesDues = collect($d->echeancier?->echeances ?? [])->filter(function ($e) use ($aujourdhui) {
                    if ($e->statut === 'PAYE') {
                        return false;
                    }
                    $date = $e->date_echeance instanceof \Carbon\Carbon
                        ? $e->date_echeance->toDateString()
                        : (string) $e->date_echeance;

                    return $date < $aujourdhui;
                });

                // Encours = capital réellement dû par les clients (montant_approuve)
                if (in_array($d->statut_global, ['DEBLOQUE', 'EN_REMBOURSEMENT', 'EN_RETARD'], true)) {
                    $actifs++;
                    $encoursParDevise[$devise] = ($encoursParDevise[$devise] ?? 0) + (float) $d->montant_approuve;
                }

                if ($echeancesDues->isNotEmpty()) {
                    $retard++;
                    $impayesParDevise[$devise] = ($impayesParDevise[$devise] ?? 0) + (float) $echeancesDues->sum(
                        fn ($e) => max(0, (float) $e->total_echeance - (float) $e->montant_paye)
                    );
                }
            }

            $formater = function (array $parDevise) use ($symboles) {
                if (empty($parDevise)) {
                    return '—';
                }
                $parts = [];
                foreach ($parDevise as $dev => $montant) {
                    $parts[] = number_format($montant, 2, ',', ' ') . ' ' . ($symboles[$dev] ?? $dev);
                }
                return implode('<br>', $parts);
            };

            return (object) [
                'code_zone'          => $codeZone,
                'zone_nom'           => $nomsZones[$codeZone] ?? $codeZone,
                'total_texte'        => (string) $dossiers->count(),
                'actifs_texte'       => (string) $actifs,
                'retard_texte'       => (string) $retard,
                'encours_texte'      => $formater($encoursParDevise),
                'impayes_texte'      => $formater($impayesParDevise),
                'encours_par_devise' => $encoursParDevise,
                'impayes_par_devise' => $impayesParDevise,
            ];
        })->sortByDesc(fn ($z) => array_sum($z->encours_par_devise))->values();

        $dossiers_retard = $enRetard;
        $dossiers_alertes = $alertes;
        $dossiers_pret_debloquer = $prets;
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
        $this->authorizeZoneAccess($dossier);

        $echeanceId = $request->input('echeance_id');
        $echeance = CreditEcheance::findOrFail($echeanceId);

        if ((int) $echeance->echeancier?->credit_demande_id !== (int) $dossier->id) {
            abort(404);
        }

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
            $dontCommission = (float)$echeance->commission_echeance * $ratio;

            CreditRemboursement::create([
                'credit_demande_id' => $dossier->id,
                'echeance_id'       => $echeance->id,
                'agent_matricule'   => $agent?->matricule ?? 'SYSTEM',
                'compte_id'         => $compteRmb->code_compte,
                'montant_recu'      => $montantRestantDu,
                'dont_capital'      => $dontCapital,
                'dont_interet'      => $dontInteret,
                'dont_commission'   => $dontCommission,
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
    // ACTIONS TRANSVERSES : Annulation / Suspension / Suspect
    // ================================================================
    //
    // NOTE : ces 5 méthodes existaient dans le code (commit 9d982ce) et ont
    // été supprimées par erreur (collateral damage) lors d'un refactor du
    // flux de remboursement (commit 520c0f6, 12/06/2026), alors que les
    // routes (routes/credit.php: annuler/suspendre/lever-suspension/
    // lever-suspicion/signaler-suspect) et les vues (modals show.blade.php,
    // boutons supervision.blade.php) y font toujours référence — d'où
    // l'erreur "Method does not exist" au clic. Restaurées ici à l'identique.

    public function annuler(Request $request, CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);
        $request->validate(['motif' => 'required|string|max:500']);

        if (in_array($dossier->statut_global, ['DEBLOQUE', 'EN_REMBOURSEMENT', 'SOLDE'])) {
            return back()->with('error', 'Un crédit débloqué ou soldé ne peut pas être annulé ici.');
        }

        $ancien = $dossier->statut_global;
        $dossier->update([
            'statut_global'        => 'ANNULE',
            'est_annule'           => true,
            'motif_annulation'     => $request->motif,
            'annule_par_matricule' => Auth::user()->agent?->matricule,
            'annule_le'            => now(),
        ]);
        $this->logAudit($dossier, 'ANNULATION', $ancien, 'ANNULE', $request->motif);

        app(NotificationService::class)->notifyUsers(
            User::query()
                ->whereIn('agent_matricule', array_values(array_filter([
                    $dossier->agent_createur_matricule,
                    $dossier->agent_analyse_matricule,
                ])))
                ->get(),
            'Dossier crédit annulé',
            sprintf('Le dossier %s a été annulé. Motif: %s', $dossier->numero_dossier, $request->motif),
            [
                'type' => 'warning',
                'category' => 'credit',
                'icon' => 'fas fa-ban',
                'action_url' => route('credit.show', $dossier),
            ]
        );

        return back()->with('success', 'Dossier annulé.');
    }

    public function suspendre(Request $request, CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);
        $request->validate(['motif' => 'required|string|max:500']);

        $ancien = $dossier->statut_global;
        $dossier->update([
            'statut_global'          => 'SUSPENDU',
            'est_suspendu'           => true,
            'motif_suspension'       => $request->motif,
            'suspendu_par_matricule' => Auth::user()->agent?->matricule,
            'suspendu_le'            => now(),
        ]);
        $this->logAudit($dossier, 'SUSPENSION', $ancien, 'SUSPENDU', $request->motif);

        app(NotificationService::class)->notifyUsers(
            User::query()
                ->whereIn('agent_matricule', array_values(array_filter([
                    $dossier->agent_createur_matricule,
                    $dossier->agent_analyse_matricule,
                ])))
                ->get(),
            'Dossier crédit suspendu',
            sprintf('Le dossier %s a été suspendu. Motif: %s', $dossier->numero_dossier, $request->motif),
            [
                'type' => 'warning',
                'category' => 'credit',
                'icon' => 'fas fa-pause-circle',
                'action_url' => route('credit.show', $dossier),
            ]
        );

        return back()->with('success', 'Dossier suspendu.');
    }

    public function leverSuspension(CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        $dossier->update([
            'statut_global'          => 'EN_VALIDATION',
            'est_suspendu'           => false,
            'motif_suspension'       => null,
            'suspendu_par_matricule' => null,
            'suspendu_le'            => null,
        ]);
        $this->logAudit($dossier, 'LEVER_SUSPENSION', 'SUSPENDU', 'EN_VALIDATION');

        app(NotificationService::class)->notifyUsers(
            User::query()
                ->whereIn('agent_matricule', array_values(array_filter([
                    $dossier->agent_createur_matricule,
                    $dossier->agent_analyse_matricule,
                ])))
                ->get(),
            'Suspension levée',
            sprintf('La suspension du dossier %s a été levée. Le dossier revient en validation.', $dossier->numero_dossier),
            [
                'type' => 'info',
                'category' => 'credit',
                'icon' => 'fas fa-play-circle',
                'action_url' => route('credit.show', $dossier),
            ]
        );

        return back()->with('success', 'Suspension levée.');
    }

    public function signalerSuspect(Request $request, CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);
        $request->validate(['motif' => 'required|string|max:500']);

        $ancien = $dossier->statut_global;
        $dossier->update([
            'statut_global'         => 'SUSPECT',
            'est_suspect'           => true,
            'motif_suspicion'       => $request->motif,
            'signale_par_matricule' => Auth::user()->agent?->matricule,
            'signale_le'            => now(),
        ]);
        $this->logAudit($dossier, 'SIGNALEMENT_SUSPECT', $ancien, 'SUSPECT', $request->motif);

        app(NotificationService::class)->notifyUsers(
            User::query()
                ->whereIn('agent_matricule', array_values(array_filter([
                    $dossier->agent_createur_matricule,
                    $dossier->agent_analyse_matricule,
                ])))
                ->get(),
            'Dossier signalé suspect',
            sprintf('Le dossier %s a été signalé comme suspect. Motif: %s', $dossier->numero_dossier, $request->motif),
            [
                'type' => 'danger',
                'category' => 'credit',
                'icon' => 'fas fa-exclamation-triangle',
                'action_url' => route('credit.show', $dossier),
            ]
        );

        return back()->with('success', 'Dossier signalé comme suspect.');
    }

    public function leverSuspicion(CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        $dossier->update([
            'statut_global'          => 'EN_VALIDATION',
            'est_suspect'            => false,
            'motif_suspicion'        => null,
            'signale_par_matricule'  => null,
            'signale_le'             => null,
        ]);
        $this->logAudit($dossier, 'LEVER_SUSPICION', 'SUSPECT', 'EN_VALIDATION');

        app(NotificationService::class)->notifyUsers(
            User::query()
                ->whereIn('agent_matricule', array_values(array_filter([
                    $dossier->agent_createur_matricule,
                    $dossier->agent_analyse_matricule,
                ])))
                ->get(),
            'Suspicion levée',
            sprintf('Le signalement de suspicion du dossier %s a été levé. Le dossier revient en validation.', $dossier->numero_dossier),
            [
                'type' => 'info',
                'category' => 'credit',
                'icon' => 'fas fa-shield-alt',
                'action_url' => route('credit.show', $dossier),
            ]
        );

        return back()->with('success', 'Suspicion levée.');
    }

    // ================================================================
    // PIÈCES JUSTIFICATIVES
    // ================================================================

    /**
     * Marque une pièce justificative comme fournie/manquante, permet d'y
     * joindre un fichier (photo scannée ou PDF déjà existant) et d'ajouter
     * un commentaire.
     *
     * Jusqu'ici l'onglet "Pièces & docs" était uniquement en lecture (les 4
     * lignes standard IDENTITE/DOMICILE/REVENU/AUTRE sont créées automatiquement
     * à la création du dossier — voir store(), mais rien ne permettait de les
     * mettre à jour ni d'y joindre un document). Autorisé pendant toute la
     * phase de constitution/analyse du dossier (BROUILLON → EN_VALIDATION) ;
     * plus modifiable une fois PRET_A_DEBLOQUER ou au-delà.
     *
     * Stockage : même principe que les photos client/agent
     * (base_path('images_projet/...'), pas le disque Storage) mais dans
     * images_projet/credits/pieces/{dossier_id}/ — servi via une route
     * protégée par authentification (piecesFichier()), pas en accès public
     * direct, car ce sont des documents d'identité/revenus sensibles.
     *
     * Pour les pièces comme la carte d'électeur qui ne nécessitent qu'une
     * simple photo (scan téléphone/webcam), le champ accepte une image
     * (jpg/png) en plus du PDF direct : une image envoyée est automatiquement
     * convertie en PDF une page avant d'être enregistrée, pour que TOUTES les
     * pièces soient stockées de manière uniforme en PDF.
     */
    public function updatePiece(Request $request, CreditDemande $dossier, CreditPiece $piece)
    {
        $this->authorizeZoneAccess($dossier);

        if ((int) $piece->credit_demande_id !== (int) $dossier->id) {
            abort(404);
        }

        if (in_array($dossier->statut_global, ['PRET_A_DEBLOQUER','DEBLOQUE','EN_REMBOURSEMENT','EN_RETARD','SOLDE','ANNULE'])) {
            return back()->with('error', 'Les pièces ne sont plus modifiables à ce stade du dossier.')->withFragment('tab_pieces');
        }

        $validated = $request->validate([
            'est_recu'     => 'sometimes|boolean',
            'nom_fichier'  => 'nullable|string|max:255',
            'observations' => 'nullable|string|max:500',
            'fichier'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:8192',
        ]);

        $nomFichier = $validated['nom_fichier'] ?? $piece->nom_fichier;
        $estRecu = $request->boolean('est_recu');

        if ($request->hasFile('fichier')) {
            $nomFichier = $this->storePieceFichier($dossier, $piece, $request->file('fichier'));
            $estRecu = true; // Un document joint vaut automatiquement "Fournie"
        }

        $piece->update([
            'est_recu'     => $estRecu,
            'nom_fichier'  => $nomFichier,
            'observations' => $validated['observations'] ?? null,
        ]);

        $this->logAudit(
            $dossier,
            'PIECE_MISE_A_JOUR',
            $dossier->statut_global,
            $dossier->statut_global,
            sprintf(
                "Pièce %s (%s) : %s%s%s",
                $piece->type_piece,
                $piece->libelle,
                $piece->est_recu ? 'Fournie' : 'Manquante',
                $piece->nom_fichier ? " | Réf: {$piece->nom_fichier}" : '',
                $piece->observations ? " | {$piece->observations}" : ''
            )
        );

        return back()->with('success', 'Pièce justificative mise à jour.')->withFragment('tab_pieces');
    }

    /**
     * Enregistre le fichier joint à une pièce (image scannée ou PDF) dans
     * images_projet/credits/pieces/{dossier_id}/ et retourne le chemin
     * relatif à stocker dans nom_fichier. Une image est convertie en PDF
     * une page (DomPDF) pour que le stockage soit uniformément du PDF.
     */
    private function storePieceFichier(CreditDemande $dossier, CreditPiece $piece, $fichier): string
    {
        $destinationDir = base_path('images_projet/credits/pieces/' . $dossier->id);
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $baseName = 'piece_' . $piece->id . '_' . time();
        $extension = strtolower($fichier->getClientOriginalExtension());
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png']);

        // Supprime l'ancien fichier de cette pièce s'il existe (remplacement)
        if ($piece->nom_fichier) {
            $ancienPath = base_path('images_projet/' . $piece->nom_fichier);
            if (file_exists($ancienPath)) {
                @unlink($ancienPath);
            }
        }

        $nomFichierFinal = $baseName . '.pdf';
        $cheminAbsolu = $destinationDir . '/' . $nomFichierFinal;

        if ($isImage) {
            // Photo/scan (ex: carte d'électeur) → conversion en PDF une page
            $base64 = base64_encode(file_get_contents($fichier->getRealPath()));
            $mime = $fichier->getMimeType();
            $pdf = Pdf::loadView('impressions.credit.piece_scan', [
                'imageData' => "data:{$mime};base64,{$base64}",
                'dossier'   => $dossier,
                'piece'     => $piece,
            ]);
            $pdf->save($cheminAbsolu);
        } else {
            // Déjà un PDF → enregistré tel quel
            $fichier->move($destinationDir, $nomFichierFinal);
        }

        return 'credits/pieces/' . $dossier->id . '/' . $nomFichierFinal;
    }

    /**
     * Sert le fichier PDF d'une pièce justificative (accès protégé par
     * authentification + portée de zone, contrairement aux photos
     * client/agent qui sont servies sans contrôle de zone).
     */
    public function piecesFichier(CreditDemande $dossier, CreditPiece $piece)
    {
        $this->authorizeZoneAccess($dossier);

        if ((int) $piece->credit_demande_id !== (int) $dossier->id || !$piece->nom_fichier) {
            abort(404);
        }

        $path = base_path('images_projet/' . $piece->nom_fichier);
        if (!file_exists($path)) {
            abort(404);
        }

        if (ob_get_level()) {
            ob_end_clean();
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Length' => filesize($path),
        ]);
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

    /**
     * Périmètre de zone de l'utilisateur : null = accès GLOBAL (pas de
     * filtre), sinon les codes de zone de ses affectations ACTIVES.
     *
     * Corrigé le 22/09/2026 (port du correctif Cooperc-AB) — deux bugs ici :
     *  1) le périmètre global n'était accordé qu'au détenteur de EBEN-PER61.
     *     Or le GÉRANT (EBEN-ROL12) détient PER63/PER64 (validation Gérant +
     *     déblocage) mais PAS PER61 → sur les pages utilisant cette méthode
     *     (En Cours, Supervision…), il tombait dans le filtre par zone et
     *     voyait une liste VIDE alors que la liste des dossiers lui montrait
     *     tout.
     *  2) `$user->agent->code_zone` est une colonne qui N'EXISTE PAS sur
     *     tb_agents → Eloquent renvoie toujours null, donc le repli codé en
     *     dur 'ZONE-01' (qui ne correspond à AUCUNE zone réelle, format
     *     ZON-BMB-26-xxxxx) était utilisé pour tout le monde : la clause
     *     whereIn ne matchait jamais rien, chacun ne voyait que ses propres
     *     dossiers créés.
     * Le vrai périmètre vient de tb_affectations_zones (Etat = ACTIF).
     */
    private function resolveZoneScope($user): ?array
    {
        $perms = $user?->getPermissionCodes() ?? [];
        if (count(array_intersect(['EBEN-PER61', 'EBEN-PER62', 'EBEN-PER63', 'EBEN-PER64'], $perms)) > 0) {
            return null; // Superviseur / Gérant : accès global, pas de restriction de zone.
        }

        $matricule = $user?->agent?->matricule;
        if (!$matricule) {
            return [];
        }

        return DB::table('tb_affectations_zones')
            ->where('agent_matricule', $matricule)
            ->where('Etat', 'ACTIF')
            ->pluck('code_zone')
            ->toArray();
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
        try {
            $this->authorizeZoneAccess($dossier);
            return true;
        } catch (\Throwable $e) {
            if ($throw) {
                throw $e;
            }
            return false;
        }
    }

    /**
     * Vérifie que l'utilisateur connecté a réellement le droit d'agir sur CE
     * dossier précis (pas seulement qu'il a la permission générique requise
     * par le middleware de la route). Reproduit exactement le même périmètre
     * que la liste des dossiers (index()) : superviseur = accès global ;
     * agent crédit (PER58) = seulement ses dossiers affectés ou ceux de ses
     * portefeuilles actifs ; autres utilisateurs = dossiers qu'ils ont créés
     * ou qui sont dans leur(s) zone(s) habituelle(s).
     *
     * Avant cette implémentation, cette méthode était un stub vide : la seule
     * protection réelle venait des middlewares `permission:` (par rôle),
     * sans aucune vérification par dossier — un utilisateur pouvait accéder
     * à N'IMPORTE QUEL dossier (y compris hors de sa zone/portefeuille) en
     * devinant/changeant simplement l'ID dans l'URL.
     */
    private function authorizeZoneAccess(CreditDemande $dossier): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Authentification requise.');
        }

        $perms     = $user->getPermissionCodes();
        $matricule = $user->agent?->matricule;

        $superviseurPerms = ['EBEN-PER61', 'EBEN-PER62', 'EBEN-PER63', 'EBEN-PER64'];
        if (count(array_intersect($superviseurPerms, $perms)) > 0) {
            return; // Superviseur : accès global à tous les dossiers, sans restriction.
        }

        $estAgentCredit = in_array('EBEN-PER58', $perms, true);
        if ($estAgentCredit) {
            if ($matricule && $dossier->agent_analyse_matricule === $matricule) {
                return;
            }
            $portefeuilleIds = $this->resolvePortefeuilleScope($user);
            if (!empty($portefeuilleIds) && in_array($dossier->portefeuille_id, $portefeuilleIds, true)) {
                return;
            }
            abort(403, "Vous n'avez pas accès à ce dossier crédit (non affecté à vous ni à votre portefeuille).");
        }

        // Autres profils (agent commercial, chargé d'opérations, etc.) :
        // dossiers qu'ils ont créés eux-mêmes, ou situés dans leur zone.
        if ($matricule && $dossier->agent_createur_matricule === $matricule) {
            return;
        }

        $zonesCodes = $this->resolveZoneScope($user);
        if ($zonesCodes === null || in_array($dossier->code_zone, $zonesCodes, true)) {
            return;
        }

        abort(403, "Vous n'avez pas accès à ce dossier crédit (hors de votre zone).");
    }

    /**
     * Agents éligibles à recevoir un dossier crédit en analyse : ceux dont
     * l'utilisateur associé a la permission EBEN-PER58 (Saisir analyse crédit),
     * ou qui sont Administrateur (EBEN-ROL1, bypass total) — ET qui disposent
     * réellement d'au moins un portefeuille actif (via affectation active ou
     * colonne statique, cf. resolveAgentPortefeuilleIds()).
     *
     * Avant ce correctif, TOUS les agents du système apparaissaient dans la
     * liste (y compris des caissiers, RH, etc.), puis n'importe quel agent
     * "éligible par permission" mais SANS portefeuille apparaissait aussi,
     * menant systématiquement à l'erreur "ne dispose d'aucun portefeuille
     * actif" lors de la tentative d'affectation. Le filtrage se fait
     * maintenant en amont : seuls des agents réellement affectables
     * apparaissent dans la liste.
     */
    private function resolveAssignableCreditAgents()
    {
        $matriculesEligibles = DB::table('users')
            ->join('tb_role_user', 'users.id', '=', 'tb_role_user.user_id')
            ->join('tb_role_permission', 'tb_role_user.role_code', '=', 'tb_role_permission.role_code')
            ->whereNotNull('users.agent_matricule')
            ->where(function ($q) {
                $q->where('tb_role_permission.permission_code', 'EBEN-PER58')
                  ->orWhere('tb_role_user.role_code', 'EBEN-ROL1');
            })
            ->pluck('users.agent_matricule')
            ->unique();

        $matriculesAvecPortefeuille = $matriculesEligibles->filter(
            fn ($matricule) => !empty($this->resolveAgentPortefeuilleIds($matricule))
        );

        $agents = Agent::whereIn('matricule', $matriculesAvecPortefeuille)->orderBy('nom')->get(['matricule','nom','postnom','prenom']);

        return $agents->map(function ($agent) {
            $portefeuilleIds = $this->resolveAgentPortefeuilleIds($agent->matricule);
            $portefeuilles = \App\Models\Tresorerie\Portefeuille::whereIn('id', $portefeuilleIds)
                ->get(['id', 'nom_portefeuille']);

            $agent->portefeuilles_actifs = $portefeuilles->map(fn ($pf) => [
                'id' => $pf->id,
                'nom_portefeuille' => $pf->nom_portefeuille,
            ])->values()->all();

            $agent->portefeuille_actif_unique_id = $portefeuilles->count() === 1 ? $portefeuilles->first()->id : null;
            $agent->portefeuille_actif_resume = $portefeuilles->pluck('nom_portefeuille')->implode(', ');

            return $agent;
        });
    }

    private function resolveDemandeurMeta($matricule): array
    {
        $agent = Agent::where('matricule', $matricule)->first();
        return [
            'nom' => $agent ? "{$agent->nom} {$agent->prenom}" : 'SYSTÈME',
            'matricule' => $matricule
        ];
    }

    /**
     * Vérifie que l'agent a bien le profil analyse crédit (EBEN-PER58) via
     * son utilisateur associé, ou qu'il est Administrateur (EBEN-ROL1).
     * Auparavant cette méthode retournait toujours `true` (jamais implémentée),
     * ce qui permettait d'affecter n'importe quel agent — puis l'affectation
     * échouait un peu plus loin faute de portefeuille compatible.
     */
    private function isEligibleCreditAnalyst(string $matricule): bool
    {
        return DB::table('users')
            ->join('tb_role_user', 'users.id', '=', 'tb_role_user.user_id')
            ->join('tb_role_permission', 'tb_role_user.role_code', '=', 'tb_role_permission.role_code')
            ->where('users.agent_matricule', $matricule)
            ->where(function ($q) {
                $q->where('tb_role_permission.permission_code', 'EBEN-PER58')
                  ->orWhere('tb_role_user.role_code', 'EBEN-ROL1');
            })
            ->exists();
    }

    /**
     * Portefeuilles actuellement rattachés à un agent.
     *
     * IMPORTANT : ne pas se limiter à la colonne statique
     * tb_portefeuilles_agents.agent_matricule — un portefeuille peut être
     * réaffecté à un autre agent via une AFFECTATION (tb_affectations_portefeuilles,
     * cf. Portefeuille::affectationActive()) sans que cette colonne ne soit mise
     * à jour. C'est exactement le même principe de repli que partout ailleurs
     * dans l'app (affectationActive->agent ?? agent). Ignorer les affectations
     * ici faisait échouer l'affectation d'un agent de crédit sur un dossier
     * dès que son portefeuille avait été réaffecté après sa création.
     */
    private function resolveAgentPortefeuilleIds(string $matricule): array
    {
        return \App\Models\Tresorerie\Portefeuille::with('affectationActive')
            ->get()
            ->filter(function ($pf) use ($matricule) {
                $agentActuel = $pf->affectationActive->agent_matricule ?? $pf->agent_matricule;
                return $agentActuel === $matricule;
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->toArray();
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