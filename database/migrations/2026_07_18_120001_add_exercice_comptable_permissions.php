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
                'code' => 'EBEN-PER116',
                'nom' => "Proposer la clôture d'un exercice comptable",
                'description' => "Préparer et proposer la clôture d'un exercice comptable (nécessite validation par un Gérant/Directeur)",
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER117',
                'nom' => "Valider/rejeter la clôture d'un exercice comptable",
                'description' => "Valider définitivement ou rejeter une proposition de clôture d'exercice comptable — action irréversible une fois validée",
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // EBEN-PER116 (proposer) → rôle Comptable (EBEN-ROL7)
        DB::table('tb_role_permission')->insertOrIgnore([
            'role_code' => 'EBEN-ROL7',
            'permission_code' => 'EBEN-PER116',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // EBEN-PER117 (valider/rejeter) → rôles Gérant (EBEN-ROL12) et Directeur (EBEN-ROL3)
        foreach (['EBEN-ROL12', 'EBEN-ROL3'] as $roleCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => $roleCode,
                'permission_code' => 'EBEN-PER117',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tb_role_permission')->whereIn('permission_code', ['EBEN-PER116', 'EBEN-PER117'])->delete();
        DB::table('tb_permissions')->whereIn('code', ['EBEN-PER116', 'EBEN-PER117'])->delete();
    }
};
