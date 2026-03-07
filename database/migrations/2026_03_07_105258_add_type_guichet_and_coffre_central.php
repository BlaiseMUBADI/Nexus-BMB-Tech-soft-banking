<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_caisses_guichets', function (Blueprint $table) {
            // Type de guichet :
            //   FIXE    = guichetier de bureau (besoin de fonds de roulement)
            //   MOBILE  = agent de terrain (départ à 0, dégagement le soir)
            //   CENTRAL = coffre-fort central (source de tous les fonds)
            $table->enum('type_guichet', ['FIXE', 'MOBILE', 'CENTRAL'])
                  ->default('FIXE')
                  ->after('code_guichet');
        });

        // Insérer le COFFRE-FORT CENTRAL s'il n'existe pas encore
        $existe = DB::table('tb_caisses_guichets')
            ->where('code_guichet', 'COFFRE_01')
            ->exists();

        if (!$existe) {
            $coffreId = DB::table('tb_caisses_guichets')->insertGetId([
                'code_guichet'        => 'COFFRE_01',
                'type_guichet'        => 'CENTRAL',
                'intitule'            => 'Coffre-Fort Central EBEN',
                'statut_operationnel' => 'OUVERT',
                'created_at'          => now(),
            ]);

            // Créer une ligne de solde pour chaque devise existante
            $devises = DB::table('tb_devises')->pluck('code_iso');
            foreach ($devises as $code) {
                DB::table('tb_caisses_guichets_soldes')->insert([
                    'guichet_id'      => $coffreId,
                    'devise_code'     => $code,
                    'solde_en_caisse' => 0.00,
                    'updated_at'      => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Supprimer le coffre central
        $coffre = DB::table('tb_caisses_guichets')->where('code_guichet', 'COFFRE_01')->first();
        if ($coffre) {
            DB::table('tb_caisses_guichets_soldes')->where('guichet_id', $coffre->id)->delete();
            DB::table('tb_caisses_guichets')->where('id', $coffre->id)->delete();
        }

        Schema::table('tb_caisses_guichets', function (Blueprint $table) {
            $table->dropColumn('type_guichet');
        });
    }
};

