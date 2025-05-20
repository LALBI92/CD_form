<?php
namespace Src\Logger;

class WebhookLogger
{
    private string $logFile;
    private string $source;

    public function __construct(string $source)
    {
        $this->source = $source;
        $this->logFile = __DIR__ . '/../../logs/webhooks.log';
        
        // Créer le dossier logs s'il n'existe pas
        if (!is_dir(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }

    public function log(string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = [
            'timestamp' => $timestamp,
            'source' => $this->source,
            'message' => $message,
            'context' => $context
        ];

        $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
        file_put_contents($this->logFile, $logLine, FILE_APPEND);
    }

    public function logError(string $message, \Throwable $e): void
    {
        $this->log($message, [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
} 