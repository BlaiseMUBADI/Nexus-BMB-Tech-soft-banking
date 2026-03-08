<?php

namespace App\Http\Controllers\Tresorerie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaissesGuichet;
use App\Models\CaissesGuichetSolde;
use App\Models\ClotureCaisse;
use App\Models\MouvementInterCaisse;
use App\Models\Devise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * TresorerieController
 * --------------------
 * Gère le Coffre-Fort Central (COFFRE_01) :
 *   - Vue d'ensemble : soldes par devise
 *   - Approvisionnement externe (banque → coffre)
 *   - Historique complet des mouvements coffre (entrées + sorties)
 *
 * Permission requise : EBEN-PER44 (Voir trésorerie) — ROL1 Administrateur, ROL3 Directeur,
 *                       ROL5 Superviseur, ROL8 Trésorier
 */
class TresorerieController extends Controller
{
    /**
     * Page principale du coffre-fort.
     */
    public function index()
    {
        $coffre = CaissesGuichet::central()
            ->with(['soldes.devise'])
            ->firstOrFail();

        $devises  = Devise::orderBy('code_iso')->get();

        $aujourdHui = now()->toDateString();
        $stats      = $this->computeStats($coffre->id, $aujourdHui);

        return view('tresorerie.coffre', compact('coffre', 'stats'));
    }

    /**
     * Approvisionnement du coffre depuis une source externe (banque, capital).
     */
    public function approvisionner(Request $request)
    {
        $request->validate([
            'devise_code'  => 'required|exists:tb_devises,code_iso',
            'montant'      => 'required|numeric|min:1',
            'source'       => 'nullable|string|max:150',
            'observations' => 'nullable|string|max:255',
        ], [
            'devise_code.required' => 'Sélectionnez une devise.',
            'devise_code.exists'   => 'Devise invalide.',
            'montant.min'          => 'Le montant doit être supérieur à 0.',
        ]);

        $coffre = CaissesGuichet::central()->firstOrFail();

        $soldeCoffre = CaissesGuichetSolde::firstOrCreate(
            ['guichet_id' => $coffre->id, 'devise_code' => $request->devise_code],
            ['solde_en_caisse' => 0]
        );

        $observations = 'Approvisionnement externe';
        if ($request->source)       $observations .= ' — ' . $request->source;
        if ($request->observations) $observations .= ' : ' . $request->observations;

        try {
            DB::transaction(function () use ($request, $soldeCoffre, $coffre, $observations) {
                $reference = 'APP-' . now()->format('Ymd-His');

                MouvementInterCaisse::create([
                    'guichet_source_id'    => null,
                    'guichet_dest_id'      => $coffre->id,
                    'agent_initiateur'     => Auth::user()->agent_matricule,
                    'type_flux'            => 'ALIMENTATION',
                    'montant'              => $request->montant,
                    'devise_code'          => $request->devise_code,
                    'reference_bordereau'  => $reference,
                    'date_mouvement'       => now(),
                    'statut'               => 'CONFIRME',
                    'validateur_matricule' => Auth::user()->agent_matricule,
                    'observations'         => $observations,
                ]);

                $soldeCoffre->increment('solde_en_caisse', (float) $request->montant);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
            ], 500);
        }

        $nouveauSolde = (float) ($soldeCoffre->fresh()->solde_en_caisse ?? 0);

        return response()->json([
            'success'       => true,
            'message'       => 'Coffre approvisionné : +' . number_format($request->montant, 2, ',', ' ') . ' ' . $request->devise_code . '. Nouveau solde : ' . number_format($nouveauSolde, 2, ',', ' '),
            'nouveau_solde' => (float) $nouveauSolde,
            'devise_code'   => $request->devise_code,
        ]);
    }

