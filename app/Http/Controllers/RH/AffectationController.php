<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Affectation;
use App\Models\Agent;
use App\Models\Poste;

class AffectationController extends Controller
{
    public function index(Request $request)
    {
        $postes = Poste::all();
        $agents = Agent::all();
        $affectations = Affectation::with(['agent', 'poste'])->get();
        $poste_id = $request->get('poste_id');
        $agentsByPoste = $poste_id ? Affectation::where('poste_id', $poste_id)->with('agent')->get() : collect();
        return view('rh.affectation.index', compact('postes', 'agents', 'affectations', 'agentsByPoste', 'poste_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'agent_matricule' => 'required|exists:tb_agents,matricule',
            'poste_id' => 'required|exists:tb_postes,id',
            'date_debut' => 'required|date',
        ]);
        Affectation::create($request->only('agent_matricule', 'poste_id', 'date_debut', 'date_fin'));
        return redirect()->route('affectations.index')->with('success', 'Affectation enregistrée !');
    }
}
