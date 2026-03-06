<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Affectation;
use App\Models\CaissesGuichet;

class CaisseController extends Controller
{
    /**
     * Affiche la page Ouverture / Fermeture du guichet de l'agent connecté.
     * Chaque guichetier ne voit et ne gère QUE son propre guichet.
     * La gestion globale des guichets est dans Administration.
     */
    public function ouverture()
    {
        $user = auth()->user();

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
        $user    = auth()->user();
        $guichet = CaissesGuichet::findOrFail($id);

        // Vérifier que ce guichet appartient bien à l'agent connecté
        $estTitulaire = Affectation::where('agent_matricule', $user->agent_matricule)
            ->where('guichet_id', $guichet->id)
            ->where('Etat', 'ACTIF')
            ->exists();

        if (!$estTitulaire && !$user->hasPermission('EBEN-PER1')) {
            return response()->json(['message' => 'Vous n\'êtes pas titulaire de ce guichet.'], 403);
        }

        $nouveauStatut = strtoupper($request->input('statut'));
        if (!in_array($nouveauStatut, ['OUVERT', 'FERME', 'SUSPENDU'])) {
            return response()->json(['message' => 'Statut invalide.'], 422);
        }

        $guichet->statut_operationnel = $nouveauStatut;
        $guichet->save();

        return response()->json([
            'success' => true,
            'message' => 'Guichet ' . strtolower($nouveauStatut) . ' avec succès.',
            'statut'  => $nouveauStatut,
        ]);
    }
}
