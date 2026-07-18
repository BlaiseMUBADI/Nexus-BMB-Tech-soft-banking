<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Caisse\CaissesGuichet;
use App\Models\Caisse\CaissesGuichetSolde;
use App\Models\Caisse\Depense;
use App\Models\Caisse\Transaction;
use App\Models\Comptabilite\CategorieDepense;
use App\Models\RH\Affectation;
use App\Services\Comptabilite\OhadaAccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepenseController extends Controller
{
    /**
     * Retrouve le guichet actif de l'agent connecté (même logique que le module Opérations).
     */
    private function getGuichetAgent(): ?CaissesGuichet
    {
        $matricule = Auth::user()?->agent_matricule;
        if (!$matricule) {
            return null;
        }

        $affectation = Affectation::where('agent_matricule', $matricule)
            ->where('Etat', 'ACTIF')
            ->whereNotNull('guichet_id')
            ->orderByDesc('date_debut')
            ->with('guichet')
            ->first();

        return $affectation?->guichet;
    }

    public function index(Request $request)
    {
        $guichet = $this->getGuichetAgent();

        $query = Depense::with(['transaction', 'categorie', 'agent'])
            ->orderByDesc('created_at');

        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $depenses = $query->paginate(20)->withQueryString();
        $categories = CategorieDepense::where('est_actif', true)->orderBy('libelle')->get();

        return view('Caisse_Guichet.depenses', compact('depenses', 'categories', 'guichet'));
    }

    public function store(Request $request, OhadaAccountingService $accountingService)
    {
        $validated = $request->validate([
            'categorie_id'  => 'required|exists:tb_categories_depenses,id',
            'devise_code'   => 'required|exists:tb_devises,code_iso',
            'montant'       => 'required|numeric|min:0.01',
            'motif'         => 'required|string|max:500',
            'piece_justificative' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $user = Auth::user();
        $guichet = $this->getGuichetAgent();

        if (!$guichet) {
            return response()->json(['success' => false, 'message' => 'Aucun guichet affecté à votre compte.'], 422);
        }

        if ($guichet->statut_operationnel !== 'OUVERT') {
            return response()->json(['success' => false, 'message' => 'Guichet non ouvert. Impossible de saisir une dépense.'], 422);
        }

        $categorie = CategorieDepense::with('compteCharge')->findOrFail($validated['categorie_id']);
        if (!$categorie->est_actif) {
            return response()->json(['success' => false, 'message' => 'Cette catégorie de dépense est désactivée.'], 422);
        }

        $solde = CaissesGuichetSolde::where('guichet_id', $guichet->id)
            ->where('devise_code', $validated['devise_code'])
            ->first();

        if (!$solde || (float) $solde->solde_en_caisse < (float) $validated['montant']) {
            $disponible = $solde ? number_format((float) $solde->solde_en_caisse, 2, ',', ' ') : '0,00';
            return response()->json([
                'success' => false,
                'message' => "Solde de caisse insuffisant en {$validated['devise_code']}. Disponible : {$disponible} {$validated['devise_code']}.",
            ], 422);
        }

        $reference = 'DEP-' . now()->format('Ymd-His') . '-' . strtoupper(substr($user->agent_matricule ?? 'XXXX', 0, 4));
        $cheminJustificatif = null;

        if ($request->hasFile('piece_justificative')) {
            $cheminJustificatif = $request->file('piece_justificative')->store('depenses_justificatifs', 'public');
        }

        try {
            $transaction = DB::transaction(function () use ($validated, $guichet, $user, $reference, $categorie, $cheminJustificatif, $accountingService) {
                $transaction = Transaction::create([
                    'reference'       => $reference,
                    'guichet_id'      => $guichet->id,
                    'agent_matricule' => $user->agent_matricule,
                    'compte_code'     => null,
                    'type'            => Transaction::DEPENSE,
                    'devise_code'     => $validated['devise_code'],
                    'montant'         => $validated['montant'],
                    'observations'    => $validated['motif'],
                    'statut'          => Transaction::CONFIRME,
                    'date_operation'  => now(),
                ]);

                CaissesGuichetSolde::where('guichet_id', $guichet->id)
                    ->where('devise_code', $validated['devise_code'])
                    ->decrement('solde_en_caisse', $validated['montant']);

                $depense = Depense::create([
                    'transaction_id'       => $transaction->id,
                    'categorie_id'         => $categorie->id,
                    'motif'                => $validated['motif'],
                    'piece_justificative'  => $cheminJustificatif,
                    'agent_matricule'      => $user->agent_matricule,
                ]);

                $accountingService->postTransaction($transaction, [
                    'compte_charge' => $categorie->numero_compte_charge,
                ]);

                $transaction->depense_id = $depense->id;
                return $transaction;
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        ActivityLog::record(
            'CAISSE',
            'DEPENSE_CREEE',
            $transaction,
            $reference,
            "Dépense « {$categorie->libelle} » : {$validated['montant']} {$validated['devise_code']} — {$validated['motif']}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Dépense enregistrée avec succès. Référence : ' . $reference,
            'recu_url' => route('caisses.depenses.recu', $transaction->depense_id),
        ]);
    }

    public function recu($id)
    {
        $depense = Depense::with(['transaction.guichet', 'categorie.compteCharge', 'agent'])->findOrFail($id);
        $transaction = $depense->transaction;
        $guichet = $transaction->guichet;

        $agentNom = $depense->agent ? trim(($depense->agent->prenom ?? '') . ' ' . ($depense->agent->nom ?? '')) : $depense->agent_matricule;

        $printedByUser = Auth::user();
        $imprimeParNom = 'Utilisateur inconnu';
        if ($printedByUser) {
            $printedByUser->loadMissing('agent');
            if ($printedByUser->agent) {
                $imprimeParNom = trim(($printedByUser->agent->prenom ?? '') . ' ' . ($printedByUser->agent->nom ?? ''));
            }
            if (empty($imprimeParNom)) {
                $imprimeParNom = $printedByUser->name ?? $printedByUser->agent_matricule ?? 'Utilisateur inconnu';
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('impressions.caisse.recu_operation_administrative', [
            'transaction' => $transaction,
            'categorie' => $depense->categorie,
            'sens' => 'DEPENSE',
            'motif' => $depense->motif,
            'pieceJustificative' => $depense->piece_justificative,
            'guichet' => $guichet,
            'agentNom' => $agentNom,
            'imprimeParNom' => $imprimeParNom,
        ]);
        $pdf->setPaper([0, 0, 595.28, 420], 'landscape');

        return $pdf->stream('recu-depense-' . $transaction->reference . '.pdf');
    }

    public function annuler(Request $request, $id, OhadaAccountingService $accountingService)
    {
        $depense = Depense::with('transaction')->findOrFail($id);
        $transaction = $depense->transaction;

        if (!$transaction || $transaction->statut === Transaction::ANNULE) {
            return response()->json(['success' => false, 'message' => 'Cette dépense est déjà annulée ou introuvable.'], 422);
        }

        try {
            DB::transaction(function () use ($transaction, $accountingService) {
                CaissesGuichetSolde::where('guichet_id', $transaction->guichet_id)
                    ->where('devise_code', $transaction->devise_code)
                    ->increment('solde_en_caisse', $transaction->montant);

                $transaction->update(['statut' => Transaction::ANNULE]);

                $accountingService->postReversal($transaction, 'Annulation dépense de caisse', [
                    'agent_matricule' => Auth::user()?->agent_matricule,
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        ActivityLog::record(
            'CAISSE',
            'DEPENSE_ANNULEE',
            $transaction,
            $transaction->reference,
            "Annulation de la dépense {$transaction->reference}"
        );

        return response()->json(['success' => true, 'message' => 'Dépense annulée avec succès.']);
    }
}
