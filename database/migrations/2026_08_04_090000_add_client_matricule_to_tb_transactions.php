<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute une référence client structurée (nullable) aux transactions —
 * utilisée pour les opérations sans compte qui concernent tout de même un
 * client précis (ex. Frais carte membre, encaissé depuis Opérations de
 * caisse). Ne remplace pas compte_code : reste NULL pour DEPOT/RETRAIT/etc.
 * qui identifient déjà le client via le compte.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_transactions', function (Blueprint $table) {
            $table->string('client_matricule', 40)->nullable()->after('compte_dest_code');

            $table->foreign('client_matricule', 'fk_transactions_client')
                ->references('matricule')->on('tb_clients')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('tb_transactions', function (Blueprint $table) {
            $table->dropForeign('fk_transactions_client');
            $table->dropColumn('client_matricule');
        });
    }
};
