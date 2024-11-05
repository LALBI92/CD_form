

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fonction de log pour enregistrer des messages
function writeLog($message) {
    $logFile = 'devis.log';
    $date = date('Y-m-d H:i:s');
    $logMessage = "[$date] $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Connexion à la base de données
$host = '127.0.0.1';
$port = 8889;
$db = 'mysql'; 
$user = 'root';
$pass = 'root';
$socket = '/Applications/MAMP/tmp/mysql/mysql.sock';

// Créer une connexion
$conn = new mysqli($host, $user, $pass, $db, $port, $socket);

// Vérifiez la connexion
if ($conn->connect_error) {
    writeLog("La connexion a échoué: " . $conn->connect_error);
    die("La connexion a échoué: " . $conn->connect_error);
}
writeLog("Connexion à la base de données réussie.");



// STEP 2 Récupérer les données du formulaire
$nom = $_POST['name'];
$email = $_POST['email'];
$telephone = $_POST['phone'];
$adresse = $_POST['add'];
$description = $_POST['description'];
$profession = $_POST['profession'];
$siret = !empty($_POST['siret']) ? $_POST['siret'] : null;
$raison = !empty($_POST['raison']) ? $_POST['raison'] : null;

// DND Contenants Cartons
$benne_10m3_cartons_qty = isset($_POST['benne_10m3_cartons_qty']) ? $_POST['benne_10m3_cartons_qty'] : 0;
$benne_15m3_cartons_qty = isset($_POST['benne_15m3_cartons_qty']) ? $_POST['benne_15m3_cartons_qty'] : 0;
$benne_30m3_cartons_qty = isset($_POST['benne_30m3_cartons_qty']) ? $_POST['benne_30m3_cartons_qty'] : 0;
$benne_palette_660l_cartons_qty = isset($_POST['palette_660l_cartons_qty']) ? $_POST['palette_660l_cartons_qty'] : 0;
$benne_bac_roulant_770l_cartons_qty = isset($_POST['bac_roulant_770l_cartons_qty']) ? $_POST['bac_roulant_770l_cartons_qty'] : 0;

// DND Contenants Papiers
$benne_10m3_papiers_qty = isset($_POST['benne_10m3_papiers_qty']) ? $_POST['benne_10m3_papiers_qty'] : 0;
$benne_15m3_papiers_qty = isset($_POST['benne_15m3_papiers_qty']) ? $_POST['benne_15m3_papiers_qty'] : 0;
$benne_30m3_papiers_qty = isset($_POST['benne_30m3_papiers_qty']) ? $_POST['benne_30m3_papiers_qty'] : 0;
$benne_palette_660l_papiers_qty = isset($_POST['palette_660l_papiers_qty']) ? $_POST['palette_660l_papiers_qty'] : 0;
$benne_bac_roulant_770l_papiers_qty = isset($_POST['bac_roulant_770l_papiers_qty']) ? $_POST['bac_roulant_770l_papiers_qty'] : 0;

// DND Contenants Plastiques
$benne_10m3_plastiques_qty = isset($_POST['benne_10m3_plastiques_qty']) ? $_POST['benne_10m3_plastiques_qty'] : 0;
$benne_15m3_plastiques_qty = isset($_POST['benne_15m3_plastiques_qty']) ? $_POST['benne_15m3_plastiques_qty'] : 0;
$benne_30m3_plastiques_qty = isset($_POST['benne_30m3_plastiques_qty']) ? $_POST['benne_30m3_plastiques_qty'] : 0;
$benne_palette_660l_plastiques_qty = isset($_POST['palette_660l_plastiques_qty']) ? $_POST['palette_660l_plastiques_qty'] : 0;

// DND Contenants Palettes
$benne_10m3_palettes_qty = isset($_POST['benne_10m3_palettes_qty']) ? $_POST['benne_10m3_palettes_qty'] : 0;
$benne_15m3_palettes_qty = isset($_POST['benne_15m3_palettes_qty']) ? $_POST['benne_15m3_palettes_qty'] : 0;
$benne_30m3_palettes_qty = isset($_POST['benne_30m3_palettes_qty']) ? $_POST['benne_30m3_palettes_qty'] : 0;

// DND Contenants Encombrants
$benne_10m3_encombrants_qty = isset($_POST['benne_10m3_encombrants_qty']) ? $_POST['benne_10m3_encombrants_qty'] : 0;
$benne_15m3_encombrants_qty = isset($_POST['benne_15m3_encombrants_qty']) ? $_POST['benne_15m3_encombrants_qty'] : 0;
$benne_30m3_encombrants_qty = isset($_POST['benne_30m3_encombrants_qty']) ? $_POST['benne_30m3_encombrants_qty'] : 0;

// DND Contenants DIB
$benne_10m3_dib_qty = isset($_POST['benne_10m3_dib_qty']) ? $_POST['benne_10m3_dib_qty'] : 0;
$benne_15m3_dib_qty = isset($_POST['benne_15m3_dib_qty']) ? $_POST['benne_15m3_dib_qty'] : 0;
$benne_30m3_dib_qty = isset($_POST['benne_30m3_dib_qty']) ? $_POST['benne_30m3_dib_qty'] : 0;

// DND Contenants Bois
$benne_10m3_bois_qty = isset($_POST['benne_10m3_bois_qty']) ? $_POST['benne_10m3_bois_qty'] : 0;
$benne_15m3_bois_qty = isset($_POST['benne_15m3_bois_qty']) ? $_POST['benne_15m3_bois_qty'] : 0;
$benne_30m3_bois_qty = isset($_POST['benne_30m3_bois_qty']) ? $_POST['benne_30m3_bois_qty'] : 0;
$benne_palette_660l_bois_qty = isset($_POST['palette_660l_bois_qty']) ? $_POST['palette_660l_bois_qty'] : 0;

// DND Contenants Ferrailles
$benne_10m3_ferrailles_qty = isset($_POST['benne_10m3_ferrailles_qty']) ? $_POST['benne_10m3_ferrailles_qty'] : 0;
$benne_15m3_ferrailles_qty = isset($_POST['benne_15m3_ferrailles_qty']) ? $_POST['benne_15m3_ferrailles_qty'] : 0;
$benne_30m3_ferrailles_qty = isset($_POST['benne_30m3_ferrailles_qty']) ? $_POST['benne_30m3_ferrailles_qty'] : 0;
$benne_palette_660l_ferrailles_qty = isset($_POST['palette_660l_ferrailles_qty']) ? $_POST['palette_660l_ferrailles_qty'] : 0;

// DND Contenants Déchets Verts
$benne_10m3_dechets_verts_qty = isset($_POST['benne_10m3_dechets_verts_qty']) ? $_POST['benne_10m3_dechets_verts_qty'] : 0;
$benne_15m3_dechets_verts_qty = isset($_POST['benne_15m3_dechets_verts_qty']) ? $_POST['benne_15m3_dechets_verts_qty'] : 0;
$benne_30m3_dechets_verts_qty = isset($_POST['benne_30m3_dechets_verts_qty']) ? $_POST['benne_30m3_dechets_verts_qty'] : 0;


// Dechets chantiers
$big_bag_1m3_qty = isset($_POST['big_bag_1m3_qty']) ? $_POST['big_bag_1m3_qty'] : 0;
$benne_chaine_3m3_qty = isset($_POST['benne_chaine_3m3_qty']) ? $_POST['benne_chaine_3m3_qty'] : 0;
$benne_chaine_8m3_qty = isset($_POST['benne_chaine_8m3_qty']) ? $_POST['benne_chaine_8m3_qty'] : 0;
$benne_chaine_15m3_qty = isset($_POST['benne_chaine_15m3_qty']) ? $_POST['benne_chaine_15m3_qty'] : 0;
$benne_ampliroll_30m3_qty_1 = isset($_POST['benne_ampliroll_30m3_qty_1']) ? $_POST['benne_ampliroll_30m3_qty_1'] : 0;
$big_bag_1m3_qty_bois = isset($_POST['big_bag_1m3_qty_bois']) ? $_POST['big_bag_1m3_qty_bois'] : 0;
$benne_ampliroll_3m3_qty_bois = isset($_POST['benne_ampliroll_3m3_qty_bois']) ? $_POST['benne_ampliroll_3m3_qty_bois'] : 0;
$benne_chaine_8m3_qty_bois = isset($_POST['benne_chaine_8m3_qty_bois']) ? $_POST['benne_chaine_8m3_qty_bois'] : 0;
$benne_chaine_15m3_qty_bois = isset($_POST['benne_chaine_15m3_qty_bois']) ? $_POST['benne_chaine_15m3_qty_bois'] : 0;
$benne_ampliroll_30m3_qty_bois = isset($_POST['benne_ampliroll_30m3_qty_bois']) ? $_POST['benne_ampliroll_30m3_qty_bois'] : 0;
$big_bag_1m3_qty_platre = isset($_POST['big_bag_1m3_qty_platre']) ? $_POST['big_bag_1m3_qty_platre'] : 0;
$benne_chaine_8m3_qty_platre = isset($_POST['benne_chaine_8m3_qty_platre']) ? $_POST['benne_chaine_8m3_qty_platre'] : 0;
$big_bag_1m3_qty_gravats_melange = isset($_POST['big_bag_1m3_qty_gravats_melange']) ? $_POST['big_bag_1m3_qty_gravats_melange'] : 0;
$benne_ampliroll_3m3_qty_gravats_melange = isset($_POST['benne_ampliroll_3m3_qty_gravats_melange']) ? $_POST['benne_ampliroll_3m3_qty_gravats_melange'] : 0;
$benne_chaine_8m3_qty_gravats_melange = isset($_POST['benne_chaine_8m3_qty_gravats_melange']) ? $_POST['benne_chaine_8m3_qty_gravats_melange'] : 0;
$big_bag_1m3_qty_gravats_propres = isset($_POST['big_bag_1m3_qty_gravats_propres']) ? $_POST['big_bag_1m3_qty_gravats_propres'] : 0;
$benne_ampliroll_3m3_qty_gravats_propres = isset($_POST['benne_ampliroll_3m3_qty_gravats_propres']) ? $_POST['benne_ampliroll_3m3_qty_gravats_propres'] : 0;
$benne_chaine_8m3_qty_gravats_propres = isset($_POST['benne_chaine_8m3_qty_gravats_propres']) ? $_POST['benne_chaine_8m3_qty_gravats_propres'] : 0;

// DEEE
$box_d3e_qty = isset($_POST['box_d3e_qty']) ? $_POST['box_d3e_qty'] : 0;
$bac_550l_qty = isset($_POST['bac_550l_qty']) ? $_POST['bac_550l_qty'] : 0;
$caisse_palette_qty = isset($_POST['caisse_palette_qty']) ? $_POST['caisse_palette_qty'] : 0;
$box_cartouche_qty = isset($_POST['box_cartouche_qty']) ? $_POST['box_cartouche_qty'] : 0;
$bac_550l_cartouche_qty = isset($_POST['bac_550l_cartouche_qty']) ? $_POST['bac_550l_cartouche_qty'] : 0;
$bac_pile_6kg_qty = isset($_POST['bac_pile_6kg_qty']) ? $_POST['bac_pile_6kg_qty'] : 0;
$bac_pile_15kg_qty = isset($_POST['bac_pile_15kg_qty']) ? $_POST['bac_pile_15kg_qty'] : 0;
$machine_laver_qty = isset($_POST['machine_laver_qty']) ? $_POST['machine_laver_qty'] : 0;
$refrigerateur_qty = isset($_POST['refrigerateur_qty']) ? $_POST['refrigerateur_qty'] : 0;
$four_qty = isset($_POST['four_qty']) ? $_POST['four_qty'] : 0;
$cuisiniere_qty = isset($_POST['cuisiniere_qty']) ? $_POST['cuisiniere_qty'] : 0;
$box_ampoules_qty = isset($_POST['box_ampoules_qty']) ? $_POST['box_ampoules_qty'] : 0;
$box_neons_qty = isset($_POST['box_neons_qty']) ? $_POST['box_neons_qty'] : 0;
$bac_550l_ampoules_qty = isset($_POST['bac_550l_ampoules_qty']) ? $_POST['bac_550l_ampoules_qty'] : 0;

// Archives
$box_70L_archives_domicile_qty = isset($_POST['box_70L_archives_domicile_qty']) ? $_POST['box_70L_archives_domicile_qty'] : 0;
$box_130L_archives_domicile_qty = isset($_POST['box_130L_archives_domicile_qty']) ? $_POST['box_130L_archives_domicile_qty'] : 0;
$box_240L_archives_domicile_qty = isset($_POST['box_240L_archives_domicile_qty']) ? $_POST['box_240L_archives_domicile_qty'] : 0;
$box_480L_archives_domicile_qty = isset($_POST['box_480L_archives_domicile_qty']) ? $_POST['box_480L_archives_domicile_qty'] : 0;
$box_70L_archives_depot_qty = isset($_POST['box_70L_archives_depot_qty']) ? $_POST['box_70L_archives_depot_qty'] : 0;
$box_130L_archives_depot_qty = isset($_POST['box_130L_archives_depot_qty']) ? $_POST['box_130L_archives_depot_qty'] : 0;
$box_240L_archives_depot_qty = isset($_POST['box_240L_archives_depot_qty']) ? $_POST['box_240L_archives_depot_qty'] : 0;
$box_480L_archives_depot_qty = isset($_POST['box_480L_archives_depot_qty']) ? $_POST['box_480L_archives_depot_qty'] : 0;
$vrac_estimer_archives = isset($_POST['destruction_archives']) && in_array('vrac_estimer_archives', $_POST['destruction_archives']) ? 'checked' : '';

// Location Bennes
$benne_8m3_gravats_qty = isset($_POST['benne_8m3_gravats_qty']) ? $_POST['benne_8m3_gravats_qty'] : 0;
$benne_8m3_dechets_qty = isset($_POST['benne_8m3_dechets_qty']) ? $_POST['benne_8m3_dechets_qty'] : 0;
$benne_10m3_dechets_qty = isset($_POST['benne_10m3_dechets_qty']) ? $_POST['benne_10m3_dechets_qty'] : 0;
$benne_15m3_dechets_qty = isset($_POST['benne_15m3_dechets_qty']) ? $_POST['benne_15m3_dechets_qty'] : 0;
$benne_30m3_dechets_qty = isset($_POST['benne_30m3_dechets_qty']) ? $_POST['benne_30m3_dechets_qty'] : 0;

$camion_selectionne = isset($_POST['camion_selectionne']) ? $_POST['camion_selectionne'] : null; // Gérer le cas où aucun camion n'est sélectionné

// Vérification et gestion des fichiers uploadés
if (isset($_FILES['file-upload']['name']) && $_FILES['file-upload']['error'][0] === UPLOAD_ERR_NO_FILE) {
    writeLog("Aucun fichier n'a été uploadé.");
    $fileNames = ''; // Aucun fichier à stocker
} else {
    $files = isset($_FILES['file-upload']['name']) ? $_FILES['file-upload']['name'] : []; 
    $fileNames = is_array($files) ? implode(',', $files) : '';
    writeLog("Fichiers uploadés : $fileNames");
}

writeLog("Données récupérées du formulaire : 
    Nom = $nom, 
    Email = $email, 
    Téléphone = $telephone, 
    Adresse = $adresse, 
    Description = $description, 
    Profession = $profession, 
    SIRET = $siret, 
    Raison sociale = $raison, 
    Camion sélectionné = $camion_selectionne, 
    
    DND = {Benne 10m³ Cartons = $benne_10m3_cartons_qty, Benne 15m³ Cartons = $benne_15m3_cartons_qty, Benne 30m³ Cartons = $benne_30m3_cartons_qty, 
    Papiers (10m³) = $benne_10m3_papiers_qty, Papiers (15m³) = $benne_15m3_papiers_qty, Papiers (30m³) = $benne_30m3_papiers_qty, 
    Plastiques (10m³) = $benne_10m3_plastiques_qty, Plastiques (15m³) = $benne_15m3_plastiques_qty, Plastiques (30m³) = $benne_30m3_plastiques_qty, 
    Palettes (10m³) = $benne_10m3_palettes_qty, Palettes (15m³) = $benne_15m3_palettes_qty, Palettes (30m³) = $benne_30m3_palettes_qty, 
    Encombrants (10m³) = $benne_10m3_encombrants_qty, Encombrants (15m³) = $benne_15m3_encombrants_qty, Encombrants (30m³) = $benne_30m3_encombrants_qty}, 
    
    Déchets Chantiers = {Big Bag 1m³ = $big_bag_1m3_qty, Benne à chaîne 3m³ = $benne_chaine_3m3_qty, Benne à chaîne 8m³ = $benne_chaine_8m3_qty, 
    Benne à chaîne 15m³ = $benne_chaine_15m3_qty, Benne Ampliroll 30m³ = $benne_ampliroll_30m3_qty_1, Bois (Big Bag 1m³) = $big_bag_1m3_qty_bois, 
    Benne Ampliroll 3m³ Bois = $benne_ampliroll_3m3_qty_bois, Benne à chaîne 8m³ Bois = $benne_chaine_8m3_qty_bois, Benne à chaîne 15m³ Bois = $benne_chaine_15m3_qty_bois, 
    Benne Ampliroll 30m³ Bois = $benne_ampliroll_30m3_qty_bois, Plâtre (Big Bag 1m³) = $big_bag_1m3_qty_platre, Benne à chaîne 8m³ Plâtre = $benne_chaine_8m3_qty_platre, 
    Gravats Mélangés (Big Bag 1m³) = $big_bag_1m3_qty_gravats_melange, Benne Ampliroll 3m³ Gravats Mélangés = $benne_ampliroll_3m3_qty_gravats_melange, 
    Benne à chaîne 8m³ Gravats Mélangés = $benne_chaine_8m3_qty_gravats_melange, Gravats Propres (Big Bag 1m³) = $big_bag_1m3_qty_gravats_propres, 
    Benne Ampliroll 3m³ Gravats Propres = $benne_ampliroll_3m3_qty_gravats_propres, Benne à chaîne 8m³ Gravats Propres = $benne_chaine_8m3_qty_gravats_propres}, 
    
    DEEE = {Box D3E = $box_d3e_qty, Bac 550L = $bac_550l_qty, Caisse Palette = $caisse_palette_qty, Box Cartouche = $box_cartouche_qty, 
    Bac 550L Cartouche = $bac_550l_cartouche_qty, Bac Pile 6kg = $bac_pile_6kg_qty, Bac Pile 15kg = $bac_pile_15kg_qty, 
    Machine à laver = $machine_laver_qty, Réfrigérateur = $refrigerateur_qty, Four = $four_qty, Cuisinière = $cuisiniere_qty, 
    Box Ampoules = $box_ampoules_qty, Box Néons = $box_neons_qty, Bac 550L Ampoules = $bac_550l_ampoules_qty}, 
    
    Archives = {Box 70L = $box_70L_archives_qty, Box 130L = $box_130L_archives_qty, Box 240L = $box_240L_archives_qty, Box 480L = $box_480L_archives_qty, Vrac à Estimer = $vrac_estimer_archives}, 
    
    Location de Bennes = {Benne 8m³ Gravats = $benne_8m3_gravats_qty, Benne 8m³ Déchets = $benne_8m3_dechets_qty, 
    Benne 10m³ Déchets = $benne_10m3_dechets_qty, Benne 15m³ Déchets = $benne_15m3_dechets_qty, Benne 30m³ Déchets = $benne_30m3_dechets_qty}.");
    

    $columns = [
        'nom', 'email', 'telephone', 'adresse', 'description', 'profession', 'siret', 'raison', 'file_names', 
        'benne_10m3_cartons_qty', 'benne_15m3_cartons_qty', 'benne_30m3_cartons_qty', 'benne_10m3_papiers_qty', 
        'benne_15m3_papiers_qty', 'benne_30m3_papiers_qty', 'benne_10m3_plastiques_qty', 'benne_15m3_plastiques_qty', 
        'benne_30m3_plastiques_qty', 'benne_10m3_palettes_qty', 'benne_15m3_palettes_qty', 'benne_30m3_palettes_qty', 
        'benne_10m3_encombrants_qty', 'benne_15m3_encombrants_qty', 'benne_30m3_encombrants_qty', 'big_bag_1m3_qty', 
        'benne_chaine_3m3_qty', 'benne_chaine_8m3_qty', 'benne_chaine_15m3_qty', 'benne_ampliroll_30m3_qty_1', 
        'big_bag_1m3_qty_bois', 'benne_ampliroll_3m3_qty_bois', 'benne_chaine_8m3_qty_bois', 'benne_chaine_15m3_qty_bois', 
        'benne_ampliroll_30m3_qty_bois', 'big_bag_1m3_qty_platre', 'benne_chaine_8m3_qty_platre', 
        'big_bag_1m3_qty_gravats_melange', 'benne_ampliroll_3m3_qty_gravats_melange', 'benne_chaine_8m3_qty_gravats_melange', 
        'big_bag_1m3_qty_gravats_propres', 'benne_ampliroll_3m3_qty_gravats_propres', 'benne_chaine_8m3_qty_gravats_propres', 
        'box_d3e_qty', 'bac_550l_qty', 'caisse_palette_qty', 'box_cartouche_qty', 'bac_550l_cartouche_qty', 
        'bac_pile_6kg_qty', 'bac_pile_15kg_qty', 'machine_laver_qty', 'refrigerateur_qty', 'four_qty', 
        'cuisiniere_qty', 'box_ampoules_qty', 'box_neons_qty', 'bac_550l_ampoules_qty', 'box_70L_archives_qty', 
        'box_130L_archives_qty', 'box_240L_archives_qty', 'box_480L_archives_qty', 'vrac_estimer_archives', 
        'benne_8m3_gravats_qty', 'benne_8m3_dechets_qty', 'benne_10m3_dechets_qty', 'benne_15m3_dechets_qty', 
        'benne_30m3_dechets_qty', 'camion_selectionne'
    ];
    
    $values = [
        $nom, $email, $telephone, $adresse, $description, $profession, $siret, $raison, $fileNames, 
        $benne_10m3_cartons_qty, $benne_15m3_cartons_qty, $benne_30m3_cartons_qty, $benne_10m3_papiers_qty, 
        $benne_15m3_papiers_qty, $benne_30m3_papiers_qty, $benne_10m3_plastiques_qty, $benne_15m3_plastiques_qty, 
        $benne_30m3_plastiques_qty, $benne_10m3_palettes_qty, $benne_15m3_palettes_qty, $benne_30m3_palettes_qty, 
        $benne_10m3_encombrants_qty, $benne_15m3_encombrants_qty, $benne_30m3_encombrants_qty, $big_bag_1m3_qty, 
        $benne_chaine_3m3_qty, $benne_chaine_8m3_qty, $benne_chaine_15m3_qty, $benne_ampliroll_30m3_qty_1, 
        $big_bag_1m3_qty_bois, $benne_ampliroll_3m3_qty_bois, $benne_chaine_8m3_qty_bois, $benne_chaine_15m3_qty_bois, 
        $benne_ampliroll_30m3_qty_bois, $big_bag_1m3_qty_platre, $benne_chaine_8m3_qty_platre, 
        $big_bag_1m3_qty_gravats_melange, $benne_ampliroll_3m3_qty_gravats_melange, $benne_chaine_8m3_qty_gravats_melange, 
        $big_bag_1m3_qty_gravats_propres, $benne_ampliroll_3m3_qty_gravats_propres, $benne_chaine_8m3_qty_gravats_propres, 
        $box_d3e_qty, $bac_550l_qty, $caisse_palette_qty, $box_cartouche_qty, $bac_550l_cartouche_qty, $bac_pile_6kg_qty, 
        $bac_pile_15kg_qty, $machine_laver_qty, $refrigerateur_qty, $four_qty, $cuisiniere_qty, $box_ampoules_qty, 
        $box_neons_qty, $bac_550l_ampoules_qty, $box_70L_archives_qty, $box_130L_archives_qty, 
        $box_240L_archives_qty, $box_480L_archives_qty, $vrac_estimer_archives, $benne_8m3_gravats_qty, 
        $benne_8m3_dechets_qty, $benne_10m3_dechets_qty, $benne_15m3_dechets_qty, $benne_30m3_dechets_qty, 
        $camion_selectionne
    ];
    
    writeLog("Nombre de colonnes : " . count($columns));
    writeLog("Nombre de valeurs : " . count($values));
    


$sql = "INSERT INTO devis (
    nom, email, telephone, adresse, description, profession, siret, raison, file_names, 
    benne_10m3_cartons_qty, benne_15m3_cartons_qty, benne_30m3_cartons_qty, benne_10m3_papiers_qty, 
    benne_15m3_papiers_qty, benne_30m3_papiers_qty, benne_10m3_plastiques_qty, benne_15m3_plastiques_qty, 
    benne_30m3_plastiques_qty, benne_10m3_palettes_qty, benne_15m3_palettes_qty, benne_30m3_palettes_qty, 
    benne_10m3_encombrants_qty, benne_15m3_encombrants_qty, benne_30m3_encombrants_qty, big_bag_1m3_qty, 
    benne_chaine_3m3_qty, benne_chaine_8m3_qty, benne_chaine_15m3_qty, benne_ampliroll_30m3_qty_1, 
    big_bag_1m3_qty_bois, benne_ampliroll_3m3_qty_bois, benne_chaine_8m3_qty_bois, benne_chaine_15m3_qty_bois, 
    benne_ampliroll_30m3_qty_bois, big_bag_1m3_qty_platre, benne_chaine_8m3_qty_platre, 
    big_bag_1m3_qty_gravats_melange, benne_ampliroll_3m3_qty_gravats_melange, benne_chaine_8m3_qty_gravats_melange, 
    big_bag_1m3_qty_gravats_propres, benne_ampliroll_3m3_qty_gravats_propres, benne_chaine_8m3_qty_gravats_propres, 
    box_d3e_qty, bac_550l_qty, caisse_palette_qty, box_cartouche_qty, bac_550l_cartouche_qty, bac_pile_6kg_qty, 
    bac_pile_15kg_qty, machine_laver_qty, refrigerateur_qty, four_qty, cuisiniere_qty, box_ampoules_qty, box_neons_qty, 
    bac_550l_ampoules_qty, box_70L_archives_qty, box_130L_archives_qty, box_240L_archives_qty, box_480L_archives_qty, 
    vrac_estimer_archives, benne_8m3_gravats_qty, benne_8m3_dechets_qty, benne_10m3_dechets_qty, 
    benne_15m3_dechets_qty, benne_30m3_dechets_qty, camion_selectionne
) VALUES (?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


// Préparation de la requête
$stmt = $conn->prepare($sql);


if (!$stmt) {
    writeLog("Erreur lors de la préparation de la requête : " . $conn->error);
    die("Erreur lors de la préparation de la requête : " . $conn->error);
}
writeLog("Requête préparée avec succès.");

// Liaison des paramètres (ajustez les types selon les données)
$stmt->bind_param(
    'ssssssssssiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiis',
    $nom, $email, $telephone, $adresse, $description, $profession, $siret, $raison, $fileNames, 
    $benne_10m3_cartons_qty, $benne_15m3_cartons_qty, $benne_30m3_cartons_qty, 
    $benne_10m3_papiers_qty, $benne_15m3_papiers_qty, $benne_30m3_papiers_qty, 
    $benne_10m3_plastiques_qty, $benne_15m3_plastiques_qty, $benne_30m3_plastiques_qty, 
    $benne_10m3_palettes_qty, $benne_15m3_palettes_qty, $benne_30m3_palettes_qty, 
    $benne_10m3_encombrants_qty, $benne_15m3_encombrants_qty, $benne_30m3_encombrants_qty, 
    $big_bag_1m3_qty, $benne_chaine_3m3_qty, $benne_chaine_8m3_qty, $benne_chaine_15m3_qty, 
    $benne_ampliroll_30m3_qty_1, $big_bag_1m3_qty_bois, $benne_ampliroll_3m3_qty_bois, 
    $benne_chaine_8m3_qty_bois, $benne_chaine_15m3_qty_bois, $benne_ampliroll_30m3_qty_bois, 
    $big_bag_1m3_qty_platre, $benne_chaine_8m3_qty_platre, 
    $big_bag_1m3_qty_gravats_melange, $benne_ampliroll_3m3_qty_gravats_melange, 
    $benne_chaine_8m3_qty_gravats_melange, $big_bag_1m3_qty_gravats_propres, 
    $benne_ampliroll_3m3_qty_gravats_propres, $benne_chaine_8m3_qty_gravats_propres, 
    $box_d3e_qty, $bac_550l_qty, $caisse_palette_qty, $box_cartouche_qty, $bac_550l_cartouche_qty, 
    $bac_pile_6kg_qty, $bac_pile_15kg_qty, $machine_laver_qty, $refrigerateur_qty, $four_qty, 
    $cuisiniere_qty, $box_ampoules_qty, $box_neons_qty, $bac_550l_ampoules_qty, 
    $box_70L_archives_qty, $box_130L_archives_qty, $box_240L_archives_qty, 
    $box_480L_archives_qty, $vrac_estimer_archives, $benne_8m3_gravats_qty, $benne_8m3_dechets_qty, 
    $benne_10m3_dechets_qty, $benne_15m3_dechets_qty, $benne_30m3_dechets_qty, $camion_selectionne
);


// Exécution de la requête
if ($stmt->execute()) {
    writeLog("Requête exécutée avec succès.");
    echo "Insertion réussie!";
} else {
    writeLog("Erreur lors de l'exécution de la requête : " . $stmt->error);
    echo "Erreur lors de l'insertion : " . $stmt->error;
}

// Fermeture
$stmt->close();
$conn->close();
writeLog("Connexion à la base de données fermée.");
?>
