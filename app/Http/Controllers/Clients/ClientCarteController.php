<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Caisse\CaissesGuichet;
use App\Models\Clients\Client;
use App\Models\Clients\ClientCarte;
use App\Models\RH\Affectation;
use App\Support\BaconQrCode\Renderer\Image\SvgImageBackEnd;
use App\Support\BaconQrCode\Renderer\ImageRenderer;
use App\Support\BaconQrCode\Renderer\RendererStyle\RendererStyle;
use App\Support\BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Émission de la carte membre physique — encaissement du frais puis impression.
 *
 * La carte n'est imprimable qu'après paiement du frais configuré dans
 * Trésorerie > Commissions (CommissionRule, code_operation = 'CARTE_MEMBRE').
 */
class ClientCarteController extends Controller
{
    private function getGuichetAgent(): ?CaissesGuichet
    {
        $matricule = Auth::user()?->agent_matricule;
        if (!$matricule) {
            return null;
        }

        $affectation = Affectation::where('agent_matricule', $matricule)
            ->where('Etat', 'ACTIF')
            ->whereNotNull('guichet_id')
            ->orderByDesc('date_debut')
            ->with('guichet')
            ->first();

        return $affectation?->guichet;
    }

    private function isMobileGuichet(): bool
    {
        return strtoupper((string) ($this->getGuichetAgent()?->type_guichet)) === 'MOBILE';
    }

