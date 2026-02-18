<?php

namespace App\Http\Controllers\RH;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
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
        $services = Service::orderByDesc('created_at')->get();
        return view('rh.services.liste', compact('services'));
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
            \Log::error('Erreur suppression service: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression du service.'], 500);
        }
    }
}
