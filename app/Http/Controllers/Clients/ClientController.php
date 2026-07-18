<?php


namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\RH\Affectation;
use App\Models\Zone;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    private const FICHE_RECOLTE_SINGLE_PDF_MAX_ROWS = 250;

    private const FICHE_RECOLTE_ZIP_CHUNK_SIZE = 250;

    private const LISTE_CLIENTS_SINGLE_PDF_MAX_ROWS = 300;

    private const LISTE_CLIENTS_ZIP_CHUNK_SIZE = 300;

    private function isMobileGuichet(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || empty($user->agent_matricule)) {
            return false;
        }

        $affectation = Affectation::with('guichet')
            ->where('agent_matricule', $user->agent_matricule)
            ->where('Etat', 'ACTIF')
            ->whereNotNull('guichet_id')
            ->latest('date_debut')
            ->first();

        return (bool) ($affectation && $affectation->guichet && $affectation->guichet->type_guichet === 'MOBILE');
    }

    private function abortIfMobilePrintForbidden(string $documentType): void
    {
        if (!$this->isMobileGuichet()) {
            return;
        }

        Log::warning('[Client] Impression refusée pour guichet mobile', [
            'document_type' => $documentType,
            'agent_matricule' => Auth::user()?->agent_matricule,
            'ip' => request()->ip(),
        ]);

        abort(403, 'Accès refusé : un guichet mobile ne peut pas imprimer les documents liés au client.');
    }

    private function buildZoneLabel(array $zoneNames): string
    {
        $zoneNames = array_values(array_filter($zoneNames));
        if (empty($zoneNames)) {
            return '';
        }

        if (count($zoneNames) === 1) {
            $label = trim($zoneNames[0]);
            return preg_match('/^zones?\b/i', $label) ? $label : 'Zone ' . $label;
        }

        $joined = implode(', ', array_map('trim', $zoneNames));
        return preg_match('/^zones?\b/i', $joined) ? $joined : 'Zones ' . $joined;
    }

    private function respondWithPdf($pdf, string $filename, Request $request)
    {
        $outputMode = strtolower((string) $request->input('output', 'stream'));

        if ($request->boolean('download') || $outputMode === 'download') {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    private function getExportFormat(Request $request): string
    {
        return strtolower((string) $request->input('export_format', 'pdf'));
    }

    private function buildFicheRecoltePdf($clients, ?Zone $zone, string $agentCommercialNom, string $dateRecolte)
    {
        return Pdf::loadView('impressions.clients.fiche_recolte_journaliere', [
            'clients' => $clients,
            'zone' => $zone,
            'agentCommercialNom' => $agentCommercialNom,
            'dateRecolte' => $dateRecolte,
        ])->setPaper('a4', 'portrait');
    }

    private function buildListeClientsPdf($clients, array $filtres, ?Zone $zone)
    {
        return Pdf::loadView('impressions.clients.liste', compact('clients', 'filtres', 'zone'))
            ->setPaper('a4', 'portrait');
    }

    private function createChunkedPdfZip(
        Builder $query,
        int $chunkSize,
        callable $pdfFactory,
        string $zipFilenamePrefix,
        string $pdfFilenamePrefix,
        array $logContext = []
    ) {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('L\'extension ZIP n\'est pas disponible sur le serveur pour les exports volumineux. Utilisez l\'export CSV.');
        }

        $exportDirectory = storage_path('app/tmp/exports');

        if (!is_dir($exportDirectory) && !mkdir($exportDirectory, 0775, true) && !is_dir($exportDirectory)) {
            throw new \RuntimeException('Impossible de créer le dossier temporaire d\'export PDF.');
        }

        $zipFilename = $zipFilenamePrefix . '_' . now()->format('Ymd_His') . '.zip';
        $zipPath = $exportDirectory . DIRECTORY_SEPARATOR . $zipFilename;
        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Impossible de créer l\'archive ZIP des PDF.');
        }

        $partIndex = 0;

        $query->chunk($chunkSize, function ($rows) use (&$partIndex, $zip, $pdfFactory, $pdfFilenamePrefix) {
            $partIndex++;

            $pdf = $pdfFactory($rows->values());

            $zip->addFromString(
                sprintf('%s_partie_%03d.pdf', $pdfFilenamePrefix, $partIndex),
                $pdf->output()
            );
        });

        $zip->close();

        Log::warning($logContext['message'] ?? '[Client] Export PDF segmenté en ZIP', [
            'clients_count' => $logContext['clients_count'] ?? null,
            'parts' => $partIndex,
            'chunk_size' => $chunkSize,
            'document_type' => $logContext['document_type'] ?? null,
            'user_id' => Auth::id(),
        ]);

        return response()->download($zipPath, $zipFilename, ['Content-Type' => 'application/zip'])
            ->deleteFileAfterSend(true);
    }

    private function downloadListeClientsZip(Builder $query, array $filtres, ?Zone $zone, int $clientsCount)
    {
        return $this->createChunkedPdfZip(
            $query,
            self::LISTE_CLIENTS_ZIP_CHUNK_SIZE,
            fn ($clients) => $this->buildListeClientsPdf($clients, $filtres, $zone),
            'Listes_clients',
            'Liste_clients',
            [
                'message' => '[Client] Liste clients exportée en ZIP',
                'clients_count' => $clientsCount,
                'document_type' => 'liste_clients',
            ]
        );
    }

    private function downloadFicheRecolteZip(
        Builder $query,
        ?Zone $zone,
        string $agentCommercialNom,
        string $dateRecolte,
        int $clientsCount
    ) {
        return $this->createChunkedPdfZip(
            $query,
            self::FICHE_RECOLTE_ZIP_CHUNK_SIZE,
            fn ($clients) => $this->buildFicheRecoltePdf($clients, $zone, $agentCommercialNom, $dateRecolte),
            'Fiches_recolte_journaliere',
            'Fiche_recolte_journaliere',
            [
                'message' => '[Client] Fiche récolte exportée en ZIP',
                'clients_count' => $clientsCount,
                'document_type' => 'fiche_recolte_journaliere',
            ]
        );
    }

    private function downloadClientsCsv(
        Builder $query,
        string $documentType,
        ?Zone $zone,
        string $agentCommercialNom,
        string $dateRecolte
    ) {
        $filename = $documentType === 'fiche_recolte_journaliere'
            ? 'Fiche_recolte_journaliere_' . now()->format('Ymd_His') . '.csv'
            : 'Liste_clients_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query, $documentType, $zone, $agentCommercialNom, $dateRecolte) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            if ($documentType === 'fiche_recolte_journaliere') {
                fputcsv($handle, ['Zone', $zone?->nom ?? 'Toutes les zones'], ';');
                fputcsv($handle, ['Agent commercial', $agentCommercialNom], ';');
                fputcsv($handle, ['Date de recolte', $dateRecolte], ';');
                fputcsv($handle, [], ';');
                fputcsv($handle, ['Matricule client', 'Nom complet', 'Zone', 'EAC', 'RMB', 'Signature'], ';');
            } else {
                fputcsv($handle, ['Matricule', 'Nom complet', 'Sexe', 'Zone', 'Telephone', 'Email', 'Comptes', 'Etat civil', 'Date inscription'], ';');
            }

            $query->chunk(1000, function ($clients) use ($handle, $documentType) {
                foreach ($clients as $client) {
                    $nomComplet = trim(implode(' ', array_filter([
                        strtoupper((string) ($client->nom ?? '')),
                        strtoupper((string) ($client->postnom ?? '')),
                        strtoupper((string) ($client->prenom ?? '')),
                    ])));

                    if ($documentType === 'fiche_recolte_journaliere') {
                        fputcsv($handle, [
                            $client->matricule,
                            $nomComplet,
                            $client->zone->nom ?? '',
                            '',
                            '',
                            '',
                        ], ';');

                        continue;
                    }

                    fputcsv($handle, [
                        $client->matricule,
                        $nomComplet,
                        $client->sexe,
                        $client->zone->nom ?? '',
                        $client->telephone,
                        $client->email,
                        $client->comptes_count,
                        $client->etat_civil,
                        optional($client->created_at)->format('d/m/Y'),
                    ], ';');
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildClientPhotoDataUri(?string $relativePath): ?string
    {
        if (!$relativePath) {
            return null;
        }

        $photoPath = base_path('images_projet/' . ltrim($relativePath, '/'));
        if (!file_exists($photoPath)) {
            return null;
        }

        $imageInfo = @getimagesize($photoPath);
        if ($imageInfo === false) {
            return null;
        }

        [$width, $height, $imageType] = $imageInfo;
        $maxWidth = 320;
        $maxHeight = 400;
        $ratio = min($maxWidth / max($width, 1), $maxHeight / max($height, 1), 1);
        $targetWidth = max(1, (int) round($width * $ratio));
        $targetHeight = max(1, (int) round($height * $ratio));

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $source = @imagecreatefromjpeg($photoPath);
                break;
            case IMAGETYPE_PNG:
                $source = @imagecreatefrompng($photoPath);
                break;
            case IMAGETYPE_GIF:
                $source = @imagecreatefromgif($photoPath);
                break;
            default:
                return 'data:' . ($imageInfo['mime'] ?? 'image/jpeg') . ';base64,' . base64_encode((string) file_get_contents($photoPath));
        }

        if (!$source) {
            return null;
        }

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        ob_start();
        imagejpeg($canvas, null, 78);
        $binary = (string) ob_get_clean();

        imagedestroy($source);
        imagedestroy($canvas);

        return 'data:image/jpeg;base64,' . base64_encode($binary);
    }

    private function resolveZoneScope(): array
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || empty($user->agent_matricule)) {
            return ['restricted' => false, 'zone_codes' => []];
        }

        $affectation = Affectation::with('guichet')
            ->where('agent_matricule', $user->agent_matricule)
            ->where('Etat', 'ACTIF')
            ->whereNotNull('guichet_id')
            ->latest('date_debut')
            ->first();

        if (!$affectation || !$affectation->guichet || $affectation->guichet->type_guichet !== 'MOBILE') {
            return ['restricted' => false, 'zone_codes' => []];
        }

        $zones = Zone::assignedToAgent($user->agent_matricule)
            ->orderBy('nom')
            ->get(['code_zone', 'nom']);

        $zoneCodes = $zones->pluck('code_zone')
            ->filter()
            ->values()
            ->all();

        $zoneNames = $zones->pluck('nom')
            ->filter()
            ->values()
            ->all();

        $zoneLabel = $this->buildZoneLabel($zoneNames);

        return [
            'restricted' => true,
            'zone_codes' => $zoneCodes,
            'zone_names' => $zoneNames,
            'zone_label' => $zoneLabel,
            'agent_matricule' => $user->agent_matricule,
        ];
    }

    private function applyZoneScopeToClients(Builder $query, array $zoneScope): Builder
    {
        if (!($zoneScope['restricted'] ?? false)) {
            return $query;
        }

        $zoneCodes = $zoneScope['zone_codes'] ?? [];
        if (empty($zoneCodes)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('code_zone', $zoneCodes);
    }

    private function canAccessClientZone(?string $clientZone, array $zoneScope): bool
    {
        if (!($zoneScope['restricted'] ?? false)) {
            return true;
        }

        return in_array($clientZone, $zoneScope['zone_codes'] ?? [], true);
    }

    private function restrictedZonesQuery(array $zoneScope)
    {
        $zones = Zone::orderBy('nom');

        if (!($zoneScope['restricted'] ?? false)) {
            return $zones;
        }

        $zoneCodes = $zoneScope['zone_codes'] ?? [];
        if (empty($zoneCodes)) {
            return $zones->whereRaw('1 = 0');
        }

        return $zones->whereIn('code_zone', $zoneCodes);
    }

    private function zoneRestrictionInfo(array $zoneScope): array
    {
        return [
            'active' => (bool) ($zoneScope['restricted'] ?? false),
            'zone_count' => count($zoneScope['zone_codes'] ?? []),
            'zone_names' => $zoneScope['zone_names'] ?? [],
            'zone_label' => $zoneScope['zone_label'] ?? '',
        ];
    }


    /* La méthode pour afficher la liste des clients avec la possibilité de rechercher par nom, postnom ou matricule */
    public function index()
    {
        $zoneScope = $this->resolveZoneScope();

        // On ajoute with(['zone']) pour charger la relation
        $query = \App\Models\Clients\Client::with(['zone']);
        $this->applyZoneScopeToClients($query, $zoneScope);

        // Si une recherche est effectuée, filtrer les clients
        if (request()->has('search') && request('search')) {
            $search = trim((string) request('search'));
            $query->where(function($q) use ($search) {
                $q->searchFullName($search)
                ->orWhere('nom', 'like', "%$search%")
                ->orWhere('postnom', 'like', "%$search%")
                ->orWhere('prenom', 'like', "%$search%")
                ->orWhere('matricule', 'like', "%$search%")
                ->orWhere('telephone', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
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

        $zones = $this->restrictedZonesQuery($zoneScope)->get();
        $zoneRestriction = $this->zoneRestrictionInfo($zoneScope);

        $canPrintDocuments = !$this->isMobileGuichet();

        return view('clients.liste', compact('clients', 'stats', 'zones', 'zoneRestriction', 'canPrintDocuments'));
    }

                
     /* La méthode pour afficher le formulaire de création d'un nouveau client */

    public function create()
    {
        $zoneScope = $this->resolveZoneScope();
        $zones = $this->restrictedZonesQuery($zoneScope)->get();
        $zoneRestriction = $this->zoneRestrictionInfo($zoneScope);

        return view('clients.create', compact('zones', 'zoneRestriction'));
    }

    /* La méthode pour stocker un nouveau client dans la base de données */
    public function store(Request $request)
    {
        $zoneScope = $this->resolveZoneScope();

        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'postnom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:tb_clients,email',
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
                'numero_piece_identite' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('tb_clients', 'numero_piece_identite')
                        ->where(fn ($query) => $query->where('type_piece_identite', $request->input('type_piece_identite'))),
                ],
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

        if (($zoneScope['restricted'] ?? false) && !in_array($validated['code_zone'], $zoneScope['zone_codes'] ?? [], true)) {
            Log::warning('[Client] Tentative de création hors zone autorisée', [
                'agent_matricule' => Auth::user()?->agent_matricule,
                'code_zone' => $validated['code_zone'],
                'zones_autorisees' => $zoneScope['zone_codes'] ?? [],
                'ip' => request()->ip(),
            ]);

            return back()->withErrors([
                'code_zone' => 'Accès refusé : vous ne pouvez créer un client que dans votre zone affectée.',
            ])->withInput();
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

        try {
            $client = \App\Models\Clients\Client::create($validated);
        } catch (QueryException $e) {
            if (($e->errorInfo[0] ?? null) === '23000') {
                return back()->withErrors([
                    'numero_piece_identite' => 'Doublon détecté : cette pièce d\'identité existe déjà dans le système.',
                ])->withInput();
            }

            throw $e;
        }

        \App\Models\ActivityLog::record(
            'CLIENT',
            'CLIENT_CREE',
            $client,
            $client->matricule,
            "Création du client {$client->nom} {$client->prenom} ({$client->matricule})"
        );

        return redirect()->route('clients.create')->with('success', 'Client ajouté avec succès. Matricule : ' . $client->matricule);
    }

    /**
     * La méthode pour afficher les détails d'un client spécifique.
     */
    public function show(string $id)
    {
        $zoneScope = $this->resolveZoneScope();
        $client = \App\Models\Clients\Client::where('matricule', $id)->first();
        if (!$client) {
            Log::warning('[Client] Client introuvable', ['matricule' => $id, 'action' => 'show', 'ip' => request()->ip()]);
            abort(404, 'Client introuvable : ' . $id);
        }

        if (!$this->canAccessClientZone($client->code_zone, $zoneScope)) {
            Log::warning('[Client] Accès refusé hors zone', ['matricule' => $id, 'action' => 'show', 'ip' => request()->ip()]);
            abort(403, 'Accès refusé : ce client n\'appartient pas à votre zone.');
        }

        return view('clients.show', compact('client'));
    }

    /**
     * La méthode pour afficher le formulaire de modification d'un client spécifique.
     */
     
    public function edit(string $id)
    {
        $zoneScope = $this->resolveZoneScope();
        $client = \App\Models\Clients\Client::where('matricule', $id)->first();
        if (!$client) {
            Log::warning('[Client] Client introuvable', ['matricule' => $id, 'action' => 'edit', 'ip' => request()->ip()]);
            abort(404, 'Client introuvable : ' . $id);
        }

        if (!$this->canAccessClientZone($client->code_zone, $zoneScope)) {
            Log::warning('[Client] Accès refusé hors zone', ['matricule' => $id, 'action' => 'edit', 'ip' => request()->ip()]);
            abort(403, 'Accès refusé : ce client n\'appartient pas à votre zone.');
        }

        $zones = $this->restrictedZonesQuery($zoneScope)->get();
        return view('clients.edit', compact('client', 'zones'));
    }

    /**
     * La méthode pour mettre à jour les informations d'un client spécifique dans la base de données.
     */
    public function update(Request $request, string $id)
    {
        $zoneScope = $this->resolveZoneScope();
        $client = \App\Models\Clients\Client::where('matricule', $id)->first();
        if (!$client) {
            Log::warning('[Client] Client introuvable', ['matricule' => $id, 'action' => 'update', 'ip' => request()->ip()]);
            abort(404, 'Client introuvable : ' . $id);
        }

        if (!$this->canAccessClientZone($client->code_zone, $zoneScope)) {
            Log::warning('[Client] Accès refusé hors zone', ['matricule' => $id, 'action' => 'update', 'ip' => request()->ip()]);
            abort(403, 'Accès refusé : ce client n\'appartient pas à votre zone.');
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

        if (($zoneScope['restricted'] ?? false) && !in_array($validated['code_zone'], $zoneScope['zone_codes'] ?? [], true)) {
            Log::warning('[Client] Tentative de modification hors zone autorisée', [
                'matricule' => $id,
                'agent_matricule' => Auth::user()?->agent_matricule,
                'code_zone' => $validated['code_zone'],
                'zones_autorisees' => $zoneScope['zone_codes'] ?? [],
                'ip' => request()->ip(),
            ]);

            return back()->withErrors([
                'code_zone' => 'Accès refusé : vous ne pouvez affecter ce client qu\'à votre zone.',
            ])->withInput();
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

        $ancienNom = $client->nom . ' ' . $client->prenom;
        $client->update($validated);

        \App\Models\ActivityLog::record(
            'CLIENT',
            'CLIENT_MODIFIE',
            $client,
            $client->matricule,
            "Modification du client {$ancienNom} ({$client->matricule})"
        );

        return redirect()->route('clients.edit', $client->matricule)->with('success', 'Client modifié avec succès.');
    }

    /**
     * La méthode pour supprimer un client spécifique de la base de données.
     */
    public function destroy(string $id)
    {
        $zoneScope = $this->resolveZoneScope();
        $client = \App\Models\Clients\Client::where('matricule', $id)->first();
        if (!$client) {
            Log::warning('[Client] Client introuvable', ['matricule' => $id, 'action' => 'destroy', 'ip' => request()->ip()]);
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Client introuvable.'], 404);
            }
            abort(404, 'Client introuvable : ' . $id);
        }

        if (!$this->canAccessClientZone($client->code_zone, $zoneScope)) {
            Log::warning('[Client] Accès refusé hors zone', ['matricule' => $id, 'action' => 'destroy', 'ip' => request()->ip()]);
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Accès refusé : client hors de votre zone.'], 403);
            }
            abort(403, 'Accès refusé : ce client n\'appartient pas à votre zone.');
        }

        // Supprimer la photo si elle existe
        if ($client->photo && file_exists(base_path('images_projet/' . $client->photo))) {
            @unlink(base_path('images_projet/' . $client->photo));
        }

        $nomClient = $client->nom . ' ' . $client->prenom;
        $matriculeClient = $client->matricule;
        $client->delete();

        \App\Models\ActivityLog::record(
            'CLIENT',
            'CLIENT_SUPPRIME',
            null,
            $matriculeClient,
            "Suppression du client {$nomClient} ({$matriculeClient})"
        );

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
    public function imprimerFiche(Request $request, string $matricule)
    {
        $this->abortIfMobilePrintForbidden('fiche-client');
        ini_set('memory_limit', '512M');

        $zoneScope = $this->resolveZoneScope();
        $client = \App\Models\Clients\Client::with(['zone', 'comptes'])
                    ->where('matricule', $matricule)
                    ->first();
        if (!$client) {
            Log::warning('[Client] Client introuvable pour impression fiche', ['matricule' => $matricule, 'ip' => request()->ip()]);
            abort(404, 'Client introuvable : ' . $matricule);
        }

        if (!$this->canAccessClientZone($client->code_zone, $zoneScope)) {
            Log::warning('[Client] Impression refusée hors zone', ['matricule' => $matricule, 'ip' => request()->ip()]);
            abort(403, 'Accès refusé : ce client n\'appartient pas à votre zone.');
        }

        $photoBase64 = $this->buildClientPhotoDataUri($client->photo);

        $pdf = Pdf::loadView('impressions.clients.fiche', compact('client', 'photoBase64'))
                  ->setPaper('a4', 'portrait');

        return $this->respondWithPdf($pdf, 'Fiche_' . $matricule . '.pdf', $request);
    }

    /* ─────────────────────────────────────────────────────────
     *  IMPRESSION  —  Liste filtrée de clients
     * ───────────────────────────────────────────────────────── */
    public function imprimerListe(\Illuminate\Http\Request $request)
    {
        $this->abortIfMobilePrintForbidden('liste-clients');

        // DomPDF reste coûteux: on relève la limite puis on segmente les gros volumes.
        ini_set('memory_limit', '768M');

        $documentType = $request->input('document_type', 'liste_clients');
        $exportFormat = $this->getExportFormat($request);

        $zoneScope = $this->resolveZoneScope();
        // Optimisation : utiliser withCount() au lieu de with() pour les comptes
        // Cela charge seulement le nombre de comptes sans charger tous les objets Compte
        $query = \App\Models\Clients\Client::with(['zone'])->withCount('comptes');
        $this->applyZoneScopeToClients($query, $zoneScope);

        // Filtres
        if ($request->filled('code_zone')) {
            if (($zoneScope['restricted'] ?? false) && !in_array($request->code_zone, $zoneScope['zone_codes'] ?? [], true)) {
                Log::warning('[Client] Impression liste refusée hors zone', [
                    'code_zone' => $request->code_zone,
                    'agent_matricule' => Auth::user()?->agent_matricule,
                    'ip' => request()->ip(),
                ]);
                $query->whereRaw('1 = 0');
            } else {
                $query->where('code_zone', $request->code_zone);
            }
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

        $query->orderBy('nom')->orderBy('matricule');
        $clientsCount = (clone $query)->count();
        $filtres  = $request->only(['code_zone','sexe','date_debut','date_fin','avec_photo','avec_comptes','etat_civil','document_type','date_recolte']);
        $zone = null;
        if ($request->filled('code_zone') && (!($zoneScope['restricted'] ?? false) || in_array($request->code_zone, $zoneScope['zone_codes'] ?? [], true))) {
            $zone = \App\Models\Zone::with(['agent', 'affectationActive.agent'])->find($request->code_zone);
        }

        /** @var \App\Models\User|null $printedByUser */
        $printedByUser = Auth::user();
        $printedByUser?->loadMissing('agent');

        $agentCommercialNom = null;
        $zoneAgentActif = $zone?->affectationActive?->agent ?? $zone?->agent;
        if ($zoneAgentActif) {
            $agentCommercialNom = trim(
                strtoupper((string) ($zoneAgentActif->nom ?? '')) . ' ' .
                strtoupper((string) ($zoneAgentActif->postnom ?? '')) . ' ' .
                strtoupper((string) ($zoneAgentActif->prenom ?? ''))
            );
        }

        if (!$agentCommercialNom && $printedByUser?->agent) {
            $a = $printedByUser->agent;
            $agentCommercialNom = trim(
                strtoupper((string) ($a->nom ?? '')) . ' ' .
                strtoupper((string) ($a->postnom ?? '')) . ' ' .
                strtoupper((string) ($a->prenom ?? ''))
            );
        }

        if (!$agentCommercialNom) {
            $agentCommercialNom = strtoupper((string) ($printedByUser->name ?? $printedByUser->agent_matricule ?? 'NON DEFINI'));
        }

        $dateRecolte = $request->filled('date_recolte')
            ? \Carbon\Carbon::parse($request->input('date_recolte'))->format('d/m/Y')
            : now()->format('d/m/Y');

        if ($exportFormat === 'csv') {
            return $this->downloadClientsCsv(clone $query, $documentType, $zone, $agentCommercialNom, $dateRecolte);
        }

        if ($documentType === 'fiche_recolte_journaliere') {
            try {
                if ($clientsCount > self::FICHE_RECOLTE_SINGLE_PDF_MAX_ROWS) {
                    return $this->downloadFicheRecolteZip(
                        clone $query,
                        $zone,
                        $agentCommercialNom,
                        $dateRecolte,
                        $clientsCount
                    );
                }

                $clients = $query->get();
                $pdf = $this->buildFicheRecoltePdf($clients, $zone, $agentCommercialNom, $dateRecolte);

                return $this->respondWithPdf($pdf, 'Fiche_recolte_journaliere.pdf', $request);
            } catch (\Throwable $e) {
                Log::error('[Client] Erreur génération Fiche récolte journalière', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'clients_count' => $clientsCount,
                    'memory_get_peak_usage' => memory_get_peak_usage(true),
                    'ini_memory_limit' => ini_get('memory_limit'),
                    'user_id' => Auth::id(),
                ]);
                abort(500, 'Erreur lors de la génération du PDF: ' . $e->getMessage());
            }
        }

        try {
            if ($clientsCount > self::LISTE_CLIENTS_SINGLE_PDF_MAX_ROWS) {
                return $this->downloadListeClientsZip(clone $query, $filtres, $zone, $clientsCount);
            }

            $clients = $query->get();
            $pdf = $this->buildListeClientsPdf($clients, $filtres, $zone);

            return $this->respondWithPdf($pdf, 'Liste_clients.pdf', $request);
        } catch (\Throwable $e) {
            Log::error('[Client] Erreur génération Liste clients PDF', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'clients_count' => $clientsCount,
                'memory_get_peak_usage' => memory_get_peak_usage(true),
                'ini_memory_limit' => ini_get('memory_limit'),
                'user_id' => Auth::id(),
            ]);
            abort(500, 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
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
