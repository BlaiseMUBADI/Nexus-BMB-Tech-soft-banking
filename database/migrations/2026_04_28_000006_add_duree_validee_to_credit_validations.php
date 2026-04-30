<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_credit_validations', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_credit_validations', 'duree_mois_validee')) {
                $table->unsignedTinyInteger('duree_mois_validee')->nullable()->after('montant_valide');
            }
        });

        DB::table('tb_credit_validations as v')
            ->join('tb_credit_demandes as d', 'd.id', '=', 'v.credit_demande_id')
            ->whereIn('v.decision', ['APPROUVE', 'APPROUVE_AVEC_RESERVE'])
            ->whereNull('v.duree_mois_validee')
            ->update(['v.duree_mois_validee' => DB::raw('d.duree_mois')]);
    }

    public function down(): void
    {
        Schema::table('tb_credit_validations', function (Blueprint $table) {
            if (Schema::hasColumn('tb_credit_validations', 'duree_mois_validee')) {
                $table->dropColumn('duree_mois_validee');
            }
        });
    }
};
