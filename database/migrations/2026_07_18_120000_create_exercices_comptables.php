<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_exercices_comptables', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('annee')->unique();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('statut', ['OUVERT', 'EN_ATTENTE_VALIDATION', 'CLOTURE'])->default('OUVERT');
            $table->decimal('resultat_net_cloture', 18, 2)->nullable();
            $table->string('propose_par_matricule', 50)->nullable();
            $table->timestamp('propose_le')->nullable();
            $table->string('valide_par_matricule', 50)->nullable();
            $table->timestamp('valide_le')->nullable();
            $table->string('rejete_par_matricule', 50)->nullable();
            $table->timestamp('rejete_le')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        Schema::create('tb_soldes_ouverture', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exercice_id');
            $table->string('numero_compte', 20);
            // Solde signé : positif = débiteur (ACTIF), négatif = créditeur (PASSIF)
            $table->decimal('solde_ouverture', 18, 2)->default(0);
            $table->timestamps();

            $table->foreign('exercice_id', 'fk_solde_ouv_exercice')
                ->references('id')->on('tb_exercices_comptables')->cascadeOnDelete();
            $table->foreign('numero_compte', 'fk_solde_ouv_compte')
                ->references('numero_compte')->on('tb_plan_comptable')->restrictOnDelete();

            $table->unique(['exercice_id', 'numero_compte'], 'uniq_exercice_compte');
        });

        // ── Création automatique du premier exercice comptable ──
        // Il englobe tout l'historique existant (aucun exercice précédent = pas de RAN à établir).
        $premiereEcriture = DB::table('tb_compta_journaux')->min('date_ecriture');
        $dateDebut = $premiereEcriture ? \Carbon\Carbon::parse($premiereEcriture)->startOfYear()->toDateString() : now()->startOfYear()->toDateString();
        $anneeActuelle = (int) now()->year;

        DB::table('tb_exercices_comptables')->insertOrIgnore([
            'annee' => $anneeActuelle,
            'date_debut' => $dateDebut,
            'date_fin' => $anneeActuelle . '-12-31',
            'statut' => 'OUVERT',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_soldes_ouverture');
        Schema::dropIfExists('tb_exercices_comptables');
    }
};
