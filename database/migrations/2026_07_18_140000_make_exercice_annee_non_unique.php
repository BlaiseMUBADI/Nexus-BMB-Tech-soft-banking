<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permet des exercices comptables à période flexible (mensuelle, trimestrielle...),
     * pas uniquement annuelle : plusieurs exercices peuvent désormais partager la même
     * "année" d'étiquette (ex: 3 exercices trimestriels en 2026).
     */
    public function up(): void
    {
        Schema::table('tb_exercices_comptables', function (Blueprint $table) {
            $table->dropUnique(['annee']);
        });
    }

    public function down(): void
    {
        Schema::table('tb_exercices_comptables', function (Blueprint $table) {
            $table->unique('annee');
        });
    }
};
