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
        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->boolean('prelevement_auto_autorise')->default(false)->after('statut_global')
                  ->comment('Autorise le prélèvement automatique même avant la date d\'échéance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->dropColumn('prelevement_auto_autorise');
        });
    }
};
