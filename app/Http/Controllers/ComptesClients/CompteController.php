<?php


namespace App\Http\Controllers\ComptesClients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Compte;
use App\Models\Client;
use Illuminate\Support\Facades\Log;
use App\Models\Devise;


class CompteController extends Controller
{
    // Affiche le formulaire d'ouverture de compte
    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        $comptes = Compte::with(['client', 'portefeuille.agent'])->orderByDesc('created_at')->get();
        $portefeuilles = \App\Models\Portefeuille::with('agent')->orderBy('nom_portefeuille')->get();
        $devises = \App\Models\Devise::orderBy('nom')->get();
        return view('comptes_clients.create', compact('clients', 'comptes', 'portefeuilles', 'devises'));
    }

    // Enregistre un nouveau compte
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_matricule' => 'required|exists:tb_clients,matricule',
            'type' => 'required|in:COURANT,EPARGNE_LIBRE,EPARGNE_BLOQUEE,CAUTION_CREDIT',
            'devise' => 'required|exists:tb_devises,code_iso',
            'portefeuille_id' => $request->type === 'CAUTION_CREDIT' ? 'required|exists:tb_portefeuilles_agents,id' : 'nullable',
        ]);

        // Vérifier si le client a déjà un compte de ce type et de cette devise
        $existe = Compte::where('client_matricule', $validated['client_matricule'])
            ->where('type', $validated['type'])
            ->where('devise', $validated['devise'])
            ->exists();
        if ($existe) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Ce client possède déjà un compte de ce type et de cette devise.'
                ], 422);
            }
            return redirect()->route('comptes.create')
                ->with('success', null)
                ->with('error', 'Ce client possède déjà un compte de ce type et de cette devise.');
        }

        $compte = Compte::create([
            'client_matricule' => $validated['client_matricule'],
            'type' => $validated['type'],
            'solde_reel' => 0,
            'solde_bloque' => 0,
            'devise' => $validated['devise'],
            'portefeuille_id' => $validated['portefeuille_id'] ?? null,
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Compte ouvert avec succès.']);
        }
        return redirect()->route('comptes.create')->with('success', 'Compte ouvert avec succès.');
    }

    // Supprime un compte
    public function destroy($code_compte)
    {
        $compte = Compte::findOrFail($code_compte);
        $compte->delete();
        return response()->json(['message' => 'Compte supprimé avec succès.']);
    }

    // Affiche la liste des comptes avec les relations client et portefeuille.agent
    public function index()
    {
        $comptes = Compte::with(['client', 'portefeuille.agent'])->orderByDesc('created_at')->get();
        $stats = [
            'total'          => $comptes->count(),
            'courant'        => $comptes->where('type', 'COURANT')->count(),
            'epargne'        => $comptes->whereIn('type', ['EPARGNE_LIBRE', 'EPARGNE_BLOQUEE'])->count(),
            'caution_credit' => $comptes->where('type', 'CAUTION_CREDIT')->count(),
        ];
        return view('comptes_clients.liste', compact('comptes', 'stats'));
    }

    public function show($code_compte)
    {
        $compte = Compte::with(['client', 'portefeuille.agent'])->findOrFail($code_compte);
        return view('comptes_clients.show', compact('compte'));
    }

    public function edit($code_compte)
    {
        $compte = Compte::with(['client', 'portefeuille.agent'])->findOrFail($code_compte);
        // Ajoute ici la logique d'édition si besoin
        return view('comptes_clients.edit', compact('compte'));
    }
}
