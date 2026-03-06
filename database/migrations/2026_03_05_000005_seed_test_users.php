<?php

/**
 * ============================================================
 * MIGRATION 5/5 — UTILISATEURS TEST + PERMISSIONS RÔLES
 * ============================================================
 * Fichier : 2026_03_05_000005_seed_test_users.php
 *
 * ⚠  Cette migration est ADDITIVE (INSERT OR IGNORE).
 *    Elle ne supprime ni ne modifie aucune donnée existante.
 *    Sécuritaire à exécuter sur une base déjà en production.
 *
 * Ce qu'elle fait :
 *  1. Complète les permissions des rôles 2-5 si manquantes
 *     (correction du bug initial : rôles sans permissions)
 *  2. Ajoute 2 agents test
 *  3. Ajoute 2 utilisateurs test avec leurs rôles
 *  4. Ajoute leurs affectations
 *
 * Utilisateurs créés :
 * ┌─────────────────────────────┬────────────────┬──────────────┐
 * │ Email                       │ Mot de passe   │ Rôle         │
 * ├─────────────────────────────┼────────────────┼──────────────┤
 * │ jean.caissier@bmb.cd        │ Caissier@2026  │ Caissier     │
 * │ marie.rh@bmb.cd             │ AgentRH@2026   │ Agent RH     │
 * └─────────────────────────────┴────────────────┴──────────────┘
 *
 * Pour annuler (down) : supprime les lignes insérées par email.
 * ============================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // ====================================================
        // 1. CORRECTION : permissions manquantes pour rôles 2-5
        //    (INSERT OR IGNORE = idempotent)
        // ====================================================

        // Caissier (EBEN-ROL2) — caisse + consultation clients/comptes
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER10', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER11', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER12', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER13', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER14', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER15', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Directeur (EBEN-ROL3) — vision globale lecture seule
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER1',  'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER2',  'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER4',  'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER6',  'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER10', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER15', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL3', 'permission_code' => 'EBEN-PER20', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Agent RH (EBEN-ROL4) — gestion RH complète
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER6',  'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER7',  'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER8',  'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER9',  'created_at' => $now, 'updated_at' => $now],
        ]);

        // Superviseur (EBEN-ROL5) — supervision transversale lecture
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER2',  'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER6',  'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER10', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER15', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER20', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ====================================================
        // 2. AGENTS TEST
        // ====================================================
        DB::table('tb_agents')->insertOrIgnore([
            [
                'matricule'      => 'AG-EBENKGA-26-00002',
                'nom'            => 'MULUMBA',
                'postnom'        => null,
                'prenom'         => 'Jean',
                'sexe'           => 'M',
                'date_naissance' => '1990-06-15',
                'telephone'      => '+243810000002',
                'email'          => 'jean.caissier@bmb.cd',
                'adresse'        => 'Kinshasa',
                'photo'          => null,
                'date_embauche'  => $now->toDateString(),
                'statut'         => 'actif',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'matricule'      => 'AG-EBENKGA-26-00003',
                'nom'            => 'KASONGO',
                'postnom'        => null,
                'prenom'         => 'Marie',
                'sexe'           => 'F',
                'date_naissance' => '1992-03-20',
                'telephone'      => '+243810000003',
                'email'          => 'marie.rh@bmb.cd',
                'adresse'        => 'Kinshasa',
                'photo'          => null,
                'date_embauche'  => $now->toDateString(),
                'statut'         => 'actif',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
        ]);

        // ====================================================
        // 3. UTILISATEURS TEST
        //    Hash bcrypt cost=12 :
        //      Caissier@2026 → $2y$12$o/m3X...
        //      AgentRH@2026  → $2y$12$j8T6Z...
        // ====================================================
        DB::table('users')->insertOrIgnore([
            [
                'agent_matricule'   => 'AG-EBENKGA-26-00002',
                'name'              => 'jean_caissier',
                'email'             => 'jean.caissier@bmb.cd',
                'email_verified_at' => $now,
                'password'          => '$2y$12$o/m3X.G8ImNrB8WgzrankOb5R8trQnBbSwK4vVzYXa7WHjy6AIIfG',
                'remember_token'    => null,
                'etat'              => 'actif',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'agent_matricule'   => 'AG-EBENKGA-26-00003',
                'name'              => 'marie_rh',
                'email'             => 'marie.rh@bmb.cd',
                'email_verified_at' => $now,
                'password'          => '$2y$12$j8T6Zy8w4q.zzZ1eNt/eeuak5l4H/PdUWwz7rmUNqNKJL7UQ7PR4m',
                'remember_token'    => null,
                'etat'              => 'actif',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ]);

        // ====================================================
        // 4. AFFECTATIONS TEST (postes créés dans migration 2)
        //    (Insère seulement si tb_postes contient id 2 et 3)
        // ====================================================
        $poste2 = DB::table('tb_postes')->where('id', 2)->exists();
        $poste3 = DB::table('tb_postes')->where('id', 3)->exists();

        if ($poste2) {
            DB::table('tb_affectations')->insertOrIgnore([[
                'agent_matricule' => 'AG-EBENKGA-26-00002',
                'poste_id'        => 2,
                'guichet_id'      => null,
                'date_debut'      => $now->toDateString(),
                'date_fin'        => null,
                'Etat'            => 'ACTIF',
                'created_at'      => $now,
                'updated_at'      => $now,
            ]]);
        }

        if ($poste3) {
            DB::table('tb_affectations')->insertOrIgnore([[
                'agent_matricule' => 'AG-EBENKGA-26-00003',
                'poste_id'        => 3,
                'guichet_id'      => null,
                'date_debut'      => $now->toDateString(),
                'date_fin'        => null,
                'Etat'            => 'ACTIF',
                'created_at'      => $now,
                'updated_at'      => $now,
            ]]);
        }

        // ====================================================
        // 5. RÔLES → UTILISATEURS
        // ====================================================
        $users = DB::table('users')
            ->whereIn('email', ['jean.caissier@bmb.cd', 'marie.rh@bmb.cd'])
            ->get()
            ->keyBy('email');

        $roleInserts = [];

        if (isset($users['jean.caissier@bmb.cd'])) {
            $roleInserts[] = [
                'user_id'    => $users['jean.caissier@bmb.cd']->id,
                'role_code'  => 'EBEN-ROL2',  // Caissier
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (isset($users['marie.rh@bmb.cd'])) {
            $roleInserts[] = [
                'user_id'    => $users['marie.rh@bmb.cd']->id,
                'role_code'  => 'EBEN-ROL4',  // Agent RH
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($roleInserts)) {
            DB::table('tb_role_user')->insertOrIgnore($roleInserts);
        }
    }

    // --------------------------------------------------------
    public function down(): void
    // --------------------------------------------------------
    {
        // Supprimer les rôles des utilisateurs test
        $userIds = DB::table('users')
            ->whereIn('email', ['jean.caissier@bmb.cd', 'marie.rh@bmb.cd'])
            ->pluck('id');

        DB::table('tb_role_user')->whereIn('user_id', $userIds)->delete();

        // Supprimer les affectations test
        DB::table('tb_affectations')
            ->whereIn('agent_matricule', ['AG-EBENKGA-26-00002', 'AG-EBENKGA-26-00003'])
            ->delete();

        // Supprimer les utilisateurs test
        DB::table('users')
            ->whereIn('email', ['jean.caissier@bmb.cd', 'marie.rh@bmb.cd'])
            ->delete();

        // Supprimer les agents test
        DB::table('tb_agents')
            ->whereIn('matricule', ['AG-EBENKGA-26-00002', 'AG-EBENKGA-26-00003'])
            ->delete();

        // Supprimer les permissions des rôles 2-5 ajoutées par cette migration
        DB::table('tb_role_permission')
            ->whereIn('role_code', ['EBEN-ROL2', 'EBEN-ROL3', 'EBEN-ROL4', 'EBEN-ROL5'])
            ->delete();
    }
};
