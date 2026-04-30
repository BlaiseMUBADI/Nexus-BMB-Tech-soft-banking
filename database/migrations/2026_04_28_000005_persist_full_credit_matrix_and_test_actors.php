<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $creditPerms = [
            'EBEN-PER53', 'EBEN-PER54', 'EBEN-PER55', 'EBEN-PER56', 'EBEN-PER57',
            'EBEN-PER58', 'EBEN-PER59', 'EBEN-PER60', 'EBEN-PER61', 'EBEN-PER62',
            'EBEN-PER63', 'EBEN-PER64', 'EBEN-PER65', 'EBEN-PER66', 'EBEN-PER67',
            'EBEN-PER68', 'EBEN-PER69', 'EBEN-PER70', 'EBEN-PER71', 'EBEN-PER72',
        ];

        // Keep permission labels aligned with the active validation order.
        DB::table('tb_permissions')->where('code', 'EBEN-PER61')->update([
            'nom' => 'Valider bloc Charge operations',
            'description' => 'Validation niveau 3 - Charge des operations',
            'updated_at' => $now,
        ]);

        DB::table('tb_permissions')->where('code', 'EBEN-PER62')->update([
            'nom' => 'Valider bloc Controleur',
            'description' => 'Validation niveau 2 - Controleur interne',
            'updated_at' => $now,
        ]);

        // Target credit matrix by role.
        $matrix = [
            // Agent commercial / demandeur
            'EBEN-ROL9'  => ['EBEN-PER53', 'EBEN-PER54', 'EBEN-PER55', 'EBEN-PER56', 'EBEN-PER57'],
            // Agent credit
            'EBEN-ROL6'  => ['EBEN-PER53', 'EBEN-PER54', 'EBEN-PER55', 'EBEN-PER56', 'EBEN-PER57', 'EBEN-PER58', 'EBEN-PER59', 'EBEN-PER60', 'EBEN-PER70', 'EBEN-PER71', 'EBEN-PER72'],
            // Charge des operations
            'EBEN-ROL11' => ['EBEN-PER53', 'EBEN-PER57', 'EBEN-PER61', 'EBEN-PER70', 'EBEN-PER72'],
            // Controleur interne credit
            'EBEN-ROL14' => ['EBEN-PER53', 'EBEN-PER57', 'EBEN-PER62', 'EBEN-PER70', 'EBEN-PER72'],
            // Gerant
            'EBEN-ROL12' => ['EBEN-PER53', 'EBEN-PER57', 'EBEN-PER63', 'EBEN-PER64', 'EBEN-PER70', 'EBEN-PER71', 'EBEN-PER72'],
            // Directeur national
            'EBEN-ROL3'  => ['EBEN-PER53', 'EBEN-PER57', 'EBEN-PER63', 'EBEN-PER64', 'EBEN-PER70', 'EBEN-PER71', 'EBEN-PER72'],
            // Optional: keep ROL8 minimal demandeur-style access if used in your org
            'EBEN-ROL8'  => ['EBEN-PER53', 'EBEN-PER54', 'EBEN-PER55', 'EBEN-PER56', 'EBEN-PER57'],
        ];

        // Remove credit permissions from roles that are not part of the matrix or admin role.
        DB::table('tb_role_permission')
            ->whereIn('permission_code', $creditPerms)
            ->whereNotIn('role_code', array_merge(array_keys($matrix), ['EBEN-ROL1']))
            ->delete();

        // Admin keeps all credit permissions.
        DB::table('tb_role_permission')
            ->where('role_code', 'EBEN-ROL1')
            ->whereIn('permission_code', $creditPerms)
            ->delete();

        foreach ($creditPerms as $permCode) {
            DB::table('tb_role_permission')->insertOrIgnore([
                'role_code' => 'EBEN-ROL1',
                'permission_code' => $permCode,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Enforce exact matrix for each role.
        foreach ($matrix as $roleCode => $perms) {
            DB::table('tb_role_permission')
                ->where('role_code', $roleCode)
                ->whereIn('permission_code', $creditPerms)
                ->delete();

            foreach ($perms as $permCode) {
                DB::table('tb_role_permission')->insertOrIgnore([
                    'role_code' => $roleCode,
                    'permission_code' => $permCode,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Safety lock: PER64 (deblocage) only for admin, directeur national, gerant.
        DB::table('tb_role_permission')
            ->where('permission_code', 'EBEN-PER64')
            ->whereNotIn('role_code', ['EBEN-ROL1', 'EBEN-ROL3', 'EBEN-ROL12'])
            ->delete();

        // Seed test actors for full end-to-end validation.
        // Password: CreditTest@2026
        $passwordHash = '$2y$10$hLX7FuQsGS1.aO8hAE6SmOZ.QoKJgwmlyY4UWMU6suoZYpOWzT7UW';

        $actors = [
            [
                'matricule' => 'AG-CRD-TST-0101',
                'prenom' => 'DEMANDEUR_1',
                'email' => 'credit.demandeur1@test.local',
                'login' => 'credit_demandeur1',
                'role' => 'EBEN-ROL9',
            ],
            [
                'matricule' => 'AG-CRD-TST-0102',
                'prenom' => 'CHARGE_OPERATIONS_1',
                'email' => 'credit.charge.operations1@test.local',
                'login' => 'credit_charge_operations1',
                'role' => 'EBEN-ROL11',
            ],
            [
                'matricule' => 'AG-CRD-TST-0103',
                'prenom' => 'AGENT_CREDIT_1',
                'email' => 'credit.agent.credit1@test.local',
                'login' => 'credit_agent_credit1',
                'role' => 'EBEN-ROL6',
            ],
            [
                'matricule' => 'AG-CRD-TST-0104',
                'prenom' => 'CONTROLEUR_1',
                'email' => 'credit.controleur1@test.local',
                'login' => 'credit_controleur1',
                'role' => 'EBEN-ROL14',
            ],
            [
                'matricule' => 'AG-CRD-TST-0105',
                'prenom' => 'GERANT_1',
                'email' => 'credit.gerant1@test.local',
                'login' => 'credit_gerant1',
                'role' => 'EBEN-ROL12',
            ],
            [
                'matricule' => 'AG-CRD-TST-0106',
                'prenom' => 'DIRECTEUR_NATIONAL_1',
                'email' => 'credit.directeur.national1@test.local',
                'login' => 'credit_directeur_national1',
                'role' => 'EBEN-ROL3',
            ],
        ];

        foreach ($actors as $actor) {
            DB::table('tb_agents')->upsert([
                [
                    'matricule' => $actor['matricule'],
                    'nom' => 'TEST',
                    'postnom' => 'CREDIT',
                    'prenom' => $actor['prenom'],
                    'sexe' => 'M',
                    'email' => $actor['email'],
                    'date_embauche' => now()->toDateString(),
                    'statut' => 'actif',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ], ['matricule'], [
                'nom', 'postnom', 'prenom', 'sexe', 'email', 'date_embauche', 'statut', 'updated_at',
            ]);

            DB::table('users')->upsert([
                [
                    'agent_matricule' => $actor['matricule'],
                    'name' => $actor['login'],
                    'email' => $actor['email'],
                    'email_verified_at' => $now,
                    'password' => $passwordHash,
                    'etat' => 'actif',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ], ['email'], [
                'agent_matricule', 'name', 'email_verified_at', 'password', 'etat', 'updated_at',
            ]);

            $userId = DB::table('users')->where('email', $actor['email'])->value('id');

            if ($userId) {
                DB::table('tb_role_user')->where('user_id', $userId)->delete();
                DB::table('tb_role_user')->insertOrIgnore([
                    'user_id' => $userId,
                    'role_code' => $actor['role'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        $now = now();

        // Restore historical labels (before workflow reorder).
        DB::table('tb_permissions')->where('code', 'EBEN-PER61')->update([
            'nom' => 'Valider bloc Charge operations',
            'description' => 'Validation niveau 2 - Charge des operations',
            'updated_at' => $now,
        ]);

        DB::table('tb_permissions')->where('code', 'EBEN-PER62')->update([
            'nom' => 'Valider bloc Controleur',
            'description' => 'Validation niveau 3 - Controleur interne',
            'updated_at' => $now,
        ]);

        $actorEmails = [
            'credit.demandeur1@test.local',
            'credit.charge.operations1@test.local',
            'credit.agent.credit1@test.local',
            'credit.controleur1@test.local',
            'credit.gerant1@test.local',
            'credit.directeur.national1@test.local',
        ];

        $actorMatricules = [
            'AG-CRD-TST-0101',
            'AG-CRD-TST-0102',
            'AG-CRD-TST-0103',
            'AG-CRD-TST-0104',
            'AG-CRD-TST-0105',
            'AG-CRD-TST-0106',
        ];

        $userIds = DB::table('users')
            ->whereIn('email', $actorEmails)
            ->pluck('id')
            ->all();

        if (!empty($userIds)) {
            DB::table('tb_role_user')->whereIn('user_id', $userIds)->delete();
        }

        DB::table('users')->whereIn('email', $actorEmails)->delete();
        DB::table('tb_agents')->whereIn('matricule', $actorMatricules)->delete();
    }
};
