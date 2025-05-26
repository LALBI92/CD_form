# 🚀 Workflow d'intervention et facturation automatique

## 📋 **Vue d'ensemble**

Ce système automatise la création et l'envoi de factures selon le type de client (particulier/entreprise) lorsque la date d'intervention est renseignée dans Pipedrive.

## 🔄 **Flux de traitement**

### 1. **Déclenchement**
- **Événement** : Date d'intervention renseignée dans un deal Pipedrive
- **Automation Pipedrive** : Le deal passe automatiquement à l'étape "Intervention prévue" (ID: 5)

### 2. **Détection du type de client**
Le système détermine automatiquement le type de client :
- **🏠 Particulier** : Si l'organisation n'a pas de SIRET
- **🏢 Entreprise** : Si l'organisation a un SIRET renseigné

### 3. **Logique conditionnelle**

#### **Pour les PARTICULIERS** :
- ✅ **Action immédiate** : Création automatique de la facture
- 💰 **Délai de paiement** : "Due on Receipt" (paiement à réception)
- 📧 **Envoi** : Facture envoyée automatiquement au client

#### **Pour les ENTREPRISES** :
- ⏳ **Attente** : Délai de paiement à renseigner manuellement
- 🔽 **Options disponibles** :
  - NET 7 (7 jours)
  - NET 15 (15 jours) 
  - NET 30 (30 jours)
  - NET 45 (45 jours)
  - NET 60 (60 jours)
  - NET 90 (90 jours)
- ✅ **Action** : Une fois le délai renseigné → création automatique de la facture
- 📧 **Envoi** : Facture envoyée avec le délai choisi

## ⚙️ **Configuration technique**

### **Champs Pipedrive utilisés :**
- **Jour d'intervention** (ID: 41) : `9aafcf168c42ff17ddce50af4f6ba276dd04c320`
- **Délai de paiement** (ID: 48) : `64be55b0a2e9f215d834ea35442c68cd926370ee`
- **invoiced_estimate_id** (ID: 44) : pour lier au devis Invoiced
- **invoiced_invoice_id** (ID: 45) : pour stocker l'ID de la facture créée

### **Webhook Pipedrive**
- **Endpoint** : `webhooks/pipedrive_hook.php`
- **Événements surveillés** : `deal.change`
- **Filtres** : Changements des champs date d'intervention et délai de paiement

### **Intégration Invoiced**
- **Conversion** : Devis → Facture via `$estimate->invoice($invoiceData)`
- **Délais supportés** : Tous les délais NET standard d'Invoiced
- **Envoi automatique** : Via `$invoice->send()`

## 📝 **Utilisation**

### **Workflow utilisateur :**

1. **Approuver le devis** dans Invoiced
   → Deal passe à "Devis Validé" dans Pipedrive

2. **Commercial appelle le client** pour fixer la date d'intervention

3. **Renseigner la date d'intervention** dans Pipedrive
   → Deal passe automatiquement à "Intervention prévue"

4. **Si particulier** :
   → Facture créée et envoyée automatiquement ✅

5. **Si entreprise** :
   → Renseigner le "Délai de paiement" requis
   → Facture créée et envoyée automatiquement ✅

## 🔍 **Logs et monitoring**

- **Fichier de log** : `logs/pipedrive-handler-*.log`
- **Événements loggés** :
  - Type de client détecté
  - Création de facture (succès/erreur)
  - Mise à jour des deals
  - Erreurs d'API

## 🚨 **Gestion d'erreurs**

Le système gère automatiquement :
- **Deals sans devis** : Log d'erreur, traitement ignoré
- **Organisations manquantes** : Log d'erreur
- **Erreurs API Invoiced** : Log détaillé avec stack trace
- **Timeouts réseau** : Retry automatique (future amélioration)

## 🧪 **Tests**

Pour tester le système :

1. **Créer un deal** avec un devis Invoiced lié
2. **Renseigner la date d'intervention**
3. **Vérifier les logs** pour confirmer le bon fonctionnement
4. **Pour entreprises** : Tester le délai de paiement

## 📈 **Améliorations futures**

- **Notifications Slack/Email** en cas d'erreur
- **Dashboard de monitoring** des factures créées
- **Retry automatique** pour les erreurs temporaires
- **Validation des délais** selon les conditions commerciales
- **Statistiques** sur les délais de paiement par client

---

✅ **Le workflow est opérationnel et prêt à être utilisé !** 