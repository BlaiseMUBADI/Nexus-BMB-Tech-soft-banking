<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_caisses_guichets', function (Blueprint $table) {
            if (Schema::hasColumn('tb_caisses_guichets', 'updated_at')) {
                return;
            }
            // created_at existe déjà — on ajoute uniquement updated_at
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });

        // Initialise updated_at = created_at pour les lignes existantes
        DB::statement('UPDATE tb_caisses_guichets SET updated_at = created_at WHERE updated_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_caisses_guichets', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
};
