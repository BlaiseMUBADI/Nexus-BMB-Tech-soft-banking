<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dossierNum = 'CRD-EBEN-2026-00030';
echo "=== SUPPRESSION COMPLÈTE du dossier : {$dossierNum} ===\n";

$dossier = DB::table('tb_credit_demandes')->where('numero_dossier', $dossierNum)->first();
if (!$dossier) {
    echo "Dossier introuvable. Rien à supprimer.\n";
    exit;
}

$dossierId = $dossier->id;
echo "Dossier trouvé, ID: {$dossierId}\n";

// 1. Supprimer les remboursements
$deletedRemb = DB::table('tb_credit_remboursements')->where('credit_demande_id', $dossierId)->delete();
echo "1. Remboursements supprimés : {$deletedRemb}\n";

// 2. Supprimer les échéances
$echeancier = DB::table('tb_credit_echeanciers')->where('credit_demande_id', $dossierId)->first();
if ($echeancier) {
    $deletedEch = DB::table('tb_credit_echeances')->where('echeancier_id', $echeancier->id)->delete();
    echo "2. Échéances supprimées : {$deletedEch}\n";
    
    // 3. Supprimer l'échéancier
    DB::table('tb_credit_echeanciers')->where('id', $echeancier->id)->delete();
    echo "3. Échéancier supprimé.\n";
}

// 4. Supprimer le dossier lui-même
DB::table('tb_credit_demandes')->where('id', $dossierId)->delete();
echo "4. Dossier supprimé de la table tb_credit_demandes.\n";

echo "\n=== SUPPRESSION TERMINÉE AVEC SUCCÈS ===\n";
echo "Le dossier {$dossierNum} a été entièrement effacé du système.\n";