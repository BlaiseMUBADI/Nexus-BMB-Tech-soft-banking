<?php


namespace App\Http\Controllers\ComptesClients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clients\Compte;
use App\Models\Clients\Client;
use App\Models\Caisse\Transaction;
use App\Models\RH\Affectation;
use App\Models\Zone;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tresorerie\Devise;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;


class CompteController extends Controller
{
    private function getCurrentGuichetType(): ?string
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || empty($user->agent_matricule)) {
            return null;
        }

        $affectation = Affectation::with('guichet')
            ->where('agent_matricule', $user->agent_matricule)
            ->where('Etat', 'ACTIF')
            ->whereNotNull('guichet_id')
            ->latest('date_debut')
            ->first();

        return $affectation?->guichet?->type_guichet;
    }

    private function isMobileGuichet(): bool
    {
        return $this->getCurrentGuichetType() === 'MOBILE';
    }

    private function abortIfMobilePrintForbidden(string $documentType): void
    {
        if (!$this->isMobileGuichet()) {
            return;
        }

        Log::warning('[Compte] Impression refusée pour guichet mobile', [
            'document_type' => $documentType,
            'agent_matricule' => Auth::user()?->agent_matricule,
            'ip' => request()->ip(),
        ]);

        abort(403, 'Accès refusé : un guichet mobile ne peut pas imprimer les documents liés au compte ou au client.');
    }

    private function allowedOperationTypesForHistory(): array
    {
        if ($this->getCurrentGuichetType() === 'MOBILE') {
            return [
                Transaction::DEPOT,
                Transaction::CHANGE,
                Transaction::PAIEMENT,
                Transaction::REMBOURSEMENT,
            ];
        }

        return [
            Transaction::DEPOT,
            Transaction::RETRAIT,
            Transaction::VIREMENT,
            Transaction::CHANGE,
            Transaction::PAIEMENT,
            Transaction::REMBOURSEMENT,
        ];
    }

    private function operationTypeOptionsForHistory(): array
    {
        return collect($this->allowedOperationTypesForHistory())
            ->map(fn ($type) => [
                'value' => $type,
                'label' => Transaction::typeLabel($type),
            ])
            ->values()
            ->all();
    }

    private function buildZoneLabel(array $zoneNames): string
    {
        $zoneNames = array_values(array_filter($zoneNames));
        if (empty($zoneNames)) {
            return '';
        }

        if (count($zoneNames) === 1) {
            $label = trim($zoneNames[0]);
            return preg_match('/^zones?\b/i', $label) ? $label : 'Zone ' . $label;
        }

        $joined = implode(', ', array_map('trim', $zoneNames));
        return preg_match('/^zones?\b/i', $joined) ? $joined : 'Zones ' . $joined;
    }

    private function resolveZoneScope(): array
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || empty($user->agent_matricule)) {
            return ['restricted' => false, 'zone_codes' => []];
        }

        $affectation = Affectation::with('guichet')
            ->where('agent_matricule', $user->agent_matricule)
            ->where('Etat', 'ACTIF')
            ->whereNotNull('guichet_id')
            ->latest('date_debut')
            ->first();

        if (!$affectation || !$affectation->guichet || $affectation->guichet->type_guichet !== 'MOBILE') {
            return ['restricted' => false, 'zone_codes' => []];
        }

        $zones = Zone::assignedToAgent($user->agent_matricule)
            ->orderBy('nom')
            ->get(['code_zone', 'nom']);

        $zoneCodes = $zones->pluck('code_zone')
            ->filter()
            ->values()
            ->all();

        $zoneNames = $zones->pluck('nom')
            ->filter()
            ->values()
            ->all();

        $zoneLabel = $this->buildZoneLabel($zoneNames);

        return [
            'restricted' => true,
            'zone_codes' => $zoneCodes,
            'zone_names' => $zoneNames,
            'zone_label' => $zoneLabel,
            'agent_matricule' => $user->agent_matricule,
        ];
    }

    private function applyZoneScopeToComptes($query, array $zoneScope)
    {
        if (!($zoneScope['restricted'] ?? false)) {
            return $query;
        }

        $zoneCodes = $zoneScope['zone_codes'] ?? [];
        if (empty($zoneCodes)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('client', fn ($q) => $q->whereIn('code_zone', $zoneCodes));
    }

    private function canAccessCompte(?Compte $compte, array $zoneScope): bool
    {
        if (!$compte) {
            return false;
        }

        if (!($zoneScope['restricted'] ?? false)) {
            return true;
        }

        return in_array(optional($compte->client)->code_zone, $zoneScope['zone_codes'] ?? [], true);
    }

    private function restrictedZonesQuery(array $zoneScope)
    {
        $zones = Zone::orderBy('nom');

        if (!($zoneScope['restricted'] ?? false)) {
            return $zones;
        }

        $zoneCodes = $zoneScope['zone_codes'] ?? [];
        if (empty($zoneCodes)) {
            return $zones->whereRaw('1 = 0');
        }

        return $zones->whereIn('code_zone', $zoneCodes);
    }

    private function zoneRestrictionInfo(array $zoneScope): array
    {
        return [
            'active' => (bool) ($zoneScope['restricted'] ?? false),
            'zone_count' => count($zoneScope['zone_codes'] ?? []),
            'zone_names' => $zoneScope['zone_names'] ?? [],
            'zone_label' => $zoneScope['zone_label'] ?? '',
        ];
    }

    // Affiche le formulaire d'ouverture de compte
    public function create()
    {
        $zoneScope = $this->resolveZoneScope();

        $clientsQuery = Client::orderBy('nom');
        if ($zoneScope['restricted'] ?? false) {
            $zoneCodes = $zoneScope['zone_codes'] ?? [];
            if (empty($zoneCodes)) {
                $clientsQuery->whereRaw('1 = 0');
            } else {
                $clientsQuery->whereIn('code_zone', $zoneCodes);
            }
        }

        $comptesQuery = Compte::with(['client', 'portefeuille.agent', 'portefeuille.affectationActive.agent'])->orderByDesc('created_at');
        $this->applyZoneScopeToComptes($comptesQuery, $zoneScope);

        $clients = $clientsQuery->get();
        $comptes = $comptesQuery->get();
        $portefeuilles = \App\Models\Tresorerie\Portefeuille::with(['agent', 'affectationActive.agent'])->orderBy('nom_portefeuille')->get();
        $devises = \App\Models\Tresorerie\Devise::orderBy('nom')->get();
        $zoneRestriction = $this->zoneRestrictionInfo($zoneScope);

        return view('comptes_clients.create', compact('clients', 'comptes', 'portefeuilles', 'devises', 'zoneRestriction'));
    }

    // Enregistre un nouveau compte
    public function store(Request $request)
    {
        $zoneScope = $this->resolveZoneScope();

        $validated = $request->validate([
            'client_matricule' => 'required|exists:tb_clients,matricule',
            'type' => 'required|in:CC,RMB,GTC,DAT,EAV',
            'devise' => [
                'required',
                'exists:tb_devises,code_iso',
                Rule::unique('tb_comptes', 'devise')
                    ->where(fn ($query) => $query
                        ->where('client_matricule', $request->input('client_matricule'))
                        ->where('type', $request->input('type'))),
            ],
            'portefeuille_id' => $request->type === 'GTC' ? 'required|exists:tb_portefeuilles_agents,id' : 'nullable',
        ]);

        $client = Client::where('matricule', $validated['client_matricule'])->first();
        if (!$client) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Client introuvable.'], 404);
            }
            return redirect()->route('comptes.create')->with('error', 'Client introuvable.');
        }

        if (($zoneScope['restricted'] ?? false) && !in_array($client->code_zone, $zoneScope['zone_codes'] ?? [], true)) {
            Log::warning('[Compte] Tentative d\'ouverture hors zone autorisée', [
                'agent_matricule' => Auth::user()?->agent_matricule,
                'client_matricule' => $validated['client_matricule'],
                'client_zone' => $client->code_zone,
                'zones_autorisees' => $zoneScope['zone_codes'] ?? [],
                'ip' => request()->ip(),
            ]);

            $message = 'Accès refusé : vous ne pouvez ouvrir un compte que pour les clients de votre zone.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 403);
            }
            return redirect()->route('comptes.create')->with('error', $message);
        }

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

        try {
            $compte = Compte::create([
                'client_matricule' => $validated['client_matricule'],
                'type' => $validated['type'],
                'solde_reel' => 0,
                'solde_bloque' => 0,
                'devise' => $validated['devise'],
                'portefeuille_id' => $validated['portefeuille_id'] ?? null,
            ]);
        } catch (QueryException $e) {
            if (($e->errorInfo[0] ?? null) === '23000') {
                $message = 'Doublon détecté : ce client possède déjà un compte de ce type dans cette devise.';

                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()->route('comptes.create')->with('error', $message);
            }

            throw $e;
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Compte ouvert avec succès.']);
        }
        return redirect()->route('comptes.create')->with('success', 'Compte ouvert avec succès.');
    }

    // Supprime un compte
    public function destroy($code_compte)
    {
        try {
            $zoneScope = $this->resolveZoneScope();
            $compte = Compte::find($code_compte);
            if (!$compte) {
                Log::warning('[Compte] Compte introuvable', ['code_compte' => $code_compte, 'action' => 'destroy', 'ip' => request()->ip()]);
                return response()->json(['success' => false, 'message' => 'Compte introuvable : ' . $code_compte], 404);
            }

            $compte->loadMissing('client');
            if (!$this->canAccessCompte($compte, $zoneScope)) {
                Log::warning('[Compte] Suppression refusée hors zone', ['code_compte' => $code_compte, 'ip' => request()->ip()]);
                return response()->json(['success' => false, 'message' => 'Accès refusé : compte hors de votre zone.'], 403);
            }

            $compte->delete();
            return response()->json(['success' => true, 'message' => 'Compte supprimé avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression : ' . $e->getMessage()], 500);
        }
    }

    // Affiche la liste des comptes avec les relations client et portefeuille.agent
    public function index()
    {
        $zoneScope = $this->resolveZoneScope();

        $query = Compte::with(['client', 'portefeuille.agent', 'portefeuille.affectationActive.agent'])->orderByDesc('created_at');
        $this->applyZoneScopeToComptes($query, $zoneScope);
        $comptes = $query->get();

        $stats = [
            'total'          => $comptes->count(),
            'courant'        => $comptes->where('type', 'CC')->count(),
            'remboursement'  => $comptes->where('type', 'RMB')->count(),
            'caution'        => $comptes->where('type', 'GTC')->count(),
            'depot_terme'    => $comptes->where('type', 'DAT')->count(),
            'epargne_vie'    => $comptes->where('type', 'EAV')->count(),
        ];
        $devises = Devise::orderBy('nom')->get();
        $zones   = $this->restrictedZonesQuery($zoneScope)->get();
        $portefeuilles = \App\Models\Tresorerie\Portefeuille::with(['agent', 'affectationActive.agent'])->orderBy('nom_portefeuille')->get();
        $zoneRestriction = $this->zoneRestrictionInfo($zoneScope);

        $canPrintDocuments = !$this->isMobileGuichet();

        return view('comptes_clients.liste', compact('comptes', 'stats', 'devises', 'zones', 'portefeuilles', 'zoneRestriction', 'canPrintDocuments'));
    }

    public function show($code_compte)
    {
        $zoneScope = $this->resolveZoneScope();
        $compte = Compte::with(['client', 'portefeuille.agent', 'portefeuille.affectationActive.agent'])->find($code_compte);
        if (!$compte) {
            Log::warning('[Compte] Compte introuvable', ['code_compte' => $code_compte, 'action' => 'show', 'ip' => request()->ip()]);
            abort(404, 'Compte introuvable : ' . $code_compte);
        }

        if (!$this->canAccessCompte($compte, $zoneScope)) {
            Log::warning('[Compte] Accès refusé hors zone', ['code_compte' => $code_compte, 'action' => 'show', 'ip' => request()->ip()]);
            abort(403, 'Accès refusé : ce compte n\'appartient pas à votre zone.');
        }

        return view('comptes_clients.show', compact('compte'));
    }

    public function edit($code_compte)
    {
        $zoneScope = $this->resolveZoneScope();
        $compte = Compte::with(['client', 'portefeuille.agent'])->find($code_compte);
        if (!$compte) {
            Log::warning('[Compte] Compte introuvable', ['code_compte' => $code_compte, 'action' => 'edit', 'ip' => request()->ip()]);
            abort(404, 'Compte introuvable : ' . $code_compte);
        }

        if (!$this->canAccessCompte($compte, $zoneScope)) {
            Log::warning('[Compte] Accès refusé hors zone', ['code_compte' => $code_compte, 'action' => 'edit', 'ip' => request()->ip()]);
            abort(403, 'Accès refusé : ce compte n\'appartient pas à votre zone.');
        }

        return view('comptes_clients.edit', compact('compte'));
    }

    // ── Impression RIB ─────────────────────────────────────────────────────
    public function imprimerRIB(string $code_compte)
    {
        $this->abortIfMobilePrintForbidden('rib');

        $zoneScope = $this->resolveZoneScope();
        $compte = Compte::with(['client'])->find($code_compte);
        if (!$compte) {
            Log::warning('[Compte] Compte introuvable pour RIB', ['code_compte' => $code_compte, 'ip' => request()->ip()]);
            abort(404, 'Compte introuvable : ' . $code_compte);
        }

        if (!$this->canAccessCompte($compte, $zoneScope)) {
            Log::warning('[Compte] Impression RIB refusée hors zone', ['code_compte' => $code_compte, 'ip' => request()->ip()]);
            abort(403, 'Accès refusé : ce compte n\'appartient pas à votre zone.');
        }

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
        $this->abortIfMobilePrintForbidden('releve-bancaire');

        $zoneScope = $this->resolveZoneScope();
        $compte  = Compte::with(['client'])->find($code_compte);
        if (!$compte) {
            Log::warning('[Compte] Compte introuvable pour relevé', ['code_compte' => $code_compte, 'ip' => request()->ip()]);
            abort(404, 'Compte introuvable : ' . $code_compte);
        }

        if (!$this->canAccessCompte($compte, $zoneScope)) {
            Log::warning('[Compte] Relevé refusé hors zone', ['code_compte' => $code_compte, 'ip' => request()->ip()]);
            abort(403, 'Accès refusé : ce compte n\'appartient pas à votre zone.');
        }

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
        $this->abortIfMobilePrintForbidden('liste-comptes');

        $zoneScope = $this->resolveZoneScope();
        $query = Compte::with(['client.zone']);
        $this->applyZoneScopeToComptes($query, $zoneScope);

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
            if (($zoneScope['restricted'] ?? false) && !in_array($request->code_zone, $zoneScope['zone_codes'] ?? [], true)) {
                Log::warning('[Compte] Impression liste refusée hors zone', [
                    'code_zone' => $request->code_zone,
                    'agent_matricule' => Auth::user()?->agent_matricule,
                    'ip' => request()->ip(),
                ]);
                $query->whereRaw('1 = 0');
            } else {
                $query->whereHas('client', fn($q) => $q->where('code_zone', $request->code_zone));
            }
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
        $zone = null;
        if ($request->filled('code_zone') && (!($zoneScope['restricted'] ?? false) || in_array($request->code_zone, $zoneScope['zone_codes'] ?? [], true))) {
            $zone = \App\Models\Zone::find($request->code_zone);
        }
        $portefeuille = ($request->filled('portefeuille_id') && $request->portefeuille_id !== 'tous' && $request->portefeuille_id !== 'aucun')
                ? \App\Models\Tresorerie\Portefeuille::with(['agent', 'affectationActive.agent'])->find($request->portefeuille_id) : null;

        $pdf = Pdf::loadView('impressions.comptes.liste', compact('comptes', 'filtres', 'zone', 'portefeuille'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('Liste_comptes.pdf');
    }

    // ── Historique des mouvements d'un compte ────────────────────────────────
    public function historiqueCompte(\Illuminate\Http\Request $request, string $code_compte)
    {
        $zoneScope = $this->resolveZoneScope();
        $allowedOperationTypes = $this->allowedOperationTypesForHistory();
        $operationTypeOptions = $this->operationTypeOptionsForHistory();

        $compte = Compte::with(['client'])->find($code_compte);
        if (!$compte) {
            Log::warning('[Compte] Compte introuvable pour historique', ['code_compte' => $code_compte, 'ip' => request()->ip()]);
            abort(404, 'Compte introuvable : ' . $code_compte);
        }

        if (!$this->canAccessCompte($compte, $zoneScope)) {
            Log::warning('[Compte] Historique refusé hors zone', ['code_compte' => $code_compte, 'ip' => request()->ip()]);
            abort(403, 'Accès refusé : ce compte n\'appartient pas à votre zone.');
        }

        $devise = Devise::where('code_iso', $compte->devise)->first();

        $query = Transaction::where('compte_code', $code_compte)
            ->whereIn('type', $allowedOperationTypes)
            ->with(['guichet']);

        if ($request->filled('date_debut')) {
            $query->whereDate('date_operation', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_operation', '<=', $request->date_fin);
        }
        if ($request->filled('type') && $request->type !== 'tous' && in_array($request->type, $allowedOperationTypes, true)) {
            $query->where('type', $request->type);
        }
        if ($request->filled('statut') && $request->statut !== 'tous') {
            $query->where('statut', $request->statut);
        }

        $transactions = $query->orderByDesc('date_operation')->paginate(30)->withQueryString();

        return view('comptes_clients.historique', compact('compte', 'devise', 'transactions', 'operationTypeOptions'));
    }
}
