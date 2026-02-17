<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = \App\Models\Client::query();
        if (request()->has('search') && request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%$search%")
                  ->orWhere('postnom', 'like', "%$search%")
                  ->orWhere('matricule', 'like', "%$search%") ;
            });
        }
        // Afficher le dernier client en premier (ordre décroissant par date de création)
        $clients = $query->orderByDesc('created_at')->get();
        return view('clients.liste', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'postnom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'telephone' => 'nullable|string|max:255',
                'sexe' => 'required|in:M,F',
                'date_naissance' => 'required|date',
                'lieu_naissance' => 'required|string|max:255',
                'adresse' => 'required|string|max:255',
                'etat_civil' => 'required|string|max:255',
                'nom_conjoint' => 'nullable|string|max:255',
                'zone' => 'required|string|max:255',
                'type_piece_identite' => 'required|string|max:255',
                'lieu_delivrance_piece' => 'required|string|max:255',
                'date_delivrance_piece' => 'required|date',
                'numero_piece_identite' => 'required|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                // Partie 6 : Activité économique
                'secteur_activite' => 'nullable|string|max:255',
                'type_activite' => 'nullable|string|max:255',
                'nom_entreprise' => 'nullable|string|max:255',
                'adresse_entreprise' => 'nullable|string|max:255',
                'telephone_entreprise' => 'nullable|string|max:255',
                'statut_entreprise' => 'nullable|string|max:255',
                'nombre_annees_experience' => 'nullable|integer|min:0',
                'revenu_mensuel' => 'nullable|numeric|min:0',
                'autres_details_activite' => 'nullable|string|max:255',
            ], [
                'photo.image' => 'Le fichier doit être une image.',
                'photo.mimes' => 'Le format de la photo doit être jpeg, png, jpg ou gif.',
                'photo.max' => 'La taille de la photo ne doit pas dépasser 2 Mo.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log toutes les erreurs de validation
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $msg) {
                    Log::error('Validation (' . $field . '): ' . $msg);
                }
            }
            throw $e;
        }

        if ($request->hasFile('photo')) {
            try {
                $image = $request->file('photo');
                // Vérifie que la taille du fichier ne dépasse pas 1 Mo
                if ($image->getSize() > 1 * 1024 * 1024) {
                    return back()->withErrors(['photo' => 'La taille de la photo ne doit pas dépasser 1 Mo.'])->withInput();
                }
                $imageName = time() . '_' . $image->getClientOriginalName();
                $destinationPath = base_path('images_projet/clients');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                // Traitement de l'image avec GD
                $srcPath = $image->getRealPath();
                $info = getimagesize($srcPath);
                // Vérifie que le fichier est bien une image
                if ($info === false) {
                    throw new \Exception('Fichier image invalide.');
                }
                list($width, $height) = $info;
                $maxDim = 600;
                // Calcule le ratio pour ne pas dépasser 600px en largeur ou hauteur
                $ratio = min($maxDim / $width, $maxDim / $height, 1);
                $newWidth = (int)($width * $ratio);
                $newHeight = (int)($height * $ratio);
                // Crée la ressource image selon le type
                switch ($info[2]) {
                    case IMAGETYPE_JPEG:
                        $srcImg = imagecreatefromjpeg($srcPath);
                        break;
                    case IMAGETYPE_PNG:
                        $srcImg = imagecreatefrompng($srcPath);
                        break;
                    case IMAGETYPE_GIF:
                        $srcImg = imagecreatefromgif($srcPath);
                        break;
                    default:
                        throw new \Exception('Format d\'image non supporté.');
                }
                $dstImg = imagecreatetruecolor($newWidth, $newHeight);
                // Gère la transparence pour PNG et GIF
                if ($info[2] == IMAGETYPE_PNG || $info[2] == IMAGETYPE_GIF) {
                    imagecolortransparent($dstImg, imagecolorallocatealpha($dstImg, 0, 0, 0, 127));
                    imagealphablending($dstImg, false);
                    imagesavealpha($dstImg, true);
                }
                // Redimensionne l'image
                imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                // Enregistre l'image en JPEG avec une compression à 80%
                $savePath = $destinationPath . DIRECTORY_SEPARATOR . $imageName;
                imagejpeg($dstImg, $savePath, 80);
                // Libère la mémoire
                imagedestroy($srcImg);
                imagedestroy($dstImg);
                $validated['photo'] = 'clients/' . $imageName;
            } catch (\Exception $e) {
                Log::error('Erreur upload photo client: ' . $e->getMessage());
                return back()->withErrors(['photo' => 'Erreur lors de l’upload de la photoâÂ¯: ' . $e->getMessage()])->withInput();
            }
        }
    // Génération du matricule client
    // Préfixe : CL, code agence : EBENKGA, année sur 2 chiffres, numéro séquentiel sur 5 chiffres
    $prefixe = 'CL';
    $codeAgence = 'EBENKGA';
    $annee = date('y');
    // Compter le nombre de clients créés cette année (pour le séquentiel)
    $count = \App\Models\Client::whereYear('created_at', date('Y'))->count() + 1;
    $numSeq = str_pad($count, 5, '0', STR_PAD_LEFT);
    $matricule = "$prefixe-$codeAgence-$annee-$numSeq";
    $validated['matricule'] = $matricule;

    \App\Models\Client::create($validated);
    return redirect()->route('clients.create')->with('success', 'Client ajouté avec succès. Matricule : ' . $matricule);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // On suppose que $id est le matricule du client
        $client = \App\Models\Client::where('matricule', $id)->firstOrFail();
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $client = \App\Models\Client::where('matricule', $id)->firstOrFail();
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $client = \App\Models\Client::where('matricule', $id)->firstOrFail();
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'postnom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'telephone' => 'nullable|string|max:255',
                'sexe' => 'required|in:M,F',
                'date_naissance' => 'required|date',
                'lieu_naissance' => 'required|string|max:255',
                'adresse' => 'required|string|max:255',
                'etat_civil' => 'required|string|max:255',
                'nom_conjoint' => 'nullable|string|max:255',
                'zone' => 'required|string|max:255',
                'type_piece_identite' => 'required|string|max:255',
                'lieu_delivrance_piece' => 'required|string|max:255',
                'date_delivrance_piece' => 'required|date',
                'numero_piece_identite' => 'required|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'secteur_activite' => 'nullable|string|max:255',
                'type_activite' => 'nullable|string|max:255',
                'nom_entreprise' => 'nullable|string|max:255',
                'adresse_entreprise' => 'nullable|string|max:255',
                'telephone_entreprise' => 'nullable|string|max:255',
                'statut_entreprise' => 'nullable|string|max:255',
                'nombre_annees_experience' => 'nullable|integer|min:0',
                'revenu_mensuel' => 'nullable|numeric|min:0',
                'autres_details_activite' => 'nullable|string|max:255',
            ], [
                'photo.image' => 'Le fichier doit être une image.',
                'photo.mimes' => 'Le format de la photo doit être jpeg, png, jpg ou gif.',
                'photo.max' => 'La taille de la photo ne doit pas dépasser 2 Mo.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $msg) {
                    Log::error('Validation (' . $field . '): ' . $msg);
                }
            }
            throw $e;
        }

        if ($request->hasFile('photo')) {
            try {
                $image = $request->file('photo');
                if ($image->getSize() > 1 * 1024 * 1024) {
                    return back()->withErrors(['photo' => 'La taille de la photo ne doit pas dépasser 1 Mo.'])->withInput();
                }
                $imageName = time() . '_' . $image->getClientOriginalName();
                $destinationPath = base_path('images_projet/clients');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $srcPath = $image->getRealPath();
                $info = getimagesize($srcPath);
                if ($info === false) {
                    throw new \Exception('Fichier image invalide.');
                }
                list($width, $height) = $info;
                $maxDim = 600;
                $ratio = min($maxDim / $width, $maxDim / $height, 1);
                $newWidth = (int)($width * $ratio);
                $newHeight = (int)($height * $ratio);
                switch ($info[2]) {
                    case IMAGETYPE_JPEG:
                        $srcImg = imagecreatefromjpeg($srcPath);
                        break;
                    case IMAGETYPE_PNG:
                        $srcImg = imagecreatefrompng($srcPath);
                        break;
                    case IMAGETYPE_GIF:
                        $srcImg = imagecreatefromgif($srcPath);
                        break;
                    default:
                        throw new \Exception('Format d\'image non supporté.');
                }
                $dstImg = imagecreatetruecolor($newWidth, $newHeight);
                if ($info[2] == IMAGETYPE_PNG || $info[2] == IMAGETYPE_GIF) {
                    imagecolortransparent($dstImg, imagecolorallocatealpha($dstImg, 0, 0, 0, 127));
                    imagealphablending($dstImg, false);
                    imagesavealpha($dstImg, true);
                }
                imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                $savePath = $destinationPath . DIRECTORY_SEPARATOR . $imageName;
                imagejpeg($dstImg, $savePath, 80);
                imagedestroy($srcImg);
                imagedestroy($dstImg);
                // Supprime l'ancienne photo si elle existe et est différente
                if ($client->photo && file_exists(base_path('images_projet/' . $client->photo))) {
                    @unlink(base_path('images_projet/' . $client->photo));
                }
                $validated['photo'] = 'clients/' . $imageName;
            } catch (\Exception $e) {
                Log::error('Erreur upload photo client: ' . $e->getMessage());
                return back()->withErrors(['photo' => 'Erreur lors de l’upload de la photoâÂ¯: ' . $e->getMessage()])->withInput();
            }
        } else {
            // Si aucune nouvelle photo, garder l'ancienne
            $validated['photo'] = $client->photo;
        }

        $client->update($validated);
        return redirect()->route('clients.edit', $client->matricule)->with('success', 'Client modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = \App\Models\Client::where('matricule', $id)->firstOrFail();
        // Supprimer la photo si elle existe
        if ($client->photo && file_exists(base_path('images_projet/' . $client->photo))) {
            @unlink(base_path('images_projet/' . $client->photo));
        }
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }

    /**
     * Serve une image d'un client.
     */
    public function serveImage(string $id)
    {
        $client = \App\Models\Client::findOrFail($id);
        if ($client->photo) {
            $path = storage_path('app/public/clients/' . $client->photo);
            if (file_exists($path)) {
                return Response::file($path);
            }
        }
        return response()->json(['message' => 'Image not found'], 404);
    }

    /**
     * Sert une image client stockée hors du dossier public.
     */
    /*public function photo($filename)
    {
        $path = base_path('images_projet/clients/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        // Récupérer le type de fichier (ex: image/jpeg)
        $type = mime_content_type($path);

        // On force la réponse avec le bon header
        return response()->file($path, [
            'Content-Type' => $type,
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ]);
    }

    public function photo($filename)
    {
        $path = base_path('images_projet/clients/' . $filename);
        dd([
            'Fichier_Existe' => file_exists($path),
            'Chemin_Complet' => $path,
            'Taille_Fichier' => filesize($path) . ' octets'
        ]);
    }*/


   /* public function photo($filename)
    {
        $path = base_path('images_projet/clients/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        // --- NETTOYAGE DU FLUX ---
        // Supprime tout espace ou caractère envoyé par erreur avant l'image
        while (ob_get_level()) {
            ob_end_clean();
        }

        $type = mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $type,
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }*/
    public function photo($filename)
    {
        $path = base_path('images_projet/clients/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        // On vide les tampons pour éviter tout caractère parasite
        if (ob_get_level()) ob_end_clean();

        $type = mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $type,
            'X-Content-Type-Options' => 'nosniff', // Indispensable pour Edge/IE
            'Content-Length' => filesize($path),
        ]);
    }



}
