<?php
namespace Src\Handlers;

use Invoiced\Client as InvClient;
use Src\Logger\WebhookLogger;

/**
 * Webhook Pipedrive v2 (deal.change) : gestion de la date d'intervention
 * et création automatique de factures selon le type de client
 */
class PipedriveHandler
{
    private InvClient $inv;
    private WebhookLogger $logger;
    private string $pipedriveToken;
    private string $pipedriveBaseUrl = 'https://api.pipedrive.com/v1';
    private ?string $delaiPaiementFieldKey = null;

    public function __construct()
    {
        $this->logger = new WebhookLogger('pipedrive-handler');
        $this->inv = new InvClient($_ENV['INVOICED_API_KEY']);
        $this->pipedriveToken = $_ENV['PIPEDRIVE_API_TOKEN'];
        
        // Initialisation des champs personnalisés
        $this->initCustomFields();
    }

    /**
     * Initialise les champs personnalisés nécessaires
     */
    private function initCustomFields(): void
    {
        // Vérifier si le champ "Délai de paiement" existe
        $dealFields = $this->callPipedriveApi('GET', '/dealFields');
        foreach ($dealFields->data as $field) {
            if ($field->name === 'Délai de paiement') {
                $this->delaiPaiementFieldKey = $field->key;
                $this->logger->log('Champ Délai de paiement trouvé', ['key' => $this->delaiPaiementFieldKey]);
                break;
            }
        }

        // Créer le champ s'il n'existe pas
        if (!$this->delaiPaiementFieldKey) {
            $field = $this->callPipedriveApi('POST', '/dealFields', [
                'name' => 'Délai de paiement',
                'field_type' => 'enum',
                'options' => [
                    ['label' => 'Paiement à réception (particuliers)', 'value' => 'DUE_ON_RECEIPT'],
                    ['label' => 'NET 7 (7 jours)', 'value' => 'NET_7'],
                    ['label' => 'NET 15 (15 jours)', 'value' => 'NET_15'],
                    ['label' => 'NET 30 (30 jours)', 'value' => 'NET_30'],
                    ['label' => 'NET 45 (45 jours)', 'value' => 'NET_45'],
                    ['label' => 'NET 60 (60 jours)', 'value' => 'NET_60'],
                    ['label' => 'NET 90 (90 jours)', 'value' => 'NET_90']
                ]
            ]);
            $this->delaiPaiementFieldKey = $field->data->key;
            $this->logger->log('Champ Délai de paiement créé', ['key' => $this->delaiPaiementFieldKey]);
        }

        // Vérifier/créer le champ "URL Facture Invoiced"
        $urlFactureExists = false;
        foreach ($dealFields->data as $field) {
            if ($field->name === 'URL Facture Invoiced') {
                $urlFactureExists = true;
                $this->logger->log('Champ URL Facture Invoiced trouvé', ['key' => $field->key]);
                break;
            }
        }

        if (!$urlFactureExists) {
            $urlField = $this->callPipedriveApi('POST', '/dealFields', [
                'name' => 'URL Facture Invoiced',
                'field_type' => 'varchar'
            ]);
            $this->logger->log('Champ URL Facture Invoiced créé', ['key' => $urlField->data->key]);
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
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
            throw new \Exception("Erreur API Pipedrive: $httpCode - $response");
        }

        return json_decode($response);
    }

