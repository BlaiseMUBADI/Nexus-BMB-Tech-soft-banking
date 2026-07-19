<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_demandes_virement', function (Blueprint $table) {
            $table->id();

            // Compte source
            $table->string('client_source_matricule', 191);
            $table->string('compte_source_code', 64);
            $table->decimal('montant_source', 18, 2);
            $table->string('devise_source', 5);

            // Compte destination
            $table->string('client_dest_matricule', 191);
            $table->string('compte_dest_code', 64);
            $table->decimal('montant_dest', 18, 2);
            $table->string('devise_dest', 5);

            // Taux de change appliqué si devises différentes (nul si même devise)
            $table->decimal('taux_change', 14, 6)->nullable();

            $table->text('motif');

            $table->enum('statut', ['EN_ATTENTE', 'APPROUVEE', 'REJETEE'])->default('EN_ATTENTE');

            $table->string('comptable_matricule', 50);
            $table->timestamp('propose_le')->nullable();

            $table->string('validateur_matricule', 50)->nullable();
            $table->text('commentaire_validateur')->nullable();
            $table->timestamp('traite_le')->nullable();

            // Renseigné uniquement après exécution (APPROUVEE) : la transaction/écriture générée
            $table->unsignedBigInteger('transaction_id')->nullable();

            $table->timestamps();

            $table->foreign('client_source_matricule', 'fk_vir_client_src')->references('matricule')->on('tb_clients')->restrictOnDelete();
            $table->foreign('client_dest_matricule', 'fk_vir_client_dst')->references('matricule')->on('tb_clients')->restrictOnDelete();
            $table->foreign('compte_source_code', 'fk_vir_compte_src')->references('code_compte')->on('tb_comptes')->restrictOnDelete();
            $table->foreign('compte_dest_code', 'fk_vir_compte_dst')->references('code_compte')->on('tb_comptes')->restrictOnDelete();
            $table->foreign('comptable_matricule', 'fk_vir_comptable')->references('matricule')->on('tb_agents')->restrictOnDelete();
            $table->foreign('validateur_matricule', 'fk_vir_validateur')->references('matricule')->on('tb_agents')->nullOnDelete();
            $table->foreign('transaction_id', 'fk_vir_transaction')->references('id')->on('tb_transactions')->nullOnDelete();

            $table->index(['statut', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_demandes_virement');
    }
};
