<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration : Mise à jour des types de comptes bancaires
 *
 * Anciens types : COURANT, EPARGNE_LIBRE, EPARGNE_BLOQUEE, CAUTION_CREDIT
 * Nouveaux types : CC (Compte Courant), RMB (Remboursement), GTC (Caution),
 *                  DAT (Dépôt à Terme), EAV (Épargne & Vie)
 *
 * Format code compte : 243-525/14-<TYPE>-<5 chiffres><3 lettres>
 * Exemple : 243-525/14-CC-00001ABC
 */
class UpdateTypeEnumTbComptes extends Migration
{
    public function up()
    {
        // 1. Modifier l'enum pour accepter anciens ET nouveaux types (transition)
        DB::statement("ALTER TABLE `tb_comptes` MODIFY COLUMN `type`
            ENUM('COURANT','EPARGNE_LIBRE','EPARGNE_BLOQUEE','CAUTION_CREDIT','CC','RMB','GTC','DAT','EAV')
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
            COMMENT 'CC=Compte Courant, RMB=Remboursement, GTC=Caution, DAT=Dépôt à Terme, EAV=Épargne & Vie'");

        // 2. Migrer les anciennes valeurs vers les nouveaux codes
        DB::table('tb_comptes')->where('type', 'COURANT')->update(['type' => 'CC']);
        DB::table('tb_comptes')->where('type', 'EPARGNE_LIBRE')->update(['type' => 'EAV']);
        DB::table('tb_comptes')->where('type', 'EPARGNE_BLOQUEE')->update(['type' => 'DAT']);
        DB::table('tb_comptes')->where('type', 'CAUTION_CREDIT')->update(['type' => 'GTC']);

        // 3. Restreindre l'enum aux nouveaux codes uniquement
        DB::statement("ALTER TABLE `tb_comptes` MODIFY COLUMN `type`
            ENUM('CC','RMB','GTC','DAT','EAV')
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
            COMMENT 'CC=Compte Courant, RMB=Remboursement, GTC=Caution, DAT=Dépôt à Terme, EAV=Épargne & Vie'");
    }

    public function down()
    {
        // 1. Élargir l'enum pour accepter tous les types
        DB::statement("ALTER TABLE `tb_comptes` MODIFY COLUMN `type`
            ENUM('COURANT','EPARGNE_LIBRE','EPARGNE_BLOQUEE','CAUTION_CREDIT','CC','RMB','GTC','DAT','EAV')
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");

        // 2. Restaurer les anciennes valeurs
        DB::table('tb_comptes')->where('type', 'CC')->update(['type' => 'COURANT']);
        DB::table('tb_comptes')->where('type', 'EAV')->update(['type' => 'EPARGNE_LIBRE']);
        DB::table('tb_comptes')->where('type', 'DAT')->update(['type' => 'EPARGNE_BLOQUEE']);
        DB::table('tb_comptes')->where('type', 'GTC')->update(['type' => 'CAUTION_CREDIT']);

        // 3. Restreindre aux anciens codes
        DB::statement("ALTER TABLE `tb_comptes` MODIFY COLUMN `type`
            ENUM('COURANT','EPARGNE_LIBRE','EPARGNE_BLOQUEE','CAUTION_CREDIT')
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
    }
}
