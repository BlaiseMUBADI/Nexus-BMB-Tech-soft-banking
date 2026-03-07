<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_mouvements_inter_caisses', function (Blueprint $table) {
            // Statut du mouvement (double validation)
            $table->enum('statut', ['EN_ATTENTE', 'VALIDE', 'CONFIRME', 'ANNULE'])
                  ->default('CONFIRME')
                  ->after('date_mouvement');

            // Superviseur qui a validé (null = validation directe)
            $table->string('validateur_matricule', 50)->nullable()->after('statut');

            // Observations / notes
            $table->string('observations', 255)->nullable()->after('validateur_matricule');
        });
    }

    public function down(): void
    {
        Schema::table('tb_mouvements_inter_caisses', function (Blueprint $table) {
            $table->dropColumn(['statut', 'validateur_matricule', 'observations']);
        });
    }
};
