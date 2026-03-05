<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaissesGuichet;
use App\Models\CaissesGuichetSolde;
use App\Models\Devise;
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
        // Liste complète des guichets avec leurs soldes multi-devises
        $guichets = CaissesGuichet::with('soldes')->orderBy('code_guichet')->get();

        // Devises disponibles pour créer les soldes initiaux
        $devises  = Devise::orderBy('code_iso')->get();

        // ── Statistiques pour le mini-dashboard ─────────────────

        $stats = [
            'total'     => $guichets->count(),
            'ouverts'   => $guichets->where('statut_operationnel', 'OUVERT')->count(),
            'fermes'    => $guichets->where('statut_operationnel', 'FERME')->count(),
            'suspendus' => $guichets->where('statut_operationnel', 'SUSPENDU')->count(),
        ];

        // Soldes totaux par devise (somme tous guichets confondus)
        $soldesParDevise = CaissesGuichetSolde::select('devise_code', DB::raw('SUM(solde_en_caisse) as total'))
            ->groupBy('devise_code')
            ->with('devise')
            ->get();

        return view('administration.guichets', compact('guichets', 'devises', 'stats', 'soldesParDevise'));
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
            'code_guichet' => 'required|string|max:20|unique:tb_caisses_guichets,code_guichet',
            'intitule'     => 'required|string|max:100',
            'devises'      => 'required|array|min:1',
            'devises.*'    => 'exists:tb_devises,code_iso',
        ], [
            'code_guichet.unique' => 'Ce code guichet est déjà utilisé.',
            'devises.required'    => 'Sélectionnez au moins une devise pour ce guichet.',
            'devises.*.exists'    => 'Une devise sélectionnée est invalide.',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Créer le guichet (table mère)
            $guichet = CaissesGuichet::create([
                'code_guichet'        => strtoupper(trim($request->code_guichet)),
                'intitule'            => $request->intitule,
                'statut_operationnel' => 'FERME',
                'created_at'          => now(),
            ]);

            // 2. Créer une ligne de solde pour chaque devise sélectionnée
            foreach ($request->devises as $deviseCode) {
                CaissesGuichetSolde::create([
                    'guichet_id'      => $guichet->id,
                    'devise_code'     => $deviseCode,
                    'solde_en_caisse' => 0.00,
                ]);
            }
        });

        $nbDevises = count($request->devises);
        return response()->json([
            'message' => "Guichet créé avec succès ({$nbDevises} devise(s) configurée(s))."
        ], 201);
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
                'message' => 'Ce guichet gère déjà la devise ' . $request->devise_code . '.'
            ], 422);
        }

        CaissesGuichetSolde::create([
            'guichet_id'      => $id,
            'devise_code'     => $request->devise_code,
            'solde_en_caisse' => 0.00,
        ]);

        return response()->json([
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
                'message' => 'Impossible de supprimer un guichet ouvert ou suspendu. Fermez-le d\'abord.'
            ], 403);
        }

        // Sécurité : refuser si un solde est non nul
        $soldesNonNuls = $guichet->soldes->where('solde_en_caisse', '!=', 0);
        if ($soldesNonNuls->count() > 0) {
            $details = $soldesNonNuls->map(fn($s) => $s->devise_code . ': ' . number_format($s->solde_en_caisse, 2))->join(', ');
            return response()->json([
                'message' => "Impossible de supprimer : des soldes non nuls existent ({$details}). Dégagez les fonds d'abord."
            ], 403);
        }

        $guichet->delete(); // Les soldes sont supprimés en cascade
        return response()->json(['message' => 'Guichet supprimé avec succès.']);
    }
}
