<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Caisse\CaissesGuichet;
use App\Models\Caisse\Depense;
use App\Models\Caisse\Recette;
use App\Models\Comptabilite\CategorieDepense;
use App\Models\Comptabilite\CategorieRecette;
use App\Models\RH\Affectation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationAdministrativeController extends Controller
{
    private function getGuichetAgent(): ?CaissesGuichet
    {
        $matricule = Auth::user()?->agent_matricule;
        if (!$matricule) {
            return null;
        }

        $affectation = Affectation::where('agent_matricule', $matricule)
            ->where('Etat', 'ACTIF')
            ->whereNotNull('guichet_id')
            ->orderByDesc('date_debut')
            ->with('guichet')
            ->first();

        return $affectation?->guichet;
    }

    public function index(Request $request)
    {
        $guichet = $this->getGuichetAgent();

        $depenses = Depense::with(['transaction', 'categorie', 'agent'])
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'page_depenses')
            ->withQueryString();

        $recettes = Recette::with(['transaction', 'categorie', 'agent'])
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'page_recettes')
            ->withQueryString();

        $categoriesDepenses = CategorieDepense::where('est_actif', true)->orderBy('libelle')->get();
        $categoriesRecettes = CategorieRecette::where('est_actif', true)->orderBy('libelle')->get();

        return view('Caisse_Guichet.operations_administratives', compact(
            'guichet', 'depenses', 'recettes', 'categoriesDepenses', 'categoriesRecettes'
        ));
    }
}
