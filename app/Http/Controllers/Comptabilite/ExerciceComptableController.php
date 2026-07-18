<?php

namespace App\Http\Controllers\Comptabilite;

use App\Http\Controllers\Controller;
use App\Models\Comptabilite\ExerciceComptable;
use App\Services\Comptabilite\ExerciceComptableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExerciceComptableController extends Controller
{
    public function index()
    {
        $exercices = ExerciceComptable::with('soldesOuverture.compte')->orderByDesc('annee')->get();

        // Résolution des noms d'agents pour affichage (matricule -> nom complet)
        $matricules = $exercices->flatMap(fn ($e) => [$e->propose_par_matricule, $e->valide_par_matricule, $e->rejete_par_matricule])
            ->filter()->unique();
        $agents = \App\Models\RH\Agent::whereIn('matricule', $matricules)->get()->keyBy('matricule');

        return view('comptabilite.exercices', compact('exercices', 'agents'));
    }

    public function proposerCloture(Request $request, ExerciceComptable $exercice, ExerciceComptableService $service)
    {
        $request->validate(['date_cloture_effective' => 'nullable|date']);

        try {
            $service->proposerCloture($exercice, Auth::user()->agent_matricule ?? 'INCONNU', $request->input('date_cloture_effective'));
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('comptabilite.exercices.index')
            ->with('success', "Proposition de clôture de l'exercice {$exercice->annee} envoyée. En attente de validation par un Gérant/Directeur.");
    }

    public function validerCloture(Request $request, ExerciceComptable $exercice, ExerciceComptableService $service)
    {
        $request->validate([
            'confirmation' => 'required|in:CONFIRMER',
            'date_fin_nouvel_exercice' => 'nullable|date',
        ]);

        try {
            $service->validerCloture($exercice, Auth::user()->agent_matricule ?? 'INCONNU', $request->input('date_fin_nouvel_exercice'));
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('comptabilite.exercices.index')
            ->with('success', "Exercice {$exercice->annee} clôturé définitivement au " . $exercice->date_fin->format('d/m/Y') . '. Le nouvel exercice est maintenant ouvert.');
    }

    public function rejeterCloture(Request $request, ExerciceComptable $exercice, ExerciceComptableService $service)
    {
        try {
            $service->rejeterCloture($exercice, Auth::user()->agent_matricule ?? 'INCONNU', $request->input('motif'));
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('comptabilite.exercices.index')
            ->with('success', "Proposition de clôture de l'exercice {$exercice->annee} rejetée. L'exercice reste ouvert.");
    }
}
