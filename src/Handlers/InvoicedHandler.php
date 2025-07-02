<?php
namespace Src\Handlers;

use Invoiced\Client                as InvClient;
use Src\Logger\WebhookLogger;

class InvoicedHandler
{
    private InvClient          $inv;
    private string            $pipedriveToken;
    private WebhookLogger      $logger;
    private string            $pipedriveBaseUrl = 'https://api.pipedrive.com/v1';
    private ?string           $invoicedFieldKey = null;
    private ?string           $invoicedPersonFieldKey = null;
    private ?string           $categoriesFieldKey = null;
    private ?string           $devisInvoicedFieldKey = null;
    private $siretFieldKey; // Clé du champ SIRET/SIREN

    // Clé du champ 'Date du solde'
    private string $dateSoldeFieldKey = '58319154cbe240244c3907dc4476d04c653b75ac';

    public function __construct()
    {
        $this->logger = new WebhookLogger('invoiced-handler');
        
        // 1) SDK Invoiced
        $this->inv = new InvClient($_ENV['INVOICED_API_KEY']);

        // 2) Token Pipedrive
        $this->pipedriveToken = $_ENV['PIPEDRIVE_API_TOKEN'];

        // 3) Initialisation des champs personnalisés
        $this->initCustomFields();
    }

    /**
     * Initialise les champs personnalisés
     */
    private function initCustomFields(): void
    {
        try {
            // Récupération des champs personnalisés organisations
            $orgFields = $this->callPipedriveApi('GET', '/organizationFields');
            
            foreach ($orgFields->data as $field) {
                if ($field->name === 'Invoiced ID' || $field->name === 'invoiced_id') {
                    $this->invoicedFieldKey = $field->key;
                    $this->logger->log('Champ organisation trouvé', ['key' => $field->key, 'name' => $field->name]);
                }
                // Détection du champ SIRET/SIREN
                if (strtolower($field->name) === 'siren' || 
                    strtolower($field->name) === 'siret' ||
                    stripos($field->name, 'siret') !== false || 
                    stripos($field->name, 'siren') !== false) {
                    $this->siretFieldKey = $field->key;
                    $this->logger->log('Champ SIRET/SIREN trouvé', ['key' => $field->key, 'name' => $field->name]);
                }
            }

            $fields = $this->callPipedriveApi('GET', '/personFields');
            foreach ($fields->data as $field) {
                if ($field->name === 'invoiced_id') {
                    $this->invoicedPersonFieldKey = $field->key;
                    $this->logger->log('Champ personne trouvé', ['key' => $this->invoicedPersonFieldKey]);
                }
            }

            // Vérifier si le champ catégories existe pour les deals
            $dealFields = $this->callPipedriveApi('GET', '/dealFields');
            foreach ($dealFields->data as $field) {
                if ($field->name === 'Catégorie') {
                    $this->categoriesFieldKey = $field->key;
                    $this->logger->log('Champ catégories trouvé', ['key' => $this->categoriesFieldKey]);
                }
                if ($field->name === 'Devis Invoiced') {
                    $this->devisInvoicedFieldKey = $field->key;
                    $this->logger->log('Champ Devis Invoiced trouvé', ['key' => $this->devisInvoicedFieldKey]);
                }
            }

            // 2. Créer les champs s'ils n'existent pas
            if (!$this->invoicedFieldKey) {
                $field = $this->callPipedriveApi('POST', '/organizationFields', [
                    'name' => 'invoiced_id',
                    'field_type' => 'varchar',
                    'field_for' => 'organization'
                ]);
                $this->invoicedFieldKey = $field->data->key;
                $this->logger->log('Champ organisation créé', ['key' => $this->invoicedFieldKey]);
            }

            if (!$this->invoicedPersonFieldKey) {
                $field = $this->callPipedriveApi('POST', '/personFields', [
                    'name' => 'invoiced_id',
                    'field_type' => 'varchar',
                    'field_for' => 'person'
                ]);
                $this->invoicedPersonFieldKey = $field->data->key;
                $this->logger->log('Champ personne créé', ['key' => $this->invoicedPersonFieldKey]);
            }

            if (!$this->categoriesFieldKey) {
                $field = $this->callPipedriveApi('POST', '/dealFields', [
                    'name' => 'Catégorie',
                    'field_type' => 'varchar',
                    'field_for' => 'deal'
                ]);
                $this->categoriesFieldKey = $field->data->key;
                $this->logger->log('Champ catégories créé', ['key' => $this->categoriesFieldKey]);
            }
        } catch (\Exception $e) {
            $this->logger->logError('Erreur lors de l\'initialisation des champs personnalisés', $e);
        }
    }

    /**
     * Appel API Pipedrive
     */
    private function callPipedriveApi(string $method, string $endpoint, array $data = []): object
    {
        // DEBUG: Vérification des types avant construction de l'URL
        $this->logger->log('=== DEBUG CALL PIPEDRIVE API ===', [
            'method' => $method,
            'endpoint' => $endpoint,
            'pipedriveBaseUrl_type' => gettype($this->pipedriveBaseUrl),
            'pipedriveBaseUrl_value' => (string)$this->pipedriveBaseUrl,
            'pipedriveToken_type' => gettype($this->pipedriveToken),
            'pipedriveToken_length' => is_string($this->pipedriveToken) ? strlen($this->pipedriveToken) : 'not_string'
        ]);
        
        try {
            $url = $this->pipedriveBaseUrl . $endpoint . '?api_token=' . (string)$this->pipedriveToken;
            $this->logger->log('URL construite avec succès', ['url_length' => strlen($url)]);
        } catch (\Exception $e) {
            $this->logger->logError('Erreur lors de la construction de l\'URL', $e);
            throw $e;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15); // Timeout réduit à 15 secondes
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // Timeout de connexion réduit à 5 secondes
        
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new \Exception("Erreur CURL: $curlError");
        }

