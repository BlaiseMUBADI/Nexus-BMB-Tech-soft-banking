<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_credit_demandes', 'agent_analyse_matricule')) {
                $table->string('agent_analyse_matricule', 50)
                    ->nullable()
                    ->after('agent_createur_matricule');

                $table->index('agent_analyse_matricule', 'idx_credit_demande_agent_analyse');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            if (Schema::hasColumn('tb_credit_demandes', 'agent_analyse_matricule')) {
                $table->dropIndex('idx_credit_demande_agent_analyse');
                $table->dropColumn('agent_analyse_matricule');
            }
        });
    }
};
