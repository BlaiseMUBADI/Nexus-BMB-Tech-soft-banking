<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_credit_commission_rules', function (Blueprint $table) {
            $table->id();
            $table->string('libelle', 200);
            $table->string('devise_code', 5);
            $table->enum('type_credit', ['INDIVIDUEL', 'SOLIDAIRE', 'PME', 'TOUS'])->default('TOUS');
            $table->string('code_zone', 20)->nullable();
            $table->unsignedBigInteger('portefeuille_id')->nullable();
            $table->decimal('montant_min', 15, 2)->nullable();
            $table->decimal('montant_max', 15, 2)->nullable();
            $table->enum('mode_calcul', ['FIXE', 'POURCENTAGE'])->default('FIXE');
            $table->decimal('valeur', 15, 4);
            $table->unsignedTinyInteger('priorite')->default(0);
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index(['devise_code', 'type_credit', 'est_actif'], 'idx_ccr_devise_type');
            $table->index(['code_zone', 'portefeuille_id'], 'idx_ccr_zone_pf');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_credit_commission_rules');
    }
};
