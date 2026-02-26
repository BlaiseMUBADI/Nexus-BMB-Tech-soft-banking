<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // Table: tb_services
        Schema::create('tb_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nom', 191);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Table: tb_postes
        Schema::create('tb_postes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_id');
            $table->string('nom', 191);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('service_id')->references('id')->on('tb_services')->onDelete('restrict')->onUpdate('cascade');
        });

        // Table: tb_agents
        Schema::create('tb_agents', function (Blueprint $table) {
            $table->string('matricule', 50)->primary();
            $table->string('nom', 191);
            $table->string('postnom', 191)->nullable();
            $table->string('prenom', 191)->nullable();
            $table->enum('sexe', ['M', 'F'])->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('telephone', 50)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('adresse', 191)->nullable();
            $table->string('photo', 255)->nullable();
            $table->date('date_embauche')->nullable();
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
        });

        // Table: tb_affectations
        Schema::create('tb_affectations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('agent_matricule', 50);
            $table->unsignedBigInteger('poste_id');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->timestamps();
            $table->string('Etat', 50);
            $table->foreign('agent_matricule')->references('matricule')->on('tb_agents')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('poste_id')->references('id')->on('tb_postes')->onDelete('restrict')->onUpdate('cascade');
        });

        // Table: tb_clients
        Schema::create('tb_clients', function (Blueprint $table) {
            $table->string('matricule', 191)->unique()->nullable();
            $table->string('nom', 191);
            $table->string('postnom', 191);
            $table->string('prenom', 191);
            $table->string('email', 191)->nullable();
            $table->string('telephone', 191)->nullable();
            $table->enum('sexe', ['M', 'F']);
            $table->date('date_naissance');
            $table->string('lieu_naissance', 191);
            $table->string('adresse', 191);
            $table->string('etat_civil', 191);
            $table->string('nom_conjoint', 191)->nullable();
            $table->string('zone', 191);
            $table->string('type_piece_identite', 191);
            $table->string('lieu_delivrance_piece', 191);
            $table->date('date_delivrance_piece');
            $table->string('numero_piece_identite', 191);
            $table->string('photo', 191)->nullable();
            $table->string('secteur_activite', 191)->nullable();
            $table->string('type_activite', 191)->nullable();
            $table->string('nom_entreprise', 191)->nullable();
            $table->string('adresse_entreprise', 191)->nullable();
            $table->string('telephone_entreprise', 191)->nullable();
            $table->string('statut_entreprise', 191)->nullable();
            $table->string('nombre_annees_experience', 191)->nullable();
            $table->decimal('revenu_mensuel', 15, 2)->nullable();
            $table->string('revenu_mensuel_devise', 10)->nullable();
            $table->string('autres_details_activite', 191)->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('tb_affectations');
        Schema::dropIfExists('tb_agents');
        Schema::dropIfExists('tb_postes');
        Schema::dropIfExists('tb_services');
        Schema::dropIfExists('tb_clients');
    }
};
