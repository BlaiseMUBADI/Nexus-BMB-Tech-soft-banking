<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tb_credit_validations') || !Schema::hasTable('tb_credit_demandes')) {
            return;
        }

        DB::transaction(function (): void {
            // Normalise tous les montants de validation a 2 decimales.
            DB::table('tb_credit_validations')
                ->whereNotNull('montant_valide')
                ->update([
                    'montant_valide' => DB::raw('ROUND(montant_valide, 2)'),
                ]);

            // Synchronise le montant approuve avec la derniere etape approuvee du workflow.
            DB::statement(
                "UPDATE tb_credit_demandes d
                 JOIN (
                     SELECT v.credit_demande_id, ROUND(v.montant_valide, 2) AS montant_retenu
                     FROM tb_credit_validations v
                     JOIN (
                         SELECT credit_demande_id, MAX(ordre_etape) AS max_ordre
                         FROM tb_credit_validations
                         WHERE decision IN ('APPROUVE', 'APPROUVE_AVEC_RESERVE')
                           AND montant_valide IS NOT NULL
                         GROUP BY credit_demande_id
                     ) x
                       ON x.credit_demande_id = v.credit_demande_id
                      AND x.max_ordre = v.ordre_etape
                     WHERE v.decision IN ('APPROUVE', 'APPROUVE_AVEC_RESERVE')
                       AND v.montant_valide IS NOT NULL
                 ) s ON s.credit_demande_id = d.id
                 SET d.montant_approuve = s.montant_retenu"
            );
        });
    }

    public function down(): void
    {
        // Migration de correction de donnees non reversible.
    }
};
