<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Administration\ParametreGeneral;
use App\Services\Images\SignatureProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Paramètres généraux configurables depuis Administration — pour l'instant :
 * la signature du gérant, utilisée sur les documents/cartes imprimés
 * (jamais codée en dur, contrairement à un logo statique).
 */
class ParametresController extends Controller
{
    public function edit()
    {
        $signaturePath = ParametreGeneral::get(ParametreGeneral::SIGNATURE_GERANT);
        $signatureUrl = $signaturePath ? route('administration.parametres.signature', basename($signaturePath)) : null;

        return view('administration.parametres', [
            'signatureUrl' => $signatureUrl,
        ]);
    }

    /**
     * Enregistre la signature du gérant. Deux sources possibles :
     *  - `signature_data` : image déjà recadrée/traitée côté navigateur
     *    (éditeur canvas de la page), envoyée en data URL PNG ;
     *  - `signature` : fichier brut classique (repli si JS désactivé).
     * Dans les deux cas, un passage serveur (SignatureProcessor) retire
     * automatiquement le fond quelle que soit la couleur de l'encre ou du
     * fond d'origine — garantie même si le traitement JS a été contourné.
     */
    public function updateSignature(Request $request)
    {
        $request->validate([
            'signature'      => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'signature_data' => 'nullable|string',
            'sensibilite'    => 'nullable|integer|min:0|max:100',
        ]);

        if (empty($request->input('signature_data')) && !$request->hasFile('signature')) {
            return back()->withErrors(['signature' => 'Veuillez sélectionner ou traiter une image de signature.']);
        }

        $dir = base_path('images_projet/signatures');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ancien = ParametreGeneral::get(ParametreGeneral::SIGNATURE_GERANT);
        $sensibilite = (int) $request->input('sensibilite', 50);

        $timestamp = now()->format('YmdHis');
        $tmpSource = tempnam(sys_get_temp_dir(), 'sig_src_');
        $filename  = 'gerant-' . $timestamp . '.png';
        $destPath  = $dir . DIRECTORY_SEPARATOR . $filename;

        try {
            $dataUrl = $request->input('signature_data');
            if (!empty($dataUrl) && preg_match('/^data:image\/(png|jpeg|jpg);base64,(.+)$/', $dataUrl, $m)) {
                // Image déjà cadrée par l'éditeur JS (canvas -> data URL)
                file_put_contents($tmpSource, base64_decode($m[2]));
            } elseif ($request->hasFile('signature')) {
                // Repli fichier brut (JS désactivé/indisponible)
                copy($request->file('signature')->getRealPath(), $tmpSource);
            } else {
                throw new \RuntimeException('Aucune image de signature reçue.');
            }

            SignatureProcessor::removeBackground($tmpSource, $destPath, $sensibilite);
        } catch (\Throwable $e) {
            @unlink($tmpSource);
            return back()->withErrors(['signature' => "Impossible de traiter l'image : " . $e->getMessage()]);
        } finally {
            @unlink($tmpSource);
        }

        ParametreGeneral::set(ParametreGeneral::SIGNATURE_GERANT, 'signatures/' . $filename, Auth::user()?->agent_matricule);

        if ($ancien) {
            $ancienPath = base_path('images_projet/' . $ancien);
            if (file_exists($ancienPath) && $ancienPath !== $destPath) {
                @unlink($ancienPath);
            }
        }

        ActivityLog::record(
            'ADMINISTRATION',
            'SIGNATURE_GERANT_MISE_A_JOUR',
            null,
            $filename,
            'Mise à jour de la signature du gérant (fond retiré automatiquement) utilisée sur les documents imprimés.'
        );

        return redirect()->route('administration.parametres.edit')
            ->with('success', 'Signature du gérant mise à jour avec succès (fond retiré automatiquement).');
    }

    public function removeSignature()
    {
        $ancien = ParametreGeneral::get(ParametreGeneral::SIGNATURE_GERANT);
        if ($ancien) {
            $ancienPath = base_path('images_projet/' . $ancien);
            if (file_exists($ancienPath)) {
                @unlink($ancienPath);
            }
        }

        ParametreGeneral::set(ParametreGeneral::SIGNATURE_GERANT, null, Auth::user()?->agent_matricule);

        return redirect()->route('administration.parametres.edit')
            ->with('success', 'Signature du gérant supprimée.');
    }

    /**
     * Sert l'image de signature (même principe que ClientController::photo()).
     */
    public function signature($filename)
    {
        $path = base_path('images_projet/signatures/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        if (ob_get_level()) {
            ob_end_clean();
        }

        $type = mime_content_type($path);
        header('Content-Type: ' . $type);
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}
