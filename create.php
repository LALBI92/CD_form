<?php
require 'vendor/autoload.php';

$invoiced = new Invoiced\Client("nOn4ifncFkuCuxOcd7EmkQ50MJewNlxH"); // Remplacez avec votre clé API réelle

$customer = $invoiced->Customer->create([
  'name' => "Acme",
  'email' => "billing@acmecorp.com", // Assurez-vous que l'adresse e-mail est valide
  'number' => "1234",
  'payment_terms' => "NET 30"
]);

// Afficher les détails du client créé
print_r($customer);
