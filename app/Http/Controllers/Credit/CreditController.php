<?php

namespace App\Http\Controllers\Credit;

use App\Http\Controllers\Controller;
use App\Models\Credit\CreditDemande;
use App\Models\Credit\CreditAnalyse;
use App\Models\Credit\CreditValidation;
use App\Models\Credit\CreditPiece;
use App\Models\Credit\CreditDeblocage;
use App\Models\Credit\CreditEcheancier;
use App\Models\Credit\CreditRemboursement;
use App\Models\Credit\CreditAudit;
use App\Models\Clients\Client;
use App\Models\Clients\Compte;
use App\Models\User;
use App\Models\RH\Agent;
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
        $dashboardPerms = ['EBEN-PER61', 'EBEN-PER62', 'EBEN-PER63', 'EBEN-PER64', 'EBEN-PER65'];
        if (count(array_intersect($dashboardPerms, $perms)) === 0) {
            return redirect()
                ->route('credit.index')
                ->with('error', "Accès non autorisé au tableau de bord crédit.");
        }

        $superviseurPerms = ['EBEN-PER61', 'EBEN-PER62', 'EBEN-PER63', 'EBEN-PER64', 'EBEN-PER65'];
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
        $superviseurPerms = ['EBEN-PER61', 'EBEN-PER62', 'EBEN-PER63', 'EBEN-PER64', 'EBEN-PER65'];
        $estSuperviseur   = count(array_intersect($superviseurPerms, $perms)) > 0;

        // L'agent crédit (PER58) sans rôle superviseur ne voit que ses dossiers affectés
        $estAgentCredit = in_array('EBEN-PER58', $perms, true) && !$estSuperviseur;

        $query = CreditDemande::with(['client', 'zone']);

        if ($estSuperviseur) {
            // Scope global : tous les dossiers, pas de restriction
        } elseif ($estAgentCredit) {
            // L'agent ne voit que les dossiers qui lui ont été affectés
            if ($matricule) {
                $query->where('agent_analyse_matricule', $matricule);
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

        // Filtres
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
        if ($request->filled('zone')) {
            $query->where('code_zone', $request->zone);
        }
        if ($request->filled('type_credit')) {
            $query->where('type_credit', $request->type_credit);
        }

        $dossiers = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $zones = Zone::when($zonesCodes !== null, fn($q) => $q->whereIn('code_zone', $zonesCodes))
            ->orderBy('nom')->get();

        return view('credit.liste', compact('dossiers', 'zones'));
    }

    // ================================================================
    // CRÉATION D'UN DOSSIER
    // ================================================================

    public function create(Request $request)
    {
        // Règle métier: toute personne habilitée à créer une demande peut sélectionner n'importe quel client.
        $clients = Client::orderBy('nom')->orderBy('postnom')->orderBy('prenom')->get();
        $zones = Zone::orderBy('nom')->get();
        $selectedClientMatricule = $request->query('client_matricule');

        return view('credit.creation', compact('clients', 'zones', 'selectedClientMatricule'));
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
            'montant'  => 'required|numeric|min:1',
            'taux'     => 'required|numeric|min:0.01|max:100',
            'duree'    => 'required|integer|min:1|max:360',
        ]);

        $calcul = $this->amortissement->simuler(
            (float) $request->montant,
            (float) $request->taux,
            (int) $request->duree
        );

        return response()->json($calcul);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_matricule'     => 'required|string|exists:tb_clients,matricule',
            'montant_demande'      => 'required|numeric|min:1',
            'devise'               => 'required|in:CDF,USD,EUR',
            'duree_mois'           => 'required|integer|min:1|max:360',
            'taux_interet_mensuel' => 'required|numeric|min:0.01|max:100',
            'type_credit'          => 'required|in:INDIVIDUEL,SOLIDAIRE,PME',
            'objet_credit'         => 'required|string|max:500',
            'garantie_description' => 'nullable|string',
            'service_provenance'   => 'nullable|string|max:100',
            'referent_nom'         => 'nullable|string|max:120',
        ]);

        $user  = Auth::user();
        $agent = $user->agent;

        $client = Client::findOrFail($validated['client_matricule']);

        $calcul = $this->amortissement->simuler(
            (float) $validated['montant_demande'],
            (float) $validated['taux_interet_mensuel'],
            (int) $validated['duree_mois']
        );

        $demande = DB::transaction(function () use ($validated, $client, $agent, $calcul) {
            $demande = CreditDemande::create([
                'client_matricule'        => $validated['client_matricule'],
                'compte_id'               => null,
                'portefeuille_id'         => null,
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

        $assignableAgents = collect();
        if ($authUser?->hasPermission('EBEN-PER61') && $dossier->statut_global === 'SOUMIS') {
            $assignableAgents = $this->resolveAssignableCreditAgents();
        }

        $demandeurMeta = $this->resolveDemandeurMeta($dossier->agent_createur_matricule);

        $demande = $dossier;
        return view('credit.show', compact('dossier', 'demande', 'canViewAudit', 'assignableAgents', 'demandeurMeta'));
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
        ]);

        if (!$this->isEligibleCreditAnalyst($validated['agent_analyse_matricule'])) {
            return back()->with('error', 'L\'agent sélectionné n\'a pas le profil analyse crédit (PER58).');
        }

        $ancienAgent = $dossier->agent_analyse_matricule;

        $dossier->update([
            'agent_analyse_matricule' => $validated['agent_analyse_matricule'],
        ]);

        $details = $ancienAgent
            ? "Réaffecté de {$ancienAgent} vers {$validated['agent_analyse_matricule']}"
            : "Affecté à {$validated['agent_analyse_matricule']}";

        $this->logAudit($dossier, 'AFFECTATION_ANALYSE', $dossier->statut_global, $dossier->statut_global, $details);

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

        DB::transaction(function () use ($validated, $dossier, $agent) {
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
            }

            if ($statut === 'COMPLETE') {
                $dossier->update(['statut_global' => 'EN_VALIDATION']);
                // Activer le bloc n°1 (Agent crédit)
                $dossier->validations()->where('ordre_etape', 1)
                    ->update(['etape_precedente_ok' => true]);
                $this->logAudit($dossier, 'ANALYSE_COMPLETE', 'EN_ANALYSE', 'EN_VALIDATION');
            }
        });

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
                : (float) $validated['montant_valide'];
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

            $montantRetenu = $montantValide ?? $conditionsAvantDecision['montant'];
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
                    'icon' => 'fas fa-hand-holding-usd',
                    'action_url' => route('credit.deblocage', $dossier),
                ]
            );

            $notificationService->notifyUsers(
                $targetUsers,
                'Validation crédit terminée',
                sprintf('Le dossier %s est validé et prêt à débloquer.', $dossier->numero_dossier),
                [
                    'type' => 'info',
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

        // Option B: le déblocage crédite les comptes (RMB/GTC) sans sortie physique de caisse.
        // Le solde coffre sera décrémenté lors d'un RETRAIT client RMB au guichet.
        $coffreSolde = CaissesGuichetSolde::findOrFail($validated['coffre_solde_id']);

        $alreadyDebloque = false;

        DB::transaction(function () use ($dossier, $validated, $agentMatricule, $montant, $coffreSolde, &$alreadyDebloque) {
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

            // ── Répartition du montant débloqué ──────────────────────────
            $netVerse  = round($montant * 0.80, 2);  // 80% → RMB disponible
            $caution   = round($montant * 0.20, 2);  // 20% → GTC bloqué
            $fraisReel = round($montant * 0.04, 2);  // 4% → prélèvement sur RMB

            $soldeAvantRmb = (float) $compteCredit->solde_reel;

            // 1. Créditer le compte RMB client (80%)
            $compteCredit->increment('solde_reel', $netVerse);

            // 2. Prélever les frais (4%) depuis le RMB
            $compteCredit->decrement('solde_reel', $fraisReel);

            $compteCredit->refresh();
            $soldeApresRmb = (float) $compteCredit->solde_reel;

            // 3. Créditer le compte GTC (caution bloquée) — créer si absent
            $compteGtc = Compte::firstOrCreate(
                [
                    'client_matricule' => $dossier->client_matricule,
                    'type'             => 'GTC',
                    'devise'           => $dossier->devise,
                ],
                [
                    'solde_reel'    => 0,
                    'solde_bloque'  => 0,
                    'portefeuille_id' => null,
                ]
            );
            $compteGtc->increment('solde_bloque', $caution);

            $referenceDepot = 'DEB-' . $dossier->numero_dossier . '-' . now()->format('YmdHis') . '-D';
            $referenceFrais = 'DEB-' . $dossier->numero_dossier . '-' . now()->format('YmdHis') . '-F';

            // 4. Historique RMB: depot de deblocage (visible dans releve)
            Transaction::create([
                'compte_code'             => $compteCredit->code_compte,
                'agent_matricule'         => $agentMatricule,
                'guichet_id'              => $coffreSolde->guichet_id,
                'devise_code'             => $dossier->devise,
                'type'                    => Transaction::DEPOT,
                'montant'                 => $netVerse,
                'montant_commission_total'=> 0,
                'solde_compte_avant'      => $soldeAvantRmb,
                'solde_compte_apres'      => round($soldeAvantRmb + $netVerse, 2),
                'montant_total_client'    => $netVerse,
                'montant_net_client'      => $netVerse,
                'reference'               => $referenceDepot,
                'observations'            => 'Deblocage credit ' . $dossier->numero_dossier . ' (80% RMB)',
                'statut'                  => Transaction::CONFIRME,
                'date_operation'          => Carbon::parse($validated['date_deblocage']),
            ]);

            // 5. Historique RMB: frais de dossier (visible dans releve)
            Transaction::create([
                'compte_code'             => $compteCredit->code_compte,
                'agent_matricule'         => $agentMatricule,
                'guichet_id'              => $coffreSolde->guichet_id,
                'devise_code'             => $dossier->devise,
                'type'                    => Transaction::RETRAIT,
                'montant'                 => $fraisReel,
                'montant_commission_total'=> 0,
                'solde_compte_avant'      => round($soldeAvantRmb + $netVerse, 2),
                'solde_compte_apres'      => $soldeApresRmb,
                'montant_total_client'    => $fraisReel,
                'montant_net_client'      => $fraisReel,
                'reference'               => $referenceFrais,
                'observations'            => 'Frais deblocage credit ' . $dossier->numero_dossier . ' (4%)',
                'statut'                  => Transaction::CONFIRME,
                'date_operation'          => Carbon::parse($validated['date_deblocage']),
            ]);

            CreditDeblocage::create([
                'credit_demande_id'     => $dossier->id,
                'agent_matricule'       => $agentMatricule,
                'compte_debit_id'       => $coffreSolde->guichet->code_guichet ?? ('GUICHET-' . $coffreSolde->guichet_id),
                'guichet_solde_id'      => $coffreSolde->id,
                'compte_credit_id'      => $compteCredit->code_compte,
                'montant_debloque'     => $montant,
                'devise'               => $dossier->devise,
                'frais_dossier'        => $fraisReel,
                'montant_net_verse'    => $soldeApresRmb - $soldeAvantRmb,
                'reference_transaction' => $validated['reference_comptable'],
                'observations'         => $validated['observations'],
                'debloque_le'          => Carbon::parse($validated['date_deblocage']),
            ]);

            // Option B: aucun mouvement de coffre ici.
            // La sortie espèces sera constatée lors d'un RETRAIT client RMB au guichet.

            // Générer l'échéancier
            $datePremier = Carbon::parse($validated['date_premier_remboursement']);
            $this->amortissement->genererEtSauvegarder($dossier, $datePremier);

            $dossier->update([
                'compte_id' => $compteCredit->code_compte,
                'portefeuille_id' => $compteCredit->portefeuille_id,
                'statut_global' => 'DEBLOQUE',
            ]);
            $this->logAudit($dossier, 'DEBLOCAGE', $ancien, 'DEBLOQUE',
                "Montant débloqué : {$montant} {$dossier->devise}");
        });

        if ($alreadyDebloque) {
            return back()->with('error', 'Ce dossier a deja ete debloque par un autre traitement.');
        }

        return redirect()->route('credit.show', $dossier)
            ->with('success', "Deblocage de {$dossier->numero_dossier} effectue. Le coffre sera impacte uniquement au retrait RMB du client.");
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
            'portefeuille_id' => null,
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

        return view('credit.remboursement', compact('dossier', 'demande', 'prochaineEcheance', 'echeancier', 'comptesInstitution'));
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

        DB::transaction(function () use ($validated, $dossier, $agent) {
            $rembours = CreditRemboursement::create([
                'credit_demande_id'  => $dossier->id,
                'echeance_id'        => $validated['echeance_id'],
                'agent_matricule'    => $agent?->matricule ?? 'SYSTEM',
                'compte_id'          => $dossier->compte_id,
                'montant_recu'       => $validated['montant_recu'],
                'dont_capital'       => $validated['dont_capital'],
                'dont_interet'       => $validated['dont_interet'],
                'dont_penalite'      => $validated['dont_penalite'] ?? 0,
                'type_remboursement' => $validated['type_remboursement'],
                'reference_caisse'   => $validated['reference_caisse'],
                'observations'       => $validated['observations'],
                'recu_le'            => Carbon::parse($validated['date_paiement']),
            ]);

            // Marquer l'échéance comme payée si spécifiée
            if ($validated['echeance_id']) {
                $echeance = \App\Models\Credit\CreditEcheance::find($validated['echeance_id']);
                if ($echeance) {
                    $newMontant = $echeance->montant_paye + $validated['montant_recu'];
                    $nouveau    = $newMontant >= $echeance->total_echeance ? 'PAYE' : 'PARTIELLEMENT_PAYE';
                    $echeance->update([
                        'montant_paye'           => $newMontant,
                        'statut'                 => $nouveau,
                        'date_paiement_effectif' => $validated['date_paiement'],
                    ]);
                }
            }

            // Mettre à jour le statut du dossier
            if ($dossier->statut_global === 'DEBLOQUE') {
                $dossier->update(['statut_global' => 'EN_REMBOURSEMENT']);
            }

            // Vérifier si toutes les échéances sont soldées
            $totalEcheances    = $dossier->echeancier?->echeances()->count() ?? 0;
            $echeancesPayees   = $dossier->echeancier?->echeances()->where('statut','PAYE')->count() ?? 0;
            if ($totalEcheances > 0 && $totalEcheances === $echeancesPayees) {
                $dossier->update(['statut_global' => 'SOLDE']);
                $this->logAudit($dossier, 'REMBOURSEMENT', 'EN_REMBOURSEMENT', 'SOLDE', 'Crédit soldé intégralement.');
            } else {
                $this->logAudit($dossier, 'REMBOURSEMENT', $dossier->statut_global, $dossier->statut_global,
                    "Remboursement de {$validated['montant_recu']} {$dossier->devise}");
            }
        });

        return redirect()->route('credit.show', $dossier)
            ->with('success', 'Remboursement enregistré.');
    }

    // ================================================================
    // ACTIONS TRANSVERSES : Annulation / Suspension / Suspect
    // ================================================================

    public function annuler(Request $request, CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);
        $request->validate(['motif' => 'required|string|max:500']);

        if (in_array($dossier->statut_global, ['DEBLOQUE','EN_REMBOURSEMENT','SOLDE'])) {
            return back()->with('error', 'Un crédit débloqué ou soldé ne peut pas être annulé ici.');
        }

        $ancien = $dossier->statut_global;
        $dossier->update([
            'statut_global'         => 'ANNULE',
            'est_annule'            => true,
            'motif_annulation'      => $request->motif,
            'annule_par_matricule'  => Auth::user()->agent?->matricule,
            'annule_le'             => now(),
        ]);
        $this->logAudit($dossier, 'ANNULATION', $ancien, 'ANNULE', $request->motif);

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

        return back()->with('success', 'Suspension levée.');
    }

    public function signalerSuspect(Request $request, CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);
        $request->validate(['motif' => 'required|string|max:500']);

        $ancien = $dossier->statut_global;
        $dossier->update([
            'statut_global'          => 'SUSPECT',
            'est_suspect'            => true,
            'motif_suspicion'        => $request->motif,
            'signale_par_matricule'  => Auth::user()->agent?->matricule,
            'signale_le'             => now(),
        ]);
        $this->logAudit($dossier, 'SIGNALEMENT_SUSPECT', $ancien, 'SUSPECT', $request->motif);

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

        return back()->with('success', 'Suspicion levée.');
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
    // PDF ÉCHÉANCIER
    // ================================================================

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
    // PDF FICHE CRÉDIT
    // ================================================================

    public function pdfFiche(CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        $dossier->load(['client','compte','zone','analyse','validations','pieces','deblocage','echeancier.echeances']);

        if (in_array($dossier->statut_global, ['BROUILLON', 'ANNULE'], true)) {
            return back()->with('error', 'La fiche PDF est disponible à partir du statut SOUMIS.');
        }

        $demande = $dossier;
        $demandeurMeta = $this->resolveDemandeurMeta($dossier->agent_createur_matricule);

        $pdf = Pdf::loadView('impressions.credit.fiche_credit', compact('dossier', 'demande', 'demandeurMeta'))
            ->setPaper('A4', 'portrait');

        $filename = 'dossier_demande_credit_analyse_' . $dossier->numero_dossier . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->stream($filename);
    }

    // ================================================================
    // HELPERS PRIVÉS
    // ================================================================

    private function resolveZoneScope(\App\Models\User $user): ?array
    {
        if ($user->hasPermission('EBEN-PER1')) {
            return null; // Admin : toutes les zones
        }

        if ($user->hasPermission('EBEN-PER61')) {
            return null; // Chargé des opérations : supervision globale des demandes crédit
        }

        $agent = $user->agent;
        if (!$agent) {
            return []; // Aucune zone
        }

        $zones = Zone::assignedToAgent($agent->matricule)
            ->pluck('code_zone')
            ->toArray();

        if (!empty($zones)) {
            return $zones;
        }

        // Fallback métier: en configuration agence unique/tests, certains acteurs crédit
        // n'ont pas de zone explicite. On ouvre alors le scope Crédit à toutes les zones.
        $creditPermissions = [
            'EBEN-PER53', 'EBEN-PER54', 'EBEN-PER56', 'EBEN-PER57', 'EBEN-PER58',
            'EBEN-PER59', 'EBEN-PER60', 'EBEN-PER61', 'EBEN-PER62', 'EBEN-PER63',
            'EBEN-PER64', 'EBEN-PER65', 'EBEN-PER66', 'EBEN-PER67', 'EBEN-PER68',
            'EBEN-PER69', 'EBEN-PER70', 'EBEN-PER71', 'EBEN-PER72',
        ];

        if ($user->hasPermission($creditPermissions)) {
            return null;
        }

        return [];
    }

    private function canAccessZone(\App\Models\User $user, string $codeZone): bool
    {
        $zones = $this->resolveZoneScope($user);
        if ($zones === null) {
            return true;
        }
        return in_array($codeZone, $zones);
    }

    private function canAccessDemande(\App\Models\User $user, CreditDemande $dossier, bool $allowCreator = false): bool
    {
        if ($this->canAccessZone($user, $dossier->code_zone)) {
            return true;
        }

        if ($allowCreator && $user->agent?->matricule) {
            if ($dossier->agent_createur_matricule === $user->agent->matricule) {
                return true;
            }

            return $dossier->agent_analyse_matricule === $user->agent->matricule;
        }

        return false;
    }

    private function authorizeZoneAccess(CreditDemande $dossier): void
    {
        if (!$this->canAccessZone(Auth::user(), $dossier->code_zone)) {
            abort(403, 'Accès refusé à ce dossier crédit.');
        }
    }

    private function authorizeDemandeAccess(CreditDemande $dossier, bool $allowCreator = false): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !$this->canAccessDemande($user, $dossier, $allowCreator)) {
            abort(403, 'Accès refusé à ce dossier crédit.');
        }
    }

    private function resolveAssignableCreditAgents()
    {
        return Agent::query()
            ->whereIn('matricule', function ($q) {
                $q->select('u.agent_matricule')
                    ->from('users as u')
                    ->join('tb_role_user as ru', 'ru.user_id', '=', 'u.id')
                    ->join('tb_role_permission as rp', 'rp.role_code', '=', 'ru.role_code')
                    ->where('rp.permission_code', 'EBEN-PER58')
                    ->where('u.etat', 'actif')
                    ->whereNotNull('u.agent_matricule');
            })
            ->where('statut', 'actif')
            ->orderBy('nom')
            ->orderBy('postnom')
            ->orderBy('prenom')
            ->get(['matricule', 'nom', 'postnom', 'prenom']);
    }

    private function resolveDemandeurMeta(?string $matricule): ?array
    {
        if (empty($matricule)) {
            return null;
        }

        $user = DB::table('users as u')
            ->leftJoin('tb_agents as a', 'a.matricule', '=', 'u.agent_matricule')
            ->where('u.agent_matricule', $matricule)
            ->select(
                'u.id',
                'u.email',
                'u.agent_matricule',
                'a.nom',
                'a.postnom',
                'a.prenom'
            )
            ->first();

        if (!$user) {
            return [
                'matricule' => $matricule,
                'nom_complet' => $matricule,
                'role_code' => null,
                'role_nom' => null,
                'email' => null,
            ];
        }

        $role = DB::table('tb_role_user as ru')
            ->leftJoin('tb_roles as r', 'r.code', '=', 'ru.role_code')
            ->where('ru.user_id', $user->id)
            ->orderBy('ru.role_code')
            ->select('ru.role_code', 'r.nom as role_nom')
            ->first();

        $nomComplet = trim(implode(' ', array_filter([
            $user->nom ?? null,
            $user->postnom ?? null,
            $user->prenom ?? null,
        ])));

        if ($nomComplet === '') {
            $nomComplet = $user->email ?: $user->agent_matricule;
        }

        return [
            'matricule' => $user->agent_matricule,
            'nom_complet' => $nomComplet,
            'role_code' => $role->role_code ?? null,
            'role_nom' => $role->role_nom ?? null,
            'email' => $user->email,
        ];
    }

    private function isEligibleCreditAnalyst(string $matricule): bool
    {
        return DB::table('users as u')
            ->join('tb_role_user as ru', 'ru.user_id', '=', 'u.id')
            ->join('tb_role_permission as rp', 'rp.role_code', '=', 'ru.role_code')
            ->where('u.agent_matricule', $matricule)
            ->where('u.etat', 'actif')
            ->where('rp.permission_code', 'EBEN-PER58')
            ->exists();
    }

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
}
