<?php


namespace App\Http\Controllers\ComptesClients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clients\Compte;
use App\Models\Clients\Client;
use Illuminate\Support\Facades\Log;
use App\Models\Tresorerie\Devise;
use Barryvdh\DomPDF\Facade\Pdf;


class CompteController extends Controller
{
    // Affiche le formulaire d'ouverture de compte
    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        $comptes = Compte::with(['client', 'portefeuille.agent'])->orderByDesc('created_at')->get();
        $portefeuilles = \App\Models\Tresorerie\Portefeuille::with('agent')->orderBy('nom_portefeuille')->get();
        $devises = \App\Models\Tresorerie\Devise::orderBy('nom')->get();
        return view('comptes_clients.create', compact('clients', 'comptes', 'portefeuilles', 'devises'));
    }

    // Enregistre un nouveau compte
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_matricule' => 'required|exists:tb_clients,matricule',
            'type' => 'required|in:CC,RMB,GTC,DAT,EAV',
            'devise' => 'required|exists:tb_devises,code_iso',
            'portefeuille_id' => $request->type === 'GTC' ? 'required|exists:tb_portefeuilles_agents,id' : 'nullable',
        ]);

        // Vérifier si le client a déjà un compte de ce type et de cette devise
        $existe = Compte::where('client_matricule', $validated['client_matricule'])
            ->where('type', $validated['type'])
            ->where('devise', $validated['devise'])
            ->exists();
        if ($existe) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce client possède déjà un compte de ce type et de cette devise.'
                ], 422);
            }
            return redirect()->route('comptes.create')
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
            return response()->json(['success' => true, 'message' => 'Compte ouvert avec succès.']);
        }
        return redirect()->route('comptes.create')->with('success', 'Compte ouvert avec succès.');
    }

    // Supprime un compte
    public function destroy($code_compte)
    {
        try {
            $compte = Compte::findOrFail($code_compte);
            $compte->delete();
            return response()->json(['success' => true, 'message' => 'Compte supprimé avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression : ' . $e->getMessage()], 500);
        }
    }

    // Affiche la liste des comptes avec les relations client et portefeuille.agent
    public function index()
    {
        $comptes = Compte::with(['client', 'portefeuille.agent'])->orderByDesc('created_at')->get();
        $stats = [
            'total'          => $comptes->count(),
            'courant'        => $comptes->where('type', 'CC')->count(),
            'remboursement'  => $comptes->where('type', 'RMB')->count(),
            'caution'        => $comptes->where('type', 'GTC')->count(),
            'depot_terme'    => $comptes->where('type', 'DAT')->count(),
            'epargne_vie'    => $comptes->where('type', 'EAV')->count(),
        ];
        $devises = Devise::orderBy('nom')->get();
        $zones   = \App\Models\Zone::orderBy('nom')->get();
        $portefeuilles = \App\Models\Tresorerie\Portefeuille::with('agent')->orderBy('nom_portefeuille')->get();
        return view('comptes_clients.liste', compact('comptes', 'stats', 'devises', 'zones', 'portefeuilles'));
    }

    public function show($code_compte)
    {
        $compte = Compte::with(['client', 'portefeuille.agent'])->findOrFail($code_compte);
        return view('comptes_clients.show', compact('compte'));
    }

    public function edit($code_compte)
    {
        $compte = Compte::with(['client', 'portefeuille.agent'])->findOrFail($code_compte);
        return view('comptes_clients.edit', compact('compte'));
    }

    // ── Impression RIB ─────────────────────────────────────────────────────
    public function imprimerRIB(string $code_compte)
    {
        $compte = Compte::with(['client'])->findOrFail($code_compte);
        $client = $compte->client;
        $devise = Devise::where('code_iso', $compte->devise)->first();

        // Construction IBAN simplifié COOPEC EBEN
        $iban = 'CD89 EBEN G001 ' . implode(' ', str_split(str_pad($code_compte, 16, '0', STR_PAD_LEFT), 4));

        $pdf = Pdf::loadView('impressions.comptes.rib', compact('compte', 'client', 'devise', 'iban'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('RIB_' . $code_compte . '.pdf');
    }
    // ── Relevé bancaire ───────────────────────────────────────────────────
    public function releveCompte(\Illuminate\Http\Request $request, string $code_compte)
    {
        $compte  = Compte::with(['client'])->findOrFail($code_compte);
        $client  = $compte->client;
        $devise  = Devise::where('code_iso', $compte->devise)->first();

        $dateDebut = $request->filled('date_debut')
            ? \Carbon\Carbon::parse($request->date_debut)->startOfDay()
            : now()->startOfMonth();

        $dateFin = $request->filled('date_fin')
            ? \Carbon\Carbon::parse($request->date_fin)->endOfDay()
            : now()->endOfDay();

        $transactions = \App\Models\Caisse\Transaction::where('compte_code', $code_compte)
            ->whereBetween('date_operation', [$dateDebut, $dateFin])
            ->where('statut', 'CONFIRME')
            ->orderBy('date_operation')
            ->get();

        // Solde d'ouverture = solde actuel − somme des mouvements dans la période
        $soldeActuel = (float) $compte->solde_reel;
        $mouvement = $transactions->sum(function ($t) {
            return $t->type === 'DEPOT' ? (float)$t->montant : -((float)$t->montant);
        });
        $soldeOuverture = $soldeActuel - $mouvement;

        // Photo client en base64 pour PDF
        $photoBase64 = null;
        if ($client && $client->photo) {
            $photoPath = base_path('images_projet/' . $client->photo);
            if (file_exists($photoPath)) {
                $mime = mime_content_type($photoPath);
                $photoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($photoPath));
            }
        }

        $pdf = Pdf::loadView('impressions.comptes.releve', compact(
            'compte', 'client', 'devise', 'transactions',
            'soldeOuverture', 'soldeActuel', 'dateDebut', 'dateFin', 'photoBase64'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('Releve_' . $code_compte . '_' . $dateDebut->format('Ymd') . '_' . $dateFin->format('Ymd') . '.pdf');
    }

    // ── Impression liste filtrée ───────────────────────────────────────────────
    public function imprimerListe(\Illuminate\Http\Request $request)
    {
        $query = Compte::with(['client.zone']);

        if ($request->filled('type') && $request->type !== 'tous') {
            $query->where('type', $request->type);
        }
        if ($request->filled('devise') && $request->devise !== 'tous') {
            $query->where('devise', $request->devise);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        if ($request->filled('solde_min')) {
            $query->where('solde_reel', '>=', (float) $request->solde_min);
        }
        if ($request->filled('solde_max')) {
            $query->where('solde_reel', '<=', (float) $request->solde_max);
        }
        if ($request->filled('etat_solde') && $request->etat_solde !== 'tous') {
            if ($request->etat_solde === 'positif') {
                $query->where('solde_reel', '>', 0);
            } elseif ($request->etat_solde === 'negatif') {
                $query->where('solde_reel', '<', 0);
            } elseif ($request->etat_solde === 'nul') {
                $query->where('solde_reel', '=', 0);
            }
        }
        if ($request->filled('code_zone')) {
            $query->whereHas('client', fn($q) => $q->where('code_zone', $request->code_zone));
        }
        if ($request->filled('portefeuille_id') && $request->portefeuille_id !== 'tous') {
            if ($request->portefeuille_id === 'aucun') {
                $query->whereNull('portefeuille_id');
            } else {
                $query->where('portefeuille_id', $request->portefeuille_id);
            }
        }

        $comptes = $query->orderBy('type')->orderBy('code_compte')->get();
        $filtres = $request->only(['type','devise','date_debut','date_fin','solde_min','solde_max','etat_solde','code_zone','portefeuille_id']);
        $zone    = $request->filled('code_zone') ? \App\Models\Zone::find($request->code_zone) : null;
        $portefeuille = ($request->filled('portefeuille_id') && $request->portefeuille_id !== 'tous' && $request->portefeuille_id !== 'aucun')
                        ? \App\Models\Tresorerie\Portefeuille::with('agent')->find($request->portefeuille_id) : null;

        $pdf = Pdf::loadView('impressions.comptes.liste', compact('comptes', 'filtres', 'zone', 'portefeuille'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('Liste_comptes.pdf');
    }

    // ── Historique des mouvements d'un compte ────────────────────────────────
    public function historiqueCompte(\Illuminate\Http\Request $request, string $code_compte)
    {
        $compte = Compte::with(['client'])->findOrFail($code_compte);
        $devise = Devise::where('code_iso', $compte->devise)->first();

        $query = \App\Models\Caisse\Transaction::where('compte_code', $code_compte)
            ->with(['guichet']);

        if ($request->filled('date_debut')) {
            $query->whereDate('date_operation', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_operation', '<=', $request->date_fin);
        }
        if ($request->filled('type') && $request->type !== 'tous') {
            $query->where('type', $request->type);
        }
        if ($request->filled('statut') && $request->statut !== 'tous') {
            $query->where('statut', $request->statut);
        }

        $transactions = $query->orderByDesc('date_operation')->paginate(30)->withQueryString();

        return view('comptes_clients.historique', compact('compte', 'devise', 'transactions'));
    }
}
