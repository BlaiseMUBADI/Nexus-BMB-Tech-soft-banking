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
                  ->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('permission_code', 'fk_rp_permission')
                  ->references('code')->on('tb_permissions')
                  ->onDelete('restrict')->onUpdate('cascade');
        });
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
