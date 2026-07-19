<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Caisse\CaissesGuichet;
use App\Models\Caisse\CaissesGuichetSolde;
use App\Models\Caisse\Recette;
use App\Models\Caisse\Transaction;
use App\Models\Comptabilite\CategorieRecette;
use App\Models\RH\Affectation;
use App\Services\Comptabilite\OhadaAccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecetteController extends Controller
{
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

    public function store(Request $request, OhadaAccountingService $accountingService)
    {
        $validated = $request->validate([
            'categorie_id'  => 'required|exists:tb_categories_recettes,id',
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

        // Sécurité : les Recettes sont réservées aux guichets FIXE/CENTRAL, jamais aux MOBILE
        if ($guichet->type_guichet === 'MOBILE') {
            return response()->json(['success' => false, 'message' => "Les recettes de caisse sont réservées aux guichets de bureau (FIXE). Un guichet MOBILE ne peut pas en saisir."], 403);
        }

        if ($guichet->statut_operationnel !== 'OUVERT') {
            return response()->json(['success' => false, 'message' => 'Guichet non ouvert. Impossible de saisir une recette.'], 422);
        }

        $categorie = CategorieRecette::with('compteProduit')->findOrFail($validated['categorie_id']);
        if (!$categorie->est_actif) {
            return response()->json(['success' => false, 'message' => 'Cette catégorie de recette est désactivée.'], 422);
        }

        $reference = 'REC-' . now()->format('Ymd-His') . '-' . strtoupper(substr($user->agent_matricule ?? 'XXXX', 0, 4));
        $cheminJustificatif = null;

        if ($request->hasFile('piece_justificative')) {
            $cheminJustificatif = $request->file('piece_justificative')->store('recettes_justificatifs', 'public');
        }

        try {
            $transaction = DB::transaction(function () use ($validated, $guichet, $user, $reference, $categorie, $cheminJustificatif, $accountingService) {
                $transaction = Transaction::create([
                    'reference'       => $reference,
                    'guichet_id'      => $guichet->id,
                    'agent_matricule' => $user->agent_matricule,
                    'compte_code'     => null,
                    'type'            => Transaction::RECETTE,
                    'devise_code'     => $validated['devise_code'],
                    'montant'         => $validated['montant'],
                    'observations'    => $validated['motif'],
                    'statut'          => Transaction::CONFIRME,
                    'date_operation'  => now(),
                ]);

                // La recette est un encaissement : le guichet reçoit des espèces → solde augmente
                CaissesGuichetSolde::where('guichet_id', $guichet->id)
                    ->where('devise_code', $validated['devise_code'])
                    ->increment('solde_en_caisse', $validated['montant']);

                $recette = Recette::create([
                    'transaction_id'      => $transaction->id,
                    'categorie_id'        => $categorie->id,
                    'motif'               => $validated['motif'],
                    'piece_justificative' => $cheminJustificatif,
                    'agent_matricule'     => $user->agent_matricule,
                ]);

                $accountingService->postTransaction($transaction, [
                    'compte_produit' => $categorie->numero_compte_produit,
                ]);

                $transaction->recette_id = $recette->id;
                return $transaction;
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        ActivityLog::record(
            'CAISSE',
            'RECETTE_CREEE',
            $transaction,
            $reference,
            "Recette « {$categorie->libelle} » : {$validated['montant']} {$validated['devise_code']} — {$validated['motif']}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Recette enregistrée avec succès. Référence : ' . $reference,
            'recu_url' => route('caisses.recettes.recu', $transaction->recette_id),
        ]);
    }

    public function recu($id)
    {
        $recette = Recette::with(['transaction.guichet', 'categorie.compteProduit', 'agent'])->findOrFail($id);
        $transaction = $recette->transaction;
        $guichet = $transaction->guichet;

        $agentNom = $recette->agent ? trim(($recette->agent->prenom ?? '') . ' ' . ($recette->agent->nom ?? '')) : $recette->agent_matricule;

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
            'categorie' => $recette->categorie,
            'sens' => 'RECETTE',
            'motif' => $recette->motif,
            'pieceJustificative' => $recette->piece_justificative,
            'guichet' => $guichet,
            'agentNom' => $agentNom,
            'imprimeParNom' => $imprimeParNom,
        ]);
        $pdf->setPaper([0, 0, 595.28, 420], 'landscape');

        return $pdf->stream('recu-recette-' . $transaction->reference . '.pdf');
    }

    public function annuler(Request $request, $id, OhadaAccountingService $accountingService)
    {
        $recette = Recette::with('transaction')->findOrFail($id);
        $transaction = $recette->transaction;

        if (!$transaction || $transaction->statut === Transaction::ANNULE) {
            return response()->json(['success' => false, 'message' => 'Cette recette est déjà annulée ou introuvable.'], 422);
        }

        try {
            DB::transaction(function () use ($transaction, $accountingService) {
                // Annulation : on retire du solde ce qui avait été ajouté
                CaissesGuichetSolde::where('guichet_id', $transaction->guichet_id)
                    ->where('devise_code', $transaction->devise_code)
                    ->decrement('solde_en_caisse', $transaction->montant);

                $transaction->update(['statut' => Transaction::ANNULE]);

                $accountingService->postReversal($transaction, 'Annulation recette de caisse', [
                    'agent_matricule' => Auth::user()?->agent_matricule,
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        ActivityLog::record(
            'CAISSE',
            'RECETTE_ANNULEE',
            $transaction,
            $transaction->reference,
            "Annulation de la recette {$transaction->reference}"
        );

        return response()->json(['success' => true, 'message' => 'Recette annulée avec succès.']);
    }
}
