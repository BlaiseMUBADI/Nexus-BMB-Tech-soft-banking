<?php
/**
 * DIAGNOSTIC TEMPORAIRE — À SUPPRIMER APRÈS UTILISATION
 * URL : https://coopaeben.info/diagnostic.php?k=EBENCLEAR2026
 */
if (($_GET['k'] ?? '') !== 'EBENCLEAR2026') {
    http_response_code(403);
    die('Accès refusé.');
}

header('Content-Type: text/plain; charset=utf-8');
echo "=== DIAGNOSTIC SERVEUR ===\n\n";

// 1. Version PHP
echo "PHP : " . PHP_VERSION . "\n";
echo "SAPI : " . php_sapi_name() . "\n\n";

// 2. Document Root
echo "DOCUMENT_ROOT : " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "SCRIPT_FILENAME : " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "\n";
echo "cwd : " . getcwd() . "\n\n";

// 3. Vérifier que le .htaccess est actif (mod_rewrite)
echo "mod_rewrite : " . (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()) ? 'OUI' : 'non détectable (normal en CGI)') . "\n\n";

// 4. Vérifier fichiers critiques
$basePath = dirname(__DIR__);
$checks = [
    'bootstrap/app.php',
    'vendor/autoload.php',
    'routes/web.php',
    'routes/tresorerie.php',
    'app/Http/Controllers/Tresorerie/TresorerieController.php',
    'app/Models/Caisse/CaissesGuichet.php',
    'app/Models/Caisse/CaissesGuichetSolde.php',
    'app/Models/Caisse/ClotureCaisse.php',
    'app/Models/Caisse/MouvementInterCaisse.php',
    'app/Models/Tresorerie/Devise.php',
    'app/Http/Middleware/CheckPermission.php',
    'resources/views/tresorerie/coffre.blade.php',
    'resources/views/tresorerie/agents_mobiles.blade.php',
    'resources/views/layouts/sidebar.blade.php',
    'public/.htaccess',
];

echo "=== Fichiers critiques ===\n";
foreach ($checks as $file) {
    $full = $basePath . '/' . $file;
    $exists = file_exists($full);
    $size = $exists ? filesize($full) : 0;
    // Check BOM
    $hasBom = false;
    if ($exists && $size >= 3) {
        $h = fopen($full, 'rb');
        $first3 = fread($h, 3);
        fclose($h);
        $hasBom = ($first3 === "\xEF\xBB\xBF");
    }
    $status = $exists ? "OK ({$size}o)" : "*** MANQUANT ***";
    if ($hasBom) $status .= " [BOM!]";
    echo "  " . ($exists ? "✓" : "✗") . " {$file} — {$status}\n";
}

// 5. Vérifier cache bootstrap
echo "\n=== Cache bootstrap ===\n";
$cacheDir = $basePath . '/bootstrap/cache/';
if (is_dir($cacheDir)) {
    foreach (glob($cacheDir . '*.php') as $f) {
        echo "  " . basename($f) . " (" . filesize($f) . "o)\n";
    }
} else {
    echo "  Dossier bootstrap/cache introuvable!\n";
}

// 6. Vérifier storage permissions
echo "\n=== Permissions storage ===\n";
$storageDirs = ['storage', 'storage/framework', 'storage/framework/views', 'storage/framework/cache', 'storage/logs'];
foreach ($storageDirs as $dir) {
    $full = $basePath . '/' . $dir;
    $writable = is_writable($full);
    echo "  " . ($writable ? "✓" : "✗") . " {$dir} — " . ($writable ? "writable" : "*** NON WRITABLE ***") . "\n";
}

// 7. Essayer de booter Laravel et lister les routes tresorerie
echo "\n=== Test Boot Laravel ===\n";
try {
    chdir($basePath);
    require $basePath . '/vendor/autoload.php';
    $app = require $basePath . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $app->boot();

    $router = $app->make('router');
    $routes = collect($router->getRoutes()->getIterator());
    $tresoRoutes = $routes->filter(function ($route) {
        return str_starts_with($route->uri(), 'tresorerie');
    });

    echo "  Routes tresorerie trouvées : " . $tresoRoutes->count() . "\n";
    foreach ($tresoRoutes as $route) {
        $methods = implode('|', $route->methods());
        echo "    {$methods} /{$route->uri()} → {$route->getName()}\n";
    }
} catch (\Throwable $e) {
    echo "  *** ERREUR BOOT: " . $e->getMessage() . "\n";
    echo "  Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== FIN DIAGNOSTIC ===\n";
echo "⚠ SUPPRIMEZ CE FICHIER IMMÉDIATEMENT !\n";
