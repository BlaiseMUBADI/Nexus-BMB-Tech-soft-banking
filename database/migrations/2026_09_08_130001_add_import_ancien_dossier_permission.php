<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Permission dédiée pour l'import d'un ancien dossier de crédit (historique) :
 * cette action crée un dossier avec les 4 blocs de validation déjà approuvés
 * et un déblocage historique déjà exécuté, en contournant le circuit normal
 * (analyse → contrôleur → chargé opérations → gérant). Réservée aux profils
 * de supervision, pas à tous les créateurs de dossier normal (EBEN-PER54).
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            [
                'code' => 'EBEN-PER127',
                'nom' => "Importer un ancien dossier de crédit (historique)",
                'description' => "Créer un dossier de crédit historique déjà débloqué dans le passé, avec validations et échéancier générés automatiquement",
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $roles = ['EBEN-ROL1', 'EBEN-ROL3', 'EBEN-ROL12'];

        $pivots = [];
        foreach ($roles as $roleCode) {
            $pivots[] = ['role_code' => $roleCode, 'permission_code' => 'EBEN-PER127', 'created_at' => $now, 'updated_at' => $now];
        }

        DB::table('tb_role_permission')->insertOrIgnore($pivots);
    }

    public function down(): void
    {
        DB::table('tb_role_permission')->where('permission_code', 'EBEN-PER127')->delete();
        DB::table('tb_permissions')->where('code', 'EBEN-PER127')->delete();
    }
};
