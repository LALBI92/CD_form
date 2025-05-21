# Procédure de Déploiement

## Workflow de Développement

### 1. Développement Local

# S'assurer d'être sur la branche local
git checkout local

# Faire les modifications...

# Ajouter les modifications
git add .

# Créer un commit
git commit -m "Description des modifications"

# Pousser vers GitHub
git push origin local


### 2. Déploiement en Développement

# Basculez sur la branche develop
git checkout develop

# Fusionnez local dans develop
git merge local

# Poussez develop vers GitHub
git push origin develop

### 3. Mise à jour du Serveur de Développement

# Sur le serveur de dev (via cPanel)
cd /home/votre_utilisateur/www/dev2.cityrecyclage.com

# S'assurer d'être sur develop
git checkout develop

# Récupérer les modifications
git pull origin develop


## Structure des Branches
- `local` : Environnement de développement local
- `develop` : Environnement de développement (dev2.cityrecyclage.com)
- `master` : Environnement de production

## Points Importants
- Toujours travailler sur la branche `local`
- Pousser d'abord sur `local`, puis sur `develop`
- Sur le serveur de dev, rester sur la branche `develop`
- Vérifier que les modifications sont bien présentes sur dev2.cityrecyclage.com après chaque pull
