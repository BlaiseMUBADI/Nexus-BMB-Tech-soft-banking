<?php
// database/structure_app.php
// Ce script crée la structure de base de données spécifique à l'application (hors tables Laravel par défaut).

use Illuminate\Database\Capsule\Manager as Capsule;

// ...existing code...
// Synchronize with structure.sql
Capsule::schema()->create('tb_services', function ($table) {
    $table->bigIncrements('id');
    $table->string('nom', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->text('description')->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->timestamp('created_at')->nullable();
    $table->timestamp('updated_at')->nullable();
});

Capsule::schema()->create('tb_roles', function ($table) {
    $table->string('code', 20)->primary()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci'); // EBEN-ROL1, EBEN-ROL2
    $table->string('nom', 191)->unique()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('description', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->timestamp('created_at')->nullable();
    $table->timestamp('updated_at')->nullable();
});

Capsule::schema()->create('tb_permissions', function ($table) {
    $table->string('code', 20)->primary()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci'); // EBEN-PER1, EBEN-PER2
    $table->string('nom', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('description', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->timestamp('created_at')->nullable();
    $table->timestamp('updated_at')->nullable();
});

// Table pivot adaptée
Capsule::schema()->create('tb_role_permission', function ($table) {
    $table->string('role_code', 20)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('permission_code', 20)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->timestamp('created_at')->nullable();
    $table->timestamp('updated_at')->nullable();
    $table->primary(['role_code', 'permission_code']);
    $table->foreign('role_code')->references('code')->on('tb_roles')->onDelete('cascade');
    $table->foreign('permission_code')->references('code')->on('tb_permissions')->onDelete('cascade');
});

Capsule::schema()->create('tb_postes', function ($table) {
    $table->bigIncrements('id');
    $table->unsignedBigInteger('service_id');
    $table->string('nom', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->text('description')->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->timestamp('created_at')->nullable();
    $table->timestamp('updated_at')->nullable();
    $table->foreign('service_id')->references('id')->on('tb_services')->onDelete('restrict')->onUpdate('cascade');
});

Capsule::schema()->create('tb_agents', function ($table) {
    $table->string('matricule', 50)->primary()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('nom', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('postnom', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('prenom', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->enum('sexe', ['M', 'F'])->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->date('date_naissance')->nullable();
    $table->string('telephone', 50)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('email', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('adresse', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('photo', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->date('date_embauche')->nullable();
    $table->enum('statut', ['actif', 'inactif'])->default('actif')->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->timestamp('created_at')->nullable();
    $table->timestamp('updated_at')->nullable();
});

Capsule::schema()->create('tb_affectations', function ($table) {
    $table->bigIncrements('id');
    $table->string('agent_matricule', 50)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->unsignedBigInteger('poste_id');
    $table->date('date_debut');
    $table->date('date_fin')->nullable();
    $table->timestamp('created_at')->nullable();
    $table->timestamp('updated_at')->nullable();
    $table->string('Etat', 50)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->foreign('agent_matricule')->references('matricule')->on('tb_agents')->onDelete('restrict')->onUpdate('cascade');
    $table->foreign('poste_id')->references('id')->on('tb_postes')->onDelete('restrict')->onUpdate('cascade');
});

Capsule::schema()->create('tb_clients', function ($table) {
    $table->string('matricule', 191)->unique()->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('nom', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('postnom', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('prenom', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('email', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('telephone', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->enum('sexe', ['M', 'F'])->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->date('date_naissance');
    $table->string('lieu_naissance', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('adresse', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('etat_civil', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('nom_conjoint', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('zone', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('type_piece_identite', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('lieu_delivrance_piece', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->date('date_delivrance_piece');
    $table->string('numero_piece_identite', 191)->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('photo', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('secteur_activite', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('type_activite', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('nom_entreprise', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('adresse_entreprise', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('telephone_entreprise', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('statut_entreprise', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('nombre_annees_experience', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->decimal('revenu_mensuel', 15, 2)->nullable();
    $table->string('revenu_mensuel_devise', 10)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->string('autres_details_activite', 191)->nullable()->charset('utf8mb4')->collation('utf8mb4_0900_ai_ci');
    $table->timestamp('created_at')->nullable();
    $table->timestamp('updated_at')->nullable();
});
// ...existing code...
