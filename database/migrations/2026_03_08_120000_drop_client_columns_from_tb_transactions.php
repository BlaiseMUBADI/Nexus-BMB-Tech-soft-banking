<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Supprime client_nom et client_ref de tb_transactions.
 * Ces données (nom client, réf. externe) ne sont plus stockées
 * dans la table transactions — utiliser le compte_code pour
 * identifier le client lorsque nécessaire.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_transactions', function (Blueprint $table) {
            $toDrop = array_filter(
                ['client_nom', 'client_ref'],
                fn($col) => Schema::hasColumn('tb_transactions', $col)
            );
            if (!empty($toDrop)) {
                $table->dropColumn(array_values($toDrop));
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_transactions', function (Blueprint $table) {
            $table->string('client_nom', 150)->nullable()->after('devise_code');
            $table->string('client_ref', 50)->nullable()->after('client_nom')
                  ->comment('Réf. externe, passeport, ID…');
        });
    }
};
