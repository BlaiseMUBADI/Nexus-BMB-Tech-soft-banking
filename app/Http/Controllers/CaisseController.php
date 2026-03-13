<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RH\Affectation;
use App\Models\Caisse\CaissesGuichet;
use App\Models\Caisse\ClotureCaisse;
use App\Models\Caisse\MouvementInterCaisse;

class CaisseController extends Controller
{
    /**
     * Affiche la page Ouverture / Fermeture du guichet de l'agent connecté.
     * Chaque guichetier ne voit et ne gère QUE son propre guichet.
     * La gestion globale des guichets est dans Administration.
     */
    public function ouverture()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Trouver l'affectation active de l'agent connecté qui a un guichet
        $affectation = Affectation::with(['guichet.soldes.devise'])
            ->where('agent_matricule', $user->agent_matricule)
            ->where('Etat', 'ACTIF')
            ->whereNotNull('guichet_id')
            ->latest('date_debut')
            ->first();

        $guichet = $affectation?->guichet;

        return view('Caisse_Guichet.ouverture', compact('guichet', 'user'));
    }

    /**
     * Ouvrir / Fermer / Suspendre le guichet de l'agent connecté.
     * Sécurité : vérifie que l'agent connecté est bien titulaire du guichet.
     */
    public function changerStatut(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = CaissesGuichet::find($id);

        if (!$guichet) {
            Log::warning('[Caisse] Guichet introuvable', ['id' => $id, 'action' => 'changerStatut', 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Guichet introuvable.'], 404);
        }

        // Vérifier que ce guichet appartient bien à l'agent connecté
        $estTitulaire = Affectation::where('agent_matricule', $user->agent_matricule)
            ->where('guichet_id', $guichet->id)
            ->where('Etat', 'ACTIF')
            ->exists();

        if (!$estTitulaire && !$user->hasPermission('EBEN-PER1')) {
            return response()->json(['success' => false, 'message' => 'Vous n\'êtes pas titulaire de ce guichet.'], 403);
        }

        $nouveauStatut = strtoupper($request->input('statut'));
        if (!in_array($nouveauStatut, ['OUVERT', 'SUSPENDU'])) {
            return response()->json(['success' => false, 'message' => 'Ce statut ne peut pas être défini directement. Utilisez la procédure de clôture pour fermer le guichet.'], 422);
        }

        try {
            $guichet->statut_operationnel = $nouveauStatut;
            $guichet->save();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Guichet ' . strtolower($nouveauStatut) . ' avec succès.',
            'statut'  => $nouveauStatut,
        ]);
    }

    /**
     * Étape 1/2 de la fermeture sécurisée.
     * Retourne les soldes comptables du guichet pour pré-remplir le formulaire de billetage.
     * L'agent compare ces chiffres avec son comptage physique.
     */
    public function initierFermeture($id)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = CaissesGuichet::with('soldes.devise')->find($id);

        if (!$guichet) {
            Log::warning('[Caisse] Guichet introuvable', ['id' => $id, 'action' => 'initierFermeture', 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Guichet introuvable.'], 404);
        }

        $estTitulaire = Affectation::where('agent_matricule', $user->agent_matricule)
            ->where('guichet_id', $guichet->id)
            ->where('Etat', 'ACTIF')
            ->exists();

        if (!$estTitulaire && !$user->hasPermission('EBEN-PER1')) {
            return response()->json(['success' => false, 'message' => 'Vous n\'êtes pas titulaire de ce guichet.'], 403);
        }

        if ($guichet->statut_operationnel === 'FERME') {
            return response()->json(['success' => false, 'message' => 'Ce guichet est déjà fermé.'], 422);
        }

        if ($guichet->statut_operationnel === 'EN_VERIFICATION') {
            return response()->json(['success' => false, 'message' => 'Ce guichet est déjà en cours de vérification par le superviseur.'], 422);
        }

        $soldes = $guichet->soldes->sortBy('devise_code')->values()->map(fn($s) => [
            'devise_code'    => $s->devise_code,
            'devise_nom'     => $s->devise->nom     ?? $s->devise_code,
            'devise_symbole' => $s->devise->symbole ?? $s->devise_code,
            'solde_comptable'=> (float) $s->solde_en_caisse,
            'solde_fmt'      => number_format($s->solde_en_caisse, 2, ',', ' ') . ' ' . $s->devise_code,
        ]);

        return response()->json([
            'success'      => true,
            'code_guichet' => $guichet->code_guichet,
            'intitule'     => $guichet->intitule,
            'soldes'       => $soldes,
        ]);
    }

    /**
     * Étape 2/2 de la fermeture sécurisée — Arrêté de caisse.
     * 1. Valide le billetage soumis par l'agent.
     * 2. Calcule l'écart (physique − système) par devise.
     * 3. Enregistre la clôture dans tb_cloture_caisse.
     * 4. Passe le guichet en statut FERME.
     *
     * Règle métier : si un écart est détecté sur une devise,
     * le champ motif_ecart devient obligatoire.
     */
    public function confirmerFermeture(Request $request, $id)
    {
        $request->validate([
            'billetage'                  => 'required|array|min:1',
            'billetage.*.devise_code'    => 'required|string|max:10',
            'billetage.*.solde_physique' => 'required|numeric|min:0',
            'billetage.*.detail'         => 'nullable|array',
            'motif_ecart'                => 'nullable|string|max:500',
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $guichet = CaissesGuichet::with('soldes')->find($id);

        if (!$guichet) {
            Log::warning('[Caisse] Guichet introuvable', ['id' => $id, 'action' => 'confirmerFermeture', 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Guichet introuvable.'], 404);
        }

        $estTitulaire = Affectation::where('agent_matricule', $user->agent_matricule)
            ->where('guichet_id', $guichet->id)
            ->where('Etat', 'ACTIF')
            ->exists();

        if (!$estTitulaire && !$user->hasPermission('EBEN-PER1')) {
            return response()->json(['success' => false, 'message' => 'Vous n\'êtes pas titulaire de ce guichet.'], 403);
        }

        // Vérifier s'il y a un écart sur au moins une devise
        $aUnEcart = false;
        foreach ($request->billetage as $b) {
            $solde = $guichet->soldes->where('devise_code', $b['devise_code'])->first();
            if ($solde && abs((float)$b['solde_physique'] - (float)$solde->solde_en_caisse) > 0.009) {
                $aUnEcart = true;
                break;
            }
        }

        if ($aUnEcart && empty(trim($request->motif_ecart ?? ''))) {
            return response()->json([
                'success' => false,
                'message' => 'Un écart a été détecté. Le motif est obligatoire avant de pouvoir clôturer.',
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $guichet, $user) {
                foreach ($request->billetage as $b) {
                    $solde = $guichet->soldes->where('devise_code', $b['devise_code'])->first();
                    if (!$solde) {
                        continue; // devise inconnue, on ignore
                    }

                    $comptable = (float) $solde->solde_en_caisse;
                    $physique  = (float) $b['solde_physique'];
                    $ecart     = $physique - $comptable;

                    $statutEcart = match(true) {
                        $ecart >  0.009 => ClotureCaisse::EXCEDENT,
                        $ecart < -0.009 => ClotureCaisse::DEFICIT,
                        default         => ClotureCaisse::EQUILIBRE,
                    };

                    ClotureCaisse::create([
                        'guichet_id'       => $guichet->id,
                        'devise_code'      => $b['devise_code'],
                        'solde_comptable'  => $comptable,
                        'solde_physique'   => $physique,
                        'ecart_caisse'     => $ecart,
                        'detail_billetage' => $b['detail'] ?? [],
                        'motif_ecart'      => $request->motif_ecart ?: null,
                        'statut_ecart'     => $statutEcart,
                        'agent_cloturant'  => $user->agent_matricule,
                        'statut_validation'=> ClotureCaisse::VALIDATION_EN_ATTENTE,
                    ]);
                }

                // Passage en EN_VERIFICATION — le superviseur valide la réception physique
                $guichet->statut_operationnel = 'EN_VERIFICATION';
                $guichet->save();
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la clôture : ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Billetage soumis. Guichet ' . $guichet->code_guichet . ' en attente de validation du superviseur.',
            'statut'  => 'EN_VERIFICATION',
        ]);
    }

    /**
     * Retourne la liste des clôtures EN_ATTENTE de validation pour l'agent connecté.
     * Utilisé côté guichetier pour afficher l'état de sa demande de clôture.
     */
    public function maCloturePendante()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $affectation = Affectation::where('agent_matricule', $user->agent_matricule)
            ->where('Etat', 'ACTIF')
            ->whereNotNull('guichet_id')
            ->latest('date_debut')
            ->first();

        if (!$affectation) {
            return response()->json(['pending' => false, 'statut_guichet' => null]);
        }

        // Récupérer le guichet avec ses soldes courants
        $guichet = \App\Models\Caisse\CaissesGuichet::with('soldes.devise')
            ->find($affectation->guichet_id);

        if (!$guichet) {
            return response()->json(['pending' => false, 'statut_guichet' => null]);
        }

        $soldes = $guichet->soldes->map(fn($s) => [
            'devise_code'    => $s->devise_code,
            'symbole'        => $s->devise->symbole ?? $s->devise_code,
            'solde_en_caisse'=> $s->solde_en_caisse,
        ])->values();

        $cloture = ClotureCaisse::where('guichet_id', $guichet->id)
            ->where('statut_validation', ClotureCaisse::VALIDATION_EN_ATTENTE)
            ->latest('date_cloture')
            ->first();

        return response()->json([
            'pending'          => (bool) $cloture,
            'statut_guichet'   => $guichet->statut_operationnel,
            'soldes'           => $soldes,
            'date_soumission'  => $cloture?->date_cloture?->format('d/m/Y H:i'),
            'statut_validation'=> $cloture?->statut_validation,
        ]);
    }

    /**
     * Guichetier soumet une demande d'approvisionnement.
     * Crée un MouvementInterCaisse avec type_flux='DEMANDE_APPRO', statut='EN_ATTENTE'.
     * Le trésorier voit la demande dans État du Coffre et l'approuve ou la rejette.
     */
    public function demanderApprovisionnement(Request $request)
    {
        $request->validate([
            'devise_code' => 'required|exists:tb_devises,code_iso',
            'montant'     => 'required|numeric|min:1',
            'motif'       => 'nullable|string|max:255',
        ], [
            'montant.min' => 'Le montant demandé doit être supérieur à 0.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Retrouver le guichet de l'agent
        $affectation = Affectation::with('guichet')
            ->where('agent_matricule', $user->agent_matricule)
            ->where('Etat', 'ACTIF')
            ->whereNotNull('guichet_id')
            ->latest('date_debut')
            ->first();

        if (!$affectation || !$affectation->guichet) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun guichet affecté à votre compte.',
            ], 422);
        }

        $guichet   = $affectation->guichet;
        $reference = 'REQ-' . now()->format('Ymd-His') . '-' . strtoupper(substr($user->agent_matricule ?? 'XX', 0, 4));

        MouvementInterCaisse::create([
            'guichet_source_id'   => null,           // sera renseigné (coffre) à l'approbation
            'guichet_dest_id'     => $guichet->id,
            'agent_initiateur'    => $user->agent_matricule,
            'type_flux'           => 'DEMANDE_APPRO',
            'montant'             => $request->montant,
            'devise_code'         => $request->devise_code,
            'reference_bordereau' => $reference,
            'date_mouvement'      => now(),
            'statut'              => 'EN_ATTENTE',
            'observations'        => $request->motif,
        ]);

        return response()->json([
            'success'   => true,
            'reference' => $reference,
            'message'   => 'Demande envoyée au trésorier ('
                         . number_format($request->montant, 2, ',', ' ') . ' ' . $request->devise_code
                         . '). Référence : ' . $reference,
        ]);
    }
    /**
     * Retourne l'historique JSON des demandes du guichet de l'agent connecté.
     */
    public function mesDemandes()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $affectation = Affectation::where('agent_matricule', $user->agent_matricule)
            ->where('Etat', 'ACTIF')
            ->whereNotNull('guichet_id')
            ->latest('date_debut')
            ->first();

        if (!$affectation) {
            return response()->json([]);
        }

        $demandes = MouvementInterCaisse::where('type_flux', 'DEMANDE_APPRO')
            ->where('agent_initiateur', $user->agent_matricule)
            ->orderByRaw("FIELD(statut, 'EN_ATTENTE', 'CONFIRME', 'ANNULE')")
            ->orderByDesc('date_mouvement')
            ->limit(50)
            ->get()
            ->map(function ($m) {
                return [
                    'id'          => $m->id,
                    'reference'   => $m->reference_bordereau,
                    'devise_code' => $m->devise_code,
                    'montant_fmt' => number_format($m->montant, 2, ',', ' ') . ' ' . $m->devise_code,
                    'motif'       => $m->observations,
                    'statut'      => $m->statut,
                    'validateur'  => $m->validateur_matricule,
                    'date'        => $m->date_mouvement?->format('d/m/Y H:i'),
                ];
            });

        return response()->json($demandes);
    }}
