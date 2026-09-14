<?php

namespace App\Services\Images;

/**
 * Suppression automatique du fond d'une image de signature, quelle que soit
 * la couleur d'encre (noire, bleue, etc.) ou la couleur de fond (blanc, gris
 * clair, etc.) : le fond devient transparent (canal alpha). Le tracé de
 * l'encre est repeint en BLANC pur par défaut (quelle que soit sa couleur
 * d'origine), pour s'intégrer directement à la carte membre (fond vert
 * foncé) sans dépendre d'un filtre CSS — DomPDF (génération du PDF de la
 * carte) ne supporte pas la propriété CSS `filter`, contrairement à un
 * navigateur.
 *
 * Algorithme :
 *  1. Estimation de la couleur de fond par échantillonnage d'un anneau de
 *     pixels sur les bords de l'image (on suppose que l'encre ne touche pas
 *     les bords, hypothèse valable pour une signature centrée/recadrée).
 *  2. Pour chaque pixel, distance euclidienne RGB à cette couleur de fond.
 *  3. Alpha = 0 si distance <= seuilBas (fond), 255 si distance >= seuilHaut
 *     (encre), rampe linéaire lissée entre les deux (anti-aliasing propre
 *     sur les bords du tracé plutôt qu'un contour dur/pixelisé).
 */
class SignatureProcessor
{
    /**
     * @param string $sourcePath     Chemin de l'image source (jpg/png/gif)
     * @param string $destPath       Chemin de sortie (toujours écrit en PNG avec alpha)
     * @param int    $sensibilite    0-100 : plus haut = fond retiré plus agressivement
     * @param bool   $recolorerBlanc Si true (défaut), le tracé est repeint en blanc pur ;
     *                               si false, la couleur d'encre d'origine est conservée.
     */
    public static function removeBackground(
        string $sourcePath,
        string $destPath,
        int $sensibilite = 50,
        bool $recolorerBlanc = true
    ): void {
        $data = @file_get_contents($sourcePath);
        if ($data === false) {
            throw new \RuntimeException("Impossible de lire l'image source : {$sourcePath}");
        }

        $src = @imagecreatefromstring($data);
        if (!$src) {
            throw new \RuntimeException('Format image non supporté ou fichier corrompu.');
        }

        $width  = imagesx($src);
        $height = imagesy($src);

        // Image de destination en vraies couleurs + canal alpha
        $dest = imagecreatetruecolor($width, $height);
        imagesavealpha($dest, true);
        imagealphablending($dest, false);
        // Fond transparent par défaut
        $transparent = imagecolorallocatealpha($dest, 0, 0, 0, 127);
        imagefill($dest, 0, 0, $transparent);

        // Normaliser la source en vraies couleurs pour un échantillonnage fiable
        $srcTrue = imagecreatetruecolor($width, $height);
        imagealphablending($srcTrue, false);
        imagesavealpha($srcTrue, true);
        imagecopy($srcTrue, $src, 0, 0, 0, 0, $width, $height);

        // 1. Estimation de la couleur de fond via un anneau de bord
        [$bgR, $bgG, $bgB] = self::estimerCouleurFond($srcTrue, $width, $height);

        // 2. Seuils dérivés de la sensibilité (0-100) : plus la sensibilité est
        //    haute, plus la rampe fond/encre se resserre autour d'une distance
        //    plus faible (on retire plus de fond, y compris les fonds légèrement
        //    non-uniformes : ombres de scan, papier jauni, etc.)
        $sensibilite = max(0, min(100, $sensibilite));
        $seuilBas  = 60 - ($sensibilite * 0.45);   // ~15 à ~60
        $seuilHaut = $seuilBas + 35;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($srcTrue, $x, $y);
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;
                $aOrig = 127 - (($rgba >> 24) & 0x7F); // GD alpha inversé -> 0..127 => 0..127 opaque
                $aOrig = (int) round(($aOrig / 127) * 255);

                $distance = sqrt((($r - $bgR) ** 2) + (($g - $bgG) ** 2) + (($b - $bgB) ** 2));

                if ($distance <= $seuilBas) {
                    $alpha = 0;
                } elseif ($distance >= $seuilHaut) {
                    $alpha = 255;
                } else {
                    $alpha = (int) round((($distance - $seuilBas) / ($seuilHaut - $seuilBas)) * 255);
                }

                $alpha = min($alpha, $aOrig ?: 255);

                if ($alpha > 0) {
                    $gdAlpha = 127 - (int) round(($alpha / 255) * 127);
                    // Le tracé est repeint en blanc pur (peu importe l'encre
                    // d'origine : noire, bleue…) pour s'intégrer directement
                    // à la carte membre (fond vert foncé) sans dépendre d'un
                    // filtre CSS — DomPDF (génération du PDF de la carte) ne
                    // supporte pas la propriété CSS `filter`.
                    $couleur = $recolorerBlanc ? [255, 255, 255] : [$r, $g, $b];
                    $color = imagecolorallocatealpha($dest, $couleur[0], $couleur[1], $couleur[2], $gdAlpha);
                    imagesetpixel($dest, $x, $y, $color);
                }
            }
        }

        imagepng($dest, $destPath, 9);

        imagedestroy($src);
        imagedestroy($srcTrue);
        imagedestroy($dest);
    }

    /**
     * Moyenne des pixels sur un anneau de bord (5% de la plus petite dimension,
     * mini 4px, maxi 25px) : hypothèse que l'encre ne touche pas les bords.
     */
    private static function estimerCouleurFond($image, int $width, int $height): array
    {
        $bordure = (int) max(4, min(25, round(min($width, $height) * 0.05)));

        $sommeR = 0; $sommeG = 0; $sommeB = 0; $n = 0;

        $echantillonner = function ($x, $y) use ($image, &$sommeR, &$sommeG, &$sommeB, &$n) {
            $rgba = imagecolorat($image, $x, $y);
            $sommeR += ($rgba >> 16) & 0xFF;
            $sommeG += ($rgba >> 8) & 0xFF;
            $sommeB += $rgba & 0xFF;
            $n++;
        };

        for ($x = 0; $x < $width; $x += max(1, intdiv($width, 80))) {
            for ($yy = 0; $yy < $bordure; $yy++) {
                $echantillonner($x, $yy);
                $echantillonner($x, $height - 1 - $yy);
            }
        }
        for ($y = 0; $y < $height; $y += max(1, intdiv($height, 80))) {
            for ($xx = 0; $xx < $bordure; $xx++) {
                $echantillonner($xx, $y);
                $echantillonner($width - 1 - $xx, $y);
            }
        }

        if ($n === 0) {
            return [255, 255, 255];
        }

        return [(int) round($sommeR / $n), (int) round($sommeG / $n), (int) round($sommeB / $n)];
    }
}
