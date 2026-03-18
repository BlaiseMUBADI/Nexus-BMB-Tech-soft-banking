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
                'code' => 'EBEN-PER110',
                'nom' => 'Voir rapport journalier caisse/guichet',
                'description' => 'Consulter le journal des operations et le rapport journalier caisse/guichet',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $this->copyRoleMappings('EBEN-PER10', 'EBEN-PER110', $now);
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
            ->where('permission_code', 'EBEN-PER110')
            ->delete();

        DB::table('tb_permissions')
            ->where('code', 'EBEN-PER110')
            ->delete();
    }
};
