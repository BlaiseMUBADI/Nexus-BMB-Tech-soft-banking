<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les champs caution et frais_etude à tb_credit_deblocages
 * pour tracer le flux financier complet au déblocage.
 *
 * Flux : montant_valide_gerant = caution(20%) + frais_total(4%) + montant_decaisse(80%)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_credit_deblocages', function (Blueprint $table) {
            // Montant de référence validé par le gérant (base de calcul)
            if (!Schema::hasColumn('tb_credit_deblocages', 'montant_valide_gerant')) {
                $table->decimal('montant_valide_gerant', 15, 2)->nullable()->after('montant_debloque');
            }
            // Caution 20% bloquée dans le compte RMB client
            if (!Schema::hasColumn('tb_credit_deblocages', 'montant_caution')) {
                $table->decimal('montant_caution', 15, 2)->default(0)->after('montant_valide_gerant');
            }
            // Frais 3% étude de dossier (non remboursable)
            if (!Schema::hasColumn('tb_credit_deblocages', 'frais_etude')) {
                $table->decimal('frais_etude', 15, 2)->default(0)->after('montant_caution');
            }
            // frais_dossier existant = 1% frais de dossier (non remboursable)
        });
    }

    public function down(): void
    {
        Schema::table('tb_credit_deblocages', function (Blueprint $table) {
            $table->dropColumn(['montant_valide_gerant', 'montant_caution', 'frais_etude']);
        });
    }
};
