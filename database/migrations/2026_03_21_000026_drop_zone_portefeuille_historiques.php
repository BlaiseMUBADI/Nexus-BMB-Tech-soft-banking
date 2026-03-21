<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('tb_zone_historiques');
        Schema::dropIfExists('tb_portefeuille_historiques');
    }

    public function down(): void
    {
    }
};