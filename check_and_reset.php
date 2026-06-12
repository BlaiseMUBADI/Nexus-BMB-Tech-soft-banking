<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dossierNum = 'CRD-EBEN-2026-00030';
echo "=== Vérification du dossier : {$dossierNum} ===\n";

$dossier = DB::table('tb_credit_demandes')->where('numero_dossier', $dossierNum)->first();
if (!$dossier) {
    echo "Dossier introuvable.\n";
    exit;
}

echo "Dossier trouvé, ID: {$dossier->id}, Statut: {$dossier->statut_global}\n";

$echeancier = DB::table('tb_credit_echeanciers')->where('credit_demande_id', $dossier->id)->first();
if ($echeancier) {
    echo "Echeancier trouvé, ID: {$echeancier->id}\n";
    $count = DB::table('tb_credit_echeances')->where('echeancier_id', $echeancier->id)->count();
    echo "Nombre d'échéances: {$count}\n";
    
    // Réinitialiser les échéances
    $updated = DB::table('tb_credit_echeances')
        ->where('echeancier_id', $echeancier->id)
        ->update([
            'montant_paye' => 0,
            'statut' => 'EN_ATTENTE',
            'date_paiement_effectif' => null,
        ]);
    echo " -> {$updated} échéances réinitialisées.\n";
} else {
    echo "Aucun échéancier trouvé pour ce dossier.\n";
}

// Remettre le statut à EN_REMBOURSEMENT
DB::table('tb_credit_demandes')->where('id', $dossier->id)->update(['statut_global' => 'EN_REMBOURSEMENT']);
echo " -> Statut remis à 'EN_REMBOURSEMENT'.\n";

echo "\n=== Le dossier est prêt pour un nouveau test ===\n";