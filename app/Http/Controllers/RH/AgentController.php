<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\RH\Agent;

class AgentController extends Controller
{
    /**
     * Affiche la photo d'un agent (identique à client).
     */
    public function photo($filename)
    {
        $path = base_path('images_projet/agents/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }
        // On vide les tampons pour éviter tout caractère parasite
        if (ob_get_level()) ob_end_clean();
        $type = mime_content_type($path);
        return response()->file($path, [
            'Content-Type' => $type,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Length' => filesize($path),
        ]);
    }

    public function index()
    {
        $agents = \App\Models\RH\Agent::orderByDesc('created_at')->get();
        return view('rh.agents.liste', compact('agents'));
    }

    public function create()
    {
        return view('rh.agents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:191',
            'postnom' => 'nullable|string|max:191',
            'prenom' => 'nullable|string|max:191',
            'sexe' => 'required|in:M,F',
            'date_naissance' => 'nullable|date',
            'telephone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'adresse' => 'nullable|string|max:191',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'date_embauche' => 'nullable|date',
            'statut' => 'required|in:actif,inactif',
        ]);


        // Ne pas fournir de matricule, il sera généré automatiquement par le modèle Agent

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $imageName = time() . '_' . Str::slug($image->getClientOriginalName());
            $destinationPath = base_path('images_projet/agents');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $image->move($destinationPath, $imageName);
            $validated['photo'] = 'agents/' . $imageName;
        }

        $agent = \App\Models\RH\Agent::create($validated);
        return redirect()->route('agents.create')->with('success', 'Agent ajouté avec succès. Matricule : ' . $agent->matricule);
    }

    public function show($matricule)
    {
        $agent = \App\Models\RH\Agent::where('matricule', $matricule)->firstOrFail();
        return view('rh.agents.show', compact('agent'));
    }

    public function edit($matricule)
    {
        $agent = \App\Models\RH\Agent::where('matricule', $matricule)->firstOrFail();
        return view('rh.agents.edit', compact('agent'));
    }

    public function update(Request $request, $matricule)
    {
        $agent = \App\Models\RH\Agent::where('matricule', $matricule)->firstOrFail();
        $validated = $request->validate([
            'nom' => 'required|string|max:191',
            'postnom' => 'nullable|string|max:191',
            'prenom' => 'nullable|string|max:191',
            'sexe' => 'required|in:M,F',
            'date_naissance' => 'nullable|date',
            'telephone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'adresse' => 'nullable|string|max:191',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'date_embauche' => 'nullable|date',
            'statut' => 'required|in:actif,inactif',
        ]);

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $imageName = time() . '_' . Str::slug($image->getClientOriginalName());
            $destinationPath = base_path('images_projet/agents');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $image->move($destinationPath, $imageName);
            $validated['photo'] = 'agents/' . $imageName;
        }

        $agent->update($validated);
        return redirect()->route('agents.edit', $matricule)->with('success', 'Agent modifié avec succès.');
    }

    public function destroy($matricule)
    {
        $agent = \App\Models\RH\Agent::where('matricule', $matricule)->firstOrFail();
        // Supprimer la photo si elle existe
        if ($agent->photo && file_exists(base_path('images_projet/' . $agent->photo))) {
            @unlink(base_path('images_projet/' . $agent->photo));
        }
        $agent->delete();
        return redirect()->route('agents.index')->with('success', 'Agent supprimé avec succès.');
    }
}
