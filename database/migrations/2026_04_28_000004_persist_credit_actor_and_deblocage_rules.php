<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // Keep permission labels aligned with the active workflow.
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

        // Deblocage must stay restricted (never for charge des operations).
        DB::table('tb_role_permission')
            ->where('permission_code', 'EBEN-PER64')
            ->whereNotIn('role_code', ['EBEN-ROL1', 'EBEN-ROL3', 'EBEN-ROL12'])
            ->delete();

        DB::table('tb_role_permission')->insertOrIgnore([
            [
                'role_code' => 'EBEN-ROL3',
                'permission_code' => 'EBEN-PER64',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_code' => 'EBEN-ROL12',
                'permission_code' => 'EBEN-PER64',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Ensure role Agent Credit can be selected in affectation (PER58).
        DB::table('tb_role_permission')->insertOrIgnore([
            'role_code' => 'EBEN-ROL6',
            'permission_code' => 'EBEN-PER58',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Create second credit agent actor: login = credit_agent_credit1
        $matricule = 'AG-CRD-TST-0006';
        $email = 'credit.agent.credit1@test.local';
        $login = 'credit_agent_credit1';
        $passwordHash = '$2y$10$hLX7FuQsGS1.aO8hAE6SmOZ.QoKJgwmlyY4UWMU6suoZYpOWzT7UW'; // CreditTest@2026

        DB::table('tb_agents')->upsert([
            [
                'matricule' => $matricule,
                'nom' => 'TEST',
                'postnom' => 'CREDIT',
                'prenom' => 'AGENT_CREDIT_1',
                'sexe' => 'M',
                'email' => $email,
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
                'agent_matricule' => $matricule,
                'name' => $login,
                'email' => $email,
                'email_verified_at' => $now,
                'password' => $passwordHash,
                'etat' => 'actif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['email'], [
            'agent_matricule', 'name', 'email_verified_at', 'password', 'etat', 'updated_at',
        ]);

        $userId = DB::table('users')->where('email', $email)->value('id');

        if ($userId) {
            DB::table('tb_role_user')->where('user_id', $userId)->delete();
            DB::table('tb_role_user')->insertOrIgnore([
                'user_id' => $userId,
                'role_code' => 'EBEN-ROL6',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $now = now();

        // Restore original labels from previous dataset.
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

        // Restore historical deblocage right for charge des operations if needed.
        DB::table('tb_role_permission')->insertOrIgnore([
            'role_code' => 'EBEN-ROL11',
            'permission_code' => 'EBEN-PER64',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $email = 'credit.agent.credit1@test.local';
        $matricule = 'AG-CRD-TST-0006';
        $userId = DB::table('users')->where('email', $email)->value('id');

        if ($userId) {
            DB::table('tb_role_user')->where('user_id', $userId)->delete();
        }

        DB::table('users')->where('email', $email)->delete();
        DB::table('tb_agents')->where('matricule', $matricule)->delete();
    }
};
