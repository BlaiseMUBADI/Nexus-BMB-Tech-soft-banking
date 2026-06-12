<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$result = DB::select("SHOW COLUMNS FROM tb_credit_demandes LIKE 'statut_global'");
if (!empty($result)) {
    echo "Type de la colonne statut_global : " . $result[0]->Type . "\n";
} else {
    echo "Colonne non trouvée.\n";
}