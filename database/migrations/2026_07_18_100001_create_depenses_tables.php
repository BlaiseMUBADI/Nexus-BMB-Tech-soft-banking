<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── Catégories de dépenses : mapping dynamique catégorie → compte OHADA de charge ──
        Schema::create('tb_categories_depenses', function (Blueprint $table) {
            $table->id();
            $table->string('libelle', 150);
            $table->string('numero_compte_charge', 20); // FK vers tb_plan_comptable
            $table->boolean('est_actif')->default(true);
            $table->timestamps();

            $table->foreign('numero_compte_charge', 'fk_cat_depense_compte')
                ->references('numero_compte')->on('tb_plan_comptable')
                ->restrictOnDelete()->restrictOnUpdate();
        });

        // ── Dépenses : métadonnées liées à une transaction de type DEPENSE ──
        Schema::create('tb_depenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id')->unique();
            $table->unsignedBigInteger('categorie_id');
            $table->text('motif');
            $table->string('piece_justificative', 255)->nullable();
            $table->string('agent_matricule', 50)->nullable();
            $table->timestamps();

            $table->foreign('transaction_id', 'fk_depense_transaction')
                ->references('id')->on('tb_transactions')
                ->cascadeOnDelete();
            $table->foreign('categorie_id', 'fk_depense_categorie')
                ->references('id')->on('tb_categories_depenses')
                ->restrictOnDelete();
            $table->foreign('agent_matricule', 'fk_depense_agent')
                ->references('matricule')->on('tb_agents')
                ->nullOnDelete();
        });

        // ── Catégories de départ (modifiables ensuite via l'interface) ──
        $now = now();
        DB::table('tb_categories_depenses')->insert([
            ['libelle' => 'Fournitures de bureau',                'numero_compte_charge' => '6051', 'est_actif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['libelle' => 'Transport / carburant',                 'numero_compte_charge' => '6111', 'est_actif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['libelle' => 'Loyer et charges locatives',            'numero_compte_charge' => '6221', 'est_actif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['libelle' => 'Entretien et réparations',              'numero_compte_charge' => '6241', 'est_actif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['libelle' => 'Électricité et eau',                    'numero_compte_charge' => '6251', 'est_actif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['libelle' => 'Téléphone et internet',                 'numero_compte_charge' => '6281', 'est_actif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['libelle' => 'Impôts et taxes',                       'numero_compte_charge' => '6411', 'est_actif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['libelle' => 'Autres charges diverses',                'numero_compte_charge' => '6581', 'est_actif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_depenses');
        Schema::dropIfExists('tb_categories_depenses');
    }
};
