#!/bin/bash

# 🚀 Script de déploiement vers DEV
# Usage: ./deploy-to-dev.sh "Message de commit"

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}🚀 Déploiement vers DEV en cours...${NC}"

# Vérifier si on est sur la branche develop
CURRENT_BRANCH=$(git branch --show-current)
if [ "$CURRENT_BRANCH" != "develop" ]; then
    echo -e "${RED}❌ Vous devez être sur la branche develop${NC}"
    echo -e "${YELLOW}Changement vers develop...${NC}"
    git checkout develop
fi

# Vérifier s'il y a des modifications
if ! git diff-index --quiet HEAD --; then
    # Il y a des modifications
    if [ -z "$1" ]; then
        echo -e "${RED}❌ Message de commit requis${NC}"
        echo "Usage: ./deploy-to-dev.sh \"Votre message de commit\""
        exit 1
    fi
    
    echo -e "${YELLOW}📦 Ajout des modifications...${NC}"
    git add .
    
    echo -e "${YELLOW}💾 Commit des modifications...${NC}"
    git commit -m "$1"
else
    echo -e "${GREEN}✅ Aucune modification à commiter${NC}"
fi

# Push vers GitHub
echo -e "${YELLOW}📤 Push vers GitHub (branche develop)...${NC}"
git push origin develop

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Push réussi !${NC}"
    echo -e "${YELLOW}🔄 Maintenant, connectez-vous au terminal cPanel de dev2.cityrecyclage.com${NC}"
    echo -e "${YELLOW}   et exécutez: cd /public_html && git pull origin develop${NC}"
else
    echo -e "${RED}❌ Erreur lors du push${NC}"
    exit 1
fi 