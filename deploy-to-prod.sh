#!/bin/bash

# 🚀 Script de déploiement vers PRODUCTION
# Usage: ./deploy-to-prod.sh

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${RED}🚨 DÉPLOIEMENT EN PRODUCTION 🚨${NC}"
echo -e "${YELLOW}⚠️  Assurez-vous que les tests sur dev2.cityrecyclage.com sont OK !${NC}"
echo

# Confirmation
read -p "Êtes-vous sûr de vouloir déployer en production ? (oui/non): " confirm
if [ "$confirm" != "oui" ]; then
    echo -e "${YELLOW}❌ Déploiement annulé${NC}"
    exit 0
fi

# Vérifier si on est sur develop
CURRENT_BRANCH=$(git branch --show-current)
if [ "$CURRENT_BRANCH" != "develop" ]; then
    echo -e "${RED}❌ Vous devez être sur la branche develop${NC}"
    echo -e "${YELLOW}Changement vers develop...${NC}"
    git checkout develop
fi

# S'assurer que develop est à jour
echo -e "${YELLOW}🔄 Mise à jour de la branche develop...${NC}"
git pull origin develop

# Passer sur master
echo -e "${YELLOW}🔄 Passage sur la branche master...${NC}"
git checkout master

# Mise à jour de master
echo -e "${YELLOW}🔄 Mise à jour de la branche master...${NC}"
git pull origin master

# Merge develop dans master
echo -e "${YELLOW}🔀 Merge develop → master...${NC}"
git merge develop

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Conflit lors du merge !${NC}"
    echo -e "${YELLOW}Résolvez les conflits manuellement puis recommencez${NC}"
    exit 1
fi

# Push master
echo -e "${YELLOW}📤 Push vers GitHub (branche master)...${NC}"
git push origin master

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Push réussi !${NC}"
    echo
    echo -e "${BLUE}🎯 PROCHAINES ÉTAPES :${NC}"
    echo -e "${YELLOW}1. Connectez-vous au terminal cPanel de devis.cityrecyclage.com${NC}"
    echo -e "${YELLOW}2. Exécutez: cd /public_html && git pull origin master${NC}"
    echo -e "${YELLOW}3. Testez le site en production${NC}"
    echo
    echo -e "${GREEN}🚀 Déploiement préparé avec succès !${NC}"
    
    # Retour sur develop pour continuer le développement
    git checkout develop
else
    echo -e "${RED}❌ Erreur lors du push${NC}"
    exit 1
fi 