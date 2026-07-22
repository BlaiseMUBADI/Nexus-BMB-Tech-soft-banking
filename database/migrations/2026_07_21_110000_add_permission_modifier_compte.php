<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Permission dédiée à la MODIFICATION d'un compte client.
 *
 * Historique : le module Client a 3 permissions séparées (créer/modifier/
 * supprimer), mais le module Compte n'en a jamais eu que 2 (créer EBEN-PER19,
 * supprimer EBEN-PER108) — la modification était accidentellement gérée par
 * EBEN-PER18 ("Voir comptes", permission de LECTURE), et le formulaire de
 * modification n'a en réalité jamais été construit (route + contrôleur
 * existaient, mais aucune vue), d'où l'erreur "View [comptes_clients.edit]
 * not found" en production.
 *
 * On crée donc EBEN-PER121, en héritant des rôles qui ont déjà EBEN-PER19
 * (Créer compte), suivant exactement le même principe que EBEN-PER108
 * (Supprimer compte) qui hérite lui aussi de EBEN-PER19.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_permissions')->insertOrIgnore([
            'code' => 'EBEN-PER121',
            'nom' => 'Modifier compte client',
            'description' => 'Modifier un compte client (réaffectation du portefeuille/agent gestionnaire)',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $roleCodes = DB::table('tb_role_permission')
            ->where('permission_code', 'EBEN-PER19')
            ->pluck('role_code');

        foreach ($roleCodes as $roleCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => $roleCode,
                'permission_code' => 'EBEN-PER121',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tb_role_permission')->where('permission_code', 'EBEN-PER121')->delete();
        DB::table('tb_permissions')->where('code', 'EBEN-PER121')->delete();
    }
};
