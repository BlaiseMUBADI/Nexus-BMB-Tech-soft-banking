<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Le "Solder par anticipation" (règlement de toutes les échéances restantes
 * en une fois, avec remise de 50% sur l'intérêt restant si strictement plus
 * de 3 échéances restent à payer) accordait jusqu'ici une remise commerciale
 * à TOUT titulaire d'EBEN-PER10|EBEN-PER111 — y compris un simple Caissier.
 * Cette permission dédiée réserve la décision (remise d'intérêt) aux profils
 * de supervision, comme la validation finale d'un dossier (EBEN-PER63).
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            [
                'code' => 'EBEN-PER128',
                'nom' => 'Solder un crédit par anticipation (remise intérêt)',
                'description' => "Régler en une fois toutes les échéances restantes d'un dossier depuis le solde "
                    . "RMB, avec remise de 50% sur l'intérêt restant si plus de 3 échéances restent dues.",
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        foreach (['EBEN-ROL1', 'EBEN-ROL12', 'EBEN-ROL3'] as $roleCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => $roleCode,
                'permission_code' => 'EBEN-PER128',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tb_role_permission')->where('permission_code', 'EBEN-PER128')->delete();
        DB::table('tb_permissions')->where('code', 'EBEN-PER128')->delete();
    }
};
