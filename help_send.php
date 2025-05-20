<?php
// help_send.php

// Activer les erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Charger Composer et les dépendances
require 'vendor/autoload.php';
use \Mailjet\Resources;

// Charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Clés API
$mailjetPublicKey = $_ENV['MAILJET_PUBLIC_KEY'];
$mailjetPrivateKey = $_ENV['MAILJET_PRIVATE_KEY'];

// Initialisation Mailjet
$mj = new \Mailjet\Client($mailjetPublicKey, $mailjetPrivateKey, true, ['version' => 'v3.1']);

// Fichier de log (facultatif)
$logFile = __DIR__ . '/help.log';
file_put_contents($logFile, "=== Début help_send.php ===\n", FILE_APPEND);

try {
    // Récupération champs
    $helpName    = $_POST['helpName'] ?? '';
    $helpEmail   = $_POST['helpEmail'] ?? '';
    $helpMessage = $_POST['helpMessage'] ?? '';

    // Log
    file_put_contents($logFile, "Form data: $helpName / $helpEmail / $helpMessage\n", FILE_APPEND);

    // Validation
    if (empty($helpName) || empty($helpEmail) || empty($helpMessage)) {
        throw new Exception("Tous les champs sont obligatoires dans le formulaire d'aide.");
    }

    // Construire le contenu HTML
    $htmlContent = "
        <h2>Nouvelle demande d'aide</h2>
        <p><strong>Nom :</strong> $helpName</p>
        <p><strong>Email :</strong> $helpEmail</p>
        <p><strong>Message :</strong><br>$helpMessage</p>
    ";

    // Préparer la requête
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => "commercial@citydebarras.fr",
                    'Name'  => "City Débarras"
                ],
                'To' => [
                    // Mettez ici vos destinataires (adapté à vos besoins)
                    [ 'Email' => "commercial@citydebarras.fr", 'Name' => "Service Commercial" ],
                    [ 'Email' => "bd@growthomodo.com",         'Name' => "Backup"            ]
                ],
                'Subject' => "Nouvelle demande d'aide de $helpName",
                'HTMLPart' => $htmlContent
            ]
        ]
    ];

    // Envoi
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    file_put_contents($logFile, "Requête envoyée à Mailjet.\n", FILE_APPEND);

    if ($response->success()) {
        // Redirection vers la page "merci"
        header('Location: https://citydebarras.fr/ty-contact');
        exit; // Assurez-vous de stopper l'exécution après la redirection
    } else {
        // Gérer l’erreur
        $errorMessage = "Erreur d'envoi via Mailjet : " . json_encode($response->getData());
        throw new Exception($errorMessage);
    }


} catch (Exception $e) {
    file_put_contents($logFile, "Exception : ".$e->getMessage()."\n", FILE_APPEND);
    echo "<p style='font-family: sans-serif; color: red;'>Une erreur est survenue : " . htmlspecialchars($e->getMessage()) . "</p>";
}

file_put_contents($logFile, "=== Fin help_send.php ===\n", FILE_APPEND);