    /** @param array $event  payload complet v2 */
    public function handle(array $event): void
    {
        $this->logger->log('Traitement événement Pipedrive', [
            'action' => $event['meta']['action'] ?? 'unknown',
            'entity' => $event['meta']['entity'] ?? 'unknown'
        ]);

        /* Filtrage basique déjà fait dans le hook */
        if (($event['meta']['action'] ?? null) !== 'change'
            || ($event['meta']['entity'] ?? null) !== 'deal') {
            $this->logger->log('Événement ignoré - type non géré');
            return;
        }

        $deal     = $event['data']     ?? [];
        $previous = $event['previous'] ?? [];

        // Détection du passage à l'étape "Intervention prévue" (ID: 5)
        $stageAfter  = $deal['stage_id'] ?? null;
        $stageBefore = $previous['stage_id'] ?? null;

        $this->logger->log('Vérification changement d\'étape', [
            'deal_id' => $deal['id'] ?? null,
            'stage_before' => $stageBefore,
            'stage_after' => $stageAfter
        ]);

        // Si le deal passe à l'étape "Intervention prévue" (5)
        // MAIS SEULEMENT si on vient d'une étape antérieure (éviter les retours en arrière)
        if ($stageBefore != 5 && $stageAfter == 5 && ($stageBefore === null || $stageBefore < 5)) {
            $this->logger->log('Deal passé à étape Intervention prévue depuis étape antérieure', [
                'deal_id' => $deal['id'],
                'from_stage' => $stageBefore,
                'to_stage' => $stageAfter
            ]);
            $this->processInterventionStage($deal);
        } elseif ($stageBefore != 5 && $stageAfter == 5 && $stageBefore >= 5) {
            $this->logger->log('Deal revenu à Intervention prévue depuis étape postérieure - ignoré', [
                'deal_id' => $deal['id'],
                'from_stage' => $stageBefore,
                'to_stage' => $stageAfter
            ]);
        }

        // Si le délai de paiement vient d'être renseigné pour une entreprise
        if ($this->delaiPaiementFieldKey) {
            $delaiPaiementBefore = $previous['custom_fields'][$this->delaiPaiementFieldKey]['value'] ?? null;
            $delaiPaiementAfter = $deal['custom_fields'][$this->delaiPaiementFieldKey]['value'] ?? null;
            
            if (empty($delaiPaiementBefore) && !empty($delaiPaiementAfter)) {
                $this->processPaymentTermsSet($deal);
            }
        }

        $this->logger->log('Traitement événement terminé');
    }

    /**
     * Traite l'événement : passage à l'étape "Intervention prévue"
     */
    private function processInterventionStage(array $deal): void
    {
        $dealId = $deal['id'] ?? null;
        if (!$dealId) {
            $this->logger->log('ID deal manquant');
            return;
        }

        // Récupération du deal complet pour avoir tous les champs
        $dealDetails = $this->callPipedriveApi('GET', '/deals/' . $dealId);
        $fullDeal = $dealDetails->data;

        // PROTECTION ANTI-DUPLICATION : Vérifier si une facture existe déjà
        $existingInvoiceId = $fullDeal->{'8bd7f09811f5c47aa719d924e9a193d1d3cf8242'} ?? null;
        if (!empty($existingInvoiceId)) {
            $this->logger->log('Facture déjà existante pour ce deal', [
                'deal_id' => $dealId,
                'existing_invoice_id' => $existingInvoiceId
            ]);
            
            // Même si la facture existe, on doit s'assurer que le deal est à l'étape "facturation"
            $this->ensureDealInFacturationStage($dealId, $existingInvoiceId);
            return; // ARRÊT - Ne pas créer de nouvelle facture
        }

        // Récupération de l'ID du devis Invoiced depuis les métadonnées
        $estimateId = $fullDeal->{'b8b55bcfd1cc07f3e577fb7a8d4fe498b435813a'} ?? null;
        
        if (!empty($estimateId)) {
            $this->logger->log('ID devis récupéré depuis le champ ID', ['estimate_id' => $estimateId]);
        } else {
            $this->logger->log('ID devis Invoiced non trouvé dans le deal', ['deal_id' => $dealId]);
            return;
        }

        // Récupération de l'organisation liée au deal
        $orgId = $fullDeal->org_id->value ?? $fullDeal->org_id ?? null;
        if (!$orgId) {
            $this->logger->log('Organisation non trouvée pour le deal', ['deal_id' => $dealId]);
            return;
        }

        // Récupération des détails de l'organisation
        $orgDetails = $this->callPipedriveApi('GET', '/organizations/' . $orgId);
        $organization = $orgDetails->data;

        // Détermination du type de client via le SIRET
        $isCompany = !empty($organization->custom_fields->siret ?? null);
        
        $this->logger->log('Type de client déterminé', [
            'deal_id' => $dealId,
            'org_id' => $orgId,
            'is_company' => $isCompany,
            'siret' => $organization->custom_fields->siret ?? 'non présent'
        ]);

        if ($isCompany) {
            // ENTREPRISE : Demander le délai de paiement
            $this->requestPaymentTerms($dealId);
        } else {
            // PARTICULIER : Créer facture immédiatement
            $this->createInvoiceForIndividual($estimateId, $dealId);
        }
    }

    /**
     * Demande à renseigner le délai de paiement pour une entreprise
     */
    private function requestPaymentTerms(int $dealId): void
    {
        $this->logger->log('Demande de délai de paiement pour entreprise', ['deal_id' => $dealId]);
        
        // On pourrait ajouter ici une note ou une activité dans Pipedrive
        // pour rappeler de renseigner le délai de paiement
        
        // Pour l'instant on log juste, l'utilisateur devra renseigner manuellement
        $this->logger->log('Action requise : renseigner le délai de paiement pour cette entreprise', [
            'deal_id' => $dealId
        ]);
    }

