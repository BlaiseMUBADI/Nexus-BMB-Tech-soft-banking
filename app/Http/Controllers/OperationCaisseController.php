<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RH\Affectation;
use App\Models\Clients\Compte;
use App\Models\Caisse\CaissesGuichet;
use App\Models\Caisse\CaissesGuichetSolde;
use App\Models\Caisse\DemandeModification;
use App\Models\Caisse\MouvementInterCaisse;
use App\Models\Caisse\Transaction;
use App\Models\Tresorerie\CommissionRule;
use App\Services\Comptabilite\OhadaAccountingService;
use App\Services\Commissions\CommissionEngine;
use App\Services\Notifications\NotificationService;
use App\Models\Zone;
use Illuminate\Validation\Rule;

/**
 * OperationCaisseController
 * --------------------------
 * Gère toutes les opérations du module Caisse / Guichet :
 *
 *  index()             — vue Opérations de Caisse (formulaire + historique)
 *  store()             — AJAX POST : enregistrer une opération, mettre à jour soldes
 *  annuler()           — AJAX POST : annuler une opération (inversement soldes)
 *  journal()           — AJAX GET  : journal des opérations (filtrable)
 *  journalPage()       — vue Journal des Opérations
 *  rapportFinJournee() — vue Rapport de Fin de Journée (guichets FIXE)
 *  mobileIndex()       — vue Gestion Départ / Retour (guichets MOBILE)
 *  mobileDepart()      — AJAX POST : demande de dotation mobile
 *  mobileRetour()      — AJAX POST : déclaration de reversement mobile
 */
class OperationCaisseController extends Controller
{
    // ══════════════════════════════════════════════════════════════
    //  HELPER — Récupère le guichet de l'agent connecté
    // ══════════════════════════════════════════════════════════════

    private function getGuichetAgent(): ?CaissesGuichet
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $affectation = Affectation::with(['guichet.soldes.devise'])
            ->where('agent_matricule', $user->agent_matricule)
            ->where('Etat', 'ACTIF')
            ->whereNotNull('guichet_id')
            ->latest('date_debut')
            ->first();

