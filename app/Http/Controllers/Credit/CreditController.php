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
use App\Models\Zone;
use App\Models\Tresorerie\Portefeuille;
use App\Services\Credit\AmortissementService;
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

        // Scope zone
        $zonesCodes = $this->resolveZoneScope($user);

        $query = CreditDemande::when($zonesCodes !== null, fn($q) => $q->whereIn('code_zone', $zonesCodes));

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
        $user       = Auth::user();
        $zonesCodes = $this->resolveZoneScope($user);

        $query = CreditDemande::with(['client', 'zone'])
            ->when($zonesCodes !== null, fn($q) => $q->whereIn('code_zone', $zonesCodes));

        // Filtres
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
        $user       = Auth::user();
        $zonesCodes = $this->resolveZoneScope($user);

        $clients = Client::when($zonesCodes !== null, fn($q) => $q->whereIn('code_zone', $zonesCodes))
            ->orderBy('nom')->get();

        $zones = Zone::when($zonesCodes !== null, fn($q) => $q->whereIn('code_zone', $zonesCodes))
            ->orderBy('nom')->get();

        return view('credit.creation', compact('clients', 'zones'));
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
            'compte_id'            => 'required|string|exists:tb_comptes,code_compte',
            'montant_demande'      => 'required|numeric|min:1',
            'devise'               => 'required|in:CDF,USD,EUR',
            'duree_mois'           => 'required|integer|min:1|max:360',
            'taux_interet_mensuel' => 'required|numeric|min:0.01|max:100',
            'type_credit'          => 'required|in:INDIVIDUEL,SOLIDAIRE,PME',
            'objet_credit'         => 'required|string|max:500',
            'garantie_description' => 'nullable|string',
        ]);

        $user  = Auth::user();
        $agent = $user->agent;

        // Récupérer zone et portefeuille depuis le compte
        $compte = Compte::with('portefeuille')->findOrFail($validated['compte_id']);
        $client = Client::findOrFail($validated['client_matricule']);

        if (!$this->canAccessZone($user, $client->code_zone)) {
            abort(403, 'Accès refusé à cette zone.');
        }

        $calcul = $this->amortissement->calculer(
            (float) $validated['montant_demande'],
            (float) $validated['taux_interet_mensuel'],
            (int)   $validated['duree_mois']
        );

        $demande = DB::transaction(function () use ($validated, $compte, $client, $agent, $calcul) {
            $demande = CreditDemande::create([
                'client_matricule'        => $validated['client_matricule'],
                'compte_id'               => $validated['compte_id'],
                'portefeuille_id'         => $compte->portefeuille_id,
                'code_zone'               => $client->code_zone,
                'agent_createur_matricule'=> $agent?->matricule ?? 'SYSTEM',
                'montant_demande'         => $validated['montant_demande'],
                'devise'                  => $validated['devise'],
                'duree_mois'              => $validated['duree_mois'],
                'taux_interet_mensuel'    => $validated['taux_interet_mensuel'],
                'type_credit'             => $validated['type_credit'],
                'objet_credit'            => $validated['objet_credit'],
                'garantie_description'    => $validated['garantie_description'],
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
            $blocs = [
                ['type_validateur' => 'AGENT_CREDIT',      'ordre_etape' => 1],
                ['type_validateur' => 'CHARGE_OPERATIONS', 'ordre_etape' => 2],
                ['type_validateur' => 'CONTROLEUR',        'ordre_etape' => 3],
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
        $this->authorizeZoneAccess($dossier);

        $dossier->load([
            'client', 'compte', 'zone', 'portefeuille',
            'analyse', 'validations', 'pieces',
            'deblocage', 'echeancier.echeances',
            'remboursements', 'audits',
        ]);

        $demande = $dossier;
        return view('credit.show', compact('dossier', 'demande'));
    }

    // ================================================================
    // SOUMISSION
    // ================================================================

    public function soumettre(CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

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

        if (!in_array($dossier->statut_global, ['SOUMIS','EN_ANALYSE'])) {
            return back()->with('error', 'Ce dossier ne peut pas être analysé dans son état actuel.');
        }

        $dossier->load(['client','analyse']);
        $demande = $dossier;
        return view('credit.analyse', compact('dossier', 'demande'));
    }

    public function storeAnalyse(Request $request, CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

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

        $user  = Auth::user();
        $agent = $user->agent;

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

        if ($dossier->statut_global !== 'EN_VALIDATION') {
            return back()->with('error', 'Ce dossier n\'est pas en phase de validation.');
        }

        $dossier->load(['client','analyse','validations']);
        $demande = $dossier;
        return view('credit.validation', compact('dossier', 'demande'));
    }

    public function storeValidation(Request $request, CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        $validated = $request->validate([
            'type_validateur' => 'required|in:AGENT_CREDIT,CHARGE_OPERATIONS,CONTROLEUR,GERANT',
            'decision'        => 'required|in:APPROUVE,APPROUVE_AVEC_RESERVE,REJETE',
            'montant_valide'  => 'nullable|numeric|min:0',
            'observations'    => 'nullable|string',
            'conditions'      => 'nullable|string',
        ]);

        $user  = Auth::user();
        $agent = $user->agent;

        DB::transaction(function () use ($validated, $dossier, $agent) {
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

            $validation->update([
                'validateur_matricule' => $agent?->matricule ?? 'SYSTEM',
                'decision'             => $validated['decision'],
                'montant_valide'       => $validated['montant_valide'],
                'observations'         => $validated['observations'],
                'conditions'           => $validated['conditions'],
                'valide_le'            => now(),
            ]);

            if ($validated['decision'] === 'REJETE') {
                $dossier->update(['statut_global' => 'ANNULE', 'est_annule' => true,
                    'motif_annulation' => 'Rejeté lors de la validation par '.$validated['type_validateur'],
                    'annule_par_matricule' => $agent?->matricule,
                    'annule_le' => now()]);
                $this->logAudit($dossier, 'REJET', $ancien, 'ANNULE');
                return;
            }

            // Activer le bloc suivant
            $prochainOrdre = $validation->ordre_etape + 1;
            $prochaineValidation = $dossier->validations()->where('ordre_etape', $prochainOrdre)->first();

            if ($prochaineValidation) {
                $prochaineValidation->update(['etape_precedente_ok' => true]);
                $this->logAudit($dossier, 'VALIDATION_PARTIELLE', $ancien, 'EN_VALIDATION',
                    "Bloc {$validated['type_validateur']} validé.");
            } else {
                // Tous les blocs sont validés
                if ($validated['montant_valide']) {
                    $dossier->update(['montant_approuve' => $validated['montant_valide']]);
                }
                $dossier->update(['statut_global' => 'PRET_A_DEBLOQUER']);
                $this->logAudit($dossier, 'VALIDATION_COMPLETE', $ancien, 'PRET_A_DEBLOQUER');
            }
        });

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

        // Comptes disponibles pour le débit (caisse / coffre central)
        $comptesDebit = Compte::whereIn('type', ['GTC','CC'])
            ->where('solde_reel', '>', 0)
            ->get(['code_compte','type','devise','solde_reel']);

        $demande = $dossier;
        $comptesClient = Compte::where('code_compte', $dossier->compte_id)
            ->get(['code_compte','type as type_compte','devise','solde_reel']);

        return view('credit.deblocage', compact('dossier', 'demande', 'comptesDebit', 'comptesClient'));
    }

    public function storeDeblocage(Request $request, CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        if (!$dossier->peutEtreDebloque()) {
            return back()->with('error', 'Les conditions de déblocage ne sont pas remplies.');
        }

        $validated = $request->validate([
            'compte_debit_id'           => 'required|string|exists:tb_comptes,code_compte',
            'date_premier_remboursement' => 'required|date|after:today',
            'frais_dossier'             => 'nullable|numeric|min:0',
            'observations'              => 'nullable|string',
        ]);

        $user   = Auth::user();
        $agent  = $user->agent;
        $montant = (float)($dossier->montant_approuve ?? $dossier->montant_demande);
        $frais   = (float)($validated['frais_dossier'] ?? 0);

        DB::transaction(function () use ($dossier, $validated, $agent, $montant, $frais) {
            $ancien = $dossier->statut_global;

            CreditDeblocage::create([
                'credit_demande_id'    => $dossier->id,
                'agent_matricule'      => $agent?->matricule ?? 'SYSTEM',
                'compte_debit_id'      => $validated['compte_debit_id'],
                'compte_credit_id'     => $dossier->compte_id,
                'montant_debloque'     => $montant,
                'devise'               => $dossier->devise,
                'frais_dossier'        => $frais,
                'montant_net_verse'    => $montant - $frais,
                'observations'         => $validated['observations'],
                'debloque_le'          => now(),
            ]);

            // Générer l'échéancier
            $datePremier = Carbon::parse($validated['date_premier_remboursement']);
            $this->amortissement->genererEtSauvegarder($dossier, $datePremier);

            $dossier->update(['statut_global' => 'DEBLOQUE']);
            $this->logAudit($dossier, 'DEBLOCAGE', $ancien, 'DEBLOQUE',
                "Montant débloqué : {$montant} {$dossier->devise}");
        });

        return redirect()->route('credit.show', $dossier)
            ->with('success', "Déblocage de {$dossier->numero_dossier} effectué. Échéancier généré.");
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
                'recu_le'            => now(),
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
                        'date_paiement_effectif' => now()->toDateString(),
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

        return $pdf->download($filename);
    }

    // ================================================================
    // PDF FICHE CRÉDIT
    // ================================================================

    public function pdfFiche(CreditDemande $dossier)
    {
        $this->authorizeZoneAccess($dossier);

        $dossier->load(['client','compte','zone','analyse','validations','pieces','deblocage','echeancier.echeances']);

        $demande = $dossier;

        $pdf = Pdf::loadView('impressions.credit.fiche_credit', compact('dossier', 'demande'))
            ->setPaper('A4', 'portrait');

        $filename = 'fiche_credit_' . $dossier->numero_dossier . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    // ================================================================
    // HELPERS PRIVÉS
    // ================================================================

    private function resolveZoneScope(\App\Models\User $user): ?array
    {
        if ($user->hasPermission('EBEN-PER1')) {
            return null; // Admin : toutes les zones
        }

        $agent = $user->agent;
        if (!$agent) {
            return []; // Aucune zone
        }

        $zones = Zone::where('agent_commercial_matricule', $agent->matricule)
            ->pluck('code_zone')
            ->toArray();

        return empty($zones) ? [] : $zones;
    }

    private function canAccessZone(\App\Models\User $user, string $codeZone): bool
    {
        $zones = $this->resolveZoneScope($user);
        if ($zones === null) {
            return true;
        }
        return in_array($codeZone, $zones);
    }

    private function authorizeZoneAccess(CreditDemande $dossier): void
    {
        if (!$this->canAccessZone(Auth::user(), $dossier->code_zone)) {
            abort(403, 'Accès refusé à ce dossier crédit.');
        }
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
