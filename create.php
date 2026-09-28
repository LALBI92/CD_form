<?php
// Script de test manuel : crée un client fictif dans Invoiced.
// Ligne de commande uniquement (il était exposé publiquement sur le web).
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require 'vendor/autoload.php';

// Clé lue dans .env, comme invoiced.php (plus jamais en dur dans le code)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$apiKey = $_ENV['INVOICED_API_KEY'] ?? '';
if ($apiKey === '') {
    fwrite(STDERR, "INVOICED_API_KEY absente du .env\n");
    exit(1);
}

$invoiced = new Invoiced\Client($apiKey);

$customer = $invoiced->Customer->create([
  'name' => "Acme",
  'email' => "billing@acmecorp.com", // Assurez-vous que l'adresse e-mail est valide
  'number' => "1234",
  'payment_terms' => "NET 30"
]);

// Afficher les détails du client créé
print_r($customer);
