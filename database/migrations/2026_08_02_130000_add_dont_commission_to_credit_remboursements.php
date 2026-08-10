<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute une colonne dédiée `dont_commission` à `tb_credit_remboursements`.
 *
 * Jusqu'ici la table ne décomposait un remboursement qu'en `dont_capital` /
 * `dont_interet` / `dont_penalite` — la part "commission" de chaque échéance
 * (voir `commission_echeance` sur `tb_credit_echeances`, introduite par la
 * migration 2026_07_16_100001_add_commission_columns_to_credit_tables) était
 * comptabilisée par défaut dans `dont_capital`, faute de colonne dédiée.
 *
 * Cela mélangeait deux natures comptables différentes (remboursement de
 * capital emprunté vs. frais/commission perçus par la coopérative), ce qui
 * fausse les rapports de ventilation des recettes crédit (distinguer combien
 * a été réellement recouvré en capital, en intérêts, et en commissions).
 *
 * Bonne pratique adoptée : chaque composante de l'échéance a sa propre
 * colonne miroir dans le remboursement, comme c'est déjà le cas pour le
 * capital et l'intérêt.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_credit_remboursements', function (Blueprint $table) {
            $table->decimal('dont_commission', 15, 2)->default(0)->after('dont_interet');
        });
    }

    public function down(): void
    {
        Schema::table('tb_credit_remboursements', function (Blueprint $table) {
            $table->dropColumn('dont_commission');
        });
    }
};
