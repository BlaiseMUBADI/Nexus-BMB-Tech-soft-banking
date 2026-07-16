<?php

namespace App\Http\Controllers\Credit;

use App\Http\Controllers\Controller;
use App\Models\Credit\CreditCommissionRule;
use App\Models\Zone;
use App\Models\Tresorerie\Portefeuille;
use Illuminate\Http\Request;

class CreditCommissionController extends Controller
{
    public function index(Request $request)
    {
        $query = CreditCommissionRule::with(['zone', 'portefeuille']);

        if ($request->filled('devise')) {
            $query->where('devise_code', $request->devise);
        }
        if ($request->filled('type_credit') && $request->type_credit !== 'TOUS') {
            $query->where('type_credit', $request->type_credit);
        }
        if ($request->filled('est_actif')) {
            $query->where('est_actif', $request->est_actif === '1');
        }

        $rules = $query->orderByDesc('priorite')->orderBy('devise_code')->paginate(20);

        $zones = Zone::orderBy('nom')->get();
        $portefeuilles = Portefeuille::orderBy('nom_portefeuille')->get();

        $stats = [
            'total' => CreditCommissionRule::count(),
            'actives' => CreditCommissionRule::where('est_actif', true)->count(),
            'fixes' => CreditCommissionRule::where('mode_calcul', 'FIXE')->count(),
            'pourcentages' => CreditCommissionRule::where('mode_calcul', 'POURCENTAGE')->count(),
        ];

        return view('credit.commissions', compact('rules', 'zones', 'portefeuilles', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:200',
            'devise_code' => 'required|in:CDF,USD,EUR',
            'type_credit' => 'required|in:INDIVIDUEL,SOLIDAIRE,PME,TOUS',
            'code_zone' => 'nullable|exists:tb_zones,code_zone',
            'portefeuille_id' => 'nullable|exists:tb_portefeuilles_agents,id',
            'montant_min' => 'nullable|numeric|min:0',
            'montant_max' => 'nullable|numeric|min:0',
            'mode_calcul' => 'required|in:FIXE,POURCENTAGE',
            'valeur' => 'required|numeric|min:0',
            'priorite' => 'nullable|integer|min:0',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'est_actif' => 'nullable|boolean',
            'observations' => 'nullable|string',
        ]);

        $validated['est_actif'] = $request->has('est_actif');

        CreditCommissionRule::create($validated);

        return redirect()->route('credit.commissions.index')
            ->with('success', 'Règle de commission crédit créée avec succès.');
    }

    public function update(Request $request, CreditCommissionRule $rule)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:200',
            'devise_code' => 'required|in:CDF,USD,EUR',
            'type_credit' => 'required|in:INDIVIDUEL,SOLIDAIRE,PME,TOUS',
            'code_zone' => 'nullable|exists:tb_zones,code_zone',
            'portefeuille_id' => 'nullable|exists:tb_portefeuilles_agents,id',
            'montant_min' => 'nullable|numeric|min:0',
            'montant_max' => 'nullable|numeric|min:0',
            'mode_calcul' => 'required|in:FIXE,POURCENTAGE',
            'valeur' => 'required|numeric|min:0',
            'priorite' => 'nullable|integer|min:0',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'est_actif' => 'nullable|boolean',
            'observations' => 'nullable|string',
        ]);

        $validated['est_actif'] = $request->has('est_actif');

        $rule->update($validated);

        return redirect()->route('credit.commissions.index')
            ->with('success', 'Règle de commission crédit mise à jour.');
    }

    public function destroy(CreditCommissionRule $rule)
    {
        $rule->delete();

        return redirect()->route('credit.commissions.index')
            ->with('success', 'Règle de commission crédit supprimée.');
    }
}
