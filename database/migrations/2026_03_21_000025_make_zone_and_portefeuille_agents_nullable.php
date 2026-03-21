<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE tb_zones MODIFY agent_commercial_matricule VARCHAR(50) NULL');
        DB::statement('ALTER TABLE tb_portefeuilles_agents MODIFY agent_matricule VARCHAR(50) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE tb_zones MODIFY agent_commercial_matricule VARCHAR(50) NOT NULL');
        DB::statement('ALTER TABLE tb_portefeuilles_agents MODIFY agent_matricule VARCHAR(50) NOT NULL');
    }
};