    /**
     * Génère un PNG avec un dégradé diagonal vert pour le fond de la carte.
     * DomPDF ne supporte pas linear-gradient en CSS, on génère donc une image
     * bitmap à la volée. Si GD n'est pas disponible, retourne une couleur unie.
     */
    private function buildGradientPngBase64(int $width, int $height): string
    {
        if (!function_exists('imagecreatetruecolor')) {
            return '';
        }

        $img = imagecreatetruecolor($width, $height);
        if ($img === false) {
            return '';
        }

        $colors = [
            ['r' => 0x0b, 'g' => 0x6e, 'b' => 0x4f], // #0b6e4f
            ['r' => 0x0a, 'g' => 0x5c, 'b' => 0x42], // #0a5c42
            ['r' => 0x0d, 'g' => 0x3b, 'b' => 0x2e], // #0d3b2e
        ];

        $stops = [0.0, 0.45, 1.0];

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $t = ($x / max(1, $width - 1)) * 0.5 + ($y / max(1, $height - 1)) * 0.5;

                if ($t <= $stops[0]) {
                    $c = $colors[0];
                } elseif ($t >= $stops[count($stops) - 1]) {
                    $c = $colors[count($colors) - 1];
                } else {
                    for ($i = 0; $i < count($stops) - 1; $i++) {
                        if ($t >= $stops[$i] && $t <= $stops[$i + 1]) {
                            $local = ($t - $stops[$i]) / max(0.0001, $stops[$i + 1] - $stops[$i]);
                            $a = $colors[$i];
                            $b = $colors[$i + 1];
                            $c = [
                                'r' => (int) round($a['r'] + ($b['r'] - $a['r']) * $local),
                                'g' => (int) round($a['g'] + ($b['g'] - $a['g']) * $local),
                                'b' => (int) round($a['b'] + ($b['b'] - $a['b']) * $local),
                            ];
                            break;
                        }
                    }
                }

                $col = imagecolorallocate($img, $c['r'], $c['g'], $c['b']);
                imagesetpixel($img, $x, $y, $col);
            }
        }

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return $png !== false ? base64_encode($png) : '';
    }

    /**
     * Génère le QR code de vérification en SVG (base64), avec une bibliothèque
     * d'encodage QR (bacon/bacon-qr-code) embarquée DIRECTEMENT dans
     * app/Support/ — pas dans vendor/ — donc :
     *  - aucune installation Composer requise (déployée comme n'importe quel
     *    autre fichier du projet, contrairement à simplesoftwareio/simple-qrcode
     *    qui posait des soucis d'installation sur l'hébergement mutualisé) ;
     *  - AUCUN appel réseau externe (contrairement à une API web tierce) —
     *    aucune donnée client (token de vérification) n'est jamais transmise
     *    à un service tiers non maîtrisé, essentiel en contexte bancaire ;
     *  - moteur SVG choisi (pas GD/Imagick) : ne dépend d'aucune extension
     *    d'image, juste de `xmlwriter` (quasi universellement disponible).
     *
     * Une carte membre doit rester consultable/imprimable MÊME si la
     * génération échoue (cas limite : xmlwriter absent) — on retourne alors
     * une chaîne vide, jamais d'exception qui casserait toute la page.
     */
    private function resolveQrSvgBase64(string $url): string
    {
        try {
            $renderer = new ImageRenderer(
                new RendererStyle(240),
                new SvgImageBackEnd()
            );
            $svg = (new Writer($renderer))->writeString($url);

            return base64_encode($svg);
        } catch (\Throwable $e) {
            Log::error('Génération QR code carte membre impossible : ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Nom affiché sur la carte, tronqué si nécessaire pour tenir sur UNE
     * seule ligne (7.5pt gras majuscules, ~44mm de large disponible dans le
     * bloc infos). Un nom complet trop long (nom + postnom + prénom) fait
     * sinon passer à la ligne, ajoutant ~2,7mm de hauteur — largement assez
     * pour repousser la carte sur une 2e page en impression PDF (marge
     * disponible d'origine : environ 3mm seulement). On coupe à 24
     * caractères avec "…" plutôt que de laisser DomPDF gérer le débordement
     * (support CSS text-overflow/ellipsis peu fiable en PDF).
     */
    private function resolveNomCarteMembre(Client $client): string
    {
        $nomComplet = $client->full_name;
        $limite = 24;

        if (mb_strlen($nomComplet) <= $limite) {
            return $nomComplet;
        }

        return rtrim(mb_substr($nomComplet, 0, $limite - 1)) . '…';
    }

    /**
     * Charge une image locale en data URI base64, sans jamais planter si le
     * fichier est manquant/corrompu/illisible (permissions serveur, etc.) —
     * retourne simplement null, la vue affiche alors un espace vide à la
     * place plutôt que de faire échouer toute la page.
     */
    private function loadImageAsBase64(?string $path, string $mimeParDefaut): ?string
    {
        if (!$path) {
            return null;
        }
        try {
            if (!file_exists($path) || !is_readable($path)) {
                return null;
            }
            $contenu = file_get_contents($path);
            if ($contenu === false) {
                return null;
            }
            return 'data:' . (mime_content_type($path) ?: $mimeParDefaut) . ';base64,' . base64_encode($contenu);
        } catch (\Throwable $e) {
            Log::error("Lecture image impossible ({$path}) : " . $e->getMessage());
            return null;
        }
    }

    /**
     * Affiche un aperçu HTML de la carte membre (sans marquer comme imprimée)
     * avec un bouton "Imprimer" qui appelle la route d'impression PDF.
     */
    public function apercu(Client $client)
    {
        if ($this->isMobileGuichet()) {
            abort(403, "L'impression de documents n'est pas autorisée depuis un guichet mobile.");
        }

        $carte = ClientCarte::where('client_matricule', $client->matricule)
            ->where('statut', '!=', ClientCarte::REVOQUEE)
            ->latest('id')
            ->first();

        if (!$carte) {
            return redirect()
                ->route('clients.show', $client->matricule)
                ->with('error', "Aucune carte membre payée n'a été trouvée pour ce client. Veuillez d'abord encaisser le frais via la Caisse.");
        }

        $verificationUrl = route('clients.carte-membre.verifier', $carte->token_verification);

        try {
            $signaturePath = \App\Models\Administration\ParametreGeneral::get(
                \App\Models\Administration\ParametreGeneral::SIGNATURE_GERANT
            );

            $photoBase64 = $client->photo
                ? $this->loadImageAsBase64(base_path('images_projet/' . ltrim($client->photo, '/')), 'image/jpeg')
                : null;

            $signatureBase64 = $signaturePath
                ? $this->loadImageAsBase64(base_path('images_projet/' . ltrim($signaturePath, '/')), 'image/png')
                : null;

            $logoBase64 = $this->loadImageAsBase64(
                base_path('public/dist/img/vrailogoeben-removebg-preview.png'),
                'image/png'
            );

            $qrSvg = $this->resolveQrSvgBase64($verificationUrl);

            $bgGradientBase64 = $this->buildGradientPngBase64(620, 400);
        } catch (\Throwable $e) {
            Log::error("Aperçu carte membre impossible pour {$client->matricule} : " . $e->getMessage());
            return redirect()
                ->route('clients.show', $client->matricule)
                ->with('error', "L'aperçu de la carte membre n'a pas pu être généré (problème technique). Veuillez réessayer ou contacter l'administrateur si le problème persiste.");
        }

        return view('clients.carte_membre_apercu', [
            'client' => $client,
            'nomCarteMembre' => $this->resolveNomCarteMembre($client),
            'carte' => $carte,
            'verificationUrl' => $verificationUrl,
            'photoBase64' => $photoBase64,
            'signatureBase64' => $signatureBase64,
            'logoBase64' => $logoBase64,
            'qrSvgBase64' => $qrSvg,
            'bgGradientBase64' => $bgGradientBase64,
            'signaturePath' => $signaturePath,
        ]);
    }

    /**
     * Génère et affiche la carte membre (impression PDF), et marque la carte comme imprimée.
     */
    public function imprimer(Request $request, Client $client)
    {
        if ($this->isMobileGuichet()) {
            abort(403, "L'impression de documents n'est pas autorisée depuis un guichet mobile.");
        }

        $carte = ClientCarte::where('client_matricule', $client->matricule)
            ->where('statut', '!=', ClientCarte::REVOQUEE)
            ->latest('id')
            ->first();

        if (!$carte) {
            abort(404, "Aucune carte membre payée n'a été trouvée pour ce client.");
        }

        $verificationUrl = route('clients.carte-membre.verifier', $carte->token_verification);

        try {
            $signaturePath = \App\Models\Administration\ParametreGeneral::get(
                \App\Models\Administration\ParametreGeneral::SIGNATURE_GERANT
            );

            $photoBase64 = $client->photo
                ? $this->loadImageAsBase64(base_path('images_projet/' . ltrim($client->photo, '/')), 'image/jpeg')
                : null;

            $signatureBase64 = $signaturePath
                ? $this->loadImageAsBase64(base_path('images_projet/' . ltrim($signaturePath, '/')), 'image/png')
                : null;

            $logoBase64 = $this->loadImageAsBase64(
                base_path('public/dist/img/vrailogoeben-removebg-preview.png'),
                'image/png'
            );

            $qrSvgBase64 = $this->resolveQrSvgBase64($verificationUrl);

            $bgGradientBase64 = $this->buildGradientPngBase64(620, 400);

            $viewData = [
                'client' => $client,
                'nomCarteMembre' => $this->resolveNomCarteMembre($client),
                'carte' => $carte,
                'verificationUrl' => $verificationUrl,
                'photoBase64' => $photoBase64,
                'signatureBase64' => $signatureBase64,
                'logoBase64' => $logoBase64,
                'qrSvgBase64' => $qrSvgBase64,
                'bgGradientBase64' => $bgGradientBase64,
            ];

            $html = view('impressions.clients.carte_membre_carte', $viewData)->render();
            $pdf = Pdf::loadHTML($html)
                ->setPaper([0, 0, 242.65, 152.72], 'portrait')
                ->setOptions(['font_height_ratio' => 1.0], true);

            // Le PDF est rendu AVANT de marquer la carte "imprimée" : si la
            // génération échoue, le statut de la carte reste intact (pas de
            // fausse "impression" enregistrée pour un PDF qui n'a jamais existé).
            $pdf->render();
        } catch (\Throwable $e) {
            Log::error("Impression carte membre impossible pour {$client->matricule} : " . $e->getMessage());
            return redirect()
                ->route('clients.show', $client->matricule)
                ->with('error', "L'impression de la carte membre a échoué (problème technique). Veuillez réessayer ou contacter l'administrateur si le problème persiste.");
        }

        if ($carte->statut === ClientCarte::PAYEE) {
            $user = Auth::user();
            $carte->update([
                'statut' => ClientCarte::IMPRIMEE,
                'imprimee_le' => now(),
                'imprimee_par_matricule' => $user?->agent_matricule,
            ]);

            ActivityLog::record(
                'CLIENTS',
                'CARTE_MEMBRE_IMPRIMEE',
                $carte,
                (string) $carte->id,
                "Carte membre imprimée pour {$client->full_name} ({$client->matricule})"
            );
        }

        $outputMode = strtolower((string) $request->input('output', 'stream'));
        $filename = 'Carte_membre_' . $client->matricule . '_pvc.pdf';

        if ($request->boolean('download') || $outputMode === 'download') {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    /**
     * Page de vérification publique (scannée via le QR de la carte) — réservée
     * aux utilisateurs authentifiés de l'application, n'expose jamais le solde
     * ni le numéro de compte du client.
     */
    public function verifier(string $token)
    {
        $carte = ClientCarte::with('client')->where('token_verification', $token)->first();

        if (!$carte) {
            abort(404, 'Carte introuvable.');
        }

        return view('clients.carte_membre_verification', [
            'carte' => $carte,
            'client' => $carte->client,
        ]);
    }
}
