<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * EBEN-PER109 ("Remboursements Caisse/Guichet") a été créée comme intermédiaire
 * entre PER65 et PER111 (cf. 2026_06_02_000000_add_caisse_remboursements_permission.php),
 * puis totalement remplacée par EBEN-PER111 (cf. 2026_06_02_000021_...php et le
 * commentaire "Modèle strict bancaire: plus de cohabitation avec PER109" dans
 * OperationCaisseController). Elle n'est vérifiée par aucune route, contrôleur
 * ou vue : permission fantôme, on la supprime pour éviter toute confusion côté
 * gestion des rôles.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('tb_role_permission')
            ->where('permission_code', 'EBEN-PER109')
            ->delete();

        DB::table('tb_permissions')
            ->where('code', 'EBEN-PER109')
            ->delete();
    }

    public function down(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            [
                'code' => 'EBEN-PER109',
                'nom' => 'Remboursements Caisse/Guichet',
                'description' => 'Accéder aux remboursements crédit depuis la Caisse/Guichet',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        foreach (['EBEN-ROL1', 'EBEN-ROL2', 'EBEN-ROL6', 'EBEN-ROL11'] as $roleCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => $roleCode,
                'permission_code' => 'EBEN-PER109',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
};
