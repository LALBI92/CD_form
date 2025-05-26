# Corrections des Produits dans les Deals

## Problèmes identifiés

### 1. **Mapping incomplet des produits**
- Le fichier `ProductIdMapping.php` ne contenait que 2 mappings (field328 et field333)
- Le produit `field297` (vu dans les logs) n'était pas mappé
- Cela causait l'erreur : "Produit non trouvé dans le mapping"

### 2. **Erreur API Invoiced**
- L'API Invoiced générait une erreur : `Call to undefined method Invoiced\Estimate::update()`
- Cette erreur empêchait la sauvegarde des métadonnées du deal dans le devis

## Solutions implémentées

### ✅ **Mapping complet des produits**
- **Mis à jour** `src/Handlers/ProductIdMapping.php` avec **562 mappings complets**
- Tous les produits `fieldXXX` d'Invoiced sont maintenant mappés avec leurs IDs Pipedrive
- Format : `'field297' => 185` (code Invoiced => ID Pipedrive)

### ✅ **Correction de l'API Invoiced**
- **Corrigé** la méthode de mise à jour des métadonnées dans `InvoicedHandler.php`
- Remplacé `$estimate->update()` par `$estimate->save()`
- Maintenant les métadonnées `pipedrive_deal_id` sont correctement sauvegardées

## Résultat attendu

Maintenant, lors de la création d'un deal à partir d'un devis :

1. ✅ **Le deal est créé** avec l'organisation (même pour les particuliers)
2. ✅ **Les produits sont ajoutés** au deal grâce au mapping complet
3. ✅ **Les métadonnées sont sauvegardées** dans le devis Invoiced
4. ✅ **Synchronisation bidirectionnelle** fonctionnelle

## Test recommandé

Pour tester, créez un nouveau devis dans Invoiced avec :
- Un client particulier ou entreprise
- Plusieurs produits (y compris field297)
- Vérifiez que le deal dans Pipedrive contient tous les produits

## Logs à surveiller

Dans `logs/webhooks.log`, vous devriez maintenant voir :
- "Deal créé avec succès"
- "Produit ajouté au deal" (pour chaque produit)
- Plus d'erreur "Produit non trouvé dans le mapping"
- Plus d'erreur "Estimate::update()" 