        if ($httpCode >= 400) {
            $errorMessage = "Erreur API Pipedrive: $httpCode";
            try {
                $errorData = json_decode($response, true);
                if (isset($errorData['error'])) {
                    $errorMessage .= " - " . $errorData['error'];
                }
            } catch (\Exception $e) {
                $errorMessage .= " - Réponse: $response";
            }
            throw new \Exception($errorMessage);
        }

        $decodedResponse = json_decode($response);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Erreur de décodage JSON: " . json_last_error_msg() . " - Réponse: $response");
        }

        return $decodedResponse;
    }

    /**
     * @param array $event  Payload Webhook Invoiced
     */
    public function handle(array $event): void
    {
        $this->logger->log('Traitement événement Invoiced', ['type' => $event['type'] ?? 'unknown']);

        switch ($event['type'] ?? '') {

            /*────────────────────────────────────────────────────
             * 1. Création client (customer.created)
             ────────────────────────────────────────────────────*/
            case 'customer.created':
                $c = $event['data']['object'];
                $this->logger->log('Création client', [
                    'customer_id' => $c['id'],
                    'type' => $c['type']
                ]);

                // Pour tous les types de clients (company ou particulier), on crée une organisation
                $orgData = [
                    'name' => $c['name'],
                ];

                // Gestion de l'adresse pour tous les types
                if (!empty($c['address1'])) {
                    $orgData['address'] = $c['address1'];
                }

                // Ajout du champ personnalisé
                $orgData[$this->invoicedFieldKey] = (string)$c['id'];

                // Pour les entreprises, on ajoute le SIRET si disponible
                if ($c['type'] === 'company' && !empty($c['metadata']['siret']) && $this->siretFieldKey) {
                    $orgData[$this->siretFieldKey] = $c['metadata']['siret'];
                }

                $org = $this->callPipedriveApi('POST', '/organizations', $orgData);

                // Création du contact lié à l'organisation
                $personData = [
                    'name' => $c['attention_to'] ?? $c['name'], // Utilise le nom du contact si disponible
                    'org_id' => $org->data->id,
                ];

                if (!empty($c['email'])) {
                    $personData['email'] = [$c['email']];
                }
                if (!empty($c['phone'])) {
                    $personData['phone'] = [$c['phone']];
                }

                // Ajout du champ personnalisé pour le contact
                $personData[$this->invoicedPersonFieldKey] = (string)$c['id'];

                $person = $this->callPipedriveApi('POST', '/persons', $personData);

                $this->logger->log('Organisation et contact créés dans Pipedrive', [
                    'invoiced_id' => $c['id'],
                    'type' => $c['type'],
                    'org_id' => $org->data->id,
                    'person_id' => $person->data->id
                ]);

                // Stocke les IDs Pipedrive côté Invoiced
                $customer = $this->inv->Customer->retrieve($c['id']);
                $customer->metadata = array_merge($customer->metadata ?? [], [
                    'pipedrive_org_id' => $org->data->id,
                    'pipedrive_person_id' => $person->data->id
                ]);
                $customer->save();
                break;

            case 'customer.updated':
                $c = $event['data']['object'];
                $previous = $event['data']['previous'] ?? [];
                
                // Filtrer les mises à jour causées par nos propres modifications de métadonnées
                if (isset($previous['metadata']) && 
                    isset($c['metadata']['pipedrive_org_id']) && 
                    !isset($previous['metadata']['pipedrive_org_id'])) {
                    $this->logger->log('Mise à jour client ignorée - causée par synchronisation métadonnées', [
                        'customer_id' => $c['id'],
                        'type' => $c['type']
                    ]);
                    break;
                }
                
                $this->logger->log('Mise à jour client', [
                    'customer_id' => $c['id'],
                    'type' => $c['type']
                ]);

                // Récupération des IDs depuis les métadonnées
                $orgId = $c['metadata']['pipedrive_org_id'] ?? null;
                $personId = $c['metadata']['pipedrive_person_id'] ?? null;

                if ($orgId) {
                    // Mise à jour de l'organisation (pour tous les types)
                    $orgData = [
                        'name' => $c['name'],
                    ];

                    if (!empty($c['address1'])) {
                        $orgData['address'] = $c['address1'];
                    }

                    // Pour les entreprises, mise à jour du SIRET si disponible
                    if ($c['type'] === 'company' && !empty($c['metadata']['siret']) && $this->siretFieldKey) {
                        $orgData[$this->siretFieldKey] = $c['metadata']['siret'];
                    }

                    $this->callPipedriveApi('PUT', '/organizations/' . $orgId, $orgData);

                    // Mise à jour du contact lié
                    if ($personId) {
                        $personData = [
                            'name' => $c['attention_to'] ?? $c['name'],
                        ];

                        if (!empty($c['email'])) {
                            $personData['email'] = [$c['email']];
                        }
                        if (!empty($c['phone'])) {
                            $personData['phone'] = [$c['phone']];
                        }

                        $this->callPipedriveApi('PUT', '/persons/' . $personId, $personData);
                    }

                    $this->logger->log('Organisation et contact mis à jour dans Pipedrive', [
                        'invoiced_id' => $c['id'],
                        'type' => $c['type'],
                        'org_id' => $orgId,
                        'person_id' => $personId
                    ]);
                } else {
                    $this->logger->log('IDs Pipedrive non trouvés dans les métadonnées', [
                        'customer_id' => $c['id']
                    ]);
                }
                break;

            /*────────────────────────────────────────────────────
             * 2. Devis envoyé (estimate.sent)
             ────────────────────────────────────────────────────*/
            case 'estimate.created':
            case 'estimate.updated':
                $est = $event['data']['object'];
                $previous = $event['data']['previous'] ?? [];
                
                // Filtrer les mises à jour causées par nos propres modifications de métadonnées
                if ($event['type'] === 'estimate.updated' && 
                    isset($previous['metadata']) && 
                    isset($est['metadata']['pipedrive_deal_id']) && 
                    !isset($previous['metadata']['pipedrive_deal_id'])) {
                    $this->logger->log('Mise à jour devis ignorée - causée par synchronisation métadonnées', [
                        'estimate_id' => $est['id'],
                        'status' => $est['status'] ?? 'unknown'
                    ]);
                    break;
                }
                
                if (($est['status'] ?? '') === 'draft') {
                    $this->logger->log('Devis ignoré - statut brouillon', ['status' => $est['status'] ?? 'unknown']);
                    break;
                }

                // PROTECTION RACE CONDITION : Délai pour estimate.created
                if ($event['type'] === 'estimate.created') {
                    $this->logger->log('estimate.created détecté - application du délai de sécurité', [
                        'estimate_id' => $est['id'],
                        'type' => $event['type']
                    ]);
                    sleep(2); // Laisser le temps au customer.created de finir
                }

                $this->logger->log('Traitement devis', [
                    'estimate_id' => $est['id'],
                    'status' => $est['status'] ?? 'unknown'
                ]);

                // Log des produits du devis
                $this->logger->log('Produits du devis', [
                    'items' => array_map(function($item) {
                        return [
                            'name' => $item['name'],
                            'code' => $item['catalog_item'],
                            'quantity' => $item['quantity'],
                            'unit_cost' => $item['unit_cost']
                        ];
                    }, $est['items'])
                ]);

                // Récupération des IDs Pipedrive depuis les métadonnées du client
                $customerId = $est['customer']['id'] ?? null;
                if (!$customerId) {
                    $this->logger->log('ID client non trouvé dans le devis', ['estimate_id' => (string)($est['id'] ?? 'unknown')]);
                    break;
                }

                // Utiliser d'abord les données du client incluses dans le devis
                $customerData = $est['customer'] ?? [];
                $orgId = $customerData['metadata']['pipedrive_org_id'] ?? null;
                $personId = $customerData['metadata']['pipedrive_person_id'] ?? null;
                
                // Si pas trouvé dans les données du devis, récupérer depuis Invoiced
                if (!$orgId || !$personId) {
                    $customer = $this->inv->Customer->retrieve($customerId);
                    $orgId = $orgId ?: ($customer->metadata['pipedrive_org_id'] ?? null);
                    $personId = $personId ?: ($customer->metadata['pipedrive_person_id'] ?? null);
                }

                if (!$orgId && !$personId) {
                    $this->logger->log('IDs Pipedrive non trouvés dans les métadonnées, recherche par invoiced_id', ['customer_id' => $customerId]);
                    
                    // Recherche de l'organisation par invoiced_id
                    $search = $this->callPipedriveApi('GET', '/organizations', [
                        'term' => (string)$customerId,
                        'fields' => $this->invoicedFieldKey
                    ]);

                    if (isset($search->data) && !empty($search->data)) {
                        foreach ($search->data as $org) {
                            if (isset($org->{$this->invoicedFieldKey}) && $org->{$this->invoicedFieldKey} === (string)$customerId) {
                                $orgId = $org->id;
                                // Recherche du contact associé
                                $persons = $this->callPipedriveApi('GET', '/persons', [
                                    'org_id' => $orgId
                                ]);
                                if (!empty($persons->data)) {
                                    $personId = $persons->data[0]->id;
                                }
                                break;
                            }
                        }
                    }

                    if ($orgId) {
                        $this->logger->log('Organisation trouvée dans Pipedrive', [
                            'org_id' => $orgId,
                            'person_id' => $personId
                        ]);

                        // Stockage des IDs dans les métadonnées du client si pas déjà présents
                        $customer = $customer ?? $this->inv->Customer->retrieve($customerId);
                        $customer->metadata = array_merge($customer->metadata ?? [], [
                            'pipedrive_org_id' => $orgId,
                            'pipedrive_person_id' => $personId
                        ]);
                        $customer->save();
                    } else {
                        $this->logger->log('Organisation non trouvée, création nécessaire', ['customer_id' => $customerId]);
                        // Code existant pour la création...
                    }
                } else {
                    $this->logger->log('IDs Pipedrive trouvés', [
                        'org_id' => $orgId,
                        'person_id' => $personId,
                        'source' => $customerData ? 'devis' : 'metadonnees'
                    ]);
                }

                // PROTECTION ANTI-DUPLICATION : Délai pour estimate.updated sans deal_id
                if ($event['type'] === 'estimate.updated' && empty($est['metadata']['pipedrive_deal_id'])) {
                    $this->logger->log('estimate.updated sans deal_id détecté - application du délai de 8 secondes', [
                        'estimate_id' => $est['id'],
                        'type' => $event['type']
                    ]);
                    sleep(8);
                    
                    // Re-vérifier si un deal a été créé entre temps par un autre webhook
                    try {
                        $updatedEstimate = $this->inv->Estimate->retrieve($est['id']);
                        if (!empty($updatedEstimate->metadata['pipedrive_deal_id'])) {
                            $this->logger->log('Deal créé par webhook concurrent - abandon du traitement', [
                                'estimate_id' => $est['id'],
                                'existing_deal_id' => $updatedEstimate->metadata['pipedrive_deal_id']
                            ]);
                            break; // Abandonner le traitement
                        }
                    } catch (\Exception $e) {
                        $this->logger->log('Erreur lors de la re-vérification du devis', [
                            'estimate_id' => $est['id'],
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // Recherche du deal existant
                $dealId = null;
                
                // 1. D'abord, vérifier si l'ID du deal est stocké dans les métadonnées du devis
                if (!empty($est['metadata']['pipedrive_deal_id'])) {
                    $dealId = $est['metadata']['pipedrive_deal_id'];
                    $this->logger->log('Deal ID trouvé dans les métadonnées du devis', ['deal_id' => $dealId]);
                    
                    // Vérifier que le deal existe encore dans Pipedrive
                    try {
                        $dealCheck = $this->callPipedriveApi('GET', '/deals/' . $dealId);
                        if (!isset($dealCheck->data)) {
                            $this->logger->log('Deal inexistant dans Pipedrive, réinitialisation', ['deal_id' => $dealId]);
                            $dealId = null;
                        }
                    } catch (\Exception $e) {
                        $this->logger->log('Erreur lors de la vérification du deal, réinitialisation', ['deal_id' => $dealId]);
                        $dealId = null;
                    }
                }
                
                // 2. Si pas trouvé dans les métadonnées, chercher par l'ID du devis dans les champs custom
                if (!$dealId) {
                    $estimateId = $est['id'] ?? null;
                    if ($estimateId) {
                        $search = $this->callPipedriveApi('GET', '/deals', [
                            'limit' => 100,
                            'status' => 'all_not_deleted'
                        ]);
                        
                        if (isset($search->data) && !empty($search->data)) {
                            foreach ($search->data as $deal) {
                                // Vérifier le champ custom qui contient l'ID du devis
                                $customField = 'b8b55bcfd1cc07f3e577fb7a8d4fe498b435813a';
                                if (isset($deal->{$customField}) && $deal->{$customField} === (string)$estimateId) {
                                    $dealId = $deal->id;
                                    $this->logger->log('Deal trouvé par estimate_id dans champ custom', [
                                        'deal_id' => $dealId,
                                        'estimate_id' => $estimateId
                                    ]);
                                    
                                    // Mettre à jour les métadonnées du devis avec l'ID du deal trouvé
                                    $estimate = $this->inv->Estimate->retrieve($est['id']);
                                    $estimate->metadata = array_merge($estimate->metadata ?? [], [
                                        'pipedrive_deal_id' => $dealId
                                    ]);
                                    $estimate->save();
                                    $this->logger->log('Métadonnées du devis mises à jour avec le deal ID', ['deal_id' => $dealId]);
                                    break;
                                }
                            }
                        }
                    }
                }

                if (!$dealId) {
                    // Création du deal si non trouvé
                    $deal = $this->createDealFromEstimate($est, $orgId, $personId);
                    $dealId = $deal->data->id ?? null;
                    $this->logger->log('Deal créé dans Pipedrive', ['deal_id' => $dealId, 'estimate_id' => $est['id'], 'org_id' => $orgId, 'person_id' => $personId]);
                    
                    // Stocker l'ID du deal dans les métadonnées du devis
                    $estimate = $this->inv->Estimate->retrieve($est['id']);
                    $estimate->metadata = array_merge($estimate->metadata ?? [], [
                        'pipedrive_deal_id' => $dealId
                    ]);
                    $estimate->save();
                } else {
                                        $this->logger->log('Deal existant trouvé, mise à jour au lieu de création', [
                        'deal_id' => $dealId,
                        'estimate_id' => $est['id']
                    ]);

                    // Vérifier si le deal doit être mis à jour avec la nouvelle organisation
                    $dealInfo = $this->callPipedriveApi('GET', '/deals/' . $dealId);
                    if ($dealInfo->data->org_id != $orgId) {
                        $this->logger->log('Mise à jour de l\'organisation du deal', [
                            'deal_id' => $dealId,
                            'old_org_id' => $dealInfo->data->org_id,
                            'new_org_id' => $orgId,
                            'new_person_id' => $personId
                        ]);
                        
                        $this->callPipedriveApi('PUT', '/deals/' . $dealId, [
                            'org_id' => $orgId,
                            'person_id' => $personId
                        ]);
                    }
                    
                    // Vérifier si les produits ont changé avant de les supprimer/re-ajouter
                    $currentProducts = $this->callPipedriveApi('GET', '/deals/' . $dealId . '/products');
                    $currentProductCodes = [];
                    if (isset($currentProducts->data)) {
                        foreach ($currentProducts->data as $product) {
                            // Récupérer le code du produit depuis Pipedrive
                            $productDetail = $this->callPipedriveApi('GET', '/products/' . $product->product_id);
                            if (isset($productDetail->data->code)) {
                                $currentProductCodes[] = [
                                    'code' => $productDetail->data->code,
                                    'quantity' => $product->quantity,
                                    'price' => $product->item_price
                                ];
                            }
                        }
                    }
                    
                    // Produits du devis
                    $newProductCodes = array_map(function($item) {
                        return [
                            'code' => $item['catalog_item'],
                            'quantity' => $item['quantity'],
                            'price' => $item['unit_cost']
                        ];
                    }, $est['items']);
                    
                    // Comparer les produits
                    $productsChanged = (serialize($currentProductCodes) !== serialize($newProductCodes));
                    
                    if ($productsChanged) {
                        $this->logger->log('Produits du deal ont changé, mise à jour nécessaire', [
                            'deal_id' => $dealId,
                            'current_products' => $currentProductCodes,
                            'new_products' => $newProductCodes
                        ]);
                        
                        // Supprimer tous les produits existants
                        $this->removeAllProductsFromDeal($dealId);
                        $this->logger->log('Produits existants supprimés du deal', ['deal_id' => $dealId]);
                    } else {
                        $this->logger->log('Produits du deal inchangés, pas de modification nécessaire', [
                            'deal_id' => $dealId
                        ]);
                    }
                }

                // Ajout des produits au deal (seulement si nouveau deal ou produits changés)
                $shouldAddProducts = false;
                
                if (!isset($productsChanged)) {
                    // Nouveau deal créé
                    $shouldAddProducts = true;
                    $this->logger->log('Nouveau deal - ajout des produits nécessaire', ['deal_id' => $dealId]);
                } else if ($productsChanged) {
                    // Deal existant avec produits modifiés
                    $shouldAddProducts = true;
                    $this->logger->log('Produits modifiés - ajout des produits nécessaire', ['deal_id' => $dealId]);
                }
                
                if ($shouldAddProducts) {
                    $productIdMapping = require __DIR__ . '/ProductIdMapping.php';
                    foreach ($est['items'] as $item) {
                        $code = $item['catalog_item'];
                        $pipedriveProductId = $productIdMapping[$code] ?? null;

                        if ($pipedriveProductId) {
                            $productData = [
                                'product_id' => $pipedriveProductId,
                                'item_price' => $item['unit_cost'],
                                'quantity' => $item['quantity']
                            ];
                            $response = $this->callPipedriveApi('POST', '/deals/' . $dealId . '/products', $productData);
                            if (isset($response->success) && $response->success) {
                                $this->logger->log('Produit ajouté avec succès au deal', [
                                    'deal_id' => $dealId,
                                    'product_id' => $pipedriveProductId,
                                    'quantity' => $item['quantity'],
                                    'item_price' => $item['unit_cost'],
                                    'response' => $response->data
                                ]);
                            } else {
                                $this->logger->log('Erreur lors de l\'ajout du produit au deal', [
                                    'deal_id' => $dealId,
                                    'product_id' => $pipedriveProductId,
                                    'error' => $response
                                ]);
                            }
                        } else {
                            $this->logger->log('ID produit Pipedrive manquant pour ce code', [
                                'catalog_item' => $code
                            ]);
                        }
                    }
                } else {
                    $this->logger->log('Ajout de produits ignoré - aucun changement détecté', [
                        'deal_id' => $dealId
                    ]);
                }

                // Mise à jour des catégories du deal APRÈS l'ajout des produits
                if ($dealId && $this->categoriesFieldKey) {
                    $categories = $this->extractCategoriesFromDeal($dealId);
                    if (!empty($categories)) {
                        $updateData = [$this->categoriesFieldKey => $categories];
                        
                        // Ajout de l'URL du devis si disponible
                        if ($this->devisInvoicedFieldKey && !empty($est['url'])) {
                            $updateData[$this->devisInvoicedFieldKey] = $est['url'];
                        }

                        // Ajout de l'ID numérique du devis pour le PipedriveHandler
                        $updateData['b8b55bcfd1cc07f3e577fb7a8d4fe498b435813a'] = (string)$est['id'];
                        
                        $this->callPipedriveApi('PUT', '/deals/' . $dealId, $updateData);
                        $this->logger->log('Catégories, URL et ID devis mis à jour dans le deal', [
                            'deal_id' => $dealId,
                            'categories' => $categories,
                            'url' => $est['url'] ?? 'non disponible',
                            'estimate_id' => $est['id']
                        ]);
                    } else {
                        $this->logger->log('Aucune catégorie trouvée pour le deal', [
                            'deal_id' => $dealId,
                            'products_count' => 'à vérifier'
                        ]);
                    }
                }
                break;

            /*────────────────────────────────────────────────────
             * 3. Devis accepté (estimate.approved)
             ────────────────────────────────────────────────────*/
            case 'estimate.approved':
                $est = $event['data']['object'];
                $dealId = $est['metadata']['pipedrive_deal_id'] ?? null;
                
                if (!$dealId) {
                    $this->logger->log('Deal ID non trouvé dans les métadonnées', ['estimate_id' => (string)($est['id'] ?? 'unknown')]);
                    break;
                }

                $this->logger->log('Mise à jour statut deal - devis validé', [
                    'estimate_id' => $est['id'],
                    'deal_id' => $dealId
                ]);

                $this->callPipedriveApi('PUT', '/deals/'.$dealId, [
                    'stage_id' => (int)$_ENV['PIPEDRIVE_STAGE_DEVIS_VALIDE'],
                ]);
                break;

            /*────────────────────────────────────────────────────
             * 3. Facture créée/mise à jour (invoice.created/invoice.updated)
             ────────────────────────────────────────────────────*/
            case 'invoice.created':
            case 'invoice.updated':
                $invoice = $event['data']['object'];
                $dealId = $invoice['metadata']['pipedrive_deal_id'] ?? null;
                
                if (!$dealId) {
                    $this->logger->log('Deal ID non trouvé dans les métadonnées de la facture', [
                        'invoice_id' => $invoice['id']
                    ]);
                    break;
                }

                $this->logger->log('Facture créée/mise à jour - calcul date du solde', [
                    'invoice_id' => $invoice['id'],
                    'deal_id' => $dealId,
                    'payment_terms' => $invoice['payment_terms'] ?? 'aucun'
                ]);

                // Calculer et mettre à jour la date du solde pour les professionnels
                $this->updateDateSolde($dealId, $invoice);
                break;

            /*────────────────────────────────────────────────────
             * 4. Facture payée (invoice.paid) - LOGIQUE AVANCÉE AVEC ACOMPTES
             ────────────────────────────────────────────────────*/
            case 'invoice.paid':
                $invoice = $event['data']['object'];
                $dealId = $invoice['metadata']['pipedrive_deal_id'] ?? null;
                
                if (!$dealId) {
                    $this->logger->log('Deal ID non trouvé dans les métadonnées de la facture', [
                        'invoice_id' => (string)($invoice['id'] ?? 'unknown')
                    ]);
                    break;
                }

                $this->logger->log('Facture entièrement payée - traitement workflow complet', [
                    'invoice_id' => $invoice['id'],
                    'deal_id' => $dealId,
                    'amount_paid' => $invoice['total'] ?? 0
                ]);

                // Récupération des détails du deal pour déterminer le type de client
                $dealDetails = $this->callPipedriveApi('GET', '/deals/' . $dealId);
                $deal = $dealDetails->data;
                $orgId = $deal->org_id->value ?? $deal->org_id ?? null;
                
                if ($orgId) {
                    $isCompany = $this->isCompanyCustomer($orgId);
                    
                    if ($isCompany) {
                        // PROFESSIONNEL : Passage à "Paiement complet" puis "Gagné"
                        $this->logger->log('Client professionnel - facture soldée', [
                            'deal_id' => $dealId,
                            'org_id' => $orgId
                        ]);
                        
                        $this->updateDealStage($dealId, 8); // Paiement Complet
                        $this->markDealAsWon($dealId, $invoice);
                    } else {
                        // PARTICULIER : Passage direct à "Paiement complet" puis "Gagné"
                        $this->logger->log('Client particulier - facture payée', [
                            'deal_id' => $dealId,
                            'org_id' => $orgId
                        ]);
                        
                        $this->updateDealStage($dealId, 8); // Paiement Complet
                        $this->markDealAsWon($dealId, $invoice);
                    }
                } else {
                    $this->logger->log('Organisation non trouvée pour le deal', ['deal_id' => $dealId]);
                }
                break;

            /*────────────────────────────────────────────────────
             * 5. Paiement partiel (invoice.payment_succeeded mais pas invoice.paid)
             ────────────────────────────────────────────────────*/
            case 'payment.created':
                $payment = $event['data']['object'];
                $invoiceId = null;
                
                // Récupération de l'ID de la facture depuis le paiement
                if (isset($payment['applied_to']) && is_array($payment['applied_to'])) {
                    foreach ($payment['applied_to'] as $application) {
                        if ($application['type'] === 'invoice') {
                            $invoiceId = $application['invoice'];
                            break;
                        }
                    }
                }
                
                if (!$invoiceId) {
                    $this->logger->log('ID facture non trouvé dans le paiement');
                    break;
                }
                
                // Vérification si la facture est entièrement payée
                $invoice = $this->inv->Invoice->retrieve($invoiceId);
                $dealId = $invoice->metadata['pipedrive_deal_id'] ?? null;
                
                if (!$dealId) {
                    $this->logger->log('Deal ID non trouvé dans les métadonnées de la facture', [
                        'invoice_id' => $invoiceId
                    ]);
                    break;
                }
                
                // Si la facture n'est PAS entièrement payée, c'est un acompte
                if ($invoice->status !== 'paid') {
                    $this->logger->log('Acompte reçu sur facture', [
                        'invoice_id' => $invoiceId,
                        'deal_id' => $dealId,
                        'payment_amount' => $payment['amount'] ?? 0,
                        'invoice_status' => $invoice->status,
                        'invoice_balance' => $invoice->balance ?? 0
                    ]);
                    
                    // Récupération des détails du deal pour déterminer le type de client
                    $dealDetails = $this->callPipedriveApi('GET', '/deals/' . $dealId);
                    $deal = $dealDetails->data;
                    $orgId = $deal->org_id->value ?? $deal->org_id ?? null;
                    
                    if ($orgId && $this->isCompanyCustomer($orgId)) {
                        // PROFESSIONNEL : Passage à "Paiement avant intervention"
                        $this->logger->log('Acompte professionnel reçu - passage à paiement avant intervention', [
                            'deal_id' => $dealId,
                            'org_id' => $orgId
                        ]);
                        
                        // Calcul et mise à jour de la date du solde
                        $this->updateDateSolde($dealId, $invoice);
                        
                        $this->updateDealStage($dealId, 6); // Paiement avant intervention effectué
                    }
                    // Pour les particuliers, pas d'acompte normalement (paiement immédiat)
                }
                break;

            default:
                $this->logger->log('Type d\'événement non géré', ['type' => $event['type'] ?? 'unknown']);
                break;
        }

        $this->logger->log('Traitement événement terminé');
    }

    /**
     * Gestion des erreurs d'API avec retry
     */
    private function callPipedriveApiWithRetry(string $method, string $endpoint, array $data = [], int $maxRetries = 3): object
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxRetries) {
            try {
                return $this->callPipedriveApi($method, $endpoint, $data);
            } catch (\Exception $e) {
                $lastException = $e;
                $attempt++;
                
                if ($attempt < $maxRetries) {
                    $this->logger->log('Tentative échouée, nouvelle tentative dans 1 seconde', [
                        'attempt' => $attempt,
                        'error' => $e->getMessage()
                    ]);
                    sleep(1);
                }
            }
        }

        throw $lastException;
    }

    private function createDealFromEstimate(array $est, $orgId = null, $personId = null): object
    {
        // Si les IDs ne sont pas fournis, essayer de les récupérer depuis les métadonnées du client
        if (!$orgId || !$personId) {
            $customerId = $est['customer']['id'] ?? null;
            if ($customerId) {
                $customer = $this->inv->Customer->retrieve($customerId);
                $orgId = $orgId ?: ($customer->metadata['pipedrive_org_id'] ?? null);
                $personId = $personId ?: ($customer->metadata['pipedrive_person_id'] ?? null);
            }
        }

        // Création du deal dans Pipedrive
        $dealData = [
            'title' => 'Devis #' . $est['number'],
            'value' => $est['total'],
            'currency' => strtoupper($est['currency']),
            'status' => 'open',
            'pipeline_id' => 1, // Pipeline Leads
            'stage_id' => 2, // Étape "Devis envoyé"
        ];

        if ($orgId) {
            $dealData['org_id'] = $orgId;
        }
        if ($personId) {
            $dealData['person_id'] = $personId;
        }

        // Ajout des champs personnalisés pour le devis
        if ($this->devisInvoicedFieldKey && !empty($est['url'])) {
            $dealData[$this->devisInvoicedFieldKey] = $est['url'];
            $this->logger->log('URL du devis ajoutée au deal', [
                'url' => $est['url'],
                'field_key' => $this->devisInvoicedFieldKey
            ]);
        }

        // Ajout de l'ID numérique du devis pour le PipedriveHandler
        $dealData['b8b55bcfd1cc07f3e577fb7a8d4fe498b435813a'] = (string)$est['id'];
        $this->logger->log('ID numérique du devis ajouté au deal', [
            'estimate_id' => $est['id']
        ]);

        return $this->callPipedriveApi('POST', '/deals', $dealData);
    }

    /**
     * Supprime tous les produits d'un deal
     */
    private function removeAllProductsFromDeal($dealId): void
    {
        try {
            // Récupération des produits existants du deal
            $productsResponse = $this->callPipedriveApi('GET', '/deals/' . $dealId . '/products');
            
            if (isset($productsResponse->data) && !empty($productsResponse->data)) {
                foreach ($productsResponse->data as $product) {
                    // Suppression de chaque produit du deal
                    $this->callPipedriveApi('DELETE', '/deals/' . $dealId . '/products/' . $product->id);
                    $this->logger->log('Produit supprimé du deal', [
                        'deal_id' => $dealId,
                        'product_id' => $product->id
                    ]);
                }
            }
        } catch (\Exception $e) {
            $this->logger->logError('Erreur lors de la suppression des produits du deal', $e);
        }
    }

    /**
     * Extrait les catégories des produits d'un deal depuis Pipedrive
     * @param int $dealId ID du deal
     * @return string Catégories séparées par des virgules
     */
    private function extractCategoriesFromDeal(int $dealId): string
    {
        try {
            // Récupération des produits du deal
            $productsResponse = $this->callPipedriveApi('GET', '/deals/' . $dealId . '/products');
            $categoryIds = [];

            if (isset($productsResponse->data) && !empty($productsResponse->data)) {
                foreach ($productsResponse->data as $dealProduct) {
                    // Récupération des détails du produit pour obtenir sa catégorie
                    $productResponse = $this->callPipedriveApi('GET', '/products/' . $dealProduct->product_id);
                    
                    if (isset($productResponse->data->category)) {
                        $categoryId = $productResponse->data->category;
                        if (!empty($categoryId) && !in_array($categoryId, $categoryIds)) {
                            $categoryIds[] = $categoryId;
                        }
                    }
                }
            }

            // Récupération des noms des catégories via ProductFields
            $categories = [];
            if (!empty($categoryIds)) {
                // Récupération des champs produits pour trouver le champ catégorie
                $productFieldsResponse = $this->callPipedriveApi('GET', '/productFields');
                
                // Log de tous les champs pour diagnostic
                $this->logger->log('Champs produits disponibles', [
                    'fields' => array_map(function($field) {
                        return [
                            'name' => $field->name,
                            'key' => $field->key,
                            'has_options' => isset($field->options),
                            'options_count' => isset($field->options) ? count($field->options) : 0
                        ];
                    }, $productFieldsResponse->data ?? [])
                ]);
                
                if (isset($productFieldsResponse->data)) {
                    foreach ($productFieldsResponse->data as $field) {
                        if ($field->name === 'Catégorie' && isset($field->options)) {
                            // Récupération des noms des catégories depuis les options
                            foreach ($field->options as $option) {
                                if (in_array($option->id, $categoryIds)) {
                                    $categories[] = $option->label;
                                }
                            }
                            break;
                        }
                    }
                }
            }

            $categoriesString = implode(', ', $categories);
            
            $this->logger->log('Catégories extraites du deal', [
                'deal_id' => $dealId,
                'categories' => $categoriesString,
                'category_ids' => $categoryIds,
                'products_count' => count($productsResponse->data ?? [])
            ]);

            return $categoriesString;
        } catch (\Exception $e) {
            $this->logger->logError('Erreur lors de l\'extraction des catégories', $e);
            return '';
        }
    }

    /**
     * Met à jour l'étape d'un deal
     */
    private function updateDealStage(int $dealId, int $stageId): void
    {
        try {
            $this->callPipedriveApi('PUT', '/deals/' . $dealId, [
                'stage_id' => $stageId
            ]);
            
            $this->logger->log('Étape du deal mise à jour', [
                'deal_id' => (string)$dealId,
                'stage_id' => (string)$stageId
            ]);
        } catch (\Exception $e) {
            $this->logger->logError('Erreur lors de la mise à jour de l\'étape du deal', $e);
        }
    }

    /**
     * Marque un deal comme gagné
     */
    private function markDealAsWon(int $dealId, array $invoice): void
    {
        try {
            $this->callPipedriveApi('PUT', '/deals/' . $dealId, [
                'status' => 'won'
            ]);
            
            // Sécurisation de l'accès aux données de la facture
            $invoiceId = isset($invoice['id']) ? (string)$invoice['id'] : 'unknown';
            $invoiceTotal = isset($invoice['total']) ? (string)$invoice['total'] : '0';
            
            $this->logger->log('Deal marqué comme gagné', [
                'deal_id' => (string)$dealId,
                'invoice_id' => $invoiceId,
                'invoice_total' => $invoiceTotal
            ]);
        } catch (\Exception $e) {
            $this->logger->logError('Erreur lors du marquage du deal comme gagné', $e);
        }
    }

    /**
     * Détermine si une organisation est une entreprise basé sur la présence du SIRET
     */
    private function isCompanyCustomer(int $orgId): bool
    {
        try {
            $orgDetails = $this->callPipedriveApi('GET', '/organizations/' . $orgId);
            
            // Vérification dans custom_fields (ancien format)
            $customFields = (array)($orgDetails->data->custom_fields ?? []);
            $hasSirenInCustomFields = !empty($customFields) && isset($customFields[$this->siretFieldKey]) && !empty($customFields[$this->siretFieldKey]);
            
            // Vérification directe dans l'objet (nouveau format)
            $orgData = (array)$orgDetails->data;
            $hasSirenDirect = isset($orgData[$this->siretFieldKey]) && !empty($orgData[$this->siretFieldKey]);
            
            $hasSiren = $hasSirenInCustomFields || $hasSirenDirect;
            
            $this->logger->log('Résultat vérification type client', [
                'org_id' => $orgId,
                'is_company' => $hasSiren,
                'custom_fields_count' => count($customFields),
                'siret_in_custom_fields' => $hasSirenInCustomFields,
                'siret_direct' => $hasSirenDirect,
                'siret_value' => $orgData[$this->siretFieldKey] ?? 'non trouvé'
            ]);
            
            return $hasSiren;
        } catch (\Exception $e) {
            $this->logger->logError('Erreur lors de la vérification du type de client', $e);
            return false;
        }
    }

    /**
     * Calcule et met à jour la date du solde basée sur la date d'édition + délai de paiement
     */
    private function updateDateSolde(int $dealId, $invoice): void
    {
        try {
            // Récupération de la facture Invoiced si ce n'est qu'un array
            if (is_array($invoice)) {
                $invoiceId = $invoice['id'] ?? null;
                if ($invoiceId) {
                    $invoice = $this->inv->Invoice->retrieve($invoiceId);
                }
            }
            
            // Date d'édition de la facture (date de création)
            $invoiceDate = $invoice->date ?? $invoice->created_at ?? time();
            if (is_string($invoiceDate)) {
                $invoiceDate = strtotime($invoiceDate);
            }
            
            // Délai de paiement (par défaut NET 30 pour les entreprises)
            $paymentTerms = $invoice->payment_terms ?? 'NET_30';
            
            // Calcul de la date du solde
            $dateSolde = $invoiceDate;
            switch ($paymentTerms) {
                case 'DUE_ON_RECEIPT':
                    // Paiement immédiat - date du solde = date de la facture
                    break;
                case 'NET_7':
                    $dateSolde = strtotime('+7 days', $invoiceDate);
                    break;
                case 'NET_15':
                    $dateSolde = strtotime('+15 days', $invoiceDate);
                    break;
                case 'NET_30':
                    $dateSolde = strtotime('+30 days', $invoiceDate);
                    break;
                case 'NET_45':
                    $dateSolde = strtotime('+45 days', $invoiceDate);
                    break;
                case 'NET_60':
                    $dateSolde = strtotime('+60 days', $invoiceDate);
                    break;
                case 'NET_90':
                    $dateSolde = strtotime('+90 days', $invoiceDate);
                    break;
                default:
                    // Par défaut NET 30
                    $dateSolde = strtotime('+30 days', $invoiceDate);
                    break;
            }
            
            // Format pour Pipedrive (YYYY-MM-DD)
            $dateSoldeFormatted = date('Y-m-d', $dateSolde);
            
            // Mise à jour du deal avec la date du solde
            $this->callPipedriveApi('PUT', '/deals/' . $dealId, [
                $this->dateSoldeFieldKey => $dateSoldeFormatted
            ]);
            
            $this->logger->log('Date du solde calculée et mise à jour', [
                'deal_id' => (string)$dealId,
                'invoice_date' => date('Y-m-d', $invoiceDate),
                'payment_terms' => $paymentTerms,
                'date_solde' => $dateSoldeFormatted
            ]);
            
        } catch (\Exception $e) {
            $this->logger->logError('Erreur lors du calcul de la date du solde', $e);
        }
    }
}
