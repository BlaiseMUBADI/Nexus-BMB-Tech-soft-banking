<?php

/**
 * ============================================================
 * MIGRATION 4/4 — Lien Guichet ↔ Affectation
 * ============================================================
 * Ajoute la colonne guichet_id (nullable) dans tb_affectations
 * pour permettre d'attacher un agent à un guichet de caisse.
 *
 * Logique métier :
 *   Un agent peut avoir une affectation RH sur un poste (poste_id)
 *   ET être titulaire d'un guichet de caisse (guichet_id).
 *   Les deux liens sont indépendants et optionnels.
 * ============================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_affectations', function (Blueprint $table) {
            // Colonne nullable : l'affectation reste valable sans guichet
            $table->unsignedBigInteger('guichet_id')
                  ->nullable()
                  ->after('poste_id')
                  ->comment('Guichet de caisse affecté (optionnel). NULL = agent hors caisse.');

            $table->foreign('guichet_id', 'fk_affectation_guichet')
                  ->references('id')
                  ->on('tb_caisses_guichets')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tb_affectations', function (Blueprint $table) {
            $table->dropForeign('fk_affectation_guichet');
            $table->dropColumn('guichet_id');
        });
    }
};
