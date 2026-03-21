<?php
namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PortefeuilleAffectation;
use App\Models\Tresorerie\Portefeuille;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PortefeuilleController extends Controller
{
    // Redirige vers la page unifiée Zones + Portefeuilles (onglet Portefeuilles)
    public function index()
    {
        return redirect()->route('administration.zones.index')->with('_tab', 'portefeuilles');
    }

    // Supprimer un portefeuille d'agent
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id): void {
                $this->expireObsoleteAffectations();

                $portefeuille = Portefeuille::with('affectationActive')->lockForUpdate()->findOrFail($id);

                PortefeuilleAffectation::where('portefeuille_id', $portefeuille->id)
                    ->actives()
                    ->update([
                        'date_fin' => now()->toDateString(),
                        'Etat' => 'TERMINE',
                        'motif' => DB::raw("COALESCE(motif, 'Suppression de portefeuille')"),
                        'updated_at' => now(),
                    ]);

                $portefeuille->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Le portefeuille a été supprimé avec succès.'
            ]);
        } catch (\Exception $e) {
            Log::warning('Suppression portefeuille refusée', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer ce portefeuille car il est lié à d\'autres données.'
            ], 422);
        }
    }


    // Enregistre un portefeuille d'agent
    public function store(Request $request)
    {
        $request->validate([
            'nom_portefeuille' => 'required|string|max:100',
            'agent_matricule' => 'nullable|string|max:50|exists:tb_agents,matricule',
            'taux_commission_agent' => 'required|numeric|min:0',
            'date_debut' => 'nullable|date|before_or_equal:today',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'etat_affectation' => 'nullable|string|in:ACTIF,INACTIF,TERMINE,EXPIRE',
            'motif' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request): void {
            $this->expireObsoleteAffectations();

            $portefeuille = Portefeuille::create([
                'nom_portefeuille' => $request->input('nom_portefeuille'),
                'agent_matricule' => null,
                'taux_commission_agent' => $request->input('taux_commission_agent'),
            ]);

            if ($request->filled('agent_matricule')) {
                PortefeuilleAffectation::create(array_merge(
                    ['portefeuille_id' => $portefeuille->id],
                    $this->buildPortefeuilleAffectationPayload(
                        agentMatricule: $request->input('agent_matricule'),
                        dateDebut: $request->input('date_debut'),
                        dateFin: $request->input('date_fin'),
                        etat: $request->input('etat_affectation'),
                        motif: $request->input('motif'),
                        defaultMotif: 'Création portefeuille'
                    )
                ));
            }

            $this->synchronizeLegacyPortefeuilleAgent($portefeuille);
        });

        return response()->json(['success' => true, 'message' => 'Portefeuille ajouté avec succès !']);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'nom_portefeuille' => 'required|string|max:100',
            'agent_matricule' => 'nullable|string|max:50|exists:tb_agents,matricule',
            'taux_commission_agent' => 'required|numeric|min:0',
            'date_debut' => 'nullable|date|before_or_equal:today',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'etat_affectation' => 'nullable|string|in:ACTIF,INACTIF,TERMINE,EXPIRE',
            'motif' => 'nullable|string|max:255',
        ]);

        try {
            $updated = DB::transaction(function () use ($validated, $id): bool {
                $this->expireObsoleteAffectations();

                $portefeuille = Portefeuille::with('affectationActive')
                    ->lockForUpdate()
                    ->findOrFail($id);

                $activeAffectation = PortefeuilleAffectation::where('portefeuille_id', $portefeuille->id)
                    ->actives()
                    ->lockForUpdate()
                    ->latest('date_debut')
                    ->first();

                $latestAffectation = PortefeuilleAffectation::where('portefeuille_id', $portefeuille->id)
                    ->lockForUpdate()
                    ->latest('date_debut')
                    ->latest('id')
                    ->first();

                $metaChanged =
                    $portefeuille->nom_portefeuille !== $validated['nom_portefeuille'] ||
                    (float) $portefeuille->taux_commission_agent !== (float) $validated['taux_commission_agent'];

                if ($metaChanged) {
                    $portefeuille->update([
                        'nom_portefeuille' => $validated['nom_portefeuille'],
                        'taux_commission_agent' => $validated['taux_commission_agent'],
                    ]);
                }

                $assignmentChanged = $this->applyPortefeuilleAffectationUpdate($portefeuille, $activeAffectation, $latestAffectation, $validated);

                $this->synchronizeLegacyPortefeuilleAgent($portefeuille);

                return $metaChanged || $assignmentChanged;
            });

            return response()->json([
                'success' => true,
                'message' => $updated ? 'Portefeuille mis à jour avec succès.' : 'Aucun changement détecté.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Erreur update portefeuille', ['id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function applyPortefeuilleAffectationUpdate(Portefeuille $portefeuille, ?PortefeuilleAffectation $activeAffectation, ?PortefeuilleAffectation $latestAffectation, array $validated): bool
    {
        $incomingAgent = $validated['agent_matricule'] ?? null;
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

            return $this->updatePortefeuilleAffectationIfNeeded($activeAffectation, $payload);
        }

        $payload = $this->buildPortefeuilleAffectationPayload(
            agentMatricule: $incomingAgent,
            dateDebut: $validated['date_debut'] ?? optional($editableAffectation?->date_debut)->toDateString(),
            dateFin: $validated['date_fin'] ?? optional($editableAffectation?->date_fin)->toDateString(),
            etat: $validated['etat_affectation'] ?? $editableAffectation?->Etat,
            motif: $validated['motif'] ?? $editableAffectation?->motif,
            defaultMotif: $editableAffectation ? 'Mise à jour affectation portefeuille' : 'Affectation portefeuille'
        );

        if ($editableAffectation && $editableAffectation->agent_matricule === $incomingAgent) {
            return $this->updatePortefeuilleAffectationIfNeeded($editableAffectation, $payload);
        }

        if ($activeAffectation) {
            $this->updatePortefeuilleAffectationIfNeeded($activeAffectation, [
                'date_debut' => optional($activeAffectation->date_debut)->toDateString() ?? now()->toDateString(),
                'date_fin' => $payload['date_debut'],
                'Etat' => 'TERMINE',
                'motif' => $validated['motif'] ?? $activeAffectation->motif,
            ]);
        }

        PortefeuilleAffectation::create(array_merge(['portefeuille_id' => $portefeuille->id], $payload));

        return true;
    }

    private function buildPortefeuilleAffectationPayload(
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

    private function updatePortefeuilleAffectationIfNeeded(PortefeuilleAffectation $affectation, array $payload): bool
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

    private function synchronizeLegacyPortefeuilleAgent(Portefeuille $portefeuille): void
    {
        $activeAgent = PortefeuilleAffectation::where('portefeuille_id', $portefeuille->id)
            ->actives()
            ->latest('date_debut')
            ->value('agent_matricule');

        if ($portefeuille->agent_matricule !== $activeAgent) {
            $portefeuille->forceFill(['agent_matricule' => $activeAgent])->save();
        }
    }

    private function expireObsoleteAffectations(): void
    {
        $today = now()->toDateString();

        PortefeuilleAffectation::whereRaw('UPPER(Etat) = ?', ['ACTIF'])
            ->whereNotNull('date_fin')
            ->whereDate('date_fin', '<=', $today)
            ->update([
                'Etat' => 'EXPIRE',
                'updated_at' => now(),
            ]);
    }
}
