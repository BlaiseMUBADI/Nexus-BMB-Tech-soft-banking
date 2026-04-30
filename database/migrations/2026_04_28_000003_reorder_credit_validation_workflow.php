<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Nouvel ordre métier: Agent crédit -> Contrôleur -> Chargé opérations -> Gérant
        DB::statement("UPDATE tb_credit_validations SET ordre_etape = CASE
            WHEN type_validateur = 'AGENT_CREDIT' THEN 1
            WHEN type_validateur = 'CONTROLEUR' THEN 2
            WHEN type_validateur = 'CHARGE_OPERATIONS' THEN 3
            WHEN type_validateur = 'GERANT' THEN 4
            ELSE ordre_etape
        END");

        // Recalcule les verrous d'étape pour dossiers encore en validation.
        DB::statement("UPDATE tb_credit_validations v
            JOIN tb_credit_demandes d ON d.id = v.credit_demande_id
            LEFT JOIN tb_credit_validations va ON va.credit_demande_id = v.credit_demande_id AND va.type_validateur = 'AGENT_CREDIT'
            LEFT JOIN tb_credit_validations vc ON vc.credit_demande_id = v.credit_demande_id AND vc.type_validateur = 'CONTROLEUR'
            LEFT JOIN tb_credit_validations vo ON vo.credit_demande_id = v.credit_demande_id AND vo.type_validateur = 'CHARGE_OPERATIONS'
            SET v.etape_precedente_ok = CASE
                WHEN v.type_validateur = 'AGENT_CREDIT' THEN 1
                WHEN v.type_validateur = 'CONTROLEUR' THEN IF(va.decision <> 'EN_ATTENTE', 1, 0)
                WHEN v.type_validateur = 'CHARGE_OPERATIONS' THEN IF(vc.decision <> 'EN_ATTENTE', 1, 0)
                WHEN v.type_validateur = 'GERANT' THEN IF(vo.decision <> 'EN_ATTENTE', 1, 0)
                ELSE v.etape_precedente_ok
            END
            WHERE d.statut_global = 'EN_VALIDATION'");
    }

    public function down(): void
    {
        // Ancien ordre: Agent crédit -> Chargé opérations -> Contrôleur -> Gérant
        DB::statement("UPDATE tb_credit_validations SET ordre_etape = CASE
            WHEN type_validateur = 'AGENT_CREDIT' THEN 1
            WHEN type_validateur = 'CHARGE_OPERATIONS' THEN 2
            WHEN type_validateur = 'CONTROLEUR' THEN 3
            WHEN type_validateur = 'GERANT' THEN 4
            ELSE ordre_etape
        END");

        DB::statement("UPDATE tb_credit_validations v
            JOIN tb_credit_demandes d ON d.id = v.credit_demande_id
            LEFT JOIN tb_credit_validations va ON va.credit_demande_id = v.credit_demande_id AND va.type_validateur = 'AGENT_CREDIT'
            LEFT JOIN tb_credit_validations vo ON vo.credit_demande_id = v.credit_demande_id AND vo.type_validateur = 'CHARGE_OPERATIONS'
            LEFT JOIN tb_credit_validations vc ON vc.credit_demande_id = v.credit_demande_id AND vc.type_validateur = 'CONTROLEUR'
            SET v.etape_precedente_ok = CASE
                WHEN v.type_validateur = 'AGENT_CREDIT' THEN 1
                WHEN v.type_validateur = 'CHARGE_OPERATIONS' THEN IF(va.decision <> 'EN_ATTENTE', 1, 0)
                WHEN v.type_validateur = 'CONTROLEUR' THEN IF(vo.decision <> 'EN_ATTENTE', 1, 0)
                WHEN v.type_validateur = 'GERANT' THEN IF(vc.decision <> 'EN_ATTENTE', 1, 0)
                ELSE v.etape_precedente_ok
            END
            WHERE d.statut_global = 'EN_VALIDATION'");
    }
};
