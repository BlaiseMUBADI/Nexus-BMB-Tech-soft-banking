<?php
namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tresorerie\Portefeuille;

class PortefeuilleController extends Controller
{
    // Redirige vers la page unifiée Zones + Portefeuilles (onglet Portefeuilles)
    public function index()
    {
        return redirect()->route('administration.zones.index')->with('_tab', 'portefeuilles');
    }

    // Supprimer un portefeuille d'agent/*
   /* public function destroy($id)
    {
        $portefeuille = Portefeuille::findOrFail($id);
        $portefeuille->delete();
        return response()->json(['success' => true, 'message' => 'Portefeuille supprimé avec succès !']);
    }*/
    public function destroy($id)
    {
        try {
            $portefeuille = Portefeuille::findOrFail($id);
            $portefeuille->delete();

            return response()->json([
                'success' => true,
                'message' => 'Le portefeuille a été supprimé avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer ce portefeuille car il est lié à d\'autres données.'
            ], 422);
        }
    }


    // Enregistre un portefeuille d'agent
    public function store(Request $request)
    {
        $request->validate([
            'nom_portefeuille' => 'required|string|max:100',
            'agent_matricule' => 'required|string|max:50|exists:tb_agents,matricule',
            'taux_commission_agent' => 'required|numeric|min:0',
        ]);

        Portefeuille::create([
            'nom_portefeuille' => $request->input('nom_portefeuille'),
            'agent_matricule' => $request->input('agent_matricule'),
            'taux_commission_agent' => $request->input('taux_commission_agent'),
        ]);

        return response()->json(['success' => true, 'message' => 'Portefeuille ajouté avec succès !']);
    }
}
