<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute le support de la commission sur les virements bancaires, afin que le
 * barème par tranches (tb_commission_rules : montant_min/montant_max + mode
 * FIXE) — déjà utilisé pour Dépôt/Retrait/Change — s'applique aussi au
 * Virement. La commission est déterminée et gelée au moment de la PROPOSITION
 * (comme montant_dest/taux_change), puis réappliquée telle quelle à
 * l'exécution.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_demandes_virement', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_demandes_virement', 'commission_totale')) {
                $table->decimal('commission_totale', 18, 2)->nullable()->default(0)->after('montant_source')
                    ->comment('Commission (barème tb_commission_rules) prélevée en plus du montant sur le compte source');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_demandes_virement', function (Blueprint $table) {
            $table->dropColumn('commission_totale');
        });
    }
};
