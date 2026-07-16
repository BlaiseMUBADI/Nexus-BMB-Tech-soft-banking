<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->decimal('commission_totale', 15, 2)->default(0)->after('total_interets');
        });

        Schema::table('tb_credit_echeanciers', function (Blueprint $table) {
            $table->decimal('total_commission', 15, 2)->default(0)->after('total_general');
        });

        Schema::table('tb_credit_echeances', function (Blueprint $table) {
            $table->decimal('commission_echeance', 15, 2)->default(0)->after('interet_echeance');
        });
    }

    public function down(): void
    {
        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->dropColumn('commission_totale');
        });

        Schema::table('tb_credit_echeanciers', function (Blueprint $table) {
            $table->dropColumn('total_commission');
        });

        Schema::table('tb_credit_echeances', function (Blueprint $table) {
            $table->dropColumn('commission_echeance');
        });
    }
};
