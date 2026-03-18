#!/bin/bash
# Script de diagnostic et test - Erreur Mémoire PDF ClientList
# Usage: bash VERIFY_PDF_FIX.sh

echo "=========================================="
echo "VÉRIFICATION FIX PDF Liste Clients"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Vérifier la limite de mémoire dans php.ini
echo -e "${YELLOW}[1/5] Vérification limite mémoire PHP...${NC}"
MEMORY_LIMIT=$(php -r "echo ini_get('memory_limit');")
echo "Limite actuelle: $MEMORY_LIMIT"
if [[ $MEMORY_LIMIT == *"256"* ]] || [[ $MEMORY_LIMIT == *"512"* ]]; then
    echo -e "${GREEN}✓ Limite acceptable${NC}"
else
    echo -e "${RED}✗ Limite peut être insuffisante. Définie dans php.ini à 512M${NC}"
fi
echo ""

# 2. Vérifier les fichiers
echo -e "${YELLOW}[2/5] Vérification fichiers générés...${NC}"
FILES=(
    "app/Http/Controllers/Clients/ClientController.php"
    "resources/views/impressions/clients/liste.blade.php"
    "resources/views/impressions/clients/fiche_recolte_journaliere.blade.php"
)

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $file"
    else
        echo -e "${RED}✗${NC} $file MANQUANT"
    fi
done
echo ""

# 3. Vérifier syntaxe PHP
echo -e "${YELLOW}[3/5] Vérification syntaxe PHP...${NC}"
php -l app/Http/Controllers/Clients/ClientController.php
echo ""

# 4. Vérifier base de données
echo -e "${YELLOW}[4/5] Vérification permissions DB (EBEN-PER15)...${NC}"
echo "Exécutez cette requête SQL pour vérifier:"
echo "SELECT * FROM tb_permissions WHERE code = 'EBEN-PER15';"
echo ""

# 5. Recommander nettoyage cache
echo -e "${YELLOW}[5/5] Recommandations...${NC}"
echo -e "${YELLOW}Important: Exécutez ces commandes pour compléter le déploiement:${NC}"
echo ""
echo "  php artisan cache:clear"
echo "  php artisan view:clear"
echo "  php artisan config:clear"
echo "  php artisan route:clear"
echo "  php artisan optimize:clear"
echo ""
echo "Puis testez la route:"
echo "  curl 'http://localhost/comptes-clients/clients-liste-pdf'"
echo ""
echo -e "${GREEN}========== Vérification terminée ==========${NC}"
