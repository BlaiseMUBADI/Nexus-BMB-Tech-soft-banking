<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            [
                'code' => 'EBEN-PER76',
                'nom' => 'Voir rapport agents terrain',
                'description' => 'Acces au rapport Agents Terrain depuis le menu Clients/Membres',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER77',
                'nom' => 'Ajouter en tresorerie',
                'description' => 'Creation/ajout d operations dans le module tresorerie',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER78',
                'nom' => 'Modifier en tresorerie',
                'description' => 'Modification d operations dans le module tresorerie',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER79',
                'nom' => 'Supprimer en tresorerie',
                'description' => 'Suppression d operations dans le module tresorerie',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('tb_role_permission')->insertOrIgnore(
            array_map(
                fn (int $n) => [
                    'role_code' => 'EBEN-ROL1',
                    'permission_code' => 'EBEN-PER' . $n,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                range(76, 79)
            )
        );

        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL8', 'permission_code' => 'EBEN-PER76', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL8', 'permission_code' => 'EBEN-PER77', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL8', 'permission_code' => 'EBEN-PER78', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL8', 'permission_code' => 'EBEN-PER79', 'created_at' => $now, 'updated_at' => $now],

            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER76', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER76', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL9', 'permission_code' => 'EBEN-PER76', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        $codes = [
            'EBEN-PER76',
            'EBEN-PER77',
            'EBEN-PER78',
            'EBEN-PER79',
        ];

        DB::table('tb_role_permission')->whereIn('permission_code', $codes)->delete();
        DB::table('tb_permissions')->whereIn('code', $codes)->delete();
    }
};
