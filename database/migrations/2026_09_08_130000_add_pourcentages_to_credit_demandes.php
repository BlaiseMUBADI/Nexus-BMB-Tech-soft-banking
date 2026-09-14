<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les pourcentages de retenue au déblocage (caution, frais de dossier,
 * frais d'étude) sur tb_credit_demandes, modifiables par dossier — nécessaire
 * pour l'import d'anciens dossiers de crédit (historique) où ces taux peuvent
 * différer du barème actuel de l'agence (défauts : 20% / 1% / 3%).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_credit_demandes', 'pourcentage_caution')) {
                $table->decimal('pourcentage_caution', 5, 2)->default(20.00)->after('commission_totale');
            }
            if (!Schema::hasColumn('tb_credit_demandes', 'pourcentage_frais_dossier')) {
                $table->decimal('pourcentage_frais_dossier', 5, 2)->default(1.00)->after('pourcentage_caution');
            }
            if (!Schema::hasColumn('tb_credit_demandes', 'pourcentage_frais_etude')) {
                $table->decimal('pourcentage_frais_etude', 5, 2)->default(3.00)->after('pourcentage_frais_dossier');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->dropColumn(['pourcentage_caution', 'pourcentage_frais_dossier', 'pourcentage_frais_etude']);
        });
    }
};
