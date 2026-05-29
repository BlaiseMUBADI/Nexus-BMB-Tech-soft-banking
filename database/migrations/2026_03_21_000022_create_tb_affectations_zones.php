<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tb_affectations_zones')) {
            return;
        }

        Schema::create('tb_affectations_zones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code_zone', 50);
            $table->string('agent_matricule', 50);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('Etat', 50)->default('ACTIF');
            $table->string('motif', 255)->nullable();
            $table->unsignedBigInteger('effectue_par_user_id')->nullable();
            $table->timestamps();

            $table->index(['code_zone', 'Etat', 'date_fin'], 'idx_aff_zone_active');
            $table->index(['agent_matricule', 'Etat', 'date_fin'], 'idx_aff_zone_agent_active');
            $table->index(['code_zone', 'date_debut'], 'idx_aff_zone_period');

            $table->foreign('agent_matricule', 'fk_aff_zone_agent')
                ->references('matricule')->on('tb_agents')
                ->restrictOnDelete()->cascadeOnUpdate();
        });

        $today = now()->toDateString();

        $rows = DB::table('tb_zones')
            ->whereNotNull('agent_commercial_matricule')
            ->select(['code_zone', 'agent_commercial_matricule'])
            ->get();

        foreach ($rows as $row) {
            DB::table('tb_affectations_zones')->insert([
                'code_zone' => $row->code_zone,
                'agent_matricule' => $row->agent_commercial_matricule,
                'date_debut' => $today,
                'date_fin' => null,
                'Etat' => 'ACTIF',
                'motif' => 'Initialisation depuis tb_zones',
                'effectue_par_user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_affectations_zones');
    }
};