    /**
     * Alimentation d'un guichet depuis le coffre (Inter-caisses).
     */
    public function alimenter(Request $request)
    {
        $request->validate([
            'guichet_id'   => 'required|exists:tb_caisses_guichets,id',
            'devise_code'  => 'required|exists:tb_devises,code_iso',
            'montant'      => 'required|numeric|min:1',
            'observations' => 'nullable|string|max:255',
        ], [
            'montant.min' => 'Le montant doit être supérieur à 0.',
        ]);

        $guichet = CaissesGuichet::with('soldes')->findOrFail($request->guichet_id);

        if ($guichet->statut_operationnel !== 'OUVERT') {
            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'alimenter : le guichet ' . $guichet->code_guichet .
                             ' est ' . $guichet->statut_operationnel . '. L\'agent doit d\'abord ouvrir sa session.',
            ], 422);
        }

        $solde = CaissesGuichetSolde::where('guichet_id', $request->guichet_id)
            ->where('devise_code', $request->devise_code)
            ->first();

        if (!$solde) {
            return response()->json([
                'success' => false,
                'message' => 'Le guichet ' . $guichet->code_guichet . ' ne gère pas la devise ' . $request->devise_code . '.',
            ], 422);
        }

        $coffre = CaissesGuichet::central()->first();
        $soldeCoffre = null;
        if ($coffre) {
            $soldeCoffre = CaissesGuichetSolde::where('guichet_id', $coffre->id)
                ->where('devise_code', $request->devise_code)
                ->first();

            if (!$soldeCoffre || $soldeCoffre->solde_en_caisse < $request->montant) {
                $disponible = $soldeCoffre ? number_format($soldeCoffre->solde_en_caisse, 2, ',', ' ') : '0,00';
                return response()->json([
                    'success' => false,
                    'message' => 'Fonds insuffisants dans le coffre. Disponible : ' . $disponible .
                                 ' ' . $request->devise_code . ' | Demandé : ' .
                                 number_format($request->montant, 2, ',', ' ') . ' ' . $request->devise_code,
                ], 422);
            }
        }

        try {
            DB::transaction(function () use ($request, $solde, $soldeCoffre, $guichet, $coffre) {
                $reference = 'ALI-' . now()->format('Ymd-His') . '-G' . str_pad($guichet->id, 2, '0', STR_PAD_LEFT);

                MouvementInterCaisse::create([
                    'guichet_source_id'    => $coffre ? $coffre->id : null,
                    'guichet_dest_id'      => $guichet->id,
                    'agent_initiateur'     => Auth::user()->agent_matricule,
                    'type_flux'            => 'ALIMENTATION',
                    'montant'              => $request->montant,
                    'devise_code'          => $request->devise_code,
                    'reference_bordereau'  => $reference,
                    'date_mouvement'       => now(),
                    'statut'               => 'CONFIRME',
                    'validateur_matricule' => Auth::user()->agent_matricule,
                    'observations'         => $request->observations,
                ]);

                if ($soldeCoffre) {
                    $soldeCoffre->decrement('solde_en_caisse', (float) $request->montant);
                }
                $solde->increment('solde_en_caisse', (float) $request->montant);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        $nouveauSoldeCoffre = $soldeCoffre ? (float) $soldeCoffre->fresh()->solde_en_caisse : null;

        return response()->json([
            'success'              => true,
            'message'              => 'Alimentation de ' . number_format($request->montant, 2, ',', ' ') . ' ' .
                                     $request->devise_code . ' effectuée sur ' . $guichet->code_guichet . '.',
            'solde_coffre_restant' => $nouveauSoldeCoffre,
            'devise_code'          => $request->devise_code,
        ]);
    }

    /**
     * Historique des alimentations inter-caisses (JSON).
     */
    public function alimentations()
    {
        $mouvements = MouvementInterCaisse::with(['guichetSource', 'guichetDest'])
            ->where('type_flux', 'ALIMENTATION')
            ->orderByDesc('date_mouvement')
            ->limit(150)
            ->get()
            ->map(function ($m) {
                return [
                    'id'             => $m->id,
                    'reference'      => $m->reference_bordereau,
                    'type_flux'      => $m->type_flux,
                    'montant'        => (float) $m->montant,
                    'devise_code'    => $m->devise_code,
                    'statut'         => $m->statut,
                    'guichet_source' => $m->guichetSource ? $m->guichetSource->code_guichet : 'Externe',
                    'guichet_dest'   => $m->guichetDest   ? $m->guichetDest->code_guichet   : '—',
                    'initiateur'     => $m->agent_initiateur,
                    'date'           => $m->date_mouvement ? $m->date_mouvement->format('d/m/Y H:i') : '—',
                    'observations'   => $m->observations,
                ];
            });

        return response()->json($mouvements);
    }

    /**
     * Historique AJAX des mouvements du coffre (entrées + sorties).
     */
    public function mouvements(Request $request)
    {
        $coffre = CaissesGuichet::central()->firstOrFail();

        $limit  = (int) ($request->get('limit', 100));
        $devise = $request->get('devise_code');

        $query = MouvementInterCaisse::where(function ($q) use ($coffre) {
                    $q->where('guichet_source_id', $coffre->id)
                      ->orWhere('guichet_dest_id', $coffre->id);
                })
                ->with(['guichetSource', 'guichetDest'])
                ->orderByDesc('date_mouvement')
                ->orderByDesc('id');

        if ($devise) {
            $query->where('devise_code', $devise);
        }

        $mouvements = $query->limit($limit)->get();

        return response()->json(
            $mouvements->map(function ($m) use ($coffre) {
                // Sens du mouvement par rapport au coffre
                $sens = ($m->guichet_dest_id === $coffre->id) ? 'ENTREE' : 'SORTIE';

                return [
                    'id'             => $m->id,
                    'reference'      => $m->reference_bordereau,
                    'type_flux'      => $m->type_flux,
                    'sens'           => $sens,
                    'montant'        => (float) $m->montant,
                    'devise_code'    => $m->devise_code,
                    'contrepartie'   => $sens === 'ENTREE'
                                        ? ($m->guichetSource?->code_guichet ?? 'Externe')
                                        : ($m->guichetDest?->code_guichet ?? '—'),
                    'statut'         => $m->statut,
                    'initiateur'     => $m->agent_initiateur,
                    'observations'   => $m->observations,
                    'date'           => $m->date_mouvement?->format('d/m/Y H:i'),
                ];
            })
        );
    }

    /**
     * Calcule les statistiques journalières par devise (réutilisable).
     */
    private function computeStats(int $coffreId, string $date): array
    {
        $mouvements = MouvementInterCaisse::where(function ($q) use ($coffreId) {
                $q->where('guichet_source_id', $coffreId)
                  ->orWhere('guichet_dest_id', $coffreId);
            })
            ->whereDate('date_mouvement', $date)
            ->where('statut', 'CONFIRME')
            ->select('devise_code', 'montant', 'guichet_source_id', 'guichet_dest_id')
            ->get();

        $parDevise = $mouvements->groupBy('devise_code')
            ->map(function ($items, $devise) use ($coffreId) {
                $entrees = $items->filter(fn ($m) => $m->guichet_dest_id == $coffreId);
                $sorties = $items->filter(fn ($m) => $m->guichet_source_id == $coffreId);
                return [
                    'devise_code'     => $devise,
                    'entrees_count'   => $entrees->count(),
                    'entrees_montant' => (float) $entrees->sum('montant'),
                    'sorties_count'   => $sorties->count(),
                    'sorties_montant' => (float) $sorties->sum('montant'),
                    'total'           => $items->count(),
                ];
            })->values()->toArray();

        return [
            'total_entrees'    => $mouvements->filter(fn ($m) => $m->guichet_dest_id == $coffreId)->count(),
            'total_sorties'    => $mouvements->filter(fn ($m) => $m->guichet_source_id == $coffreId)->count(),
            'total_mouvements' => $mouvements->count(),
            'par_devise'       => $parDevise,
        ];
    }

    /**
     * Statistiques journalières en JSON (pour mises à jour temps réel).
     */
    public function stats()
    {
        $coffre = CaissesGuichet::central()->firstOrFail();
        return response()->json($this->computeStats($coffre->id, now()->toDateString()));
    }

    /**
     * Soldes actuels du coffre en JSON (pour mises à jour temps réel).
     */
    public function balances()
    {
        $coffre = CaissesGuichet::central()->with('soldes.devise')->first();
        if (!$coffre) {
            return response()->json([]);
        }
        return response()->json(
            $coffre->soldes->map(fn($s) => [
                'devise_code' => $s->devise_code,
                'symbole'     => $s->devise?->symbole ?? $s->devise_code,
                'solde'       => (float) $s->solde_en_caisse,
            ])
        );
    }

    // ════════════════════════════════════════════════════════════════════════
    // DEMANDES D'APPROVISIONNEMENT
    // Utilisé directement tb_mouvements_inter_caisses
    // type_flux = 'DEMANDE_APPRO' | statut : EN_ATTENTE / CONFIRME / ANNULE
    // ════════════════════════════════════════════════════════════════════════

    /**
     * Nombre de demandes EN_ATTENTE (pour badge sidebar).
     */
    public function demandesCount()
    {
        return response()->json([
            'count' => MouvementInterCaisse::where('type_flux', 'DEMANDE_APPRO')
                            ->where('statut', 'EN_ATTENTE')
                            ->count(),
        ]);
    }

    /**
     * Liste JSON de toutes les demandes (EN_ATTENTE en premier).
     * Source : tb_mouvements_inter_caisses où type_flux = 'DEMANDE_APPRO'
     */
    public function demandesJson()
    {
        $demandes = MouvementInterCaisse::with(['guichetDest', 'agentInitiateur'])
            ->where('type_flux', 'DEMANDE_APPRO')
            ->orderByRaw("FIELD(statut, 'EN_ATTENTE', 'CONFIRME', 'ANNULE')")
            ->orderByDesc('date_mouvement')
            ->limit(200)
            ->get()
            ->map(function ($m) {
                $agent = $m->agentInitiateur;
                return [
                    'id'              => $m->id,
                    'reference'       => $m->reference_bordereau,
                    'guichet_code'    => $m->guichetDest?->code_guichet ?? '—',
                    'guichet_intitule'=> $m->guichetDest?->intitule ?? '—',
                    'agent_matricule' => $m->agent_initiateur,
                    'agent_nom'       => $agent ? trim($agent->prenom . ' ' . $agent->nom) : $m->agent_initiateur,
                    'devise_code'     => $m->devise_code,
                    'montant'         => (float) $m->montant,
                    'montant_fmt'     => number_format($m->montant, 2, ',', ' ') . ' ' . $m->devise_code,
                    'motif'           => $m->observations,
                    'statut'          => $m->statut,   // EN_ATTENTE | CONFIRME | ANNULE
                    'validateur'      => $m->validateur_matricule,
                    'date'            => $m->date_mouvement?->format('d/m/Y H:i'),
                ];
            });

        return response()->json($demandes);
    }

    /**
     * Approuver une demande :
     *   - Vérifie solde coffre
     *   - Débite coffre, crédite guichet
     *   - Passe le mouvement de EN_ATTENTE → CONFIRME
     */
    public function approuverDemande(Request $request, $id)
    {
        $request->validate(['observations' => 'nullable|string|max:255']);

        $demande = MouvementInterCaisse::where('type_flux', 'DEMANDE_APPRO')
            ->where('statut', 'EN_ATTENTE')
            ->findOrFail($id);

        $coffre = CaissesGuichet::central()->firstOrFail();

        $soldeCoffre = CaissesGuichetSolde::where('guichet_id', $coffre->id)
            ->where('devise_code', $demande->devise_code)
            ->first();

        if (!$soldeCoffre || $soldeCoffre->solde_en_caisse < $demande->montant) {
            $dispo = $soldeCoffre ? number_format($soldeCoffre->solde_en_caisse, 2, ',', ' ') : '0,00';
            return response()->json([
                'success' => false,
                'message' => 'Fonds insuffisants dans le coffre. Disponible : ' . $dispo . ' ' . $demande->devise_code,
            ], 422);
        }

        $soldeGuichet = CaissesGuichetSolde::where('guichet_id', $demande->guichet_dest_id)
            ->where('devise_code', $demande->devise_code)
            ->first();

        if (!$soldeGuichet) {
            return response()->json([
                'success' => false,
                'message' => 'Le guichet ne gère pas la devise ' . $demande->devise_code . '.',
            ], 422);
        }

        try {
            DB::transaction(function () use ($demande, $coffre, $soldeCoffre, $soldeGuichet, $request) {
                // Met à jour le mouvement existant (EN_ATTENTE → CONFIRME)
                $demande->update([
                    'guichet_source_id'    => $coffre->id,   // on fixe le coffre comme source
                    'statut'               => 'CONFIRME',
                    'validateur_matricule' => Auth::user()->agent_matricule,
                    'observations'         => ($demande->observations ? $demande->observations . ' | ' : '')
                                             . 'Approuvé' . ($request->observations ? ' : ' . $request->observations : ''),
                ]);

                $soldeCoffre->decrement('solde_en_caisse', (float) $demande->montant);
                $soldeGuichet->increment('solde_en_caisse', (float) $demande->montant);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        $guichetCode = $demande->guichetDest?->code_guichet ?? 'le guichet';
        return response()->json([
            'success' => true,
            'message' => 'Demande #' . $demande->id . ' approuvée. '
                       . number_format((float) $demande->montant, 2, ',', ' ') . ' ' . $demande->devise_code
                       . ' transférés vers ' . $guichetCode . '.',
        ]);
    }

    /**
     * Rejeter une demande : EN_ATTENTE → ANNULE.
     */
    public function rejeterDemande(Request $request, $id)
    {
        $request->validate([
            'observations' => 'required|string|max:255',
        ], [
            'observations.required' => 'Veuillez indiquer le motif du rejet.',
        ]);

        $demande = MouvementInterCaisse::where('type_flux', 'DEMANDE_APPRO')
            ->where('statut', 'EN_ATTENTE')
            ->findOrFail($id);

        $demande->update([
            'statut'               => 'ANNULE',
            'validateur_matricule' => Auth::user()->agent_matricule,
            'observations'         => ($demande->observations ? $demande->observations . ' | ' : '')
                                     . 'Rejeté : ' . $request->observations,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Demande #' . $demande->id . ' rejetée.',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // GESTION CLÔTURES GUICHETS (Double contrôle fin de journée)
    // ══════════════════════════════════════════════════════════════════

    /**
     * Retourne JSON des guichets EN_VERIFICATION en attente superviseur.
     */
    public function cloturesEnVerification()
    {
        $guichets = CaissesGuichet::with(['soldes.devise', 'affectations' => function ($q) {
                $q->where('Etat', 'ACTIF')->with('agent');
            }])
            ->where('statut_operationnel', 'EN_VERIFICATION')
            ->orderBy('updated_at')
            ->get()
            ->map(function ($g) {
                // Toutes les lignes de clôture (EN_ATTENTE + déjà traitées)
                // pour affichage complet dans la carte superviseur
                $clotures   = ClotureCaisse::where('guichet_id', $g->id)
                    ->whereIn('statut_validation', [
                        ClotureCaisse::VALIDATION_EN_ATTENTE,
                        ClotureCaisse::VALIDATION_VALIDE,
                        ClotureCaisse::VALIDATION_REJETE,
                    ])
                    ->orderBy('date_cloture')
                    ->get();

                $pendingCount = $clotures->where('statut_validation', ClotureCaisse::VALIDATION_EN_ATTENTE)->count();

                $agentActif = $g->affectations->first();
                $agentNom   = $agentActif && $agentActif->agent
                    ? ($agentActif->agent->prenoms . ' ' . $agentActif->agent->nom)
                    : ($clotures->first()?->agent_cloturant ?? 'Inconnu');

                $montants = $clotures->map(fn($c) => [
                    'cloture_id'       => $c->id,
                    'devise_code'      => $c->devise_code,
                    'solde_physique'   => number_format($c->solde_physique, 2, ',', ' ') . ' ' . $c->devise_code,
                    'solde_comptable'  => number_format($c->solde_comptable, 2, ',', ' ') . ' ' . $c->devise_code,
                    'ecart'            => number_format($c->ecart_caisse, 2, ',', ' ') . ' ' . $c->devise_code,
                    'statut_ecart'     => $c->statut_ecart,
                    'statut_validation'=> $c->statut_validation,
                    'motif_ecart'      => $c->motif_ecart,
                    'date'             => $c->date_cloture?->format('d/m/Y H:i'),
                ]);

                return [
                    'guichet_id'    => $g->id,
                    'code_guichet'  => $g->code_guichet,
                    'intitule'      => $g->intitule,
                    'agent_nom'     => $agentNom,
                    'agent_matric'  => $agentActif?->agent_matricule ?? $clotures->first()?->agent_cloturant,
                    'montants'      => $montants,
                    'nb_lignes'     => $clotures->count(),
                    'pending_count' => $pendingCount,
                ];
            })
            ->filter(fn($g) => $g['pending_count'] > 0)
            ->values();

        return response()->json($guichets);
    }

    /**
     * Valider la clôture — superviseur.
     * 1. Marque ClotureCaisse → VALIDE.
     * 2. Crée mouvement DEGAGEMENT Guichet → Coffre.
     * 3. Solde guichet → 0. Coffre += montant physique.
     * 4. Guichet → FERME.
     */
    public function approuverCloture(Request $request, $guichetId)
    {
        $request->validate(['observations' => 'nullable|string|max:500']);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = CaissesGuichet::with('soldes')->findOrFail($guichetId);

        if ($guichet->statut_operationnel !== 'EN_VERIFICATION') {
            return response()->json(['success' => false, 'message' => "Ce guichet n'est pas en attente de vérification."], 422);
        }

        $clotures = ClotureCaisse::where('guichet_id', $guichetId)
            ->where('statut_validation', ClotureCaisse::VALIDATION_EN_ATTENTE)
            ->get();

        if ($clotures->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Aucune clôture en attente pour ce guichet.'], 422);
        }

        $coffre = CaissesGuichet::central()->firstOrFail();

        try {
            DB::transaction(function () use ($clotures, $guichet, $coffre, $user, $request) {
                $reference = 'DEG-' . now()->format('Ymd-His') . '-' . $guichet->code_guichet;

                foreach ($clotures as $cloture) {
                    /** @var \App\Models\ClotureCaisse $cloture */
                    // 1. Valider la ligne
                    $cloture->update([
                        'statut_validation'       => ClotureCaisse::VALIDATION_VALIDE,
                        'validateur_matricule'     => $user->agent_matricule,
                        'date_validation'          => now(),
                        'observations_superviseur' => $request->observations,
                    ]);

                    // 2. Dégagement Guichet → Coffre
                    MouvementInterCaisse::create([
                        'guichet_source_id'   => $guichet->id,
                        'guichet_dest_id'     => $coffre->id,
                        'agent_initiateur'    => $user->agent_matricule,
                        'type_flux'           => 'DEGAGEMENT',
                        'montant'             => $cloture->solde_physique,
                        'devise_code'         => $cloture->devise_code,
                        'reference_bordereau' => $reference,
                        'date_mouvement'      => now(),
                        'statut'              => 'CONFIRME',
                        'observations'        => 'Dégagement automatique clôture ' . $guichet->code_guichet,
                        'validateur_matricule'=> $user->agent_matricule,
                    ]);

                    // 3. Soldes : guichet → 0 / coffre += physique
                    $soldeSrc = $guichet->soldes->where('devise_code', $cloture->devise_code)->first();
                    if ($soldeSrc) {
                        $soldeSrc->solde_en_caisse = 0;
                        $soldeSrc->save();
                    }

                    $soldeDst = CaissesGuichetSolde::firstOrCreate(
                        ['guichet_id' => $coffre->id, 'devise_code' => $cloture->devise_code],
                        ['solde_en_caisse' => 0]
                    );
                    $soldeDst->increment('solde_en_caisse', (float) $cloture->solde_physique);
                }

                // 4. Guichet → FERME
                $guichet->statut_operationnel = 'FERME';
                $guichet->save();
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Clôture validée. Guichet ' . $guichet->code_guichet . ' fermé, fonds transférés au coffre.',
        ]);
    }

    /**
     * Rejeter la clôture — superviseur.
     * Le guichet repasse en OUVERT pour correction.
     */
    public function rejeterCloture(Request $request, $guichetId)
    {
        $request->validate([
            'observations' => 'required|string|max:500',
        ], [
            'observations.required' => 'Le motif du rejet est obligatoire.',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = CaissesGuichet::findOrFail($guichetId);

        if ($guichet->statut_operationnel !== 'EN_VERIFICATION') {
            return response()->json(['success' => false, 'message' => "Ce guichet n'est pas en attente de vérification."], 422);
        }

        try {
            DB::transaction(function () use ($guichetId, $guichet, $user, $request) {
                ClotureCaisse::where('guichet_id', $guichetId)
                    ->where('statut_validation', ClotureCaisse::VALIDATION_EN_ATTENTE)
                    ->update([
                        'statut_validation'       => ClotureCaisse::VALIDATION_REJETE,
                        'validateur_matricule'     => $user->agent_matricule,
                        'date_validation'          => now(),
                        'observations_superviseur' => $request->observations,
                    ]);

                // Guichet repasse en OUVERT
                $guichet->statut_operationnel = 'OUVERT';
                $guichet->save();
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Clôture rejetée. Guichet " . $guichet->code_guichet . " remis en OUVERT pour correction.",
        ]);
    }

    /**
     * Valider UNE ligne de clôture (1 devise).
     * Si toutes les devises du guichet sont validées → guichet FERME.
     */
    public function approuverLigneCloture(Request $request, $clotureId)
    {
        $request->validate(['observations' => 'nullable|string|max:500']);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $cloture = ClotureCaisse::findOrFail($clotureId);

        if ($cloture->statut_validation !== ClotureCaisse::VALIDATION_EN_ATTENTE) {
            return response()->json(['success' => false, 'message' => 'Cette ligne a déjà été traitée.'], 422);
        }

        $guichet = CaissesGuichet::with('soldes')->findOrFail($cloture->guichet_id);
        $coffre  = CaissesGuichet::central()->firstOrFail();

        try {
            DB::transaction(function () use ($cloture, $guichet, $coffre, $user, $request) {
                $reference = 'DEG-' . now()->format('Ymd-His') . '-' . $guichet->code_guichet . '-' . $cloture->devise_code;

                // 1. Valider la ligne
                $cloture->update([
                    'statut_validation'        => ClotureCaisse::VALIDATION_VALIDE,
                    'validateur_matricule'      => $user->agent_matricule,
                    'date_validation'           => now(),
                    'observations_superviseur'  => $request->observations,
                ]);

                // 2. Dégagement de la devise concernée
                MouvementInterCaisse::create([
                    'guichet_source_id'    => $guichet->id,
                    'guichet_dest_id'      => $coffre->id,
                    'agent_initiateur'     => $user->agent_matricule,
                    'type_flux'            => 'DEGAGEMENT',
                    'montant'              => $cloture->solde_physique,
                    'devise_code'          => $cloture->devise_code,
                    'reference_bordereau'  => $reference,
                    'date_mouvement'       => now(),
                    'statut'               => 'CONFIRME',
                    'observations'         => 'Dégagement ' . $cloture->devise_code . ' — clôture ' . $guichet->code_guichet,
                    'validateur_matricule'  => $user->agent_matricule,
                ]);

                // 3. Guichet solde → 0 pour cette devise
                $soldeSrc = $guichet->soldes->where('devise_code', $cloture->devise_code)->first();
                if ($soldeSrc) {
                    $soldeSrc->solde_en_caisse = 0;
                    $soldeSrc->save();
                }

                // 4. Coffre += montant physique pour cette devise
                $soldeDst = CaissesGuichetSolde::firstOrCreate(
                    ['guichet_id' => $coffre->id, 'devise_code' => $cloture->devise_code],
                    ['solde_en_caisse' => 0]
                );
                $soldeDst->increment('solde_en_caisse', (float) $cloture->solde_physique);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        // Après la transaction : vérifier s'il reste des devises EN_ATTENTE
        $pendingCount = ClotureCaisse::where('guichet_id', $guichet->id)
            ->where('statut_validation', ClotureCaisse::VALIDATION_EN_ATTENTE)
            ->count();

        $guichetFerme = false;
        if ($pendingCount === 0) {
            $guichet->statut_operationnel = 'FERME';
            $guichet->save();
            $guichetFerme = true;
            $message = 'Devise ' . $cloture->devise_code . ' validée. Toutes les devises traitées — Guichet ' . $guichet->code_guichet . ' fermé.';
        } else {
            $message = 'Devise ' . $cloture->devise_code . ' validée. ' . $pendingCount . ' devise(s) restante(s) à valider.';
        }

        return response()->json([
            'success'       => true,
            'message'       => $message,
            'guichet_ferme' => $guichetFerme,
            'pending_count' => $pendingCount,
        ]);
    }

    /**
     * Rejeter UNE ligne de clôture (1 devise).
     * Le guichet repasse immédiatement en OUVERT pour correction.
     */
    public function rejeterLigneCloture(Request $request, $clotureId)
    {
        $request->validate([
            'observations' => 'required|string|max:500',
        ], [
            'observations.required' => 'Le motif du rejet est obligatoire.',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $cloture = ClotureCaisse::findOrFail($clotureId);

        if ($cloture->statut_validation !== ClotureCaisse::VALIDATION_EN_ATTENTE) {
            return response()->json(['success' => false, 'message' => 'Cette ligne a déjà été traitée.'], 422);
        }

        $guichet = CaissesGuichet::findOrFail($cloture->guichet_id);

        try {
            DB::transaction(function () use ($cloture, $guichet, $user, $request) {
                // Rejeter TOUTES les lignes EN_ATTENTE de ce guichet
                ClotureCaisse::where('guichet_id', $guichet->id)
                    ->where('statut_validation', ClotureCaisse::VALIDATION_EN_ATTENTE)
                    ->update([
                        'statut_validation'        => ClotureCaisse::VALIDATION_REJETE,
                        'validateur_matricule'      => $user->agent_matricule,
                        'date_validation'           => now(),
                        'observations_superviseur'  => '[Rejet ' . $cloture->devise_code . '] ' . $request->observations,
                    ]);

                // Guichet → OUVERT pour correction
                $guichet->statut_operationnel = 'OUVERT';
                $guichet->save();
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Devise ' . $cloture->devise_code . ' rejetée. Guichet ' . $guichet->code_guichet . ' remis en OUVERT pour correction.',
        ]);
    }

    /**
     * Compteur : nombre de guichets EN_VERIFICATION (pour badge sidebar).
     */
    public function cloturesCount()
    {
        $count = CaissesGuichet::where('statut_operationnel', 'EN_VERIFICATION')->count();
        return response()->json(['count' => $count]);
    }
}
