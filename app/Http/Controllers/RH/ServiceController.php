<?php

namespace App\Http\Controllers\RH;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Poste;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    /**
     * Retourne la liste des postes d'un service (AJAX)
     */
    public function postesAjax($id)
    {
        $service = \App\Models\Service::findOrFail($id);
        $postes = $service->postes;
        return view('rh.services.postes_table', compact('postes', 'service'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:191',
            'description' => 'nullable|string',
        ]);
        $service = \App\Models\Service::create($validated);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Service ajouté avec succès.',
                'service' => $service
            ]);
        }
        return redirect()->route('services.index')->with('success', 'Service ajouté avec succès.');
    }

    public function index()
    {
        $services = Service::withCount('postes')->orderByDesc('created_at')->get();

        $totalPostes        = Poste::count();
        $servicesAvecPostes = $services->where('postes_count', '>', 0)->count();
        $postesGuichet      = Poste::where('nom', 'like', '%guichet%')->count();

        $stats = [
            'total_services'      => $services->count(),
            'total_postes'        => $totalPostes,
            'services_avec_postes'=> $servicesAvecPostes,
            'postes_guichet'      => $postesGuichet,
        ];

        return view('rh.services.liste', compact('services', 'stats'));
    }
    /**
     * Supprimer un service (AJAX)
     */
    public function destroy($id)
    {
        $service = \App\Models\Service::find($id);
        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service introuvable.'], 404);
        }
        try {
            $service->delete();
            return response()->json(['success' => true, 'message' => 'Service supprimé avec succès.']);
        } catch (\Exception $e) {
            Log::error('Erreur suppression service: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression du service.'], 500);
        }
    }
    /**
     * Suppression AJAX d'un service (JSON)
     */
    public function ajaxDestroy($service_id)
    {
        $service = \App\Models\Service::find($service_id);
        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service introuvable.'], 404);
        }
        try {
            $service->delete();
            return response()->json(['success' => true, 'message' => 'Service supprimé avec succès.']);
        } catch (\Exception $e) {
            Log::error('Erreur suppression service: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression du service.'], 500);
        }
    }
}