        return $affectation?->guichet;
    }

    private function buildZoneLabel(array $zoneNames): string
    {
        $zoneNames = array_values(array_filter($zoneNames));
        if (empty($zoneNames)) {
            return '';
        }

        if (count($zoneNames) === 1) {
            $label = trim($zoneNames[0]);
            return preg_match('/^zones?\b/i', $label) ? $label : 'Zone ' . $label;
        }

        $joined = implode(', ', array_map('trim', $zoneNames));
        return preg_match('/^zones?\b/i', $joined) ? $joined : 'Zones ' . $joined;
    }

    private function resolveZoneScope(): array
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $guichet = $this->getGuichetAgent();

        if (!$user || !$guichet || $guichet->type_guichet !== 'MOBILE') {
            return ['restricted' => false, 'zone_codes' => []];
        }

        $zones = Zone::assignedToAgent($user->agent_matricule)
            ->orderBy('nom')
            ->get(['code_zone', 'nom']);

        $zoneCodes = $zones->pluck('code_zone')
            ->filter()
            ->values()
            ->all();

        $zoneNames = $zones->pluck('nom')
            ->filter()
            ->values()
            ->all();

        $zoneLabel = $this->buildZoneLabel($zoneNames);

        return [
            'restricted' => true,
            'zone_codes' => $zoneCodes,
            'zone_names' => $zoneNames,
            'zone_label' => $zoneLabel,
            'agent_matricule' => $user->agent_matricule,
        ];
    }

    private function applyZoneScopeToComptes($query, array $zoneScope)
    {
        if (!($zoneScope['restricted'] ?? false)) {
            return $query;
        }

        $zoneCodes = $zoneScope['zone_codes'] ?? [];
        if (empty($zoneCodes)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('client', fn($q) => $q->whereIn('code_zone', $zoneCodes));
    }

    private function canAccessCompte(?Compte $compte, array $zoneScope): bool
    {
        if (!$compte) {
            return false;
        }

        if (!($zoneScope['restricted'] ?? false)) {
            return true;
        }

        return in_array(optional($compte->client)->code_zone, $zoneScope['zone_codes'] ?? [], true);
    }

    private function getAllowedOperationTypes(?CaissesGuichet $guichet): array
    {
        return Transaction::allowedTypesForGuichetType($guichet?->type_guichet);
    }

    private function getOperationTypeOptions(?CaissesGuichet $guichet): array
    {
        $labels = [
            Transaction::DEPOT => '💰 Dépôt (compte client)',
            Transaction::RETRAIT => '💸 Retrait (compte client)',
            Transaction::CHANGE => '🔄 Change de devises',
            Transaction::PAIEMENT => '🧾 Paiement facture/service',
            Transaction::REMBOURSEMENT => '↩ Remboursement',
            Transaction::VIREMENT => '🔁 Virement',
        ];

        return collect(Transaction::operationTypeOptions($guichet?->type_guichet))
            ->map(fn ($type) => [
                'value' => $type['value'],
                'label' => $labels[$type['value']] ?? $type['label'],
            ])
            ->values()
            ->all();
    }

    private function getOperationTypeFilterOptions(?CaissesGuichet $guichet): array
    {
        return Transaction::operationTypeOptions($guichet?->type_guichet);
    }

    private function buildCommissionContext(string $type, ?Compte $compteOperation, ?CaissesGuichet $guichet, string $devise, float $montant, ?string $agentMatricule): array
    {
        return [
            'code_operation' => $type,
            'type_compte' => $compteOperation?->type ?? CommissionRule::TYPE_NO_ACCOUNT,
            'type_guichet' => strtoupper((string) ($guichet?->type_guichet ?? CommissionRule::ALL)),
            'devise_code' => $devise,
            'code_zone' => $compteOperation?->client?->code_zone,
            'portefeuille_id' => $compteOperation?->portefeuille_id,
            'montant' => $montant,
            'agent_matricule' => $agentMatricule,
            'guichet_id' => $guichet?->id,
        ];
    }

    private function previewCommissionAmount(CommissionEngine $commissionEngine, array $commissionContext): float
    {
        $rule = $commissionEngine->resolveRule($commissionContext, now());
        if (!$rule) {
            return 0.0;
        }

        return $commissionEngine->calculateCommission($rule, (float) ($commissionContext['montant'] ?? 0));
    }

    private function computeCompteImpact(string $type, float $montant, float $commission): array
    {
        $commission = max(0, round($commission, 2));
        $montant = max(0, round($montant, 2));

        if ($type === Transaction::DEPOT) {
            $delta = round($montant - $commission, 2);

            return [
                'delta' => $delta,
                'total_client' => $delta,
                'sens' => 'CREDIT',
            ];
        }

        if ($type === Transaction::RETRAIT) {
            $totalDebite = round($montant + $commission, 2);

            return [
                'delta' => -$totalDebite,
                'total_client' => $totalDebite,
                'sens' => 'DEBIT',
            ];
        }

        return [
            'delta' => 0.0,
            'total_client' => null,
            'sens' => null,
        ];
    }

    // ══════════════════════════════════════════════════════════════
    //  1. OPÉRATIONS DE CAISSE
    // ══════════════════════════════════════════════════════════════

    /**
     * Affiche la page de saisie des opérations de caisse.
     * Montre aussi le récapitulatif du jour (dernières 20 opérations).
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = $this->getGuichetAgent();
        $zoneScope = $this->resolveZoneScope();

        $operations = collect();
        $latestDemandesByTx = [];
        if ($guichet) {
            $operations = Transaction::where('guichet_id', $guichet->id)
                ->with('compte.client')
                ->whereDate('date_operation', today())
                ->orderByDesc('date_operation')
                ->limit(30)
                ->get();

            if ($operations->isNotEmpty()) {
                $latestDemandesByTx = DemandeModification::whereIn('transaction_id', $operations->pluck('id')->all())
                    ->orderByDesc('id')
                    ->get()
                    ->unique('transaction_id')
                    ->keyBy('transaction_id')
                    ->all();
            }
        }

        $comptesQuery = \App\Models\Clients\Compte::with('client')
            ->orderBy('devise')
            ->orderBy('code_compte');
        $this->applyZoneScopeToComptes($comptesQuery, $zoneScope);
        $comptes = $comptesQuery->get();

        $zoneRestriction = [
            'active' => (bool) ($zoneScope['restricted'] ?? false),
            'zone_count' => count($zoneScope['zone_codes'] ?? []),
            'zone_names' => $zoneScope['zone_names'] ?? [],
            'zone_label' => $zoneScope['zone_label'] ?? '',
        ];

        $operationTypeOptions = $this->getOperationTypeOptions($guichet);

        // ══════════════════════════════════════════════════════════════
        // Permission annulation bancaire : EBEN-PER25 (transactions)
        // Modèle strict bancaire: plus de cohabitation avec PER109.
        // ══════════════════════════════════════════════════════════════
        $canDeleteOperation = $user->hasPermission('EBEN-PER25');

        return view('Caisse_Guichet.operations', compact('guichet', 'user', 'operations', 'comptes', 'zoneRestriction', 'operationTypeOptions', 'canDeleteOperation', 'latestDemandesByTx'));
    }

    /**
     * Enregistre une opération de caisse.
     * Met à jour les soldes en temps réel.
     *
     * DEPOT      → crédite le compte du client  + solde guichet augmente (reçoit les espèces)
     * RETRAIT     → débite le compte du client   + solde guichet diminue  (donne les espèces)
     * PAIEMENT    → solde guichet augmente (espèces, sans compte)
     * REMBOURSEMENT → solde guichet diminue (espèces, sans compte)
     * CHANGE      → solde +montant(devise_code), -montant_dest(devise_dest)
     */
    public function store(Request $request, CommissionEngine $commissionEngine, OhadaAccountingService $accountingService)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = $this->getGuichetAgent();

        if (!$guichet) {
            return response()->json(['success' => false, 'message' => 'Aucun guichet affecté à votre compte.'], 422);
        }

        $allowedTypes = $this->getAllowedOperationTypes($guichet);

        $request->validate([
            'type_operation' => ['required', Rule::in($allowedTypes)],
            'devise_code'    => 'required|exists:tb_devises,code_iso',
            'montant'        => 'required|numeric|min:0.01',
            'observations'   => 'nullable|string|max:500',
            'compte_code'    => 'required_if:type_operation,DEPOT,RETRAIT|nullable|exists:tb_comptes,code_compte',
            'devise_dest'    => 'required_if:type_operation,CHANGE|nullable|exists:tb_devises,code_iso|different:devise_code',
            'montant_dest'   => 'required_if:type_operation,CHANGE|nullable|numeric|min:0.01',
            'taux_change'    => 'nullable|numeric|min:0',
        ], [
            'type_operation.in'        => $guichet->type_guichet === 'MOBILE'
                ? 'Sur un guichet mobile, seules les opérations de dépôt et de change sont autorisées.'
                : 'Type d\'opération invalide.',
            'compte_code.required_if'  => 'Le compte client est obligatoire pour un dépôt ou un retrait.',
            'compte_code.exists'       => 'Le numéro de compte est introuvable.',
            'devise_dest.required_if'  => 'La devise de destination est obligatoire pour un change.',
            'montant_dest.required_if' => 'Le montant destination est obligatoire pour un change.',
            'devise_dest.different'    => 'Les deux devises doivent être différentes.',
        ]);

        if ($guichet->statut_operationnel !== 'OUVERT') {
            $msg = match($guichet->statut_operationnel) {
                'EN_VERIFICATION' => 'Guichet en cours de vérification. Opérations bloquées.',
                'FERME'           => 'Guichet fermé. Ouvrez-le avant de saisir des opérations.',
                'SUSPENDU'        => 'Guichet suspendu. Reprenez la session avant de continuer.',
                default           => 'Guichet non ouvert.',
            };
            return response()->json(['success' => false, 'message' => $msg], 422);
        }

        $type    = $request->type_operation;
        $montant = (float) $request->montant;
        $devise  = $request->devise_code;
        $zoneScope = $this->resolveZoneScope();

        $compteOperation = null;
        if (in_array($type, [Transaction::DEPOT, Transaction::RETRAIT], true)) {
            $compteOperation = Compte::with('client')->where('code_compte', $request->compte_code)->first();
            if (!$compteOperation) {
                return response()->json(['success' => false, 'message' => 'Compte client introuvable.'], 422);
            }

            if (!$this->canAccessCompte($compteOperation, $zoneScope)) {
                Log::warning('[Caisse] Opération refusée hors zone', [
                    'type_operation' => $type,
                    'compte_code' => $request->compte_code,
                    'agent_matricule' => $user->agent_matricule,
                    'ip' => request()->ip(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Accès refusé : ce compte client est hors de votre zone affectée.',
                ], 403);
            }
        }

        $commissionContext = $this->buildCommissionContext(
            $type,
            $compteOperation,
            $guichet,
            $devise,
            $montant,
            $user->agent_matricule
        );

        $commissionPreviewAmount = $this->previewCommissionAmount($commissionEngine, $commissionContext);
        $compteImpact = $this->computeCompteImpact($type, $montant, $commissionPreviewAmount);
        $soldeCompteAvant = $compteOperation ? (float) $compteOperation->solde_reel : null;

        if ($type === Transaction::DEPOT && $compteOperation && $compteImpact['delta'] <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Commission invalide : le montant net credite au client doit etre strictement positif.',
            ], 422);
        }

        // Récupérer le solde pour la devise source
        $solde = CaissesGuichetSolde::where('guichet_id', $guichet->id)
            ->where('devise_code', $devise)
            ->first();

        if (!$solde) {
            return response()->json([
                'success' => false,
                'message' => "La devise {$devise} n'est pas disponible sur ce guichet. Contactez l'administration.",
            ], 422);
        }

        // Vérification des soldes suffisants
        if (in_array($type, [Transaction::RETRAIT, Transaction::REMBOURSEMENT])) {
            if ((float) $solde->solde_en_caisse < $montant) {
                return response()->json([
                    'success' => false,
                    'message' => "Solde guichet insuffisant en {$devise}. Disponible : "
                               . number_format((float)$solde->solde_en_caisse, 2, ',', ' ')
                               . " {$devise}.",
                ], 422);
            }
        }

        // Pour un RETRAIT : vérifier aussi le solde du compte client (montant + commission)
        if ($type === Transaction::RETRAIT && $request->filled('compte_code')) {
            $totalDebiteClient = (float) $compteImpact['total_client'];
            if ((float) $compteOperation->solde_reel < $totalDebiteClient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solde compte insuffisant. Disponible sur le compte '
                               . $compteOperation->code_compte . ' : '
                               . number_format((float)$compteOperation->solde_reel, 2, ',', ' ')
                               . ' ' . $compteOperation->devise . '. Montant total debite (avec commission) : '
                               . number_format($totalDebiteClient, 2, ',', ' ')
                               . ' ' . $compteOperation->devise . '.',
                ], 422);
            }
        }

        // Vérifications supplémentaires pour CHANGE
        if ($type === Transaction::CHANGE) {
            $deviseDest  = $request->devise_dest;
            $montantDest = (float) $request->montant_dest;

            $soldeDest = CaissesGuichetSolde::where('guichet_id', $guichet->id)
                ->where('devise_code', $deviseDest)
                ->first();

            if (!$soldeDest) {
                return response()->json([
                    'success' => false,
                    'message' => "La devise destination {$deviseDest} n'est pas disponible sur ce guichet.",
                ], 422);
            }

            if ((float) $soldeDest->solde_en_caisse < $montantDest) {
                return response()->json([
                    'success' => false,
                    'message' => "Solde insuffisant en {$deviseDest} pour effectuer cet échange. "
                               . "Disponible : " . number_format((float)$soldeDest->solde_en_caisse, 2, ',', ' ')
                               . " {$deviseDest}.",
                ], 422);
            }
        }

        // Générer la référence
        $reference = 'OP-' . now()->format('Ymd-His') . '-'
                   . strtoupper(substr($user->agent_matricule ?? 'XXXX', 0, 4));

        $transaction = null;
        $commissionSnapshot = null;
        $soldeCompteApres = $compteOperation ? round($soldeCompteAvant + (float) $compteImpact['delta'], 2) : null;
        $finalCompteDelta = (float) $compteImpact['delta'];

        try {
            DB::transaction(function () use ($request, $guichet, $user, $type, $montant, $devise, $reference, $commissionEngine, $accountingService, $compteOperation, $commissionContext, $commissionPreviewAmount, $soldeCompteAvant, &$soldeCompteApres, &$finalCompteDelta, &$transaction, &$commissionSnapshot) {

                // 1. Enregistrer l'opération
                $transaction = Transaction::create([
                    'reference'       => $reference,
                    'guichet_id'      => $guichet->id,
                    'agent_matricule' => $user->agent_matricule,
                    'compte_code'     => in_array($type, [Transaction::DEPOT, Transaction::RETRAIT])
                                            ? $request->compte_code
                                            : null,
                    'type'            => $type,
                    'devise_code'     => $devise,
                    'montant'         => $montant,
                    'devise_dest'     => $type === Transaction::CHANGE ? $request->devise_dest            : null,
                    'montant_dest'    => $type === Transaction::CHANGE ? (float)$request->montant_dest    : null,
                    'taux_change'     => $request->taux_change ? (float)$request->taux_change : null,
                    'observations'    => $request->observations,
                    'statut'          => Transaction::CONFIRME,
                    'date_operation'  => now(),
                    'montant_commission_total' => $commissionPreviewAmount,
                    'solde_compte_avant' => $soldeCompteAvant,
                    'solde_compte_apres' => $soldeCompteApres,
                    'montant_total_client' => in_array($type, [Transaction::DEPOT, Transaction::RETRAIT], true)
                        ? abs($finalCompteDelta)
                        : null,
                    'montant_net_client' => in_array($type, [Transaction::DEPOT, Transaction::RETRAIT], true)
                        ? $finalCompteDelta
                        : null,
                ]);

                // 2. Mettre à jour les soldes
                switch ($type) {
                    case Transaction::DEPOT:
                        // Le guichet reçoit les espèces → solde guichet augmente
                        CaissesGuichetSolde::where('guichet_id', $guichet->id)
                            ->where('devise_code', $devise)
                            ->increment('solde_en_caisse', $montant);
                        // Crédit net du compte client (montant - commission)
                        if ($finalCompteDelta >= 0) {
                            Compte::where('code_compte', $request->compte_code)
                                ->increment('solde_reel', $finalCompteDelta);
                        } else {
                            Compte::where('code_compte', $request->compte_code)
                                ->decrement('solde_reel', abs($finalCompteDelta));
                        }
                        break;

                    case Transaction::RETRAIT:
                        // Le guichet donne les espèces → solde guichet diminue
                        CaissesGuichetSolde::where('guichet_id', $guichet->id)
                            ->where('devise_code', $devise)
                            ->decrement('solde_en_caisse', $montant);
                        // Débit compte client (montant + commission)
                        Compte::where('code_compte', $request->compte_code)
                            ->decrement('solde_reel', abs($finalCompteDelta));
                        break;

                    case Transaction::PAIEMENT:
                        // Espèces sans compte — solde guichet augmente
                        CaissesGuichetSolde::where('guichet_id', $guichet->id)
                            ->where('devise_code', $devise)
                            ->increment('solde_en_caisse', $montant);
                        break;

                    case Transaction::REMBOURSEMENT:
                        // Espèces sans compte — solde guichet diminue
                        CaissesGuichetSolde::where('guichet_id', $guichet->id)
                            ->where('devise_code', $devise)
                            ->decrement('solde_en_caisse', $montant);
                        break;

                    case Transaction::CHANGE:
                        $deviseDest  = $request->devise_dest;
                        $montantDest = (float) $request->montant_dest;
                        // Le guichet reçoit la devise source
                        CaissesGuichetSolde::where('guichet_id', $guichet->id)
                            ->where('devise_code', $devise)
                            ->increment('solde_en_caisse', $montant);
                        // Le guichet donne la devise destination
                        CaissesGuichetSolde::where('guichet_id', $guichet->id)
                            ->where('devise_code', $deviseDest)
                            ->decrement('solde_en_caisse', $montantDest);
                        break;
                }

                $commissionSnapshot = $commissionEngine->applyToTransaction($transaction, $commissionContext);

                $commissionFinale = (float) ($commissionSnapshot?->montant_commission ?? 0);
                $ecartCommission = round($commissionFinale - $commissionPreviewAmount, 2);

                if (in_array($type, [Transaction::DEPOT, Transaction::RETRAIT], true) && $compteOperation && $ecartCommission !== 0.0) {
                    // Ajuster le compte client si la commission finale diffère de la prévisualisation.
                    if ($ecartCommission > 0) {
                        Compte::where('code_compte', $request->compte_code)->decrement('solde_reel', $ecartCommission);
                    } else {
                        Compte::where('code_compte', $request->compte_code)->increment('solde_reel', abs($ecartCommission));
                    }

                    $finalCompteDelta = round($finalCompteDelta - $ecartCommission, 2);
                }

                $soldeCompteApres = $soldeCompteAvant !== null
                    ? round($soldeCompteAvant + $finalCompteDelta, 2)
                    : null;

                $transaction->forceFill([
                    'montant_commission_total' => $commissionFinale,
                    'solde_compte_apres' => $soldeCompteApres,
                    'montant_total_client' => in_array($type, [Transaction::DEPOT, Transaction::RETRAIT], true)
                        ? abs($finalCompteDelta)
                        : null,
                    'montant_net_client' => in_array($type, [Transaction::DEPOT, Transaction::RETRAIT], true)
                        ? $finalCompteDelta
                        : null,
                ])->save();

                $accountingService->postTransaction($transaction, [
                    'commission' => $commissionFinale,
                    'agent_matricule' => $user->agent_matricule,
                    'commission_trace' => [
                        'snapshot_id' => $commissionSnapshot?->id,
                        'rule_id' => $commissionSnapshot?->commission_rule_id,
                        'libelle' => $commissionSnapshot?->libelle,
                        'mode' => $commissionSnapshot?->mode_calcul,
                        'valeur' => (float) ($commissionSnapshot?->valeur_snapshot ?? 0),
                        'base' => (float) ($commissionSnapshot?->base_calcul ?? $transaction->montant ?? 0),
                        'montant' => $commissionFinale,
                        'has_rule' => (bool) ($commissionSnapshot?->commission_rule_id),
                    ],
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage(),
            ], 500);
        }

        // Retourner les soldes mis à jour pour rafraîchir l'interface
        $guichet->unsetRelation('soldes');
        $soldesMaj = $guichet->load('soldes.devise')->soldes
            ->sortBy('devise_code')
            ->map(fn($s) => [
                'devise_code'    => $s->devise_code,
                'symbole'        => $s->devise->symbole ?? $s->devise_code,
                'solde_en_caisse'=> number_format((float)$s->solde_en_caisse, 2, ',', ' '),
            ])->values();

        $typeLabel = Transaction::typeLabel($type);
        $msg = "{$typeLabel} de " . number_format($montant, 2, ',', ' ') . " {$devise} enregistré. Réf : {$reference}";
        if ($commissionSnapshot) {
            $msg .= ' Commission appliquée : '
                . number_format((float) $commissionSnapshot->montant_commission, 2, ',', ' ')
                . ' '
                . ($commissionSnapshot->devise_code ?: $devise)
                . '.';
        }

        $clientOperation = null;
        if ($transaction && in_array($type, [Transaction::DEPOT, Transaction::RETRAIT], true) && $compteOperation) {
            $clientOperation = [
                'compte_code' => $transaction->compte_code,
                'solde_avant' => (float) ($transaction->solde_compte_avant ?? 0),
                'solde_apres' => (float) ($transaction->solde_compte_apres ?? 0),
                'montant_total_client' => (float) ($transaction->montant_total_client ?? 0),
                'montant_net_client' => (float) ($transaction->montant_net_client ?? 0),
                'solde_avant_fmt' => number_format((float) ($transaction->solde_compte_avant ?? 0), 2, ',', ' ') . ' ' . $compteOperation->devise,
                'solde_apres_fmt' => number_format((float) ($transaction->solde_compte_apres ?? 0), 2, ',', ' ') . ' ' . $compteOperation->devise,
                'montant_total_client_fmt' => number_format((float) ($transaction->montant_total_client ?? 0), 2, ',', ' ') . ' ' . $compteOperation->devise,
                'montant_net_client_fmt' => number_format((float) ($transaction->montant_net_client ?? 0), 2, ',', ' ') . ' ' . $compteOperation->devise,
            ];

            $msg .= ' Solde client: avant '
                . number_format((float) ($transaction->solde_compte_avant ?? 0), 2, ',', ' ')
                . ' ' . $compteOperation->devise
                . ' | apres '
                . number_format((float) ($transaction->solde_compte_apres ?? 0), 2, ',', ' ')
                . ' ' . $compteOperation->devise
                . '.';
        }

        // Retrouver l'id de la transaction fraîchement créée pour le bordereau
        $bordereauUrl = $transaction
            ? route('caisses.operations.bordereau', ['id' => $transaction->id])
            : null;

        return response()->json([
            'success'        => true,
            'reference'      => $reference,
            'soldes'         => $soldesMaj,
            'message'        => $msg,
            'bordereau_url'  => $bordereauUrl,
            'commission'     => $commissionSnapshot ? [
                'montant' => (float) $commissionSnapshot->montant_commission,
                'montant_fmt' => number_format((float) $commissionSnapshot->montant_commission, 2, ',', ' ') . ' ' . ($commissionSnapshot->devise_code ?: $devise),
                'libelle' => $commissionSnapshot->libelle,
                'mode_calcul' => $commissionSnapshot->mode_calcul,
            ] : null,
            'client_operation' => $clientOperation,
        ]);
    }

    /**
     * Annule une opération déjà confirmée.
     * Inverse le mouvement de solde correspondant.
     */
    public function annuler(Request $request, $id, OhadaAccountingService $accountingService)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();

        // ══════════════════════════════════════════════════════════════
        // Vérifier permission annulation transaction bancaire
        // Modèle strict bancaire : EBEN-PER25 uniquement.
        // ══════════════════════════════════════════════════════════════
        if (!$user->hasPermission('EBEN-PER25')) {
            Log::warning('[Caisse] Tentative d\'annulation sans permission (PER25)', [
                'user_id' => $user->id,
                'agent_matricule' => $user->agent_matricule,
                'transaction_id' => $id,
                'ip' => request()->ip(),
            ]);

            // Retourner la page 403 ergonomique (silencieuse, sans popup)
            return response()->view('errors.403', [], 403);
        }

        $guichet = $this->getGuichetAgent();

        if (!$guichet) {
            return response()->json(['success' => false, 'message' => 'Guichet introuvable.'], 422);
        }

        $op = Transaction::where('id', $id)
            ->where('guichet_id', $guichet->id)
            ->first();

        if (!$op) {
            Log::warning('[Caisse] Opération introuvable pour annulation', ['id' => $id, 'guichet_id' => $guichet->id, 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Opération introuvable.'], 404);
        }

        if ($op->statut === Transaction::ANNULE) {
            return response()->json(['success' => false, 'message' => 'Cette opération est déjà annulée.'], 422);
        }

        // Autoriser l'annulation uniquement le même jour
        if (!$op->date_operation->isToday()) {
            if (!$user->hasPermission('EBEN-PER1')) { // Seul un admin peut annuler une ancienne op
                return response()->json([
                    'success' => false,
                    'message' => 'L\'annulation d\'opérations antérieures requiert des droits administrateur.',
                ], 403);
            }
        }

        try {
            DB::transaction(function () use ($op, $guichet, $user, $accountingService) {
                $op->statut = Transaction::ANNULE;
                $op->save();

                $type    = $op->type;
                $montant = (float) $op->montant;
                $devise  = $op->devise_code;
                $commission = (float) ($op->montant_commission_total ?? 0);

                $montantTotalClient = $op->montant_total_client !== null
                    ? abs((float) $op->montant_total_client)
                    : match ($type) {
                        Transaction::DEPOT => round(max(0, $montant - $commission), 2),
                        Transaction::RETRAIT => round($montant + $commission, 2),
                        default => 0.0,
                    };

                // Inverser le mouvement de solde (et de compte le cas échéant)
                switch ($type) {
                    case Transaction::DEPOT:
                        // Annulation dépôt : le guichet redonne les espèces, on débite le compte
                        CaissesGuichetSolde::where('guichet_id', $guichet->id)
                            ->where('devise_code', $devise)
                            ->decrement('solde_en_caisse', $montant);
                        if ($op->compte_code) {
                            Compte::where('code_compte', $op->compte_code)
                                ->decrement('solde_reel', $montantTotalClient);
                        }
                        break;

                    case Transaction::PAIEMENT:
                        CaissesGuichetSolde::where('guichet_id', $guichet->id)
                            ->where('devise_code', $devise)
                            ->decrement('solde_en_caisse', $montant);
                        break;

                    case Transaction::RETRAIT:
                        // Annulation retrait : le guichet récupère les espèces, on crédite le compte
                        CaissesGuichetSolde::where('guichet_id', $guichet->id)
                            ->where('devise_code', $devise)
                            ->increment('solde_en_caisse', $montant);
                        if ($op->compte_code) {
                            Compte::where('code_compte', $op->compte_code)
                                ->increment('solde_reel', $montantTotalClient);
                        }
                        break;

                    case Transaction::REMBOURSEMENT:
                        CaissesGuichetSolde::where('guichet_id', $guichet->id)
                            ->where('devise_code', $devise)
                            ->increment('solde_en_caisse', $montant);
                        break;

                    case Transaction::CHANGE:
                        // Reprendre la devise source
                        CaissesGuichetSolde::where('guichet_id', $guichet->id)
                            ->where('devise_code', $devise)
                            ->decrement('solde_en_caisse', $montant);
                        // Rendre la devise destination
                        if ($op->devise_dest && $op->montant_dest) {
                            CaissesGuichetSolde::where('guichet_id', $guichet->id)
                                ->where('devise_code', $op->devise_dest)
                                ->increment('solde_en_caisse', (float) $op->montant_dest);
                        }
                        break;
                }

                $accountingService->postReversal($op, 'Annulation operation caisse', [
                    'agent_matricule' => $user->agent_matricule,
                ]);
            });

            app(NotificationService::class)->notifyUsersWithPermission(
                'EBEN-PER44',
                'Operation annulee',
                'L\'operation ' . $op->reference . ' a ete annulee au guichet ' . ($guichet->code_guichet ?? 'N/A') . '.',
                [
                    'type' => 'warning',
                    'icon' => 'fas fa-ban',
                    'action_url' => route('caisses.journal.page'),
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation : ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Opération ' . $op->reference . ' annulée avec succès.',
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    //  1b. RECHERCHE DE COMPTE CLIENT (AJAX)
    // ══════════════════════════════════════════════════════════════

    /**
     * Recherche un compte client par code ou nom du titulaire.
     * Utilisé par le formulaire de saisie pour les opérations DEPOT / RETRAIT.
     */
    public function searchCompte(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $zoneScope = $this->resolveZoneScope();

        $query = Compte::with('client')
            ->where(function ($query) use ($q) {
                $query->where('code_compte', 'like', "%{$q}%")
                      ->orWhereHas('client', function ($cq) use ($q) {
                          $cq->searchFullName($q)
                             ->orWhere('nom', 'like', "%{$q}%")
                             ->orWhere('postnom', 'like', "%{$q}%")
                             ->orWhere('prenom', 'like', "%{$q}%")
                             ->orWhere('matricule', 'like', "%{$q}%");
                      });
            });

        $this->applyZoneScopeToComptes($query, $zoneScope);

        $comptes = $query
            ->limit(10)
            ->get()
            ->map(fn($c) => [
                'code_compte' => $c->code_compte,
                'client_nom'  => $c->client?->full_name ?: '—',
                'devise'      => $c->devise,
                'solde'       => number_format((float) $c->solde_reel, 2, ',', ' ') . ' ' . $c->devise,
            ]);

        return response()->json($comptes);
    }

    /**
     * Prévisualise la commission et l'impact client en temps réel.
     * GET /caisses/operations/commission-preview
     */
    public function commissionPreview(Request $request, CommissionEngine $commissionEngine)
    {
        $guichet = $this->getGuichetAgent();
        if (!$guichet) {
            return response()->json([
                'ready' => false,
                'message' => 'Aucun guichet affecte a votre profil.',
            ], 422);
        }

        $type = strtoupper(trim((string) $request->input('type_operation', '')));
        $devise = strtoupper(trim((string) $request->input('devise_code', '')));
        $montant = (float) $request->input('montant', 0);
        $compteCode = trim((string) $request->input('compte_code', ''));

        $allowedTypes = $this->getAllowedOperationTypes($guichet);
        if ($type === '' || !in_array($type, $allowedTypes, true)) {
            return response()->json([
                'ready' => false,
                'message' => 'Selectionnez un type operation valide.',
            ]);
        }

        if ($devise === '' || $montant <= 0) {
            return response()->json([
                'ready' => false,
                'message' => 'Renseignez la devise et un montant superieur a 0.',
            ]);
        }

        $zoneScope = $this->resolveZoneScope();
        $compteOperation = null;
        $requiresCompte = in_array($type, [Transaction::DEPOT, Transaction::RETRAIT], true);

        if ($requiresCompte) {
            if ($compteCode === '') {
                return response()->json([
                    'ready' => false,
                    'requires_compte' => true,
                    'message' => 'Selectionnez un compte client.',
                ]);
            }

            $compteOperation = Compte::with('client')->where('code_compte', $compteCode)->first();
            if (!$compteOperation) {
                return response()->json([
                    'ready' => false,
                    'requires_compte' => true,
                    'message' => 'Compte client introuvable.',
                ], 404);
            }

            if (!$this->canAccessCompte($compteOperation, $zoneScope)) {
                return response()->json([
                    'ready' => false,
                    'requires_compte' => true,
                    'message' => 'Acces refuse a ce compte client.',
                ], 403);
            }

            if (strtoupper((string) $compteOperation->devise) !== $devise) {
                return response()->json([
                    'ready' => false,
                    'requires_compte' => true,
                    'message' => 'La devise operation doit correspondre a la devise du compte.',
                ]);
            }
        }

        $commissionContext = $this->buildCommissionContext(
            $type,
            $compteOperation,
            $guichet,
            $devise,
            $montant,
            Auth::user()?->agent_matricule
        );

        $rule = $commissionEngine->resolveRule($commissionContext, now());
        $commissionAmount = $rule
            ? $commissionEngine->calculateCommission($rule, $montant)
            : 0.0;

        $impact = $this->computeCompteImpact($type, $montant, $commissionAmount);

        $soldeAvant = $compteOperation ? (float) $compteOperation->solde_reel : null;
        $soldeApres = $soldeAvant !== null
            ? round($soldeAvant + (float) $impact['delta'], 2)
            : null;

        return response()->json([
            'ready' => true,
            'message' => null,
            'commission' => [
                'has_rule' => (bool) $rule,
                'rule_id' => $rule?->id,
                'libelle' => $rule?->libelle ?? 'Aucune regle de commission applicable',
                'mode' => $rule?->mode_calcul ?? CommissionRule::MODE_FIXED,
                'valeur' => (float) ($rule?->valeur ?? 0),
                'montant' => (float) $commissionAmount,
                'devise_code' => $devise,
            ],
            'impact' => [
                'sens' => $impact['sens'],
                'delta' => (float) $impact['delta'],
                'total_client' => $impact['total_client'] !== null ? (float) $impact['total_client'] : null,
                'formule' => $type === Transaction::RETRAIT
                    ? 'Montant total debite = montant retrait + commission'
                    : ($type === Transaction::DEPOT ? 'Montant net credite = montant depot - commission' : null),
            ],
            'compte' => $compteOperation ? [
                'code_compte' => $compteOperation->code_compte,
                'devise' => $compteOperation->devise,
                'solde_avant' => $soldeAvant,
                'solde_apres' => $soldeApres,
                'insuffisant' => $type === Transaction::RETRAIT && $soldeApres !== null && $soldeApres < 0,
            ] : null,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    //  2. JOURNAL DES OPÉRATIONS
    // ══════════════════════════════════════════════════════════════

    /**
     * Page du journal de caisse.
     */
    public function journalPage()
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = $this->getGuichetAgent();
        $operationTypeOptions = $this->getOperationTypeFilterOptions($guichet);

        return view('Caisse_Guichet.journal', compact('guichet', 'user', 'operationTypeOptions'));
    }

    /**
     * Retourne les opérations en JSON (pour le journal AJAX).
     * Filtres : date (YYYY-MM-DD), type_operation
     */
    public function journal(Request $request)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = $this->getGuichetAgent();

        if (!$guichet) {
            return response()->json(['operations' => [], 'totaux' => [], 'date' => today()->toDateString()]);
        }

        $date = $request->filled('date') ? $request->date : today()->toDateString();
        $type = $request->input('type', 'TOUS');
        $allowedTypes = $this->getAllowedOperationTypes($guichet);

        $query = Transaction::where('guichet_id', $guichet->id)
            ->with('compte.client')
            ->whereDate('date_operation', $date)
            ->whereIn('type', $allowedTypes)
            ->orderByDesc('date_operation');

        if ($type !== 'TOUS' && in_array($type, $allowedTypes, true)) {
            $query->where('type', $type);
        }

        $opsRows = $query->limit(300)->get();

        $latestDemandes = collect();
        if ($opsRows->isNotEmpty()) {
            $latestDemandes = DemandeModification::whereIn('transaction_id', $opsRows->pluck('id')->all())
                ->orderByDesc('id')
                ->get()
                ->unique('transaction_id')
                ->keyBy('transaction_id');
        }

        $ops = $opsRows->map(fn($op) => [
            'id'              => $op->id,
            'reference'       => $op->reference,
            'compte_code'     => $op->compte_code,
            'client_full_name'=> optional(optional($op->compte)->client)->full_name,
            'type'            => $op->type,
            'type_label'      => Transaction::typeLabel($op->type),
            'badge_class'     => Transaction::typeBadgeClass($op->type),
            'icon'            => Transaction::typeIcon($op->type),
            'devise'          => $op->devise_code,
            'montant'         => (float) $op->montant,
            'montant_fmt'     => number_format((float)$op->montant, 2, ',', ' ') . ' ' . $op->devise_code,
            'devise_dest'     => $op->devise_dest,
            'montant_dest_fmt'=> $op->montant_dest
                                    ? number_format((float)$op->montant_dest, 2, ',', ' ') . ' ' . $op->devise_dest
                                    : null,
            'taux_change'     => $op->taux_change,
            'statut'          => $op->statut,
            'date'            => $op->date_operation?->format('d/m/Y H:i:s'),
            'observations'    => $op->observations,
            'demande_id'      => $latestDemandes->get($op->id)?->id,
            'demande_statut'  => $latestDemandes->get($op->id)?->statut,
            'demande_type'    => $latestDemandes->get($op->id)?->type_demande,
        ]);

        // Totaux par type (opérations confirmées uniquement)
        $toutes = Transaction::where('guichet_id', $guichet->id)
            ->whereDate('date_operation', $date)
            ->whereIn('type', $allowedTypes)
            ->where('statut', Transaction::CONFIRME)
            ->get();

        $totaux = $toutes->groupBy('type')->map(fn($items, $t) => [
            'type'       => $t,
            'type_label' => Transaction::typeLabel($t),
            'count'      => $items->count(),
            'par_devise' => $items->groupBy('devise_code')->map(fn($d, $dev) => [
                'devise' => $dev,
                'total'  => round($d->sum(fn($i) => (float)$i->montant), 2),
                'fmt'    => number_format($d->sum(fn($i) => (float)$i->montant), 2, ',', ' ') . ' ' . $dev,
            ])->values(),
        ])->values();

        return response()->json([
            'operations' => $ops,
            'totaux'     => $totaux,
            'total_count'=> $ops->count(),
            'date'       => $date,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    //  3. RAPPORT DE FIN DE JOURNÉE (guichets FIXE)
    // ══════════════════════════════════════════════════════════════

    /**
     * Rapport de réconciliation journalière pour les guichets de bureau (FIXE).
     * Affiche : activité par type, soldes comptables actuels, évolution intra-journalière.
     */
    public function rapportFinJournee(Request $request)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = $this->getGuichetAgent();

        // Cette page est accessible aux FIXE et CENTRAL, pas aux MOBILE
        if ($guichet && $guichet->type_guichet === 'MOBILE') {
            abort(403, 'Page réservée aux guichets de bureau (FIXE).');
        }

        $date = $request->input('date', today()->toDateString());

        $stats = [
            'total_operations' => 0,
            'par_type'         => collect(),
            'soldes_actuels'   => collect(),
            'par_devise'       => collect(),
        ];

        if ($guichet) {
            $ops = Transaction::where('guichet_id', $guichet->id)
                ->whereDate('date_operation', $date)
                ->where('statut', Transaction::CONFIRME)
                ->orderBy('date_operation')
                ->get();

            $stats['total_operations'] = $ops->count();

            $stats['par_type'] = $ops->groupBy('type')->map(fn($items, $t) => [
                'type'       => $t,
                'label'      => Transaction::typeLabel($t),
                'badge'      => Transaction::typeBadgeClass($t),
                'icon'       => Transaction::typeIcon($t),
                'count'      => $items->count(),
                'par_devise' => $items->groupBy('devise_code')->map(fn($d, $dev) => [
                    'devise' => $dev,
                    'total'  => round($d->sum(fn($i) => (float)$i->montant), 2),
                    'fmt'    => number_format($d->sum(fn($i) => (float)$i->montant), 2, ',', ' ') . ' ' . $dev,
                ])->values(),
            ])->values();

            $entryTypes = [Transaction::DEPOT, Transaction::PAIEMENT];
            $exitTypes  = [Transaction::RETRAIT, Transaction::REMBOURSEMENT];

            $stats['par_devise'] = $ops->groupBy('devise_code')->map(function ($items, $devise) use ($entryTypes, $exitTypes) {
                $totalEntrees = (float) $items->whereIn('type', $entryTypes)->sum('montant');
                $totalSorties = (float) $items->whereIn('type', $exitTypes)->sum('montant');

                return [
                    'devise' => $devise,
                    'count' => $items->count(),
                    'total_entrees' => $totalEntrees,
                    'total_sorties' => $totalSorties,
                    'net' => $totalEntrees - $totalSorties,
                    'volume_total' => (float) $items->sum('montant'),
                ];
            })->sortBy('devise')->values();

            $stats['soldes_actuels'] = $guichet->fresh('soldes.devise')->soldes
                ->sortBy('devise_code')
                ->map(fn($s) => [
                    'devise_code' => $s->devise_code,
                    'nom'         => $s->devise->nom     ?? $s->devise_code,
                    'symbole'     => $s->devise->symbole ?? $s->devise_code,
                    'solde'       => (float) $s->solde_en_caisse,
                    'solde_fmt'   => number_format((float)$s->solde_en_caisse, 2, ',', ' '),
                ])->values();

            // Dernières opérations pour le tableau récapitulatif
            $stats['ops_recentes'] = $ops->take(50)->reverse()->map(fn($op) => [
                'reference'   => $op->reference,
                'type_label'  => Transaction::typeLabel($op->type),
                'badge_class' => Transaction::typeBadgeClass($op->type),
                'montant_fmt' => number_format((float)$op->montant, 2, ',', ' ') . ' ' . $op->devise_code,
                'heure'       => $op->date_operation?->format('H:i'),
            ])->values();
        }

        return view('Caisse_Guichet.rapport_fin_journee', compact('guichet', 'user', 'stats', 'date'));
    }

    // ══════════════════════════════════════════════════════════════
    //  4. GESTION MOBILE (guichets MOBILE uniquement)
    // ══════════════════════════════════════════════════════════════

    /**
     * Page de gestion des départs et retours pour agents mobiles.
     * - Demande de dotation (matin) → alerte le trésorier
     * - Déclaration de reversement (soir) → confirme le retour de fonds
     */
    public function mobileIndex()
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = $this->getGuichetAgent();

        // Accès interdit aux guichets de bureau (FIXE). CENTRAL non concerné.
        if ($guichet && $guichet->type_guichet === 'FIXE') {
            abort(403, 'Page réservée aux guichets mobiles.');
        }

        // Historique dotations + reversements des 30 derniers jours
        $historique = [];
        if ($guichet) {
            $historique = MouvementInterCaisse::where(function ($q) use ($guichet) {
                    // Dotations reçues (coffre → mobile)
                    $q->where('guichet_dest_id', $guichet->id)
                      ->where('type_flux', 'DOTATION_MOBILE');
                })
                ->orWhere(function ($q) use ($guichet) {
                    // Reversements effectués (mobile → coffre)
                    $q->where('guichet_source_id', $guichet->id)
                      ->where('type_flux', 'REVERSEMENT_MOBILE');
                })
                ->orderByDesc('date_mouvement')
                ->limit(60)
                ->get()
                ->map(fn($m) => [
                    'id'          => $m->id,
                    'type'        => $m->type_flux,
                    'type_label'  => $m->type_flux === 'DOTATION_MOBILE' ? 'Dotation' : 'Reversement',
                    'badge_class' => $m->type_flux === 'DOTATION_MOBILE' ? 'badge-info' : 'badge-success',
                    'devise'      => $m->devise_code,
                    'montant_fmt' => number_format((float)$m->montant, 2, ',', ' ') . ' ' . $m->devise_code,
                    'statut'      => $m->statut,
                    'date'        => $m->date_mouvement?->format('d/m/Y H:i'),
                    'obs'         => $m->observations,
                ]);
        }

        return view('Caisse_Guichet.mobile', compact('guichet', 'user', 'historique'));
    }

    /**
     * Soumet une demande de dotation au trésorier.
     * (Agent mobile indique les montants dont il a besoin par devise)
     */
    public function mobileDepart(Request $request)
    {
        $request->validate([
            'dotations'               => 'required|array|min:1',
            'dotations.*.devise_code' => 'required|exists:tb_devises,code_iso',
            'dotations.*.montant'     => 'required|numeric|min:1',
            'observations'            => 'nullable|string|max:500',
        ], [
            'dotations.min' => 'Ajoutez au moins une devise.',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = $this->getGuichetAgent();

        if (!$guichet || $guichet->type_guichet !== 'MOBILE') {
            return response()->json([
                'success' => false,
                'message' => 'Formulaire réservé aux guichets mobiles.',
            ], 403);
        }

        $refs = [];
        try {
            DB::transaction(function () use ($request, $guichet, $user, &$refs) {
                foreach ($request->dotations as $d) {
                    if ((float) $d['montant'] <= 0) continue;

                    $ref = 'DOT-' . now()->format('YmdHis') . '-'
                         . strtoupper(substr($user->agent_matricule ?? 'XXXX', 0, 4));

                    MouvementInterCaisse::create([
                        'guichet_source_id'   => null,  // Le trésorier associe le coffre à l'approbation
                        'guichet_dest_id'     => $guichet->id,
                        'agent_initiateur'    => $user->agent_matricule,
                        'type_flux'           => 'DOTATION_MOBILE',
                        'montant'             => (float) $d['montant'],
                        'devise_code'         => $d['devise_code'],
                        'reference_bordereau' => $ref,
                        'date_mouvement'      => now(),
                        'statut'              => 'EN_ATTENTE',
                        'observations'        => $request->observations,
                    ]);

                    $refs[] = $ref;
                }
            });

            app(NotificationService::class)->notifyUsersWithPermission(
                'EBEN-PER46',
                'Demande de dotation mobile',
                'Le guichet mobile ' . $guichet->code_guichet . ' a soumis ' . count($refs) . ' demande(s) de dotation.',
                [
                    'type' => 'action_required',
                    'icon' => 'fas fa-shipping-fast',
                    'action_url' => route('tresorerie.etat-coffre'),
                    'meta' => ['references' => $refs],
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'refs'    => $refs,
            'message' => count($refs) . ' demande(s) de dotation envoyée(s) au trésorier. '
                       . 'Références : ' . implode(', ', $refs),
        ]);
    }

    /**
     * Déclare un reversement de fonds au coffre (fin de mission).
     * (Agent mobile indique les montants qu'il ramène par devise)
     */
    public function mobileRetour(Request $request)
    {
        $request->validate([
            'reversements'               => 'required|array|min:1',
            'reversements.*.devise_code' => 'required|exists:tb_devises,code_iso',
            'reversements.*.montant'     => 'required|numeric|min:0',
            'observations'               => 'nullable|string|max:500',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = $this->getGuichetAgent();

        if (!$guichet || $guichet->type_guichet !== 'MOBILE') {
            return response()->json([
                'success' => false,
                'message' => 'Formulaire réservé aux guichets mobiles.',
            ], 403);
        }

        $refs = [];
        try {
            DB::transaction(function () use ($request, $guichet, $user, &$refs) {
                foreach ($request->reversements as $r) {
                    if ((float) $r['montant'] <= 0) continue;

                    $ref = 'REV-' . now()->format('YmdHis') . '-'
                         . strtoupper(substr($user->agent_matricule ?? 'XXXX', 0, 4));

                    MouvementInterCaisse::create([
                        'guichet_source_id'   => $guichet->id,
                        'guichet_dest_id'     => null,  // Le trésorier associe le coffre à la confirmation
                        'agent_initiateur'    => $user->agent_matricule,
                        'type_flux'           => 'REVERSEMENT_MOBILE',
                        'montant'             => (float) $r['montant'],
                        'devise_code'         => $r['devise_code'],
                        'reference_bordereau' => $ref,
                        'date_mouvement'      => now(),
                        'statut'              => 'EN_ATTENTE',
                        'observations'        => $request->observations,
                    ]);

                    $refs[] = $ref;
                }
            });

            app(NotificationService::class)->notifyUsersWithPermission(
                'EBEN-PER46',
                'Reversement mobile declare',
                'Le guichet mobile ' . $guichet->code_guichet . ' a declare un reversement en attente de confirmation.',
                [
                    'type' => 'action_required',
                    'icon' => 'fas fa-undo-alt',
                    'action_url' => route('tresorerie.etat-coffre'),
                    'meta' => ['references' => $refs],
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'refs'    => $refs,
            'message' => 'Reversement déclaré. Le trésorier confirmera la réception. '
                       . 'Références : ' . implode(', ', $refs),
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    //  WORKFLOW MODIFICATION/SUPPRESSION AVEC APPROBATION SUPERVISEUR
    // ══════════════════════════════════════════════════════════════

    /**
     * Retourne l'état de la dernière demande liée à une opération.
     * GET /caisses/operations/{id}/demande-statut
     */
    public function statutDemandeModification($id)
    {
        $guichet = $this->getGuichetAgent();

        if (!$guichet) {
            return response()->json([
                'success' => false,
                'can_submit' => false,
                'reason' => 'AUCUN_GUICHET',
                'message' => 'Aucun guichet affecté.',
            ], 422);
        }

        $op = Transaction::where('id', $id)
            ->where('guichet_id', $guichet->id)
            ->first();

        if (!$op) {
            return response()->json([
                'success' => false,
                'can_submit' => false,
                'reason' => 'OP_INTROUVABLE',
                'message' => 'Opération introuvable pour ce guichet.',
            ], 404);
        }

        $pending = DemandeModification::where('transaction_id', $id)
            ->where('statut', DemandeModification::EN_ATTENTE)
            ->latest('id')
            ->first();

        $approved = DemandeModification::where('transaction_id', $id)
            ->where('statut', DemandeModification::APPROUVEE)
            ->latest('id')
            ->first();

        $latest = DemandeModification::where('transaction_id', $id)
            ->latest('id')
            ->first();

        $reason = 'AUCUNE_DEMANDE';
        $canSubmit = true;
        $message = 'Aucune demande en attente. Vous pouvez soumettre une nouvelle demande.';

        if ($op->statut !== Transaction::CONFIRME) {
            $reason = 'OP_NON_CONFIRMEE';
            $canSubmit = false;
            $message = 'Cette opération n\'est plus modifiable (déjà annulée ou non confirmée).';
        } elseif ($pending) {
            $reason = 'DEMANDE_EN_ATTENTE';
            $canSubmit = false;
            $message = 'Une demande est déjà en attente pour cette opération (#' . $pending->id . ').';
        } elseif ($approved) {
            $reason = 'DEMANDE_DEJA_TRAITEE_APPROUVEE';
            $canSubmit = false;
            $message = 'Une demande a déjà été approuvée pour cette opération (#' . $approved->id . '). Aucune nouvelle demande autorisée.';
        } elseif ($latest && $latest->statut === DemandeModification::REJETEE) {
            $reason = 'DERNIERE_REJETEE';
            $canSubmit = true;
            $message = 'La dernière demande a été rejetée. Vous pouvez soumettre une nouvelle demande.';
        }

        return response()->json([
            'success' => true,
            'can_submit' => $canSubmit,
            'reason' => $reason,
            'message' => $message,
            'operation_statut' => $op->statut,
            'latest_demande' => $latest ? [
                'id' => $latest->id,
                'type_demande' => $latest->type_demande,
                'statut' => $latest->statut,
                'motif' => $latest->motif,
                'commentaire_superviseur' => $latest->commentaire_superviseur,
                'demandee_le' => $latest->created_at?->format('d/m/Y H:i'),
                'traitee_le' => $latest->traitee_le?->format('d/m/Y H:i'),
            ] : null,
        ]);
    }

    /**
     * Guichetier soumet une demande de modification ou suppression d'une opération.
     * POST /caisses/operations/{id}/demande
     */
    public function demanderModification(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'type_demande'         => 'required|in:MODIFICATION,SUPPRESSION',
            'motif'                => 'required|string|min:5|max:500',
            'nouveau_montant'      => 'required_if:type_demande,MODIFICATION|nullable|numeric|min:0.01',
            'nouvelles_observations' => 'nullable|string|max:500',
        ], [
            'motif.required'            => 'Le motif de la demande est obligatoire.',
            'motif.min'                 => 'Le motif doit comporter au moins 5 caractères.',
            'nouveau_montant.required_if'=> 'Le nouveau montant est requis pour une modification.',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = $this->getGuichetAgent();

        if (!$guichet) {
            return response()->json(['success' => false, 'message' => 'Aucun guichet affecté.'], 422);
        }

        $op = Transaction::where('id', $id)
            ->where('guichet_id', $guichet->id)
            ->where('statut', Transaction::CONFIRME)
            ->first();

        if (!$op) {
            Log::warning('[Caisse] Opération introuvable pour demande modification', ['id' => $id, 'guichet_id' => $guichet->id, 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Opération introuvable ou non confirmée.'], 404);
        }

        // Vérifier qu'il n'existe pas déjà une demande EN_ATTENTE pour cette opération
        $existante = DemandeModification::where('transaction_id', $id)
            ->where('statut', DemandeModification::EN_ATTENTE)
            ->first();

        if ($existante) {
            return response()->json([
                'success' => false,
                'message' => 'Une demande est déjà en attente pour cette opération (#' . $existante->id . ').',
                'reason'  => 'DEMANDE_EN_ATTENTE',
                'current' => [
                    'id'            => $existante->id,
                    'type_demande'  => $existante->type_demande,
                    'statut'        => $existante->statut,
                    'demandee_le'   => $existante->created_at?->format('d/m/Y H:i'),
                    'traitee_le'    => $existante->traitee_le?->format('d/m/Y H:i'),
                ],
            ], 422);
        }

        // Si une demande a déjà été approuvée pour cette opération, on verrouille.
        $dejaApprouvee = DemandeModification::where('transaction_id', $id)
            ->where('statut', DemandeModification::APPROUVEE)
            ->latest('id')
            ->first();

        if ($dejaApprouvee) {
            return response()->json([
                'success' => false,
                'message' => 'Cette opération est verrouillée: une demande (#' . $dejaApprouvee->id . ') a déjà été approuvée.',
                'reason'  => 'DEMANDE_DEJA_TRAITEE_APPROUVEE',
                'current' => [
                    'id'            => $dejaApprouvee->id,
                    'type_demande'  => $dejaApprouvee->type_demande,
                    'statut'        => $dejaApprouvee->statut,
                    'demandee_le'   => $dejaApprouvee->created_at?->format('d/m/Y H:i'),
                    'traitee_le'    => $dejaApprouvee->traitee_le?->format('d/m/Y H:i'),
                ],
            ], 422);
        }

        // Info client pour l'audit
        $clientNom = null;
        if ($op->compte_code) {
            $compte = \App\Models\Clients\Compte::with('client')->where('code_compte', $op->compte_code)->first();
            if ($compte && $compte->client) {
                $clientNom = trim(($compte->client->nom ?? '') . ' ' . ($compte->client->postnom ?? '') . ' ' . ($compte->client->prenom ?? ''));
            }
        }

        $demande = DemandeModification::create([
            'transaction_id'         => $op->id,
            'reference_operation'    => $op->reference,
            'guichet_id'             => $guichet->id,
            'compte_code'            => $op->compte_code,
            'client_nom'             => $clientNom,
            'type_operation'         => $op->type,
            'devise_code'            => $op->devise_code,
            'ancien_montant'         => $op->montant,
            'anciennes_observations' => $op->observations,
            'type_demande'           => $request->type_demande,
            'agent_matricule'        => $user->agent_matricule,
            'motif'                  => $request->motif,
            'nouveau_montant'        => $request->type_demande === 'MODIFICATION' ? $request->nouveau_montant : null,
            'nouvelles_observations' => $request->nouvelles_observations,
            'statut'                 => DemandeModification::EN_ATTENTE,
        ]);

        app(NotificationService::class)->notifyUsersWithPermission(
            'EBEN-PER44',
            'Nouvelle demande de modification',
            'Demande #' . $demande->id . ' (' . $demande->type_demande . ') sur l\'operation ' . $op->reference . ' en attente de traitement.',
            [
                'type' => 'warning',
                'icon' => 'fas fa-edit',
                'action_url' => route('caisses.demandes.modification.page'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Demande soumise au superviseur. Vous serez informé de la décision.',
        ]);
    }

    /**
     * Nombre de demandes EN_ATTENTE (badge sidebar superviseur).
     * GET /caisses/demandes-modification/count
     */
    public function demandesModificationCount()
    {
        return response()->json([
            'count' => \App\Models\Caisse\DemandeModification::where('statut', \App\Models\Caisse\DemandeModification::EN_ATTENTE)->count(),
        ]);
    }

    /**
     * Vue superviseur — liste des demandes de modification/suppression.
     * GET /caisses/demandes-modification
     */
    public function demandesModificationPage()
    {
        return view('Caisse_Guichet.demandes_modification');
    }

    /**
     * JSON des demandes (filtrable par statut).
     * GET /caisses/demandes-modification/data
     */
    public function demandesModificationJson(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Caisse\DemandeModification::with(['agentDemandeur', 'guichet', 'superviseur'])
            ->orderByRaw("FIELD(statut, 'EN_ATTENTE', 'APPROUVEE', 'REJETEE')")
            ->orderByDesc('created_at');

        if ($request->filled('statut') && $request->statut !== 'tous') {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type_demande') && $request->type_demande !== 'tous') {
            $query->where('type_demande', $request->type_demande);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('reference_operation', 'like', "%{$search}%")
                  ->orWhere('client_nom', 'like', "%{$search}%")
                  ->orWhere('compte_code', 'like', "%{$search}%")
                  ->orWhere('agent_matricule', 'like', "%{$search}%")
                  ->orWhere('motif', 'like', "%{$search}%")
                  ->orWhereHas('guichet', function ($g) use ($search) {
                      $g->where('intitule', 'like', "%{$search}%")
                        ->orWhere('code_guichet', 'like', "%{$search}%");
                  });
            });
        }

        $limit = (int) $request->input('limit', 200);
        $limit = max(20, min($limit, 500));

        $demandes = $query->limit($limit)->get()->map(function ($d) {
            $agent = $d->agentDemandeur;
            $sup   = $d->superviseur;
            return [
                'id'                    => $d->id,
                'transaction_id'        => $d->transaction_id,
                'reference_operation'   => $d->reference_operation,
                'guichet'               => $d->guichet?->intitule ?? '—',
                'compte_code'           => $d->compte_code,
                'client_nom'            => $d->client_nom,
                'type_operation'        => $d->type_operation,
                'devise_code'           => $d->devise_code,
                'ancien_montant'        => number_format((float)$d->ancien_montant, 2, ',', ' ') . ' ' . $d->devise_code,
                'nouveau_montant'       => $d->nouveau_montant
                    ? number_format((float)$d->nouveau_montant, 2, ',', ' ') . ' ' . $d->devise_code
                    : null,
                'type_demande'          => $d->type_demande,
                'motif'                 => $d->motif,
                'nouvelles_observations'=> $d->nouvelles_observations,
                'agent_matricule'       => $d->agent_matricule,
                'agent_nom'             => $agent ? trim(($agent->prenom ?? '') . ' ' . ($agent->nom ?? '')) : $d->agent_matricule,
                'statut'                => $d->statut,
                'superviseur_nom'       => $sup ? trim(($sup->prenom ?? '') . ' ' . ($sup->nom ?? '')) : null,
                'commentaire_superviseur' => $d->commentaire_superviseur,
                'demandee_le'           => $d->created_at?->format('d/m/Y H:i'),
                'traitee_le'            => $d->traitee_le?->format('d/m/Y H:i'),
            ];
        });

        return response()->json($demandes);
    }

    /**
     * Superviseur approuve une demande — exécute la modification ou suppression.
     * POST /caisses/demandes-modification/{id}/approuver
     */
    public function approuverModification(\Illuminate\Http\Request $request, $id, CommissionEngine $commissionEngine, OhadaAccountingService $accountingService)
    {
        $request->validate([
            'commentaire' => 'nullable|string|max:500',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $demande = \App\Models\Caisse\DemandeModification::where('statut', \App\Models\Caisse\DemandeModification::EN_ATTENTE)
            ->findOrFail($id);

        $op = Transaction::with(['guichet'])->findOrFail($demande->transaction_id);

        try {
            DB::transaction(function () use ($demande, $op, $user, $request, $commissionEngine, $accountingService) {
                if ($demande->type_demande === \App\Models\Caisse\DemandeModification::SUPPRESSION) {
                    // ── Exécuter la suppression (annulation) ──────────────
                    $montant = (float) $op->montant;
                    $devise  = $op->devise_code;
                    $commission = (float) ($op->montant_commission_total ?? 0);
                    $montantTotalClient = $op->montant_total_client !== null
                        ? abs((float) $op->montant_total_client)
                        : match ($op->type) {
                            Transaction::DEPOT => round(max(0, $montant - $commission), 2),
                            Transaction::RETRAIT => round($montant + $commission, 2),
                            default => 0.0,
                        };

                    $op->statut = Transaction::ANNULE;
                    $op->observations = ($op->observations ? $op->observations . ' | ' : '')
                        . 'ANNULÉ sur demande superviseur — Motif : ' . $demande->motif;
                    $op->save();

                    // Inverser les soldes
                    switch ($op->type) {
                        case Transaction::DEPOT:
                            CaissesGuichetSolde::where('guichet_id', $op->guichet_id)
                                ->where('devise_code', $devise)
                                ->decrement('solde_en_caisse', $montant);
                            if ($op->compte_code) {
                                Compte::where('code_compte', $op->compte_code)->decrement('solde_reel', $montantTotalClient);
                            }
                            break;
                        case Transaction::RETRAIT:
                            CaissesGuichetSolde::where('guichet_id', $op->guichet_id)
                                ->where('devise_code', $devise)
                                ->increment('solde_en_caisse', $montant);
                            if ($op->compte_code) {
                                Compte::where('code_compte', $op->compte_code)->increment('solde_reel', $montantTotalClient);
                            }
                            break;
                        case Transaction::PAIEMENT:
                            CaissesGuichetSolde::where('guichet_id', $op->guichet_id)
                                ->where('devise_code', $devise)
                                ->decrement('solde_en_caisse', $montant);
                            break;
                        case Transaction::REMBOURSEMENT:
                            CaissesGuichetSolde::where('guichet_id', $op->guichet_id)
                                ->where('devise_code', $devise)
                                ->increment('solde_en_caisse', $montant);
                            break;
                        case Transaction::CHANGE:
                            CaissesGuichetSolde::where('guichet_id', $op->guichet_id)
                                ->where('devise_code', $devise)
                                ->decrement('solde_en_caisse', $montant);
                            if ($op->devise_dest && $op->montant_dest) {
                                CaissesGuichetSolde::where('guichet_id', $op->guichet_id)
                                    ->where('devise_code', $op->devise_dest)
                                    ->increment('solde_en_caisse', (float) $op->montant_dest);
                            }
                            break;
                    }

                    $accountingService->postReversal($op, 'Annulation sur demande superviseur', [
                        'agent_matricule' => $user->agent_matricule,
                    ]);
                } else {
                    // ── Exécuter la modification ───────────────────────────
                    $ancienMontant  = (float) $op->montant;
                    $ancienneCommission = (float) ($op->montant_commission_total ?? 0);
                    $ancienMontantDest = (float) ($op->montant_dest ?? 0);
                    $ancienneDeviseDest = $op->devise_dest;

                    $ancienNetCompte = $op->montant_net_client !== null
                        ? (float) $op->montant_net_client
                        : (float) $this->computeCompteImpact($op->type, $ancienMontant, $ancienneCommission)['delta'];

                    $nouveauMontant = (float) $demande->nouveau_montant;
                    $diff           = $nouveauMontant - $ancienMontant;
                    $devise         = $op->devise_code;

                    $accountingService->postReversal($op, 'Regularisation avant modification', [
                        'montant' => $ancienMontant,
                        'commission' => $ancienneCommission,
                        'montant_dest' => $ancienMontantDest,
                        'devise_dest' => $ancienneDeviseDest,
                        'agent_matricule' => $user->agent_matricule,
                    ]);

                    $op->montant      = $nouveauMontant;
                    $op->observations = ($demande->nouvelles_observations ?? $op->observations);
                    $op->observations .= ' | Modifié par superviseur — Motif : ' . $demande->motif;
                    $op->save();

                    // Ajuster les soldes de la différence
                    if ($diff != 0) {
                        switch ($op->type) {
                            case Transaction::DEPOT:
                                CaissesGuichetSolde::where('guichet_id', $op->guichet_id)
                                    ->where('devise_code', $devise)
                                    ->increment('solde_en_caisse', $diff);
                                break;
                            case Transaction::RETRAIT:
                                CaissesGuichetSolde::where('guichet_id', $op->guichet_id)
                                    ->where('devise_code', $devise)
                                    ->decrement('solde_en_caisse', $diff);
                                break;
                            case Transaction::PAIEMENT:
                                CaissesGuichetSolde::where('guichet_id', $op->guichet_id)
                                    ->where('devise_code', $devise)
                                    ->increment('solde_en_caisse', $diff);
                                break;
                            case Transaction::REMBOURSEMENT:
                                CaissesGuichetSolde::where('guichet_id', $op->guichet_id)
                                    ->where('devise_code', $devise)
                                    ->decrement('solde_en_caisse', $diff);
                                break;
                        }
                    }

                    $compteOperation = $op->compte_code
                        ? Compte::with('client')->where('code_compte', $op->compte_code)->first()
                        : null;

                    $commissionContext = $this->buildCommissionContext(
                        $op->type,
                        $compteOperation,
                        $op->guichet,
                        $op->devise_code,
                        $nouveauMontant,
                        $user->agent_matricule
                    );

                    $commissionPreview = $this->previewCommissionAmount($commissionEngine, $commissionContext);
                    $impactPreview = $this->computeCompteImpact($op->type, $nouveauMontant, $commissionPreview);
                    $nouveauNetCompte = (float) $impactPreview['delta'];

                    if ($op->compte_code && in_array($op->type, [Transaction::DEPOT, Transaction::RETRAIT], true)) {
                        $deltaCompte = round($nouveauNetCompte - $ancienNetCompte, 2);
                        if ($deltaCompte > 0) {
                            Compte::where('code_compte', $op->compte_code)->increment('solde_reel', $deltaCompte);
                        } elseif ($deltaCompte < 0) {
                            Compte::where('code_compte', $op->compte_code)->decrement('solde_reel', abs($deltaCompte));
                        }
                    }

                    $commissionSnapshot = $commissionEngine->applyToTransaction($op, $commissionContext);
                    $commissionFinale = (float) ($commissionSnapshot?->montant_commission ?? 0);
                    $impactFinal = $this->computeCompteImpact($op->type, $nouveauMontant, $commissionFinale);
                    $netFinalCompte = (float) $impactFinal['delta'];

                    if ($op->compte_code && in_array($op->type, [Transaction::DEPOT, Transaction::RETRAIT], true)) {
                        $ecartFinal = round($netFinalCompte - $nouveauNetCompte, 2);
                        if ($ecartFinal > 0) {
                            Compte::where('code_compte', $op->compte_code)->increment('solde_reel', $ecartFinal);
                        } elseif ($ecartFinal < 0) {
                            Compte::where('code_compte', $op->compte_code)->decrement('solde_reel', abs($ecartFinal));
                        }
                    }

                    $soldeAvantSnapshot = $op->solde_compte_avant !== null ? (float) $op->solde_compte_avant : null;
                    $soldeApresSnapshot = $soldeAvantSnapshot !== null
                        ? round($soldeAvantSnapshot + $netFinalCompte, 2)
                        : null;

                    $op->forceFill([
                        'montant_commission_total' => $commissionFinale,
                        'solde_compte_apres' => $soldeApresSnapshot,
                        'montant_total_client' => in_array($op->type, [Transaction::DEPOT, Transaction::RETRAIT], true)
                            ? abs($netFinalCompte)
                            : null,
                        'montant_net_client' => in_array($op->type, [Transaction::DEPOT, Transaction::RETRAIT], true)
                            ? $netFinalCompte
                            : null,
                    ])->save();

                    $accountingService->postTransaction($op, [
                        'commission' => $commissionFinale,
                        'agent_matricule' => $user->agent_matricule,
                        'commission_trace' => [
                            'snapshot_id' => $commissionSnapshot?->id,
                            'rule_id' => $commissionSnapshot?->commission_rule_id,
                            'libelle' => $commissionSnapshot?->libelle,
                            'mode' => $commissionSnapshot?->mode_calcul,
                            'valeur' => (float) ($commissionSnapshot?->valeur_snapshot ?? 0),
                            'base' => (float) ($commissionSnapshot?->base_calcul ?? $op->montant ?? 0),
                            'montant' => $commissionFinale,
                            'has_rule' => (bool) ($commissionSnapshot?->commission_rule_id),
                        ],
                    ]);
                }

                // Marquer la demande comme approuvée
                $demande->update([
                    'statut'                  => \App\Models\Caisse\DemandeModification::APPROUVEE,
                    'superviseur_matricule'   => $user->agent_matricule,
                    'commentaire_superviseur' => $request->commentaire,
                    'traitee_le'              => now(),
                ]);
            });

            app(NotificationService::class)->notifyAgentMatricules(
                [$demande->agent_matricule],
                'Demande approuvee',
                'Votre demande #' . $demande->id . ' concernant l\'operation ' . $demande->reference_operation . ' a ete approuvee.',
                [
                    'type' => 'info',
                    'icon' => 'fas fa-check',
                    'action_url' => route('caisses.journal.page'),
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        $action = $demande->type_demande === 'SUPPRESSION' ? 'annulée' : 'modifiée';
        return response()->json([
            'success' => true,
            'message' => "Demande #{$id} approuvée. Opération {$demande->reference_operation} {$action}.",
        ]);
    }

    /**
     * Superviseur rejette une demande (aucun changement sur la transaction).
     * POST /caisses/demandes-modification/{id}/rejeter
     */
    public function rejeterModification(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'commentaire' => 'required|string|min:5|max:500',
        ], [
            'commentaire.required' => 'Veuillez indiquer le motif du rejet.',
            'commentaire.min'      => 'Le motif doit comporter au moins 5 caractères.',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $demande = \App\Models\Caisse\DemandeModification::where('statut', \App\Models\Caisse\DemandeModification::EN_ATTENTE)
            ->findOrFail($id);

        $demande->update([
            'statut'                  => \App\Models\Caisse\DemandeModification::REJETEE,
            'superviseur_matricule'   => $user->agent_matricule,
            'commentaire_superviseur' => $request->commentaire,
            'traitee_le'              => now(),
        ]);

        app(NotificationService::class)->notifyAgentMatricules(
            [$demande->agent_matricule],
            'Demande rejetee',
            'Votre demande #' . $demande->id . ' concernant l\'operation ' . $demande->reference_operation . ' a ete rejetee. Motif: ' . $request->commentaire,
            [
                'type' => 'warning',
                'icon' => 'fas fa-times',
                'action_url' => route('caisses.journal.page'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Demande #{$id} rejetée.",
        ]);
    }

    /**
     * Génère et retourne le bordereau PDF d'une opération.
     * GET /caisses/operations/{id}/bordereau
     */
    public function bordereau($id)
    {
        $op = Transaction::with(['guichet', 'compte.client.zone', 'devise', 'commissions'])->findOrFail($id);

        $guichet = $op->guichet;
        $compte  = $op->compte;
        $client  = $compte?->client;
        $zoneNom = $client?->zone?->nom;

        // Agent caissier
        $agentUser = \App\Models\User::with('agent')->where('agent_matricule', $op->agent_matricule)->first();
        $agentNom  = null;
        if ($agentUser?->agent) {
            $a = $agentUser->agent;
            $agentNom = trim(($a->prenom ?? '') . ' ' . ($a->nom ?? ''));
        } elseif ($agentUser) {
            $agentNom = $agentUser->name ?? $op->agent_matricule;
        } else {
            $agentNom = $op->agent_matricule;
        }

        // Utilisateur qui lance l'impression (traçabilité)
        /** @var \App\Models\User|null $printedByUser */
        $printedByUser   = Auth::user();
        $imprimeParNom   = 'Utilisateur inconnu';
        $imprimeParProfil = null;

        if ($printedByUser) {
            $printedByUser->loadMissing('agent');

            if ($printedByUser->agent) {
                $ap = $printedByUser->agent;
                $imprimeParNom = trim(($ap->prenom ?? '') . ' ' . ($ap->nom ?? ''));
            }

            if (empty($imprimeParNom)) {
                $imprimeParNom = $printedByUser->name ?? $printedByUser->agent_matricule ?? 'Utilisateur inconnu';
            }

            $roles = (array) $printedByUser->getRoleCodes();
            $imprimeParProfil = $roles[0] ?? null;
        }

        // Photo client en base64 pour DomPDF
        $photoBase64 = null;
        if ($client && $client->photo) {
            $photoPath = storage_path('app/public/' . ltrim($client->photo, '/'));
            if (file_exists($photoPath)) {
                $mime = mime_content_type($photoPath);
                $photoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($photoPath));
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('impressions.caisse.bordereau', compact(
            'op', 'guichet', 'compte', 'client', 'agentNom', 'photoBase64', 'imprimeParNom', 'imprimeParProfil', 'zoneNom'
        ));
        $pdf->setPaper([0, 0, 595.28, 420], 'landscape'); // A5 landscape (half A4)

        return $pdf->stream('bordereau-' . $op->reference . '.pdf');
    }
}
