<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$numDossier = 'CRD-EBEN-2026-00030';
echo "=== RESTAURATION du dossier : {$numDossier} ===\n";

// Vérifier si le dossier existe déjà
$existingDossier = DB::table('tb_credit_demandes')->where('numero_dossier', $numDossier)->first();
if ($existingDossier) {
    $dossierId = $existingDossier->id;
    echo "Le dossier existe déjà (ID: {$dossierId}). Mise à jour des données...\n";
    
    DB::table('tb_credit_demandes')->where('id', $dossierId)->update([
        'statut_global' => 'DEBLOQUE',
        'updated_at' => now(),
    ]);
} else {
    // 1. Recréer la demande de crédit
    $dossierId = DB::table('tb_credit_demandes')->insertGetId([
        'numero_dossier' => $numDossier,
        'client_matricule' => 'CL-EBENKGA-26-00009',
        'compte_id' => '243-52514-RMB-00184ZKH',
        'portefeuille_id' => 3,
        'code_zone' => 'ZON-EBENKGA-26-00002',
        'agent_createur_matricule' => 'AG-CRD-TST-0001',
        'agent_analyse_matricule' => 'AG-CRD-TST-0003',
        'montant_demande' => 900000.00,
        'devise' => 'CDF',
        'duree_mois' => 6,
        'taux_interet_mensuel' => 5.0000,
        'type_credit' => 'INDIVIDUEL',
        'objet_credit' => 'Investissement commerce textiles importés',
        'garantie_description' => 'Stocks entrepôt (inventaire) + caution commerciale',
        'montant_approuve' => 900000.00,
        'montant_total_echeances' => 1057500.00,
        'total_interets' => 157500.00,
        'statut_global' => 'DEBLOQUE',
        'created_at' => '2026-04-01 10:52:13',
        'updated_at' => now(),
    ]);
    echo "1. Demande de crédit recréée (ID: {$dossierId})\n";
}

// Nettoyer les données existantes pour éviter les doublons
DB::table('tb_credit_analyses')->where('credit_demande_id', $dossierId)->delete();
DB::table('tb_credit_validations')->where('credit_demande_id', $dossierId)->delete();
DB::table('tb_credit_deblocages')->where('credit_demande_id', $dossierId)->delete();

$echeancier = DB::table('tb_credit_echeanciers')->where('credit_demande_id', $dossierId)->first();
if ($echeancier) {
    DB::table('tb_credit_echeances')->where('echeancier_id', $echeancier->id)->delete();
    DB::table('tb_credit_echeanciers')->where('id', $echeancier->id)->delete();
}

// 2. Recréer l'analyse
DB::table('tb_credit_analyses')->insert([
    'credit_demande_id' => $dossierId,
    'analyseur_matricule' => 'AG-CRD-TST-0003',
    'revenu_mensuel_verifie' => 300000.00,
    'capacite_remboursement' => 150000.00,
    'ratio_endettement' => 30.00,
    'score_risque' => 'MOYEN',
    'recommandation' => 'FAVORABLE',
    'montant_recommande' => 900000.00,
    'statut' => 'COMPLETE',
    'complete_le' => '2026-04-01 12:00:00',
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "2. Analyse recréée\n";

// 3. Recréer les validations (simplifié pour le test)
$validateurs = ['AGENT_CREDIT', 'CHARGE_OPERATIONS', 'CONTROLEUR', 'GERANT'];
foreach ($validateurs as $i => $type) {
    DB::table('tb_credit_validations')->insert([
        'credit_demande_id' => $dossierId,
        'type_validateur' => $type,
        'ordre_etape' => $i + 1,
        'validateur_matricule' => 'AG-CRD-TST-000' . ($i + 1),
        'decision' => 'APPROUVE',
        'observations' => 'Validé pour test',
        'valide_le' => '2026-04-01 14:00:00',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
echo "3. Validations recréées\n";

// 4. Recréer le déblocage (pour restaurer la caution GTC de 180 000 CDF)
DB::table('tb_credit_deblocages')->insert([
    'credit_demande_id' => $dossierId,
    'agent_matricule' => 'AG-CRD-TST-0001',
    'compte_debit_id' => '243-52514-GTC-00184ZKH', // Compte caution débité
    'compte_credit_id' => '243-52514-RMB-00184ZKH', // Compte RMB crédité
    'montant_debloque' => 900000.00,
    'devise' => 'CDF',
    'frais_dossier' => 0.00,
    'montant_net_verse' => 900000.00,
    'numero_ordre' => 'ORD-TEST-001',
    'observations' => 'Déblocage initial pour test',
    'debloque_le' => '2026-04-02 10:00:00',
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "4. Déblocage recréé (Caution GTC débitée)\n";

// 5. Recréer l'échéancier et les 6 échéances
$echeancierId = DB::table('tb_credit_echeanciers')->insertGetId([
    'credit_demande_id' => $dossierId,
    'montant_capital' => 900000.00,
    'taux_mensuel' => 5.0000,
    'duree_mois' => 6,
    'date_premier_remboursement' => '2026-05-02',
    'type_amortissement' => 'DEGRESSIF',
    'total_capital' => 900000.00,
    'total_interets' => 157500.00,
    'total_general' => 1057500.00,
    'created_at' => now(),
    'updated_at' => now(),
]);

$capitalEcheance = 900000 / 6; // 150 000
$interetEcheance = 157500 / 6; // 26 250
$totalEcheance = $capitalEcheance + $interetEcheance; // 176 250
$capitalRestant = 900000;

for ($i = 1; $i <= 6; $i++) {
    $capitalRestantDebut = $capitalRestant;
    $capitalRestant -= $capitalEcheance;
    $dateEcheance = date('Y-m-d', strtotime("+{$i} months", strtotime('2026-04-01')));
    
    DB::table('tb_credit_echeances')->insert([
        'echeancier_id' => $echeancierId,
        'numero_echeance' => $i,
        'date_echeance' => $dateEcheance,
        'capital_restant_debut' => $capitalRestantDebut,
        'capital_echeance' => $capitalEcheance,
        'interet_echeance' => $interetEcheance,
        'total_echeance' => $totalEcheance,
        'capital_restant_fin' => $capitalRestant,
        'statut' => 'EN_ATTENTE',
        'montant_paye' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
echo "5. Échéancier recréé avec 6 échéances de 176 250 CDF.\n";

// 6. Remettre les comptes à leur état initial
DB::table('tb_comptes')->where('code_compte', '243-52514-GTC-00184ZKH')->update([
    'solde_reel' => 180000.00,
    'solde_bloque' => 180000.00
]);
DB::table('tb_comptes')->where('code_compte', '243-52514-RMB-00184ZKH')->update([
    'solde_reel' => 0.00
]);
echo "6. Comptes RMB et GTC restaurés.\n";

echo "\n=== RESTAURATION TERMINÉE AVEC SUCCÈS ===\n";
echo "Le dossier {$numDossier} est de nouveau disponible et prêt pour vos tests de remboursement.\n";