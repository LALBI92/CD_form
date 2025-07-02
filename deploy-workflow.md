# 🚀 Workflow de Déploiement - City Recyclage

## 📍 **URLs des Environnements**
- **Local** : localhost (MAMP)
- **Dev** : dev2.cityrecyclage.com (racine)
- **Prod** : devis.cityrecyclage.com

## 🔄 **Workflow Git**

### **Structure des Branches**
```
master/main     ← PRODUCTION (devis.cityrecyclage.com)
    ↑
develop         ← DÉVELOPPEMENT (dev2.cityrecyclage.com)
    ↑
feature/*       ← Nouvelles fonctionnalités (local)
```

### **Processus de Déploiement**

#### **A. Local → Dev**
1. **Sur votre machine locale :**
   ```bash
   git add .
   git commit -m "Description des modifications"
   git push origin develop
   ```

2. **Dans cPanel dev2.cityrecyclage.com :**
   ```bash
   cd /public_html
   git pull origin develop
   ```

#### **B. Dev → Production**
1. **Tests validés sur dev, depuis votre machine locale :**
   ```bash
   git checkout master
   git merge develop
   git push origin master
   ```

2. **Dans cPanel devis.cityrecyclage.com :**
   ```bash
   cd /public_html
   git pull origin master
   ```

## ⚙️ **Configuration Initiale**

### **1. Sur dev2.cityrecyclage.com (cPanel Terminal)**
```bash
# Aller à la racine du site
cd /public_html

# Si CD_form existe déjà, déplacer les fichiers
mv CD_form/* ./
mv CD_form/.* ./ 2>/dev/null || true
rmdir CD_form

# OU cloner depuis GitHub si pas encore fait
git clone https://github.com/LALBI92/CD_form.git .
git checkout develop
```

### **2. Sur devis.cityrecyclage.com (cPanel Terminal)**
```bash
# Aller à la racine du site
cd /public_html

# Cloner depuis GitHub
git clone https://github.com/LALBI92/CD_form.git .
git checkout master
```

## 🔧 **Commandes Utiles**

### **Synchronisation Rapide Dev**
```bash
git pull origin develop
```

### **Synchronisation Rapide Prod**
```bash
git pull origin master
```

### **Rollback d'Urgence Prod**
```bash
git reset --hard HEAD~1
```

## 📝 **Checklist de Déploiement**

### **Pour chaque modification :**
- [ ] ✅ Développement et test en local
- [ ] ✅ Push sur branche develop
- [ ] ✅ Pull et test sur dev2.cityrecyclage.com
- [ ] ✅ Validation fonctionnelle sur dev
- [ ] ✅ Merge develop → master
- [ ] ✅ Push master
- [ ] ✅ Pull sur devis.cityrecyclage.com
- [ ] ✅ Test final en production

## ⚠️ **Sécurité et Bonnes Pratiques**

- 🔒 **Jamais de push direct sur master en production**
- 🧪 **Toujours tester sur dev avant prod**
- 💾 **Backup avant déploiement important**
- 📝 **Messages de commit clairs**
- 🏷️ **Tags pour versions importantes**

## 🆘 **En Cas de Problème**

### **Conflit de merge :**
```bash
git status
git diff
# Résoudre manuellement puis :
git add .
git commit -m "Résolution conflit"
```

### **Restaurer version précédente :**
```bash
git log --oneline
git reset --hard [COMMIT_ID]
``` 