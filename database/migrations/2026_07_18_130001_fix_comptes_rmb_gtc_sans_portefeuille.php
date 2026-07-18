<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Corrige les comptes RMB/GTC existants dont portefeuille_id est NULL
     * (bug de création : le portefeuille du dossier crédit n'était pas repris).
     * On retrouve le dossier crédit correspondant (client + devise + type de compte)
     * et on reprend son portefeuille_id.
     */
    public function up(): void
    {
        $comptesSansPortefeuille = DB::table('tb_comptes')
            ->whereIn('type', ['RMB', 'GTC'])
            ->whereNull('portefeuille_id')
            ->get(['code_compte', 'client_matricule', 'devise', 'type']);

        $corriges = 0;

        foreach ($comptesSansPortefeuille as $compte) {
            $dossier = DB::table('tb_credit_demandes')
                ->where('client_matricule', $compte->client_matricule)
                ->where('devise', $compte->devise)
                ->whereNotNull('portefeuille_id')
                ->orderByDesc('created_at')
                ->first(['portefeuille_id']);

            if ($dossier) {
                DB::table('tb_comptes')
                    ->where('code_compte', $compte->code_compte)
                    ->update(['portefeuille_id' => $dossier->portefeuille_id]);
                $corriges++;
            }
        }

        \Illuminate\Support\Facades\Log::info("[Migration] Comptes RMB/GTC corrigés (portefeuille_id retrouvé) : {$corriges}");
    }

    public function down(): void
    {
        // Pas de rollback destructif : on ne remet pas les portefeuille_id à null.
    }
};
