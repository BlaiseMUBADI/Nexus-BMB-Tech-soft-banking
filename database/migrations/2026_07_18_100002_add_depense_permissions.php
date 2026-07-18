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
                'code' => 'EBEN-PER114',
                'nom' => 'Enregistrer une dépense de caisse',
                'description' => 'Saisir une dépense (sortie de caisse) avec catégorie comptable OHADA et justificatif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER115',
                'nom' => 'Gérer les catégories de dépenses',
                'description' => 'Créer/modifier/supprimer les catégories de dépenses et leur mapping vers le plan comptable OHADA',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // EBEN-PER114 : hérité par les rôles ayant déjà la saisie caisse (EBEN-PER11)
        $this->copyRoleMappings('EBEN-PER11', 'EBEN-PER114', $now);
        // EBEN-PER115 : hérité par les rôles ayant déjà la gestion du plan comptable (EBEN-PER51)
        $this->copyRoleMappings('EBEN-PER51', 'EBEN-PER115', $now);
    }

    private function copyRoleMappings(string $source, string $target, $now): void
    {
        $roleCodes = DB::table('tb_role_permission')->where('permission_code', $source)->pluck('role_code');

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
        DB::table('tb_role_permission')->whereIn('permission_code', ['EBEN-PER114', 'EBEN-PER115'])->delete();
        DB::table('tb_permissions')->whereIn('code', ['EBEN-PER114', 'EBEN-PER115'])->delete();
    }
};
