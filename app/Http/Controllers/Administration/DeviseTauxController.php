<?php
namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Devise;
use App\Models\TauxEchange;

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
            return redirect()->route('administration.devises_taux.index')->with('success', 'Devise supprimée !');
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
            return redirect()->route('administration.devises_taux.index')->with('error', $e->getMessage());
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
            return redirect()->route('administration.devises_taux.index')->with('success', 'Taux supprimé !');
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
            return redirect()->route('administration.devises_taux.index')->with('error', $e->getMessage());
        }
    }


    public function index()
    {
        $devises = Devise::orderBy('code_iso')->get();
        $taux    = TauxEchange::orderBy('date_application', 'desc')->get();

        $stats = [
            'total_devises'    => $devises->count(),
            'devise_reference' => $devises->firstWhere('est_reference', true)?->code_iso ?? '—',
            'total_taux'       => $taux->count(),
            'dernier_taux'     => $taux->first()?->date_application ?? '—',
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
        return redirect()->route('administration.devises_taux.index')->with('success', 'Devise ajoutée !');
    }

    public function storeTaux(Request $request)
    {
        $request->validate([
            'devise_source'      => 'required|string|exists:tb_devises,code_iso',
            'devise_destination' => 'required|string|exists:tb_devises,code_iso',
            'taux'               => 'required|numeric',
        ]);

        try {
            TauxEchange::create([
                'devise_source'      => $request->devise_source,
                'devise_destination' => $request->devise_destination,
                'taux'               => $request->taux,
            ]);
            if ($request->taux > 0 && $request->devise_source !== $request->devise_destination) {
                TauxEchange::create([
                    'devise_source'      => $request->devise_destination,
                    'devise_destination' => $request->devise_source,
                    'taux'               => round(1 / $request->taux, 4),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erreur ajout taux', ['error' => $e->getMessage()]);
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Taux ajouté (et inverse si applicable) !']);
        }
        return redirect()->route('administration.devises_taux.index')->with('success', 'Taux ajouté (et inverse si applicable) !');
    }
}
