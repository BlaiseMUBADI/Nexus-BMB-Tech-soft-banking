<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Le champ compte_debit_id stocke le code du coffre/guichet (source des fonds),
 * pas un code de compte client. La FK vers tb_comptes est incorrecte — on la supprime.
 * On ajoute à la place une colonne guichet_solde_id (FK vers tb_caisses_guichets_soldes)
 * pour conserver la traçabilité exacte du coffre débité.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_credit_deblocages', function (Blueprint $table) {
            // Supprimer la FK erronée (coffre ≠ compte client)
            try {
                $table->dropForeign(['compte_debit_id']);
            } catch (\Throwable $e) {
                // La FK peut ne pas exister selon l'historique de la base.
            }

            // Ajouter la référence correcte vers le solde du coffre débité
            if (!Schema::hasColumn('tb_credit_deblocages', 'guichet_solde_id')) {
                $table->unsignedBigInteger('guichet_solde_id')->nullable()->after('compte_debit_id');
                $table->foreign('guichet_solde_id')
                      ->references('id')
                      ->on('tb_caisses_guichets_soldes')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_credit_deblocages', function (Blueprint $table) {
            $table->dropForeign(['guichet_solde_id']);
            $table->dropColumn('guichet_solde_id');

            // Rétablir la FK d'origine (ne devrait pas être nécessaire en prod)
            $table->foreign('compte_debit_id')
                  ->references('code_compte')
                  ->on('tb_comptes')
                  ->restrictOnDelete();
        });
    }
};
