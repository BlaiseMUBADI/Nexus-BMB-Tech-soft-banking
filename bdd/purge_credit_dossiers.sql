-- ============================================================
-- PURGE COMPLETE DES DOSSIERS DE CREDIT (Nexus-BMB-Tech-soft-banking)
-- ============================================================
-- ATTENTION : IRREVERSIBLE. Faites une sauvegarde de la base avant
-- d'executer ce script (aucun modele Credit n'utilise les soft deletes).
--
-- Supprime TOUS les dossiers de credit et toutes leurs donnees liees :
--   echeanciers, echeances, remboursements, deblocages, pieces
--   justificatives, validations, analyses, journal d'audit credit.
--
-- N'affecte PAS :
--   - tb_credit_commission_rules (configuration des commissions, pas des
--     donnees de dossier)
--   - Les clients, comptes, agents, zones, portefeuilles
--
-- Equivalent a la commande Artisan (avec confirmation et --dry-run) :
--   php artisan credit:purge-all
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE tb_credit_echeances;
TRUNCATE TABLE tb_credit_remboursements;
TRUNCATE TABLE tb_credit_deblocages;
TRUNCATE TABLE tb_credit_pieces;
TRUNCATE TABLE tb_credit_validations;
TRUNCATE TABLE tb_credit_analyses;
TRUNCATE TABLE tb_credit_audits;
TRUNCATE TABLE tb_credit_echeanciers;
TRUNCATE TABLE tb_credit_demandes;

SET FOREIGN_KEY_CHECKS = 1;
