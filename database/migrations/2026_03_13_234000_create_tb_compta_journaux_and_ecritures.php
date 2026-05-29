<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tb_compta_journaux')) {
        Schema::create('tb_compta_journaux', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code_journal', 20)->default('CAI');
            $table->string('reference_piece', 80)->unique();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->enum('type_piece', ['OPERATION', 'ANNULATION', 'REGULARISATION'])->default('OPERATION');
            $table->string('devise_code', 3)->nullable();
            $table->string('libelle', 191);
            $table->enum('statut', ['COMPTABILISE', 'ANNULE'])->default('COMPTABILISE');
            $table->string('agent_matricule', 50)->nullable();
            $table->timestamp('date_ecriture')->useCurrent();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['transaction_id', 'type_piece'], 'idx_compta_journal_trans_type');
            $table->index(['date_ecriture', 'devise_code'], 'idx_compta_journal_date_devise');

            $table->foreign('transaction_id', 'fk_compta_journal_transaction')
                ->references('id')->on('tb_transactions')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('agent_matricule', 'fk_compta_journal_agent')
                ->references('matricule')->on('tb_agents')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('devise_code', 'fk_compta_journal_devise')
                ->references('code_iso')->on('tb_devises')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
        } // end hasTable tb_compta_journaux

        if (!Schema::hasTable('tb_compta_ecritures')) {
        Schema::create('tb_compta_ecritures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('journal_id');
            $table->string('numero_compte', 20);
            $table->string('devise_code', 3)->nullable();
            $table->string('libelle_ligne', 191)->nullable();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);
            $table->unsignedInteger('ordre')->default(1);
            $table->timestamps();

            $table->index(['numero_compte', 'devise_code'], 'idx_compta_ecriture_compte_devise');
            $table->index('journal_id', 'idx_compta_ecriture_journal');

            $table->foreign('journal_id', 'fk_compta_ecriture_journal')
                ->references('id')->on('tb_compta_journaux')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('numero_compte', 'fk_compta_ecriture_compte')
                ->references('numero_compte')->on('tb_plan_comptable')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('devise_code', 'fk_compta_ecriture_devise')
                ->references('code_iso')->on('tb_devises')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
        } // end hasTable tb_compta_ecritures
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_compta_ecritures');
        Schema::dropIfExists('tb_compta_journaux');
    }
};
