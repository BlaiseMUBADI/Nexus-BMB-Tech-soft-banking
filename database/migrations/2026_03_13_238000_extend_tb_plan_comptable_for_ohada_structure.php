<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE tb_plan_comptable MODIFY COLUMN type_compte ENUM('ACTIF','PASSIF','CHARGE','PRODUIT','MIXTE','HORS_BILAN') NOT NULL");

        Schema::table('tb_plan_comptable', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_plan_comptable', 'classe_ohada')) {
                $table->char('classe_ohada', 1)->nullable()->after('numero_compte');
            }

            if (!Schema::hasColumn('tb_plan_comptable', 'parent_compte')) {
                $table->string('parent_compte', 20)->nullable()->after('libelle');
            }

            if (!Schema::hasColumn('tb_plan_comptable', 'niveau')) {
                $table->unsignedTinyInteger('niveau')->default(1)->after('parent_compte');
            }

            if (!Schema::hasColumn('tb_plan_comptable', 'est_mouvementable')) {
                $table->boolean('est_mouvementable')->default(true)->after('type_compte');
            }

            if (!Schema::hasColumn('tb_plan_comptable', 'est_actif')) {
                $table->boolean('est_actif')->default(true)->after('est_mouvementable');
            }
        });

        DB::statement("UPDATE tb_plan_comptable SET classe_ohada = LEFT(numero_compte, 1) WHERE classe_ohada IS NULL OR classe_ohada = ''");
        DB::statement("UPDATE tb_plan_comptable SET niveau = CHAR_LENGTH(numero_compte) WHERE niveau IS NULL OR niveau = 0");
        DB::statement("UPDATE tb_plan_comptable SET est_mouvementable = 0 WHERE CHAR_LENGTH(numero_compte) <= 2");

        Schema::table('tb_plan_comptable', function (Blueprint $table) {
            $table->index(['classe_ohada', 'numero_compte'], 'idx_plan_ohada_classe_num');
            $table->index('parent_compte', 'idx_plan_ohada_parent');
            $table->index('est_actif', 'idx_plan_ohada_actif');
        });
    }

    public function down(): void
    {
        Schema::table('tb_plan_comptable', function (Blueprint $table) {
            $table->dropIndex('idx_plan_ohada_classe_num');
            $table->dropIndex('idx_plan_ohada_parent');
            $table->dropIndex('idx_plan_ohada_actif');
            $table->dropColumn(['classe_ohada', 'parent_compte', 'niveau', 'est_mouvementable', 'est_actif']);
        });

        DB::statement("ALTER TABLE tb_plan_comptable MODIFY COLUMN type_compte ENUM('ACTIF','PASSIF','CHARGE','PRODUIT') NOT NULL");
    }
};
