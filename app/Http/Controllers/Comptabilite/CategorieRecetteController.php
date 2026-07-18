<?php

namespace App\Http\Controllers\Comptabilite;

use App\Http\Controllers\Controller;
use App\Models\Comptabilite\CategorieRecette;
use Illuminate\Http\Request;

class CategorieRecetteController extends Controller
{
    public function index()
    {
        $categories = CategorieRecette::with('compteProduit')->orderBy('libelle')->get();
        $comptesEligibles = CategorieRecette::comptesEligibles();

        return view('comptabilite.categories_recettes', compact('categories', 'comptesEligibles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle'                => 'required|string|max:150',
            'numero_compte_produit'  => 'required|exists:tb_plan_comptable,numero_compte',
        ]);

        $compteValide = CategorieRecette::comptesEligibles()->pluck('numero_compte')->contains($validated['numero_compte_produit']);
        if (!$compteValide) {
            return back()->withErrors(['numero_compte_produit' => 'Ce compte n\'est pas un compte de produit mouvementable (classe 7).']);
        }

        $categorie = CategorieRecette::create($validated + ['est_actif' => true]);

        \App\Models\ActivityLog::record(
            'COMPTABILITE',
            'CATEGORIE_RECETTE_CREEE',
            $categorie,
            $categorie->libelle,
            "Création catégorie de recette « {$categorie->libelle} » → compte {$categorie->numero_compte_produit}"
        );

        return redirect()->route('comptabilite.categories-recettes.index')->with('success', 'Catégorie créée avec succès.');
    }

    public function update(Request $request, CategorieRecette $categorieRecette)
    {
        $validated = $request->validate([
            'libelle'                => 'required|string|max:150',
            'numero_compte_produit'  => 'required|exists:tb_plan_comptable,numero_compte',
            'est_actif'               => 'nullable|boolean',
        ]);

        $compteValide = CategorieRecette::comptesEligibles()->pluck('numero_compte')->contains($validated['numero_compte_produit']);
        if (!$compteValide) {
            return back()->withErrors(['numero_compte_produit' => 'Ce compte n\'est pas un compte de produit mouvementable (classe 7).']);
        }

        $ancienCompte = $categorieRecette->numero_compte_produit;
        $validated['est_actif'] = $request->has('est_actif');
        $categorieRecette->update($validated);

        \App\Models\ActivityLog::record(
            'COMPTABILITE',
            'CATEGORIE_RECETTE_MODIFIEE',
            $categorieRecette,
            $categorieRecette->libelle,
            "Modification catégorie « {$categorieRecette->libelle} » : compte {$ancienCompte} → {$categorieRecette->numero_compte_produit}"
        );

        return redirect()->route('comptabilite.categories-recettes.index')->with('success', 'Catégorie modifiée avec succès.');
    }

    public function destroy(CategorieRecette $categorieRecette)
    {
        if ($categorieRecette->recettes()->exists()) {
            return back()->withErrors(['error' => 'Impossible de supprimer : des recettes existent déjà dans cette catégorie. Désactivez-la plutôt.']);
        }

        $libelle = $categorieRecette->libelle;
        $categorieRecette->delete();

        \App\Models\ActivityLog::record(
            'COMPTABILITE',
            'CATEGORIE_RECETTE_SUPPRIMEE',
            null,
            $libelle,
            "Suppression catégorie de recette « {$libelle} »"
        );

        return redirect()->route('comptabilite.categories-recettes.index')->with('success', 'Catégorie supprimée avec succès.');
    }
}
