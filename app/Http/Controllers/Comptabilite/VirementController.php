<?php

namespace App\Http\Controllers\Comptabilite;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Caisse\Transaction;
use App\Models\Clients\Compte;
use App\Models\Comptabilite\DemandeVirement;
use App\Models\Tresorerie\CommissionRule;
use App\Services\Comptabilite\OhadaAccountingService;
use App\Services\Commissions\CommissionEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VirementController extends Controller
{
    /**
     * Types de comptes exclus des virements (comptes bloqués/garantie).
     */
    private const TYPES_EXCLUS = ['GTC'];

    public function index(Request $request)
    {
        $query = DemandeVirement::with(['clientSource', 'clientDest', 'compteSource', 'compteDest', 'comptable', 'validateur'])
            ->orderByDesc('created_at');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $demandes = $query->paginate(20)->withQueryString();

        return view('comptabilite.virements.index', compact('demandes'));
    }

    public function create()
    {
        return view('comptabilite.virements.creer');
    }

    /**
     * Recherche AJAX d'un compte client (par code compte ou nom du titulaire).
     * Exclut les comptes de type GTC (garantie bloquée).
     */
    public function searchCompte(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $comptes = Compte::with('client')
            ->whereNotIn('type', self::TYPES_EXCLUS)
            ->where(function ($query) use ($q) {
                $query->where('code_compte', 'like', "%{$q}%")
                    ->orWhereHas('client', function ($cq) use ($q) {
                        $cq->searchFullName($q)
                            ->orWhere('nom', 'like', "%{$q}%")
                            ->orWhere('postnom', 'like', "%{$q}%")
                            ->orWhere('prenom', 'like', "%{$q}%")
                            ->orWhere('matricule', 'like', "%{$q}%");
                    });
            })
            ->limit(15)
            ->get()
            ->map(fn ($c) => [
                'code_compte'      => $c->code_compte,
                'client_matricule' => $c->client_matricule,
                'client_nom'       => $c->client?->full_name ?: '—',
                'type'             => $c->type,
                'devise'           => $c->devise,
                'solde_reel'       => number_format((float) $c->solde_reel, 2, ',', ' '),
                'solde_bloque'     => number_format((float) $c->solde_bloque, 2, ',', ' '),
                'solde_disponible' => (float) $c->solde_reel,
            ]);

        return response()->json($comptes);
    }

    public function store(Request $request, CommissionEngine $commissionEngine)
    {
        $validated = $request->validate([
            'compte_source_code' => 'required|exists:tb_comptes,code_compte',
            'compte_dest_code'   => 'required|exists:tb_comptes,code_compte|different:compte_source_code',
            'montant_source'     => 'required|numeric|min:0.01',
            'motif'              => 'required|string|max:500',
        ]);
        // NOTE : le taux de change n'est plus saisi librement. Il est déterminé
        // automatiquement à partir du taux ACTIF (tb_taux_echanges, géré par la
        // Trésorerie) — voir bloc de résolution ci-dessous.

        $compteSource = Compte::with('client')->findOrFail($validated['compte_source_code']);
        $compteDest = Compte::with('client')->findOrFail($validated['compte_dest_code']);

        if (in_array($compteSource->type, self::TYPES_EXCLUS, true) || in_array($compteDest->type, self::TYPES_EXCLUS, true)) {
            return response()->json(['success' => false, 'message' => 'Les comptes de garantie (GTC) sont exclus des virements.'], 422);
        }

        $montantSource = (float) $validated['montant_source'];

        // Commission (barème par tranches — tb_commission_rules, code_operation = VIREMENT)
        // Prélevée EN PLUS du montant sur le compte source, comme pour un Retrait.
        $commissionTotale = $this->resolveCommission($commissionEngine, $compteSource, $montantSource);

        if (($montantSource + $commissionTotale) > (float) $compteSource->solde_reel) {
            return response()->json([
                'success' => false,
                'message' => 'Montant + commission (' . number_format($montantSource + $commissionTotale, 2, ',', ' ') . ' ' . $compteSource->devise
                           . ') supérieur au solde disponible du compte source (' . number_format((float) $compteSource->solde_reel, 2, ',', ' ') . ' ' . $compteSource->devise . ').',
            ], 422);
        }

        $memeDevise = $compteSource->devise === $compteDest->devise;
        $tauxChange = null;
        $montantDest = $montantSource;

        if (!$memeDevise) {
            $tauxActif = \App\Models\Tresorerie\TauxEchange::actif($compteSource->devise, $compteDest->devise);
            if (!$tauxActif) {
                return response()->json([
                    'success' => false,
                    'message' => "Aucun taux de change actif n'est défini pour {$compteSource->devise} → {$compteDest->devise}. Contactez la Trésorerie pour faire activer un taux avant de proposer ce virement.",
                ], 422);
            }
            $tauxChange = (float) $tauxActif->taux;
            $montantDest = round($montantSource * $tauxChange, 2);
        }

        $demande = DemandeVirement::create([
            'client_source_matricule' => $compteSource->client_matricule,
            'compte_source_code'      => $compteSource->code_compte,
            'montant_source'          => $montantSource,
            'commission_totale'       => $commissionTotale,
            'devise_source'           => $compteSource->devise,
            'client_dest_matricule'   => $compteDest->client_matricule,
            'compte_dest_code'        => $compteDest->code_compte,
            'montant_dest'            => $montantDest,
            'devise_dest'             => $compteDest->devise,
            'taux_change'             => $tauxChange,
            'motif'                   => $validated['motif'],
            'statut'                  => DemandeVirement::EN_ATTENTE,
            'comptable_matricule'     => Auth::user()?->agent_matricule,
            'propose_le'              => now(),
        ]);

        ActivityLog::record(
            'COMPTABILITE',
            'VIREMENT_PROPOSE',
            $demande,
            'DVIR-' . $demande->id,
            "Demande de virement proposée : {$compteSource->code_compte} -> {$compteDest->code_compte} de {$montantSource} {$compteSource->devise} (commission : {$commissionTotale} {$compteSource->devise})"
        );

        return response()->json([
            'success' => true,
            'message' => 'Demande de virement créée' . ($commissionTotale > 0 ? ' — commission applicable : ' . number_format($commissionTotale, 2, ',', ' ') . ' ' . $compteSource->devise : '') . '. Elle doit maintenant être validée.',
        ]);
    }

    /**
     * Résout la commission applicable pour ce virement via le barème par
     * tranches (tb_commission_rules — montant_min/montant_max + mode FIXE),
     * exactement le même moteur que pour Dépôt/Retrait/Change au guichet.
     */
    private function resolveCommission(CommissionEngine $commissionEngine, Compte $compteSource, float $montantSource): float
    {
        $rule = $commissionEngine->resolveRule([
            'code_operation'  => Transaction::VIREMENT,
            'type_compte'     => $compteSource->type ?: CommissionRule::TYPE_NO_ACCOUNT,
            'type_guichet'    => CommissionRule::ALL,
            'devise_code'     => $compteSource->devise,
            'code_zone'       => $compteSource->client?->code_zone,
            'portefeuille_id' => $compteSource->portefeuille_id,
            'montant'         => $montantSource,
        ], now());

        return $rule ? $commissionEngine->calculateCommission($rule, $montantSource) : 0.0;
    }

    public function approuver(Request $request, $id, OhadaAccountingService $accountingService)
    {
        $demande = DemandeVirement::with(['compteSource', 'compteDest'])->findOrFail($id);

        if ($demande->statut !== DemandeVirement::EN_ATTENTE) {
            return response()->json(['success' => false, 'message' => 'Cette demande a déjà été traitée.'], 422);
        }

        $compteSource = $demande->compteSource;
        $compteDest = $demande->compteDest;

        if (!$compteSource || !$compteDest) {
            return response()->json(['success' => false, 'message' => 'Compte source ou destination introuvable.'], 422);
        }

        if (in_array($compteSource->type, self::TYPES_EXCLUS, true) || in_array($compteDest->type, self::TYPES_EXCLUS, true)) {
            return response()->json(['success' => false, 'message' => 'Les comptes de garantie (GTC) sont exclus des virements.'], 422);
        }

        // Vérification du solde au moment de la validation (pas à la création) — montant + commission
        $commissionTotale = (float) ($demande->commission_totale ?? 0);
        $totalDebite = (float) $demande->montant_source + $commissionTotale;
        if ($totalDebite > (float) $compteSource->solde_reel) {
            return response()->json([
                'success' => false,
                'message' => 'Solde insuffisant sur le compte source au moment de la validation (montant + commission requis : '
                           . number_format($totalDebite, 2, ',', ' ') . ' ' . $compteSource->devise . ', disponible : '
                           . number_format((float) $compteSource->solde_reel, 2, ',', ' ') . ' ' . $compteSource->devise . ').',
            ], 422);
        }

        try {
            $transaction = DB::transaction(function () use ($demande, $compteSource, $compteDest, $accountingService, $commissionTotale, $totalDebite) {
                $reference = 'VIR-' . now()->format('Ymd-His') . '-' . $demande->id;

                $soldeSourceAvant = (float) $compteSource->solde_reel;
                $soldeDestAvant = (float) $compteDest->solde_reel;

                // Le compte source paie le montant transféré + la commission.
                // Le compte destination reçoit le montant intégral (non affecté par la commission).
                $compteSource->decrement('solde_reel', $totalDebite);
                $compteDest->increment('solde_reel', (float) $demande->montant_dest);

                $transaction = Transaction::create([
                    'reference'       => $reference,
                    'agent_matricule' => Auth::user()?->agent_matricule,
                    'compte_code'     => $compteSource->code_compte,
                    'compte_dest_code' => $compteDest->code_compte,
                    'type'            => Transaction::VIREMENT,
                    'devise_code'     => $demande->devise_source,
                    'montant'         => $demande->montant_source,
                    'devise_dest'     => $demande->devise_dest,
                    'montant_dest'    => $demande->montant_dest,
                    'taux_change'     => $demande->taux_change,
                    'montant_commission_total' => $commissionTotale,
                    'solde_compte_avant' => $soldeSourceAvant,
                    'solde_compte_apres' => (float) $soldeSourceAvant - $totalDebite,
                    'observations'    => 'Virement vers ' . $compteDest->code_compte . ' — ' . $demande->motif
                                       . ($commissionTotale > 0 ? ' (commission : ' . number_format($commissionTotale, 2, ',', ' ') . ' ' . $demande->devise_source . ')' : ''),
                    'statut'          => Transaction::CONFIRME,
                    'date_operation'  => now(),
                ]);

                $accountingService->postTransaction($transaction, ['commission' => $commissionTotale]);

                $demande->update([
                    'statut'                 => DemandeVirement::APPROUVEE,
                    'validateur_matricule'    => Auth::user()?->agent_matricule,
                    'traite_le'               => now(),
                    'transaction_id'          => $transaction->id,
                ]);

                return $transaction;
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        ActivityLog::record(
            'COMPTABILITE',
            'VIREMENT_APPROUVE',
            $transaction,
            $transaction->reference,
            "Virement approuvé et exécuté : {$compteSource->code_compte} -> {$compteDest->code_compte} de {$demande->montant_source} {$demande->devise_source}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Virement approuvé et exécuté avec succès. Référence : ' . $transaction->reference,
        ]);
    }

    public function rejeter(Request $request, $id)
    {
        $validated = $request->validate([
            'commentaire_validateur' => 'required|string|max:500',
        ]);

        $demande = DemandeVirement::findOrFail($id);

        if ($demande->statut !== DemandeVirement::EN_ATTENTE) {
            return response()->json(['success' => false, 'message' => 'Cette demande a déjà été traitée.'], 422);
        }

        $demande->update([
            'statut'                 => DemandeVirement::REJETEE,
            'validateur_matricule'    => Auth::user()?->agent_matricule,
            'commentaire_validateur'  => $validated['commentaire_validateur'],
            'traite_le'               => now(),
        ]);

        ActivityLog::record(
            'COMPTABILITE',
            'VIREMENT_REJETE',
            $demande,
            'DVIR-' . $demande->id,
            "Demande de virement rejetée : {$validated['commentaire_validateur']}"
        );

        return response()->json(['success' => true, 'message' => 'Demande de virement rejetée.']);
    }

    public function recu($id)
    {
        $demande = DemandeVirement::with(['clientSource', 'clientDest', 'compteSource', 'compteDest', 'comptable', 'validateur', 'transaction'])
            ->where('statut', DemandeVirement::APPROUVEE)
            ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('impressions.comptabilite.recu_virement', [
            'demande' => $demande,
        ]);
        $pdf->setPaper([0, 0, 595.28, 420], 'landscape');

        return $pdf->stream('recu-virement-' . ($demande->transaction->reference ?? $demande->id) . '.pdf');
    }
}
