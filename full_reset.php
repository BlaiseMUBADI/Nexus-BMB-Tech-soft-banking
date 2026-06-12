<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dossierNum = 'CRD-EBEN-2026-00030';
echo "=== RÉINITIALISATION COMPLÈTE du dossier : {$dossierNum} ===\n";

$dossier = DB::table('tb_credit_demandes')->where('numero_dossier', $dossierNum)->first();
if (!$dossier) {
    echo "Dossier introuvable.\n";
    exit;
}

echo "1. Suppression de tous les remboursements...\n";
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

echo "3. Réinitialisation du statut du dossier...\n";
DB::table('tb_credit_demandes')->where('id', $dossier->id)->update([
    'statut_global' => 'DEBLOQUE'
]);
echo "   -> Statut remis à 'DEBLOQUE'.\n";

echo "4. Restauration du compte GTC (Caution)...\n";
$compteGtc = DB::table('tb_comptes')
    ->where('client_matricule', $dossier->client_matricule)
    ->where('type', 'GTC')
    ->where('devise', $dossier->devise)
    ->first();

if ($compteGtc) {
    // On remet la caution bloquée à 20% du montant approuvé (900 000 * 0.20 = 180 000)
    $montantCaution = 180000.00;
    DB::table('tb_comptes')->where('code_compte', $compteGtc->code_compte)->update([
        'solde_reel' => $montantCaution,
        'solde_bloque' => $montantCaution,
    ]);
    echo "   -> Compte GTC restauré avec {$montantCaution} {$compteGtc->devise} (solde réel et bloqué).\n";
} else {
    echo "   -> Aucun compte GTC trouvé pour ce client.\n";
}

echo "5. Nettoyage du compte RMB (mise à 0 pour un nouveau départ)...\n";
$compteRmb = DB::table('tb_comptes')
    ->where('client_matricule', $dossier->client_matricule)
    ->where('type', 'RMB')
    ->where('devise', $dossier->devise)
    ->first();
if ($compteRmb) {
    DB::table('tb_comptes')->where('code_compte', $compteRmb->code_compte)->update(['solde_reel' => 0]);
    echo "   -> Compte RMB remis à 0,00 {$compteRmb->devise}.\n";
}

echo "\n=== RÉINITIALISATION TERMINÉE AVEC SUCCÈS ===\n";
echo "Le dossier est maintenant dans son état initial, prêt pour un nouveau test.\n";