<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute compte_dest_code sur tb_transactions.
 *
 * Nécessaire pour que les VIREMENTS bancaires apparaissent dans l'historique
 * ET le relevé de compte DES DEUX CÔTÉS (compte source ET compte destination),
 * pas seulement côté source (compte_code). Jusqu'ici, le compte destination
 * n'était mémorisé que dans tb_demandes_virement, jamais sur la transaction
 * réelle — un client ne voyait donc jamais les virements reçus dans son
 * historique/relevé.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_transactions', 'compte_dest_code')) {
                $table->string('compte_dest_code', 64)->nullable()->after('compte_code')
                    ->comment('Compte destination — renseigné uniquement pour les VIREMENTS entre comptes clients');
            }
        });

        Schema::table('tb_transactions', function (Blueprint $table) {
            $table->foreign('compte_dest_code', 'tb_transactions_compte_dest_fk')
                ->references('code_compte')->on('tb_comptes')
                ->nullOnDelete();

            $table->index('compte_dest_code', 'idx_trans_compte_dest');
        });
    }

    public function down(): void
    {
        Schema::table('tb_transactions', function (Blueprint $table) {
            $table->dropForeign('tb_transactions_compte_dest_fk');
            $table->dropIndex('idx_trans_compte_dest');
            $table->dropColumn('compte_dest_code');
        });
    }
};
