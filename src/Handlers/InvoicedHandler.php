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

                if ($c['type'] === 'company') {
                    // 1. Création de l'organisation
                    $orgData = [
                        'name' => $c['name'],
                    ];

                    if (!empty($c['address1'])) {
                        $orgData['address'] = $c['address1'];
                    }

                    // Ajout du champ personnalisé et du SIRET
                    $orgData[$this->invoicedFieldKey] = (string)$c['id'];
                    if (!empty($c['metadata']['siret'])) {
                        $orgData['custom_fields'] = [
                            'siret' => $c['metadata']['siret']
                        ];
                    }

                    $org = $this->callPipedriveApi('POST', '/organizations', $orgData);

                    // 2. Création du contact lié à l'organisation
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
                } else {
                    // Création d'une simple personne
                    $personData = [
                        'name' => $c['name'],
                    ];

                    if (!empty($c['email'])) {
                        $personData['email'] = [$c['email']];
                    }
                    if (!empty($c['phone'])) {
                        $personData['phone'] = [$c['phone']];
                    }

                    // Ajout du champ personnalisé
                    $personData[$this->invoicedPersonFieldKey] = (string)$c['id'];

                    $person = $this->callPipedriveApi('POST', '/persons', $personData);

                    $this->logger->log('Personne créée dans Pipedrive', [
                        'invoiced_id' => $c['id'],
                        'person_id' => $person->data->id
                    ]);

                    // Stocke l'ID Pipedrive côté Invoiced
                    $this->inv->Customer->update($c['id'], [
                        'metadata' => ['pipedrive_person_id' => $person->data->id],
                    ]);
                }
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

                if ($c['type'] === 'company' && $orgId) {
                    // Mise à jour de l'organisation
                    $orgData = [
                        'name' => $c['name'],
                    ];

                    if (!empty($c['address1'])) {
                        $orgData['address'] = $c['address1'];
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
                    $this->inv->Estimate->update($est['id'], [
                        'metadata' => ['pipedrive_deal_id' => $dealId]
                    ]);
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

            case 'estimate.updated':
                $est = $event['data']['object'];
                if (($est['status'] ?? '') === 'draft') {
                    $this->logger->log('Devis ignoré - statut brouillon', ['status' => $est['status'] ?? 'unknown']);
                    break;
                }

                $this->logger->log('Traitement devis (mise à jour)', [
                    'estimate_id' => $est['id'],
                    'status' => $est['status'] ?? 'unknown'
                ]);

                // Recherche du deal existant par numéro de devis (DEV-xxxx)
                $dealId = null;
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
                                $this->logger->log('Deal existant trouvé', [
                                    'deal_id' => $dealId,
                                    'estimate_id' => $estimateId
                                ]);
                                break;
                            }
                        }
                    }
                }

                if (!$dealId) {
                    // Création du deal si non trouvé
                    $deal = $this->createDealFromEstimate($est);
                    $dealId = $deal->data->id ?? null;
                    $this->logger->log('Deal créé dans Pipedrive', ['deal_id' => $dealId]);
                }

                // Suppression des produits existants du deal
                $this->removeAllProductsFromDeal($dealId);

                // Ajout des nouveaux produits du devis
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
                                'response' => $response->data ?? $response
                            ]);
                        } else {
                            $this->logger->log('Erreur lors de l\'ajout du produit au deal', [
                                'deal_id' => $dealId,
                                'product_id' => $pipedriveProductId,
                                'response' => $response
                            ]);
                        }
                    } else {
                        $this->logger->log('Produit non trouvé dans le mapping', [
                            'catalog_item' => $code,
                            'item' => $item
                        ]);
                    }
                }
                $this->logger->log('Traitement événement terminé', []);
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
        $dealData['custom_fields'] = [
            'invoiced_estimate_id' => (string)$est['id'],
            'invoiced_estimate_url' => $est['url'] ?? '',
            'invoiced_estimate_status' => $est['status'] ?? '',
        ];

        return $this->callPipedriveApi('POST', '/deals', $dealData);
    }
}
