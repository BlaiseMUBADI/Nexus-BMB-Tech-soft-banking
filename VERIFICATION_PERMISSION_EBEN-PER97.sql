-- ══════════════════════════════════════════════════════════════════════════════
-- VÉRIFICATION — Où trouver la permission EBEN-PER97
-- ══════════════════════════════════════════════════════════════════════════════

-- 1️⃣ ONGLET "PERMISSION" — Laissez-vous que la permission existe
SELECT 
    code,
    nom,
    description,
    created_at,
    updated_at
FROM tb_permissions
WHERE code = 'EBEN-PER97';

-- ═════════════════════════════════════════════════════════════════════════════

-- 2️⃣ ONGLET "ROLE ET PERMISSION" — Où elle est assignée aux rôles
SELECT 
    rp.role_code,
    rp.permission_code,
    p.nom as permission_name,
    rp.created_at
FROM tb_role_permission rp
JOIN tb_permissions p ON p.code = rp.permission_code
WHERE rp.permission_code = 'EBEN-PER97'
ORDER BY rp.role_code;

-- ═════════════════════════════════════════════════════════════════════════════

-- 3️⃣ RÉSULTAT ATTENDU (après déploiement)
-- Requête 1 devrait retourner :
-- Code: EBEN-PER97
-- Nom: Supprimer operation caisse
-- Description: Permission de suppression/annulation dans le module Caisse
--
-- Requête 2 devrait retourner 4 lignes :
-- SUPERVISEUR_CAISSE | EBEN-PER97 | Supprimer operation caisse
-- TRESORIER          | EBEN-PER97 | Supprimer operation caisse
-- DIRECTEUR_AGENCE   | EBEN-PER97 | Supprimer operation caisse
-- ADMIN              | EBEN-PER97 | Supprimer operation caisse

-- ══════════════════════════════════════════════════════════════════════════════
