<?php

namespace App\Http\Controllers\Comptabilite;

use App\Http\Controllers\Controller;
use App\Models\Comptabilite\CategorieDepense;
use Illuminate\Http\Request;

class CategorieDepenseController extends Controller
{
    public function index()
    {
        $categories = CategorieDepense::with('compteCharge')->orderBy('libelle')->get();
        $comptesEligibles = CategorieDepense::comptesEligibles();

        return view('comptabilite.categories_depenses', compact('categories', 'comptesEligibles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle'               => 'required|string|max:150',
            'numero_compte_charge'  => 'required|exists:tb_plan_comptable,numero_compte',
        ]);

        // Sécurité : le compte choisi doit être un compte de charge mouvementable
        $compteValide = CategorieDepense::comptesEligibles()->pluck('numero_compte')->contains($validated['numero_compte_charge']);
        if (!$compteValide) {
            return back()->withErrors(['numero_compte_charge' => 'Ce compte n\'est pas un compte de charge mouvementable (classe 6).']);
        }

        $categorie = CategorieDepense::create($validated + ['est_actif' => true]);

        \App\Models\ActivityLog::record(
            'COMPTABILITE',
            'CATEGORIE_DEPENSE_CREEE',
            $categorie,
            $categorie->libelle,
            "Création catégorie de dépense « {$categorie->libelle} » → compte {$categorie->numero_compte_charge}"
        );

        return redirect()->route('comptabilite.categories-depenses.index')->with('success', 'Catégorie créée avec succès.');
    }

    public function update(Request $request, CategorieDepense $categorieDepense)
    {
        $validated = $request->validate([
            'libelle'               => 'required|string|max:150',
            'numero_compte_charge'  => 'required|exists:tb_plan_comptable,numero_compte',
            'est_actif'             => 'nullable|boolean',
        ]);

        $compteValide = CategorieDepense::comptesEligibles()->pluck('numero_compte')->contains($validated['numero_compte_charge']);
        if (!$compteValide) {
            return back()->withErrors(['numero_compte_charge' => 'Ce compte n\'est pas un compte de charge mouvementable (classe 6).']);
        }

        $ancienCompte = $categorieDepense->numero_compte_charge;
        $validated['est_actif'] = $request->has('est_actif');
        $categorieDepense->update($validated);

        // Traçabilité obligatoire : un changement de mapping affecte la comptabilité future
        \App\Models\ActivityLog::record(
            'COMPTABILITE',
            'CATEGORIE_DEPENSE_MODIFIEE',
            $categorieDepense,
            $categorieDepense->libelle,
            "Modification catégorie « {$categorieDepense->libelle} » : compte {$ancienCompte} → {$categorieDepense->numero_compte_charge}"
        );

        return redirect()->route('comptabilite.categories-depenses.index')->with('success', 'Catégorie modifiée avec succès.');
    }

    public function destroy(CategorieDepense $categorieDepense)
    {
        if ($categorieDepense->depenses()->exists()) {
            return back()->withErrors(['error' => 'Impossible de supprimer : des dépenses existent déjà dans cette catégorie. Désactivez-la plutôt.']);
        }

        $libelle = $categorieDepense->libelle;
        $categorieDepense->delete();

        \App\Models\ActivityLog::record(
            'COMPTABILITE',
            'CATEGORIE_DEPENSE_SUPPRIMEE',
            null,
            $libelle,
            "Suppression catégorie de dépense « {$libelle} »"
        );

        return redirect()->route('comptabilite.categories-depenses.index')->with('success', 'Catégorie supprimée avec succès.');
    }
}
