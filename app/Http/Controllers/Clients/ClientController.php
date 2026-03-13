<?php


namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class ClientController extends Controller
{

    /* La méthode pour afficher la liste des clients avec la possibilité de rechercher par nom, postnom ou matricule */
    public function index()
    {
        // On ajoute with(['zone']) pour charger la relation
        $query = \App\Models\Clients\Client::with(['zone']); 

        // Si une recherche est effectuée, filtrer les clients
        if (request()->has('search') && request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%$search%")
                ->orWhere('postnom', 'like', "%$search%")
                ->orWhere('matricule', 'like', "%$search%");
            });
        }

        // On récupère les résultats
        $clients = $query->orderByDesc('created_at')->get();

        $stats = [
            'total'      => $clients->count(),
            'hommes'     => $clients->where('sexe', 'M')->count(),
            'femmes'     => $clients->where('sexe', 'F')->count(),
            'avec_photo' => $clients->filter(fn($c) => $c->photo)->count(),
        ];

        $zones = \App\Models\Zone::orderBy('nom')->get();

        return view('clients.liste', compact('clients', 'stats', 'zones'));
    }

                
     /* La méthode pour afficher le formulaire de création d'un nouveau client */

    public function create()
    {
        $zones = \App\Models\Zone::orderBy('nom')->get();
        return view('clients.create', compact('zones'));
    }

    /* La méthode pour stocker un nouveau client dans la base de données */
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
                'code_zone' => 'required|exists:tb_zones,code_zone',
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


    $client = \App\Models\Clients\Client::create($validated);
    return redirect()->route('clients.create')->with('success', 'Client ajouté avec succès. Matricule : ' . $client->matricule);
    }

    /**
     * La méthode pour afficher les détails d'un client spécifique.
     */
    public function show(string $id)
    {
        $client = \App\Models\Clients\Client::where('matricule', $id)->first();
        if (!$client) {
            Log::warning('[Client] Client introuvable', ['matricule' => $id, 'action' => 'show', 'ip' => request()->ip()]);
            abort(404, 'Client introuvable : ' . $id);
        }
        return view('clients.show', compact('client'));
    }

    /**
     * La méthode pour afficher le formulaire de modification d'un client spécifique.
     */
     
    public function edit(string $id)
    {
        $client = \App\Models\Clients\Client::where('matricule', $id)->first();
        if (!$client) {
            Log::warning('[Client] Client introuvable', ['matricule' => $id, 'action' => 'edit', 'ip' => request()->ip()]);
            abort(404, 'Client introuvable : ' . $id);
        }
        $zones = \App\Models\Zone::orderBy('nom')->get();
        return view('clients.edit', compact('client', 'zones'));
    }

    /**
     * La méthode pour mettre à jour les informations d'un client spécifique dans la base de données.
     */
    public function update(Request $request, string $id)
    {
        $client = \App\Models\Clients\Client::where('matricule', $id)->first();
        if (!$client) {
            Log::warning('[Client] Client introuvable', ['matricule' => $id, 'action' => 'update', 'ip' => request()->ip()]);
            abort(404, 'Client introuvable : ' . $id);
        }
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
                'code_zone' => 'required|exists:tb_zones,code_zone',
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
     * La méthode pour supprimer un client spécifique de la base de données.
     */
    public function destroy(string $id)
    {
        $client = \App\Models\Clients\Client::where('matricule', $id)->first();
        if (!$client) {
            Log::warning('[Client] Client introuvable', ['matricule' => $id, 'action' => 'destroy', 'ip' => request()->ip()]);
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Client introuvable.'], 404);
            }
            abort(404, 'Client introuvable : ' . $id);
        }
        // Supprimer la photo si elle existe
        if ($client->photo && file_exists(base_path('images_projet/' . $client->photo))) {
            @unlink(base_path('images_projet/' . $client->photo));
        }
        $client->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Client supprimé avec succès.']);
        }

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }

    /**
     * La méthode pour servir une image d'un client.
     */
    public function serveImage(string $id)
    {
        $client = \App\Models\Clients\Client::find($id);
        if (!$client) {
            Log::warning('[Client] Client introuvable pour image', ['id' => $id, 'ip' => request()->ip()]);
            return response()->json(['message' => 'Client introuvable'], 404);
        }
        if ($client->photo) {
            $path = storage_path('app/public/clients/' . $client->photo);
            if (file_exists($path)) {
                return Response::file($path);
            }
        }
        return response()->json(['message' => 'Image not found'], 404);
    }

        /**
        * La méthode pour afficher la photo d'un client.
        * Cette méthode est utilisée pour afficher la photo du client dans les vues.
        * Elle prend le nom de fichier de la photo en paramètre et retourne l'image correspondante.
        * Si l'image n'existe pas, elle retourne une erreur 404.
        */  
    
    /* ─────────────────────────────────────────────────────────
     *  IMPRESSION  —  Fiche individuelle client
     * ───────────────────────────────────────────────────────── */
    public function imprimerFiche(string $matricule)
    {
        $client = \App\Models\Clients\Client::with(['zone', 'comptes'])
                    ->where('matricule', $matricule)
                    ->first();
        if (!$client) {
            Log::warning('[Client] Client introuvable pour impression fiche', ['matricule' => $matricule, 'ip' => request()->ip()]);
            abort(404, 'Client introuvable : ' . $matricule);
        }

        // Photo base64
        $photoBase64 = null;
        if ($client->photo) {
            $photoPath = base_path('images_projet/' . $client->photo);
            if (file_exists($photoPath)) {
                $mime = mime_content_type($photoPath);
                $photoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($photoPath));
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('impressions.clients.fiche', compact('client', 'photoBase64'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Fiche_' . $matricule . '.pdf');
    }

    /* ─────────────────────────────────────────────────────────
     *  IMPRESSION  —  Liste filtrée de clients
     * ───────────────────────────────────────────────────────── */
    public function imprimerListe(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Clients\Client::with(['zone', 'comptes']);

        // Filtres
        if ($request->filled('code_zone')) {
            $query->where('code_zone', $request->code_zone);
        }
        if ($request->filled('sexe') && in_array($request->sexe, ['M', 'F'])) {
            $query->where('sexe', $request->sexe);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        if ($request->filled('avec_photo') && $request->avec_photo !== 'tous') {
            if ($request->avec_photo === 'oui') {
                $query->whereNotNull('photo')->where('photo', '!=', '');
            } else {
                $query->where(function($q) { $q->whereNull('photo')->orWhere('photo', ''); });
            }
        }
        if ($request->filled('avec_comptes') && $request->avec_comptes !== 'tous') {
            if ($request->avec_comptes === 'oui') {
                $query->has('comptes');
            } else {
                $query->doesntHave('comptes');
            }
        }
        if ($request->filled('etat_civil') && $request->etat_civil !== 'tous') {
            $query->where('etat_civil', $request->etat_civil);
        }

        $clients  = $query->orderBy('nom')->get();
        $filtres  = $request->only(['code_zone','sexe','date_debut','date_fin','avec_photo','avec_comptes','etat_civil']);
        $zone     = $request->filled('code_zone') ? \App\Models\Zone::find($request->code_zone) : null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('impressions.clients.liste', compact('clients', 'filtres', 'zone'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Liste_clients.pdf');
    }

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
