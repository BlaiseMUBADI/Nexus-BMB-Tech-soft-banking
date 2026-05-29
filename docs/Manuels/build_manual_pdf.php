<?php
/**
 * Génère MANUEL_UTILISATEUR.pdf à partir de MANUEL_UTILISATEUR.html
 * via Chrome / Edge en mode headless (rendu fidèle navigateur :
 * dégradés, polices, mise en page A4 professionnelle, numéros de page).
 *
 * Régénère le HTML automatiquement s'il est obsolète.
 *
 * Exécution :
 *   php docs/Manuels/build_manual_pdf.php
 */

$htmlPath = __DIR__ . '/MANUEL_UTILISATEUR.html';
$pdfPath  = __DIR__ . '/MANUEL_UTILISATEUR.pdf';
$mdPath   = __DIR__ . '/MANUEL_UTILISATEUR.md';

if (!is_file($htmlPath) || (is_file($mdPath) && filemtime($mdPath) > @filemtime($htmlPath))) {
    echo "Régénération du HTML...\n";
    require __DIR__ . '/build_manual_html.php';
}

// --- Localisation du navigateur (Chrome prioritaire, sinon Edge) ----------
$candidates = [
    'C:\Program Files\Google\Chrome\Application\chrome.exe',
    'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
    'C:\Program Files\Microsoft\Edge\Application\msedge.exe',
    'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe',
];
$browser = null;
foreach ($candidates as $c) {
    if (is_file($c)) { $browser = $c; break; }
}
if (!$browser) {
    fwrite(STDERR, "Aucun navigateur Chrome/Edge trouvé.\n");
    exit(1);
}
echo "Navigateur : {$browser}\n";

// --- Préparation des chemins -----------------------------------------------
$fileUrl  = 'file:///' . str_replace('\\', '/', $htmlPath);
$userData = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'chrome_pdf_' . bin2hex(random_bytes(4));
@mkdir($userData);

if (is_file($pdfPath)) { @unlink($pdfPath); }

// --- Construction de la commande -------------------------------------------
$args = [
    escapeshellarg($browser),
    '--headless=new',
    '--disable-gpu',
    '--no-sandbox',
    '--hide-scrollbars',
    '--run-all-compositor-stages-before-draw',
    '--virtual-time-budget=10000',
    '--user-data-dir=' . escapeshellarg($userData),
    '--no-pdf-header-footer',
    '--print-to-pdf=' . escapeshellarg($pdfPath),
    escapeshellarg($fileUrl),
];
$cmd = implode(' ', $args) . ' 2>&1';

echo "Génération du PDF...\n";
$t0 = microtime(true);
exec($cmd, $out, $rc);
$dt = round(microtime(true) - $t0, 2);

@array_map('unlink', glob($userData . '/*') ?: []);
@rmdir($userData);

if (!is_file($pdfPath) || filesize($pdfPath) < 1024) {
    fwrite(STDERR, "Échec de génération PDF (code {$rc}).\n");
    fwrite(STDERR, implode("\n", $out) . "\n");
    exit(2);
}

echo "PDF écrit : {$pdfPath}\n";
echo "Taille   : " . number_format(filesize($pdfPath)) . " octets\n";
echo "Durée    : {$dt}s\n";
