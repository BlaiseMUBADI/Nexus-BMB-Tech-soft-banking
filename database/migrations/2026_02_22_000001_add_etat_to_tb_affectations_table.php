<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('tb_affectations', function (Blueprint $table) {
            $table->string('etat')->default('actif')->after('date_fin');
        });
    }

    public function down()
    {
        Schema::table('tb_affectations', function (Blueprint $table) {
            $table->dropColumn('etat');
        });
    }
};
