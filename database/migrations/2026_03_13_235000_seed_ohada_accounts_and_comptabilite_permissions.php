<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_plan_comptable')->insertOrIgnore([
            ['numero_compte' => '7061', 'libelle' => 'Commissions sur services bancaires', 'type_compte' => 'PRODUIT'],
            ['numero_compte' => '7071', 'libelle' => 'Produits services guichet', 'type_compte' => 'PRODUIT'],
            ['numero_compte' => '4711', 'libelle' => 'Compte transitoire operations de change', 'type_compte' => 'PASSIF'],
        ]);

        DB::table('tb_permissions')->insertOrIgnore([
            [
                'code' => 'EBEN-PER49',
                'nom' => 'Voir comptabilite',
                'description' => 'Acces au module Comptabilite OHADA',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER50',
                'nom' => 'Journal comptable',
                'description' => 'Consulter le journal des ecritures comptables',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER51',
                'nom' => 'Plan comptable',
                'description' => 'Consulter le plan comptable OHADA',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER52',
                'nom' => 'Grand livre',
                'description' => 'Consulter le grand livre comptable',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        foreach (['EBEN-ROL1', 'EBEN-ROL8'] as $roleCode) {
            foreach (['EBEN-PER49', 'EBEN-PER50', 'EBEN-PER51', 'EBEN-PER52'] as $permCode) {
                DB::table('tb_role_permission')->insertOrIgnore([
                    'role_code' => $roleCode,
                    'permission_code' => $permCode,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        foreach (['EBEN-ROL3', 'EBEN-ROL5'] as $roleCode) {
            foreach (['EBEN-PER49', 'EBEN-PER50', 'EBEN-PER52'] as $permCode) {
                DB::table('tb_role_permission')->insertOrIgnore([
                    'role_code' => $roleCode,
                    'permission_code' => $permCode,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('tb_role_permission')->whereIn('permission_code', [
            'EBEN-PER49', 'EBEN-PER50', 'EBEN-PER51', 'EBEN-PER52',
        ])->delete();

        DB::table('tb_permissions')->whereIn('code', [
            'EBEN-PER49', 'EBEN-PER50', 'EBEN-PER51', 'EBEN-PER52',
        ])->delete();

        DB::table('tb_plan_comptable')->whereIn('numero_compte', ['7061', '7071', '4711'])->delete();
    }
};
