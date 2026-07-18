<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tb_agents')->insertOrIgnore([
            'matricule' => 'SYSTEM-AUTO',
            'nom' => 'Système',
            'prenom' => 'Automatique',
            'statut' => 'actif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('tb_agents')->where('matricule', 'SYSTEM-AUTO')->delete();
    }
};
