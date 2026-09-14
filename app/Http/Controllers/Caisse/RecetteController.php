<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Caisse\CaissesGuichet;
use App\Models\Caisse\CaissesGuichetSolde;
use App\Models\Caisse\Recette;
use App\Models\Caisse\Transaction;
use App\Models\Clients\Client;
use App\Models\Clients\ClientCarte;
use App\Models\Comptabilite\CategorieRecette;
use App\Models\RH\Affectation;
use App\Models\Tresorerie\CommissionRule;
use App\Services\Commissions\CommissionEngine;
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

    public function store(Request $request, OhadaAccountingService $accountingService, CommissionEngine $commissionEngine)
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

        // ── Catégorie CARTE_MEMBRE : client obligatoire, frais imposé par la
        // règle Trésorerie > Commissions (code_operation CARTE_MEMBRE), puis
        // création d'un ClientCarte PAYEE rendant la carte imprimable.
        $clientCarte = null;
        $montantFinal = (float) $validated['montant'];
        if ($categorie->isCarteMembre()) {
            $request->validate([
                'client_matricule' => 'required|exists:tb_clients,matricule',
            ], [
                'client_matricule.required' => 'Le client est obligatoire pour un frais de carte membre.',
                'client_matricule.exists'   => 'Client introuvable.',
            ]);

            $clientCarte = Client::where('matricule', $request->client_matricule)->first();
            if (!$clientCarte) {
                return response()->json(['success' => false, 'message' => 'Client introuvable.'], 422);
            }

            $dejaPayee = ClientCarte::where('client_matricule', $clientCarte->matricule)
                ->where('statut', '!=', ClientCarte::REVOQUEE)
                ->exists();
            if ($dejaPayee) {
                return response()->json(['success' => false, 'message' => 'Ce client a déjà une carte membre payée ou imprimée.'], 422);
            }

            $ruleCarteMembre = $commissionEngine->resolveRule([
                'code_operation' => 'CARTE_MEMBRE',
                'type_compte'    => CommissionRule::ALL,
                'type_guichet'   => strtoupper((string) $guichet->type_guichet),
                'devise_code'    => $validated['devise_code'],
            ]);
            if (!$ruleCarteMembre) {
                return response()->json(['success' => false, 'message' => "Aucun frais de carte membre n'est configuré pour {$validated['devise_code']} (Trésorerie > Commissions)."], 422);
            }

            // Le montant est TOUJOURS le frais configuré — jamais la saisie libre.
            $montantFinal = $commissionEngine->calculateCommission($ruleCarteMembre, 0);
            if ($montantFinal <= 0) {
                return response()->json(['success' => false, 'message' => 'Le frais de carte membre configuré est invalide (montant nul).'], 422);
            }
        }

        $reference = 'REC-' . now()->format('Ymd-His') . '-' . strtoupper(substr($user->agent_matricule ?? 'XXXX', 0, 4));
        $cheminJustificatif = null;

        if ($request->hasFile('piece_justificative')) {
            $cheminJustificatif = $request->file('piece_justificative')->store('recettes_justificatifs', 'public');
        }

        try {
            $transaction = DB::transaction(function () use ($validated, $guichet, $user, $reference, $categorie, $cheminJustificatif, $accountingService, $clientCarte, $montantFinal) {
                $transaction = Transaction::create([
                    'reference'       => $reference,
                    'guichet_id'      => $guichet->id,
                    'agent_matricule' => $user->agent_matricule,
                    'compte_code'     => null,
                    'client_matricule' => $clientCarte?->matricule,
                    'type'            => Transaction::RECETTE,
                    'devise_code'     => $validated['devise_code'],
                    'montant'         => $montantFinal,
                    'observations'    => $validated['motif'],
                    'statut'          => Transaction::CONFIRME,
                    'date_operation'  => now(),
                ]);

                // La recette est un encaissement : le guichet reçoit des espèces → solde augmente
                CaissesGuichetSolde::where('guichet_id', $guichet->id)
                    ->where('devise_code', $validated['devise_code'])
                    ->increment('solde_en_caisse', $montantFinal);

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

                // Frais carte membre : créer l'enregistrement de carte PAYEE
                // (imprimable depuis Comptes clients > Liste > clic droit).
                if ($clientCarte) {
                    ClientCarte::create([
                        'client_matricule' => $clientCarte->matricule,
                        'transaction_id'   => $transaction->id,
                        'montant_paye'     => $montantFinal,
                        'devise_code'      => $validated['devise_code'],
                        'token_verification' => ClientCarte::genererToken(),
                        'statut'           => ClientCarte::PAYEE,
                        'agent_encaissement_matricule' => $user->agent_matricule,
                        'guichet_id'       => $guichet->id,
                        'observations'     => $validated['motif'],
                    ]);
                }

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
            $clientCarte
                ? "Recette « {$categorie->libelle} » pour {$clientCarte->full_name} ({$clientCarte->matricule}) : {$montantFinal} {$validated['devise_code']}"
                : "Recette « {$categorie->libelle} » : {$montantFinal} {$validated['devise_code']} — {$validated['motif']}"
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

        // Sécurité : un caissier ne peut annuler QUE les recettes de son propre
        // guichet (même règle que DepenseController::annuler() /
        // OperationCaisseController::annuler()). Sans ce contrôle, n'importe
        // quel détenteur de EBEN-PER114 pouvait annuler la recette d'un
        // guichet auquel il n'est pas affecté.
        $guichet = $this->getGuichetAgent();
        if (!$guichet || (int) $transaction->guichet_id !== (int) $guichet->id) {
            if (!Auth::user()?->hasPermission('EBEN-PER1')) {
                return response()->json(['success' => false, 'message' => "Vous n'êtes pas autorisé à annuler une recette d'un autre guichet."], 403);
            }
        }

        try {
            DB::transaction(function () use ($transaction, $accountingService) {
                // Annulation : on retire du solde ce qui avait été ajouté
                CaissesGuichetSolde::where('guichet_id', $transaction->guichet_id)
                    ->where('devise_code', $transaction->devise_code)
                    ->decrement('solde_en_caisse', $transaction->montant);

                $transaction->update(['statut' => Transaction::ANNULE]);

                // Frais carte membre : révoquer la carte créée par cette recette
                // (sinon le client conserverait une carte imprimable malgré l'annulation).
                $carteLiee = ClientCarte::where('transaction_id', $transaction->id)
                    ->where('statut', '!=', ClientCarte::REVOQUEE)
                    ->first();
                if ($carteLiee) {
                    $carteLiee->update([
                        'statut'       => ClientCarte::REVOQUEE,
                        'observations' => trim(($carteLiee->observations ?? '') . ' | Révoquée : annulation de la recette ' . $transaction->reference),
                    ]);
                }

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

    /**
     * Frais de carte membre configuré (Trésorerie > Commissions, règle
     * CARTE_MEMBRE) pour la devise demandée. Utilisé par le formulaire des
     * Opérations administratives pour pré-remplir/verrouiller le montant
     * quand la catégorie « Frais de carte membre » est sélectionnée.
     *
     * GET /caisses/recettes/frais-carte-membre?devise_code=USD
     */
    public function fraisCarteMembre(Request $request, CommissionEngine $commissionEngine)
    {
        $guichet = $this->getGuichetAgent();
        if (!$guichet) {
            return response()->json(['success' => false, 'message' => 'Aucun guichet affecté à votre compte.'], 422);
        }

        $devise = strtoupper(trim((string) $request->input('devise_code', '')));
        if ($devise === '') {
            return response()->json(['success' => false, 'message' => 'Devise requise.'], 422);
        }

        $rule = $commissionEngine->resolveRule([
            'code_operation' => 'CARTE_MEMBRE',
            'type_compte'    => CommissionRule::ALL,
            'type_guichet'   => strtoupper((string) $guichet->type_guichet),
            'devise_code'    => $devise,
        ]);

        if (!$rule) {
            return response()->json([
                'success' => false,
                'message' => "Aucun frais de carte membre configuré pour {$devise} (Trésorerie > Commissions).",
            ], 422);
        }

        $montant = $commissionEngine->calculateCommission($rule, 0);
        if ($montant <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Le frais de carte membre configuré est invalide (montant nul).',
            ], 422);
        }

        return response()->json([
            'success'     => true,
            'montant'     => $montant,
            'devise_code' => $devise,
        ]);
    }
}
