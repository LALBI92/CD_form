<?php

// Activer les erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Charger les dépendances via Composer
require 'vendor/autoload.php';

// Charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Charger les clés API depuis .env
$api_key = $_ENV['INVOICED_API_KEY'];

// Inclure le script pour gérer les fichiers téléchargés
$uploadsResponse = require 'uploads.php';

// Vérifier si les fichiers ont été téléchargés correctement
if (!$uploadsResponse['success']) {
    error_log("Erreur lors du téléchargement des fichiers : " . $uploadsResponse['message']);
    foreach ($uploadsResponse['files'] as $file) {
        if ($file['status'] === 'error') {
            error_log("Erreur fichier : " . $file['name'] . " - " . $file['message']);
        }
    }
} else {
    error_log("Fichiers téléchargés avec succès : " . json_encode($uploadsResponse['files']));
}

// Passer les données à email.php
require 'email.php';

// Importer les classes nécessaires
use Invoiced\Client;

// Initialiser le client Invoiced
$invoiced = new Client($api_key);


// Chemin vers le fichier de log
$logFile = 'devis.log';  
error_log("Début du script PHP\n", 3, $logFile);

// Récupération des données du formulaire
$nom = $_POST['name'] ?? ''; 
$email = $_POST['email'] ?? '';
$telephone = $_POST['phone'] ?? '';
$adresse = $_POST['add'] ?? '';
$raisonSociale = $_POST['raison'] ?? '';
$siret = (isset($_POST['siret']) && trim($_POST['siret']) !== '') ? trim($_POST['siret']) : null;
$description = $_POST['description'] ?? '';

// Récupérer le produit généré à partir du champ caché (hiddenProduitField)
$produit_genere = $_POST['hiddenProduitField'] ?? 'non spécifié';

// Log des informations du client récupérées
error_log("=== Informations du client ===\n", 3, $logFile);
error_log("Nom: $nom\nEmail: $email\nTéléphone: $telephone\nAdresse: $adresse\nRaison Sociale: $raisonSociale\nSIRET: $siret\nDescription: $description\n", 3, $logFile);

// Récupération des détails de l'étage et de l'ascenseur
$etage = $_POST['etage'] ?? 'non spécifié';
$ascenseur = $_POST['ascenseur'] ?? 'non spécifié';

// Log des informations de l'étage et de l'ascenseur
error_log("Étage: $etage\nAscenseur: $ascenseur\n", 3, $logFile);

// Log du produit généré
error_log("Produit généré: $produit_genere\n", 3, $logFile);

// Mapping des produits et produits degressifs
$config = require 'productMapping.php';
$productMapping      = $config['mapping'];
$produits_degressifs = $config['produits_degressifs'];

// Produits commandés
$produits_commandes = [];

// Log toutes les clés de $_POST pour vérifier leur correspondance avec les champs attendus
error_log("=== Vérification des données POST reçues ===\n", 3, $logFile);
foreach ($_POST as $key => $value) {
    if (is_array($value)) {
        // Si la valeur est un tableau, on utilise json_encode pour la convertir en chaîne de caractères
        error_log("Clé: $key, Valeur: " . json_encode($value) . "\n", 3, $logFile);
    } else {
        // Sinon, on logue directement la valeur
        error_log("Clé: $key, Valeur: $value\n", 3, $logFile);
    }
}


// Itérer sur les produits mappés pour construire la liste des produits commandés
foreach ($productMapping as $productName => $productId) {
    // Ajout du suffixe _qty pour correspondre aux noms des champs dans $_POST
    $productField = $productName . '_qty';

    // Log pour voir si le champ spécifique existe dans $_POST
    error_log("Vérification pour le produit: $productName avec champ: $productField\n", 3, $logFile);

    // Vérifier si la quantité est présente et convertir la valeur en entier
    if (isset($_POST[$productField])) {
        $quantite = intval($_POST[$productField]); // Convertir en entier

        // Log pour voir la quantité récupérée
        error_log("Quantité reçue pour $productField: $quantite\n", 3, $logFile);

        if ($quantite > 0) {
            // Si le produit est dans la liste des produits dégressifs
            if (in_array($productName, $produits_degressifs)) {
                // Récupérer la clé du produit correspondant à la quantité
                $mappedProductKey = ($quantite === 1) ? $productName : $productName . '_' . $quantite;

                // Vérifier que le produit existe dans le mapping
                if (isset($productMapping[$mappedProductKey])) {
                    $produits_commandes[] = [
                        'id' => $productMapping[$mappedProductKey],
                        'name' => $productName . ' (quantité ' . $quantite . ')',
                        'quantity' => 1 // Toujours 1 unité pour le produit dégressif
                    ];
                    error_log("Produit dégressif ajouté : $productName (quantité $quantite)\n", 3, $logFile);
                } else {
                    error_log("Produit dégressif manquant dans le mapping pour : $mappedProductKey\n", 3, $logFile);
                }
            } else {
                // Logique classique pour les produits standards
                $produits_commandes[] = [
                    'id' => $productId,
                    'name' => $productName,
                    'quantity' => $quantite
                ];
                error_log("Produit standard ajouté : $productName, Quantité : $quantite\n", 3, $logFile);
            }
        }
    } else {
        error_log("Champ $productField non trouvé dans les données POST\n", 3, $logFile);
    }
}

