<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('module', 40);              // CAISSE, CLIENT, COMPTE, RH, ADMINISTRATION...
            $table->string('type_action', 60);          // ex: OPERATION_CREEE, OPERATION_ANNULEE, CLIENT_CREE...
            $table->string('loggable_type', 100)->nullable(); // Modèle concerné (polymorphique)
            $table->unsignedBigInteger('loggable_id')->nullable();
            $table->string('reference', 100)->nullable(); // libellé lisible (ex: numéro compte, matricule client)
            $table->string('acteur_matricule', 30)->nullable();
            $table->text('description')->nullable();
            $table->json('anciennes_valeurs')->nullable();
            $table->json('nouvelles_valeurs')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['module', 'type_action']);
            $table->index(['loggable_type', 'loggable_id']);
            $table->index('acteur_matricule');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_activity_logs');
    }
};
