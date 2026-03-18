<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            // Clients
            [
                'code' => 'EBEN-PER80',
                'nom' => 'Ajouter client (module Clients)',
                'description' => 'Permission d ajout dans le module Clients',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER81',
                'nom' => 'Modifier client (module Clients)',
                'description' => 'Permission de modification dans le module Clients',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER82',
                'nom' => 'Supprimer client (module Clients)',
                'description' => 'Permission de suppression dans le module Clients',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Comptes
            [
                'code' => 'EBEN-PER83',
                'nom' => 'Ajouter compte (module Comptes)',
                'description' => 'Permission d ajout dans le module Comptes',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER84',
                'nom' => 'Modifier compte (module Comptes)',
                'description' => 'Permission de modification dans le module Comptes',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER85',
                'nom' => 'Supprimer compte (module Comptes)',
                'description' => 'Permission de suppression dans le module Comptes',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // RH
            [
                'code' => 'EBEN-PER86',
                'nom' => 'Ajouter agent RH',
                'description' => 'Permission d ajout des agents RH',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER87',
                'nom' => 'Modifier agent RH',
                'description' => 'Permission de modification des agents RH',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER88',
                'nom' => 'Supprimer agent RH',
                'description' => 'Permission de suppression des agents RH',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER89',
                'nom' => 'Ajouter service/poste RH',
                'description' => 'Permission d ajout des services et postes RH',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER90',
                'nom' => 'Modifier service RH',
                'description' => 'Permission de modification des services RH',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER91',
                'nom' => 'Supprimer service/poste RH',
                'description' => 'Permission de suppression des services et postes RH',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER92',
                'nom' => 'Ajouter affectation RH',
                'description' => 'Permission d ajout des affectations RH',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER93',
                'nom' => 'Modifier affectation RH',
                'description' => 'Permission de modification des affectations RH',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER94',
                'nom' => 'Supprimer affectation RH',
                'description' => 'Permission de suppression des affectations RH',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Caisse
            [
                'code' => 'EBEN-PER95',
                'nom' => 'Ajouter operation caisse',
                'description' => 'Permission d ajout dans le module Caisse',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER96',
                'nom' => 'Modifier operation caisse',
                'description' => 'Permission de modification dans le module Caisse',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER97',
                'nom' => 'Supprimer operation caisse',
                'description' => 'Permission de suppression/annulation dans le module Caisse',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Credit
            [
                'code' => 'EBEN-PER100',
                'nom' => 'Ajouter operation credit',
                'description' => 'Permission d ajout dans le module Credit',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER101',
                'nom' => 'Modifier workflow credit',
                'description' => 'Permission de modification du workflow Credit',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'EBEN-PER102',
                'nom' => 'Supprimer/annuler dossier credit',
                'description' => 'Permission de suppression/annulation dans le module Credit',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Administrateur: tous les nouveaux codes
        DB::table('tb_role_permission')->insertOrIgnore(
            array_map(
                fn (int $n) => [
                    'role_code' => 'EBEN-ROL1',
                    'permission_code' => 'EBEN-PER' . $n,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,100,101,102]
            )
        );

        // RH
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER86', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER87', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER88', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER89', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER90', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER91', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER92', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER93', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER94', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Caisse
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER95', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER96', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER97', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Clients/Comptes + Credit metier
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER80', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER81', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER82', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER83', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER84', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER85', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER100', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER101', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL6', 'permission_code' => 'EBEN-PER102', 'created_at' => $now, 'updated_at' => $now],

            ['role_code' => 'EBEN-ROL9', 'permission_code' => 'EBEN-PER80', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL9', 'permission_code' => 'EBEN-PER81', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL9', 'permission_code' => 'EBEN-PER82', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        $codes = [
            'EBEN-PER80','EBEN-PER81','EBEN-PER82','EBEN-PER83','EBEN-PER84','EBEN-PER85',
            'EBEN-PER86','EBEN-PER87','EBEN-PER88','EBEN-PER89','EBEN-PER90','EBEN-PER91','EBEN-PER92','EBEN-PER93','EBEN-PER94',
            'EBEN-PER95','EBEN-PER96','EBEN-PER97','EBEN-PER100','EBEN-PER101','EBEN-PER102',
        ];

        DB::table('tb_role_permission')->whereIn('permission_code', $codes)->delete();
        DB::table('tb_permissions')->whereIn('code', $codes)->delete();
    }
};
