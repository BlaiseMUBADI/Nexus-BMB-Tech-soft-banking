<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Affectation;
use App\Models\Agent;
use App\Models\Poste;
use App\Models\CaissesGuichet;

class AffectationController extends Controller
{
    public function index(Request $request)
    {
        $postes       = Poste::all();
        $agents       = Agent::all();
        $guichets     = CaissesGuichet::orderBy('code_guichet')->get(); // pour le select guichet
        $affectations = Affectation::with(['agent', 'poste', 'guichet'])->get();
        $poste_id     = $request->get('poste_id');
        $agentsByPoste = $poste_id
            ? Affectation::where('poste_id', $poste_id)->with('agent')->get()
            : collect();

        return view('rh.affectation.index',
            compact('postes', 'agents', 'guichets', 'affectations', 'agentsByPoste', 'poste_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'agent_matricule' => 'required|exists:tb_agents,matricule',
            'poste_id'        => 'required|exists:tb_postes,id',
            'date_debut'      => 'required|date',
            'guichet_id'      => 'nullable|exists:tb_caisses_guichets,id',
        ]);

        $data = $request->only('agent_matricule', 'poste_id', 'guichet_id', 'date_debut', 'date_fin');
        $data['Etat'] = 'ACTIF';

        Affectation::create($data);

        // Réponse JSON (appel AJAX depuis la vue)
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'Affectation enregistrée avec succès.']);
        }
        return redirect()->route('affectations.index')->with('success', 'Affectation enregistrée !');
    }
}
