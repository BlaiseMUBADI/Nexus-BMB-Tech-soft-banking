<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute la valeur 'DEMANDE_APPRO' à l'enum type_flux de tb_mouvements_inter_caisses.
 * Permet aux guichetiers de soumettre une demande d'approvisionnement
 * sans modifier ni créer de table supplémentaire.
 */
return new class extends Migration
{
    public function up(): void
    {
        // MySQL : modification d'un ENUM via ALTER TABLE
        DB::statement("
            ALTER TABLE tb_mouvements_inter_caisses
            MODIFY COLUMN type_flux ENUM('ALIMENTATION','DEGAGEMENT','TRANSFERT','DEMANDE_APPRO') NOT NULL
        ");
    }

    public function down(): void
    {
        // Retire 'DEMANDE_APPRO' — attention : supprime les lignes avec cette valeur si existantes
        DB::statement("
            ALTER TABLE tb_mouvements_inter_caisses
            MODIFY COLUMN type_flux ENUM('ALIMENTATION','DEGAGEMENT','TRANSFERT') NOT NULL
        ");
    }
};
