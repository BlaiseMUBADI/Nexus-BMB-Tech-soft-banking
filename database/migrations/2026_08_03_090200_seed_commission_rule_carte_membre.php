<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Frais fixe d'émission de la carte membre, géré via le moteur de
 * commissions existant (tb_commission_rules), exactement comme demandé :
 * "une somme définie dans Trésorerie" — modifiable depuis
 * Trésorerie > Commissions sans toucher au code.
 *
 * code_operation = 'CARTE_MEMBRE' est une valeur libre (colonne varchar,
 * pas d'enum SQL) dédiée à cet usage — elle n'interfère pas avec les
 * règles de commission des opérations de caisse classiques (DEPOT,
 * RETRAIT, PAIEMENT, ...).
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('tb_commission_rules')->insertOrIgnore([
            'libelle' => 'Frais carte membre',
            'code_operation' => 'CARTE_MEMBRE',
            'type_compte' => 'TOUS',
            'type_guichet' => 'TOUS',
            'devise_code' => 'CDF',
            'code_zone' => null,
            'portefeuille_id' => null,
            'montant_min' => null,
            'montant_max' => null,
            'mode_calcul' => 'FIXE',
            'valeur' => 5000,
            'priorite' => 100,
            'date_debut' => $now->toDateString(),
            'date_fin' => null,
            'est_actif' => true,
            'observations' => "Frais d'émission d'une carte membre physique (montant ajustable ici).",
            'created_by_agent' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('tb_commission_rules')
            ->where('code_operation', 'CARTE_MEMBRE')
            ->where('libelle', 'Frais carte membre')
            ->delete();
    }
};
