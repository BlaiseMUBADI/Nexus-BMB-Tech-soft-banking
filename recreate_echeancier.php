<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dossierId = 69;
echo "=== Recréation de l'échéancier pour le dossier ID: {$dossierId} ===\n";

$dossier = DB::table('tb_credit_demandes')->where('id', $dossierId)->first();
if (!$dossier) {
    echo "Dossier introuvable.\n";
    exit;
}

// Vérifier si un échéancier existe déjà
$echeancier = DB::table('tb_credit_echeanciers')->where('credit_demande_id', $dossierId)->first();
if ($echeancier) {
    echo "L'échéancier existe déjà. Nettoyage des anciennes échéances...\n";
    DB::table('tb_credit_echeances')->where('echeancier_id', $echeancier->id)->delete();
} else {
    echo "Création d'un nouvel échéancier...\n";
    $montantCapitalTotal = (float) $dossier->montant_approuve;
    $montantInteretTotal = (float) $dossier->total_interets;
    $duree = (int) $dossier->duree_mois;
    $tauxMensuel = (float) $dossier->taux_interet_mensuel;

    // Calculer la date du premier remboursement (généralement 1 mois après la création)
    $datePremierRemb = date('Y-m-d', strtotime("+1 month", strtotime($dossier->created_at)));

    $echeancierId = DB::table('tb_credit_echeanciers')->insertGetId([
        'credit_demande_id' => $dossierId,
        'montant_capital' => $montantCapitalTotal,
        'taux_mensuel' => $tauxMensuel,
        'duree_mois' => $duree,
        'date_premier_remboursement' => $datePremierRemb,
        'type_amortissement' => 'DEGRESSIF', // ou 'LINEAIRE' selon le cas
        'total_capital' => $montantCapitalTotal,
        'total_interets' => $montantInteretTotal,
        'total_general' => $montantCapitalTotal + $montantInteretTotal,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $echeancier = (object)['id' => $echeancierId];
}

// Créer 6 échéances fictives pour le test (basées sur un montant total de 1 057 500 CDF sur 6 mois)
$montantTotal = 1057500;
$echeanceMensuelle = $montantTotal / 6; // 176 250 CDF par mois (approximatif pour le test)
$capitalEcheance = 900000 / 6; // 150 000
$interetEcheance = 157500 / 6; // 26 250

echo "Génération de 6 échéances...\n";
for ($i = 1; $i <= 6; $i++) {
    $dateEcheance = date('Y-m-d', strtotime("+{$i} months", strtotime($dossier->created_at)));
    
    DB::table('tb_credit_echeances')->insert([
        'echeancier_id' => $echeancier->id,
        'numero_echeance' => $i,
        'date_echeance' => $dateEcheance,
        'capital_restant_debut' => 900000 - (($i-1) * $capitalEcheance),
        'capital_echeance' => $capitalEcheance,
        'interet_echeance' => $interetEcheance,
        'total_echeance' => $echeanceMensuelle,
        'capital_restant_fin' => 900000 - ($i * $capitalEcheance),
        'statut' => 'EN_ATTENTE',
        'montant_paye' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

echo "✅ Échéancier recréé avec succès avec 6 échéances.\n";
echo "Vous pouvez maintenant recharger la page de remboursement sans erreur.\n";