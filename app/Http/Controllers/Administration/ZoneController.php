<?php
namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PortefeuilleAffectation;
use App\Models\Zone;
use App\Models\ZoneAffectation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ZoneController extends Controller
{
    // Affiche la liste des zones (page unifiée Zones + Portefeuilles)
    public function index()
    {
        $this->expireObsoleteAffectations();

        $zones = Zone::with([
            'agent',
            'affectationActive.agent',
            'affectations' => fn ($query) => $query->latest('date_debut')->latest('id'),
        ])->orderBy('created_at', 'desc')->get();
        $agents        = \App\Models\RH\Agent::orderBy('nom')->get();
        $portefeuilles = \App\Models\Tresorerie\Portefeuille::with([
            'agent',
            'affectationActive.agent',
            'affectations' => fn ($query) => $query->latest('date_debut')->latest('id'),
        ])->orderBy('created_at', 'desc')->get();
        $zoneAffectationsRecent = ZoneAffectation::with(['zone', 'agent'])
            ->orderByDesc('date_debut')
            ->orderByDesc('id')
            ->limit(30)
            ->get();
        $portefeuilleAffectationsRecent = PortefeuilleAffectation::with(['portefeuille', 'agent'])
            ->orderByDesc('date_debut')
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        $stats = [
            'total_zones'         => $zones->count(),
            'zones_avec_agent'    => $zones->filter(fn($z) => $z->affectationActive?->agent || $z->agent)->count(),
            'total_portefeuilles' => $portefeuilles->count(),
            'portefeuilles_avec_agent' => $portefeuilles->filter(fn($pf) => $pf->affectationActive?->agent || $pf->agent)->count(),
            'taux_moyen'          => $portefeuilles->avg('taux_commission_agent') ?? 0,
        ];

        return view('administration.zones', compact(
            'zones',
            'agents',
            'portefeuilles',
            'stats',
            'zoneAffectationsRecent',
            'portefeuilleAffectationsRecent'
        ));
    }

    // Ajoute une nouvelle zone
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'agent_commercial_matricule' => 'nullable|string|max:50|exists:tb_agents,matricule',
            'commune' => 'required|string|max:100',
            'commune_autre' => 'nullable|string|max:100',
            'date_debut' => 'nullable|date|before_or_equal:today',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'etat_affectation' => 'nullable|string|in:ACTIF,INACTIF,TERMINE,EXPIRE',
            'motif' => 'nullable|string|max:255',
        ]);

        $commune = $request->input('commune');
        if ($commune === 'autre') {
            $commune = $request->input('commune_autre');
        }

        try {
            DB::transaction(function () use ($request, $commune): void {
                $this->expireObsoleteAffectations();

                $zone = Zone::create([
                    'nom' => $request->input('nom'),
                    'agent_commercial_matricule' => null,
                    'commune' => $commune,
                ]);

                if ($request->filled('agent_commercial_matricule')) {
                    ZoneAffectation::create(array_merge(
                        ['code_zone' => $zone->code_zone],
                        $this->buildZoneAffectationPayload(
                            agentMatricule: $request->input('agent_commercial_matricule'),
                            dateDebut: $request->input('date_debut'),
                            dateFin: $request->input('date_fin'),
                            etat: $request->input('etat_affectation'),
                            motif: $request->input('motif'),
                            defaultMotif: 'Création de la zone'
                        )
                    ));
                }

                $this->synchronizeLegacyZoneAgent($zone);
            });
        } catch (\Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Zone ajoutée avec succès.']);
        }
        return redirect()->route('administration.zones.index')->with('success', 'Zone ajoutée avec succès.');
    }

    // Modifie une zone (infos + propriétaire/agent affecté)
    public function update(Request $request, string $code_zone)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'agent_commercial_matricule' => 'nullable|string|max:50|exists:tb_agents,matricule',
            'commune' => 'required|string|max:100',
            'date_debut' => 'nullable|date|before_or_equal:today',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'etat_affectation' => 'nullable|string|in:ACTIF,INACTIF,TERMINE,EXPIRE',
            'motif' => 'nullable|string|max:255',
        ]);

        try {
            $updated = DB::transaction(function () use ($validated, $code_zone): bool {
                $this->expireObsoleteAffectations();

                $zone = Zone::where('code_zone', $code_zone)->lockForUpdate()->first();
                if (! $zone) {
                    abort(404, 'Zone introuvable.');
                }

                $activeAffectation = ZoneAffectation::where('code_zone', $zone->code_zone)
                    ->actives()
                    ->lockForUpdate()
                    ->latest('date_debut')
                    ->first();

                $latestAffectation = ZoneAffectation::where('code_zone', $zone->code_zone)
                    ->lockForUpdate()
                    ->latest('date_debut')
                    ->latest('id')
                    ->first();

                $metaChanged = ($zone->nom !== $validated['nom']) || ($zone->commune !== $validated['commune']);

                if ($metaChanged) {
                    $zone->fill([
                        'nom' => $validated['nom'],
                        'commune' => $validated['commune'],
                    ]);
                    $zone->save();
                }

                $assignmentChanged = $this->applyZoneAffectationUpdate($zone, $activeAffectation, $latestAffectation, $validated);

                $this->synchronizeLegacyZoneAgent($zone);

                return $metaChanged || $assignmentChanged;
            });

            $message = $updated
                ? 'Zone mise à jour avec succès.'
                : 'Aucun changement détecté.';

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect()->route('administration.zones.index')->with('success', $message);
        } catch (\Throwable $e) {
            Log::error('Erreur modification zone : '.$e->getMessage(), ['code_zone' => $code_zone]);

            if ($request->ajax() || $request->expectsJson()) {
                $status = $e->getCode() === 404 ? 404 : 500;
                return response()->json(['success' => false, 'message' => $e->getMessage()], $status);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Retourne les zones en JSON pour DataTable AJAX
    public function data()
    {
        $zones = Zone::all();
        return response()->json(['data' => $zones]);
    }

    // Supprime une zone
    public function destroy($code_zone)
    {
        $zone = Zone::find($code_zone);
        if (! $zone) {
            return response()->json(['success' => false, 'message' => 'Zone introuvable.'], 404);
        }
        try {
            DB::transaction(function () use ($zone): void {
                $this->expireObsoleteAffectations();

                ZoneAffectation::where('code_zone', $zone->code_zone)
                    ->actives()
                    ->update([
                        'date_fin' => now()->toDateString(),
                        'Etat' => 'TERMINE',
                        'motif' => DB::raw("COALESCE(motif, 'Suppression de la zone')"),
                        'updated_at' => now(),
                    ]);

                $zone->delete();
            });

            return response()->json(['success' => true, 'message' => 'Zone supprimée avec succès.']);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Erreur suppression zone : '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer la zone car elle est liée à des clients. Veuillez d'abord supprimer ou réaffecter les clients de cette zone.",
            ], 409);
        }
    }

    private function applyZoneAffectationUpdate(Zone $zone, ?ZoneAffectation $activeAffectation, ?ZoneAffectation $latestAffectation, array $validated): bool
    {
        $incomingAgent = $validated['agent_commercial_matricule'] ?? null;
        $editableAffectation = $activeAffectation ?: $latestAffectation;

        if (empty($incomingAgent)) {
            if (! $activeAffectation) {
                return false;
            }

            $payload = [
                'date_debut' => $validated['date_debut'] ?? optional($activeAffectation->date_debut)->toDateString() ?? now()->toDateString(),
                'date_fin' => $validated['date_fin'] ?? now()->toDateString(),
                'Etat' => $this->normalizeEtatAffectation(
                    $validated['etat_affectation'] ?? 'INACTIF',
                    $validated['date_fin'] ?? now()->toDateString()
                ),
                'motif' => $validated['motif'] ?? $activeAffectation->motif,
            ];

            return $this->updateZoneAffectationIfNeeded($activeAffectation, $payload);
        }

        $payload = $this->buildZoneAffectationPayload(
            agentMatricule: $incomingAgent,
            dateDebut: $validated['date_debut'] ?? optional($editableAffectation?->date_debut)->toDateString(),
            dateFin: $validated['date_fin'] ?? optional($editableAffectation?->date_fin)->toDateString(),
            etat: $validated['etat_affectation'] ?? $editableAffectation?->Etat,
            motif: $validated['motif'] ?? $editableAffectation?->motif,
            defaultMotif: $editableAffectation ? 'Mise à jour affectation zone' : 'Affectation zone'
        );

        if ($editableAffectation && $editableAffectation->agent_matricule === $incomingAgent) {
            return $this->updateZoneAffectationIfNeeded($editableAffectation, $payload);
        }

        if ($activeAffectation) {
            $this->updateZoneAffectationIfNeeded($activeAffectation, [
                'date_debut' => optional($activeAffectation->date_debut)->toDateString() ?? now()->toDateString(),
                'date_fin' => $payload['date_debut'],
                'Etat' => 'TERMINE',
                'motif' => $validated['motif'] ?? $activeAffectation->motif,
            ]);
        }

        ZoneAffectation::create(array_merge(['code_zone' => $zone->code_zone], $payload));

        return true;
    }

    private function buildZoneAffectationPayload(
        string $agentMatricule,
        ?string $dateDebut,
        ?string $dateFin,
        ?string $etat,
        ?string $motif,
        string $defaultMotif
    ): array {
        $resolvedDateDebut = $dateDebut ?: now()->toDateString();
        $resolvedDateFin = $dateFin ?: null;

        return [
            'agent_matricule' => $agentMatricule,
            'date_debut' => $resolvedDateDebut,
            'date_fin' => $resolvedDateFin,
            'Etat' => $this->normalizeEtatAffectation($etat, $resolvedDateFin),
            'motif' => $motif ?: $defaultMotif,
            'effectue_par_user_id' => Auth::id(),
        ];
    }

    private function updateZoneAffectationIfNeeded(ZoneAffectation $affectation, array $payload): bool
    {
        $currentDateDebut = optional($affectation->date_debut)->toDateString();
        $currentDateFin = optional($affectation->date_fin)->toDateString();
        $currentEtat = strtoupper((string) $affectation->Etat);
        $incomingEtat = strtoupper((string) $payload['Etat']);
        $currentMotif = $affectation->motif ?? null;
        $incomingMotif = $payload['motif'] ?? null;

        if (
            $currentDateDebut === $payload['date_debut']
            && $currentDateFin === ($payload['date_fin'] ?? null)
            && $currentEtat === $incomingEtat
            && $currentMotif === $incomingMotif
        ) {
            return false;
        }

        $affectation->update([
            'date_debut' => $payload['date_debut'],
            'date_fin' => $payload['date_fin'] ?? null,
            'Etat' => $incomingEtat,
            'motif' => $incomingMotif,
            'effectue_par_user_id' => Auth::id(),
        ]);

        return true;
    }

    private function normalizeEtatAffectation(?string $etat, ?string $dateFin): string
    {
        $resolvedEtat = strtoupper((string) ($etat ?: 'ACTIF'));

        if ($resolvedEtat !== 'ACTIF') {
            return $resolvedEtat;
        }

        if ($dateFin && $dateFin <= now()->toDateString()) {
            return 'EXPIRE';
        }

        return 'ACTIF';
    }

    private function synchronizeLegacyZoneAgent(Zone $zone): void
    {
        $activeAgent = ZoneAffectation::where('code_zone', $zone->code_zone)
            ->actives()
            ->latest('date_debut')
            ->value('agent_matricule');

        if ($zone->agent_commercial_matricule !== $activeAgent) {
            $zone->forceFill(['agent_commercial_matricule' => $activeAgent])->save();
        }
    }

    private function expireObsoleteAffectations(): void
    {
        $today = now()->toDateString();

        ZoneAffectation::whereRaw('UPPER(Etat) = ?', ['ACTIF'])
            ->whereNotNull('date_fin')
            ->whereDate('date_fin', '<=', $today)
            ->update([
                'Etat' => 'EXPIRE',
                'updated_at' => now(),
            ]);

        PortefeuilleAffectation::whereRaw('UPPER(Etat) = ?', ['ACTIF'])
            ->whereNotNull('date_fin')
            ->whereDate('date_fin', '<=', $today)
            ->update([
                'Etat' => 'EXPIRE',
                'updated_at' => now(),
            ]);
    }
}
