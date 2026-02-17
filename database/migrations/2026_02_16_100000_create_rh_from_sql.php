<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_services', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('tb_postes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('tb_services');
            $table->string('nom');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('tb_agents', function (Blueprint $table) {
            $table->string('matricule', 50)->primary();
            $table->string('nom');
            $table->string('postnom')->nullable();
            $table->string('prenom')->nullable();
            $table->enum('sexe', ['M','F'])->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('telephone', 50)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('adresse', 191)->nullable();
            $table->string('photo')->nullable();
            $table->date('date_embauche')->nullable();
            $table->enum('statut', ['actif','inactif'])->default('actif');
            $table->timestamps();
        });

        Schema::create('tb_affectations', function (Blueprint $table) {
            $table->id();
            $table->string('agent_matricule', 50);
            $table->foreign('agent_matricule')->references('matricule')->on('tb_agents');
            $table->foreignId('poste_id')->constrained('tb_postes');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_affectations');
        Schema::dropIfExists('tb_agents');
        Schema::dropIfExists('tb_postes');
        Schema::dropIfExists('tb_services');
    }
};
