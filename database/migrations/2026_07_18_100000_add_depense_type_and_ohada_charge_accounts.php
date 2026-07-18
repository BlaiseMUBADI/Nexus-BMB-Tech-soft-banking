<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Ajouter DEPENSE à l'enum type de tb_transactions ──
        DB::statement("
            ALTER TABLE tb_transactions
            MODIFY COLUMN type
            ENUM('DEPOT','RETRAIT','VIREMENT','REMBOURSEMENT','CHANGE','PAIEMENT','DEPENSE')
            NOT NULL
        ");

        // ── 2. Ajouter les comptes-feuilles mouvementables sous les chapitres de charges (60-69) ──
        $comptes = [
            ['numero' => '6051', 'libelle' => 'Fournitures de bureau',                    'parent' => '605', 'classe' => '6'],
            ['numero' => '605',  'libelle' => 'Autres achats',                            'parent' => '60',  'classe' => '6', 'mov' => false],
            ['numero' => '6111', 'libelle' => 'Transports du personnel',                  'parent' => '61',  'classe' => '6'],
            ['numero' => '6221', 'libelle' => 'Locations et charges locatives',           'parent' => '62',  'classe' => '6'],
            ['numero' => '6241', 'libelle' => 'Entretien, réparations et maintenance',     'parent' => '62',  'classe' => '6'],
            ['numero' => '6281', 'libelle' => 'Frais de télécommunications (tél./internet)', 'parent' => '62', 'classe' => '6'],
            ['numero' => '6251', 'libelle' => "Frais d'électricité et eau",               'parent' => '62',  'classe' => '6'],
            ['numero' => '6411', 'libelle' => 'Impôts et taxes divers',                   'parent' => '64',  'classe' => '6'],
            ['numero' => '6581', 'libelle' => 'Autres charges diverses de gestion courante', 'parent' => '65', 'classe' => '6'],
        ];

        foreach ($comptes as $c) {
            DB::table('tb_plan_comptable')->updateOrInsert(
                ['numero_compte' => $c['numero']],
                [
                    'classe_ohada'        => $c['classe'],
                    'libelle'             => $c['libelle'],
                    'parent_compte'       => $c['parent'],
                    'niveau'              => strlen($c['numero']),
                    'type_compte'         => 'CHARGE',
                    'est_mouvementable'   => $c['mov'] ?? true,
                    'est_actif'           => true,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('tb_plan_comptable')->whereIn('numero_compte', [
            '6051', '605', '6111', '6221', '6241', '6281', '6251', '6411', '6581',
        ])->delete();

        DB::statement("
            ALTER TABLE tb_transactions
            MODIFY COLUMN type
            ENUM('DEPOT','RETRAIT','VIREMENT','REMBOURSEMENT','CHANGE','PAIEMENT')
            NOT NULL
        ");
    }
};
