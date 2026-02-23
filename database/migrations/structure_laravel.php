<?php
// database/structure_laravel.php
// Ce script crée la structure de base de données Laravel selon la structure SQL fournie.

use Illuminate\Database\Capsule\Manager as Capsule;

// ...existing code...
// Synchronize with structure.sql
Capsule::schema()->create('cache', function ($table) {
    $table->string('key', 191)->primary()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->mediumText('value')->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->integer('expiration');
    $table->index('expiration', 'cache_expiration_index');
});

Capsule::schema()->create('cache_locks', function ($table) {
    $table->string('key', 191)->primary()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('owner', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->integer('expiration');
    $table->index('expiration', 'cache_locks_expiration_index');
});

Capsule::schema()->create('failed_jobs', function ($table) {
    $table->bigIncrements('id');
    $table->string('uuid', 191)->unique()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->text('connection')->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->text('queue')->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->longText('payload')->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->longText('exception')->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->timestamp('failed_at')->useCurrent();
});

Capsule::schema()->create('jobs', function ($table) {
    $table->bigIncrements('id');
    $table->string('queue', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->longText('payload')->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->tinyInteger('attempts')->unsigned();
    $table->integer('reserved_at')->unsigned()->nullable();
    $table->integer('available_at')->unsigned();
    $table->integer('created_at')->unsigned();
    $table->index('queue', 'jobs_queue_index');
});

Capsule::schema()->create('job_batches', function ($table) {
    $table->string('id', 191)->primary()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('name', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->integer('total_jobs');
    $table->integer('pending_jobs');
    $table->integer('failed_jobs');
    $table->longText('failed_job_ids')->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->mediumText('options')->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->integer('cancelled_at')->nullable();
    $table->integer('created_at');
    $table->integer('finished_at')->nullable();
});

Capsule::schema()->create('migrations', function ($table) {
    $table->increments('id');
    $table->string('migration', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->integer('batch');
});

Capsule::schema()->create('password_reset_tokens', function ($table) {
    $table->string('email', 191)->primary()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('token', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->timestamp('created_at')->nullable();
});

Capsule::schema()->create('sessions', function ($table) {
    $table->string('id', 191)->primary()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->unsignedBigInteger('user_id')->nullable();
    $table->string('ip_address', 45)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->text('user_agent')->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->longText('payload')->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->integer('last_activity');
    $table->index('user_id', 'sessions_user_id_index');
    $table->index('last_activity', 'sessions_last_activity_index');
});

// ...existing code..
$table->foreign('agent_matricule')->references('matricule')->on('tb_agents')->onDelete('set null');
$table->foreign('poste_id')->references('id')->on('tb_postes')->onDelete('cascade');
$table->timestamp('created_at')->nullable();
$table->timestamp('updated_at')->nullable();
