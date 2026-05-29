<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tb_demandes_modification')) {
            return;
        }

        Schema::create('tb_demandes_modification', function (Blueprint $table) {
            $table->id();

            // ── Transaction concernée ─────────────────────────────────────
            $table->unsignedBigInteger('transaction_id');
            $table->string('reference_operation', 60)->nullable()->comment('Référence de l\'opération initiale');
            $table->unsignedBigInteger('guichet_id')->nullable();
            $table->string('compte_code', 60)->nullable()->comment('Compte client concerné');
            $table->string('client_nom', 200)->nullable()->comment('Nom du client (dénormalisation audit)');

            // ── Détails initiaux de l'opération ──────────────────────────
            $table->string('type_operation', 30)->nullable()->comment('Type original : DEPOT, RETRAIT...');
            $table->string('devise_code', 10)->nullable();
            $table->decimal('ancien_montant', 15, 2)->nullable()->comment('Montant original');
            $table->text('anciennes_observations')->nullable()->comment('Observations originales');

            // ── Demande ───────────────────────────────────────────────────
            $table->enum('type_demande', ['MODIFICATION', 'SUPPRESSION']);
            $table->string('agent_matricule', 60)->nullable()->comment('Guichetier demandeur');
            $table->text('motif')->comment('Motif obligatoire de la demande');

            // Données souhaitées pour MODIFICATION
            $table->decimal('nouveau_montant', 15, 2)->nullable()->comment('Nouveau montant demandé');
            $table->text('nouvelles_observations')->nullable()->comment('Nouvelles observations demandées');

            // ── Statut de la demande ──────────────────────────────────────
            $table->enum('statut', ['EN_ATTENTE', 'APPROUVEE', 'REJETEE'])->default('EN_ATTENTE');
            $table->string('superviseur_matricule', 60)->nullable()->comment('Superviseur ayant traité');
            $table->text('commentaire_superviseur')->nullable();
            $table->timestamp('traitee_le')->nullable()->comment('Date de traitement par le superviseur');

            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('tb_transactions')->cascadeOnDelete();
            $table->foreign('guichet_id')->references('id')->on('tb_caisses_guichets')->nullOnDelete();

            $table->index(['statut', 'created_at']);
            $table->index('agent_matricule');
            $table->index('superviseur_matricule');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_demandes_modification');
    }
};
