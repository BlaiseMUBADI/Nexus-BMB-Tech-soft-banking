<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_transactions', function (Blueprint $table) {
            $table->decimal('solde_compte_avant', 18, 2)->nullable()->after('montant_commission_total');
            $table->decimal('solde_compte_apres', 18, 2)->nullable()->after('solde_compte_avant');
            $table->decimal('montant_total_client', 18, 2)->nullable()->after('solde_compte_apres');
            $table->decimal('montant_net_client', 18, 2)->nullable()->after('montant_total_client');
        });
    }

    public function down(): void
    {
        Schema::table('tb_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'solde_compte_avant',
                'solde_compte_apres',
                'montant_total_client',
                'montant_net_client',
            ]);
        });
    }
};
