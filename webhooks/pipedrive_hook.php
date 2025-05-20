<?php
/**
 * Webhook Pipedrive v2  –  entity = deal, action = change
 * Flux :
 *   ➊ Log de la requête (headers + body brut)
 *   ➋ Auth par token query-string (?token=…)
 *   ➌ Parse JSON + contrôle meta
 *   ➍ Appel du handler métier
 *   ➎ Toujours répondre 200 (ou 204 si on ignore)
 */

require __DIR__.'/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__.'/..')->safeLoad();

use Src\Handlers\PipedriveHandler;
use Src\Logger\WebhookLogger;

$logger = new WebhookLogger('pipedrive');

/* -----------------------------------------------------------------
 * Helper log : error_log() => logs PHP (ou remplace par Monolog)
 * ----------------------------------------------------------------*/
function log_hook(string $step, array $context = []): void
{
    error_log('[PD-HOOK] '.$step.' '.json_encode($context, JSON_UNESCAPED_SLASHES));
}

try {
    /* 1) Récupération headers + body brut -------------------------*/
    $raw  = file_get_contents('php://input') ?: '';
    $hdrs = getallheaders();
    $logger->log('Webhook reçu', ['query' => $_GET, 'headers' => $hdrs, 'body' => $raw]);

    /* 2) Auth par token query-string ------------------------------*/
    $expected = $_ENV['PIPEDRIVE_HOOK_TOKEN'] ?? '';
    if (($_GET['token'] ?? '') !== $expected) {
        $logger->log('Token invalide', ['got' => $_GET['token'] ?? null]);
        http_response_code(200);   // on confirme la réception, mais on ne traite pas
        exit;
    }

    /* 3) Parse JSON + filtre meta --------------------------------*/
    $payload = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $logger->log('Erreur JSON', ['err' => json_last_error_msg()]);
        http_response_code(200);   // ignore
        exit;
    }

    $meta = $payload['meta'] ?? [];
    if (($meta['action'] ?? null) !== 'change' || ($meta['entity'] ?? null) !== 'deal') {
        $logger->log('Événement ignoré', ['meta' => $meta]);
        http_response_code(204);   // traité, rien à faire
        exit;
    }

    /* 4) Traitement métier ---------------------------------------*/
    $logger->log('Début du traitement', ['deal_id' => $payload['data']['id'] ?? null]);
    (new PipedriveHandler())->handle($payload);
    $logger->log('Traitement terminé avec succès');

    http_response_code(200);       // succès même si le handler a géré des erreurs
} catch (\Throwable $e) {
    $logger->logError('Exception non gérée', $e);
    http_response_code(200);
}