    /**
     * Traite l'événement : délai de paiement renseigné pour une entreprise
     */
    private function processPaymentTermsSet(array $deal): void
    {
        $dealId = $deal['id'] ?? null;
        $delaiPaiement = $deal['custom_fields'][$this->delaiPaiementFieldKey]['value'] ?? null;
        
        if (!$dealId || !$delaiPaiement) {
            $this->logger->log('Données manquantes pour traitement délai paiement', [
                'deal_id' => $dealId,
                'delai_paiement' => $delaiPaiement
            ]);
            return;
        }

        // Récupération de l'ID du devis
        $dealDetails = $this->callPipedriveApi('GET', '/deals/' . $dealId);
        $estimateId = $dealDetails->data->{'b8b55bcfd1cc07f3e577fb7a8d4fe498b435813a'} ?? null;
        
        if (!$estimateId) {
            $this->logger->log('ID devis non trouvé pour création facture entreprise', ['deal_id' => $dealId]);
            return;
        }

        $this->createInvoiceForCompany($estimateId, $dealId, $delaiPaiement);
    }

    /**
     * Crée une facture pour un particulier (paiement à réception)
     */
    private function createInvoiceForIndividual(string $estimateId, int $dealId): void
    {
        $this->logger->log('Création facture pour particulier', [
            'estimate_id' => $estimateId,
            'deal_id' => $dealId
        ]);

        try {
            // Récupération du devis
            $estimate = $this->inv->Estimate->retrieve($estimateId);
            
            // Conversion en facture avec paiement à réception
            $invoiceData = [
                'payment_terms' => 'DUE_ON_RECEIPT'  // ou 'NEVER' selon l'API Invoiced
            ];
            
            $invoice = $estimate->invoice($invoiceData);
            
            // Envoi de la facture
            $invoice->send();
            
            $this->logger->log('Facture créée et envoyée pour particulier', [
                'estimate_id' => $estimateId,
                'invoice_id' => $invoice->id,
                'deal_id' => $dealId
            ]);

            // Mise à jour du deal avec l'ID de la facture
            $this->updateDealWithInvoice($dealId, $invoice->id, 'DUE_ON_RECEIPT');

        } catch (\Exception $e) {
            $this->logger->logError('Erreur création facture particulier', $e);
        }
    }

    /**
     * Crée une facture pour une entreprise avec délai de paiement
     */
    private function createInvoiceForCompany(string $estimateId, int $dealId, string $paymentTerms): void
    {
        $this->logger->log('Création facture pour entreprise', [
            'estimate_id' => $estimateId,
            'deal_id' => $dealId,
            'payment_terms' => $paymentTerms
        ]);

        try {
            // Mapping des délais vers les valeurs Invoiced
            $invoicedTermsMapping = [
                'NET_7' => 'NET_7',
                'NET_15' => 'NET_15', 
                'NET_30' => 'NET_30',
                'NET_45' => 'NET_45',
                'NET_60' => 'NET_60',
                'NET_90' => 'NET_90'
            ];

            $invoicedTerms = $invoicedTermsMapping[$paymentTerms] ?? 'NET_30';

            // Récupération du devis
            $estimate = $this->inv->Estimate->retrieve($estimateId);
            
            // Conversion en facture avec délai de paiement
            $invoiceData = [
                'payment_terms' => $invoicedTerms
            ];
            
            $invoice = $estimate->invoice($invoiceData);
            
            // Envoi de la facture
            $invoice->send();
            
            $this->logger->log('Facture créée et envoyée pour entreprise', [
                'estimate_id' => $estimateId,
                'invoice_id' => $invoice->id,
                'deal_id' => $dealId,
                'payment_terms' => $invoicedTerms
            ]);

            // Mise à jour du deal avec l'ID de la facture
            $this->updateDealWithInvoice($dealId, $invoice->id, $invoicedTerms);

        } catch (\Exception $e) {
            $this->logger->logError('Erreur création facture entreprise', $e);
        }
    }

