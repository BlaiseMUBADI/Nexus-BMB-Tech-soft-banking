<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            'code' => 'EBEN-PER118',
            'nom' => "Voir tombée d'échéances",
            'description' => "Consulter la liste agrégée des échéances à recouvrer (par zone, portefeuille, devise) — distincte de la simple liste des dossiers crédit, car elle expose des données financières sensibles",
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Par défaut, héritée des mêmes rôles que EBEN-PER53 (Liste des dossiers),
        // pour ne pas couper l'accès existant. À ajuster ensuite plus finement si besoin.
        $roleCodes = DB::table('tb_role_permission')->where('permission_code', 'EBEN-PER53')->pluck('role_code');

        foreach ($roleCodes as $roleCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => $roleCode,
                'permission_code' => 'EBEN-PER118',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tb_role_permission')->where('permission_code', 'EBEN-PER118')->delete();
        DB::table('tb_permissions')->where('code', 'EBEN-PER118')->delete();
    }
};
