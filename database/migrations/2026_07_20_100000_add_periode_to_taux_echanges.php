<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Historisation du taux de change par PÉRIODE explicite (date_debut / date_fin),
 * au lieu d'une simple date d'application ponctuelle.
 *
 * - date_application est renommée en date_debut (même sémantique, nom plus clair
 *   maintenant qu'une période a une fin explicite).
 * - date_fin (nullable) : NULL = taux encore actif "jusqu'à nouvel ordre",
 *   sinon date de fin de validité de ce taux.
 *
 * Le "taux actif" pour une paire de devises à un instant T est celui dont
 * date_debut <= T ET (date_fin IS NULL OU date_fin >= T), voir TauxEchange::actif().
 *
 * Utilise du SQL brut (pas Schema::renameColumn) pour éviter la dépendance
 * doctrine/dbal non installée dans ce projet.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tb_taux_echanges', 'date_application') && !Schema::hasColumn('tb_taux_echanges', 'date_debut')) {
            DB::statement("ALTER TABLE tb_taux_echanges CHANGE date_application date_debut TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
        }

        if (!Schema::hasColumn('tb_taux_echanges', 'date_fin')) {
            DB::statement("ALTER TABLE tb_taux_echanges ADD date_fin TIMESTAMP NULL DEFAULT NULL AFTER date_debut");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tb_taux_echanges', 'date_fin')) {
            DB::statement("ALTER TABLE tb_taux_echanges DROP COLUMN date_fin");
        }

        if (Schema::hasColumn('tb_taux_echanges', 'date_debut') && !Schema::hasColumn('tb_taux_echanges', 'date_application')) {
            DB::statement("ALTER TABLE tb_taux_echanges CHANGE date_debut date_application TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
        }
    }
};
