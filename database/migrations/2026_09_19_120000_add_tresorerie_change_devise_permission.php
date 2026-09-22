<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Nouveau bloc du menu Trésorerie / Coffre : "Change de devises", qui permet
 * de convertir une partie du solde d'une devise vers une autre AU SEIN DU
 * MÊME coffre central (ex: convertir du CDF excédentaire en USD pour pouvoir
 * honorer des paiements en USD), avec taux de change tracé et écriture
 * comptable complète (cf. app/Http/Controllers/Tresorerie/TresorerieController::changeDevise).
 *
 * EBEN-PER44 ("Voir trésorerie") reste requis pour accéder à la section ;
 * EBEN-PER129 contrôle spécifiquement ce bloc (affichage menu + middleware
 * de route), suivant le même pattern que EBEN-PER124/125/126.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            [
                'code' => 'EBEN-PER129',
                'nom' => 'Change de devises (coffre)',
                'description' => 'Convertir un montant d\'une devise vers une autre au sein du coffre central',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $roles = ['EBEN-ROL3', 'EBEN-ROL5', 'EBEN-ROL8'];

        $pivots = [];
        foreach ($roles as $roleCode) {
            $pivots[] = ['role_code' => $roleCode, 'permission_code' => 'EBEN-PER129', 'created_at' => $now, 'updated_at' => $now];
        }

        DB::table('tb_role_permission')->insertOrIgnore($pivots);
    }

    public function down(): void
    {
        DB::table('tb_role_permission')->where('permission_code', 'EBEN-PER129')->delete();
        DB::table('tb_permissions')->where('code', 'EBEN-PER129')->delete();
    }
};
