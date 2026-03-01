<?php
namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Zone;
use Illuminate\Support\Facades\Log;


class ZoneController extends Controller
{
    // Affiche la liste des zones
    public function index()
    {
        $zones = Zone::with('agent')->get();
        $agents = \App\Models\Agent::orderBy('nom')->get();
        $portefeuilles = \App\Models\Portefeuille::all();
        return view('administration.zones', compact('zones', 'agents', 'portefeuilles'));
    }

    // Ajoute une nouvelle zone
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'agent_commercial_matricule' => 'required|string|max:50|exists:tb_agents,matricule',
            'commune' => 'required|string|max:100',
            'commune_autre' => 'nullable|string|max:100',
        ]);

        $commune = $request->input('commune');
        if ($commune === 'autre') {
            $commune = $request->input('commune_autre');
        }

        Zone::create([
            'nom' => $request->input('nom'),
            'agent_commercial_matricule' => $request->input('agent_commercial_matricule'),
            'commune' => $commune,
        ]);

        return redirect()->route('administration.zones.index')->with('success', 'Zone ajoutée avec succès.');
    }

    // Retourne les zones en JSON pour DataTable AJAX
    public function data()
    {
        $zones = Zone::all();
        return response()->json(['data' => $zones]);
    }

    // Supprime une zone
    public function destroy($code_zone)
    {
        $zone = Zone::find($code_zone);
        if (!$zone) {
            return response()->json(['message' => 'Zone introuvable.'], 404);
        }
        try {
            $zone->delete();
            return response()->json(['message' => 'Zone supprimée avec succès.']);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Erreur suppression zone : '.$e->getMessage());
            return response()->json([
                'message' => "Impossible de supprimer la zone car elle est liée à des clients. Veuillez d'abord supprimer ou réaffecter les clients de cette zone.",
            ], 409);
        }
    }
}
