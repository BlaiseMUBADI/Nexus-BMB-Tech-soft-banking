<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Autorisations dédiées aux actions de la page "État du coffre" (Trésorerie).
 *
 * Avant : la page (EBEN-PER124) affichait TOUS les montants et boutons à tout
 * utilisateur autorisé, et les approbations ne dépendaient que d'EBEN-PER46
 * (mouvements trésorerie) voire d'EBEN-PER44 pour les demandes de modification
 * d'opérations. Désormais chaque action a SA permission :
 *
 *   EBEN-PER130 – Voir les montants du coffre central (soldes + statistiques)
 *   EBEN-PER131 – Valider le billettage / clôtures de guichet (et lignes devise)
 *   EBEN-PER132 – Approuver les demandes de ravitaillement (alimentation guichet)
 *   EBEN-PER133 – Autoriser les demandes de modification/suppression d'opérations
 *
 * Attribution : PER130 suit EBEN-PER124 (toute personne qui voit la page) ;
 * PER131/132/133 suivent les titulaires d'EBEN-PER46 (EBEN-ROL1/8/14).
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            [
                'code' => 'EBEN-PER130',
                'nom' => 'Voir les montants du coffre central',
                'description' => 'Afficher les soldes et statistiques (montants) de l\'état du coffre',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER131',
                'nom' => 'Valider billettage / clôtures de guichet',
                'description' => 'Valider ou rejeter les clôtures de guichet (billettage) et leurs lignes par devise',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER132',
                'nom' => 'Approuver demandes de ravitaillement',
                'description' => 'Approuver ou rejeter les demandes d\'alimentation (ravitaillement) des guichets',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER133',
                'nom' => "Autoriser modification d'opérations",
                'description' => "Approuver ou rejeter les demandes de modification/suppression d'opérations de caisse",
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // PER130 : mêmes rôles que EBEN-PER124 (toute personne ouvrant la page)
        // PER131/132/133 : mêmes rôles que EBEN-PER46 (validateurs trésorerie)
        $pivots = [];
        foreach (['EBEN-ROL3', 'EBEN-ROL5', 'EBEN-ROL8', 'EBEN-ROL14'] as $roleCode) {
            $pivots[] = ['role_code' => $roleCode, 'permission_code' => 'EBEN-PER130', 'created_at' => $now, 'updated_at' => $now];
        }
        foreach (['EBEN-ROL1', 'EBEN-ROL8', 'EBEN-ROL14'] as $roleCode) {
            foreach (['EBEN-PER131', 'EBEN-PER132', 'EBEN-PER133'] as $perm) {
                $pivots[] = ['role_code' => $roleCode, 'permission_code' => $perm, 'created_at' => $now, 'updated_at' => $now];
            }
        }

        DB::table('tb_role_permission')->insertOrIgnore($pivots);
    }

    public function down(): void
    {
        DB::table('tb_role_permission')->whereIn('permission_code', ['EBEN-PER130', 'EBEN-PER131', 'EBEN-PER132', 'EBEN-PER133'])->delete();
        DB::table('tb_permissions')->whereIn('code', ['EBEN-PER130', 'EBEN-PER131', 'EBEN-PER132', 'EBEN-PER133'])->delete();
    }
};
