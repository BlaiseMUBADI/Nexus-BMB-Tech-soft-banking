<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_categories_recettes', function (Blueprint $table) {
            $table->string('code', 50)->nullable()->unique()->after('libelle');
        });

        // Catégorie dédiée aux frais de carte membre : le code CARTE_MEMBRE
        // déclenche (RecetteController) la sélection obligatoire d'un client,
        // l'application du frais configuré (Trésorerie > Commissions) et la
        // création d'un enregistrement ClientCarte PAYEE (carte imprimable).
        DB::table('tb_categories_recettes')->updateOrInsert(
            ['code' => 'CARTE_MEMBRE'],
            [
                'libelle'                 => 'Frais de carte membre',
                'numero_compte_produit'   => '7072',
                'est_actif'               => true,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('tb_categories_recettes')->where('code', 'CARTE_MEMBRE')->delete();

        Schema::table('tb_categories_recettes', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};
