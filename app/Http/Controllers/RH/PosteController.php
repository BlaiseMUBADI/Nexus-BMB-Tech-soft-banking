<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RH\Poste;
use App\Models\RH\Service;
use Illuminate\Support\Facades\Log;

class PosteController extends Controller
{
    // AJAX: supprime un poste et retourne JSON
    public function ajaxDestroy($service_id, $poste_id)
    {
        $poste = Poste::where('service_id', $service_id)->find($poste_id);
        if (!$poste) {
            Log::warning('[RH] Poste introuvable', ['service_id' => $service_id, 'poste_id' => $poste_id, 'action' => 'ajaxDestroy', 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Poste introuvable.'], 404);
        }
        $poste->delete();
        return response()->json([
            'success' => true,
            'message' => 'Poste supprimé avec succès.'
        ]);
    }
    public function index($service_id)
    {
        $service = Service::find($service_id);
        if (!$service) {
            Log::warning('[RH] Service introuvable', ['service_id' => $service_id, 'action' => 'postes.index', 'ip' => request()->ip()]);
            abort(404, 'Service introuvable.');
        }
        $postes = Poste::where('service_id', $service_id)->get();
        return view('rh.postes.liste', compact('service', 'postes'));
    }

    public function store(Request $request, $service_id)
    {
        $service = Service::find($service_id);
        if (!$service) {
            Log::warning('[RH] Service introuvable', ['service_id' => $service_id, 'action' => 'postes.store', 'ip' => request()->ip()]);
            abort(404, 'Service introuvable.');
        }
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
        Log::info('[DEBUG] Entrée ajaxListe', ['service_id' => $service_id]);
        $service = Service::find($service_id);
        if (!$service) {
            Log::warning('[RH] Service introuvable', ['service_id' => $service_id, 'action' => 'ajaxListe', 'ip' => request()->ip()]);
            abort(404, 'Service introuvable.');
        }
        Log::info('[DEBUG] Service trouvé', ['service' => $service]);
        $postes = Poste::where('service_id', $service_id)->get();
        Log::info('[DEBUG] Postes récupérés', ['count' => $postes->count()]);
        // On réutilise la même vue partielle
        $view = view('rh.postes.liste', compact('service', 'postes'))->render();
        Log::info('[DEBUG] Vue partielle générée');
        return $view;
    }

    // AJAX: ajoute un poste et retourne JSON
    public function ajaxStore(Request $request, $service_id)
    {
        Log::info('[DEBUG] Entrée ajaxStore', ['service_id' => $service_id, 'input' => $request->all()]);
        $service = Service::find($service_id);
        if (!$service) {
            Log::warning('[RH] Service introuvable', ['service_id' => $service_id, 'action' => 'ajaxStore', 'ip' => request()->ip()]);
            return response()->json(['success' => false, 'message' => 'Service introuvable.'], 404);
        }
        Log::info('[DEBUG] Service trouvé', ['service' => $service]);
        $validated = $request->validate([
            'nom' => 'required|string|max:191',
            'description' => 'nullable|string',
        ]);
        Log::info('[DEBUG] Données validées', ['validated' => $validated]);
        $validated['service_id'] = $service_id;
        $poste = Poste::create($validated);
        Log::info('[DEBUG] Poste créé', ['poste' => $poste]);
        return response()->json([
            'success' => true,
            'message' => 'Poste ajouté avec succès.',
            'poste' => $poste
        ]);
    }
}
