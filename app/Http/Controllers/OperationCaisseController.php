<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Affectation;
use App\Models\Compte;
use App\Models\CaissesGuichet;
use App\Models\CaissesGuichetSolde;
use App\Models\MouvementInterCaisse;
use App\Models\Transaction;

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

        $operations = [];
        if ($guichet) {
            $operations = Transaction::where('guichet_id', $guichet->id)
                ->whereDate('date_operation', today())
                ->orderByDesc('date_operation')
                ->limit(30)
                ->get();
        }

        $comptes = \App\Models\Compte::with('client')
            ->orderBy('devise')
            ->orderBy('code_compte')
            ->get();

        return view('Caisse_Guichet.operations', compact('guichet', 'user', 'operations', 'comptes'));
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
    public function store(Request $request)
    {
        $request->validate([
            'type_operation' => 'required|in:DEPOT,RETRAIT,CHANGE,PAIEMENT,REMBOURSEMENT',
            'devise_code'    => 'required|exists:tb_devises,code_iso',
            'montant'        => 'required|numeric|min:0.01',
            'observations'   => 'nullable|string|max:500',
            // Compte client — obligatoire pour DEPOT et RETRAIT
            'compte_code'    => 'required_if:type_operation,DEPOT,RETRAIT|nullable|exists:tb_comptes,code_compte',
            // Champs obligatoires uniquement pour CHANGE
            'devise_dest'    => 'required_if:type_operation,CHANGE|nullable|exists:tb_devises,code_iso|different:devise_code',
            'montant_dest'   => 'required_if:type_operation,CHANGE|nullable|numeric|min:0.01',
            'taux_change'    => 'nullable|numeric|min:0',
        ], [
            'compte_code.required_if'  => 'Le compte client est obligatoire pour un dépôt ou un retrait.',
            'compte_code.exists'       => 'Le numéro de compte est introuvable.',
            'devise_dest.required_if'  => 'La devise de destination est obligatoire pour un change.',
            'montant_dest.required_if' => 'Le montant destination est obligatoire pour un change.',
            'devise_dest.different'    => 'Les deux devises doivent être différentes.',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = $this->getGuichetAgent();

        if (!$guichet) {
            return response()->json(['success' => false, 'message' => 'Aucun guichet affecté à votre compte.'], 422);
        }

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

        // Pour un RETRAIT : vérifier aussi le solde du compte client
        if ($type === Transaction::RETRAIT && $request->filled('compte_code')) {
            $compte = Compte::where('code_compte', $request->compte_code)->first();
            if (!$compte) {
                return response()->json(['success' => false, 'message' => 'Compte client introuvable.'], 422);
            }
            if ((float) $compte->solde_reel < $montant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solde compte insuffisant. Disponible sur le compte '
                               . $compte->code_compte . ' : '
                               . number_format((float)$compte->solde_reel, 2, ',', ' ')
                               . ' ' . $compte->devise . '.',
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

        try {
            DB::transaction(function () use ($request, $guichet, $user, $type, $montant, $devise, $reference) {

                // 1. Enregistrer l'opération
                Transaction::create([
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
                ]);

                // 2. Mettre à jour les soldes
                switch ($type) {
                    case Transaction::DEPOT:
                        // Le guichet reçoit les espèces → solde guichet augmente
                        CaissesGuichetSolde::where('guichet_id', $guichet->id)
                            ->where('devise_code', $devise)
                            ->increment('solde_en_caisse', $montant);
                        // Créditer le compte client
                        Compte::where('code_compte', $request->compte_code)
                            ->increment('solde_reel', $montant);
                        break;

                    case Transaction::RETRAIT:
                        // Le guichet donne les espèces → solde guichet diminue
                        CaissesGuichetSolde::where('guichet_id', $guichet->id)
                            ->where('devise_code', $devise)
                            ->decrement('solde_en_caisse', $montant);
                        // Débiter le compte client
                        Compte::where('code_compte', $request->compte_code)
                            ->decrement('solde_reel', $montant);
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

        // Retrouver l'id de la transaction fraîchement créée pour le bordereau
        $transaction = Transaction::where('reference', $reference)->latest('id')->first();
        $bordereauUrl = $transaction
            ? route('caisses.operations.bordereau', ['id' => $transaction->id])
            : null;

        return response()->json([
            'success'        => true,
            'reference'      => $reference,
            'soldes'         => $soldesMaj,
            'message'        => $msg,
            'bordereau_url'  => $bordereauUrl,
        ]);
    }

    /**
     * Annule une opération déjà confirmée.
     * Inverse le mouvement de solde correspondant.
     */
    public function annuler(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = $this->getGuichetAgent();

        if (!$guichet) {
            return response()->json(['success' => false, 'message' => 'Guichet introuvable.'], 422);
        }

        $op = Transaction::where('id', $id)
            ->where('guichet_id', $guichet->id)
            ->firstOrFail();

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
            DB::transaction(function () use ($op, $guichet) {
                $op->statut = Transaction::ANNULE;
                $op->save();

                $type    = $op->type;
                $montant = (float) $op->montant;
                $devise  = $op->devise_code;

                // Inverser le mouvement de solde (et de compte le cas échéant)
                switch ($type) {
                    case Transaction::DEPOT:
                        // Annulation dépôt : le guichet redonne les espèces, on débite le compte
                        CaissesGuichetSolde::where('guichet_id', $guichet->id)
                            ->where('devise_code', $devise)
                            ->decrement('solde_en_caisse', $montant);
                        if ($op->compte_code) {
                            Compte::where('code_compte', $op->compte_code)
                                ->decrement('solde_reel', $montant);
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
                                ->increment('solde_reel', $montant);
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
            });
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

        $comptes = Compte::with('client')
            ->where(function ($query) use ($q) {
                $query->where('code_compte', 'like', "%{$q}%")
                      ->orWhereHas('client', fn($cq) =>
                          $cq->where('nom',     'like', "%{$q}%")
                             ->orWhere('prenom', 'like', "%{$q}%")
                             ->orWhere('postnom','like', "%{$q}%")
                      );
            })
            ->limit(10)
            ->get()
            ->map(fn($c) => [
                'code_compte' => $c->code_compte,
                'client_nom'  => $c->client
                    ? trim(($c->client->nom ?? '') . ' ' . ($c->client->postnom ?? '') . ' ' . ($c->client->prenom ?? ''))
                    : '—',
                'devise'      => $c->devise,
                'solde'       => number_format((float) $c->solde_reel, 2, ',', ' ') . ' ' . $c->devise,
            ]);

        return response()->json($comptes);
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

        return view('Caisse_Guichet.journal', compact('guichet', 'user'));
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

        $query = Transaction::where('guichet_id', $guichet->id)
            ->whereDate('date_operation', $date)
            ->orderByDesc('date_operation');

        if ($type !== 'TOUS') {
            $query->where('type', $type);
        }

        $ops = $query->limit(300)->get()->map(fn($op) => [
            'id'              => $op->id,
            'reference'       => $op->reference,
            'compte_code'     => $op->compte_code,
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
        ]);

        // Totaux par type (opérations confirmées uniquement)
        $toutes = Transaction::where('guichet_id', $guichet->id)
            ->whereDate('date_operation', $date)
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
            ->firstOrFail();

        // Vérifier qu'il n'existe pas déjà une demande EN_ATTENTE pour cette opération
        $existante = \App\Models\DemandeModification::where('transaction_id', $id)
            ->where('statut', \App\Models\DemandeModification::EN_ATTENTE)
            ->first();

        if ($existante) {
            return response()->json([
                'success' => false,
                'message' => 'Une demande est déjà en attente pour cette opération (#' . $existante->id . ').',
            ], 422);
        }

        // Info client pour l'audit
        $clientNom = null;
        if ($op->compte_code) {
            $compte = \App\Models\Compte::with('client')->where('code_compte', $op->compte_code)->first();
            if ($compte && $compte->client) {
                $clientNom = trim(($compte->client->nom ?? '') . ' ' . ($compte->client->postnom ?? '') . ' ' . ($compte->client->prenom ?? ''));
            }
        }

        \App\Models\DemandeModification::create([
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
            'statut'                 => \App\Models\DemandeModification::EN_ATTENTE,
        ]);

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
            'count' => \App\Models\DemandeModification::where('statut', \App\Models\DemandeModification::EN_ATTENTE)->count(),
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
        $query = \App\Models\DemandeModification::with(['agentDemandeur', 'guichet', 'superviseur'])
            ->orderByRaw("FIELD(statut, 'EN_ATTENTE', 'APPROUVEE', 'REJETEE')")
            ->orderByDesc('created_at');

        if ($request->filled('statut') && $request->statut !== 'tous') {
            $query->where('statut', $request->statut);
        }

        $demandes = $query->limit(200)->get()->map(function ($d) {
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
    public function approuverModification(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'commentaire' => 'nullable|string|max:500',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $demande = \App\Models\DemandeModification::where('statut', \App\Models\DemandeModification::EN_ATTENTE)
            ->findOrFail($id);

        $op = Transaction::with(['guichet'])->findOrFail($demande->transaction_id);

        try {
            DB::transaction(function () use ($demande, $op, $user, $request) {
                if ($demande->type_demande === \App\Models\DemandeModification::SUPPRESSION) {
                    // ── Exécuter la suppression (annulation) ──────────────
                    $montant = (float) $op->montant;
                    $devise  = $op->devise_code;

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
                                Compte::where('code_compte', $op->compte_code)->decrement('solde_reel', $montant);
                            }
                            break;
                        case Transaction::RETRAIT:
                            CaissesGuichetSolde::where('guichet_id', $op->guichet_id)
                                ->where('devise_code', $devise)
                                ->increment('solde_en_caisse', $montant);
                            if ($op->compte_code) {
                                Compte::where('code_compte', $op->compte_code)->increment('solde_reel', $montant);
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
                } else {
                    // ── Exécuter la modification ───────────────────────────
                    $ancienMontant  = (float) $op->montant;
                    $nouveauMontant = (float) $demande->nouveau_montant;
                    $diff           = $nouveauMontant - $ancienMontant;
                    $devise         = $op->devise_code;

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
                                if ($op->compte_code) {
                                    Compte::where('code_compte', $op->compte_code)->increment('solde_reel', $diff);
                                }
                                break;
                            case Transaction::RETRAIT:
                                CaissesGuichetSolde::where('guichet_id', $op->guichet_id)
                                    ->where('devise_code', $devise)
                                    ->decrement('solde_en_caisse', $diff);
                                if ($op->compte_code) {
                                    Compte::where('code_compte', $op->compte_code)->decrement('solde_reel', $diff);
                                }
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
                }

                // Marquer la demande comme approuvée
                $demande->update([
                    'statut'                  => \App\Models\DemandeModification::APPROUVEE,
                    'superviseur_matricule'   => $user->agent_matricule,
                    'commentaire_superviseur' => $request->commentaire,
                    'traitee_le'              => now(),
                ]);
            });
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
        $demande = \App\Models\DemandeModification::where('statut', \App\Models\DemandeModification::EN_ATTENTE)
            ->findOrFail($id);

        $demande->update([
            'statut'                  => \App\Models\DemandeModification::REJETEE,
            'superviseur_matricule'   => $user->agent_matricule,
            'commentaire_superviseur' => $request->commentaire,
            'traitee_le'              => now(),
        ]);

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
        $op = Transaction::with(['guichet', 'compte.client', 'devise'])->findOrFail($id);

        $guichet = $op->guichet;
        $compte  = $op->compte;
        $client  = $compte?->client;

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
            'op', 'guichet', 'compte', 'client', 'agentNom', 'photoBase64'
        ));
        $pdf->setPaper([0, 0, 595.28, 420], 'landscape'); // A5 landscape (half A4)

        return $pdf->stream('bordereau-' . $op->reference . '.pdf');
    }
}
