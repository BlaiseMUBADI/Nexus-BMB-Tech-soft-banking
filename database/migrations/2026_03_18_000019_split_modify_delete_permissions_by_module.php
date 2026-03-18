<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            ['code' => 'EBEN-PER103', 'nom' => 'Modifier agent et service/poste RH', 'description' => 'Modifier les agents, services et postes RH', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER104', 'nom' => 'Supprimer agent/service/poste RH', 'description' => 'Supprimer les agents, services et postes RH', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER105', 'nom' => 'Modifier affectation RH', 'description' => 'Modifier une affectation RH', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER106', 'nom' => 'Supprimer affectation RH', 'description' => 'Supprimer une affectation RH', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER107', 'nom' => 'Supprimer client', 'description' => 'Supprimer un client', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER108', 'nom' => 'Supprimer compte client', 'description' => 'Fermer/supprimer un compte client', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $this->copyRoleMappings('EBEN-PER8', 'EBEN-PER103', $now);
        $this->copyRoleMappings('EBEN-PER8', 'EBEN-PER104', $now);
        $this->copyRoleMappings('EBEN-PER9', 'EBEN-PER105', $now);
        $this->copyRoleMappings('EBEN-PER9', 'EBEN-PER106', $now);
        $this->copyRoleMappings('EBEN-PER17', 'EBEN-PER107', $now);
        $this->copyRoleMappings('EBEN-PER19', 'EBEN-PER108', $now);

        DB::table('tb_permissions')->where('code', 'EBEN-PER103')->update([
            'nom' => 'Modifier agent et service/poste RH',
            'description' => 'Modifier les agents, services et postes RH',
            'updated_at' => $now,
        ]);

        DB::table('tb_permissions')->where('code', 'EBEN-PER8')->update([
            'nom' => 'Ajouter service/poste RH',
            'description' => 'Ajouter des services et postes RH',
            'updated_at' => $now,
        ]);

        DB::table('tb_permissions')->where('code', 'EBEN-PER9')->update([
            'nom' => 'Voir et creer affectations RH',
            'description' => 'Consulter et creer les affectations RH',
            'updated_at' => $now,
        ]);

        DB::table('tb_permissions')->where('code', 'EBEN-PER11')->update([
            'nom' => 'Gerer operations caisse',
            'description' => 'Creer et confirmer les operations caisse',
            'updated_at' => $now,
        ]);

        DB::table('tb_permissions')->where('code', 'EBEN-PER17')->update([
            'nom' => 'Modifier client',
            'description' => 'Modifier un client',
            'updated_at' => $now,
        ]);

        DB::table('tb_permissions')->where('code', 'EBEN-PER19')->update([
            'nom' => 'Creer compte client',
            'description' => 'Ouvrir un compte client',
            'updated_at' => $now,
        ]);
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
        $now = now();

        DB::table('tb_role_permission')->whereIn('permission_code', [
            'EBEN-PER103', 'EBEN-PER104', 'EBEN-PER105', 'EBEN-PER106', 'EBEN-PER107', 'EBEN-PER108',
        ])->delete();

        DB::table('tb_permissions')->whereIn('code', [
            'EBEN-PER103', 'EBEN-PER104', 'EBEN-PER105', 'EBEN-PER106', 'EBEN-PER107', 'EBEN-PER108',
        ])->delete();

        DB::table('tb_permissions')->where('code', 'EBEN-PER8')->update([
            'nom' => 'Modifier agent/service/poste',
            'description' => 'Modifier ou supprimer agent, service et poste RH',
            'updated_at' => $now,
        ]);

        DB::table('tb_permissions')->where('code', 'EBEN-PER9')->update([
            'nom' => 'Gerer affectations',
            'description' => 'Gerer les affectations RH',
            'updated_at' => $now,
        ]);

        DB::table('tb_permissions')->where('code', 'EBEN-PER11')->update([
            'nom' => 'Gerer operations caisse',
            'description' => 'Gerer les operations caisse, y compris annulation',
            'updated_at' => $now,
        ]);

        DB::table('tb_permissions')->where('code', 'EBEN-PER17')->update([
            'nom' => 'Modifier client',
            'description' => 'Modifier ou supprimer un client',
            'updated_at' => $now,
        ]);

        DB::table('tb_permissions')->where('code', 'EBEN-PER19')->update([
            'nom' => 'Gerer compte client',
            'description' => 'Ouvrir et fermer un compte client',
            'updated_at' => $now,
        ]);
    }
};
