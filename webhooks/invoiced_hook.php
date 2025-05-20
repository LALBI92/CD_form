<?php
require __DIR__.'/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__.'/..')->safeLoad();

use Src\Handlers\InvoicedHandler;
use Src\Logger\WebhookLogger;

$logger = new WebhookLogger('invoiced');

/* —————————————————————————————————— */
/* 0.  Logging helper (simple)        */
function log_debug($msg, $ctx = []) {
    error_log('[WEBHOOK] '.$msg.' '.json_encode($ctx));
}

try {
    /* 1. Lire headers + body brut ————————————— */
    $raw = file_get_contents('php://input');
    $sig = $_SERVER['HTTP_X_INVOICED_SIGNATURE'] ?? null;
    $logger->log('Webhook reçu', ['signature' => $sig, 'raw' => $raw]);

    /* 2. Vérif signature — nève jamais d'exception —— */
    $secret = $_ENV['INVOICED_WEBHOOK_SECRET'] ?? '';
    $valid  = $sig && hash_equals($sig, hash_hmac('sha256', $raw, $secret));
    if (!$valid) {
        $logger->log('Signature invalide');
        http_response_code(200);
        exit;
    }

    /* 3. JSON parse + basic sanity check ———————— */
    $payload = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $logger->log('Erreur JSON', ['err' => json_last_error_msg()]);
        http_response_code(200);
        exit;
    }

    /* 4. (Optionnel) drop les doublons rapides —— */
    // ex : file-based lock, APCu, ou juste rien en dev

    /* 5. Traitement métier (catch interne) ———— */
    $logger->log('Début du traitement', ['type' => $payload['type'] ?? 'unknown']);
    (new InvoicedHandler)->handle($payload);
    $logger->log('Traitement terminé avec succès');

    http_response_code(200);           // ✅ toujours
} catch (Throwable $e) {
    $logger->logError('Exception non gérée', $e);
    http_response_code(200);           // ✅ malgré l'erreur
}
