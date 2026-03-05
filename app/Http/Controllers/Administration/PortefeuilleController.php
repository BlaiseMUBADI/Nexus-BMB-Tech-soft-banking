<?php
namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Portefeuille;

class PortefeuilleController extends Controller
{
    // Affiche la liste des portefeuilles d'agents
    public function index()
    {
        $agents = \App\Models\Agent::orderBy('nom')->get();
        $portefeuilles = Portefeuille::with('agent')->get();
        return view('administration.portefeuilles', compact('agents', 'portefeuilles'));
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
                'status' => 'success',
                'message' => 'Le portefeuille a été supprimé avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
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
