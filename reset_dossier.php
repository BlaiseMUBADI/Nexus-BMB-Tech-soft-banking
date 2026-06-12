<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dossierNum = 'CRD-EBEN-2026-00030';

echo "=== Nettoyage complet du dossier : {$dossierNum} ===\n";

$dossier = DB::table('tb_credit_demandes')->where('numero_dossier', $dossierNum)->first();
if (!$dossier) {
    echo "Dossier introuvable.\n";
    exit;
}

echo "1. Suppression des enregistrements de remboursement...\n";
$deletedRemb = DB::table('tb_credit_remboursements')->where('credit_demande_id', $dossier->id)->delete();
echo "   -> {$deletedRemb} enregistrements supprimés.\n";

echo "2. Réinitialisation de l'échéancier...\n";
$echeancier = DB::table('tb_credit_echeanciers')->where('credit_demande_id', $dossier->id)->first();
if ($echeancier) {
    $updatedEch = DB::table('tb_credit_echeances')
        ->where('echeancier_id', $echeancier->id)
        ->update([
            'montant_paye' => 0,
            'statut' => 'EN_ATTENTE',
            'date_paiement_effectif' => null,
        ]);
    echo "   -> {$updatedEch} échéances réinitialisées à 'EN_ATTENTE' avec solde 0.\n";
}

echo "3. Réinitialisation du statut du dossier...\n";
DB::table('tb_credit_demandes')->where('id', $dossier->id)->update([
    'statut_global' => 'DEBLOQUE'
]);
echo "   -> Statut remis à 'DEBLOQUE'.\n";

echo "\n=== Nettoyage terminé avec succès ===\n";
echo "Vous pouvez maintenant recommencer le processus de remboursement pour ce dossier depuis le début.\n";