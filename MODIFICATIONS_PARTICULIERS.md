# Modifications pour les Particuliers avec Organisations Virtuelles

## Problème identifié

Vous aviez raison sur deux points importants concernant l'intégration Invoiced-Pipedrive :

1. **Les particuliers n'avaient pas de champ adresse** : Dans l'ancienne logique, seules les entreprises (type "company") avaient leur adresse synchronisée dans Pipedrive.

2. **Les deals étaient rattachés aux organisations** : Les particuliers n'avaient qu'une entrée "Person" dans Pipedrive, mais pas d'organisation, ce qui posait problème pour rattacher les deals.

## Solution implémentée

### Création d'organisations virtuelles pour tous

Maintenant, **tous les clients Invoiced** (entreprises ET particuliers) ont une organisation créée dans Pipedrive :

- **Entreprises** : L'organisation porte le nom de l'entreprise
- **Particuliers** : L'organisation virtuelle porte le nom du particulier

### Gestion des adresses

- **Tous les types de clients** peuvent maintenant avoir leur adresse stockée dans l'organisation Pipedrive
- L'adresse est récupérée depuis le champ `address1` d'Invoiced

### Structure dans Pipedrive

Pour chaque client (entreprise ou particulier) :
1. **Organisation créée** avec :
   - Nom du client
   - Adresse (si disponible)
   - Champ personnalisé `invoiced_id`
   - SIRET (uniquement pour les entreprises)

2. **Contact créé** et lié à l'organisation avec :
   - Nom du contact
   - Email et téléphone
   - Champ personnalisé `invoiced_id`
   - Référence à l'organisation (`org_id`)

3. **Deal rattaché** à l'organisation lors de la création des devis

## Fichiers modifiés

### `src/Handlers/InvoicedHandler.php`

#### Section `customer.created`
- ✅ Suppression de la condition `if ($c['type'] === 'company')`
- ✅ Création d'organisation pour tous les types de clients
- ✅ Gestion de l'adresse pour tous les types
- ✅ Ajout du type dans les logs pour un meilleur suivi

#### Section `customer.updated`
- ✅ Suppression de la condition restrictive sur le type "company"
- ✅ Mise à jour possible pour tous les clients ayant une organisation
- ✅ Gestion de l'adresse dans les mises à jour
- ✅ Ajout de logs pour le suivi

#### Méthode ajoutée : `removeAllProductsFromDeal`
- ✅ Nouvelle méthode pour supprimer tous les produits d'un deal
- ✅ Utilisée lors des mises à jour de devis
- ✅ Gestion d'erreurs avec logs

## Avantages de cette approche

1. **Uniformité** : Tous les clients ont la même structure dans Pipedrive
2. **Adresses préservées** : Les particuliers conservent leur adresse
3. **Deals fonctionnels** : Tous les deals peuvent être rattachés à une organisation
4. **Compatibilité** : Les entreprises existantes continuent de fonctionner
5. **Évolutivité** : Structure cohérente pour les futurs développements

## Tests

La logique a été testée avec un script de simulation qui confirme :
- ✅ Création d'organisation virtuelle pour un particulier
- ✅ Stockage de l'adresse dans l'organisation
- ✅ Création du contact lié à l'organisation
- ✅ Stockage des métadonnées pour la synchronisation

## Prochaines étapes recommandées

1. **Test en production** : Créer un nouveau particulier via Invoiced pour vérifier le fonctionnement
2. **Migration optionnelle** : Décider si vous souhaitez migrer les particuliers existants vers cette nouvelle structure
3. **Documentation** : Mettre à jour la documentation utilisateur si nécessaire 