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

            // FK : le validateur est un agent (RESTRICT/RESTRICT — pas de cascade)
            $table->foreign('validateur_matricule', 'tb_mouvements_inter_caisses_ibfk_1')
                  ->references('matricule')->on('tb_agents')
                  ->restrictOnDelete()->restrictOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('tb_mouvements_inter_caisses', function (Blueprint $table) {
            $table->dropForeign('tb_mouvements_inter_caisses_ibfk_1');
            $table->dropColumn(['statut', 'validateur_matricule', 'observations']);
        });
    }
};
