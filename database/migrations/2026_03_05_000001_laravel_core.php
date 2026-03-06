<?php

/**
 * ============================================================
 * MIGRATION 1/3 — NOYAU LARAVEL + SÉCURITÉ (RBAC)
 * ============================================================
 * Fichier : 2026_03_05_000001_laravel_core.php
 * Exécuté EN PREMIER (pas de dépendances métier).
 *
 * Tables créées :
 * ┌─────────────────────────────┬──────────────────────────────┐
 * │ SECTION A — LARAVEL INTERNE │ cache, cache_locks           │
 * │                             │ failed_jobs, job_batches     │
 * │                             │ jobs, migrations             │
 * │                             │ password_reset_tokens        │
 * │                             │ sessions                     │
 * ├─────────────────────────────┼──────────────────────────────┤
 * │ SECTION B — RBAC            │ tb_roles                     │
 * │                             │ tb_permissions               │
 * │                             │ tb_role_permission (pivot)   │
 * └─────────────────────────────┴──────────────────────────────┘
 * ============================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // ------------------------------------------------------------
    public function up(): void
    // ------------------------------------------------------------
    {
        // ========================================================
        // SECTION A — TABLES INTERNES LARAVEL
        // ========================================================

        // A1. Cache applicatif Laravel
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key', 191)->primary();
            $table->mediumText('value');
            $table->integer('expiration');
            $table->index('expiration', 'cache_expiration_index');
        });

        // A2. Verrous de cache (pour éviter les race conditions)
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key', 191)->primary();
            $table->string('owner', 191);
            $table->integer('expiration');
            $table->index('expiration', 'cache_locks_expiration_index');
        });

        // A3. Jobs échoués (queue worker — archive des erreurs)
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid', 191)->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // A4. Batches de jobs (traitement groupé asynchrone)
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id', 191)->primary();
            $table->string('name', 191);
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        // A5. File de jobs (queue worker — tâches en attente)
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue', 191);
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
            $table->index('queue', 'jobs_queue_index');
        });

        // NOTE: la table `migrations` est gérée automatiquement par Laravel.
        // Elle ne doit PAS être créée ici.

        // A7. Tokens de réinitialisation de mot de passe
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 191)->primary();
            $table->string('token', 191);
            $table->timestamp('created_at')->nullable();
        });

        // A8. Sessions utilisateurs (driver : database)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id', 191)->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity');
            $table->index('user_id',       'sessions_user_id_index');
            $table->index('last_activity', 'sessions_last_activity_index');
        });

        // ========================================================
        // SECTION B — RBAC (Contrôle d'accès basé sur les rôles)
        // Ces 3 tables sont la fondation de la sécurité Nexus.
        // Elles doivent exister avant users (migration 2/3).
        // ========================================================

        // B1. Rôles attribuables (ex : ADMIN, CAISSIER, DIRECTEUR)
        Schema::create('tb_roles', function (Blueprint $table) {
            $table->string('code', 20)->primary();       // ex: ADMIN
            $table->string('nom', 191)->unique();        // Libellé affiché
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // B2. Permissions atomiques (ex : VOIR_CAISSE, VALIDER_TRANSACTION)
        Schema::create('tb_permissions', function (Blueprint $table) {
            $table->string('code', 20)->primary();       // ex: VOIR_CAISSE
            $table->string('nom', 191)->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // B3. Pivot Rôles ↔ Permissions (un rôle peut avoir N permissions)
        Schema::create('tb_role_permission', function (Blueprint $table) {
            $table->string('role_code', 20);
            $table->string('permission_code', 20);
            $table->timestamps();
            $table->primary(['role_code', 'permission_code']);

            $table->foreign('role_code', 'fk_rp_role')
                  ->references('code')->on('tb_roles')
                  ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('permission_code', 'fk_rp_permission')
                  ->references('code')->on('tb_permissions')
                  ->restrictOnDelete()->cascadeOnUpdate();
        });

        // ====================================================
        // DONNÉES DE DÉMARRAGE — Rôles, Permissions, Liens
        // ====================================================
        $now = now();

        DB::table('tb_roles')->insertOrIgnore([
            ['code' => 'EBEN-ROL1', 'nom' => 'Administrateur', 'description' => 'Accès total au système',            'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL2', 'nom' => 'Caissier',       'description' => 'Gestion caisse et guichet',         'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL3', 'nom' => 'Directeur',      'description' => 'Supervision générale',              'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL4', 'nom' => 'Agent RH',       'description' => 'Gestion des ressources humaines',   'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-ROL5', 'nom' => 'Superviseur',    'description' => 'Supervision opérationnelle',        'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('tb_permissions')->insertOrIgnore([
            // Administration
            ['code' => 'EBEN-PER1',  'nom' => 'Accès Administration',  'description' => "Accès au panneau d'administration",            'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER2',  'nom' => 'Voir les rôles',        'description' => 'Consultation des rôles',                        'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER3',  'nom' => 'Gérer les rôles',       'description' => 'Création et modification des rôles',            'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER4',  'nom' => 'Voir les permissions',  'description' => 'Consultation des permissions',                  'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER5',  'nom' => 'Gérer les permissions', 'description' => 'Gestion des permissions',                       'created_at' => $now, 'updated_at' => $now],
            // RH
            ['code' => 'EBEN-PER6',  'nom' => 'Voir RH',               'description' => 'Accès au module RH',                            'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER7',  'nom' => 'Créer agent',           'description' => "Création d'un nouvel agent",                    'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER8',  'nom' => 'Modifier agent',        'description' => "Modification d'un agent",                       'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER9',  'nom' => 'Affectations',          'description' => 'Gestion des affectations',                      'created_at' => $now, 'updated_at' => $now],
            // Caisse
            ['code' => 'EBEN-PER10', 'nom' => 'Voir caisse',           'description' => 'Consultation des caisses',                      'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER11', 'nom' => 'Ouvrir caisse',         'description' => "Ouverture d'une caisse/guichet",                 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER12', 'nom' => 'Fermer caisse',         'description' => "Fermeture d'une caisse/guichet",                 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER13', 'nom' => 'Mouvements caisse',     'description' => 'Enregistrement des mouvements',                 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER14', 'nom' => 'Clôture caisse',        'description' => 'Clôture journalière caisse',                    'created_at' => $now, 'updated_at' => $now],
            // Clients
            ['code' => 'EBEN-PER15', 'nom' => 'Voir clients',          'description' => 'Consultation des clients',                      'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER16', 'nom' => 'Créer client',          'description' => "Enregistrement d'un client",                    'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER17', 'nom' => 'Modifier client',       'description' => "Modification d'un client",                      'created_at' => $now, 'updated_at' => $now],
            // Comptes
            ['code' => 'EBEN-PER18', 'nom' => 'Voir comptes',          'description' => 'Consultation des comptes',                      'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER19', 'nom' => 'Créer compte',          'description' => "Ouverture d'un compte",                          'created_at' => $now, 'updated_at' => $now],
            // Devises
            ['code' => 'EBEN-PER20', 'nom' => 'Voir devises',          'description' => 'Consultation des devises',                      'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EBEN-PER21', 'nom' => 'Gérer devises',         'description' => 'Gestion des devises et taux',                   'created_at' => $now, 'updated_at' => $now],
        ]);

        // Toutes les permissions → ADMIN
        $adminPerms = array_map(fn($n) => ['role_code' => 'EBEN-ROL1', 'permission_code' => "EBEN-PER{$n}", 'created_at' => $now, 'updated_at' => $now], range(1, 21));
        DB::table('tb_role_permission')->insertOrIgnore($adminPerms);

        // Caissier (EBEN-ROL2)
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER10', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER11', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER12', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER13', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER14', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER15', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL2', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Directeur (EBEN-ROL3)
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

        // Agent RH (EBEN-ROL4)
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER6',  'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER7',  'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER8',  'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL4', 'permission_code' => 'EBEN-PER9',  'created_at' => $now, 'updated_at' => $now],
        ]);

        // Superviseur (EBEN-ROL5)
        DB::table('tb_role_permission')->insertOrIgnore([
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER2',  'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER6',  'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER10', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER15', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER18', 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'EBEN-ROL5', 'permission_code' => 'EBEN-PER20', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    // ------------------------------------------------------------
    public function down(): void
    // ------------------------------------------------------------
    {
        // Suppression dans l'ordre inverse (enfants → parents)
        Schema::dropIfExists('tb_role_permission');
        Schema::dropIfExists('tb_permissions');
        Schema::dropIfExists('tb_roles');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        // NOTE: `migrations` est gérée par Laravel — ne pas supprimer ici.
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};
