<?php

// Récupération des données du formulaire
$nom = htmlspecialchars($_POST['name'] ?? ''); 
$email = htmlspecialchars($_POST['email'] ?? '');
$telephone = htmlspecialchars($_POST['phone'] ?? '');
$adresse = htmlspecialchars($_POST['add'] ?? '');
$raisonSociale = htmlspecialchars($_POST['raison'] ?? '');
$siret = htmlspecialchars($_POST['siret'] ?? '');
$etage = htmlspecialchars($_POST['etage'] ?? 'non spécifié');
$ascenseur = htmlspecialchars($_POST['ascenseur'] ?? 'non spécifié');
$description = htmlspecialchars($_POST['description'] ?? '');
$je_recycle = htmlspecialchars($_POST['je_recycle'] ?? 'non spécifié');  // Récupérer la catégorie du produit
$produit_genere = $_POST['hiddenProduitField'] ?? 'non spécifié';

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

// Ajouter les produits vrac (cas des cases à cocher)
if (isset($_POST['destruction_archives']) && is_array($_POST['destruction_archives'])) {
    foreach ($_POST['destruction_archives'] as $vrac_value) {
        if (isset($productMapping[$vrac_value])) {
            $produits_commandes[] = [
                'id' => $productMapping[$vrac_value],
                'name' => $vrac_value,
                'quantity' => 1 // Quantité par défaut pour un produit vrac
            ];
        }
    }
}

// Créer un tableau des produits commandés
$produits_commandes = [];
foreach ($productMapping as $productName => $productId) {
    $productField = $productName . '_qty';
    if (isset($_POST[$productField])) {
        $quantite = intval($_POST[$productField]);
        if ($quantite > 0) {
            $produits_commandes[] = [
                'id' => $productId,
                'name' => $productName,
                'quantity' => $quantite
            ];
        }
    }
}


// Début de la génération de l'email
$htmlContent = "
    <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: auto;'>
        <h2 style='text-align: center; background-color: #4CAF50; color: #fff; padding: 10px;'>Nouvelle demande de devis</h2>
        <div style='padding: 20px; border: 1px solid #ddd;'>
            <h3>Informations Client</h3>
            <p><strong>Nom :</strong> " . htmlspecialchars($nom) . "</p>
            <p><strong>Société :</strong> " . htmlspecialchars($raisonSociale) . "</p>
            <p><strong>Email :</strong> <a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></p>
            <p><strong>Téléphone :</strong> <a href='tel:" . htmlspecialchars($telephone) . "'>" . htmlspecialchars($telephone) . "</a></p>
            <p><strong>Adresse :</strong> " . htmlspecialchars($adresse) . "</p>
            <p><strong>Étage :</strong> " . htmlspecialchars($etage) . "</p>
            <p><strong>Ascenseur :</strong> " . htmlspecialchars($ascenseur) . "</p>
            <p><strong>Catégorie du produit :</strong> " . htmlspecialchars($je_recycle) . "</p>
            <p><strong>Description :</strong><br>" . htmlspecialchars($description) . "</p>";

// 1. Gestion du camion uniquement s'il y a un produit camion sélectionné
if (!empty($produit_genere) && $produit_genere !== 'etageRDC_none') {
    $htmlContent .= "<p><strong>Camion :</strong><br>" . htmlspecialchars($produit_genere) . "</p>";
}

// 2. Gestion des produits "vrac"
if (isset($_POST['destruction_archives']) && is_array($_POST['destruction_archives'])) {
    $vrac_produits = [];
    foreach ($_POST['destruction_archives'] as $vrac_value) {
        if (isset($productMapping[$vrac_value])) {
            $vrac_produits[] = $vrac_value;
        }
    }
    
    if (!empty($vrac_produits)) {
        $htmlContent .= "<p><strong>Produits Vrac :</strong><br>" . implode(', ', $vrac_produits) . "</p>";
    }
}

// 3. Gestion des autres produits commandés (hors vrac et camion)
if (!empty($produits_commandes)) {
    $htmlContent .= "<h3>Produits Commandés</h3><table style='width: 100%; border-collapse: collapse;'>
                        <thead>
                            <tr>
                                <th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Produit</th>
                                <th style='border: 1px solid #ddd; padding: 8px; text-align: center;'>Quantité</th>
                            </tr>
                        </thead>
                        <tbody>";
    foreach ($produits_commandes as $produit) {
        $produit_name = htmlspecialchars($produit['name']);
        $produit_quantity = htmlspecialchars($produit['quantity']);
        $htmlContent .= "<tr>
                            <td style='border: 1px solid #ddd; padding: 8px;'>$produit_name</td>
                            <td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>$produit_quantity</td>
                         </tr>";
    }
    $htmlContent .= "</tbody></table>";
} else {
    $htmlContent .= "<p>Aucun produit commandé.</p>";
}

$htmlContent .= "
        </div>
        <p style='text-align: center; margin-top: 20px; color: #666;'>Cet email a été généré automatiquement, merci de ne pas y répondre directement.</p>
    </div>";

// Log du contenu de l'email pour test
error_log("=== Contenu de l'email généré ===\n", 3, $logFile);
error_log($htmlContent . "\n", 3, $logFile);
