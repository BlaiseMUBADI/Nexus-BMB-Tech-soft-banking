<?php
namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Zone;

class ZoneController extends Controller
{
    // Affiche la liste des zones
    public function index()
    {
        $zones = Zone::all();
        $agents = \App\Models\Agent::orderBy('nom')->get();
        return view('administration.zones', compact('zones', 'agents'));
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
}
