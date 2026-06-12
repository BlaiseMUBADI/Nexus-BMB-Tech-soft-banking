<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dossierNum = 'CRD-EBEN-2026-00030';
echo "=== SUPPRESSION COMPLÈTE ET SÉCURISÉE du dossier : {$dossierNum} ===\n";

$dossier = DB::table('tb_credit_demandes')->where('numero_dossier', $dossierNum)->first();
if (!$dossier) {
    echo "Dossier introuvable. Rien à supprimer.\n";
    exit;
}

$dossierId = $dossier->id;
echo "Dossier trouvé, ID: {$dossierId}\n";

// 1. Supprimer les déblocages (contrainte de clé étrangère)
$deletedDeblocage = DB::table('tb_credit_deblocages')->where('credit_demande_id', $dossierId)->delete();
echo "1. Déblocages supprimés : {$deletedDeblocage}\n";

// 2. Supprimer les remboursements
$deletedRemb = DB::table('tb_credit_remboursements')->where('credit_demande_id', $dossierId)->delete();
echo "2. Remboursements supprimés : {$deletedRemb}\n";

// 3. Supprimer les échéances
$echeancier = DB::table('tb_credit_echeanciers')->where('credit_demande_id', $dossierId)->first();
if ($echeancier) {
    $deletedEch = DB::table('tb_credit_echeances')->where('echeancier_id', $echeancier->id)->delete();
    echo "3. Échéances supprimées : {$deletedEch}\n";
    
    // 4. Supprimer l'échéancier lui-même
    DB::table('tb_credit_echeanciers')->where('id', $echeancier->id)->delete();
    echo "4. Échéancier supprimé.\n";
}

// 5. Supprimer les analyses et validations associées
$deletedValidations = DB::table('tb_credit_validations')->where('credit_demande_id', $dossierId)->delete();
echo "5. Validations supprimées : {$deletedValidations}\n";

$deletedAnalyses = DB::table('tb_credit_analyses')->where('credit_demande_id', $dossierId)->delete();
echo "6. Analyses supprimées : {$deletedAnalyses}\n";

// 6. Enfin, supprimer le dossier lui-même
DB::table('tb_credit_demandes')->where('id', $dossierId)->delete();
echo "7. Dossier supprimé de la table tb_credit_demandes.\n";

echo "\n=== SUPPRESSION TERMINÉE AVEC SUCCÈS ===\n";
echo "Le dossier {$dossierNum} a été entièrement et proprement effacé du système.\n";