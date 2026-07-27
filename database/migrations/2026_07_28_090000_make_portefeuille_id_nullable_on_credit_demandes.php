<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Le portefeuille ne doit plus être obligatoire à la CRÉATION d'un dossier
 * crédit — il ne devient pertinent (et requis) qu'au moment où l'on affecte
 * un agent de crédit au dossier (CreditController::affecterAnalyse(), qui
 * détermine lui-même le bon portefeuille selon l'agent choisi).
 *
 * Une migration précédente (2026_05_04_104108) avait rendu cette colonne
 * NOT NULL. On revient volontairement en arrière ici, à la demande explicite
 * du métier : un dossier peut désormais exister en BROUILLON/SOUMIS sans
 * portefeuille défini.
 */
return new class extends Migration
{
    public function up(): void
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
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        $nullCount = \Illuminate\Support\Facades\DB::table('tb_credit_demandes')->whereNull('portefeuille_id')->count();
        if ($nullCount > 0) {
            throw new \RuntimeException(
                "Rollback annulé : {$nullCount} dossier(s) sans portefeuille_id. "
                . "Affectez-leur un portefeuille avant de revenir à la contrainte NOT NULL."
            );
        }

        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->dropForeign('tb_credit_demandes_portefeuille_id_foreign');
        });

        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->unsignedBigInteger('portefeuille_id')->nullable(false)->change();
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
