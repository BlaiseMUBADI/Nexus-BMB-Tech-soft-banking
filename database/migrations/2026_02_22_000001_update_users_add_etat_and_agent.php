<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('etat')->default('actif');
            $table->string('login')->unique()->nullable();
            $table->unsignedBigInteger('agent_matricule')->nullable();
            $table->foreign('agent_matricule')->references('matricule')->on('tb_agents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['agent_matricule']);
            $table->dropColumn(['etat', 'login', 'agent_matricule']);
        });
    }
};
