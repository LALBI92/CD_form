<?php
namespace Src\Handlers;

use Invoiced\Client as InvClient;
use Src\Logger\WebhookLogger;

/**
 * Webhook Pipedrive v2 (deal.change) : lorsqu'on ajoute la date
 * d'intervention, on convertit le devis Invoiced en facture puis
 * on expédie le PDF au client.
 */
class PipedriveHandler
{
    private InvClient $inv;
    private WebhookLogger $logger;

    public function __construct()
    {
        $this->logger = new WebhookLogger('pipedrive-handler');
        $this->inv = new InvClient($_ENV['INVOICED_API_KEY']);
    }

    /** @param array $event  payload complet v2 */
    public function handle(array $event): void
    {
        $this->logger->log('Traitement événement Pipedrive', [
            'action' => $event['meta']['action'] ?? 'unknown',
            'entity' => $event['meta']['entity'] ?? 'unknown'
        ]);

        /* Filtrage basique déjà fait dans le hook ; garde-fou */
        if (($event['meta']['action'] ?? null) !== 'change'
            || ($event['meta']['entity'] ?? null) !== 'deal') {
            $this->logger->log('Événement ignoré - type non géré');
            return;
        }

        $deal     = $event['data']     ?? [];
        $previous = $event['previous'] ?? [];

        /* ID champ custom "Date d'intervention" */
        $dateField = 'xxxx_intervention_date';      // ← remplace

        $before = $previous[$dateField] ?? null;
        $after  = $deal[$dateField]    ?? null;

        $this->logger->log('Vérification date d\'intervention', [
            'deal_id' => $deal['id'] ?? null,
            'before' => $before,
            'after' => $after
        ]);

        /* Si la date vient d'être renseignée */
        if (empty($before) && !empty($after)) {
            $estimateId = $deal['custom_fields']['invoiced_estimate_id'] ?? null;
            if (!$estimateId) {
                $this->logger->log('ID devis non trouvé dans les métadonnées', ['deal_id' => $deal['id'] ?? null]);
                return;
            }

            $this->logger->log('Conversion devis en facture', [
                'deal_id' => $deal['id'] ?? null,
                'estimate_id' => $estimateId
            ]);

            try {
                $estimate = $this->inv->estimates()->retrieve($estimateId);
                $invoice  = $estimate->invoice();   // conversion
                $invoice->send();                   // envoi PDF

                $this->logger->log('Facture créée et envoyée', [
                    'estimate_id' => $estimateId,
                    'invoice_id' => $invoice->id
                ]);
            } catch (\Throwable $e) {
                $this->logger->logError('Erreur lors de la conversion du devis', $e);
            }
        } else {
            $this->logger->log('Date d\'intervention non modifiée ou déjà renseignée');
        }

        $this->logger->log('Traitement événement terminé');
    }
}
