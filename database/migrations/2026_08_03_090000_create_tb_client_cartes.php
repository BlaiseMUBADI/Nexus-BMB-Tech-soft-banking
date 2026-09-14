<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Suivi de l'émission des cartes membres physiques.
 *
 * Une carte membre n'est imprimable qu'après paiement du frais configuré
 * (voir CommissionRule, code_operation = 'CARTE_MEMBRE', géré depuis
 * Trésorerie > Commissions). Chaque paiement encaissé crée une ligne ici,
 * liée à la transaction de caisse (type PAIEMENT) qui a effectivement
 * encaissé les espèces au guichet.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_client_cartes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_matricule', 40)->index();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->decimal('montant_paye', 15, 2)->default(0);
            $table->string('devise_code', 3)->default('CDF');
            $table->string('token_verification', 64)->unique();
            $table->enum('statut', ['PAYEE', 'IMPRIMEE', 'REVOQUEE'])->default('PAYEE');
            $table->string('agent_encaissement_matricule', 40)->nullable();
            $table->unsignedBigInteger('guichet_id')->nullable();
            $table->timestamp('imprimee_le')->nullable();
            $table->string('imprimee_par_matricule', 40)->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->foreign('client_matricule', 'tb_client_cartes_client_fk')
                ->references('matricule')->on('tb_clients')
                ->onDelete('cascade');

            $table->foreign('transaction_id', 'tb_client_cartes_transaction_fk')
                ->references('id')->on('tb_transactions')
                ->onDelete('set null');

            $table->index(['client_matricule', 'statut'], 'idx_client_cartes_statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_client_cartes');
    }
};
