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
                'code' => 'EBEN-PER109',
                'nom' => 'Remboursements Caisse/Guichet',
                'description' => 'Consulter la liste des dossiers crédit en cours de remboursement depuis le module Caisse',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Copier les mappings de rôle depuis EBEN-PER65 (Remboursement Credit)
        $this->copyRoleMappings('EBEN-PER65', 'EBEN-PER109', $now);
    }

    private function copyRoleMappings(string $sourcePermission, string $targetPermission, $now): void
    {
        $roleCodes = DB::table('tb_role_permission')
            ->where('permission_code', $sourcePermission)
            ->pluck('role_code');

        foreach ($roleCodes as $roleCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => $roleCode,
                'permission_code' => $targetPermission,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tb_role_permission')
            ->where('permission_code', 'EBEN-PER109')
            ->delete();

        DB::table('tb_permissions')
            ->where('code', 'EBEN-PER109')
            ->delete();
    }
};
