<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_affectations_portefeuilles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('portefeuille_id');
            $table->string('agent_matricule', 50)->nullable();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('Etat', 50)->default('ACTIF');
            $table->string('motif', 255)->nullable();
            $table->unsignedBigInteger('effectue_par_user_id')->nullable();
            $table->timestamps();

            $table->index(['portefeuille_id', 'Etat', 'date_fin'], 'idx_aff_pf_active');
            $table->index(['agent_matricule', 'Etat', 'date_fin'], 'idx_aff_pf_agent_active');
            $table->index(['portefeuille_id', 'date_debut'], 'idx_aff_pf_period');

            $table->foreign('portefeuille_id', 'fk_aff_pf_portefeuille')
                ->references('id')->on('tb_portefeuilles_agents')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreign('agent_matricule', 'fk_aff_pf_agent')
                ->references('matricule')->on('tb_agents')
                ->nullOnDelete()->cascadeOnUpdate();
        });

        $today = now()->toDateString();

        $rows = DB::table('tb_portefeuilles_agents')
            ->whereNotNull('agent_matricule')
            ->select(['id', 'agent_matricule'])
            ->get();

        foreach ($rows as $row) {
            DB::table('tb_affectations_portefeuilles')->insert([
                'portefeuille_id' => $row->id,
                'agent_matricule' => $row->agent_matricule,
                'date_debut' => $today,
                'date_fin' => null,
                'Etat' => 'ACTIF',
                'motif' => 'Initialisation depuis tb_portefeuilles_agents',
                'effectue_par_user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_affectations_portefeuilles');
    }
};
