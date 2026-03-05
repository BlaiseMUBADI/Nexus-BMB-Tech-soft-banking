<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaissesGuichet;

class CaisseController extends Controller
{
    /**
     * Affiche la page Ouverture / Fermeture physique des guichets.
     * Architecture multi-devises :
     *   - soldes    → tb_caisses_guichets_soldes (une ligne par devise)
     *   - agent     → tb_affectations (affectationActive)
     */
    public function ouverture()
    {
        $guichets = CaissesGuichet::with([
            'soldes.devise',           // soldes multi-devises
            'affectationActive.agent', // agent titulaire actif
        ])->orderBy('code_guichet')->get();

        return view('Caisse_Guichet.ouverture', compact('guichets'));
    }

    /**
     * Ouvrir / Fermer / Suspendre un guichet (action physique)
     */
    public function changerStatut(Request $request, $id)
    {
        $guichet = CaissesGuichet::findOrFail($id);
        $nouveauStatut = strtoupper($request->input('statut'));

        if (!in_array($nouveauStatut, ['OUVERT', 'FERME', 'SUSPENDU'])) {
            return response()->json(['message' => 'Statut invalide.'], 422);
        }

        $guichet->statut_operationnel = $nouveauStatut;
        $guichet->save();

        return response()->json([
            'message' => 'Guichet ' . strtolower($nouveauStatut) . ' avec succès.',
            'statut'  => $nouveauStatut
        ]);
    }
}
