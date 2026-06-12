<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dossierNum = 'CRD-EBEN-2026-00042';

echo "=== Annulation du déblocage pour le dossier : {$dossierNum} ===\n";

// 1. Trouver le dossier
$dossier = DB::table('tb_credit_demandes')->where('numero_dossier', $dossierNum)->first();
if (!$dossier) {
    echo "Dossier introuvable.\n";
    exit;
}

// 2. Trouver le déblocage
$deblocage = DB::table('tb_credit_deblocages')->where('credit_demande_id', $dossier->id)->first();
if (!$deblocage) {
    echo "Aucun déblocage trouvé pour ce dossier.\n";
    exit;
}

echo "Déblocage trouvé (ID: {$deblocage->id})\n";

// 3. Trouver les transactions liées à ce déblocage (par référence contenant le numéro de dossier)
$pattern = '%' . $dossierNum . '%';
$txs = DB::table('tb_transactions')
    ->where('reference', 'like', $pattern)
    ->get();

echo "Transactions trouvées à annuler : " . $txs->count() . "\n";

foreach ($txs as $tx) {
    echo "  - Annulation TX: {$tx->reference} (Type: {$tx->type}, Montant: {$tx->montant})\n";
    
    // Inverser les effets comptables selon le type
    if ($tx->type === 'DEPOT') {
        if ($tx->compte_code) {
            DB::table('tb_comptes')->where('code_compte', $tx->compte_code)->decrement('solde_reel', $tx->montant);
        }
        if ($tx->guichet_id) {
            DB::table('tb_caisses_guichets_soldes')->where('guichet_id', $tx->guichet_id)->where('devise_code', $tx->devise_code)->decrement('solde_en_caisse', $tx->montant);
        }
    } elseif ($tx->type === 'RETRAIT') {
        if ($tx->compte_code) {
            DB::table('tb_comptes')->where('code_compte', $tx->compte_code)->increment('solde_reel', $tx->montant);
        }
        if ($tx->guichet_id) {
            DB::table('tb_caisses_guichets_soldes')->where('guichet_id', $tx->guichet_id)->where('devise_code', $tx->devise_code)->increment('solde_en_caisse', $tx->montant);
        }
    }
    
    // Supprimer la transaction
    DB::table('tb_transactions')->where('id', $tx->id)->delete();
}

// 4. Supprimer le déblocage
DB::table('tb_credit_deblocages')->where('id', $deblocage->id)->delete();
echo "Enregistrement de déblocage supprimé.\n";

// 5. Remettre le dossier en état "VALIDE" (prêt pour un nouveau déblocage)
DB::table('tb_credit_demandes')->where('id', $dossier->id)->update([
    'statut_global' => 'VALIDE',
]);
echo "Statut du dossier remis à 'VALIDE'.\n";

echo "\n=== Annulation terminée avec succès ===\n";
echo "Vous pouvez maintenant recommencer le processus de déblocage pour ce dossier.\n";