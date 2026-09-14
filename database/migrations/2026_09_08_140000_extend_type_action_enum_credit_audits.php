<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * BUG CRITIQUE : plusieurs actions du module Crédit utilisent des valeurs
 * `type_action` qui n'existaient PAS dans l'ENUM de tb_credit_audits :
 *   - PASSAGE_RETARD      (commande credit:marquer-retards)
 *   - AFFECTATION_ANALYSE (affecterAnalyse())
 *   - CLOTURE_CREDIT      (clôture d'un dossier soldé)
 *   - IMPORT_HISTORIQUE   (import d'un ancien dossier)
 *
 * Résultat : `logAudit()` échouait avec "Data truncated for column
 * type_action" — cette erreur (Warning MySQL en mode strict = exception PDO)
 * remontait dans la DB::transaction() englobante et faisait ROLLBACK
 * l'action ENTIÈRE (le vrai changement métier, pas seulement la trace
 * d'audit). C'est la cause racine du bug "statut en retard qui ne se met
 * jamais à jour" : la commande planifiée credit:marquer-retards échouait et
 * annulait systématiquement TOUTES ses mises à jour de statut.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE tb_credit_audits MODIFY type_action ENUM(
            'CREATION','SOUMISSION','ANALYSE_DEMARREE','ANALYSE_COMPLETE',
            'VALIDATION_PARTIELLE','VALIDATION_COMPLETE','REJET','DEBLOCAGE',
            'REMBOURSEMENT','ANNULATION','SUSPENSION','LEVER_SUSPENSION',
            'SIGNALEMENT_SUSPECT','LEVER_SUSPICION','MODIFICATION',
            'PASSAGE_RETARD','AFFECTATION_ANALYSE','CLOTURE_CREDIT','IMPORT_HISTORIQUE'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tb_credit_audits MODIFY type_action ENUM(
            'CREATION','SOUMISSION','ANALYSE_DEMARREE','ANALYSE_COMPLETE',
            'VALIDATION_PARTIELLE','VALIDATION_COMPLETE','REJET','DEBLOCAGE',
            'REMBOURSEMENT','ANNULATION','SUSPENSION','LEVER_SUSPENSION',
            'SIGNALEMENT_SUSPECT','LEVER_SUSPICION','MODIFICATION'
        ) NOT NULL");
    }
};
