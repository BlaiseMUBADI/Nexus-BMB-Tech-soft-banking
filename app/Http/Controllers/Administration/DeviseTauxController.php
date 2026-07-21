<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\Tresorerie\Devise;
use App\Models\Tresorerie\TauxEchange;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeviseTauxController extends Controller
{
    public function destroyDevise(Request $request, $code_iso)
    {
        try {
            $devise = Devise::findOrFail($code_iso);

            $devise->delete();
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Devise supprimée !'
                ]);
            }
            return redirect()->route('administration.devises-taux.index')->with('success', 'Devise supprimée !');
        } catch (\Exception $e) {
            Log::error('Erreur suppression devise', [
                'code_iso' => $code_iso,
                'error' => $e->getMessage(),
                'user_id' => $request->user() ? $request->user()->id : null,
                'ip' => $request->ip()
            ]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            return redirect()->route('administration.devises-taux.index')->with('error', $e->getMessage());
        }
    }

    public function destroyTaux(Request $request, $id)
    {
        try {
            $taux = TauxEchange::findOrFail($id);
            $taux->delete();
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Taux supprimé !'
                ]);
            }
            return redirect()->route('administration.devises-taux.index')->with('success', 'Taux supprimé !');
        } catch (\Exception $e) {
            Log::error('Erreur suppression taux', [
                'id' => $id,
                'error' => $e->getMessage(),
                'user_id' => $request->user() ? $request->user()->id : null,
                'ip' => $request->ip()
            ]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            return redirect()->route('administration.devises-taux.index')->with('error', $e->getMessage());
        }
    }


    public function index()
    {
        $devises = Devise::orderBy('code_iso')->get();
        $taux    = TauxEchange::orderBy('date_debut', 'desc')->get();

        $stats = [
            'total_devises'    => $devises->count(),
            'devise_reference' => $devises->firstWhere('est_reference', true)?->code_iso ?? '—',
            'total_taux'       => $taux->count(),
            'dernier_taux'     => $taux->first()?->date_debut?->format('d/m/Y H:i') ?? '—',
        ];

        return view('administration.devises_taux', compact('devises', 'taux', 'stats'));
    }

    public function storeDevise(Request $request)
    {
        $validated = $request->validate([
            'code_iso' => 'required|string|max:3|unique:tb_devises,code_iso',
            'nom' => 'required|string|max:50',
            'symbole' => 'required|string|max:5',
            'est_reference' => 'required|boolean',
        ]);
        $devise = Devise::create($validated);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Devise ajoutée !',
                'devise' => $devise
            ]);
        }
        return redirect()->route('administration.devises-taux.index')->with('success', 'Devise ajoutée !');
    }

    /**
     * Active un nouveau taux de change pour une période donnée.
     *
     * - Clôture automatiquement (date_fin = date_debut - 1s) tout taux encore
     *   "ouvert" (date_fin NULL) existant pour la même paire de devises,
     *   afin qu'il n'y ait jamais 2 taux actifs simultanés pour une même paire.
     * - Crée systématiquement le taux inverse (1/taux) pour la paire opposée,
     *   avec la même logique de clôture.
     * - Aucune ligne existante n'est modifiée en dehors de date_fin (historique
     *   intact — le taux lui-même n'est jamais réécrit).
     */
    public function storeTaux(Request $request)
    {
        $validated = $request->validate([
            'devise_source'      => 'required|string|exists:tb_devises,code_iso|different:devise_destination',
            'devise_destination' => 'required|string|exists:tb_devises,code_iso',
            'taux'               => 'required|numeric|min:0.000001',
            'date_debut'         => 'nullable|date',
            'date_fin'           => 'nullable|date|after:date_debut',
        ], [
            'devise_source.different' => 'Les devises source et destination doivent être différentes.',
            'date_fin.after'           => 'La date de fin doit être postérieure à la date de début.',
        ]);

        // "Maintenant" par défaut est ancré sur l'horloge MySQL (voir TauxEchange::dbNow())
        // pour rester cohérent avec les comparaisons faites par TauxEchange::actif()/est_actif.
        $dateDebut = $validated['date_debut'] ? \Illuminate\Support\Carbon::parse($validated['date_debut']) : TauxEchange::dbNow();
        $dateFin   = $validated['date_fin'] ? \Illuminate\Support\Carbon::parse($validated['date_fin']) : null;

        try {
            DB::transaction(function () use ($validated, $dateDebut, $dateFin) {
                $this->activerPeriode($validated['devise_source'], $validated['devise_destination'], (float) $validated['taux'], $dateDebut, $dateFin);

                // Taux inverse automatique pour la paire opposée
                $tauxInverse = round(1 / (float) $validated['taux'], 6);
                $this->activerPeriode($validated['devise_destination'], $validated['devise_source'], $tauxInverse, $dateDebut, $dateFin);
            });
        } catch (\Exception $e) {
            Log::error('Erreur ajout taux', ['error' => $e->getMessage()]);
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        ActivityLog::record(
            'TRESORERIE',
            'TAUX_CHANGE_ACTIVE',
            null,
            $validated['devise_source'] . '->' . $validated['devise_destination'],
            "Nouveau taux actif {$validated['devise_source']} → {$validated['devise_destination']} : {$validated['taux']} (et taux inverse) à partir du " . $dateDebut->format('d/m/Y H:i')
        );

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Taux activé (et taux inverse) avec succès !']);
        }
        return redirect()->route('administration.devises-taux.index')->with('success', 'Taux activé (et taux inverse) avec succès !');
    }

    /**
     * Clôture le taux "ouvert" existant pour la paire (le cas échéant) puis
     * insère la nouvelle ligne de taux pour la période demandée.
     */
    private function activerPeriode(string $source, string $destination, float $taux, \Illuminate\Support\Carbon $dateDebut, ?\Illuminate\Support\Carbon $dateFin): void
    {
        TauxEchange::where('devise_source', $source)
            ->where('devise_destination', $destination)
            ->whereNull('date_fin')
            ->where('date_debut', '<', $dateDebut)
            ->update(['date_fin' => $dateDebut->copy()->subSecond()]);

        TauxEchange::create([
            'devise_source'      => $source,
            'devise_destination' => $destination,
            'taux'               => $taux,
            'date_debut'         => $dateDebut,
            'date_fin'           => $dateFin,
        ]);
    }

    /**
     * Endpoint public de consultation (tout utilisateur authentifié) du taux
     * ACTIF pour une paire de devises — consommé par le Change au guichet et
     * le Virement bancaire interdevises. Ne nécessite pas la permission de
     * gestion des taux (EBEN-PER20/21) : un caissier doit pouvoir lire le taux
     * actif sans avoir le droit de le modifier.
     */
    public function actif(Request $request)
    {
        $source = strtoupper(trim((string) $request->query('source', '')));
        $destination = strtoupper(trim((string) $request->query('destination', '')));

        if ($source === '' || $destination === '') {
            return response()->json(['success' => false, 'message' => 'Devises source et destination requises.'], 422);
        }

        if ($source === $destination) {
            return response()->json([
                'success' => true,
                'taux' => 1.0,
                'source' => $source,
                'destination' => $destination,
            ]);
        }

        $taux = TauxEchange::actif($source, $destination);

        if (!$taux) {
            return response()->json([
                'success' => false,
                'message' => "Aucun taux de change actif n'est défini pour {$source} → {$destination}. Contactez la Trésorerie.",
            ], 404);
        }

        return response()->json([
            'success'     => true,
            'taux'        => (float) $taux->taux,
            'source'      => $source,
            'destination' => $destination,
            'date_debut'  => $taux->date_debut?->format('d/m/Y H:i'),
            'date_fin'    => $taux->date_fin?->format('d/m/Y H:i'),
        ]);
    }
}