// Ajouter le produit généré par l'utilisateur aux produits commandés
if (!empty($produit_genere) && isset($productMapping[$produit_genere])) {
    $produits_commandes[] = [
        'id' => $productMapping[$produit_genere],
        'name' => $produit_genere,
        'quantity' => 1 // Quantité par défaut pour le produit généré
    ];
    error_log("Produit généré ajouté aux produits commandés: $produit_genere\n", 3, $logFile);
} else {
    error_log("Produit généré non trouvé dans le mapping: $produit_genere\n", 3, $logFile);
}

// Vérification pour les produits "Vrac à estimer" (domicile et entrepôt)
if (!empty($_POST['destruction_archives'])) {
    foreach ($_POST['destruction_archives'] as $archiveType) {
        if ($archiveType === 'vrac_domicile_estimer_archives') {
            $produits_commandes[] = [
                'id' => $productMapping['vrac_domicile_estimer_archives'],
                'name' => 'Vrac à estimer à domicile',
                'quantity' => 1 // Par défaut pour les cases à cocher
            ];
            error_log("Produit: Vrac à estimer à domicile ajouté aux produits commandés\n", 3, $logFile);
        } elseif ($archiveType === 'vrac_depot_estimer_archives') {
            $produits_commandes[] = [
                'id' => $productMapping['vrac_depot_estimer_archives'],
                'name' => 'Vrac à estimer à l\'entrepôt',
                'quantity' => 1
            ];
            error_log("Produit: Vrac à estimer à l'entrepôt ajouté aux produits commandés\n", 3, $logFile);
        }
    }
}


// Vérifier si un camion a été sélectionné
if (!empty($_POST['camion_selectionne'])) {
    $camion = $_POST['camion_selectionne'];
    if (isset($productMapping[$camion])) {
        // Ajouter le camion dans les produits commandés
        $produits_commandes[] = [
            'id' => $productMapping[$camion],
            'name' => $camion,
            'quantity' => 1 // Quantité par défaut pour un camion sélectionné
        ];
    }
}

// Log des produits commandés
error_log("=== Produits commandés ===\n", 3, $logFile);
if (!empty($produits_commandes)) {
    foreach ($produits_commandes as $produit) {
        error_log("Produit: {$produit['name']}, ID: {$produit['id']}, Quantité: {$produit['quantity']}\n", 3, $logFile);
    }
} else {
    error_log("Aucun produit commandé\n", 3, $logFile);
}

// Déterminer le type d'entité en fonction de la profession (Invoiced attend 'company' ou 'person')
$type = ($_POST['profession'] === "2") ? "company" : "person";
$paymentTerms = ($_POST['profession'] === "2") ? "NET 30" : "Due on Receipt";

// Création du client
try {
    // Préparer les données du client
    $customerData = [
        'name' => ($_POST['profession'] === "2") ? $raisonSociale : $nom,
        'email' => $email,
        'address1' => $adresse,
        'phone' => $telephone,
        'payment_terms' => $paymentTerms,
        'type' => $type,
        'metadata' => []
    ];

    // Ajouter attention_to seulement pour les entreprises
    if ($_POST['profession'] === "2") {
        $customerData['attention_to'] = $nom; // Nom du contact pour les entreprises
        if ($siret !== null) {
            $customerData['metadata']['siret'] = $siret;
        }
    }

    // Log pour debug
    error_log("Données client envoyées à Invoiced : " . print_r($customerData, true), 3, $logFile);

    // Créer le client
    $customer = $invoiced->Customer->create($customerData);
    error_log("Client créé : ID {$customer->id}, Type: {$type}\n", 3, $logFile);
} catch (Exception $e) {
    error_log("Erreur lors de la création du client : " . $e->getMessage() . "\n", 3, $logFile);
    exit("Erreur lors de la création du client.");
}

// Création du devis
$items = [];
foreach ($produits_commandes as $produit) {
    $items[] = [
        'catalog_item' => $produit['id'],
        'quantity' => $produit['quantity']
    ];
}

try {
    $estimate = $invoiced->Estimate->create([
        'customer' => $customer->id,
        'items' => $items,
        'notes' => $description
    ]);
    error_log("Devis créé : ID {$estimate->id}\n", 3, $logFile);
} catch (Exception $e) {
    error_log("Erreur lors de la création du devis : " . $e->getMessage() . "\n", 3, $logFile);
    exit("Erreur lors de la création du devis.");
}

try {
    $sendEstimate = $estimate->send(['to' => [['email' => $email]]]);
    error_log("Devis envoyé à {$email}\n", 3, $logFile);
} catch (Exception $e) {
    error_log("Erreur lors de l'envoi du devis : " . $e->getMessage() . "\n", 3, $logFile);
    exit("Erreur lors de l'envoi du devis.");
}

//echo "Client créé, devis généré et envoyé avec succès.";


// Redirection vers la page de remerciement après traitement
header("Location: https://citydebarras.fr/ty-devis");
exit(); // Assurez-vous de terminer le script après la redirection

?>

