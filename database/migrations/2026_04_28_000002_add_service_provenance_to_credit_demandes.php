<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_credit_demandes', 'service_provenance')) {
                $table->string('service_provenance', 100)->nullable()->after('garantie_description')
                      ->comment('Service ou département ayant référé le client');
            }
            if (!Schema::hasColumn('tb_credit_demandes', 'referent_nom')) {
                $table->string('referent_nom', 120)->nullable()->after('service_provenance')
                      ->comment('Nom du référent dans le service');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_credit_demandes', function (Blueprint $table) {
            $table->dropColumn(['service_provenance', 'referent_nom']);
        });
    }
};
