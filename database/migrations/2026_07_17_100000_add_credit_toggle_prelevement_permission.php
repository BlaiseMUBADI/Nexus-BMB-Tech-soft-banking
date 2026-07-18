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
                'code' => 'EBEN-PER113',
                'nom' => 'Modifier prélèvement automatique crédit',
                'description' => 'Activer ou désactiver le prélèvement automatique sur un dossier crédit (sécurité renforcée : action distincte de la simple consultation)',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Copier les mappings depuis EBEN-PER64 (Déblocage crédit)
        // pour que les rôles habilités à débloquer un crédit puissent aussi gérer le prélèvement auto
        $this->copyRoleMappings('EBEN-PER64', 'EBEN-PER113', $now);
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
            ->where('permission_code', 'EBEN-PER113')
            ->delete();

        DB::table('tb_permissions')
            ->where('code', 'EBEN-PER113')
            ->delete();
    }
};
