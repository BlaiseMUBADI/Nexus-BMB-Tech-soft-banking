<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_categories_recettes', function (Blueprint $table) {
            $table->id();
            $table->string('libelle', 150);
            $table->string('numero_compte_produit', 20); // FK vers tb_plan_comptable (classe 7)
            $table->boolean('est_actif')->default(true);
            $table->timestamps();

            $table->foreign('numero_compte_produit', 'fk_cat_recette_compte')
                ->references('numero_compte')->on('tb_plan_comptable')
                ->restrictOnDelete()->restrictOnUpdate();
        });

        Schema::create('tb_recettes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id')->unique();
            $table->unsignedBigInteger('categorie_id');
            $table->text('motif');
            $table->string('piece_justificative', 255)->nullable();
            $table->string('agent_matricule', 50)->nullable();
            $table->timestamps();

            $table->foreign('transaction_id', 'fk_recette_transaction')
                ->references('id')->on('tb_transactions')->cascadeOnDelete();
            $table->foreign('categorie_id', 'fk_recette_categorie')
                ->references('id')->on('tb_categories_recettes')->restrictOnDelete();
            $table->foreign('agent_matricule', 'fk_recette_agent')
                ->references('matricule')->on('tb_agents')->nullOnDelete();
        });

        $now = now();
        DB::table('tb_categories_recettes')->insert([
            ['libelle' => 'Vente de formulaires / imprimés', 'numero_compte_produit' => '7051', 'est_actif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['libelle' => 'Frais de dossier divers',          'numero_compte_produit' => '7072', 'est_actif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['libelle' => 'Autres produits divers',           'numero_compte_produit' => '7581', 'est_actif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_recettes');
        Schema::dropIfExists('tb_categories_recettes');
    }
};
