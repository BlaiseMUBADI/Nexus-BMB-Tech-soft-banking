<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table de paramètres généraux (clé/valeur), pour toute configuration simple
 * gérée depuis Administration sans passer par un fichier .env ou du code en
 * dur — ex. chemin de l'image de signature du gérant utilisée sur les
 * documents/cartes imprimés.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_parametres_generaux', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cle', 100)->unique();
            $table->text('valeur')->nullable();
            $table->string('updated_by_matricule', 40)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_parametres_generaux');
    }
};
