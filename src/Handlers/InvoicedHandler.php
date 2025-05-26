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
        // 1. Vérifier si les champs existent déjà
        $fields = $this->callPipedriveApi('GET', '/organizationFields');
        foreach ($fields->data as $field) {
            if ($field->name === 'invoiced_id') {
                $this->invoicedFieldKey = $field->key;
                $this->logger->log('Champ organisation trouvé', ['key' => $this->invoicedFieldKey]);
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
    }

    /**
     * Appel API Pipedrive
     */
    private function callPipedriveApi(string $method, string $endpoint, array $data = []): object
    {
        $url = $this->pipedriveBaseUrl . $endpoint . '?api_token=' . $this->pipedriveToken;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout de 30 secondes
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout de connexion de 10 secondes
        
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
                if ($c['type'] === 'company' && !empty($c['metadata']['siret'])) {
                    $orgData['custom_fields'] = [
                        'siret' => $c['metadata']['siret']
                    ];
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
                    if ($c['type'] === 'company' && !empty($c['metadata']['siret'])) {
                        $orgData['custom_fields'] = [
                            'siret' => $c['metadata']['siret']
                        ];
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
                if (($est['status'] ?? '') === 'draft') {
                    $this->logger->log('Devis ignoré - statut brouillon', ['status' => $est['status'] ?? 'unknown']);
                    break;
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
                    $this->logger->log('ID client non trouvé dans le devis', ['estimate_id' => $est['id']]);
                    break;
                }

                $customer = $this->inv->Customer->retrieve($customerId);
                $orgId = $customer->metadata['pipedrive_org_id'] ?? null;
                $personId = $customer->metadata['pipedrive_person_id'] ?? null;

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

                        // Stockage des IDs dans les métadonnées du client
                        $customer->metadata = array_merge($customer->metadata ?? [], [
                            'pipedrive_org_id' => $orgId,
                            'pipedrive_person_id' => $personId
                        ]);
                        $customer->save();
                    } else {
                        $this->logger->log('Organisation non trouvée, création nécessaire', ['customer_id' => $customerId]);
                        // Code existant pour la création...
                    }
                }

                // Recherche du deal existant
                $dealId = null;
                
                // 1. D'abord, vérifier si l'ID du deal est stocké dans les métadonnées du devis
                if (!empty($est['metadata']['pipedrive_deal_id'])) {
                    $dealId = $est['metadata']['pipedrive_deal_id'];
                    $this->logger->log('Deal ID trouvé dans les métadonnées', ['deal_id' => $dealId]);
                } else {
                    // 2. Sinon, chercher par l'ID du devis
                    $estimateId = $est['id'] ?? null;
                    if ($estimateId) {
                        $search = $this->callPipedriveApi('GET', '/deals', [
                            'term' => (string)$estimateId,
                            'fields' => 'invoiced_estimate_id'
                        ]);
                        if (isset($search->data) && !empty($search->data)) {
                            foreach ($search->data as $deal) {
                                if (isset($deal->invoiced_estimate_id) && $deal->invoiced_estimate_id === (string)$estimateId) {
                                    $dealId = $deal->id;
                                    $this->logger->log('Deal trouvé par estimate_id', [
                                        'deal_id' => $dealId,
                                        'estimate_id' => $estimateId
                                    ]);
                                    break;
                                }
                            }
                        }
                    }
                }

                if (!$dealId) {
                    // Création du deal si non trouvé
                    $deal = $this->createDealFromEstimate($est);
                    $dealId = $deal->data->id ?? null;
                    $this->logger->log('Deal créé dans Pipedrive', ['deal_id' => $dealId]);
                    
                    // Stocker l'ID du deal dans les métadonnées du devis
                    $estimate = $this->inv->Estimate->retrieve($est['id']);
                    $estimate->metadata = array_merge($estimate->metadata ?? [], [
                        'pipedrive_deal_id' => $dealId
                    ]);
                    $estimate->save();
                } else {
                    // Si le deal existe déjà, supprimer tous les produits existants
                    $this->removeAllProductsFromDeal($dealId);
                    $this->logger->log('Produits existants supprimés du deal', ['deal_id' => $dealId]);
                    
                    // Mise à jour des catégories du deal existant
                    $categories = $this->extractCategoriesFromDeal($dealId);
                    if (!empty($categories) && $this->categoriesFieldKey) {
                        $updateData = [$this->categoriesFieldKey => $categories];
                        
                        // Ajout de l'URL du devis si disponible
                        if ($this->devisInvoicedFieldKey && !empty($est['url'])) {
                            $updateData[$this->devisInvoicedFieldKey] = $est['url'];
                        }

                        // Ajout de l'ID numérique du devis pour le PipedriveHandler
                        $updateData['b8b55bcfd1cc07f3e577fb7a8d4fe498b435813a'] = (string)$est['id'];
                        
                        $this->callPipedriveApi('PUT', '/deals/' . $dealId, $updateData);
                        $this->logger->log('Deal mis à jour avec catégories, URL et ID devis', [
                            'deal_id' => $dealId,
                            'categories' => $categories,
                            'url' => $est['url'] ?? 'non disponible',
                            'estimate_id' => $est['id']
                        ]);
                    }
                }

                // Ajout des produits au deal après sa création
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
                        // Log ou gestion de la réponse ici
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

                // Mise à jour des catégories du deal après ajout des produits
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
                    $this->logger->log('Deal ID non trouvé dans les métadonnées', ['estimate_id' => $est['id']]);
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
             * 4. Facture payée (invoice.paid)
             ────────────────────────────────────────────────────*/
            case 'invoice.paid':
                $inv = $event['data']['object'];
                $dealId = $inv['metadata']['pipedrive_deal_id'] ?? null;
                
                if (!$dealId) {
                    $this->logger->log('Deal ID non trouvé dans les métadonnées', ['invoice_id' => $inv['id']]);
                    break;
                }

                $this->logger->log('Mise à jour statut deal - facture payée', [
                    'invoice_id' => $inv['id'],
                    'deal_id' => $dealId
                ]);

                $this->callPipedriveApi('PUT', '/deals/'.$dealId, [
                    'stage_id' => (int)$_ENV['PIPEDRIVE_STAGE_PAYE'],
                ]);
                break;

            /*────────────────────────────────────────────────────
             * 5. Création/Mise à jour de facture
             ────────────────────────────────────────────────────*/
            case 'invoice.created':
            case 'invoice.updated':
                $inv = $event['data']['object'];
                $this->logger->log('Traitement facture', [
                    'invoice_id' => $inv['id'],
                    'status' => $inv['status'] ?? 'unknown'
                ]);

                // Recherche du deal associé au devis
                $estimateId = $inv['estimate'] ?? null;
                if (!$estimateId) {
                    $this->logger->log('Pas de devis associé à la facture', ['invoice_id' => $inv['id']]);
                    break;
                }

                // Recherche du deal via le devis
                $search = $this->callPipedriveApi('GET', '/deals', [
                    'term' => $estimateId,
                    'fields' => 'invoiced_estimate_id'
                ]);

                $dealId = null;
                foreach ($search->data as $deal) {
                    if (isset($deal->invoiced_estimate_id) && $deal->invoiced_estimate_id === (string)$estimateId) {
                        $dealId = $deal->id;
                        break;
                    }
                }

                if (!$dealId) {
                    $this->logger->log('Deal non trouvé pour le devis', [
                        'invoice_id' => $inv['id'],
                        'estimate_id' => $estimateId
                    ]);
                    break;
                }

                // Mise à jour du deal avec les informations de la facture
                $dealData = [
                    'title' => 'Facture #' . $inv['number'],
                    'value' => $inv['total'] * 100, // Conversion en centimes pour Pipedrive
                    'currency' => $inv['currency'],
                ];

                // Ajout des champs personnalisés pour la facture
                $dealData['custom_fields'] = [
                    'invoiced_invoice_id' => (string)$inv['id'],
                    'invoiced_invoice_url' => $inv['url'] ?? '',
                    'invoiced_invoice_status' => $inv['status'] ?? '',
                ];

                try {
                    $this->callPipedriveApi('PUT', '/deals/' . $dealId, $dealData);
                    $this->logger->log('Deal mis à jour avec les informations de la facture', [
                        'deal_id' => $dealId,
                        'invoice_id' => $inv['id']
                    ]);

                    // Stockage de l'ID du deal dans les métadonnées de la facture
                    $this->inv->Invoice->update($inv['id'], [
                        'metadata' => ['pipedrive_deal_id' => $dealId]
                    ]);
                } catch (\Exception $e) {
                    $this->logger->logError('Erreur lors de la mise à jour du deal', $e);
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

    private function createDealFromEstimate(array $est): object
    {
        // Récupération des IDs Pipedrive depuis les métadonnées du client
        $customerId = $est['customer']['id'] ?? null;
        if (!$customerId) {
            throw new \Exception('ID client non trouvé dans le devis');
        }

        $customer = $this->inv->Customer->retrieve($customerId);
        $orgId = $customer->metadata['pipedrive_org_id'] ?? null;
        $personId = $customer->metadata['pipedrive_person_id'] ?? null;

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
}
