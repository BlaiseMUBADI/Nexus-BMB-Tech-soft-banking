<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dossierId = 69;
echo "=== Vérification de l'échéancier pour le dossier ID: {$dossierId} ===\n";

$dossier = DB::table('tb_credit_demandes')->where('id', $dossierId)->first();
if (!$dossier) {
    echo "Dossier introuvable.\n";
    exit;
}

$echeancier = DB::table('tb_credit_echeanciers')->where('credit_demande_id', $dossierId)->first();
if ($echeancier) {
    echo "L'échéancier existe (ID: {$echeancier->id}).\n";
    
    // Vérifier s'il y a des échéances
    $count = DB::table('tb_credit_echeances')->where('echeancier_id', $echeancier->id)->count();
    echo "Nombre d'échéances : {$count}\n";
    
    if ($count == 0) {
        echo "ERREUR : L'échéancier est vide !\n";
    }
} else {
    echo "ERREUR : Aucun échéancier trouvé pour ce dossier !\n";
    echo "Le dossier doit être réinitialisé correctement via l'interface d'administration ou un script de création d'échéancier.\n";
}