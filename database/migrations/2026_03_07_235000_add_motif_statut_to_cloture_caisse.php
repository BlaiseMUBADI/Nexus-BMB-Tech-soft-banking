<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les colonnes manquantes à tb_cloture_caisse :
 *   - motif_ecart  : justification obligatoire si écart ≠ 0
 *   - statut_ecart : EQUILIBRE / EXCEDENT / DEFICIT
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_cloture_caisse', function (Blueprint $table) {
            $table->text('motif_ecart')
                  ->nullable()
                  ->after('detail_billetage')
                  ->comment('Justification requise si écart ≠ 0');

            $table->enum('statut_ecart', ['EQUILIBRE', 'EXCEDENT', 'DEFICIT'])
                  ->default('EQUILIBRE')
                  ->after('motif_ecart')
                  ->comment('Résultat de la confrontation physique / système');
        });
    }

    public function down(): void
    {
        Schema::table('tb_cloture_caisse', function (Blueprint $table) {
            $table->dropColumn(['motif_ecart', 'statut_ecart']);
        });
    }
};
