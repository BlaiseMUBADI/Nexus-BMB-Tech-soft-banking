<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Rend chaque bloc du menu Trésorerie / Coffre granulaire :
 * EBEN-PER124 (Voir état du coffre), EBEN-PER125 (Voir approvisionnement/
 * intercaisse), EBEN-PER126 (Voir commissions).
 *
 * EBEN-PER44 ("Voir trésorerie") reste le portail d'accès global à la
 * section (toujours requis au niveau des routes), mais chaque lien du menu
 * et sa page ne s'affichent/ne s'ouvrent désormais que si l'utilisateur a
 * AUSSI la permission spécifique du bloc concerné.
 *
 * Attribuées ici aux mêmes rôles qui avaient déjà EBEN-PER44, pour ne
 * retirer d'accès à personne au moment du déploiement.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            [
                'code' => 'EBEN-PER124',
                'nom' => 'Voir état du coffre',
                'description' => 'Accéder au bloc "État du Coffre" du menu Trésorerie',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER125',
                'nom' => 'Voir approvisionnement/intercaisse',
                'description' => 'Accéder aux blocs "Approvisionnement" et "Intercaisse" du menu Trésorerie',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER126',
                'nom' => 'Voir commissions',
                'description' => 'Accéder au bloc "Commissions" du menu Trésorerie',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $roles = ['EBEN-ROL3', 'EBEN-ROL5', 'EBEN-ROL8'];
        $newPermissions = ['EBEN-PER124', 'EBEN-PER125', 'EBEN-PER126'];

        $pivots = [];
        foreach ($roles as $roleCode) {
            foreach ($newPermissions as $permCode) {
                $pivots[] = ['role_code' => $roleCode, 'permission_code' => $permCode, 'created_at' => $now, 'updated_at' => $now];
            }
        }

        DB::table('tb_role_permission')->insertOrIgnore($pivots);
    }

    public function down(): void
    {
        DB::table('tb_role_permission')
            ->whereIn('permission_code', ['EBEN-PER124', 'EBEN-PER125', 'EBEN-PER126'])
            ->delete();

        DB::table('tb_permissions')
            ->whereIn('code', ['EBEN-PER124', 'EBEN-PER125', 'EBEN-PER126'])
            ->delete();
    }
};
