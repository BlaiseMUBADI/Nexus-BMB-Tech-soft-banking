<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Table tb_agents
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

        // Table tb_clients
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

        // Table tb_comptes
        Schema::create('tb_comptes', function (Blueprint $table) {
            $table->string('code_compte', 64)->primary();
            $table->string('client_matricule', 191);
            $table->string('numero', 30)->unique();
            $table->enum('type', ['COURANT','EPARGNE_LIBRE','EPARGNE_BLOQUEE','CAUTION_CREDIT']);
            $table->decimal('solde_reel', 18, 2)->default(0.00);
            $table->decimal('solde_bloque', 18, 2)->default(0.00);
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign('client_matricule')->references('matricule')->on('tb_clients')->onDelete('restrict')->onUpdate('restrict');
        });

        // Table tb_zones
        Schema::create('tb_zones', function (Blueprint $table) {
            $table->string('code_zone', 50)->primary();
            $table->string('nom', 100);
            $table->string('agent_commercial_matricule', 50)->nullable();
            $table->string('commune', 100)->nullable();
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign('agent_commercial_matricule')->references('matricule')->on('tb_agents')->onDelete('restrict')->onUpdate('restrict');
        });

        // Table tb_services
        Schema::create('tb_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nom', 191);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Table tb_postes
        Schema::create('tb_postes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_id');
            $table->string('nom', 191);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('service_id')->references('id')->on('tb_services')->onDelete('restrict')->onUpdate('cascade');
        });

        // Table tb_affectations
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

        // Table tb_permissions
        Schema::create('tb_permissions', function (Blueprint $table) {
            $table->string('code', 20)->primary();
            $table->string('nom', 191)->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // Table tb_roles
        Schema::create('tb_roles', function (Blueprint $table) {
            $table->string('code', 20)->primary();
            $table->string('nom', 191)->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // Table tb_role_permission
        Schema::create('tb_role_permission', function (Blueprint $table) {
            $table->string('role_code', 20);
            $table->string('permission_code', 20);
            $table->timestamps();
            $table->primary(['role_code', 'permission_code']);
            $table->foreign('role_code')->references('code')->on('tb_roles')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('permission_code')->references('code')->on('tb_permissions')->onDelete('restrict')->onUpdate('cascade');
        });

        // Table tb_role_user
        Schema::create('tb_role_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('role_code', 20);
            $table->timestamps();
            $table->foreign('role_code')->references('code')->on('tb_roles')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
        });

        // Table tb_transactions
        Schema::create('tb_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('compte_code', 64);
            $table->string('agent_matricule', 50);
            $table->enum('type', ['DEPOT','RETRAIT','VIREMENT','REMBOURSEMENT']);
            $table->decimal('montant', 18, 2);
            $table->string('reference', 50)->unique()->nullable();
            $table->foreign('compte_code')->references('code_compte')->on('tb_comptes')->onDelete('restrict');
            $table->foreign('agent_matricule')->references('matricule')->on('tb_agents')->onDelete('restrict');
        });

        // Table users
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('agent_matricule', 255)->nullable();
            $table->string('name', 191);
            $table->string('email', 191)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 191);
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->string('etat', 20)->default('actif');
            $table->foreign('agent_matricule')->references('matricule')->on('tb_agents')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_transactions');
        Schema::dropIfExists('tb_role_user');
        Schema::dropIfExists('tb_role_permission');
        Schema::dropIfExists('tb_roles');
        Schema::dropIfExists('tb_permissions');
        Schema::dropIfExists('tb_affectations');
        Schema::dropIfExists('tb_postes');
        Schema::dropIfExists('tb_services');
        Schema::dropIfExists('tb_zones');
        Schema::dropIfExists('tb_comptes');
        Schema::dropIfExists('tb_clients');
        Schema::dropIfExists('tb_agents');
        Schema::dropIfExists('users');
    }
};
