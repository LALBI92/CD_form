<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des devis</title>
    <!-- Lien vers le fichier CSS -->
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <h1>Liste des devis</h1>

    <?php
    // Activer les erreurs PHP pour le débogage
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

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
        die("La connexion a échoué: " . $conn->connect_error);
    }

    // Initialiser les variables de filtre
    $date_filter = isset($_GET['date']) ? $_GET['date'] : '';
    $name_filter = isset($_GET['name']) ? $_GET['name'] : '';
    $email_filter = isset($_GET['email']) ? $_GET['email'] : '';

    // Construire la requête SQL avec les filtres
    $sql = "SELECT id, date_soumission, nom, raison, adresse, telephone, email, description, 
            benne_10m3_cartons_qty, benne_15m3_cartons_qty, benne_30m3_cartons_qty, 
            benne_10m3_papiers_qty, benne_15m3_papiers_qty, benne_30m3_papiers_qty, 
            benne_10m3_plastiques_qty, benne_15m3_plastiques_qty, benne_30m3_plastiques_qty, 
            benne_10m3_palettes_qty, benne_15m3_palettes_qty, benne_30m3_palettes_qty, 
            benne_10m3_encombrants_qty, benne_15m3_encombrants_qty, benne_30m3_encombrants_qty,
            big_bag_1m3_qty, benne_chaine_3m3_qty, benne_chaine_8m3_qty, benne_chaine_15m3_qty, 
            benne_ampliroll_30m3_qty_1, big_bag_1m3_qty_bois, benne_ampliroll_3m3_qty_bois, 
            benne_chaine_8m3_qty_bois, benne_chaine_15m3_qty_bois, benne_ampliroll_30m3_qty_bois, 
            big_bag_1m3_qty_platre, benne_chaine_8m3_qty_platre, 
            big_bag_1m3_qty_gravats_melange, benne_ampliroll_3m3_qty_gravats_melange, 
            benne_chaine_8m3_qty_gravats_melange, big_bag_1m3_qty_gravats_propres, 
            benne_ampliroll_3m3_qty_gravats_propres, benne_chaine_8m3_qty_gravats_propres, 
            box_d3e_qty, bac_550l_qty, caisse_palette_qty, box_cartouche_qty, bac_550l_cartouche_qty, 
            bac_pile_6kg_qty, bac_pile_15kg_qty, machine_laver_qty, refrigerateur_qty, four_qty, 
            cuisiniere_qty, box_ampoules_qty, box_neons_qty, bac_550l_ampoules_qty, 
            box_70L_archives_qty, box_130L_archives_qty, box_240L_archives_qty, box_480L_archives_qty, 
            vrac_estimer_archives, benne_8m3_gravats_qty, benne_8m3_dechets_qty, 
            benne_10m3_dechets_qty, benne_15m3_dechets_qty, benne_30m3_dechets_qty, camion_selectionne
            FROM devis WHERE 1=1";

    // Appliquer les filtres s'ils existent
    if (!empty($date_filter)) {
        $sql .= " AND DATE(date_soumission) = '" . $conn->real_escape_string($date_filter) . "'";
    }

    if (!empty($name_filter)) {
        $sql .= " AND nom LIKE '%" . $conn->real_escape_string($name_filter) . "%'";
    }

    if (!empty($email_filter)) {
        $sql .= " AND email LIKE '%" . $conn->real_escape_string($email_filter) . "%'";
    }

    $result = $conn->query($sql);

    if ($result === false) {
        echo "Erreur SQL : " . $conn->error;
    } else {
        echo "<form method='GET' action=''>";
        echo "Filtrer par date : <input type='date' name='date' value='" . htmlspecialchars($date_filter) . "'>";
        echo " Rechercher par nom : <input type='text' name='name' value='" . htmlspecialchars($name_filter) . "'>";
        echo " Rechercher par email : <input type='email' name='email' value='" . htmlspecialchars($email_filter) . "'>";
        echo " <button type='submit'>Filtrer</button>";
        echo "</form>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Date de soumission</th><th>Nom</th><th>Raison sociale</th><th>Adresse</th><th>Téléphone</th><th>Email</th><th>Description</th><th>Produits commandés</th></tr>";
    }

    // Boucle pour afficher chaque ligne de résultat
    while ($row = $result->fetch_assoc()) {
        // Création du champ "Produits commandés"
        $produits_commandes = "";
        $produits = [
            'benne_10m3_cartons_qty' => 'Benne 10m³ Cartons',
            'benne_15m3_cartons_qty' => 'Benne 15m³ Cartons',
            'benne_30m3_cartons_qty' => 'Benne 30m³ Cartons',
            'benne_10m3_papiers_qty' => 'Benne 10m³ Papiers',
            'benne_15m3_papiers_qty' => 'Benne 15m³ Papiers',
            'benne_30m3_papiers_qty' => 'Benne 30m³ Papiers',
            'benne_10m3_plastiques_qty' => 'Benne 10m³ Plastiques',
            'benne_15m3_plastiques_qty' => 'Benne 15m³ Plastiques',
            'benne_30m3_plastiques_qty' => 'Benne 30m³ Plastiques',
            'benne_10m3_palettes_qty' => 'Benne 10m³ Palettes',
            'benne_15m3_palettes_qty' => 'Benne 15m³ Palettes',
            'benne_30m3_palettes_qty' => 'Benne 30m³ Palettes',
            'benne_10m3_encombrants_qty' => 'Benne 10m³ Encombrants',
            'benne_15m3_encombrants_qty' => 'Benne 15m³ Encombrants',
            'benne_30m3_encombrants_qty' => 'Benne 30m³ Encombrants',
            'big_bag_1m3_qty' => 'Big Bag 1m³',
            'benne_chaine_3m3_qty' => 'Benne à chaîne 3m³',
            'benne_chaine_8m3_qty' => 'Benne à chaîne 8m³',
            'benne_chaine_15m3_qty' => 'Benne à chaîne 15m³',
            'benne_ampliroll_30m3_qty_1' => 'Benne Ampliroll 30m³',
            'big_bag_1m3_qty_bois' => 'Big Bag 1m³ Bois',
            'benne_ampliroll_3m3_qty_bois' => 'Benne Ampliroll 3m³ Bois',
            'benne_chaine_8m3_qty_bois' => 'Benne à chaîne 8m³ Bois',
            'benne_chaine_15m3_qty_bois' => 'Benne à chaîne 15m³ Bois',
            'benne_ampliroll_30m3_qty_bois' => 'Benne Ampliroll 30m³ Bois',
            'big_bag_1m3_qty_platre' => 'Big Bag 1m³ Plâtre',
            'benne_chaine_8m3_qty_platre' => 'Benne à chaîne 8m³ Plâtre',
            'big_bag_1m3_qty_gravats_melange' => 'Big Bag 1m³ Gravats Mélangés',
            'benne_ampliroll_3m3_qty_gravats_melange' => 'Benne Ampliroll 3m³ Gravats Mélangés',
            'benne_chaine_8m3_qty_gravats_melange' => 'Benne à chaîne 8m³ Gravats Mélangés',
            'big_bag_1m3_qty_gravats_propres' => 'Big Bag 1m³ Gravats Propres',
            'benne_ampliroll_3m3_qty_gravats_propres' => 'Benne Ampliroll 3m³ Gravats Propres',
            'benne_chaine_8m3_qty_gravats_propres' => 'Benne à chaîne 8m³ Gravats Propres',
            'box_d3e_qty' => 'Box D3E',
            'bac_550l_qty' => 'Bac 550L',
            'caisse_palette_qty' => 'Caisse Palette',
            'box_cartouche_qty' => 'Box Cartouche',
            'bac_550l_cartouche_qty' => 'Bac 550L Cartouche',
            'bac_pile_6kg_qty' => 'Bac Pile 6kg',
            'bac_pile_15kg_qty' => 'Bac Pile 15kg',
            'machine_laver_qty' => 'Machine à laver',
            'refrigerateur_qty' => 'Réfrigérateur',
            'four_qty' => 'Four',
            'cuisiniere_qty' => 'Cuisinière',
            'box_ampoules_qty' => 'Box Ampoules',
            'box_neons_qty' => 'Box Néons',
            'bac_550l_ampoules_qty' => 'Bac 550L Ampoules',
            'box_70L_archives_qty' => 'Box 70L Archives',
            'box_130L_archives_qty' => 'Box 130L Archives',
            'box_240L_archives_qty' => 'Box 240L Archives',
            'box_480L_archives_qty' => 'Box 480L Archives',
            'vrac_estimer_archives' => 'Vrac à Estimer',
            'benne_8m3_gravats_qty' => 'Benne 8m³ Gravats',
            'benne_8m3_dechets_qty' => 'Benne 8m³ Déchets',
            'benne_10m3_dechets_qty' => 'Benne 10m³ Déchets',
            'benne_15m3_dechets_qty' => 'Benne 15m³ Déchets',
            'benne_30m3_dechets_qty' => 'Benne 30m³ Déchets',
            'camion_selectionne' => 'Camion sélectionné'
        ];

        foreach ($produits as $key => $label) {
            if ($row[$key] > 0) {
                $produits_commandes .= "$label : " . $row[$key] . "<br>";
            }
        }

        // Affichage des lignes du tableau
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['date_soumission'] . "</td>";
        echo "<td>" . $row['nom'] . "</td>";
        echo "<td>" . $row['raison'] . "</td>";
        echo "<td>" . $row['adresse'] . "</td>";
        echo "<td>" . $row['telephone'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['description'] . "</td>";
        echo "<td>" . $produits_commandes . "</td>";
        echo "</tr>";
    }

    // Fermer le tableau après la boucle
    echo "</table>";

    $conn->close();
    ?>

</body>
</html>
