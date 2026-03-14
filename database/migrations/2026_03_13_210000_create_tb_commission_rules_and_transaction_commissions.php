<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_commission_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('libelle', 150);
            $table->string('code_operation', 50)->default('TOUS');
            $table->string('type_compte', 20)->default('TOUS');
            $table->string('type_guichet', 20)->default('TOUS');
            $table->char('devise_code', 3)->nullable();
            $table->string('code_zone', 50)->nullable();
            $table->unsignedBigInteger('portefeuille_id')->nullable();
            $table->decimal('montant_min', 18, 2)->nullable();
            $table->decimal('montant_max', 18, 2)->nullable();
            $table->enum('mode_calcul', ['FIXE', 'POURCENTAGE']);
            $table->decimal('valeur', 18, 4);
            $table->unsignedInteger('priorite')->default(100);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->text('observations')->nullable();
            $table->string('created_by_agent', 50)->nullable();
            $table->timestamps();

            $table->index(['est_actif', 'date_debut', 'date_fin'], 'idx_comm_rules_active_dates');
            $table->index(['code_operation', 'type_compte', 'type_guichet'], 'idx_comm_rules_scope');
            $table->index(['devise_code', 'code_zone', 'portefeuille_id'], 'idx_comm_rules_context');

            $table->foreign('devise_code', 'tb_comm_rules_devise_fk')
                ->references('code_iso')->on('tb_devises')
                ->nullOnDelete();

            $table->foreign('code_zone', 'tb_comm_rules_zone_fk')
                ->references('code_zone')->on('tb_zones')
                ->nullOnDelete();

            $table->foreign('portefeuille_id', 'tb_comm_rules_portefeuille_fk')
                ->references('id')->on('tb_portefeuilles_agents')
                ->nullOnDelete();
        });

        Schema::table('tb_transactions', function (Blueprint $table) {
            $table->decimal('montant_commission_total', 18, 2)
                ->default(0)
                ->after('montant');
        });

        Schema::create('tb_transaction_commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('commission_rule_id')->nullable();
            $table->string('libelle', 150);
            $table->string('code_operation', 50);
            $table->string('type_compte', 20)->nullable();
            $table->string('type_guichet', 20)->nullable();
            $table->char('devise_code', 3)->nullable();
            $table->string('code_zone', 50)->nullable();
            $table->unsignedBigInteger('portefeuille_id')->nullable();
            $table->enum('mode_calcul', ['FIXE', 'POURCENTAGE']);
            $table->decimal('valeur_snapshot', 18, 4);
            $table->decimal('base_calcul', 18, 2)->default(0);
            $table->decimal('montant_commission', 18, 2)->default(0);
            $table->timestamp('date_application')->useCurrent();
            $table->string('agent_matricule', 50)->nullable();
            $table->unsignedBigInteger('guichet_id')->nullable();
            $table->timestamps();

            $table->index(['transaction_id', 'date_application'], 'idx_trans_comm_tx_date');
            $table->index(['code_operation', 'type_compte', 'type_guichet'], 'idx_trans_comm_scope');

            $table->foreign('transaction_id', 'tb_trans_comm_tx_fk')
                ->references('id')->on('tb_transactions')
                ->cascadeOnDelete();

            $table->foreign('commission_rule_id', 'tb_trans_comm_rule_fk')
                ->references('id')->on('tb_commission_rules')
                ->nullOnDelete();

            $table->foreign('code_zone', 'tb_trans_comm_zone_fk')
                ->references('code_zone')->on('tb_zones')
                ->nullOnDelete();

            $table->foreign('portefeuille_id', 'tb_trans_comm_portefeuille_fk')
                ->references('id')->on('tb_portefeuilles_agents')
                ->nullOnDelete();

            $table->foreign('guichet_id', 'tb_trans_comm_guichet_fk')
                ->references('id')->on('tb_caisses_guichets')
                ->nullOnDelete();

            $table->foreign('agent_matricule', 'tb_trans_comm_agent_fk')
                ->references('matricule')->on('tb_agents')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_transaction_commissions');

        Schema::table('tb_transactions', function (Blueprint $table) {
            $table->dropColumn('montant_commission_total');
        });

        Schema::dropIfExists('tb_commission_rules');
    }
};