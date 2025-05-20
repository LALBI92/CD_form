<?php
require 'vendor/autoload.php';
use \Mailjet\Resources;

// Définition du chemin absolu vers le fichier de log
$logFile = __DIR__ . '/devis.log';

// Log de début de script
file_put_contents($logFile, "=== Début du script PHP ===\n", FILE_APPEND);
error_log("=== Début du script PHP ===\n", 3, $logFile);

try {
    // Vérification si le fichier est accessible en écriture
    if (!is_writable(dirname($logFile))) {
        file_put_contents($logFile, "Erreur : Le répertoire n'est pas accessible en écriture : " . dirname($logFile) . "\n", FILE_APPEND);
        error_log("Erreur : Le répertoire n'est pas accessible en écriture : " . dirname($logFile) . "\n", 3, $logFile);
        die("Erreur : Le répertoire n'est pas accessible en écriture.");
    }

    if (!file_exists($logFile)) {
        file_put_contents($logFile, "Le fichier de log n'existe pas, il sera créé automatiquement.\n", FILE_APPEND);
        error_log("Le fichier de log n'existe pas, il sera créé automatiquement.\n", 3, $logFile);
    }

    // Initialisation du client Mailjet
    $mj = new \Mailjet\Client('66cac50d04be642b8e9d9f954dc2bed8', '76c992ace82e297ce6f62815b6a5f445', true, ['version' => 'v3.1']);
    file_put_contents($logFile, "=== Client Mailjet initialisé ===\n", FILE_APPEND);
    error_log("=== Client Mailjet initialisé ===\n", 3, $logFile);

    // Données du corps de la requête
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => "commercial@citydebarras.fr",  
                    'Name' => "Ton Nom"            
                ],
                'To' => [
                    [
                        'Email' => "bd@growthomodo.com",
                        'Name' => "Destinataire"
                    ]
                ],
                'Subject' => "Bonjour depuis Mailjet",
                'TextPart' => "Contenu texte de l'email",
                'HTMLPart' => "<h3>Contenu HTML de l'email</h3>"
            ]
        ]
    ];

    // Log des données de l'email
    file_put_contents($logFile, "=== Tentative d'envoi d'email ===\n" . print_r($body, true) . "\n=== Fin des données de l'email ===\n", FILE_APPEND);
    error_log("=== Tentative d'envoi d'email ===\n", 3, $logFile);
    error_log(print_r($body, true), 3, $logFile);
    error_log("\n=== Fin des données de l'email ===\n", 3, $logFile);

    // Envoi de la requête
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    file_put_contents($logFile, "=== Requête envoyée à Mailjet ===\n", FILE_APPEND);
    error_log("=== Requête envoyée à Mailjet ===\n", 3, $logFile);

    // Log des données de la réponse Mailjet
    if ($response->success()) {
        $responseData = $response->getData();
        file_put_contents($logFile, "=== Réponse réussie de Mailjet ===\n" . print_r($responseData, true) . "\n", FILE_APPEND);
        error_log("=== Réponse réussie de Mailjet ===\n", 3, $logFile);
        error_log(print_r($responseData, true), 3, $logFile);

        // Extraction du message_guid et de la date d'envoi
        foreach ($responseData['Messages'] as $message) {
            $messageGuid = $message['MessageID']; // MessageID est équivalent à message_guid
            $dateSent = date('Y-m-d H:i:s');

            // Log du message_guid et de la date d'envoi
            file_put_contents($logFile, "Message GUID : " . $messageGuid . "\nDate d'envoi : " . $dateSent . "\n", FILE_APPEND);
            error_log("Message GUID : " . $messageGuid . "\n", 3, $logFile);
            error_log("Date d'envoi : " . $dateSent . "\n", 3, $logFile);
        }
    } else {
        file_put_contents($logFile, "=== Erreur lors de l'envoi de l'email ===\nStatut HTTP : " . $response->getStatus() . "\nRaison de l'échec : " . $response->getReasonPhrase() . "\n" . print_r($response->getData(), true) . "\n", FILE_APPEND);
        error_log("=== Erreur lors de l'envoi de l'email ===\n", 3, $logFile);
        error_log("Statut HTTP : " . $response->getStatus() . "\n", 3, $logFile);
        error_log("Raison de l'échec : " . $response->getReasonPhrase() . "\n", 3, $logFile);
        error_log(print_r($response->getData(), true), 3, $logFile);
    }
} catch (Exception $e) {
    file_put_contents($logFile, "=== Exception capturée ===\nMessage d'erreur : " . $e->getMessage() . "\n", FILE_APPEND);
    error_log("=== Exception capturée ===\n", 3, $logFile);
    error_log("Message d'erreur : " . $e->getMessage() . "\n", 3, $logFile);
}

// Fin du script
file_put_contents($logFile, "=== Fin du script PHP ===\n", FILE_APPEND);
error_log("=== Fin du script PHP ===\n", 3, $logFile);
?>
