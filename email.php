<?php
/**************************
 *  CONFIG & INITIALISATION
 **************************/

// -- Debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// -- Autoload & dépendances
require 'vendor/autoload.php';
use Mailjet\Resources;

// -- Fichier de log
$logFile = __DIR__ . '/devis.log';
file_put_contents($logFile, "\n=== Début du script PHP ===\n", FILE_APPEND);

// -- Variables d’environnement (.env)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$mailjetPublicKey  = $_ENV['MAILJET_PUBLIC_KEY']  ?? '';
$mailjetPrivateKey = $_ENV['MAILJET_PRIVATE_KEY'] ?? '';

// -- Client Mailjet
$mj = new \Mailjet\Client(
    $mailjetPublicKey,
    $mailjetPrivateKey,
    true,
    ['version' => 'v3.1']
);
file_put_contents($logFile, "Client Mailjet initialisé.\n", FILE_APPEND);

/**************************
 *  FONCTIONS UTILITAIRES
 **************************/

/**
 * Échappe proprement une chaîne pour l’HTML (UTF-8, ENT_QUOTES)
 */
function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**************************
 *  LOGIQUE MÉTIER
 **************************/

try {
    /*----------------------------------------------------------
     * 1. TRAITEMENT DES UPLOADS (on suppose $uploadsResponse)
     *---------------------------------------------------------*/
    if (!empty($uploadsResponse) && $uploadsResponse['success']) {
        file_put_contents(
            $logFile,
            "Fichiers téléchargés : " . json_encode($uploadsResponse['files']) . "\n",
            FILE_APPEND
        );
    }

    /*----------------------------------------------------------
     * 2. RÉCUPÉRATION DES DONNÉES DU FORMULAIRE (BRUTES)
     *---------------------------------------------------------*/
    $nom          = $_POST['name']        ?? '';
    $email        = $_POST['email']       ?? '';
    $telephone    = $_POST['phone']       ?? '';
    $adresse      = $_POST['add']         ?? '';
    $raisonSociale= $_POST['raison']      ?? '';
    $siret        = $_POST['siret']       ?? '';
    $etage        = $_POST['etage']       ?? 'non spécifié';
    $ascenseur    = $_POST['ascenseur']   ?? 'non spécifié';
    $description  = $_POST['description'] ?? '';
    $je_recycle   = $_POST['je_recycle']  ?? 'non spécifié';
    $produit_genere = $_POST['hiddenProduitField'] ?? 'non spécifié';

    file_put_contents(
        $logFile,
        "Formulaire reçu : " . json_encode($_POST) . "\n",
        FILE_APPEND
    );

    /*----------------------------------------------------------
     * 3. VALIDATION MINIMALE
     *---------------------------------------------------------*/
    if (trim($nom) === '' || trim($email) === '') {
        throw new Exception("Le nom et l'e-mail sont obligatoires.");
    }

    /*----------------------------------------------------------
     * 4. PRODUITS COMMANDÉS
     *---------------------------------------------------------*/
    $config           = require 'productMapping.php';
    $productMapping   = $config['mapping'];
    $produits_commandes = [];

    // Produits “normaux”
    foreach ($productMapping as $productName => $productId) {
        $qtyField = $productName . '_qty';
        if (!empty($_POST[$qtyField])) {
            $quantite = (int) $_POST[$qtyField];
            if ($quantite > 0) {
                $produits_commandes[] = [
                    'id'       => $productId,
                    'name'     => $productName,
                    'quantity' => $quantite
                ];
            }
        }
    }

    // Produits “vrac”
    if (!empty($_POST['destruction_archives']) && is_array($_POST['destruction_archives'])) {
        foreach ($_POST['destruction_archives'] as $vrac) {
            if (isset($productMapping[$vrac])) {
                $produits_commandes[] = [
                    'id'       => $productMapping[$vrac],
                    'name'     => $vrac,
                    'quantity' => 1
                ];
            }
        }
    }

    /*----------------------------------------------------------
     * 5. LIENS DES FICHIERS TÉLÉCHARGÉS
     *---------------------------------------------------------*/
    $fileLinks = [];
    if (!empty($uploadsResponse['files'])) {
        $baseUrl = 'https://devis.cityrecyclage.com/uploads/';
        foreach ($uploadsResponse['files'] as $f) {
            if ($f['status'] === 'success') {
                $fileLinks[] = $baseUrl . basename($f['path']);
            }
        }
    }

    /*----------------------------------------------------------
     * 6. CONSTRUCTION DE L’EMAIL HTML
     *---------------------------------------------------------*/
    ob_start(); ?>
    <div style="font-family: Arial, sans-serif; line-height:1.6; color:#333; max-width:600px; margin:auto;">
        <h2 style="text-align:center; background:#4CAF50; color:#fff; padding:10px;">
            Nouvelle demande de devis
        </h2>

        <div style="padding:20px; border:1px solid #ddd;">
            <h3>Informations Client</h3>

            <p><strong>Nom&nbsp;:</strong> <?= e($nom) ?></p>
            <p><strong>Société&nbsp;:</strong> <?= e($raisonSociale) ?></p>
            <p><strong>Email&nbsp;:</strong> <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p>
            <p><strong>Téléphone&nbsp;:</strong> <a href="tel:<?= e($telephone) ?>"><?= e($telephone) ?></a></p>
            <p><strong>Adresse&nbsp;:</strong> <?= e($adresse) ?></p>
            <p><strong>Étage&nbsp;:</strong> <?= e($etage) ?></p>
            <p><strong>Ascenseur&nbsp;:</strong> <?= e($ascenseur) ?></p>
            <p><strong>Catégorie du produit&nbsp;:</strong> <?= e($je_recycle) ?></p>
            <p><strong>Description&nbsp;:</strong><br><?= nl2br(e($description)) ?></p>

            <p><strong>Camion&nbsp;:</strong>
            <?php if (!empty($produit_genere) && $produit_genere !== 'etageRDC_none' && strpos($produit_genere, 'none') === false): ?>
                <?= e($produit_genere) ?>
            <?php else: ?>
                Pas de camion commandé
            <?php endif; ?>
            </p>

            <?php if (!empty($produits_commandes)): ?>
                <h3>Produits commandés</h3>
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="border:1px solid #ddd; padding:8px; text-align:left;">Produit</th>
                            <th style="border:1px solid #ddd; padding:8px; text-align:center;">Quantité</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($produits_commandes as $p): ?>
                        <tr>
                            <td style="border:1px solid #ddd; padding:8px;"><?= e($p['name']) ?></td>
                            <td style="border:1px solid #ddd; padding:8px; text-align:center;"><?= e($p['quantity']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun produit commandé.</p>
            <?php endif; ?>

            <?php if (!empty($fileLinks)): ?>
                <h3>Fichiers téléchargés</h3>
                <ul>
                <?php foreach ($fileLinks as $link): ?>
                    <li><a href="<?= e($link) ?>" target="_blank"><?= e($link) ?></a></li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <p style="text-align:center; margin-top:20px; color:#666;">
            Cet email a été généré automatiquement, merci de ne pas y répondre directement.
        </p>
    </div>
    <?php
    $htmlContent = ob_get_clean();

    file_put_contents($logFile, "HTML généré.\n", FILE_APPEND);

    /*----------------------------------------------------------
     * 7. ENVOI PAR MAILJET
     *---------------------------------------------------------*/
    $body = [
        'Messages' => [[
            'From' => [
                'Email' => 'commercial@citydebarras.fr',
                'Name'  => 'City Débarras'
            ],
            'To' => [
                ['Email' => 'commercial@citydebarras.fr', 'Name' => 'Service Commercial'],
                ['Email' => 'bd@growthomodo.com',        'Name' => 'Backup']
            ],
            'Subject'  => "Nouvelle demande de devis de $nom",
            'HTMLPart' => $htmlContent,
            'Headers'  => [
                'Content-Type' => 'text/html; charset=UTF-8'
            ]
        ]]
    ];

    file_put_contents($logFile, "Tentative d'envoi…\n", FILE_APPEND);
    $response = $mj->post(Resources::$Email, ['body' => $body]);

    if (!$response->success()) {
        throw new Exception("Erreur Mailjet : " . json_encode($response->getData()));
    }

    file_put_contents($logFile, "Mail envoyé avec succès.\n", FILE_APPEND);

} catch (Exception $e) {
    file_put_contents($logFile, "Erreur : " . $e->getMessage() . "\n", FILE_APPEND);
    echo "Une erreur est survenue : " . e($e->getMessage());
}

file_put_contents($logFile, "=== Fin du script PHP ===\n", FILE_APPEND);
?>
