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
                'code' => 'EBEN-PER111',
                'nom' => 'Remboursements crédit (Caisse)',
                'description' => 'Consulter la liste des dossiers en cours de remboursement depuis le module Caisse/Guichet',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Copier les mappings depuis EBEN-PER65 pour que les rôles existants aient aussi la nouvelle permission
        $this->copyRoleMappings('EBEN-PER65', 'EBEN-PER111', $now);
    }

    private function copyRoleMappings(string $source, string $target, $now): void
    {
        $roleCodes = DB::table('tb_role_permission')
            ->where('permission_code', $source)
            ->pluck('role_code');

        foreach ($roleCodes as $roleCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => $roleCode,
                'permission_code' => $target,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tb_role_permission')
            ->where('permission_code', 'EBEN-PER111')
            ->delete();

        DB::table('tb_permissions')
            ->where('code', 'EBEN-PER111')
            ->delete();
    }
};
