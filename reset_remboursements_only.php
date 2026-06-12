<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dossierNum = 'CRD-EBEN-2026-00030';
echo "=== RÉINITIALISATION DES REMBOURSEMENTS pour : {$dossierNum} ===\n";

$dossier = DB::table('tb_credit_demandes')->where('numero_dossier', $dossierNum)->first();
if (!$dossier) {
    echo "Dossier introuvable.\n";
    exit;
}

echo "1. Suppression de tous les enregistrements de remboursement...\n";
$deletedRemb = DB::table('tb_credit_remboursements')->where('credit_demande_id', $dossier->id)->delete();
echo "   -> {$deletedRemb} enregistrements supprimés.\n";

echo "2. Réinitialisation complète de l'échéancier...\n";
$echeancier = DB::table('tb_credit_echeanciers')->where('credit_demande_id', $dossier->id)->first();
if ($echeancier) {
    $updatedEch = DB::table('tb_credit_echeances')
        ->where('echeancier_id', $echeancier->id)
        ->update([
            'montant_paye' => 0,
            'statut' => 'EN_ATTENTE',
            'date_paiement_effectif' => null,
        ]);
    echo "   -> {$updatedEch} échéances réinitialisées à 0 et 'EN_ATTENTE'.\n";
}

echo "3. Nettoyage des comptes pour un départ propre...\n";
$compteGtc = DB::table('tb_comptes')->where('client_matricule', $dossier->client_matricule)->where('type', 'GTC')->where('devise', $dossier->devise)->first();
if ($compteGtc) {
    DB::table('tb_comptes')->where('code_compte', $compteGtc->code_compte)->update(['solde_reel' => 180000, 'solde_bloque' => 180000]);
    echo "   -> Compte GTC (Caution) restauré à 180 000 CDF.\n";
}

$compteRmb = DB::table('tb_comptes')->where('client_matricule', $dossier->client_matricule)->where('type', 'RMB')->where('devise', $dossier->devise)->first();
if ($compteRmb) {
    DB::table('tb_comptes')->where('code_compte', $compteRmb->code_compte)->update(['solde_reel' => 0]);
    echo "   -> Compte RMB remis à 0,00 CDF.\n";
}

echo "4. Mise à jour du statut du dossier...\n";
DB::table('tb_credit_demandes')->where('id', $dossier->id)->update(['statut_global' => 'EN_REMBOURSEMENT']);
echo "   -> Statut confirmé à 'EN_REMBOURSEMENT'.\n";

echo "\n=== RÉINITIALISATION TERMINÉE AVEC SUCCÈS ===\n";
echo "Le dossier est prêt pour un nouveau test de remboursement, sans aucune trace des précédents.\n";