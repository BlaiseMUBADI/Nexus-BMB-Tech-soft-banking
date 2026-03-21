<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->dropForeignIfExists('tb_credit_demandes', [
            'tb_credit_demandes_compte_id_foreign',
            'tb_crd_compte_fk',
        ]);
        $this->dropForeignIfExists('tb_credit_demandes', [
            'tb_credit_demandes_portefeuille_id_foreign',
        ]);

        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->string('compte_id', 64)->nullable()->change();
            $table->unsignedBigInteger('portefeuille_id')->nullable()->change();
        });

        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->foreign('compte_id')->references('code_compte')->on('tb_comptes')->restrictOnDelete();
            $table->foreign('portefeuille_id')->references('id')->on('tb_portefeuilles_agents')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        $this->dropForeignIfExists('tb_credit_demandes', [
            'tb_credit_demandes_compte_id_foreign',
            'tb_crd_compte_fk',
        ]);
        $this->dropForeignIfExists('tb_credit_demandes', [
            'tb_credit_demandes_portefeuille_id_foreign',
        ]);

        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->string('compte_id', 64)->nullable(false)->change();
            $table->unsignedBigInteger('portefeuille_id')->nullable(false)->change();
        });

        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->foreign('compte_id')->references('code_compte')->on('tb_comptes')->restrictOnDelete();
            $table->foreign('portefeuille_id')->references('id')->on('tb_portefeuilles_agents')->restrictOnDelete();
        });
    }

    private function dropForeignIfExists(string $table, array $constraintNames): void
    {
        foreach ($constraintNames as $constraintName) {
            try {
                DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraintName}");
            } catch (\Throwable $exception) {
                // La contrainte peut ne pas exister selon l'historique de la base.
            }
        }
    }
};