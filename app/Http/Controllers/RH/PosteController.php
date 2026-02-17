<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poste;
use App\Models\Service;

class PosteController extends Controller
{
    public function index($service_id)
    {
        $service = Service::findOrFail($service_id);
        $postes = Poste::where('service_id', $service_id)->get();
        return view('rh.postes.liste', compact('service', 'postes'));
    }

    public function store(Request $request, $service_id)
    {
        $service = Service::findOrFail($service_id);
        $validated = $request->validate([
            'nom' => 'required|string|max:191',
            'description' => 'nullable|string',
        ]);
        $validated['service_id'] = $service_id;
        Poste::create($validated);
        return redirect()->route('postes.index', $service_id)->with('success', 'Poste ajouté avec succès.');
    }

    // AJAX: retourne la vue partielle des postes pour un service
    public function ajaxListe($service_id)
    {
        $service = Service::findOrFail($service_id);
        $postes = Poste::where('service_id', $service_id)->get();
        // On réutilise la même vue partielle
        return view('rh.postes.liste', compact('service', 'postes'))->render();
    }

    // AJAX: ajoute un poste et retourne JSON
    public function ajaxStore(Request $request, $service_id)
    {
        $service = Service::findOrFail($service_id);
        $validated = $request->validate([
            'nom' => 'required|string|max:191',
            'description' => 'nullable|string',
        ]);
        $validated['service_id'] = $service_id;
        $poste = Poste::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'Poste ajouté avec succès.',
            'poste' => $poste
        ]);
    }
}
