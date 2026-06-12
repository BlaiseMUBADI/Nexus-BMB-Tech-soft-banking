<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dossierId = 81;

// 1. Supprimer les échéances
$deletedEcheances = DB::table('tb_credit_echeances')
    ->whereIn('echeancier_id', function($q) use ($dossierId) {
        $q->select('id')->from('tb_credit_echeanciers')->where('credit_demande_id', $dossierId);
    })
    ->delete();

// 2. Supprimer l'échéancier
$deletedEcheanciers = DB::table('tb_credit_echeanciers')
    ->where('credit_demande_id', $dossierId)
    ->delete();

echo "Échéances supprimées : {$deletedEcheances}\n";
echo "Échéanciers supprimés : {$deletedEcheanciers}\n";
echo "Nettoyage terminé. Vous pouvez maintenant relancer le déblocage.\n";