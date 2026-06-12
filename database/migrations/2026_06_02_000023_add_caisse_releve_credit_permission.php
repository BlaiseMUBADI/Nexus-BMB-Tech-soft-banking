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
                'code' => 'EBEN-PER112',
                'nom' => 'Imprimer relevé crédit',
                'description' => 'Imprimer le relevé de compte crédit (PDF) depuis le module Caisse/Guichet',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Copier les mappings depuis EBEN-PER111 (Remboursements crédit Caisse)
        // pour que les rôles qui ont accès aux remboursements puissent aussi imprimer le relevé
        $this->copyRoleMappings('EBEN-PER111', 'EBEN-PER112', $now);
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
            ->where('permission_code', 'EBEN-PER112')
            ->delete();

        DB::table('tb_permissions')
            ->where('code', 'EBEN-PER112')
            ->delete();
    }
};
