<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dossierNum = 'CRD-EBEN-2026-00030';
echo "=== Correction du solde RMB pour le dossier : {$dossierNum} ===\n";

$dossier = DB::table('tb_credit_demandes')->where('numero_dossier', $dossierNum)->first();
if (!$dossier) {
    echo "Dossier introuvable.\n";
    exit;
}

$compte = DB::table('tb_comptes')
    ->where('client_matricule', $dossier->client_matricule)
    ->where('type', 'RMB')
    ->where('devise', $dossier->devise)
    ->first();

if ($compte) {
    echo "Solde RMB actuel : {$compte->solde_reel} {$compte->devise}\n";
    echo "Code compte : {$compte->code_compte}\n";
    
    // Utiliser code_compte pour la mise à jour
    DB::table('tb_comptes')
        ->where('code_compte', $compte->code_compte)
        ->update(['solde_reel' => 0]);
    
    echo "✅ Solde RMB corrigé à 0,00 {$compte->devise}\n";
} else {
    echo "Compte RMB introuvable pour ce client.\n";
}

echo "\n=== Correction terminée ===\n";