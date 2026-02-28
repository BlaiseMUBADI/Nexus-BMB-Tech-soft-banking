<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToTbZones extends Migration
{
    public function up()
    {
        Schema::table('tb_zones', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    public function down()
    {
        Schema::table('tb_zones', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
}