    /**
     * Met à jour le deal avec l'ID et l'URL de la facture créée, le délai de paiement, et change l'étape vers "facturation"
     */
    private function updateDealWithInvoice(int $dealId, string $invoiceId, string $paymentTerms): void
    {
        try {
            // Récupération de la facture pour obtenir son URL
            $invoice = $this->inv->Invoice->retrieve($invoiceId);
            $invoiceUrl = $invoice->url ?? null;

            // Récupération de la clé du champ URL Facture
            $dealFields = $this->callPipedriveApi('GET', '/dealFields');
            $urlFactureFieldKey = null;
            
            foreach ($dealFields->data as $field) {
                if ($field->name === 'URL Facture Invoiced') {
                    $urlFactureFieldKey = $field->key;
                    break;
                }
            }

            // Préparation des données de mise à jour
            $updateData = [];
            
            // Ajout de l'ID de la facture
            $updateData['8bd7f09811f5c47aa719d924e9a193d1d3cf8242'] = $invoiceId;
            
            // Ajout de l'URL de la facture si disponible
            if ($urlFactureFieldKey && $invoiceUrl) {
                $updateData[$urlFactureFieldKey] = $invoiceUrl;
            }
            
            // Ajout du délai de paiement
            if ($this->delaiPaiementFieldKey) {
                $updateData[$this->delaiPaiementFieldKey] = $paymentTerms;
            }
            
            // Changement d'étape vers "facturation"
            $updateData['stage_id'] = 6; // ID de l'étape "facturation"

            // Mise à jour du deal
            $response = $this->callPipedriveApi('PUT', "/deals/{$dealId}", $updateData);
            
            $this->logger->log('Deal mis à jour avec facture, délai de paiement et étape facturation', [
                'deal_id' => $dealId,
                'invoice_id' => $invoiceId,
                'invoice_url' => $invoiceUrl,
                'payment_terms' => $paymentTerms,
                'stage_id' => 6
            ]);
            
        } catch (Exception $e) {
            $this->logger->log('Erreur lors de la mise à jour du deal avec la facture', [
                'error' => $e->getMessage(),
                'deal_id' => $dealId,
                'invoice_id' => $invoiceId
            ]);
        }
    }

    /**
     * S'assure que le deal avec une facture existante est à l'étape "facturation"
     */
    private function ensureDealInFacturationStage(int $dealId, string $existingInvoiceId): void
    {
        try {
            // Récupération du deal complet pour vérifier l'étape actuelle
            $fullDeal = $this->callPipedriveApi('GET', "/deals/{$dealId}");
            $currentStage = $fullDeal->data->stage_id ?? null;
            
            if ($currentStage != 6) { // Si pas déjà à l'étape "facturation"
                
                // Récupération de la facture pour obtenir son délai de paiement
                $invoice = $this->inv->Invoice->retrieve($existingInvoiceId);
                $paymentTerms = $invoice->payment_terms ?? 'DUE_ON_RECEIPT';
                
                // Convertir le délai Invoiced vers le format Pipedrive si nécessaire
                $pipedriveTerms = $this->convertInvoicedTermsToPipedrive($paymentTerms);
                
                $updateData = [
                    'stage_id' => 6  // Étape "facturation"
                ];
                
                // Ajouter le délai de paiement si le champ existe
                if ($this->delaiPaiementFieldKey) {
                    $updateData[$this->delaiPaiementFieldKey] = $pipedriveTerms;
                }
                
                $this->callPipedriveApi('PUT', "/deals/{$dealId}", $updateData);
                
                $this->logger->log('Deal avec facture existante mis à jour vers étape facturation', [
                    'deal_id' => $dealId,
                    'invoice_id' => $existingInvoiceId,
                    'payment_terms' => $pipedriveTerms,
                    'previous_stage' => $currentStage,
                    'new_stage' => 6
                ]);
            }
            
        } catch (Exception $e) {
            $this->logger->log('Erreur lors de la mise à jour de l\'étape vers facturation', [
                'error' => $e->getMessage(),
                'deal_id' => $dealId,
                'invoice_id' => $existingInvoiceId
            ]);
        }
    }
    
    /**
     * Convertit les délais de paiement Invoiced vers le format Pipedrive
     */
    private function convertInvoicedTermsToPipedrive(string $invoicedTerms): string
    {
        $mapping = [
            'DUE_ON_RECEIPT' => 'Due on Receipt',
            'NET_7' => 'NET 7',
            'NET_15' => 'NET 15', 
            'NET_30' => 'NET 30',
            'NET_45' => 'NET 45',
            'NET_60' => 'NET 60',
            'NET_90' => 'NET 90'
        ];
        
        return $mapping[$invoicedTerms] ?? $invoicedTerms;
    }
}
