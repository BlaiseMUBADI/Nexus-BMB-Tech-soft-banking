<?php

namespace App\Http\Controllers\Tresorerie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Caisse\CaissesGuichet;
use App\Models\Caisse\CaissesGuichetSolde;
use App\Models\Caisse\ClotureCaisse;
use App\Models\Caisse\MouvementInterCaisse;
use App\Models\Tresorerie\CommissionRule;
use App\Models\Tresorerie\Devise;
use App\Models\Tresorerie\Portefeuille;
use App\Models\Zone;
use App\Services\Notifications\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
    private function commissionRuleValidation(Request $request): array
    {
        return $request->validate([
            'libelle' => 'required|string|max:150',
            'code_operation' => ['required', Rule::in(CommissionRule::operationChoices())],
            'type_compte' => ['required', Rule::in(CommissionRule::accountTypeChoices())],
            'type_guichet' => ['required', Rule::in(CommissionRule::guichetTypeChoices())],
            'devise_code' => 'nullable|exists:tb_devises,code_iso',
            'code_zone' => 'nullable|exists:tb_zones,code_zone',
            'portefeuille_id' => 'nullable|exists:tb_portefeuilles_agents,id',
            'montant_min' => 'nullable|numeric|min:0',
            'montant_max' => 'nullable|numeric|gte:montant_min',
            'mode_calcul' => ['required', Rule::in([CommissionRule::MODE_FIXED, CommissionRule::MODE_PERCENTAGE])],
            'valeur' => 'required|numeric|min:0',
            'priorite' => 'required|integer|min:1|max:9999',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'observations' => 'nullable|string|max:1000',
        ], [
            'montant_max.gte' => 'Le montant maximum doit être supérieur ou égal au minimum.',
        ]);
    }

    private function commissionRulePayload(array $validated): array
    {
        return [
            'libelle' => $validated['libelle'],
            'code_operation' => $validated['code_operation'],
            'type_compte' => $validated['type_compte'],
            'type_guichet' => $validated['type_guichet'],
            'devise_code' => $validated['devise_code'] ?? null,
            'code_zone' => $validated['code_zone'] ?? null,
            'portefeuille_id' => $validated['portefeuille_id'] ?? null,
            'montant_min' => $validated['montant_min'] ?? null,
            'montant_max' => $validated['montant_max'] ?? null,
            'mode_calcul' => $validated['mode_calcul'],
            'valeur' => $validated['valeur'],
            'priorite' => $validated['priorite'],
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'] ?? null,
            'observations' => $validated['observations'] ?? null,
        ];
    }

    public function commissions(Request $request)
    {
        $rules = CommissionRule::with(['devise', 'portefeuille.agent', 'portefeuille.affectationActive.agent'])
            ->orderByDesc('est_actif')
            ->orderByDesc('priorite')
            ->orderByDesc('date_debut')
            ->orderByDesc('id')
            ->get();

        $editingRule = $request->filled('edit')
            ? CommissionRule::find($request->integer('edit'))
            : null;

        $devises = Devise::orderBy('code_iso')->get(['code_iso', 'nom', 'symbole']);
        $zones = Zone::orderBy('nom')->get(['code_zone', 'nom']);
        $portefeuilles = Portefeuille::with(['agent', 'affectationActive.agent'])->orderBy('nom_portefeuille')->get();

        $stats = [
            'total' => $rules->count(),
            'actives' => $rules->where('est_actif', true)->count(),
            'fixes' => $rules->where('mode_calcul', CommissionRule::MODE_FIXED)->count(),
            'pourcentages' => $rules->where('mode_calcul', CommissionRule::MODE_PERCENTAGE)->count(),
        ];

        $nextPriority = ((int) CommissionRule::max('priorite')) + 10;
        if ($nextPriority <= 0) {
            $nextPriority = 100;
        }

        return view('tresorerie.commissions', [
            'rules' => $rules,
            'editingRule' => $editingRule,
            'devises' => $devises,
            'zones' => $zones,
            'portefeuilles' => $portefeuilles,
            'operationChoices' => CommissionRule::operationChoices(),
            'accountTypeChoices' => CommissionRule::accountTypeChoices(),
            'guichetTypeChoices' => CommissionRule::guichetTypeChoices(),
            'modeChoices' => [CommissionRule::MODE_FIXED, CommissionRule::MODE_PERCENTAGE],
            'stats' => $stats,
            'nextPriority' => $nextPriority,
        ]);
    }

    public function storeCommission(Request $request)
    {
        $validated = $this->commissionRuleValidation($request);

        CommissionRule::create($this->commissionRulePayload($validated) + [
            'est_actif' => $request->boolean('est_actif', true),
            'created_by_agent' => Auth::user()?->agent_matricule,
        ]);

        return redirect()
            ->route('tresorerie.commissions.index')
            ->with('success', 'Règle de commission ajoutée avec succès.');
    }

    public function updateCommission(Request $request, CommissionRule $commissionRule)
    {
        $validated = $this->commissionRuleValidation($request);

        $commissionRule->update($this->commissionRulePayload($validated) + [
            'est_actif' => $request->boolean('est_actif', false),
        ]);

        return redirect()
            ->route('tresorerie.commissions.index')
            ->with('success', 'Règle de commission mise à jour.');
    }

    public function toggleCommission(CommissionRule $commissionRule)
    {
        $commissionRule->update([
            'est_actif' => !$commissionRule->est_actif,
        ]);

        return redirect()
            ->route('tresorerie.commissions.index')
            ->with('success', $commissionRule->est_actif
                ? 'Règle activée.'
                : 'Règle désactivée.');
    }

    /**
     * Récupère le coffre central avec logging en cas d'absence.
     * Retourne null si introuvable (au lieu de 404).
     */
    private function getCoffreCentral(string $caller = '')
    {
        $coffre = CaissesGuichet::central()->with(['soldes.devise'])->first();

        if (!$coffre) {
            Log::error('[Trésorerie] Coffre central introuvable (type_guichet=CENTRAL absent de tb_caisses_guichets)', [
                'methode'    => $caller ?: debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? 'unknown',
                'user'       => Auth::id(),
                'ip'         => request()->ip(),
                'url'        => request()->fullUrl(),
                'timestamp'  => now()->toDateTimeString(),
            ]);
        }

        return $coffre;
    }

    /**
     * Référence de mouvement robuste (évite collisions en forte volumétrie).
     */
    private function buildReference(string $prefix, string $suffix = ''): string
    {
        try {
            $rand = strtoupper(str_pad(dechex(random_int(0, 65535)), 4, '0', STR_PAD_LEFT));
        } catch (\Exception $e) {
            $rand = strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 4));
        }

        $core = now()->format('Ymd-His-u') . '-' . $rand;
        return $suffix !== '' ? $prefix . '-' . $core . '-' . $suffix : $prefix . '-' . $core;
    }

    /**
     * Page principale du coffre-fort.
     */
    public function etat_coffre()
    {
        $coffre = $this->getCoffreCentral('etat_coffre');

        if (!$coffre) {
            return view('tresorerie.etat_coffre', [
                'coffre' => null,
                'stats'  => [
                    'total_entrees'    => 0,
                    'total_sorties'    => 0,
                    'total_mouvements' => 0,
                    'par_devise'       => [],
                ],
            ]);
        }

        $aujourdHui = now()->toDateString();
        $stats      = $this->computeStats($coffre->id, $aujourdHui);

        return view('tresorerie.etat_coffre', compact('coffre', 'stats'));
    }

    /**
     * Interface dédiée aux opérations d'approvisionnement / inter-caisses.
     */
    public function interfaceApprovisionnement(Request $request)
    {
        $coffre = $this->getCoffreCentral('interfaceApprovisionnement');
        $devises = Devise::orderBy('code_iso')->get(['code_iso', 'nom', 'symbole']);
        $guichetsAlimentables = CaissesGuichet::operationnels()
            ->with(['affectationActive.agent'])
            ->orderBy('code_guichet')
            ->get();

        $module = $request->routeIs('tresorerie.intercaisse') ? 'intercaisse' : 'approvisionnement';

        return view('tresorerie.approvisionnement', compact('coffre', 'devises', 'guichetsAlimentables', 'module'));
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

        $coffre = $this->getCoffreCentral('approvisionner');
        if (!$coffre) {
            return response()->json(['success' => false, 'message' => 'Coffre central introuvable. Contactez l\'administrateur.'], 500);
        }

        if ($coffre->statut_operationnel !== 'OUVERT') {
            return response()->json([
                'success' => false,
                'message' => 'Le coffre central est ' . $coffre->statut_operationnel . '. Approvisionnement bloqué tant qu\'il n\'est pas OUVERT.',
            ], 422);
        }

        $observations = 'Approvisionnement externe';
        if ($request->source)       $observations .= ' — ' . $request->source;
        if ($request->observations) $observations .= ' : ' . $request->observations;

        $nouveauSolde = 0.0;

        try {
            DB::transaction(function () use ($request, $coffre, $observations, &$nouveauSolde) {
                $soldeCoffre = CaissesGuichetSolde::where('guichet_id', $coffre->id)
                    ->where('devise_code', $request->devise_code)
                    ->lockForUpdate()
                    ->first();

                if (!$soldeCoffre) {
                    $soldeCoffre = CaissesGuichetSolde::create([
                        'guichet_id'       => $coffre->id,
                        'devise_code'      => $request->devise_code,
                        'solde_en_caisse'  => 0,
                    ]);
                }

                MouvementInterCaisse::create([
                    'guichet_source_id'    => null,
                    'guichet_dest_id'      => $coffre->id,
                    'agent_initiateur'     => Auth::user()->agent_matricule,
                    'type_flux'            => 'ALIMENTATION',
                    'montant'              => $request->montant,
                    'devise_code'          => $request->devise_code,
                    'reference_bordereau'  => $this->buildReference('APP'),
                    'date_mouvement'       => now(),
                    'statut'               => 'CONFIRME',
                    'validateur_matricule' => Auth::user()->agent_matricule,
                    'observations'         => $observations,
                ]);

                $soldeCoffre->increment('solde_en_caisse', (float) $request->montant);
                $nouveauSolde = (float) $soldeCoffre->fresh()->solde_en_caisse;
            });

            app(NotificationService::class)->notifyUsersWithPermission(
                'EBEN-PER44',
                'Coffre central ravitaille',
                'Le coffre central a ete approvisionne de ' . number_format((float) $request->montant, 2, ',', ' ') . ' ' . $request->devise_code . '.',
                [
                    'type' => 'info',
                    'category' => 'tresorerie',
                    'icon' => 'fas fa-piggy-bank',
                    'action_url' => route('tresorerie.etat-coffre'),
                ]
            );
        } catch (\Exception $e) {
            Log::error('[Trésorerie] Erreur approvisionnement coffre', [
                'devise_code' => $request->devise_code,
                'montant'     => $request->montant,
                'source'      => $request->source,
                'erreur'      => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
            ], 500);
        }

        \App\Models\ActivityLog::record(
            'TRESORERIE',
            'COFFRE_APPROVISIONNE',
            $coffre,
            $request->devise_code,
            "Approvisionnement coffre central : +{$request->montant} {$request->devise_code}" . ($request->source ? " (source : {$request->source})" : '')
        );

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

        $guichet = CaissesGuichet::with('soldes')->find($request->guichet_id);
        if (!$guichet) {
            Log::warning('[Trésorerie] Guichet introuvable pour alimentation', ['guichet_id' => $request->guichet_id, 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Guichet introuvable.'], 404);
        }

        if ($guichet->type_guichet === 'CENTRAL') {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas alimenter le coffre central via inter-caisses.',
            ], 422);
        }

        $coffre = $this->getCoffreCentral('alimenter');
        if (!$coffre) {
            return response()->json(['success' => false, 'message' => 'Coffre central introuvable. Contactez l\'administrateur.'], 500);
        }

        if ((int) $guichet->id === (int) $coffre->id) {
            return response()->json([
                'success' => false,
                'message' => 'Mouvement invalide : source et destination ne peuvent pas être le même guichet.',
            ], 422);
        }

        if ($coffre->statut_operationnel !== 'OUVERT') {
            return response()->json([
                'success' => false,
                'message' => 'Le coffre central est ' . $coffre->statut_operationnel . '. Alimentation inter-caisses indisponible.',
            ], 422);
        }

        if ($guichet->statut_operationnel !== 'OUVERT') {
            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'alimenter : le guichet ' . $guichet->code_guichet .
                             ' est ' . $guichet->statut_operationnel . '. L\'agent doit d\'abord ouvrir sa session.',
            ], 422);
        }

        $nouveauSoldeCoffre = null;
        try {
            DB::transaction(function () use ($request, $guichet, $coffre, &$nouveauSoldeCoffre) {
                $soldeGuichet = CaissesGuichetSolde::where('guichet_id', $guichet->id)
                    ->where('devise_code', $request->devise_code)
                    ->lockForUpdate()
                    ->first();

                if (!$soldeGuichet) {
                    throw ValidationException::withMessages([
                        'devise_code' => 'Le guichet ' . $guichet->code_guichet . ' ne gère pas la devise ' . $request->devise_code . '.',
                    ]);
                }

                $soldeCoffre = CaissesGuichetSolde::where('guichet_id', $coffre->id)
                    ->where('devise_code', $request->devise_code)
                    ->lockForUpdate()
                    ->first();

                if (!$soldeCoffre || (float) $soldeCoffre->solde_en_caisse < (float) $request->montant) {
                    $disponible = $soldeCoffre ? number_format((float) $soldeCoffre->solde_en_caisse, 2, ',', ' ') : '0,00';
                    throw ValidationException::withMessages([
                        'montant' => 'Fonds insuffisants dans le coffre. Disponible : ' . $disponible
                                   . ' ' . $request->devise_code . ' | Demandé : '
                                   . number_format((float) $request->montant, 2, ',', ' ') . ' ' . $request->devise_code,
                    ]);
                }

                MouvementInterCaisse::create([
                    'guichet_source_id'    => $coffre->id,
                    'guichet_dest_id'      => $guichet->id,
                    'agent_initiateur'     => Auth::user()->agent_matricule,
                    'type_flux'            => 'ALIMENTATION',
                    'montant'              => $request->montant,
                    'devise_code'          => $request->devise_code,
                    'reference_bordereau'  => $this->buildReference('ALI', 'G' . str_pad((string) $guichet->id, 2, '0', STR_PAD_LEFT)),
                    'date_mouvement'       => now(),
                    'statut'               => 'CONFIRME',
                    'validateur_matricule' => Auth::user()->agent_matricule,
                    'observations'         => $request->observations ?: 'Alimentation inter-caisses',
                ]);

                $soldeCoffre->decrement('solde_en_caisse', (float) $request->montant);
                $soldeGuichet->increment('solde_en_caisse', (float) $request->montant);

                $nouveauSoldeCoffre = (float) $soldeCoffre->fresh()->solde_en_caisse;
            });

            $matriculeAgentGuichet = optional($guichet->affectationActive)->agent_matricule;
            app(NotificationService::class)->notifyAgentMatricules(
                [$matriculeAgentGuichet],
                'Ravitaillement recu',
                'Le guichet ' . $guichet->code_guichet . ' a ete ravitaille de ' . number_format((float) $request->montant, 2, ',', ' ') . ' ' . $request->devise_code . '.',
                [
                    'type' => 'info',
                    'category' => 'tresorerie',
                    'icon' => 'fas fa-donate',
                    'action_url' => route('caisses.ouverture'),
                ]
            );
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first() ?? 'Données invalides.';
            return response()->json(['success' => false, 'message' => $message], 422);
        } catch (\Exception $e) {
            Log::error('[Trésorerie] Erreur alimentation inter-caisses', [
                'guichet_id'   => $guichet->id,
                'devise_code'  => $request->devise_code,
                'montant'      => $request->montant,
                'erreur'       => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        \App\Models\ActivityLog::record(
            'TRESORERIE',
            'GUICHET_ALIMENTE',
            $guichet,
            $guichet->code_guichet,
            "Alimentation du guichet {$guichet->code_guichet} depuis le coffre : +{$request->montant} {$request->devise_code}"
        );

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
                    'guichet_source_type' => $m->guichetSource ? ($m->guichetSource->type_guichet ?? '—') : 'EXTERNE',
                    'guichet_dest'   => $m->guichetDest   ? $m->guichetDest->code_guichet   : '—',
                    'guichet_dest_type' => $m->guichetDest ? ($m->guichetDest->type_guichet ?? '—') : '—',
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
        $coffre = $this->getCoffreCentral('mouvements');
        if (!$coffre) {
            return response()->json(['success' => false, 'message' => 'Coffre central introuvable.'], 500);
        }

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
        $coffre = $this->getCoffreCentral('stats');
        if (!$coffre) {
            return response()->json(['total_entrees' => 0, 'total_sorties' => 0, 'total_mouvements' => 0, 'par_devise' => []]);
        }
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
    public function demandesJson(Request $request)
    {
        $query = MouvementInterCaisse::with(['guichetDest', 'agentInitiateur'])
            ->where('type_flux', 'DEMANDE_APPRO')
            ->orderByRaw("FIELD(statut, 'EN_ATTENTE', 'CONFIRME', 'ANNULE')")
            ->orderByDesc('date_mouvement');

        if ($request->filled('statut') && $request->statut !== 'tous') {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_mouvement', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_mouvement', '<=', $request->date_fin);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('reference_bordereau', 'like', "%{$search}%")
                  ->orWhere('agent_initiateur', 'like', "%{$search}%")
                  ->orWhere('observations', 'like', "%{$search}%")
                  ->orWhereHas('guichetDest', function ($g) use ($search) {
                      $g->where('code_guichet', 'like', "%{$search}%")
                        ->orWhere('intitule', 'like', "%{$search}%");
                  });
            });
        }

        $limit = (int) $request->input('limit', 200);
        $limit = max(20, min($limit, 500));

        $demandes = $query->limit($limit)
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
            ->find($id);

        if (!$demande) {
            Log::warning('[Trésorerie] Demande appro introuvable', ['id' => $id, 'action' => 'approuverDemande', 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Demande introuvable ou déjà traitée.'], 404);
        }

        $coffre = $this->getCoffreCentral('approuverDemande');
        if (!$coffre) {
            return response()->json(['success' => false, 'message' => 'Coffre central introuvable. Contactez l\'administrateur.'], 500);
        }

        $guichetDest = CaissesGuichet::find($demande->guichet_dest_id);
        if (!$guichetDest) {
            return response()->json([
                'success' => false,
                'message' => 'Le guichet destinataire est introuvable.',
            ], 422);
        }

        if ($guichetDest->type_guichet === 'CENTRAL') {
            return response()->json([
                'success' => false,
                'message' => 'Demande invalide : le destinataire ne peut pas être le coffre central.',
            ], 422);
        }

        if ($guichetDest->statut_operationnel !== 'OUVERT') {
            return response()->json([
                'success' => false,
                'message' => 'Le guichet ' . $guichetDest->code_guichet . ' est ' . $guichetDest->statut_operationnel . '. Ouvrez la session avant approbation.',
            ], 422);
        }

        $montantDemande = (float) $demande->montant;
        $deviseDemande  = $demande->devise_code;
        try {
            DB::transaction(function () use ($demande, $coffre, $guichetDest, $request, &$montantDemande, &$deviseDemande) {
                $demandeVerrouillee = MouvementInterCaisse::where('id', $demande->id)
                    ->where('type_flux', 'DEMANDE_APPRO')
                    ->where('statut', 'EN_ATTENTE')
                    ->lockForUpdate()
                    ->first();

                if (!$demandeVerrouillee) {
                    throw ValidationException::withMessages([
                        'demande' => 'Cette demande a déjà été traitée par un autre utilisateur.',
                    ]);
                }

                $soldeCoffre = CaissesGuichetSolde::where('guichet_id', $coffre->id)
                    ->where('devise_code', $demandeVerrouillee->devise_code)
                    ->lockForUpdate()
                    ->first();

                if (!$soldeCoffre || (float) $soldeCoffre->solde_en_caisse < (float) $demandeVerrouillee->montant) {
                    $dispo = $soldeCoffre ? number_format((float) $soldeCoffre->solde_en_caisse, 2, ',', ' ') : '0,00';
                    throw ValidationException::withMessages([
                        'montant' => 'Fonds insuffisants dans le coffre. Disponible : ' . $dispo . ' ' . $demandeVerrouillee->devise_code,
                    ]);
                }

                $soldeGuichet = CaissesGuichetSolde::where('guichet_id', $guichetDest->id)
                    ->where('devise_code', $demandeVerrouillee->devise_code)
                    ->lockForUpdate()
                    ->first();

                if (!$soldeGuichet) {
                    throw ValidationException::withMessages([
                        'devise_code' => 'Le guichet ne gère pas la devise ' . $demandeVerrouillee->devise_code . '.',
                    ]);
                }

                // Met à jour le mouvement existant (EN_ATTENTE → CONFIRME)
                $demandeVerrouillee->update([
                    'guichet_source_id'    => $coffre->id,   // on fixe le coffre comme source
                    'statut'               => 'CONFIRME',
                    'validateur_matricule' => Auth::user()->agent_matricule,
                    'observations'         => ($demandeVerrouillee->observations ? $demandeVerrouillee->observations . ' | ' : '')
                                             . 'Approuvé' . ($request->observations ? ' : ' . $request->observations : ''),
                ]);

                $soldeCoffre->decrement('solde_en_caisse', (float) $demandeVerrouillee->montant);
                $soldeGuichet->increment('solde_en_caisse', (float) $demandeVerrouillee->montant);

                $montantDemande = (float) $demandeVerrouillee->montant;
                $deviseDemande  = $demandeVerrouillee->devise_code;
            });

            app(NotificationService::class)->notifyAgentMatricules(
                [$demande->agent_initiateur],
                'Demande de ravitaillement approuvee',
                'Votre demande #' . $demande->id . ' a ete approuvee: ' . number_format($montantDemande, 2, ',', ' ') . ' ' . $deviseDemande . '.',
                [
                    'type' => 'info',
                    'category' => 'tresorerie',
                    'icon' => 'fas fa-check-circle',
                    'action_url' => route('caisses.mes.demandes'),
                ]
            );
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first() ?? 'Données invalides.';
            return response()->json(['success' => false, 'message' => $message], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur.: ' . $e->getMessage()], 500);
        }

        $guichetCode = $guichetDest->code_guichet ?? 'le guichet';
        return response()->json([
            'success' => true,
            'message' => 'Demande #' . $demande->id . ' approuvée. '
                       . number_format($montantDemande, 2, ',', ' ') . ' ' . $deviseDemande
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
            ->find($id);

        if (!$demande) {
            Log::warning('[Trésorerie] Demande appro introuvable pour rejet', ['id' => $id, 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Demande introuvable ou déjà traitée.'], 404);
        }

        $demande->update([
            'statut'               => 'ANNULE',
            'validateur_matricule' => Auth::user()->agent_matricule,
            'observations'         => ($demande->observations ? $demande->observations . ' | ' : '')
                                     . 'Rejeté.: ' . $request->observations,
        ]);

        app(NotificationService::class)->notifyAgentMatricules(
            [$demande->agent_initiateur],
            'Demande de ravitaillement rejetee',
            'Votre demande #' . $demande->id . ' a ete rejetee. Motif: ' . $request->observations,
            [
                'type' => 'warning',
                'category' => 'tresorerie',
                'icon' => 'fas fa-times-circle',
                'action_url' => route('caisses.mes.demandes'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Demande #' . $demande->id . ' rejetée.',
        ]);
    }

   
    /**
     * Retourne JSON des guichets EN_VERIFICATION en attente superviseur.
     * En mode historique : inclut également les guichets FERMÉ, avec filtre date.
     */
    public function cloturesEnVerification(Request $request)
    {
        $includeHistory = $request->boolean('include_history', false);

        // Date range (utilisé uniquement en mode historique)
        $dateDebut = $includeHistory
            ? ($request->input('date_debut') ?: now()->subDays(30)->toDateString())
            : null;
        $dateFin = $includeHistory
            ? ($request->input('date_fin') ?: now()->toDateString())
            : null;

        $statutsGuichet = $includeHistory
            ? ['EN_VERIFICATION', 'FERME']
            : ['EN_VERIFICATION'];

        $guichets = CaissesGuichet::with(['soldes.devise', 'affectations' => function ($q) {
                $q->where('Etat', 'ACTIF')->with('agent');
            }])
            ->whereIn('statut_operationnel', $statutsGuichet)
            ->orderByDesc('updated_at')
            ->get()
            ->map(function ($g) use ($includeHistory, $dateDebut, $dateFin) {
                $cloturesQuery = ClotureCaisse::where('guichet_id', $g->id);

                if ($includeHistory && $dateDebut && $dateFin) {
                    $cloturesQuery->whereDate('date_cloture', '>=', $dateDebut)
                                  ->whereDate('date_cloture', '<=', $dateFin);
                }

                $allClotures = $cloturesQuery->orderByDesc('date_cloture')->get();

                $pendingCount = $allClotures
                    ->where('statut_validation', ClotureCaisse::VALIDATION_EN_ATTENTE)
                    ->count();

                $clotures = $allClotures
                    ->when(!$includeHistory, function ($collection) {
                        return $collection->where('statut_validation', ClotureCaisse::VALIDATION_EN_ATTENTE);
                    })
                    ->values();

                $agentActif = $g->affectations->first();
                $agentNom   = $agentActif && $agentActif->agent
                    ? ($agentActif->agent->prenoms . ' ' . $agentActif->agent->nom)
                    : ($allClotures->first()?->agent_cloturant ?? 'Inconnu');

                $montants = $clotures->map(fn($c) => [
                    'cloture_id'              => $c->id,
                    'devise_code'             => $c->devise_code,
                    'solde_physique'          => number_format((float) $c->solde_physique, 2, ',', ' ') . ' ' . $c->devise_code,
                    'solde_comptable'         => number_format((float) $c->solde_comptable, 2, ',', ' ') . ' ' . $c->devise_code,
                    'ecart'                   => number_format((float) $c->ecart_caisse, 2, ',', ' ') . ' ' . $c->devise_code,
                    'statut_ecart'            => $c->statut_ecart,
                    'statut_validation'       => $c->statut_validation,
                    'motif_ecart'             => $c->motif_ecart,
                    'date'                    => $c->date_cloture?->format('d/m/Y H:i'),
                    'date_validation'         => $c->date_validation?->format('d/m/Y H:i'),
                    'validateur_matricule'    => $c->validateur_matricule,
                    'observations_superviseur'=> $c->observations_superviseur,
                ]);

                return [
                    'guichet_id'     => $g->id,
                    'code_guichet'   => $g->code_guichet,
                    'intitule'       => $g->intitule,
                    'statut_guichet' => $g->statut_operationnel,
                    'agent_nom'      => $agentNom,
                    'agent_matric'   => $agentActif?->agent_matricule ?? $allClotures->first()?->agent_cloturant,
                    'montants'       => $montants,
                    'nb_lignes'      => $clotures->count(),
                    'pending_count'  => $pendingCount,
                    'total_count'    => $allClotures->count(),
                ];
            })
            ->filter(function ($g) use ($includeHistory) {
                if ($includeHistory) {
                    return $g['total_count'] > 0;
                }

                return $g['pending_count'] > 0;
            })
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
        $guichet = CaissesGuichet::with('soldes')->find($guichetId);

        if (!$guichet) {
            Log::warning('[Trésorerie] Guichet introuvable pour approbation clôture', ['guichet_id' => $guichetId, 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Guichet introuvable.'], 404);
        }

        if ($guichet->statut_operationnel !== 'EN_VERIFICATION') {
            return response()->json(['success' => false, 'message' => "Ce guichet n'est pas en attente de vérification."], 422);
        }

        $clotures = ClotureCaisse::where('guichet_id', $guichetId)
            ->where('statut_validation', ClotureCaisse::VALIDATION_EN_ATTENTE)
            ->get();

        if ($clotures->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Aucune clôture en attente pour ce guichet.'], 422);
        }

        $coffre = $this->getCoffreCentral('approuverCloture');
        if (!$coffre) {
            return response()->json(['success' => false, 'message' => 'Coffre central introuvable. Contactez l\'administrateur.'], 500);
        }

        try {
            DB::transaction(function () use ($clotures, $guichet, $coffre, $user, $request) {
                $reference = 'DEG-' . now()->format('Ymd-His') . '-' . $guichet->code_guichet;

                foreach ($clotures as $cloture) {
                    /** @var \App\Models\Caisse\ClotureCaisse $cloture */
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

            app(NotificationService::class)->notifyAgentMatricules(
                [$clotures->first()->agent_cloturant ?? null],
                'Cloture guichet approuvee',
                'La cloture du guichet ' . $guichet->code_guichet . ' a ete approuvee.',
                [
                    'type' => 'info',
                    'category' => 'tresorerie',
                    'icon' => 'fas fa-lock',
                    'action_url' => route('caisses.ouverture'),
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        \App\Models\ActivityLog::record(
            'TRESORERIE',
            'CLOTURE_APPROUVEE',
            $guichet,
            $guichet->code_guichet,
            "Clôture approuvée pour le guichet {$guichet->code_guichet}"
        );

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
        $guichet = CaissesGuichet::find($guichetId);

        if (!$guichet) {
            Log::warning('[Trésorerie] Guichet introuvable pour rejet clôture', ['guichet_id' => $guichetId, 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Guichet introuvable.'], 404);
        }

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

            app(NotificationService::class)->notifyAgentMatricules(
                [
                    ClotureCaisse::where('guichet_id', $guichetId)->latest('id')->value('agent_cloturant'),
                ],
                'Cloture guichet rejetee',
                'La cloture du guichet ' . $guichet->code_guichet . ' a ete rejetee. Motif: ' . $request->observations,
                [
                    'type' => 'warning',
                    'category' => 'tresorerie',
                    'icon' => 'fas fa-undo',
                    'action_url' => route('caisses.ouverture'),
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        \App\Models\ActivityLog::record(
            'TRESORERIE',
            'CLOTURE_REJETEE',
            $guichet,
            $guichet->code_guichet,
            "Clôture rejetée pour le guichet {$guichet->code_guichet} — motif : {$request->observations}"
        );

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
        $cloture = ClotureCaisse::find($clotureId);

        if (!$cloture) {
            Log::warning('[Trésorerie] Clôture introuvable pour approbation ligne', ['cloture_id' => $clotureId, 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Clôture introuvable.'], 404);
        }

        if ($cloture->statut_validation !== ClotureCaisse::VALIDATION_EN_ATTENTE) {
            return response()->json(['success' => false, 'message' => 'Cette ligne a déjà été traitée.'], 422);
        }

        $guichet = CaissesGuichet::with('soldes')->find($cloture->guichet_id);
        if (!$guichet) {
            Log::warning('[Trésorerie] Guichet de clôture introuvable', ['guichet_id' => $cloture->guichet_id, 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Guichet introuvable.'], 404);
        }
        $coffre  = $this->getCoffreCentral('approuverLigneCloture');
        if (!$coffre) {
            return response()->json(['success' => false, 'message' => 'Coffre central introuvable. Contactez l\'administrateur.'], 500);
        }

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

        app(NotificationService::class)->notifyAgentMatricules(
            [$cloture->agent_cloturant],
            'Cloture devise traitee',
            $message,
            [
                'type' => 'info',
                'category' => 'tresorerie',
                'icon' => 'fas fa-check-double',
                'action_url' => route('caisses.ouverture'),
            ]
        );

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
        $cloture = ClotureCaisse::find($clotureId);

        if (!$cloture) {
            Log::warning('[Trésorerie] Clôture introuvable pour rejet ligne', ['cloture_id' => $clotureId, 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Clôture introuvable.'], 404);
        }

        if ($cloture->statut_validation !== ClotureCaisse::VALIDATION_EN_ATTENTE) {
            return response()->json(['success' => false, 'message' => 'Cette ligne a déjà été traitée.'], 422);
        }

        $guichet = CaissesGuichet::find($cloture->guichet_id);
        if (!$guichet) {
            Log::warning('[Trésorerie] Guichet de clôture introuvable', ['guichet_id' => $cloture->guichet_id, 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Guichet introuvable.'], 404);
        }

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

            app(NotificationService::class)->notifyAgentMatricules(
                [$cloture->agent_cloturant],
                'Cloture devise rejetee',
                'La devise ' . $cloture->devise_code . ' a ete rejetee pour le guichet ' . $guichet->code_guichet . '. Motif: ' . $request->observations,
                [
                    'type' => 'warning',
                    'category' => 'tresorerie',
                    'icon' => 'fas fa-exclamation-triangle',
                    'action_url' => route('caisses.ouverture'),
                ]
            );
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

   
    /**
     * Vue rapport des apports des agents commerciaux (guichets MOBILE).
     */
    public function agentsMobiles(Request $request)
    {
        $today = now()->toDateString();
        $dateDebut = $request->input('date_debut', $today);
        $dateFin = $request->input('date_fin', $today);

        $guichetsMobiles = CaissesGuichet::where('type_guichet', 'MOBILE')->pluck('id')->toArray();
        $allowedMobileTypes = \App\Models\Caisse\Transaction::allowedTypesForGuichetType('MOBILE');
        $operationTypeOptions = \App\Models\Caisse\Transaction::operationTypeOptions('MOBILE');

        $query = \App\Models\Caisse\Transaction::whereIn('guichet_id', $guichetsMobiles)
            ->whereIn('type', $allowedMobileTypes)
            ->where('statut', 'CONFIRME')
            ->with(['guichet', 'compte.client']);

        
        $query->whereDate('date_operation', '>=', $dateDebut)
            ->whereDate('date_operation', '<=', $dateFin);
        if ($request->filled('agent_matricule')) {
            $query->where('agent_matricule', $request->agent_matricule);
        }
        if ($request->filled('type_compte') && $request->type_compte !== 'tous') {
            $query->whereHas('compte', fn($q) => $q->where('type', $request->type_compte));
        }
        if ($request->filled('devise_code') && $request->devise_code !== 'tous') {
            $query->where('devise_code', $request->devise_code);
        }
        if ($request->filled('type_operation') && $request->type_operation !== 'tous' && in_array($request->type_operation, $allowedMobileTypes, true)) {
            $query->where('type', $request->type_operation);
        }

        if ($request->filled('code_zone')) {
            $zone = \App\Models\Zone::where('code_zone', $request->code_zone)->first();
            $zoneAgentMatricule = $zone
                ? \App\Models\ZoneAffectation::where('code_zone', $zone->code_zone)->actives()->value('agent_matricule')
                : null;

            if ($zone && $zoneAgentMatricule) {
                // Trouver les guichets MOBILES affectés à cet agent
                $guichetsZone = \App\Models\RH\Affectation::where('agent_matricule', $zoneAgentMatricule)
                    ->whereNotNull('guichet_id')
                    ->pluck('guichet_id');
                if ($guichetsZone->isNotEmpty()) {
                    $query->whereIn('guichet_id', $guichetsZone);
                } else {
                    // Zone sans guichet affecté = aucun résultat
                    $query->whereRaw('1=0');
                }
            }
        }

        $transactions = $query->orderBy('date_operation')->get();

        
        $parAgent = $transactions->groupBy('agent_matricule')->map(function ($items, $matricule) {
            $agent = \App\Models\RH\Agent::find($matricule);
            $parDevise = $items->groupBy('devise_code')->map(function ($devItems, $devise) {
                $depots   = $devItems->whereIn('type', ['DEPOT', 'PAIEMENT'])->sum('montant');
                $retraits = $devItems->whereIn('type', ['RETRAIT', 'REMBOURSEMENT'])->sum('montant');
                return [
                    'devise'         => $devise,
                    'total_entrees'  => (float) $depots,
                    'total_sorties'  => (float) $retraits,
                    'net'            => (float) ($depots - $retraits),
                    'nb_operations'  => $devItems->count(),
                ];
            })->values();

            return [
                'matricule'     => $matricule,
                'nom_complet'   => $agent?->full_name ?: $matricule,
                'guichet'       => $items->first()?->guichet?->intitule ?? '—',
                'nb_operations' => $items->count(),
                'par_devise'    => $parDevise,
            ];
        })->values();

        
        $agents = \App\Models\RH\Agent::whereIn('matricule',
            \App\Models\RH\Affectation::whereIn('guichet_id', $guichetsMobiles)
                ->pluck('agent_matricule')
        )->orderBy('nom')->get();

        $zones   = \App\Models\Zone::orderBy('nom')->get();
        $devises = \App\Models\Tresorerie\Devise::orderBy('code_iso')->get();

        return view('tresorerie.agents_mobiles', compact(
            'parAgent', 'transactions', 'agents', 'zones', 'devises', 'operationTypeOptions', 'dateDebut', 'dateFin'
        ));
    }

    /**
     * Impression PDF du rapport agents mobiles.
     */
    public function agentsMobilesPdf(Request $request)
    {
        $today = now()->toDateString();
        $dateDebut = $request->input('date_debut', $today);
        $dateFin = $request->input('date_fin', $today);

        $guichetsMobiles = CaissesGuichet::where('type_guichet', 'MOBILE')->pluck('id')->toArray();
        $allowedMobileTypes = \App\Models\Caisse\Transaction::allowedTypesForGuichetType('MOBILE');

        $query = \App\Models\Caisse\Transaction::whereIn('guichet_id', $guichetsMobiles)
            ->whereIn('type', $allowedMobileTypes)
            ->where('statut', 'CONFIRME')
            ->with(['guichet', 'compte.client']);

        $query->whereDate('date_operation', '>=', $dateDebut)
            ->whereDate('date_operation', '<=', $dateFin);
        if ($request->filled('agent_matricule')) {
            $query->where('agent_matricule', $request->agent_matricule);
        }
        if ($request->filled('type_compte') && $request->type_compte !== 'tous') {
            $query->whereHas('compte', fn($q) => $q->where('type', $request->type_compte));
        }
        if ($request->filled('devise_code') && $request->devise_code !== 'tous') {
            $query->where('devise_code', $request->devise_code);
        }
        if ($request->filled('type_operation') && $request->type_operation !== 'tous' && in_array($request->type_operation, $allowedMobileTypes, true)) {
            $query->where('type', $request->type_operation);
        }
        if ($request->filled('code_zone')) {
            $zone = \App\Models\Zone::where('code_zone', $request->code_zone)->first();
            $zoneAgentMatricule = $zone
                ? \App\Models\ZoneAffectation::where('code_zone', $zone->code_zone)->actives()->value('agent_matricule')
                : null;

            if ($zone && $zoneAgentMatricule) {
                $guichetsZone = \App\Models\RH\Affectation::where('agent_matricule', $zoneAgentMatricule)
                    ->whereNotNull('guichet_id')->pluck('guichet_id');
                $guichetsZone->isNotEmpty()
                    ? $query->whereIn('guichet_id', $guichetsZone)
                    : $query->whereRaw('1=0');
            }
        }

        $transactions = $query->orderBy('agent_matricule')->orderBy('date_operation')->get();

        $parAgent = $transactions->groupBy('agent_matricule')->map(function ($items, $matricule) {
            $agent = \App\Models\RH\Agent::find($matricule);
            $parDevise = $items->groupBy('devise_code')->map(function ($devItems, $devise) {
                return [
                    'devise'        => $devise,
                    'total_entrees' => (float) $devItems->whereIn('type', ['DEPOT', 'PAIEMENT'])->sum('montant'),
                    'total_sorties' => (float) $devItems->whereIn('type', ['RETRAIT', 'REMBOURSEMENT'])->sum('montant'),
                    'nb_operations' => $devItems->count(),
                ];
            })->values();

            return [
                'matricule'     => $matricule,
                'nom_complet'   => $agent?->full_name ?: $matricule,
                'guichet'       => $items->first()?->guichet?->intitule ?? '—',
                'nb_operations' => $items->count(),
                'par_devise'    => $parDevise,
            ];
        })->values();

        $filtres = $request->only(['agent_matricule','type_compte','devise_code','type_operation','code_zone']);
        $filtres['date_debut'] = $dateDebut;
        $filtres['date_fin'] = $dateFin;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('impressions.tresorerie.agents_mobiles',
            compact('parAgent', 'transactions', 'filtres')
        )->setPaper('a4', 'portrait');

        return $pdf->stream('Rapport_Agents_Mobiles_' . now()->format('Ymd_His') . '.pdf');
    }
}
