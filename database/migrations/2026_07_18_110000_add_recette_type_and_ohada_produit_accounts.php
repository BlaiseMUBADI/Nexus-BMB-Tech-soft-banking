<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Ajouter RECETTE à l'enum type de tb_transactions ──
        DB::statement("
            ALTER TABLE tb_transactions
            MODIFY COLUMN type
            ENUM('DEPOT','RETRAIT','VIREMENT','REMBOURSEMENT','CHANGE','PAIEMENT','DEPENSE','RECETTE')
            NOT NULL
        ");

        // ── 2. Comptes-feuilles mouvementables de produits (classe 7) pour les recettes diverses ──
        $comptes = [
            ['numero' => '7051', 'libelle' => 'Ventes de formulaires et imprimés',        'parent' => '70',  'classe' => '7'],
            ['numero' => '7072', 'libelle' => 'Frais de dossier divers',                   'parent' => '707', 'classe' => '7'],
            ['numero' => '7581', 'libelle' => 'Autres produits divers de gestion courante', 'parent' => '75',  'classe' => '7'],
            ['numero' => '75',   'libelle' => 'Autres produits',                           'parent' => '7',   'classe' => '7', 'mov' => false],
        ];

        foreach ($comptes as $c) {
            DB::table('tb_plan_comptable')->updateOrInsert(
                ['numero_compte' => $c['numero']],
                [
                    'classe_ohada'      => $c['classe'],
                    'libelle'           => $c['libelle'],
                    'parent_compte'     => $c['parent'],
                    'niveau'            => strlen($c['numero']),
                    'type_compte'       => 'PRODUIT',
                    'est_mouvementable' => $c['mov'] ?? true,
                    'est_actif'         => true,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('tb_plan_comptable')->whereIn('numero_compte', ['7051', '7072', '7581', '75'])->delete();

        DB::statement("
            ALTER TABLE tb_transactions
            MODIFY COLUMN type
            ENUM('DEPOT','RETRAIT','VIREMENT','REMBOURSEMENT','CHANGE','PAIEMENT','DEPENSE')
            NOT NULL
        ");
    }
};
