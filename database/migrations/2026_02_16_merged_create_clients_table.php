<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->unique()->nullable();
            $table->string('nom');
            $table->string('postnom');
            $table->string('prenom');
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->enum('sexe', ['M', 'F']);
            $table->date('date_naissance');
            $table->string('lieu_naissance');
            $table->string('adresse');
            $table->string('etat_civil');
            $table->string('nom_conjoint')->nullable();
            $table->string('zone');
            $table->string('type_piece_identite');
            $table->string('lieu_delivrance_piece');
            $table->date('date_delivrance_piece');
            $table->string('numero_piece_identite');
            $table->string('photo')->nullable();
            // Partie 6 : Activité économique
            $table->string('secteur_activite')->nullable();
            $table->string('type_activite')->nullable();
            $table->string('nom_entreprise')->nullable();
            $table->string('adresse_entreprise')->nullable();
            $table->string('telephone_entreprise')->nullable();
            $table->string('statut_entreprise')->nullable();
            $table->string('nombre_annees_experience')->nullable();
            $table->decimal('revenu_mensuel', 15, 2)->nullable();
            $table->string('autres_details_activite')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
