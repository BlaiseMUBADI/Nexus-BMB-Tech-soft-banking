<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RH\Affectation;
use App\Models\RH\Agent;
use App\Models\RH\Poste;
use App\Models\Caisse\CaissesGuichet;
use App\Models\Caisse\CaissesGuichetSolde;
use App\Models\Tresorerie\Portefeuille;

class AffectationController extends Controller
{
    public function index(Request $request)
    {
        $postes       = Poste::with('service')->get();
        $agents       = Agent::all();
        $guichets     = CaissesGuichet::orderBy('code_guichet')->get();
        $affectations = Affectation::with(['agent', 'poste.service', 'guichet'])->latest()->get();
        $poste_id     = $request->get('poste_id');
        $agentsByPoste = $poste_id
            ? Affectation::where('poste_id', $poste_id)->with('agent')->get()
            : collect();

        return view('rh.affectation.index',
            compact('postes', 'agents', 'guichets', 'affectations', 'agentsByPoste', 'poste_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'agent_matricule' => 'required|exists:tb_agents,matricule',
            'poste_id'        => 'required|exists:tb_postes,id',
            'date_debut'      => 'required|date',
            'guichet_id'      => 'nullable|exists:tb_caisses_guichets,id',
        ]);

        try {
            $data = $request->only('agent_matricule', 'poste_id', 'guichet_id', 'date_debut', 'date_fin');
            $data['Etat'] = 'ACTIF';
            Affectation::create($data);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Affectation enregistrée avec succès.']);
        }
        return redirect()->route('affectations.index')->with('success', 'Affectation enregistrée !');
    }

    /**
     * Retourne les détails JSON d'une affectation (pour modal "Voir").
     */
    public function show(Affectation $affectation)
    {
        $affectation->load(['agent', 'poste.service', 'guichet']);

        return response()->json([
            'id'              => $affectation->id,
            'agent_matricule' => $affectation->agent_matricule,
            'agent_nom'       => $affectation->agent
                                    ? $affectation->agent->nom . ' ' . $affectation->agent->postnom . ' ' . $affectation->agent->prenom
                                    : '—',
            'poste'           => $affectation->poste?->nom ?? '—',
            'service'         => $affectation->poste?->service?->nom ?? '—',
            'guichet'         => $affectation->guichet?->code_guichet ?? null,
            'guichet_intitule'=> $affectation->guichet?->intitule ?? null,
            'date_debut'      => $affectation->date_debut,
            'date_fin'        => $affectation->date_fin,
            'etat'            => $affectation->Etat,
            'can_delete'      => $this->canDelete($affectation),
            'delete_reason'   => $this->deleteBlockReason($affectation),
        ]);
    }

    /**
     * Met à jour uniquement l'état (ACTIF / SUSPENDU / TERMINE)
     * et la date de fin si on passe à TERMINE.
     */
    public function updateEtat(Request $request, Affectation $affectation)
    {
        $request->validate([
            'etat'     => 'required|in:ACTIF,SUSPENDU,TERMINE',
            'date_fin' => 'nullable|date',
        ]);

        $affectation->Etat = $request->etat;
        if ($request->etat === 'TERMINE' && $request->date_fin) {
            $affectation->date_fin = $request->date_fin;
        }

        try {
            $affectation->save();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success'  => true,
            'message'  => 'État mis à jour : ' . $request->etat,
            'etat'     => $affectation->Etat,
            'date_fin' => $affectation->date_fin,
        ]);
    }

    /**
     * Supprime une affectation UNIQUEMENT si elle n'a aucune action liée.
     */
    public function destroy(Affectation $affectation)
    {
        if (!$this->canDelete($affectation)) {
            return response()->json([
                'success' => false,
                'message' => $this->deleteBlockReason($affectation),
            ], 422);
        }

        try {
            $affectation->delete();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'message' => 'Affectation supprimée avec succès.']);
    }

    // ── Helpers privés ──────────────────────────────────────────────

    private function canDelete(Affectation $affectation): bool
    {
        // Blocage 1 : affectation encore active
        if ($affectation->Etat === 'ACTIF') return false;

        // Blocage 2 : guichet associé possède des soldes (agent a manipulé de la caisse)
        if ($affectation->guichet_id) {
            $hasSoldes = CaissesGuichetSolde::where('guichet_id', $affectation->guichet_id)
                            ->where('solde_en_caisse', '>', 0)
                            ->exists();
            if ($hasSoldes) return false;
        }

        // Blocage 3 : agent possède des portefeuilles liés
        $hasPortefeuille = Portefeuille::where('agent_matricule', $affectation->agent_matricule)->exists();
        if ($hasPortefeuille) return false;

        return true;
    }

    private function deleteBlockReason(Affectation $affectation): string
    {
        if ($affectation->Etat === 'ACTIF') {
            return 'Impossible de supprimer une affectation active. Passez-la en SUSPENDU ou TERMINÉ d\'abord.';
        }
        if ($affectation->guichet_id) {
            $hasSoldes = CaissesGuichetSolde::where('guichet_id', $affectation->guichet_id)
                            ->where('solde_en_caisse', '>', 0)->exists();
            if ($hasSoldes) {
                return 'Ce guichet possède encore des soldes en caisse. Clôturez la caisse avant de supprimer.';
            }
        }
        $hasPortefeuille = Portefeuille::where('agent_matricule', $affectation->agent_matricule)->exists();
        if ($hasPortefeuille) {
            return 'Cet agent possède des portefeuilles liés. Supprimez-les d\'abord.';
        }
        return 'Suppression autorisée.';
    }
}

