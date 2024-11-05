<?php

//require 'vendor/autoload.php';
//use Invoiced\Client;

//$api_key = getenv('INVOICED_API_KEY');
//$invoiced = new Client($api_key);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Chemin vers le fichier de log
$logFile = 'devis.log';  
error_log("Début du script PHP\n", 3, $logFile);

// Récupération des données du formulaire
$nom = $_POST['name'] ?? ''; 
$email = $_POST['email'] ?? '';
$telephone = $_POST['phone'] ?? '';
$adresse = $_POST['add'] ?? '';
$raisonSociale = $_POST['raison'] ?? '';
$siret = $_POST['siret'] ?? '';
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

// Tableau de mapping entre les IDs de produits et ceux du formulaire
$productMapping = [
    'palette_660l_papiers'=> 'field21',
    'bac_roulant_770l_papiers'=> 'field22',
    'benne_10m3_papiers'=> 'field23',
    'benne_15m3_papiers'=> 'field24',
    'benne_30m3_papiers'=> 'field25',
    'palette_660l_cartons'=> 'field27',
    'bac_roulant_770l_cartons'=> 'field28',
    'benne_10m3_cartons'=> 'field29',
    'benne_15m3_cartons'=> 'field30',
    'benne_30m3_cartons'=> 'field31',
    'palette_660l_plastiques'=> 'field33',
    'bac_roulant_770l_plastiques'=> 'field34',
    'benne_10m3_plastiques'=> 'field35',
    'benne_15m3_plastiques'=> 'field36',
    'benne_30m3_plastiques'=> 'field37',
    'benne_10m3_palettes'=> 'field41',
    'benne_15m3_palettes'=> 'field42',
    'benne_30m3_palettes'=> 'field43',
    'benne_10m3_encombrants'=> 'field47',
    'benne_15m3_encombrants'=> 'field48',
    'benne_30m3_encombrants'=> 'field49',
    'benne_10m3_dib'=> 'field53',
    'benne_15m3_dib'=> 'field54',
    'benne_30m3_dib'=> 'field55',
    'palette_660l_bois'=> 'field57',
    'benne_10m3_bois'=> 'field59',
    'benne_15m3_bois'=> 'field60',
    'benne_30m3_bois'=> 'field61',
    'palette_660l_ferrailles'=> 'field63',
    'benne_10m3_ferrailles'=> 'field65',
    'benne_15m3_ferrailles'=> 'field66',
    'benne_30m3_ferrailles'=> 'field67',
    'benne_10m3_dechets_verts'=> 'field71',
    'benne_15m3_dechets_verts'=> 'field72',
    'benne_30m3_dechets_verts'=> 'field73',
    'big_bag_1m3_dib_chantier'=> 'field88',
    'benne_ampliroll_3m3_dib_chantier'=> 'field89',
    'benne_chaine_8m3_dib_chantier'=> 'field90',
    'benne_chaine_15m3_dib_chantier'=> 'field91',
    'benne_ampliroll_30m3_dib_chantier'=> 'field93',
    'big_bag_1m3_gravats_melange_chantier'=> 'field96',
    'benne_ampliroll_3m3_gravats_melange_chantier'=> 'field97',
    'benne_chaine_8m3_gravats_melange_chantier'=> 'field95',
    'big_bag_1m3_gravats_propres_chantier'=> 'field99',
    'benne_ampliroll_3m3_gravats_propres_chantier'=> 'field100',
    'benne_chaine_8m3_gravats_propres_chantier'=> 'field101',
    'big_bag_1m3_bois_chantier'=> 'field103',
    'benne_chaine_3m3_dib_chantier'=> 'field104',
    'benne_chaine_8m3_bois_chantier'=> 'field105',
    'benne_chaine_15m3_bois_chantier'=> 'field106',
    ''=> 'field107',
    'benne_ampliroll_30m3_bois_chantier'=> 'field108',
    'big_bag_1m3_platre_chantier'=> 'field109',
    'benne_chaine_8m3_platre_chantier'=> 'field110',
    'box_70L_archives_domicile'=> 'field134',
    'box_240L_archives_domicile'=> 'field135',
    'box_480L_archives_domicile'=> 'field136',
    'benne_8m3_gravats'=> 'field157',
    'benne_8m3_dechets'=> 'field158',
    'benne_10m3_dechets'=> 'field159',
    'benne_15m3_dechets'=> 'field160',
    'benne_30m3_dechets'=> 'field161',
    'box_d3e'=> 'field178',
    'caisse_palette'=> 'field179',
    'bac_550l'=> 'field180',
    'box_ampoules'=> 'field181',
    'box_neons'=> 'field182',
    'bac_550l_ampoules'=> 'field183',
    'box_cartouche'=> 'field184',
    'bac_550l_cartouche'=> 'field185',
    'bac_pile_6kg'=> 'field186',
    'bac_pile_15kg'=> 'field187',
    'box_70L_archives_depot'=> 'field189',
    'box_240L_archives_depot'=> 'field190',
    'box_480L_archives_depot'=> 'field191',
    'machine_laver'=> 'field192',
    'refrigerateur'=> 'field193',
    'four'=> 'field194',
    'cuisiniere'=> 'field195',
    'box_130L_archives_depot'=> 'field196',
    'box_130L_archives_domicile'=> 'field197',
    'etage1_avec_ascenseur_camion1' => 'field200',
    'etage1_sans_ascenseur_camion1' => 'field201',
    'etage1_avec_ascenseur_camion2' => 'field202',
    'etage1_sans_ascenseur_camion2' => 'field203',
    'etage1_avec_ascenseur_camion3' => 'field204',
    'etage1_sans_ascenseur_camion3' => 'field205',
    'etage1_avec_ascenseur_camion4' => 'field206',
    'etage1_sans_ascenseur_camion4' => 'field207',
    'etage1_avec_ascenseur_camion5' => 'field208',
    'etage1_sans_ascenseur_camion5' => 'field209',
    'etage1_avec_ascenseur_camion6' => 'field210',
    'etage1_sans_ascenseur_camion6' => 'field211',

    'etage2_avec_ascenseur_camion1' => 'field212',
    'etage2_sans_ascenseur_camion1' => 'field213',
    'etage2_avec_ascenseur_camion2' => 'field214',
    'etage2_sans_ascenseur_camion2' => 'field215',
    'etage2_avec_ascenseur_camion3' => 'field216',
    'etage2_sans_ascenseur_camion3' => 'field217',
    'etage2_avec_ascenseur_camion4' => 'field218',
    'etage2_sans_ascenseur_camion4' => 'field219',
    'etage2_avec_ascenseur_camion5' => 'field220',
    'etage2_sans_ascenseur_camion5' => 'field221',
    'etage2_avec_ascenseur_camion6' => 'field222',
    'etage2_sans_ascenseur_camion6' => 'field223',

    'etage3_avec_ascenseur_camion1' => 'field224',
    'etage3_sans_ascenseur_camion1' => 'field225',
    'etage3_avec_ascenseur_camion2' => 'field226',
    'etage3_sans_ascenseur_camion2' => 'field227',
    'etage3_avec_ascenseur_camion3' => 'field228',
    'etage3_sans_ascenseur_camion3' => 'field229',
    'etage3_avec_ascenseur_camion4' => 'field230',
    'etage3_sans_ascenseur_camion4' => 'field231',
    'etage3_avec_ascenseur_camion5' => 'field232',
    'etage3_sans_ascenseur_camion5' => 'field233',
    'etage3_avec_ascenseur_camion6' => 'field234',
    'etage3_sans_ascenseur_camion6' => 'field235',

    'etage4_avec_ascenseur_camion1' => 'field236',
    'etage4_sans_ascenseur_camion1' => 'field237',
    'etage4_avec_ascenseur_camion2' => 'field238',
    'etage4_sans_ascenseur_camion2' => 'field239',
    'etage4_avec_ascenseur_camion3' => 'field240',
    'etage4_sans_ascenseur_camion3' => 'field241',
    'etage4_avec_ascenseur_camion4' => 'field242',
    'etage4_sans_ascenseur_camion4' => 'field243',
    'etage4_avec_ascenseur_camion5' => 'field244',
    'etage4_sans_ascenseur_camion5' => 'field245',
    'etage4_avec_ascenseur_camion6' => 'field246',
    'etage4_sans_ascenseur_camion6' => 'field247',

    'etage5_avec_ascenseur_camion1' => 'field248',
    'etage5_sans_ascenseur_camion1' => 'field249',
    'etage5_avec_ascenseur_camion2' => 'field250',
    'etage5_sans_ascenseur_camion2' => 'field251',
    'etage5_avec_ascenseur_camion3' => 'field252',
    'etage5_sans_ascenseur_camion3' => 'field253',
    'etage5_avec_ascenseur_camion4' => 'field254',
    'etage5_sans_ascenseur_camion4' => 'field255',
    'etage5_avec_ascenseur_camion5' => 'field256',
    'etage5_sans_ascenseur_camion5' => 'field257',
    'etage5_avec_ascenseur_camion6' => 'field258',
    'etage5_sans_ascenseur_camion6' => 'field259',

    'etage6_avec_ascenseur_camion1' => 'field260',
    'etage6_sans_ascenseur_camion1' => 'field261',
    'etage6_avec_ascenseur_camion2' => 'field262',
    'etage6_sans_ascenseur_camion2' => 'field263',
    'etage6_avec_ascenseur_camion3' => 'field264',
    'etage6_sans_ascenseur_camion3' => 'field265',
    'etage6_avec_ascenseur_camion4' => 'field266',
    'etage6_sans_ascenseur_camion4' => 'field267',
    'etage6_avec_ascenseur_camion5' => 'field268',
    'etage6_sans_ascenseur_camion5' => 'field269',
    'etage6_avec_ascenseur_camion6' => 'field270',
    'etage6_sans_ascenseur_camion6' => 'field271',

    // Sous-sol et RDC

    'soussol1_avec_ascenseur_camion1' => 'field272',
    'soussol1_sans_ascenseur_camion1' => 'field273',
    'soussol1_avec_ascenseur_camion2' => 'field274',
    'soussol1_sans_ascenseur_camion2' => 'field275',
    'soussol1_avec_ascenseur_camion3' => 'field276',
    'soussol1_sans_ascenseur_camion3' => 'field277',
    'soussol1_avec_ascenseur_camion4' => 'field278',
    'soussol1_sans_ascenseur_camion4' => 'field279',
    'soussol1_avec_ascenseur_camion5' => 'field280',
    'soussol1_sans_ascenseur_camion5' => 'field281',
    'soussol1_avec_ascenseur_camion6' => 'field282',
    'soussol1_sans_ascenseur_camion6' => 'field283',

    'soussol2_avec_ascenseur_camion1' => 'field284',
    'soussol2_sans_ascenseur_camion1' => 'field285',
    'soussol2_avec_ascenseur_camion2' => 'field286',
    'soussol2_sans_ascenseur_camion2' => 'field287',
    'soussol2_avec_ascenseur_camion3' => 'field288',
    'soussol2_sans_ascenseur_camion3' => 'field289',
    'soussol2_avec_ascenseur_camion4' => 'field290',
    'soussol2_sans_ascenseur_camion4' => 'field291',
    'soussol2_avec_ascenseur_camion5' => 'field292',
    'soussol2_sans_ascenseur_camion5' => 'field293',
    'soussol2_avec_ascenseur_camion6' => 'field294',
    'soussol2_sans_ascenseur_camion6' => 'field295',

    'etageRDC_avec_ascenseur_camion1' => 'field296',
    'etageRDC_sans_ascenseur_camion1' => 'field297',
    'etageRDC_avec_ascenseur_camion2' => 'field298',
    'etageRDC_sans_ascenseur_camion2' => 'field299',
    'etageRDC_avec_ascenseur_camion3' => 'field300',
    'etageRDC_sans_ascenseur_camion3' => 'field301',
    'etageRDC_avec_ascenseur_camion4' => 'field302',
    'etageRDC_sans_ascenseur_camion4' => 'field303',
    'etageRDC_avec_ascenseur_camion5' => 'field304',
    'etageRDC_sans_ascenseur_camion5' => 'field305',
    'etageRDC_avec_ascenseur_camion6' => 'field306',
    'etageRDC_sans_ascenseur_camion6' => 'field307',

    'vrac_domicile_estimer_archives' => 'field321',
    'vrac_entrepot_estimer_archives' => 'field322',

    // Déchets de bureau 
    'box_gobelet' => 'field308',
    'box_capsule' => 'field309',
    'box_biodechets_hebdo' => 'field310',
    'box_biodechets_2semaines' => 'field311',
    'box_piles' => 'field312',
    'box_bouteilles_canettes' => 'field313',
    'box_papier' => 'field314',
    'lot_5_box_papier' => 'field315',
    'box_d3e' => 'field316',
    'box_secure_90' => 'field317',
    'box_secure_120' => 'field318',
    'box_secure_240' => 'field319',
    'box_secure_480' => 'field320',

];

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
            // Ajouter uniquement si la quantité est supérieure à 0
            $produits_commandes[] = [
                'id' => $productId,
                'name' => $productName,
                'quantity' => $quantite
            ];
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
        } elseif ($archiveType === 'vrac_entrepot_estimer_archives') {
            $produits_commandes[] = [
                'id' => $productMapping['vrac_entrepot_estimer_archives'],
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

// Création du client
$customerData = [
    'name' => $nom,
    'email' => $email,
    'address1' => $adresse,
    'phone' => $telephone,
    'attention_to' => $raisonSociale,
    'siret' => $siret,
    'payment_terms' => "NET 30",
    'language' => "fr"
];

// Log de la requête de création du client
error_log("=== Requête de création du client ===\n", 3, $logFile);
error_log(json_encode($customerData, JSON_PRETTY_PRINT) . "\n", 3, $logFile);

//Création du devis
$produits = [];
foreach ($produits_commandes as $produit) {
    $produits[] = [
        'catalog_item' => $produit['id'],
        'quantity' => $produit['quantity']
    ];
}

$estimateData = [
    'customer' => 'CUSTOMER_ID_PLACEHOLDER',  
    'items' => $produits
];

// Log de la requête de création du devis
error_log("=== Requête de création du devis ===\n", 3, $logFile);
error_log(json_encode($estimateData, JSON_PRETTY_PRINT) . "\n", 3, $logFile);


//Envoi devis par Email
$sendEstimateLog = [
    'estimate_id' => 'ESTIMATE_ID_PLACEHOLDER'  
];

// Log de la requête d'envoi du devis
error_log("=== Requête d'envoi du devis ===\n", 3, $logFile);
error_log(json_encode($sendEstimateLog, JSON_PRETTY_PRINT) . "\n", 3, $logFile);

// Appel de email.php pour envoyer l'email
require 'email.php';

?>
