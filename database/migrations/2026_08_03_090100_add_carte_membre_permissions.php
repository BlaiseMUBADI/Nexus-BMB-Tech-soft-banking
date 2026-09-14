<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Permissions créées : EBEN-PER122 (encaisser frais carte membre),
 * EBEN-PER123 (imprimer carte membre).
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            [
                'code' => 'EBEN-PER122',
                'nom' => 'Encaisser frais carte membre',
                'description' => 'Encaisser au guichet le frais d\'émission de la carte membre du client',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER123',
                'nom' => 'Imprimer carte membre',
                'description' => 'Imprimer la carte membre d\'un client dont le frais a été payé',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $pivots = [];
        // Caissier (ROL2) et Admin (ROL1) : peuvent encaisser
        foreach (['EBEN-ROL1', 'EBEN-ROL2'] as $roleCode) {
            $pivots[] = ['role_code' => $roleCode, 'permission_code' => 'EBEN-PER122', 'created_at' => $now, 'updated_at' => $now];
        }
        // Admin, Caissier, Superviseur, Chargé de crédit : peuvent imprimer
        foreach (['EBEN-ROL1', 'EBEN-ROL2', 'EBEN-ROL5', 'EBEN-ROL6'] as $roleCode) {
            $pivots[] = ['role_code' => $roleCode, 'permission_code' => 'EBEN-PER123', 'created_at' => $now, 'updated_at' => $now];
        }

        DB::table('tb_role_permission')->insertOrIgnore($pivots);
    }

    public function down(): void
    {
        DB::table('tb_role_permission')
            ->whereIn('permission_code', ['EBEN-PER122', 'EBEN-PER123'])
            ->delete();

        DB::table('tb_permissions')
            ->whereIn('code', ['EBEN-PER122', 'EBEN-PER123'])
            ->delete();
    }
};
