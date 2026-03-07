<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute :
 *   1. Statut EN_VERIFICATION dans tb_caisses_guichets.statut_operationnel
 *   2. Colonnes de validation superviseur dans tb_cloture_caisse
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Ajouter EN_VERIFICATION dans l'ENUM
        DB::statement("
            ALTER TABLE tb_caisses_guichets
            MODIFY COLUMN statut_operationnel
            ENUM('OUVERT','FERME','SUSPENDU','EN_VERIFICATION') DEFAULT 'FERME'
        ");

        // 2. Colonnes validation superviseur dans tb_cloture_caisse
        Schema::table('tb_cloture_caisse', function (Blueprint $table) {
            $table->enum('statut_validation', ['EN_ATTENTE', 'VALIDE', 'REJETE'])
                  ->default('EN_ATTENTE')
                  ->after('statut_ecart')
                  ->comment('Statut de validation par le superviseur');

            $table->string('validateur_matricule', 20)
                  ->nullable()
                  ->after('statut_validation')
                  ->comment('Matricule du superviseur ayant validé');

            $table->timestamp('date_validation')
                  ->nullable()
                  ->after('validateur_matricule')
                  ->comment('Date/heure de validation par le superviseur');

            $table->text('observations_superviseur')
                  ->nullable()
                  ->after('date_validation')
                  ->comment('Commentaire du superviseur lors de la validation');
        });
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE tb_caisses_guichets
            MODIFY COLUMN statut_operationnel
            ENUM('OUVERT','FERME','SUSPENDU') DEFAULT 'FERME'
        ");

        Schema::table('tb_cloture_caisse', function (Blueprint $table) {
            $table->dropColumn([
                'statut_validation',
                'validateur_matricule',
                'date_validation',
                'observations_superviseur',
            ]);
        });
    }
};
