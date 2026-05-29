<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE tb_credit_deblocages MODIFY COLUMN numero_ordre VARCHAR(80) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tb_credit_deblocages MODIFY COLUMN numero_ordre VARCHAR(30) NULL");
    }
};
