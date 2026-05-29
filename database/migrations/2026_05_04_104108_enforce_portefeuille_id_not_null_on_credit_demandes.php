<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rend tb_credit_demandes.portefeuille_id obligatoire (NOT NULL) avec FK.
     */
    public function up(): void
    {
        // Vérifier qu'aucune ligne orpheline ne subsiste avant de durcir
        $nullCount = DB::table('tb_credit_demandes')->whereNull('portefeuille_id')->count();
        if ($nullCount > 0) {
            throw new \RuntimeException(
                "Migration annulée : {$nullCount} dossier(s) sans portefeuille_id. "
                . "Exécuter d'abord le script d'adaptation des données."
            );
        }

        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            // Supprimer la FK existante avant de modifier la colonne
            $table->dropForeign('tb_credit_demandes_portefeuille_id_foreign');
        });

        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            // Rendre la colonne NOT NULL
            $table->unsignedBigInteger('portefeuille_id')->nullable(false)->change();
        });

        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            // Recréer la FK avec règles explicites
            $table->foreign('portefeuille_id', 'tb_credit_demandes_portefeuille_id_foreign')
                  ->references('id')
                  ->on('tb_portefeuilles_agents')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
        });
    }

    /**
     * Revert : repasser portefeuille_id en nullable.
     */
    public function down(): void
    {
        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->dropForeign('tb_credit_demandes_portefeuille_id_foreign');
        });

        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->unsignedBigInteger('portefeuille_id')->nullable()->change();
        });

        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->foreign('portefeuille_id', 'tb_credit_demandes_portefeuille_id_foreign')
                  ->references('id')
                  ->on('tb_portefeuilles_agents')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
        });
    }
};
