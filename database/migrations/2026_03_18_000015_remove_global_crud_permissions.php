<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $codes = ['EBEN-PER73', 'EBEN-PER74', 'EBEN-PER75'];

        DB::table('tb_role_permission')->whereIn('permission_code', $codes)->delete();
        DB::table('tb_permissions')->whereIn('code', $codes)->delete();
    }

    public function down(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            [
                'code' => 'EBEN-PER73',
                'nom' => 'Ajouter (CRUD global)',
                'description' => 'Permission globale d ajout sur les modules metiers',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER74',
                'nom' => 'Modifier (CRUD global)',
                'description' => 'Permission globale de modification sur les modules metiers',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER75',
                'nom' => 'Supprimer (CRUD global)',
                'description' => 'Permission globale de suppression sur les modules metiers',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL1', 'permission_code' => 'EBEN-PER73', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL1', 'permission_code' => 'EBEN-PER74', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL1', 'permission_code' => 'EBEN-PER75', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
};
