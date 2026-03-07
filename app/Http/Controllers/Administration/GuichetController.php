<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaissesGuichet;
use App\Models\CaissesGuichetSolde;
use App\Models\MouvementInterCaisse;
use App\Models\Devise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuichetController extends Controller
{
    /**
     * Affiche la page de gestion des guichets avec statistiques.
     * Les soldes sont chargés via la relation ->soldes().
     * L'agent titulaire est indiqué dans tb_affectations (module RH).
     */
    public function index()
    {
        // Liste des guichets opérationnels (FIXE + MOBILE — pas le coffre)
        $guichets = CaissesGuichet::operationnels()
            ->with(['soldes', 'affectationActive'])
            ->orderBy('code_guichet')->get();

        // Coffre-fort central
        $coffre = CaissesGuichet::central()
            ->with('soldes.devise')
            ->first();

        // Devises disponibles pour créer les soldes initiaux
        $devises = Devise::orderBy('code_iso')->get();

        // ── Stats mini-dashboard (guichets opérationnels uniquement) ─────
        $stats = [
            'total'          => $guichets->count(),
            'ouverts'        => $guichets->where('statut_operationnel', 'OUVERT')->count(),
            'fermes'         => $guichets->where('statut_operationnel', 'FERME')->count(),
            'suspendus'      => $guichets->where('statut_operationnel', 'SUSPENDU')->count(),
            'avec_titulaire' => $guichets->filter(fn($g) => $g->affectationActive !== null)->count(),
            'sans_titulaire' => $guichets->filter(fn($g) => $g->affectationActive === null)->count(),
        ];

        // Soldes totaux par devise (tous guichets opérationnels confondus)
        $soldesParDevise = CaissesGuichetSolde::select('devise_code', DB::raw('SUM(solde_en_caisse) as total'))
            ->when($coffre, fn($q) => $q->where('guichet_id', '!=', $coffre->id))
            ->groupBy('devise_code')
            ->with('devise')
            ->get();

        return view('administration.guichets', compact('guichets', 'devises', 'stats', 'soldesParDevise', 'coffre'));
    }

    /**
     * Créer un nouveau guichet (architecture multi-devises).
     *
     * La nouvelle structure :
     *   - tb_caisses_guichets        → code, intitulé, statut
     *   - tb_caisses_guichets_soldes → une ligne par devise sélectionnée
     *
     * L'agent titulaire est géré dans le module RH (tb_affectations).
     */
    public function store(Request $request)
    {
        $request->validate([
            'code_guichet'  => 'required|string|max:20|unique:tb_caisses_guichets,code_guichet',
            'intitule'      => 'required|string|max:100',
            'type_guichet'  => 'required|in:FIXE,MOBILE',
            'devises'       => 'required|array|min:1',
            'devises.*'     => 'exists:tb_devises,code_iso',
        ], [
            'code_guichet.unique'  => 'Ce code guichet est déjà utilisé.',
            'type_guichet.in'      => 'Type de guichet invalide (FIXE ou MOBILE uniquement).',
            'devises.required'     => 'Sélectionnez au moins une devise pour ce guichet.',
            'devises.*.exists'     => 'Une devise sélectionnée est invalide.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $guichet = CaissesGuichet::create([
                    'code_guichet'        => strtoupper(trim($request->code_guichet)),
                    'intitule'            => $request->intitule,
                    'type_guichet'        => $request->type_guichet,
                    'statut_operationnel' => 'FERME',
                ]);
                foreach ($request->devises as $deviseCode) {
                    CaissesGuichetSolde::create([
                        'guichet_id'      => $guichet->id,
                        'devise_code'     => $deviseCode,
                        'solde_en_caisse' => 0.00,
                    ]);
                }
            });
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        $nbDevises = count($request->devises);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Guichet créé avec succès ({$nbDevises} devise(s) configurée(s)).",
            ]);
        }
        return redirect()->route('administration.guichets.index')->with('success', 'Guichet créé avec succès.');
    }

    /**
     * Ajouter une devise à un guichet existant.
     */
    public function addDevise(Request $request, $id)
    {
        $guichet = CaissesGuichet::findOrFail($id);

        $request->validate([
            'devise_code' => 'required|exists:tb_devises,code_iso',
        ]);

        // Vérifier si la devise existe déjà pour ce guichet
        $exists = CaissesGuichetSolde::where('guichet_id', $id)
            ->where('devise_code', $request->devise_code)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Ce guichet gère déjà la devise ' . $request->devise_code . '.'
            ], 422);
        }

        CaissesGuichetSolde::create([
            'guichet_id' => $id,
            'devise_code' => $request->devise_code,
            'solde_en_caisse' => 0.00,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Devise ' . $request->devise_code . ' ajoutée au guichet ' . $guichet->code_guichet . '.'
        ]);
    }

    /**
     * Supprimer un guichet (seulement si FERMÉ et soldes tous à 0).
     * Les soldes sont supprimés en cascade (ON DELETE CASCADE en base).
     */
    public function destroy($id)
    {
        $guichet = CaissesGuichet::with('soldes')->findOrFail($id);

        if ($guichet->statut_operationnel !== 'FERME') {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer un guichet ouvert ou suspendu. Fermez-le d\'abord.'
            ], 403);
        }

        // Sécurité : refuser si un solde est non nul
        $soldesNonNuls = $guichet->soldes->where('solde_en_caisse', '!=', 0);
        if ($soldesNonNuls->count() > 0) {
            $details = $soldesNonNuls->map(fn($s) => $s->devise_code . ': ' . number_format($s->solde_en_caisse, 2))->join(', ');
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer : des soldes non nuls existent ({$details}). Dégagez les fonds d'abord."
            ], 403);
        }

        try {
            DB::transaction(function () use ($guichet) {
                // fk_solde_guichet et fk_affectation_guichet sont RESTRICT — suppression manuelle
                $guichet->soldes()->delete();
                \App\Models\Affectation::where('guichet_id', $guichet->id)->update(['guichet_id' => null]);
                $guichet->delete();
            });
            return response()->json(['success' => true, 'message' => 'Guichet supprimé avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression : ' . $e->getMessage()], 500);
        }
    }

    
}
