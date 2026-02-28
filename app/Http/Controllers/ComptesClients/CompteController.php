<?php


namespace App\Http\Controllers\ComptesClients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Compte;
use App\Models\Client;
use Illuminate\Support\Facades\Log;


class CompteController extends Controller
{
    // Affiche le formulaire d'ouverture de compte
    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        $comptes = Compte::with('client')->orderByDesc('created_at')->get();
        return view('comptes_clients.create', compact('clients', 'comptes'));
    }

    // Enregistre un nouveau compte
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_matricule' => 'required|exists:tb_clients,matricule',
            'type' => 'required|in:COURANT,EPARGNE_LIBRE,EPARGNE_BLOQUEE,CAUTION_CREDIT',
        ]);

        // Vérifier si le client a déjà un compte de ce type
        $existe = Compte::where('client_matricule', $validated['client_matricule'])
            ->where('type', $validated['type'])
            ->exists();
        if ($existe) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Ce client possède déjà un compte de ce type.'
                ], 422);
            }
            return redirect()->route('comptes.create')
                ->with('success', null)
                ->with('error', 'Ce client possède déjà un compte de ce type.');
        }

        $compte = Compte::create([
            'client_matricule' => $validated['client_matricule'],
            'type' => $validated['type'],
            'solde_reel' => 0,
            'solde_bloque' => 0,
        ]);

        return redirect()->route('comptes.create')->with('success', 'Compte ouvert avec succès.');
    }

    // Fonction de génération sécurisée du numéro de compte
    protected function generateSecureAccountNumber($length = 12)
    {
        // Format : 12 chiffres aléatoires
        $digits = '';
        for ($i = 0; $i < $length; $i++) {
            $digits .= random_int(0, 9);
        }
        // Optionnel : ajouter un préfixe ou checksum
        return $digits;
    }
}
