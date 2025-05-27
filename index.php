<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis City Debarras</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php
    $config = require_once 'config.php';
    ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $config['google_maps_api_key']; ?>&libraries=places"></script>
    <script src="modal.js"></script>
</head>
<body>

    <header id="header">
        <div class="container">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/new-logo-city-debarras.png" alt="City Débarras Logo" class="logo">
        </div>
    </header>



<h2>Recevez immédiatement votre devis en quelques clics</h2>

<form action="invoiced.php" method="POST" enctype="multipart/form-data" id="devisForm">
    <!-- Step 1 -->
<div id="step-1" class="form-step">


    <!-- Champ Type de besoin -->
    <div>
        <label for="type_besoin">Type de besoin</label>
        <select name="type_besoin" id="type_besoin" required>
            <option value="">Choisir ponctuel ou régulier ?</option>
            <option value="Ponctuel">Ponctuel</option>
            <option value="Regulier">Régulier</option>
        </select>
    </div>

    <!-- Champ JE RECYCLE -->
    <div>
        <label for="je_recycle">Je recycle</label>
        <select name="je_recycle" id="je_recycle" required>
            <option value="">Choisir le type de déchets</option>
            <option value="destruction_archives">Destruction d'archives / Documents confidentiels</option>
            <option value="deee">Mes déchets électriques et électroniques DEEE / D3E</option>
            <option value="dechets_bureau">Mes déchets de Bureau / 5 Flux</option>
            <option value="mobilier_bureau">Mes mobiliers de bureau / DEA</option>
            <option value="debarrasser_local">Débarrasser tous types de local</option>
            <option value="louer_benne">Louer une benne</option>
            <option value="dechets_chantiers">Mes déchets de chantiers</option>
            <option value="dechets_non_dangereux">Mes déchets non dangereux</option>
            
            
        </select>
    </div>

    <!-- Champ Déchets Non Dangereux (choix multiples) -->
<div id="dechets_nd_wrapper" style="display: none;">
    <label>Déchets Non Dangereux (Benne à Déchet unique)</label>
    <div class="choices-grid">
        <div class="choice" data-value="cartons">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/01/001-box.png" alt="Cartons" width="50">
            <span>Cartons</span>
        </div>
        <div class="choice" data-value="papiers">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/01/004-paper.png" alt="Papiers" width="50">
            <span>Papiers</span>
        </div>
        <div class="choice" data-value="plastiques">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/01/003-plastic.png" alt="Plastiques" width="50">
            <span>Plastiques</span>
        </div>
        <div class="choice" data-value="palettes">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/01/008-pallet.png" alt="Palettes" width="50">
            <span>Palettes</span>
        </div>
        <div class="choice" data-value="encombrants">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/01/001-living-room.png" alt="Encombrants" width="50">
            <span>Encombrants</span>
        </div>
        <div class="choice" data-value="dib">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/01/001-container.png" alt="DIB" width="50">
            <span>DIB</span>
        </div>
        <div class="choice" data-value="bois">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/01/005-wood.png" alt="Bois" width="50">
            <span>Bois</span>
        </div>
        <div class="choice" data-value="ferrailles">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/01/002-nut.png" alt="Ferrailles" width="50">
            <span>Ferrailles</span>
        </div>
        <div class="choice" data-value="dechets_vert">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/01/007-green-energy.png" alt="Déchets vert" width="50">
            <span>Déchets vert</span>
        </div>
    </div>
</div>


    <!-- Champ DND Contenants Cartons (choix multiples) -->
<div id="dnd_cartons_wrapper" style="display: none;">
    <label>DND Contenants Cartons (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne benne-10m3">
            <label for="benne_10m3_cartons">
                <img src="https://citydebarras.fr/wp-content/uploads/2025/05/10rose.png" alt="Benne 10m3" width="50">
                <span>Benne 10m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_10m3_cartons_qty">-</button>
                <input type="number" id="benne_10m3_cartons_qty" name="benne_10m3_cartons_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_10m3_cartons_qty">+</button>
            </div>
        </div>
        <div class="benne benne-15m3">
            <label for="benne_15m3_cartons">
                <img src="https://citydebarras.fr/wp-content/uploads/2025/05/15jaune.png" alt="Benne 15m3" width="50">
                <span>Benne 15m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_15m3_cartons_qty">-</button>
                <input type="number" id="benne_15m3_cartons_qty" name="benne_15m3_cartons_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_15m3_cartons_qty">+</button>
            </div>
        </div>
        <div class="benne benne-30m3">
            <label for="benne_30m3_cartons">
                <img src="https://citydebarras.fr/wp-content/uploads/2020/09/4.png" alt="Benne 30m3" width="50">
                <span>Benne 30m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_30m3_cartons_qty">-</button>
                <input type="number" id="benne_30m3_cartons_qty" name="benne_30m3_cartons_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_30m3_cartons_qty">+</button>
            </div>
        </div>
        <div class="benne benne-palette-660l">
            <label for="palette_660l_cartons">
                <img src="https://citydebarras.fr/wp-content/uploads/2020/09/1.png" alt="Palette 660L" width="50">
                <span>Palette 660L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="palette_660l_carton_sqty">-</button>
                <input type="number" id="palette_660l_cartons_qty" name="palette_660l_cartons_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="palette_660l_cartons_qty">+</button>
            </div>
        </div>
        <div class="benne benne-bac-roulant-770l">
            <label for="bac_roulant_770l_cartons">
                <img src="https://citydebarras.fr/wp-content/uploads/2020/09/2.png" alt="Bac Roulant 770L" width="50">
                <span>Bac Roulant 770L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_roulant_770l_cartons_qty">-</button>
                <input type="number" id="bac_roulant_770l_cartons_qty" name="bac_roulant_770l_cartons_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_roulant_770l_cartons_qty">+</button>
            </div>
        </div>

    </div>
</div>


   <!-- Champ DND Contenants Papiers (choix multiples) -->
<div id="dnd_papiers_wrapper" style="display: none;">
    <label>DND Contenants Papiers (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne benne-10m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/10rose.png" alt="Benne 10m3" />
            <label for="benne_10m3_papiers">
                <span>Benne 10m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_10m3_papiers_qty">-</button>
                <input type="number" id="benne_10m3_papiers_qty" name="benne_10m3_papiers_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_10m3_papiers_qty">+</button>
            </div>
        </div>
        <div class="benne benne-15m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/15jaune.png" alt="Benne 15m3" />
            <label for="benne_15m3_papiers">
                <span>Benne 15m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_15m3_papiers_qty">-</button>
                <input type="number" id="benne_15m3_papiers_qty" name="benne_15m3_papiers_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_15m3_papiers_qty">+</button>
            </div>
        </div>
        <div class="benne benne-30m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/09/4.png" alt="Benne 30m3" />
            <label for="benne_30m3_papiers">
                <span>Benne 30m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_30m3_papiers_qty">-</button>
                <input type="number" id="benne_30m3_papiers_qty" name="benne_30m3_papiers_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_30m3_papiers_qty">+</button>
            </div>
        </div>
        <div class="benne benne-palette-660l">
            <label for="palette_660l_papiers">
                <img src="https://citydebarras.fr/wp-content/uploads/2020/09/1.png" alt="Palette 660L" width="50">
                <span>Palette 660L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="palette_660l_papiers_sqty">-</button>
                <input type="number" id="palette_660l_papiers_qty" name="palette_660l_papiers_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="palette_660l_papiers_qty">+</button>
            </div>
        </div>
        <div class="benne benne-bac-roulant-770l">
            <label for="bac_roulant_770l_papiers">
                <img src="https://citydebarras.fr/wp-content/uploads/2020/09/2.png" alt="Bac Roulant 770L" width="50">
                <span>Bac Roulant 770L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_roulant_770l_papiers_qty">-</button>
                <input type="number" id="bac_roulant_770l_papiers_qty" name="bac_roulant_770l_papiers_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_roulant_770l_papiers_qty">+</button>
            </div>
        </div>
    </div>
</div>


   <!-- Champ DND Contenants Plastiques (choix multiples) -->
<div id="dnd_plastiques_wrapper" style="display: none;">
    <label>DND Contenants Plastiques (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne benne-10m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/10rose.png" alt="Benne 10m3" />
            <label for="benne_10m3_plastiques">
                <span>Benne 10m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_10m3_plastiques_qty">-</button>
                <input type="number" id="benne_10m3_plastiques_qty" name="benne_10m3_plastiques_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_10m3_plastiques_qty">+</button>
            </div>
        </div>
        <div class="benne benne-15m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/15jaune.png" alt="Benne 15m3" />
            <label for="benne_15m3_plastiques">
                <span>Benne 15m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_15m3_plastiques_qty">-</button>
                <input type="number" id="benne_15m3_plastiques_qty" name="benne_15m3_plastiques_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_15m3_plastiques_qty">+</button>
            </div>
        </div>
        <div class="benne benne-30m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/09/4.png" alt="Benne 30m3" />
            <label for="benne_30m3_plastiques">
                <span>Benne 30m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_30m3_plastiques_qty">-</button>
                <input type="number" id="benne_30m3_plastiques_qty" name="benne_30m3_plastiques_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_30m3_plastiques_qty">+</button>
            </div>
        </div>
        <div class="benne benne-palette-660l">
            <label for="palette_660l_plastiques">
                <img src="https://citydebarras.fr/wp-content/uploads/2020/09/1.png" alt="Palette 660L" width="50">
                <span>Palette 660L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="palette_660l_plastiques_sqty">-</button>
                <input type="number" id="palette_660l_plastiques_qty" name="palette_660l_plastiques_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="palette_660l_plastiques_qty">+</button>
            </div>
        </div>
        <div class="benne benne-bac-roulant-770l">
            <label for="bac_roulant_770l_plastiques">
                <img src="https://citydebarras.fr/wp-content/uploads/2020/09/2.png" alt="Bac Roulant 770L" width="50">
                <span>Bac Roulant 770L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_roulant_770l_plastiques_qty">-</button>
                <input type="number" id="bac_roulant_770l_plastiques_qty" name="bac_roulant_770l_plastiques_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_roulant_770l_plastiques_qty">+</button>
            </div>
        </div>
    </div>
</div>


  <!-- Champ DND Contenants Palettes (choix multiples) -->
<div id="dnd_palettes_wrapper" style="display: none;">
    <label>DND Contenants Palettes (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne benne-10m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/10rose.png" alt="Benne 10m3" />
            <label for="benne_10m3_palettes">
                <span>Benne 10m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_10m3_palettes_qty">-</button>
                <input type="number" id="benne_10m3_palettes_qty" name="benne_10m3_palettes_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_10m3_palettes_qty">+</button>
            </div>
        </div>
        <div class="benne benne-15m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/15jaune.png" alt="Benne 15m3" />
            <label for="benne_15m3_palettes">
                <span>Benne 15m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_15m3_palettes_qty">-</button>
                <input type="number" id="benne_15m3_palettes_qty" name="benne_15m3_palettes_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_15m3_palettes_qty">+</button>
            </div>
        </div>
        <div class="benne benne-30m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/09/4.png" alt="Benne 30m3" />
            <label for="benne_30m3_palettes">
                <span>Benne 30m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_30m3_palettes_qty">-</button>
                <input type="number" id="benne_30m3_palettes_qty" name="benne_30m3_palettes_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_30m3_palettes_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Champ DND Contenants Encombrants (choix multiples) -->
<div id="dnd_encombrants_wrapper" style="display: none;">
    <label>DND Contenants Encombrants (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne benne-10m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/10rose.png" alt="Benne 10m3" />
            <label for="benne_10m3_encombrants">
                <span>Benne 10m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_10m3_encombrants_qty">-</button>
                <input type="number" id="benne_10m3_encombrants_qty" name="benne_10m3_encombrants_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_10m3_encombrants_qty">+</button>
            </div>
        </div>
        <div class="benne benne-15m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/15jaune.png" alt="Benne 15m3" />
            <label for="benne_15m3_encombrants">
                <span>Benne 15m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_15m3_encombrants_qty">-</button>
                <input type="number" id="benne_15m3_encombrants_qty" name="benne_15m3_encombrants_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_15m3_encombrants_qty">+</button>
            </div>
        </div>
        <div class="benne benne-30m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/09/4.png" alt="Benne 30m3" />
            <label for="benne_30m3_encombrants">
                <span>Benne 30m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_30m3_encombrants_qty">-</button>
                <input type="number" id="benne_30m3_encombrants_qty" name="benne_30m3_encombrants_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_30m3_encombrants_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Champ DND Contenants DIB (choix multiples) -->
<div id="dnd_dib_wrapper" style="display: none;">
    <label>DND Contenants DIB (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne benne-10m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/10rose.png" alt="Benne 10m3" />
            <label for="benne_10m3_dib">
                <span>Benne 10m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_10m3_dib_qty">-</button>
                <input type="number" id="benne_10m3_dib_qty" name="benne_10m3_dib_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_10m3_dib_qty">+</button>
            </div>
        </div>
        <div class="benne benne-15m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/15jaune.png" alt="Benne 15m3" />
            <label for="benne_15m3_dib">
                <span>Benne 15m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_15m3_dib_qty">-</button>
                <input type="number" id="benne_15m3_dib_qty" name="benne_15m3_dib_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_15m3_dib_qty">+</button>
            </div>
        </div>
        <div class="benne benne-30m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/09/4.png" alt="Benne 30m3" />
            <label for="benne_30m3_dib">
                <span>Benne 30m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_30m3_dib_qty">-</button>
                <input type="number" id="benne_30m3_dib_qty" name="benne_30m3_dib_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_30m3_dib_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Champ DND Contenants Bois (choix multiples) -->
<div id="dnd_bois_wrapper" style="display: none;">
    <label>DND Contenants Bois (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne benne-10m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/10rose.png" alt="Benne 10m3" />
            <label for="benne_10m3_bois">
                <span>Benne 10m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_10m3_bois_qty">-</button>
                <input type="number" id="benne_10m3_bois_qty" name="benne_10m3_bois_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_10m3_bois_qty">+</button>
            </div>
        </div>
        <div class="benne benne-15m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/15jaune.png" alt="Benne 15m3" />
            <label for="benne_15m3_bois">
                <span>Benne 15m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_15m3_bois_qty">-</button>
                <input type="number" id="benne_15m3_bois_qty" name="benne_15m3_bois_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_15m3_bois_qty">+</button>
            </div>
        </div>
        <div class="benne benne-30m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/09/4.png" alt="Benne 30m3" />
            <label for="benne_30m3_bois">
                <span>Benne 30m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_30m3_bois_qty">-</button>
                <input type="number" id="benne_30m3_bois_qty" name="benne_30m3_bois_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_30m3_bois_qty">+</button>
            </div>
        </div>
        <div class="benne benne-palette-660l">
        <label for="palette_660l_bois">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/09/1.png" alt="Palette 660L" width="50">
            <span>Palette 660L</span>
        </label>
        <div class="quantity-selector">
            <button type="button" class="qty-minus" data-target="palette_660l_bois_sqty">-</button>
            <input type="number" id="palette_660l_bois_qty" name="palette_660l_bois_qty" value="0" min="0" readonly>
            <button type="button" class="qty-plus" data-target="palette_660l_bois_qty">+</button>
        </div>
    </div>
    </div>
</div>

<!-- Champ DND Contenants Ferrailles (choix multiples) -->
<div id="dnd_ferrailles_wrapper" style="display: none;">
    <label>DND Contenants Ferrailles (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne benne-10m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/10rose.png" alt="Benne 10m3" />
            <label for="benne_10m3_ferrailles">
                <span>Benne 10m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_10m3_ferrailles_qty">-</button>
                <input type="number" id="benne_10m3_ferrailles_qty" name="benne_10m3_ferrailles_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_10m3_ferrailles_qty">+</button>
            </div>
        </div>
        <div class="benne benne-15m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/15jaune.png" alt="Benne 15m3" />
            <label for="benne_15m3_ferrailles">
                <span>Benne 15m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_15m3_ferrailles_qty">-</button>
                <input type="number" id="benne_15m3_ferrailles_qty" name="benne_15m3_ferrailles_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_15m3_ferrailles_qty">+</button>
            </div>
        </div>
        <div class="benne benne-30m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/09/4.png" alt="Benne 30m3" />
            <label for="benne_30m3_ferrailles">
                <span>Benne 30m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_30m3_ferrailles_qty">-</button>
                <input type="number" id="benne_30m3_ferrailles_qty" name="benne_30m3_ferrailles_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_30m3_ferrailles_qty">+</button>
            </div>
        </div>
        <div class="benne benne-palette-660l">
        <label for="palette_660l_ferrailles">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/09/1.png" alt="Palette 660L" width="50">
            <span>Palette 660L</span>
        </label>
        <div class="quantity-selector">
            <button type="button" class="qty-minus" data-target="palette_660l_ferrailles_sqty">-</button>
            <input type="number" id="palette_660l_ferrailles_qty" name="palette_660l_ferrailles_qty" value="0" min="0" readonly>
            <button type="button" class="qty-plus" data-target="palette_660l_ferrailles_qty">+</button>
        </div>
    </div>
    </div>
</div>

<!-- Champ DND Contenants Déchets verts (choix multiples) -->
<div id="dnd_dechets_verts_wrapper" style="display: none;">
    <label>DND Contenants Déchets verts (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne benne-10m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/10rose.png" alt="Benne 10m3" />
            <label for="benne_10m3_dechets_verts">
                <span>Benne 10m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_10m3_dechets_verts_qty">-</button>
                <input type="number" id="benne_10m3_dechets_verts_qty" name="benne_10m3_dechets_verts_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_10m3_dechets_verts_qty">+</button>
            </div>
        </div>
        <div class="benne benne-15m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/15jaune.png" alt="Benne 15m3" />
            <label for="benne_15m3_dechets_verts">
                <span>Benne 15m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_15m3_dechets_verts_qty">-</button>
                <input type="number" id="benne_15m3_dechets_verts_qty" name="benne_15m3_dechets_verts_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_15m3_dechets_verts_qty">+</button>
            </div>
        </div>
        <div class="benne benne-30m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/09/4.png" alt="Benne 30m3" />
            <label for="benne_30m3_dechets_verts">
                <span>Benne 30m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_30m3_dechets_verts_qty">-</button>
                <input type="number" id="benne_30m3_dechets_verts_qty" name="benne_30m3_dechets_verts_qty" value="0" min="0">
                <button type="button" class="qty-plus" data-target="benne_30m3_dechets_verts_qty">+</button>
            </div>
        </div>
    </div>
</div>


   <!-- Champ Déchets Chantiers (choix multiples) -->
<div id="dechets_chantiers_wrapper" style="display: none;">
    <label>Déchets Chantiers (choix multiples)</label>
    <div class="choices-grid">
        <div class="choice" data-value="dib_chantier">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/01/001-container.png" alt="DIB" width="50">
            <span>DIB</span>
        </div>
        <div class="choice" data-value="bois_chantier">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/01/005-wood.png" alt="Bois" width="50">
            <span>Bois</span>
        </div>
        <div class="choice" data-value="platre_chantier">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/01/003-trowel.png" alt="Plâtre" width="50">
            <span>Plâtre</span>
        </div>
        <div class="choice" data-value="gravats_melange_chantier">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/01/004-shovel-1.png" alt="Gravats Mélangés" width="50">
            <span>Gravats Mélangés</span>
        </div>
        <div class="choice" data-value="gravats_propres_chantier">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/01/002-shovel.png" alt="Gravats Propres" width="50">
            <span>Gravats Propres</span>
        </div>
    </div>
</div>




<!-- Champ DC Contenants DIB Chantier (choix multiples) -->
<div id="dib_chantier_wrapper" style="display: none;">
    <label>DC Contenants DIB Chantier (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne benne-1m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/11/Big-Bag-1m.png" alt="Big Bag 1m3">
            <label for="big_bag_1m3_dib_chantier">
                <span>Big Bag 1m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="big_bag_1m3_dib_chantier_qty">-</button>
                <input type="number" id="big_bag_1m3_dib_chantier_qty" name="big_bag_1m3_dib_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="big_bag_1m3_dib_chantier_qty">+</button>
            </div>
        </div>
        <div class="benne benne-3m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/11/Benne-a-chaine-3m.png" alt="Benne à chaîne 3m3">
            <label for="benne_chaine_3m3_dib_chantier">
                <span>Benne à chaîne 3m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_chaine_3m3_dib_chantier_qty">-</button>
                <input type="number" id="benne_chaine_3m3_dib_chantier_qty" name="benne_chaine_3m3_dib_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_chaine_3m3_dib_chantier_qty">+</button>
            </div>
        </div>
        <div class="benne benne-8m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/Benne-a-chaine-3m_jaune.png" alt="Benne à chaîne 8m3">
            <label for="benne_chaine_8m3_dib_chantier">
                <span>Benne à chaîne 8m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_chaine_8m3_dib_chantier_qty">-</button>
                <input type="number" id="benne_chaine_8m3_dib_chantier_qty" name="benne_chaine_8m3_dib_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_chaine_8m3_dib_chantier_qty">+</button>
            </div>
        </div>
        <div class="benne benne-15m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/15jaune.png" alt="Benne à chaîne 15m3">
            <label for="benne_chaine_15m3_dib_chantier">
                <span>Benne ampliroll 15m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_chaine_15m3_dib_chantier_qty">-</button>
                <input type="number" id="benne_chaine_15m3_dib_chantier_qty" name="benne_chaine_15m3_dib_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_chaine_15m3_dib_chantier_qty">+</button>
            </div>
        </div>
        <div class="benne benne-30m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/09/4.png" alt="Benne ampliroll 30m3">
            <label for="benne_ampliroll_30m3_dib_chantier">
                <span>Benne ampliroll 30m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_ampliroll_30m3_dib_chantier_qty">-</button>
                <input type="number" id="benne_ampliroll_30m3_dib_chantier_qty" name="benne_ampliroll_30m3_dib_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_ampliroll_30m3_dib_chantier_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Champ DC Contenants Bois Chantier (choix multiples) -->
<div id="bois_chantier_wrapper" style="display: none;">
    <label>DC Contenants Bois Chantier (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne benne-1m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/11/Big-Bag-1m.png" alt="Big Bag 1m3">
            <label for="big_bag_1m3_bois_chantier">
                <span>Big Bag 1m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="big_bag_1m3_bois_chantier_qty">-</button>
                <input type="number" id="big_bag_1m3_bois_chantier_qty" name="big_bag_1m3_bois_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="big_bag_1m3_bois_chantier_qty">+</button>
            </div>
        </div>
        <div class="benne benne-3m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/11/Benne-ampliroll-20m.png" alt="Benne ampliroll 3m3">
            <label for="benne_ampliroll_3m3_bois_chantier">
                <span>Benne ampliroll 3m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_ampliroll_3m3_bois_chantier_qty">-</button>
                <input type="number" id="benne_ampliroll_3m3_bois_chantier_qty" name="benne_ampliroll_3m3_bois_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_ampliroll_3m3_bois_chantier_qty">+</button>
            </div>
        </div>
        <div class="benne benne-8m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/Benne-a-chaine-3m_jaune.png" alt="Benne à chaîne 8m3">
            <label for="benne_chaine_8m3_bois_chantier">
                <span>Benne à chaîne 8m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_chaine_8m3_bois_chantier_qty">-</button>
                <input type="number" id="benne_chaine_8m3_bois_chantier_qty" name="benne_chaine_8m3_bois_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_chaine_8m3_bois_chantier_qty">+</button>
            </div>
        </div>
        <div class="benne benne-15m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/15jaune.png" alt="Benne à chaîne 15m3">
            <label for="benne_chaine_15m3_bois_chantier">
                <span>Benne ampliroll 15m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_chaine_15m3_bois_chantier_qty">-</button>
                <input type="number" id="benne_chaine_15m3_bois_chantier_qty" name="benne_chaine_15m3_bois_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_chaine_15m3_bois_chantier_qty">+</button>
            </div>
        </div>
        <div class="benne benne-30m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/09/4.png" alt="Benne ampliroll 30m3">
            <label for="benne_ampliroll_30m3_bois_chantier">
                <span>Benne ampliroll 30m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_ampliroll_30m3_bois_chantier_qty">-</button>
                <input type="number" id="benne_ampliroll_30m3_bois_chantier_qty" name="benne_ampliroll_30m3_bois_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_ampliroll_30m3_bois_chantier_qty">+</button>
            </div>
        </div>
    </div>
</div>


<!-- Champ DC Contenants Platre Chantier (choix multiples) -->
<div id="platre_chantier_wrapper" style="display: none;">
    <label>DC Contenants Platre Chantier (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne benne-1m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/11/Big-Bag-1m.png" alt="Big Bag 1m3">
            <label for="big_bag_1m3_platre_chantier">
                <span>Big Bag 1m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="big_bag_1m3_platre_chantier_qty">-</button>
                <input type="number" id="big_bag_1m3_platre_chantier_qty" name="big_bag_1m3_platre_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="big_bag_1m3_platre_chantier_qty">+</button>
            </div>
        </div>
        <div class="benne benne-8m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/Benne-a-chaine-3m_jaune.png" alt="Benne à chaîne 8m3">
            <label for="benne_chaine_8m3_platre_chantier">
                <span>Benne à chaîne 8m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_chaine_8m3_platre_chantier_qty">-</button>
                <input type="number" id="benne_chaine_8m3_platre_chantier_qty" name="benne_chaine_8m3_platre_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_chaine_8m3_platre_chantier_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Champ DC Contenants Gravats Mélangés Chantier (choix multiples) -->
<div id="gravats_melange_chantier_wrapper" style="display: none;">
    <label>DC Contenants Gravats Mélangés Chantier (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne benne-1m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/11/Big-Bag-1m.png" alt="Big Bag 1m3">
            <label for="big_bag_1m3_gravats_melange_chantier">
                <span>Big Bag 1m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="big_bag_1m3_gravats_melange_chantier_qty">-</button>
                <input type="number" id="big_bag_1m3_gravats_melange_chantier_qty" name="big_bag_1m3_gravats_melange_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="big_bag_1m3_gravats_melange_chantier_qty">+</button>
            </div>
        </div>
        <div class="benne benne-3m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/11/Benne-ampliroll-20m.png" alt="Benne ampliroll 3m3">
            <label for="benne_ampliroll_3m3_gravats_melange_chantier">
                <span>Benne ampliroll 3m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_ampliroll_3m3_gravats_melange_chantier_qty">-</button>
                <input type="number" id="benne_ampliroll_3m3_gravats_melange_chantier_qty" name="benne_ampliroll_3m3_gravats_melange_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_ampliroll_3m3_gravats_melange_chantier_qty">+</button>
            </div>
        </div>
        <div class="benne benne-8m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/Benne-a-chaine-3m_jaune.png" alt="Benne à chaîne 8m3">
            <label for="benne_chaine_8m3_gravats_melange_chantier">
                <span>Benne à chaîne 8m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_chaine_8m3_gravats_melange_chantier_qty">-</button>
                <input type="number" id="benne_chaine_8m3_gravats_melange_chantier_qty" name="benne_chaine_8m3_gravats_melange_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_chaine_8m3_gravats_melange_chantier_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Champ DC Contenants Gravats Propres Chantier (choix multiples) -->
<div id="gravats_propres_chantier_wrapper" style="display: none;">
    <label>DC Contenants Gravats Propres Chantier (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne benne-1m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/11/Big-Bag-1m.png" alt="Big Bag 1m3">
            <label for="big_bag_1m3_gravats_propres_chantier">
                <span>Big Bag 1m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="big_bag_1m3_gravats_propres_chantier_qty">-</button>
                <input type="number" id="big_bag_1m3_gravats_propres_chantier_qty" name="big_bag_1m3_gravats_propres_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="big_bag_1m3_gravats_propres_chantier_qty">+</button>
            </div>
        </div>
        <div class="benne benne-3m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/11/Benne-ampliroll-20m.png" alt="Benne ampliroll 3m3">
            <label for="benne_ampliroll_3m3_gravats_propres_chantier">
                <span>Benne ampliroll 3m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_ampliroll_3m3_gravats_propres_chantier_qty">-</button>
                <input type="number" id="benne_ampliroll_3m3_gravats_propres_chantier_qty" name="benne_ampliroll_3m3_gravats_propres_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_ampliroll_3m3_gravats_propres_chantier_qty">+</button>
            </div>
        </div>
        <div class="benne benne-8m3">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/Benne-a-chaine-3m_jaune.png" alt="Benne à chaîne 8m3">
            <label for="benne_chaine_8m3_gravats_propres_chantier">
                <span>Benne à chaîne 8m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_chaine_8m3_gravats_propres_chantier_qty">-</button>
                <input type="number" id="benne_chaine_8m3_gravats_propres_chantier_qty" name="benne_chaine_8m3_gravats_propres_chantier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_chaine_8m3_gravats_propres_chantier_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Champ Déchets de Bureau (choix multiples) -->
<div id="dechets_bureau_wrapper" style="display: none;">
    <label>Déchets de Bureau (choix multiples)</label>
    <div class="choices-grid">
        <!-- Box Gobelet -->
        <div class="benne box-gobelet">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/Gobelets-1536x1536.png" alt="Box Gobelet">
            <label for="box_gobelet">
                <span>Box Gobelet 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_gobelet_qty">-</button>
                <input type="number" id="box_gobelet_qty" name="box_gobelet_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_gobelet_qty">+</button>
            </div>
        </div>
        <!-- Box Capsule -->
        <div class="benne box-capsule">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/Capsules-1536x1536.png" alt="Box Capsule">
            <label for="box_capsule">
                <span>Box Capsule 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_capsule_qty">-</button>
                <input type="number" id="box_capsule_qty" name="box_capsule_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_capsule_qty">+</button>
            </div>
        </div>
        <!-- Box Bio-déchets Hebdomadaire -->
        <div class="benne box-biodechets-hebdo">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/Dechets-1536x1536.png" alt="Box Bio-déchets Hebdomadaire">
            <label for="box_biodechets_hebdo">
                <span>Box Bio-déchets Hebdomadaire 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_biodechets_hebdo_qty">-</button>
                <input type="number" id="box_biodechets_hebdo_qty" name="box_biodechets_hebdo_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_biodechets_hebdo_qty">+</button>
            </div>
        </div>
        <!-- Box Bio-déchets 1 semaine sur 2 -->
        <div class="benne box-biodechets-2semaines">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/Dechets-1536x1536.png" alt="Box Bio-déchets 1 semaine sur 2">
            <label for="box_biodechets_2semaines">
                <span>Box Bio-déchets 1 semaine sur 2 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_biodechets_2semaines_qty">-</button>
                <input type="number" id="box_biodechets_2semaines_qty" name="box_biodechets_2semaines_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_biodechets_2semaines_qty">+</button>
            </div>
        </div>
        <!-- Box Piles -->
        <div class="benne box-piles">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/Cartouches-1536x1536.png" alt="Box Piles">
            <label for="box_piles">
                <span>Box Piles 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_piles_qty">-</button>
                <input type="number" id="box_piles_qty" name="box_piles_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_piles_qty">+</button>
            </div>
        </div>
        <!-- Box Bouteilles et Canettes -->
        <div class="benne box-bouteilles-canettes">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/IMG_20191229_115542.png" alt="Box Bouteilles et Canettes">
            <label for="box_bouteilles_canettes">
                <span>Box Bouteilles et Canettes 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_bouteilles_canettes_qty">-</button>
                <input type="number" id="box_bouteilles_canettes_qty" name="box_bouteilles_canettes_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_bouteilles_canettes_qty">+</button>
            </div>
        </div>
        <!-- Box Papier -->
        <div class="benne box-papier">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/Papiers-1-1536x1536.png" alt="Box Papier">
            <label for="box_papier">
                <span>Box Papier 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_papier_qty">-</button>
                <input type="number" id="box_papier_qty" name="box_papier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_papier_qty">+</button>
            </div>
        </div>
        <!-- Lot de 5 petits Box Papier -->
        <div class="benne lot-5-box-papier">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/Papiers-1-1536x1536.png" alt="Lot de 5 Petit Box Papier">
            <label for="lot_5_box_papier">
                <span>Lot de 5 Petit Box Papier (20L)</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="lot_5_box_papier_qty">-</button>
                <input type="number" id="lot_5_box_papier_qty" name="lot_5_box_papier_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="lot_5_box_papier_qty">+</button>
            </div>
        </div>
        <!-- Box D3E -->
        <div class="benne box_d3e_bureau">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/IMG_20191229_114714-1536x1536.png" alt="Box D3E">
            <label for="box_d3e_bureau">
                <span>Box D3E 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_d3e_bureau_qty">-</button>
                <input type="number" id="box_d3e_bureau_qty" name="box_d3e_bureau_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_d3e_bureau_qty">+</button>
            </div>
        </div>
        <!-- Box Sécurisé 90 -->
        <div class="benne box-secure-90">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/70.png" alt="Box sécurisé 90L" width="50">
            <label for="box_secure_90">
                <span>Box Sécurisé 90L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_secure_90_qty">-</button>
                <input type="number" id="box_secure_90_qty" name="box_secure_90_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_secure_90_qty">+</button>
            </div>
        </div>
        <!-- Box Sécurisé 120 -->
        <div class="benne box-secure-120">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/70.png" alt="Box sécurisé 120L" width="50">
            <label for="box_secure_120">
                <span>Box Sécurisé 120L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_secure_120_qty">-</button>
                <input type="number" id="box_secure_120_qty" name="box_secure_120_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_secure_120_qty">+</button>
            </div>
        </div>
        <!-- Box Sécurisé 240 -->
        <div class="benne box-secure-240">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/241.png" alt="Box sécurisé 240L" width="50">
            <label for="box_secure_240">
                <span>Box Sécurisé 240L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_secure_240_qty">-</button>
                <input type="number" id="box_secure_240_qty" name="box_secure_240_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_secure_240_qty">+</button>
            </div>
        </div>
        <!-- Box Sécurisé 480 -->
        <div class="benne box-secure-480">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/415.png" alt="Box roulant sécurisé 480L" width="50">
            <label for="box_secure_480">
                <span>Box Sécurisé 415L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_secure_480_qty">-</button>
                <input type="number" id="box_secure_480_qty" name="box_secure_480_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_secure_480_qty">+</button>
            </div>
        </div>
    </div>
</div>



<!-- Champ Déchets électriques et électroniques DEEE / D3E -->
<div id="deee_wrapper" style="display: none;">

    <label for="lieu_deee">Récupération à domicile</label>
    <select name="lieu_deee" id="lieu_deee">
        <option value="">Choisissez le lieu</option>
        <option value="type_deee_domicile">Oui</option>
        <option value="type_deee_depot">Non - Dépôt chez City Debarras</option>
    </select>
</div>

    <div id="type_deee_domicile_wrapper" style="display: none;">
        <label for="type_deee_domicile">Type de D3E / DEEE (domicile)</label>
        <select name="type_deee_domicile" id="deee_domicile_select">
            <option value="">Sélectionnez un type de D3E / DEEE</option>
            <option value="informatiques_bureautiques_domicile">Informatiques / Bureautiques</option>
            <option value="cartouches_encres_toners_domicile">Cartouches d'encres / Toners</option>
            <option value="accumulateurs_batteries_piles_domicile">Accumulateurs / Batteries / Piles</option>
            <option value="electromenager_chaud_froid_domicile">Electroménager chaud / Froid</option>
            <option value="climatisation_chaud_froid_domicile">Climatisation Chaud / Froid</option>
            <option value="ampoules_lampes_neons_domicile">Ampoules / Lampes / Néons</option>
        </select>
    </div>



<!-- Champ Informatiques et Bureautiques (choix multiples) -->
<div id="informatiques_bureautiques_domicile_wrapper" style="display: none;">
    <label>Informatiques et Bureautiques (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Box-e1748254026514.png" alt="Box D3E">
            <label for="box_d3e_domicile">
                <span>Box D3E 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_d3e_domicile_qty">-</button>
                <input type="number" id="box_d3e_domicile_qty" name="box_d3e_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_d3e_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/6.png" alt="Bac Industriel Roulette 200L">
            <label for="bac_industriel_200_DEEE_domicile">
                <span>Bac Industriel Roulette 200L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_industriel_200_DEEE_domicile_qty">-</button>
                <input type="number" id="bac_industriel_200_DEEE_domicile_qty" name="bac_industriel_200_DEEE_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_industriel_200_DEEE_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Bac-industriel-a%CC%80-roulettes-550L-e1748254097172.png" alt="Bac Industriel Roulette 550L">
            <label for="bac_550l_domicile">
                <span>Bac Industriel Roulette 550L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_550l_domicile_qty">-</button>
                <input type="number" id="bac_550l_domicile_qty" name="bac_550l_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_550l_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Caisse-palette-grillagée-1m3-1-e1748254117358.png" alt="Caisse Palette Grillagée 1m3">
            <label for="caisse_palette_domicile">
                <span>Caisse Palette Grillagée 1m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="caisse_palette_domicile_qty">-</button>
                <input type="number" id="caisse_palette_domicile_qty" name="caisse_palette_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="caisse_palette_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/1.png" alt="Petite imprimante">
            <label for="imprimante_petite_domicile">
                <span>Imprimante (petite)</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="imprimante_petite_domicile_qty">-</button>
                <input type="number" id="imprimante_petite_domicile_qty" name="imprimante_petite_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="imprimante_petite_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/2.png" alt="Moyenne imprimante">
            <label for="imprimante_moyenne_domicile">
                <span>Imprimante (moyenne)</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="imprimante_moyenne_domicile_qty">-</button>
                <input type="number" id="imprimante_moyenne_domicile_qty" name="imprimante_moyenne_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="imprimante_moyenne_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/3.png" alt="Grande imprimante">
            <label for="imprimante_grande_domicile">
                <span>Imprimante (grande)</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="imprimante_grande_domicile_qty">-</button>
                <input type="number" id="imprimante_grande_domicile_qty" name="imprimante_grande_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="imprimante_grande_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/4.png" alt="Déchiqueteuse">
            <label for="dechiqueteuse_domicile">
                <span>Déchiqueteuse</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="dechiqueteuse_domicile_qty">-</button>
                <input type="number" id="dechiqueteuse_domicile_qty" name="dechiqueteuse_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="dechiqueteuse_domicile_qty">+</button>
            </div>
        </div>


    </div>
</div>

<!-- Champ Cartouches d'encre / Toners (choix multiples) -->
<div id="cartouches_encres_domicile_wrapper" style="display: none;">
    <label>Cartouches d'encre / Toners (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Box-e1748254026514.png" alt="Box Cartouche">
            <label for="box_cartouche_domicile">
                <span>Box Cartouche 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_cartouche__domicileqty">-</button>
                <input type="number" id="box_cartouche_domicile_qty" name="box_cartouche_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_cartouche_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/6.png" alt="Bac Industriel Cartouches 200L">
            <label for="bac_toner_200l_domicile">
                <span>Bac Industriel Cartouches - Toners Roulette 200L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_toner_200l_domicile_qty">-</button>
                <input type="number" id="bac_toner_200l_domicile_qty" name="bac_toner_200l_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_toner_200l_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Bac-industriel-a%CC%80-roulettes-550L-e1748254097172.png" alt="Bac Industriel Roulette 550L">
            <label for="bac_550l_cartouche_domicile">
                <span>Bac Industriel Cartouches - Toners Roulette 550L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_550l_cartouche_domicile_qty">-</button>
                <input type="number" id="bac_550l_cartouche_domicile_qty" name="bac_550l_cartouche_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_550l_cartouche_domicile_qty">+</button>
            </div>
        </div>

    </div>
</div>

<!-- Champ Accumulateurs / Batteries / Piles (choix multiples) -->
<div id="piles_domicile_wrapper" style="display: none;">
    <label>Accumulateurs / Batteries / Piles (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Bac-à-piles-6kg--e1748254077253.png" alt="Bac PVC Pile 6Kg">
            <label for="bac_pile_6kg_domicile">
                <span>Bac PVC Pile 6Kg</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_pile_6kg_domicile_qty">-</button>
                <input type="number" id="bac_pile_6kg_domicile_qty" name="bac_pile_6kg_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_pile_6kg_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Bac-à-piles-15kg-e1748254046134.png" alt="Bac PVC Pile 15Kg">
            <label for="bac_pile_15kg_domicile">
                <span>Bac PVC Pile 15Kg</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_pile_15kg_domicile_qty">-</button>
                <input type="number" id="bac_pile_15kg_domicile_qty" name="bac_pile_15kg_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_pile_15kg_domicile_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Champ Électroménager Chaud / Froid (choix multiples) -->
<div id="electromenager_domicile_wrapper" style="display: none;">
    <label>Électroménager Chaud / Froid (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/machine-laver.png" alt="Machine à laver / Sèche linge">
            <label for="machine_laver_domicile">
                <span>Machine à laver / Sèche linge</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="machine_laver_domicile_qty">-</button>
                <input type="number" id="machine_laver_domicile_qty" name="machine_laver_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="machine_laver_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/frigo.png" alt="Réfrigirateur / Congélateur">
            <label for="refrigerateur_domicile">
                <span>Réfrigirateur</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="refrigerateur_domicile_qty">-</button>
                <input type="number" id="refrigerateur_domicile_qty" name="refrigerateur_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="refrigerateur_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2024/12/Design-sans-titre-7.png" alt="Congélateur">
            <label for="congelateur_domicile">
                <span>Congélateur</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="congelateur_domicile_qty">-</button>
                <input type="number" id="congelateur_domicile_qty" name="congelateur_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="congelateur_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/four.png" alt="Four">
            <label for="four_domicile">
                <span>Four</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="four_domicile_qty">-</button>
                <input type="number" id="four_domicile_qty" name="four_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="four_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/cuisiniere.png" alt="Cuisinière">
            <label for="cuisiniere_domicile">
                <span>Cuisinière</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="cuisiniere_domicile_qty">-</button>
                <input type="number" id="cuisiniere_domicile_qty" name="cuisiniere_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="cuisiniere_domicile_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Champ Ampoules / Lampes / Néons (choix multiples) -->
<div id="ampoules_domicile_wrapper" style="display: none;">
    <label>Ampoules / Lampes / Néons (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Box-e1748254026514.png" alt="Box Ampoules">
            <label for="box_ampoules_domicile">
                <span>Box Ampoules 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_ampoules_domicile_qty">-</button>
                <input type="number" id="box_ampoules_domicile_qty" name="box_ampoules_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_ampoules_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Box-e1748254026514.png" alt="Box Néons">
            <label for="box_neons_domicile">
                <span>Box Néons 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_neons_domicile_qty">-</button>
                <input type="number" id="box_neons_domicile_qty" name="box_neons_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_neons_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/6.png" alt="Bac Industriel Ampoule 200L">
            <label for="bac_200l_ampoules_domicile">
                <span>Bac Industriel Ampoules - Lampes Roulette  200L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_200l_ampoules_domicile_qty">-</button>
                <input type="number" id="bac_200l_ampoules_domicile_qty" name="bac_200l_ampoules_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_200l_ampoules_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Bac-industriel-a%CC%80-roulettes-550L-e1748254097172.png" alt="Bac Industriel Roulette 550L">
            <label for="bac_550l_ampoules_domicile">
                <span>Bac Industriel Ampoules - Lampes Roulette 550L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_550l_ampoules_domicile_qty">-</button>
                <input type="number" id="bac_550l_ampoules_domicile_qty" name="bac_550l_ampoules_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_550l_ampoules_domicile_qty">+</button>
            </div>
        </div>
    </div>
</div>

<div id="type_deee_depot_wrapper" style="display: none;">
    <label for="type_deee_depot">Type de D3E / DEEE (entrepôt) </label>
        <select name="type_deee_depot" id="deee_depot_select">
            <option value="">Sélectionnez un type de D3E / DEEE</option>
            <option value="informatiques_bureautiques_depot">Informatiques / Bureautiques</option>
            <option value="cartouches_encres_toners_depot">Cartouches d'encres / Toners</option>
            <option value="accumulateurs_batteries_piles_depot">Accumulateurs / Batteries / Piles</option>
            <option value="electromenager_chaud_froid_depot">Electroménager chaud / Froid</option>
            <option value="climatisation_chaud_froid_depot">Climatisation Chaud / Froid</option>
            <option value="ampoules_lampes_neons_depot">Ampoules / Lampes / Néons</option>
        </select>
</div>

<!-- Champ Informatiques et Bureautiques (choix multiples) -->
<div id="informatiques_bureautiques_depot_wrapper" style="display: none;">
    <label>Informatiques et Bureautiques (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Box-e1748254026514.png" alt="Box D3E">
            <label for="box_d3e_depot">
                <span>Box D3E 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_d3e_depot_qty">-</button>
                <input type="number" id="box_d3e_depot_qty" name="box_d3e_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_d3e_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/6.png" alt="Bac Industriel Roulette 200L">
            <label for="bac_industriel_200_DEEE_depot">
                <span>Bac Industriel Roulette 200L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_industriel_200_DEEE_depot_qty">-</button>
                <input type="number" id="bac_industriel_200_DEEE_depot_qty" name="bac_industriel_200_DEEE_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_industriel_200_DEEE_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Bac-industriel-a%CC%80-roulettes-550L-e1748254097172.png" alt="Bac Industriel Roulette 550L">
            <label for="bac_550l_depot">
                <span>Bac Industriel Roulette 550L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_550l_depot_qty">-</button>
                <input type="number" id="bac_550l_depot_qty" name="bac_550l_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_550l_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Caisse-palette-grillagée-1m3-1-e1748254117358.png" alt="Caisse Palette Grillagée 1m3">
            <label for="caisse_palette_depot">
                <span>Caisse Palette Grillagée 1m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="caisse_palette_depot_qty">-</button>
                <input type="number" id="caisse_palette_depot_qty" name="caisse_palette_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="caisse_palette_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/1.png" alt="Petite imprimante">
            <label for="imprimante_petite_depot">
                <span>Imprimante (petite)</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="imprimante_petite_depot_qty">-</button>
                <input type="number" id="imprimante_petite_depot_qty" name="imprimante_petite_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="imprimante_petite_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/2.png" alt="Moyenne imprimante">
            <label for="imprimante_moyenne_depot">
                <span>Imprimante (moyenne)</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="imprimante_moyenne_depot_qty">-</button>
                <input type="number" id="imprimante_moyenne_depot_qty" name="imprimante_moyenne_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="imprimante_moyenne_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/3.png" alt="Grande imprimante">
            <label for="imprimante_grande_depot">
                <span>Imprimante (grande)</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="imprimante_grande_depot_qty">-</button>
                <input type="number" id="imprimante_grande_depot_qty" name="imprimante_grande_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="imprimante_grande_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/4.png" alt="Déchiqueteuse">
            <label for="dechiqueteuse_depot">
                <span>Déchiqueteuse</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="dechiqueteuse_depot_qty">-</button>
                <input type="number" id="dechiqueteuse_depot_qty" name="dechiqueteuse_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="dechiqueteuse_depot_qty">+</button>
            </div>
        </div>


    </div>
</div>

<!-- Champ Cartouches d'encre / Toners (choix multiples) -->
<div id="cartouches_depot_encres_wrapper" style="display: none;">
    <label>Cartouches d'encre / Toners (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Box-e1748254026514.png" alt="Box Cartouche">
            <label for="box_cartouche_depot">
                <span>Box Cartouche 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_cartouche__depotqty">-</button>
                <input type="number" id="box_cartouche_depot_qty" name="box_cartouche_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_cartouche_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/6.png" alt="Bac Industriel Cartouches 200L">
            <label for="bac_toner_200l_depot">
                <span>Bac Industriel Cartouches - Toners Roulette 200L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_toner_200l_depot_qty">-</button>
                <input type="number" id="bac_toner_200l_depot_qty" name="bac_toner_200l_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_toner_200l_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Bac-industriel-a%CC%80-roulettes-550L-e1748254097172.png" alt="Bac Industriel Roulette 550L">
            <label for="bac_550l_cartouche_depot">
                <span>Bac Industriel Cartouches - Toners Roulette 550L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_550l_cartouche_depot_qty">-</button>
                <input type="number" id="bac_550l_cartouche_depot_qty" name="bac_550l_cartouche_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_550l_cartouche_depot_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Champ Accumulateurs / Batteries / Piles (choix multiples) -->
<div id="piles_depot_wrapper" style="display: none;">
    <label>Accumulateurs / Batteries / Piles (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Bac-à-piles-6kg--e1748254077253.png" alt="Bac PVC Pile 6Kg">
            <label for="bac_pile_6kg_depot">
                <span>Bac PVC Pile 6Kg</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_pile_6kg_depot_qty">-</button>
                <input type="number" id="bac_pile_6kg_depot_qty" name="bac_pile_6kg_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_pile_6kg_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Bac-à-piles-15kg-e1748254046134.png" alt="Bac PVC Pile 15Kg">
            <label for="bac_pile_15kg_depot">
                <span>Bac PVC Pile 15Kg</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_pile_15kg_depot_qty">-</button>
                <input type="number" id="bac_pile_15kg_depot_qty" name="bac_pile_15kg_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_pile_15kg_depot_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Champ Électroménager Chaud / Froid (choix multiples) -->
<div id="electromenager_depot_wrapper" style="display: none;">
    <label>Électroménager Chaud / Froid (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/machine-laver.png" alt="Machine à laver / Sèche linge">
            <label for="machine_laver_depot">
                <span>Machine à laver / Sèche linge</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="machine_laver_depot_qty">-</button>
                <input type="number" id="machine_laver_depot_qty" name="machine_laver_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="machine_laver_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/frigo.png" alt="Réfrigirateur / Congélateur">
            <label for="refrigerateur_depot">
                <span>Réfrigirateur</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="refrigerateur_depot_qty">-</button>
                <input type="number" id="refrigerateur_depot_qty" name="refrigerateur_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="refrigerateur_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2024/12/Design-sans-titre-7.png" alt="Congélateur">
            <label for="congelateur_depot">
                <span>Congélateur</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="congelateur_depot_qty">-</button>
                <input type="number" id="congelateur_depot_qty" name="congelateur_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="congelateur_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/four.png" alt="Four">
            <label for="four_depot">
                <span>Four</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="four_depot_qty">-</button>
                <input type="number" id="four_depot_qty" name="four_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="four_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/cuisiniere.png" alt="Cuisinière">
            <label for="cuisiniere_depot">
                <span>Cuisinière</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="cuisiniere_depot_qty">-</button>
                <input type="number" id="cuisiniere_depot_qty" name="cuisiniere_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="cuisiniere_depot_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Champ Ampoules / Lampes / Néons (choix multiples) -->
<div id="ampoules_depot_wrapper" style="display: none;">
    <label>Ampoules / Lampes / Néons (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Box-e1748254026514.png" alt="Box Ampoules">
            <label for="box_ampoules_depot">
                <span>Box Ampoules 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_ampoules_depot_qty">-</button>
                <input type="number" id="box_ampoules_depot_qty" name="box_ampoules_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_ampoules_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Box-e1748254026514.png" alt="Box Néons">
            <label for="box_neons_depot">
                <span>Box Néons 70L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_neons_depot_qty">-</button>
                <input type="number" id="box_neons_depot_qty" name="box_neons_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_neons_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/6.png" alt="Bac Industriel Ampoule 200L">
            <label for="bac_200l_ampoules_depot">
                <span>Bac Industriel Ampoules - Lampes Roulette 200L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_200l_ampoules_depot_qty">-</button>
                <input type="number" id="bac_200l_ampoules_depot_qty" name="bac_200l_ampoules_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_200l_ampoules_depot_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/07/Bac-industriel-a%CC%80-roulettes-550L-e1748254097172.png" alt="Bac Industriel Roulette 550L">
            <label for="bac_550l_ampoules_depot">
                <span>Bac Industriel Ampoules - Lampes Roulette 550L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_550l_ampoules_depot_qty">-</button>
                <input type="number" id="bac_550l_ampoules_depot_qty" name="bac_550l_ampoules_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_550l_ampoules_depot_qty">+</button>
            </div>
        </div>

    </div>
</div>

<!-- Champ Destruction Archives Lieu -->
<div id="destruction_archives_wrapper" style="display: none;">
    <label for="lieu_archives">Récupération à domicile</label>
    <select name="lieu_archives" id="lieu_archives">
        <option value="">Choisissez le lieu</option>
        <option value="destruction_archives_domicile">Oui</option>
        <option value="destruction_archives_depot">Non - Dépôt chez City Debarras</option>
    </select>
</div>


<!-- Champ Destruction d'archives à domicile -->
<div id="destruction_archives_domicile_wrapper" style="display: none;">
    <label>Type de Contenant pour Destruction d'archives / Documents confidentiels à domicile</label>
    <div class="choices-grid">
        <div class="benne archive-90L">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/70.png" alt="Box sécurisé 90L" width="50">
            <label for="box_90L_archives_domicile">
                <span>Box sécurisé 90L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_90L_archives_domicile_qty">-</button>
                <input type="number" id="box_90L_archives_domicile_qty" name="box_90L_archives_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_90L_archives_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne archive-130L">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/70.png" alt="Box sécurisé 130L" width="50">
            <label for="box_130L_archives_domicile">
                <span>Box sécurisé 120L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_130L_archives_domicile_qty">-</button>
                <input type="number" id="box_130L_archives_domicile_qty" name="box_130L_archives_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_130L_archives_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne archive-240L">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/241.png" alt="Box sécurisé 240L" width="50">
            <label for="box_240L_archives_domicile">
                <span>Box sécurisé 240L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_240L_archives_domicile_qty">-</button>
                <input type="number" id="box_240L_archives_domicile_qty" name="box_240L_archives_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_240L_archives_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne archive-240L">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/7.png" alt="Bac sécurisé ouvert 240L" width="50">
            <label for="bac_secure_240_ouvert_domicile">
                <span>Box sécurisé ouvert 240L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_secure_240_ouvert_domicile_qty">-</button>
                <input type="number" id="bac_secure_240_ouvert_domicile_qty" name="bac_secure_240_ouvert_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_secure_240_ouvert_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne archive-480L">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/415.png" alt="Box roulant sécurisé 480L" width="50">
            <label for="box_480L_archives_domicile">
                <span>Box roulant sécurisé 415L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_480L_archives_domicile_qty">-</button>
                <input type="number" id="box_480L_archives_domicile_qty" name="box_480L_archives_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_480L_archives_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne archive-550L">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/caisse-palette-plastique-550_01142.webp" alt="Bac sécurisé ouvert 550L" width="50">
            <label for="bac_secure_550_ouvert_domicile">
                <span>Box sécurisé ouvert 550L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_secure_550_ouvert_domicile_qty">-</button>
                <input type="number" id="bac_secure_550_ouvert_domicile_qty" name="bac_secure_550_ouvert_domicile_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_secure_550_ouvert_domicile_qty">+</button>
            </div>
        </div>

        <div class="benne archive-vrac">
            <input type="checkbox" id="vrac_domicile_estimer_archives" name="destruction_archives[]" value="vrac_domicile_estimer_archives">
            <label for="vrac_domicile_estimer_archives">
                <img src="https://citydebarras.fr/wp-content/uploads/2021/01/004-paper.png" alt="Vrac à estimer" width="50">
                <span>Vrac à estimer</span>
            </label>
        </div>
    </div>
</div>

<!-- Champ Destruction d'archives dépôt -->
<div id="destruction_archives_depot_wrapper" style="display: none;">
    <label>Contenant pour Destruction d'archives / Documents confidentiels à notre dépôt</label>
    <div class="choices-grid">
        <div class="benne archive-90L">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/70.png" alt="Box sécurisé 70L" width="50">
            <label for="box_90L_archives_depot">
                <span>Box sécurisé 90L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_90L_archives_depot_qty">-</button>
                <input type="number" id="box_90L_archives_depot_qty" name="box_90L_archives_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_90L_archives_depot_qty">+</button>
            </div>
        </div>

        <div class="benne archive-130L">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/70.png" alt="Box sécurisé 130L" width="50">
            <label for="box_130L_archives_depot">
                <span>Box sécurisé 120L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_130L_archives_depot_qty">-</button>
                <input type="number" id="box_130L_archives_depot_qty" name="box_130L_archives_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_130L_archives_depot_qty">+</button>
            </div>
        </div>

        <div class="benne archive-240L">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/241.png" alt="Box sécurisé 240L" width="50">
            <label for="box_240L_archives_depot">
                <span>Box sécurisé 240L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_240L_archives_depot_qty">-</button>
                <input type="number" id="box_240L_archives_depot_qty" name="box_240L_archives_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_240L_archives_depot_qty">+</button>
            </div>
        </div>

        <div class="benne archive-240L">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/01/7.png" alt="Bac sécurisé ouvert 240L" width="50">
            <label for="bac_secure_240_ouvert_depot">
                <span>Box sécurisé ouvert 240L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_secure_240_ouvert_depot_qty">-</button>
                <input type="number" id="bac_secure_240_ouvert_depot_qty" name="bac_secure_240_ouvert_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_secure_240_ouvert_depot_qty">+</button>
            </div>
        </div>

        <div class="benne archive-480L">
            <img src="https://citydebarras.fr/wp-content/uploads/2021/02/415.png" alt="Box roulant sécurisé 480L" width="50">
            <label for="box_480L_archives_depot">
                <span>Box roulant sécurisé 415L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="box_480L_archives_depot_qty">-</button>
                <input type="number" id="box_480L_archives_depot_qty" name="box_480L_archives_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="box_480L_archives_depot_qty">+</button>
            </div>
        </div>

        <div class="benne archive-550L">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/caisse-palette-plastique-550_01142.webp" alt="Bac sécurisé ouvert 550L" width="50">
            <label for="bac_secure_550_ouvert_depot">
                <span>Box sécurisé ouvert 550L</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="bac_secure_550_ouvert_depot_qty">-</button>
                <input type="number" id="bac_secure_550_ouvert_depot_qty" name="bac_secure_550_ouvert_depot_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="bac_secure_550_ouvert_depot_qty">+</button>
            </div>
        </div>

        <div class="benne archive-vrac">
            <input type="checkbox" id="vrac_depot_estimer_archives" name="destruction_archives[]" value="vrac_depot_estimer_archives">
            <label for="vrac_depot_estimer_archives">
                <img src="https://citydebarras.fr/wp-content/uploads/2021/01/004-paper.png" alt="Vrac à estimer" width="50">
                <span>Vrac à estimer</span>
            </label>
        </div>
    </div>
</div>

<!-- Champ Louer une benne -->
<div id="louer_benne_wrapper" style="display: none;">
    <label for="type_benne">Type de benne à louer</label>
    <select name="type_benne" id="type_benne">
        <option value="">Sélectionnez un type de benne</option>
        <option value="gravats_beton">GRAVATS, BÉTON, PARPAINGS, TUILES, TERRE, PIERRES</option>
        <option value="dnd_bennes">DÉCHETS NON DANGEREUX, BOIS, PLÂTRES, PLASTIQUES, CARTONS, MÉTAUX</option>
    </select>
</div>

<!-- Gravats Propres -->
<div id="gravats_propres_wrapper" style="display: none;">
    <label>Gravats Propres (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/Benne-a-chaine-3m_jaune.png" alt="Benne 8m3" width="50">
            <label for="benne_8m3_gravats">
                <span>Benne 8m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_8m3_gravats_qty">-</button>
                <input type="number" id="benne_8m3_gravats_qty" name="benne_8m3_gravats_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_8m3_gravats_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Déchets Non Dangereux -->
<div id="dechets_non_dangereux_wrapper" style="display: none;">
    <label>Déchets Non Dangereux (choix multiples)</label>
    <div class="choices-grid">
        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/8bleue.png" alt="Benne 8m3" width="50">
            <label for="benne_8m3_dechets">
                <span>Benne 8m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_8m3_dechets_qty">-</button>
                <input type="number" id="benne_8m3_dechets_qty" name="benne_8m3_dechets_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_8m3_dechets_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/10rose.png" alt="Benne 10m3" width="50">
            <label for="benne_10m3_dechets">
                <span>Benne 10m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_10m3_dechets_qty">-</button>
                <input type="number" id="benne_10m3_dechets_qty" name="benne_10m3_dechets_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_10m3_dechets_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2025/05/15jaune.png" alt="Benne 15m3" width="50">
            <label for="benne_15m3_dechets">
                <span>Benne 15m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_15m3_dechets_qty">-</button>
                <input type="number" id="benne_15m3_dechets_qty" name="benne_15m3_dechets_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_15m3_dechets_qty">+</button>
            </div>
        </div>

        <div class="benne">
            <img src="https://citydebarras.fr/wp-content/uploads/2020/09/4.png" alt="Benne 30m3" width="50">
            <label for="benne_30m3_dechets">
                <span>Benne 30m3</span>
            </label>
            <div class="quantity-selector">
                <button type="button" class="qty-minus" data-target="benne_30m3_dechets_qty">-</button>
                <input type="number" id="benne_30m3_dechets_qty" name="benne_30m3_dechets_qty" value="0" min="0" readonly>
                <button type="button" class="qty-plus" data-target="benne_30m3_dechets_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Champ particulier/professionnel -->


<div id="volume-section" style="display: none;">
    <h2>Estimez le volume dont vous avez besoin pour votre débarras</h2>

    <div class="accordion">
        <!-- Bureau Section -->
        <div class="accordion-item">
            <div class="accordion-header" onclick="toggleAccordion('bureau')">
                <h3>Bureau</h3>
                <span class="accordion-icon">+</span>
            </div>
            <div class="accordion-body" id="bureau" style="display: none;">
                <!-- Existing Products -->
                <div class="furniture-item">
                    <span>Armoire à rideaux haute 1,20m</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="armoire_rideaux_1m2_qty">-</button>
                        <input type="number" id="armoire_rideaux_1m2_qty" class="volume-control" value="0" data-volume="1.20" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="armoire_rideaux_1m2_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Armoire portes battantes haute 1,00m</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="armoire_battantes_1m_qty">-</button>
                        <input type="number" id="armoire_battantes_1m_qty" class="volume-control" value="0" data-volume="1.00" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="armoire_battantes_1m_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Bibliothèque</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="bibliotheque_qty">-</button>
                        <input type="number" id="bibliotheque_qty" class="volume-control" value="0" data-volume="0.80" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="bibliotheque_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Bureau</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="bureau_qty">-</button>
                        <input type="number" id="bureau_qty" class="volume-control" value="0" data-volume="1.30" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="bureau_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Bureau avec retour</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="bureau_retour_qty">-</button>
                        <input type="number" id="bureau_retour_qty" class="volume-control" value="0" data-volume="2.00" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="bureau_retour_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Caisson</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="caisson_qty">-</button>
                        <input type="number" id="caisson_qty" class="volume-control" value="0" data-volume="0.30" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="caisson_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Canapé 2 places</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="canape_2p_qty">-</button>
                        <input type="number" id="canape_2p_qty" class="volume-control" value="0" data-volume="2.25" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="canape_2p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Canapé 3 places</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="canape_3p_qty">-</button>
                        <input type="number" id="canape_3p_qty" class="volume-control" value="0" data-volume="2.96" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="canape_3p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Canapé d'angle</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="canape_angle_qty">-</button>
                        <input type="number" id="canape_angle_qty" class="volume-control" value="0" data-volume="2.08" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="canape_angle_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Chauffeuse 1 place</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="chauffeuse_1p_qty">-</button>
                        <input type="number" id="chauffeuse_1p_qty" class="volume-control" value="0" data-volume="0.40" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="chauffeuse_1p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Étagère métallique rayonnage</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="etagere_metal_qty">-</button>
                        <input type="number" id="etagere_metal_qty" class="volume-control" value="0" data-volume="2.00" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="etagere_metal_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Meuble bas portes battantes</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="meuble_battantes_qty">-</button>
                        <input type="number" id="meuble_battantes_qty" class="volume-control" value="0" data-volume="0.40" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="meuble_battantes_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Meuble TV</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="meuble_tv_qty">-</button>
                        <input type="number" id="meuble_tv_qty" class="volume-control" value="0" data-volume="1.44" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="meuble_tv_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Meuble bas portes coulissantes</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="meuble_coulissantes_qty">-</button>
                        <input type="number" id="meuble_coulissantes_qty" class="volume-control" value="0" data-volume="0.50" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="meuble_coulissantes_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Penderie 1 colonne</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="penderie_1col_qty">-</button>
                        <input type="number" id="penderie_1col_qty" class="volume-control" value="0" data-volume="0.30" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="penderie_1col_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Penderie 2 colonnes</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="penderie_2col_qty">-</button>
                        <input type="number" id="penderie_2col_qty" class="volume-control" value="0" data-volume="0.60" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="penderie_2col_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Penderie 3 colonnes</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="penderie_3col_qty">-</button>
                        <input type="number" id="penderie_3col_qty" class="volume-control" value="0" data-volume="0.90" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="penderie_3col_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Siège plastique empilable</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="siege_plastique_qty">-</button>
                        <input type="number" id="siege_plastique_qty" class="volume-control" value="0" data-volume="0.10" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="siege_plastique_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Table 4 pieds</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="table_4pieds_qty">-</button>
                        <input type="number" id="table_4pieds_qty" class="volume-control" value="0" data-volume="0.60" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="table_4pieds_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Table basse</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="table_basse_qty">-</button>
                        <input type="number" id="table_basse_qty" class="volume-control" value="0" data-volume="0.30" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="table_basse_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Tableau d'écriture</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="tableau_ecriture_qty">-</button>
                        <input type="number" id="tableau_ecriture_qty" class="volume-control" value="0" data-volume="0.10" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="tableau_ecriture_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Tabouret haut</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="tabouret_haut_qty">-</button>
                        <input type="number" id="tabouret_haut_qty" class="volume-control" value="0" data-volume="0.30" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="tabouret_haut_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Armoire 2 portes</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="armoire_2portes_qty">-</button>
                        <input type="number" id="armoire_2portes_qty" class="volume-control" value="0" data-volume="2.24" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="armoire_2portes_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Armoire 3 portes</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="armoire_3portes_qty">-</button>
                        <input type="number" id="armoire_3portes_qty" class="volume-control" value="0" data-volume="2.94" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="armoire_3portes_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Armoire 4 portes</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="armoire_4portes_qty">-</button>
                        <input type="number" id="armoire_4portes_qty" class="volume-control" value="0" data-volume="3.45" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="armoire_4portes_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Banque d'accueil</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="banque_accueil_qty">-</button>
                        <input type="number" id="banque_accueil_qty" class="volume-control" value="0" data-volume="2.30" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="banque_accueil_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Fauteuil accueil</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="fauteuil_accueil_qty">-</button>
                        <input type="number" id="fauteuil_accueil_qty" class="volume-control" value="0" data-volume="0.20" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="fauteuil_accueil_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Siège cafétéria</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="siege_cafeteria_qty">-</button>
                        <input type="number" id="siege_cafeteria_qty" class="volume-control" value="0" data-volume="0.10" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="siege_cafeteria_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Siège direction</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="siege_direction_qty">-</button>
                        <input type="number" id="siege_direction_qty" class="volume-control" value="0" data-volume="0.60" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="siege_direction_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Siège opérateur</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="siege_operateur_qty">-</button>
                        <input type="number" id="siege_operateur_qty" class="volume-control" value="0" data-volume="0.40" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="siege_operateur_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Siège poutre 4 places</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="siege_poutre_qty">-</button>
                        <input type="number" id="siege_poutre_qty" class="volume-control" value="0" data-volume="1.40" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="siege_poutre_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Siège réunion</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="siege_reunion_qty">-</button>
                        <input type="number" id="siege_reunion_qty" class="volume-control" value="0" data-volume="0.20" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="siege_reunion_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Siège visiteur</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="siege_visiteur_qty">-</button>
                        <input type="number" id="siege_visiteur_qty" class="volume-control" value="0" data-volume="0.20" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="siege_visiteur_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Table de réunion 12 places</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="table_reunion_12_qty">-</button>
                        <input type="number" id="table_reunion_12_qty" class="volume-control" value="0" data-volume="4.50" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="table_reunion_12_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Table de réunion 8 places</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="table_reunion_8_qty">-</button>
                        <input type="number" id="table_reunion_8_qty" class="volume-control" value="0" data-volume="1.70" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="table_reunion_8_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Table de réunion 4 places</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="table_reunion_4_qty">-</button>
                        <input type="number" id="table_reunion_4_qty" class="volume-control" value="0" data-volume="1.00" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="table_reunion_4_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Armoire à rideaux basse</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="armoire_rideaux_basse_qty">-</button>
                        <input type="number" id="armoire_rideaux_basse_qty" class="volume-control" value="0" data-volume="0.80" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="armoire_rideaux_basse_qty">+</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hébergement Section -->
        <div class="accordion-item">
            <div class="accordion-header" onclick="toggleAccordion('hebergement')">
                <h3>Hébergement</h3>
                <span class="accordion-icon">+</span>
            </div>
            <div class="accordion-body" id="hebergement" style="display: none;">
                <div class="furniture-item">
                    <span>Cadre de lit 1 place</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="cadre_lit_1p_qty">-</button>
                        <input type="number" id="cadre_lit_1p_qty" class="volume-control" value="0" data-volume="0.90" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="cadre_lit_1p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Cadre de lit 2 places</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="cadre_lit_2p_qty">-</button>
                        <input type="number" id="cadre_lit_2p_qty" class="volume-control" value="0" data-volume="1.80" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="cadre_lit_2p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Chevet</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="chevet_qty">-</button>
                        <input type="number" id="chevet_qty" class="volume-control" value="0" data-volume="0.10" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="chevet_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Lit superposé 1 place</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="lit_superpose_1p_qty">-</button>
                        <input type="number" id="lit_superpose_1p_qty" class="volume-control" value="0" data-volume="3.60" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="lit_superpose_1p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Matelas 1 place</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="matelas_1p_qty">-</button>
                        <input type="number" id="matelas_1p_qty" class="volume-control" value="0" data-volume="0.50" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="matelas_1p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Matelas 2 places</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="matelas_2p_qty">-</button>
                        <input type="number" id="matelas_2p_qty" class="volume-control" value="0" data-volume="1" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="matelas_2p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Sommier 1 place</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="sommier_1p_qty">-</button>
                        <input type="number" id="sommier_1p_qty" class="volume-control" value="0" data-volume="0.50" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="sommier_1p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Sommier 2 places</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="sommier_2p_qty">-</button>
                        <input type="number" id="sommier_2p_qty" class="volume-control" value="0" data-volume="1.00" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="sommier_2p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Couette - oreiller - duvet</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="couette_oreiller_duvet_qty">-</button>
                        <input type="number" id="couette_oreiller_duvet_qty" class="volume-control" value="0" data-volume="0.50" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="couette_oreiller_duvet_qty">+</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scolaire Section -->
        <div class="accordion-item">
            <div class="accordion-header" onclick="toggleAccordion('scolaire')">
                <h3>Scolaire</h3>
                <span class="accordion-icon">+</span>
            </div>
            <div class="accordion-body" id="scolaire" style="display: none;">
                <div class="furniture-item">
                    <span>Armoire haute porte battante</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="armoire_haute_qty">-</button>
                        <input type="number" id="armoire_haute_qty" class="volume-control" value="0" data-volume="0.90" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="armoire_haute_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Bac à livre</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="bac_livre_qty">-</button>
                        <input type="number" id="bac_livre_qty" class="volume-control" value="0" data-volume="0.30" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="bac_livre_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Bureau administratif</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="bureau_admin_qty">-</button>
                        <input type="number" id="bureau_admin_qty" class="volume-control" value="0" data-volume="1.30" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="bureau_admin_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Bureau 1 place</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="bureau_1p_qty">-</button>
                        <input type="number" id="bureau_1p_qty" class="volume-control" value="0" data-volume="0.20" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="bureau_1p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Bureau 2 places</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="bureau_2p_qty">-</button>
                        <input type="number" id="bureau_2p_qty" class="volume-control" value="0" data-volume="1.00" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="bureau_2p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Chaise</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="chaise_qty">-</button>
                        <input type="number" id="chaise_qty" class="volume-control" value="0" data-volume="0.20" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="chaise_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Lit à barreaux</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="lit_barreaux_qty">-</button>
                        <input type="number" id="lit_barreaux_qty" class="volume-control" value="0" data-volume="1.20" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="lit_barreaux_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Lit à barreaux 2 places</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="lit_barreaux_2p_qty">-</button>
                        <input type="number" id="lit_barreaux_2p_qty" class="volume-control" value="0" data-volume="1.50" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="lit_barreaux_2p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Meuble à dessin</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="meuble_dessin_qty">-</button>
                        <input type="number" id="meuble_dessin_qty" class="volume-control" value="0" data-volume="0.40" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="meuble_dessin_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Meuble bas porte coulissantes</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="meuble_bas_coulissantes_qty">-</button>
                        <input type="number" id="meuble_bas_coulissantes_qty" class="volume-control" value="0" data-volume="0.30" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="meuble_bas_coulissantes_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Meuble rangement bas</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="meuble_rangement_bas_qty">-</button>
                        <input type="number" id="meuble_rangement_bas_qty" class="volume-control" value="0" data-volume="0.30" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="meuble_rangement_bas_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Porte cartables</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="porte_cartables_qty">-</button>
                        <input type="number" id="porte_cartables_qty" class="volume-control" value="0" data-volume="0.80" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="porte_cartables_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Table à langer 2 places</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="table_langer_2p_qty">-</button>
                        <input type="number" id="table_langer_2p_qty" class="volume-control" value="0" data-volume="1.90" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="table_langer_2p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Table maternelle</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="table_maternelle_qty">-</button>
                        <input type="number" id="table_maternelle_qty" class="volume-control" value="0" data-volume="0.10" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="table_maternelle_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Table maternelle 2 places</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="table_maternelle_2p_qty">-</button>
                        <input type="number" id="table_maternelle_2p_qty" class="volume-control" value="0" data-volume="0.20" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="table_maternelle_2p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Vestiaires sur banc</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="vestiaires_banc_qty">-</button>
                        <input type="number" id="vestiaires_banc_qty" class="volume-control" value="0" data-volume="1.40" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="vestiaires_banc_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Bureau de maître</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="bureau_maitre_qty">-</button>
                        <input type="number" id="bureau_maitre_qty" class="volume-control" value="0" data-volume="1.30" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="bureau_maitre_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Chaise administration</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="chaise_admin_qty">-</button>
                        <input type="number" id="chaise_admin_qty" class="volume-control" value="0" data-volume="0.40" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="chaise_admin_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Chaise primaire</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="chaise_primaire_qty">-</button>
                        <input type="number" id="chaise_primaire_qty" class="volume-control" value="0" data-volume="0.20" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="chaise_primaire_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Fauteuil de direction</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="fauteuil_direction_qty">-</button>
                        <input type="number" id="fauteuil_direction_qty" class="volume-control" value="0" data-volume="0.60" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="fauteuil_direction_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Meuble à plan</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="meuble_plan_qty">-</button>
                        <input type="number" id="meuble_plan_qty" class="volume-control" value="0" data-volume="0.80" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="meuble_plan_qty">+</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restauration Section -->
        <div class="accordion-item">
            <div class="accordion-header" onclick="toggleAccordion('restauration')">
                <h3>Restauration</h3>
                <span class="accordion-icon">+</span>
            </div>
            <div class="accordion-body" id="restauration" style="display: none;">
                <div class="furniture-item">
                    <span>Buffet</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="buffet_qty">-</button>
                        <input type="number" id="buffet_qty" class="volume-control" value="0" data-volume="0.50" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="buffet_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Claustra</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="claustra_qty">-</button>
                        <input type="number" id="claustra_qty" class="volume-control" value="0" data-volume="0.60" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="claustra_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Distributeur plateaux/couverts</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="distributeur_plateaux_qty">-</button>
                        <input type="number" id="distributeur_plateaux_qty" class="volume-control" value="0" data-volume="0.50" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="distributeur_plateaux_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Mange debout</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="mange_debout_qty">-</button>
                        <input type="number" id="mange_debout_qty" class="volume-control" value="0" data-volume="0.40" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="mange_debout_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Meuble range serviettes</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="meuble_serviettes_qty">-</button>
                        <input type="number" id="meuble_serviettes_qty" class="volume-control" value="0" data-volume="0.20" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="meuble_serviettes_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Table de restauration 4 places</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="table_restauration_4p_qty">-</button>
                        <input type="number" id="table_restauration_4p_qty" class="volume-control" value="0" data-volume="0.50" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="table_restauration_4p_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Chaise restauration</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="chaise_restauration_qty">-</button>
                        <input type="number" id="chaise_restauration_qty" class="volume-control" value="0" data-volume="0.20" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="chaise_restauration_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Chaise restauration rembourrée</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="chaise_restauration_rembourree_qty">-</button>
                        <input type="number" id="chaise_restauration_rembourree_qty" class="volume-control" value="0" data-volume="0.20" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="chaise_restauration_rembourree_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Chariot à glissière</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="chariot_glissiere_qty">-</button>
                        <input type="number" id="chariot_glissiere_qty" class="volume-control" value="0" data-volume="0.60" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="chariot_glissiere_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Chariot plateau en bois</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="chariot_plateau_bois_qty">-</button>
                        <input type="number" id="chariot_plateau_bois_qty" class="volume-control" value="0" data-volume="0.50" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="chariot_plateau_bois_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Table inox de cuisine</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="table_inox_cuisine_qty">-</button>
                        <input type="number" id="table_inox_cuisine_qty" class="volume-control" value="0" data-volume="0.70" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="table_inox_cuisine_qty">+</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Médical Section -->
        <div class="accordion-item">
            <div class="accordion-header" onclick="toggleAccordion('medical')">
                <h3>Médical</h3>
                <span class="accordion-icon">+</span>
            </div>
            <div class="accordion-body" id="medical" style="display: none;">
                <div class="furniture-item">
                    <span>Fauteuil de repos</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="fauteuil_repos_qty">-</button>
                        <input type="number" id="fauteuil_repos_qty" class="volume-control" value="0" data-volume="0.50" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="fauteuil_repos_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Table de lit</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="table_lit_qty">-</button>
                        <input type="number" id="table_lit_qty" class="volume-control" value="0" data-volume="0.30" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="table_lit_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Table de chambre</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="table_chambre_qty">-</button>
                        <input type="number" id="table_chambre_qty" class="volume-control" value="0" data-volume="0.30" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="table_chambre_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Divan d'examen</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="divan_examen_qty">-</button>
                        <input type="number" id="divan_examen_qty" class="volume-control" value="0" data-volume="0.90" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="divan_examen_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Guéridon inox</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="gueridon_inox_qty">-</button>
                        <input type="number" id="gueridon_inox_qty" class="volume-control" value="0" data-volume="0.50" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="gueridon_inox_qty">+</button>
                    </div>
                </div>
                <div class="furniture-item">
                    <span>Chariot armoire</span>
                    <div class="quantity-selector">
                        <button type="button" class="qty-minus volume-control" data-target="chariot_armoire_qty">-</button>
                        <input type="number" id="chariot_armoire_qty" class="volume-control" value="0" data-volume="1.50" readonly>
                        <button type="button" class="qty-plus volume-control" data-target="chariot_armoire_qty">+</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agencement Section -->
<div class="accordion-item">
    <div class="accordion-header" onclick="toggleAccordion('agencement')">
        <h3>Agencement</h3>
        <span class="accordion-icon">+</span>
    </div>
    <div class="accordion-body" id="agencement" style="display: none;">
        <div class="furniture-item">
            <span>Armoire en bois avec porte (haute)</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="armoire_bois_haute_qty">-</button>
                <input type="number" id="armoire_bois_haute_qty" class="volume-control" value="0" data-volume="1.20" readonly>
                <button type="button" class="qty-plus volume-control" data-target="armoire_bois_haute_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Banc</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="banc_qty">-</button>
                <input type="number" id="banc_qty" class="volume-control" value="0" data-volume="0.30" readonly>
                <button type="button" class="qty-plus volume-control" data-target="banc_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Banc avec dossier</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="banc_dossier_qty">-</button>
                <input type="number" id="banc_dossier_qty" class="volume-control" value="0" data-volume="0.80" readonly>
                <button type="button" class="qty-plus volume-control" data-target="banc_dossier_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Banc/banquette rembourrée</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="banc_rembourre_qty">-</button>
                <input type="number" id="banc_rembourre_qty" class="volume-control" value="0" data-volume="0.80" readonly>
                <button type="button" class="qty-plus volume-control" data-target="banc_rembourre_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Bibliothèque en bois (sans porte)</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="bibliotheque_bois_qty">-</button>
                <input type="number" id="bibliotheque_bois_qty" class="volume-control" value="0" data-volume="1.00" readonly>
                <button type="button" class="qty-plus volume-control" data-target="bibliotheque_bois_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Petite bibliothèque</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="petite_bibliotheque_qty">-</button>
                <input type="number" id="petite_bibliotheque_qty" class="volume-control" value="0" data-volume="2.08" readonly>
                <button type="button" class="qty-plus volume-control" data-target="petite_bibliotheque_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Grande bibliothèque</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="grande_bibliotheque_qty">-</button>
                <input type="number" id="grande_bibliotheque_qty" class="volume-control" value="0" data-volume="2.40" readonly>
                <button type="button" class="qty-plus volume-control" data-target="grande_bibliotheque_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Bureau (sans caisson)</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="bureau_sans_caisson_qty">-</button>
                <input type="number" id="bureau_sans_caisson_qty" class="volume-control" value="0" data-volume="1.30" readonly>
                <button type="button" class="qty-plus volume-control" data-target="bureau_sans_caisson_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Commode</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="commode_qty">-</button>
                <input type="number" id="commode_qty" class="volume-control" value="0" data-volume="2.88" readonly>
                <button type="button" class="qty-plus volume-control" data-target="commode_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Meuble de cuisine/salle de bain (bas)</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="meuble_cuisine_bas_qty">-</button>
                <input type="number" id="meuble_cuisine_bas_qty" class="volume-control" value="0" data-volume="0.50" readonly>
                <button type="button" class="qty-plus volume-control" data-target="meuble_cuisine_bas_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Meuble de cuisine/salle de bain (haut)</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="meuble_cuisine_haut_qty">-</button>
                <input type="number" id="meuble_cuisine_haut_qty" class="volume-control" value="0" data-volume="0.30" readonly>
                <button type="button" class="qty-plus volume-control" data-target="meuble_cuisine_haut_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Placard avec portes (hauteur 2)</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="placard_hauteur2_qty">-</button>
                <input type="number" id="placard_hauteur2_qty" class="volume-control" value="0" data-volume="1.30" readonly>
                <button type="button" class="qty-plus volume-control" data-target="placard_hauteur2_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Table</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="table_qty">-</button>
                <input type="number" id="table_qty" class="volume-control" value="0" data-volume="1.30" readonly>
                <button type="button" class="qty-plus volume-control" data-target="table_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Tête de lit sans table de chevet</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="tete_lit_sans_chevet_qty">-</button>
                <input type="number" id="tete_lit_sans_chevet_qty" class="volume-control" value="0" data-volume="0.10" readonly>
                <button type="button" class="qty-plus volume-control" data-target="tete_lit_sans_chevet_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Buffet bas</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="buffet_bas_qty">-</button>
                <input type="number" id="buffet_bas_qty" class="volume-control" value="0" data-volume="0.57" readonly>
                <button type="button" class="qty-plus volume-control" data-target="buffet_bas_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Buffet haut</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="buffet_haut_qty">-</button>
                <input type="number" id="buffet_haut_qty" class="volume-control" value="0" data-volume="0.87" readonly>
                <button type="button" class="qty-plus volume-control" data-target="buffet_haut_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Caisson</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="caisson_agencement_qty">-</button>
                <input type="number" id="caisson_agencement_qty" class="volume-control" value="0" data-volume="0.22" readonly>
                <button type="button" class="qty-plus volume-control" data-target="caisson_agencement_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Commodes Hautes</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="commodes_hautes_qty">-</button>
                <input type="number" id="commodes_hautes_qty" class="volume-control" value="0" data-volume="0.70" readonly>
                <button type="button" class="qty-plus volume-control" data-target="commodes_hautes_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Desserte à roulettes</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="desserte_roulettes_qty">-</button>
                <input type="number" id="desserte_roulettes_qty" class="volume-control" value="0" data-volume="0.59" readonly>
                <button type="button" class="qty-plus volume-control" data-target="desserte_roulettes_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Échelles</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="echelles_qty">-</button>
                <input type="number" id="echelles_qty" class="volume-control" value="0" data-volume="0" readonly>
                <button type="button" class="qty-plus volume-control" data-target="echelles_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Portants</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="portants_qty">-</button>
                <input type="number" id="portants_qty" class="volume-control" value="0" data-volume="0.82" readonly>
                <button type="button" class="qty-plus volume-control" data-target="portants_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Présentoir bouteilles de vins</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="presentoir_vins_qty">-</button>
                <input type="number" id="presentoir_vins_qty" class="volume-control" value="0" data-volume="0.66" readonly>
                <button type="button" class="qty-plus volume-control" data-target="presentoir_vins_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Tablettes en bois</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="tablettes_bois_qty">-</button>
                <input type="number" id="tablettes_bois_qty" class="volume-control" value="0" data-volume="0.02" readonly>
                <button type="button" class="qty-plus volume-control" data-target="tablettes_bois_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Tablettes en verre</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="tablettes_verre_qty">-</button>
                <input type="number" id="tablettes_verre_qty" class="volume-control" value="0" data-volume="0" readonly>
                <button type="button" class="qty-plus volume-control" data-target="tablettes_verre_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Banque d'accueil/comptoir</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="banque_comptoir_qty">-</button>
                <input type="number" id="banque_comptoir_qty" class="volume-control" value="0" data-volume="1.40" readonly>
                <button type="button" class="qty-plus volume-control" data-target="banque_comptoir_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Chevalet d'écriture</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="chevalet_ecriture_qty">-</button>
                <input type="number" id="chevalet_ecriture_qty" class="volume-control" value="0" data-volume="0.40" readonly>
                <button type="button" class="qty-plus volume-control" data-target="chevalet_ecriture_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Comptoir de bar</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="comptoir_bar_qty">-</button>
                <input type="number" id="comptoir_bar_qty" class="volume-control" value="0" data-volume="1.40" readonly>
                <button type="button" class="qty-plus volume-control" data-target="comptoir_bar_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Table de réunion</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="table_reunion_qty">-</button>
                <input type="number" id="table_reunion_qty" class="volume-control" value="0" data-volume="1.30" readonly>
                <button type="button" class="qty-plus volume-control" data-target="table_reunion_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Tableau d'écriture</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="tableau_ecriture_qty">-</button>
                <input type="number" id="tableau_ecriture_qty" class="volume-control" value="0" data-volume="0.10" readonly>
                <button type="button" class="qty-plus volume-control" data-target="tableau_ecriture_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Vitrine de présentation alimentaire (non réfrigérée)</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="vitrine_presentation_qty">-</button>
                <input type="number" id="vitrine_presentation_qty" class="volume-control" value="0" data-volume="1.00" readonly>
                <button type="button" class="qty-plus volume-control" data-target="vitrine_presentation_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Vitrine haute en verre</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="vitrine_verre_qty">-</button>
                <input type="number" id="vitrine_verre_qty" class="volume-control" value="0" data-volume="1.00" readonly>
                <button type="button" class="qty-plus volume-control" data-target="vitrine_verre_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>PLV basse</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="plv_basse_qty">-</button>
                <input type="number" id="plv_basse_qty" class="volume-control" value="0" data-volume="0.80" readonly>
                <button type="button" class="qty-plus volume-control" data-target="plv_basse_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>PLV haute</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="plv_haute_qty">-</button>
                <input type="number" id="plv_haute_qty" class="volume-control" value="0" data-volume="1.20" readonly>
                <button type="button" class="qty-plus volume-control" data-target="plv_haute_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Colonnes rainurées</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="colonnes_rainurees_qty">-</button>
                <input type="number" id="colonnes_rainurees_qty" class="volume-control" value="0" data-volume="0.22" readonly>
                <button type="button" class="qty-plus volume-control" data-target="colonnes_rainurees_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Comptoir</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="comptoir_qty">-</button>
                <input type="number" id="comptoir_qty" class="volume-control" value="0" data-volume="0.61" readonly>
                <button type="button" class="qty-plus volume-control" data-target="comptoir_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Gondoles métalliques</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="gondoles_metal_qty">-</button>
                <input type="number" id="gondoles_metal_qty" class="volume-control" value="0" data-volume="1.16" readonly>
                <button type="button" class="qty-plus volume-control" data-target="gondoles_metal_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Panneaux d'affichage</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="panneaux_affichage_qty">-</button>
                <input type="number" id="panneaux_affichage_qty" class="volume-control" value="0" data-volume="0.23" readonly>
                <button type="button" class="qty-plus volume-control" data-target="panneaux_affichage_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Podiums 3 niveaux</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="podiums_3niveaux_qty">-</button>
                <input type="number" id="podiums_3niveaux_qty" class="volume-control" value="0" data-volume="1.29" readonly>
                <button type="button" class="qty-plus volume-control" data-target="podiums_3niveaux_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Présentoirs rainurés</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="presentoirs_rainures_qty">-</button>
                <input type="number" id="presentoirs_rainures_qty" class="volume-control" value="0" data-volume="0.90" readonly>
                <button type="button" class="qty-plus volume-control" data-target="presentoirs_rainures_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Vitrine comptoir</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="vitrine_comptoir_qty">-</button>
                <input type="number" id="vitrine_comptoir_qty" class="volume-control" value="0" data-volume="0.37" readonly>
                <button type="button" class="qty-plus volume-control" data-target="vitrine_comptoir_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Vitrines Hautes</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="vitrines_hautes_qty">-</button>
                <input type="number" id="vitrines_hautes_qty" class="volume-control" value="0" data-volume="0.49" readonly>
                <button type="button" class="qty-plus volume-control" data-target="vitrines_hautes_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Tapis / Rideaux et accessoires Section -->
<div class="accordion-item">
    <div class="accordion-header" onclick="toggleAccordion('tapis_rideaux')">
        <h3>Tapis / Rideaux et accessoires</h3>
        <span class="accordion-icon">+</span>
    </div>
    <div class="accordion-body" id="tapis_rideaux" style="display: none;">
        <div class="furniture-item">
            <span>Tapis anti-poussieres (m2)</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="tapis_anti_poussieres_qty">-</button>
                <input type="number" id="tapis_anti_poussieres_qty" class="volume-control" value="0" data-volume="0.01" readonly>
                <button type="button" class="qty-plus volume-control" data-target="tapis_anti_poussieres_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Tapis Brosse (m2)</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="tapis_brosse_qty">-</button>
                <input type="number" id="tapis_brosse_qty" class="volume-control" value="0" data-volume="0.03" readonly>
                <button type="button" class="qty-plus volume-control" data-target="tapis_brosse_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Tapis Caoutchouc (m2)</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="tapis_caoutchouc_qty">-</button>
                <input type="number" id="tapis_caoutchouc_qty" class="volume-control" value="0" data-volume="0.02" readonly>
                <button type="button" class="qty-plus volume-control" data-target="tapis_caoutchouc_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Tapis aluminium (m2)</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="tapis_aluminium_qty">-</button>
                <input type="number" id="tapis_aluminium_qty" class="volume-control" value="0" data-volume="0.01" readonly>
                <button type="button" class="qty-plus volume-control" data-target="tapis_aluminium_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Rideaux (m2)</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="rideaux_qty">-</button>
                <input type="number" id="rideaux_qty" class="volume-control" value="0" data-volume="0" readonly>
                <button type="button" class="qty-plus volume-control" data-target="rideaux_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Tringles (m)</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="tringles_qty">-</button>
                <input type="number" id="tringles_qty" class="volume-control" value="0" data-volume="0" readonly>
                <button type="button" class="qty-plus volume-control" data-target="tringles_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>Voilages (m2)</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="voilages_qty">-</button>
                <input type="number" id="voilages_qty" class="volume-control" value="0" data-volume="0" readonly>
                <button type="button" class="qty-plus volume-control" data-target="voilages_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Cuisine Section -->
<div class="accordion-item">
    <div class="accordion-header" onclick="toggleAccordion('cuisine')">
        <h3>Cuisine</h3>
        <span class="accordion-icon">+</span>
    </div>
    <div class="accordion-body" id="cuisine" style="display: none;">
        <div class="furniture-item">
            <span>RÉFRIGÉRATEUR</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="refrigerateur_qty">-</button>
                <input type="number" id="refrigerateur_qty" class="volume-control" value="0" data-volume="1.12" readonly>
                <button type="button" class="qty-plus volume-control" data-target="refrigerateur_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>FOUR</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="four_qty">-</button>
                <input type="number" id="four_qty" class="volume-control" value="0" data-volume="0.56" readonly>
                <button type="button" class="qty-plus volume-control" data-target="four_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>GAZINIÈRE</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="gaziniere_qty">-</button>
                <input type="number" id="gaziniere_qty" class="volume-control" value="0" data-volume="0.64" readonly>
                <button type="button" class="qty-plus volume-control" data-target="gaziniere_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>CONGÉLATEUR COFFRE</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="congelateur_coffre_qty">-</button>
                <input type="number" id="congelateur_coffre_qty" class="volume-control" value="0" data-volume="0.24" readonly>
                <button type="button" class="qty-plus volume-control" data-target="congelateur_coffre_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>ASPIRATEUR</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="aspirateur_qty">-</button>
                <input type="number" id="aspirateur_qty" class="volume-control" value="0" data-volume="0.32" readonly>
                <button type="button" class="qty-plus volume-control" data-target="aspirateur_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>RÉFRIGÉRATEUR AMÉRICAIN</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="refrigerateur_americain_qty">-</button>
                <input type="number" id="refrigerateur_americain_qty" class="volume-control" value="0" data-volume="1.84" readonly>
                <button type="button" class="qty-plus volume-control" data-target="refrigerateur_americain_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>MICRO ONDES</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="micro_ondes_qty">-</button>
                <input type="number" id="micro_ondes_qty" class="volume-control" value="0" data-volume="0.40" readonly>
                <button type="button" class="qty-plus volume-control" data-target="micro_ondes_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>LAVE VAISSELLE</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="lave_vaisselle_qty">-</button>
                <input type="number" id="lave_vaisselle_qty" class="volume-control" value="0" data-volume="0.80" readonly>
                <button type="button" class="qty-plus volume-control" data-target="lave_vaisselle_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>SÈCHE LINGE</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="seche_linge_qty">-</button>
                <input type="number" id="seche_linge_qty" class="volume-control" value="0" data-volume="0.80" readonly>
                <button type="button" class="qty-plus volume-control" data-target="seche_linge_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>LAVE LINGE</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="lave_linge_qty">-</button>
                <input type="number" id="lave_linge_qty" class="volume-control" value="0" data-volume="0.80" readonly>
                <button type="button" class="qty-plus volume-control" data-target="lave_linge_qty">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Divers Section -->
<div class="accordion-item">
    <div class="accordion-header" onclick="toggleAccordion('divers')">
        <h3>Divers</h3>
        <span class="accordion-icon">+</span>
    </div>
    <div class="accordion-body" id="divers" style="display: none;">
        <div class="furniture-item">
            <span>POUSSETTE</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="poussette_qty">-</button>
                <input type="number" id="poussette_qty" class="volume-control" value="0" data-volume="0.40" readonly>
                <button type="button" class="qty-plus volume-control" data-target="poussette_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>CARTON DE DÉMÉNAGEMENT</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="carton_demenagement_qty">-</button>
                <input type="number" id="carton_demenagement_qty" class="volume-control" value="0" data-volume="0.10" readonly>
                <button type="button" class="qty-plus volume-control" data-target="carton_demenagement_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>TONDEUSE</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="tondeuse_qty">-</button>
                <input type="number" id="tondeuse_qty" class="volume-control" value="0" data-volume="0.80" readonly>
                <button type="button" class="qty-plus volume-control" data-target="tondeuse_qty">+</button>
            </div>
        </div>
        <div class="furniture-item">
            <span>VÉLO</span>
            <div class="quantity-selector">
                <button type="button" class="qty-minus volume-control" data-target="velo_qty">-</button>
                <input type="number" id="velo_qty" class="volume-control" value="0" data-volume="0.40" readonly>
                <button type="button" class="qty-plus volume-control" data-target="velo_qty">+</button>
            </div>
        </div>
    </div>
</div>



    <!-- Volume Estimé -->
    <div id="total-volume">
        <h3>Le volume de votre déménagement est estimé à: <span id="volume-result">0.00</span> m³</h3>
    </div>


<div class="camions">
    <!-- Camion 1 -->
    <div class="camion camion-container" id="camion1" data-volume="3">
        <img src="images/3m3.png" alt="Camion 3m³">
        <h4>Camion 3m³</h4>
        <p class="fs-sm mb-36">
            Idéal pour débarrasser <span class="fw-bold">quelques cartons ou petits meubles</span>.
        </p>
        <div class="fs-sm">
            &#128230; Environ 24 cartons (50x40x40).<br>
            &#9874; Charge maximum : 915kg.
        </div>
        <button type="button" class="choisir-camion" onclick="selectCamion('camion1')">Choisir ce camion</button>
    </div>

    <!-- Camion 2 -->
    <div class="camion camion-container" id="camion2" data-volume="5">
        <img src="images/5m3.png" alt="Camion 5m³">
        <h4>Camion 5m³</h4>
        <p class="fs-sm mb-36">
            Idéal pour débarrasser <span class="fw-bold">une cave, une studette ou un petit appartement</span>.
        </p>
        <div class="fs-sm">
            &#128230; Environ 40 cartons (50x40x40).<br>
            &#9874; Charge maximum : 1000kg.
        </div>
        <button type="button" class="choisir-camion" onclick="selectCamion('camion2')">Choisir ce camion</button>
    </div>

    <!-- Camion 3 -->
    <div class="camion camion-container" id="camion3" data-volume="8">
        <img src="images/8m3.png" alt="Camion 8m³">
        <h4>Camion 8m³</h4>
        <p class="fs-sm mb-36">
            Idéal pour débarrasser <span class="fw-bold">un bureau ou un appartement T1</span>.
        </p>
        <div class="fs-sm">
            &#128230; Environ 60 cartons (50x40x40).<br>
            &#9874; Charge maximum : 895kg.
        </div>
        <button type="button" class="choisir-camion" onclick="selectCamion('camion3')">Choisir ce camion</button>
    </div>

     <!-- Camion 11 -->
     <div class="camion camion-container" id="camion11" data-volume="10">
        <img src="images/8m3.png" alt="Camion 10m³">
        <h4>Camion 10m³</h4>
        <p class="fs-sm mb-36">
            Idéal pour débarrasser <span class="fw-bold">un bureau ou un appartement T2</span>.
        </p>
        <div class="fs-sm">
            &#128230; Environ 100 cartons (50x40x40).<br>
            &#9874; Charge maximum : 950kg.
        </div>
        <button type="button" class="choisir-camion" onclick="selectCamion('camion11')">Choisir ce camion</button>
    </div>

    <!-- Camion 4 -->
    <div class="camion camion-container" id="camion4" data-volume="12">
        <img src="images/12m3.png" alt="Camion 12m³">
        <h4>Camion 12m³</h4>
        <p class="fs-sm mb-36">
            Idéal pour débarrasser <span class="fw-bold">entreprise de 2/3 bureaux ou appartement T2</span>.
        </p>
        <div class="fs-sm">
            &#128230; Environ 110 cartons (50x40x40).<br>
            &#9874; Charge maximum : 950kg.
        </div>
        <button type="button" class="choisir-camion" onclick="selectCamion('camion4')">Choisir ce camion</button>
    </div>

    <!-- Camion 12 -->
    <div class="camion camion-container" id="camion12" data-volume="15">
        <img src="images/12m3.png" alt="Camion 15m³">
        <h4>Camion 15m³</h4>
        <p class="fs-sm mb-36">
            Idéal pour débarrasser <span class="fw-bold">un petit local ou magasin</span>.
        </p>
        <div class="fs-sm">
            &#128230; Environ 130 cartons (50x40x40).<br>
            &#9874; Charge maximum : 100kg.
        </div>
        <button type="button" class="choisir-camion" onclick="selectCamion('camion12')">Choisir ce camion</button>
    </div>

      <!-- Camion 5 -->
      <div class="camion camion-container" id="camion5" data-volume="17">
        <img src="images/12m3.png" alt="Camion 17m³">
        <h4>Camion 17m³</h4>
        <p class="fs-sm mb-36">
            Idéal pour débarrasser <span class="fw-bold">un appartement T3 ou une petite maison</span>.
        </p>
        <div class="fs-sm">
            &#128230; Environ 160 cartons (50x40x40).<br>
            &#9874; Charge maximum : 1100kg.
        </div>
        <button type="button" class="choisir-camion" onclick="selectCamion('camion5')">Choisir ce camion</button>
    </div>

      <!-- Camion 6 -->
      <div class="camion camion-container" id="camion6" data-volume="20">
        <img src="images/20m3.png" alt="Camion 20m³">
        <h4>Camion 20m³</h4>
        <p class="fs-sm mb-36">
            Idéal pour débarrasser <span class="fw-bold">grande maison ou entreprise de 4/5 bureaux</span>.
        </p>
        <div class="fs-sm">
            &#128230; Environ 200 cartons (50x40x40).<br>
            &#9874; Charge maximum : 1000kg.
        </div>
        <button type="button" class="choisir-camion" onclick="selectCamion('camion6')">Choisir ce camion</button>
    </div>

    <!-- Camion 7 -->
    <div class="camion camion-container" id="camion7" data-volume="23">
        <img src="images/23m3.png" alt="Camion 23m³">
        <h4>Camion 23m³</h4>
        <p class="fs-sm mb-36">
            Idéal pour débarrasser <span class="fw-bold">grande maison ou entreprise de 6/7 bureaux</span>.
        </p>
        <div class="fs-sm">
            &#128230; Environ 240 cartons (50x40x40).<br>
            &#9874; Charge maximum : 1400kg.
        </div>
        <button type="button" class="choisir-camion" onclick="selectCamion('camion7')">Choisir ce camion</button>
    </div>

    <!-- Camion 8 -->
    <div class="camion camion-container" id="camion8" data-volume="25">
        <img src="images/23m3.png" alt="Camion 25m³">
        <h4>Camion 25m³</h4>
        <p class="fs-sm mb-36">
            Idéal pour débarrasser <span class="fw-bold">grande maison ou entreprise de 8/9 bureaux</span>.
        </p>
        <div class="fs-sm">
            &#128230; Environ 270 cartons (50x40x40).<br>
            &#9874; Charge maximum : 1780kg.
        </div>
        <button type="button" class="choisir-camion" onclick="selectCamion('camion8')">Choisir ce camion</button>
    </div>

    <!-- Camion 9 -->
    <div class="camion camion-container" id="camion9" data-volume="27">
        <img src="images/27m3.png" alt="Camion 27m³">
        <h4>Camion 27m³</h4>
        <p class="fs-sm mb-36">
            Idéal pour débarrasser <span class="fw-bold">grande maison ou entreprise de 10 bureaux</span>.
        </p>
        <div class="fs-sm">
            &#128230; Environ 320 cartons (50x40x40).<br>
            &#9874; Charge maximum : 2850kg.
        </div>
        <button type="button" class="choisir-camion" onclick="selectCamion('camion9')">Choisir ce camion</button>
    </div>

    <!-- Camion 10 -->
    <div class="camion camion-container" id="camion10" data-volume="30">
        <img src="images/30m3.png" alt="Camion 30m³">
        <h4>Camion 30m³</h4>
        <p class="fs-sm mb-36">
            Idéal pour débarrasser <span class="fw-bold">grande maison ou entreprise de + 10 bureaux</span>.
        </p>
        <div class="fs-sm">
            &#128230; Environ 400 cartons (50x40x40).<br>
            &#9874; Charge maximum : 3500kg.
        </div>
        <button type="button" class="choisir-camion" onclick="selectCamion('camion10')">Choisir ce camion</button>
    </div>


    <input type="hidden" id="camion_selectionne" name="camion_selectionne" value="none">

</div>
</div>
</div>

<button type="button" id="next-step">Suivant</button>
</div>

<!-- Step 2 -->
<div id="step-2" class="form-step" style="display: none;">

    <!-- Bouton Retour -->
    <button type="button" id="previous-step">
        ⬅️ Retour
    </button>


    <!-- Nom -->
    <label for="name">Nom <span class="required">*</span></label>
    <input type="text" id="name" name="name" placeholder="Prénom Nom" required>

    <!-- Je suis -->
    <label for="profession">Je suis <span class="required">*</span></label>
    <select id="profession" name="profession" required>
      <option value="1">Un particulier</option>
      <option value="2">Un professionnel</option>

    </select>

  <!-- SIRET (conditionnel) -->
<div id="siret-block" style="display: none;">
    <label for="siret">SIREN (9 chiffres)<span class="required">*</span></label>
    <input type="text" id="siret" name="siret" onchange="getEntrepriseInfo()">
</div>

  <!-- Raison Sociale (conditionnel) -->
  <div id="raison-block" style="display: none;">
    <label for="raison">Raison Sociale</label>
    <input type="text" id="raison" name="raison" readonly>
  </div>

  <!-- Message d'erreur -->
  <div id="error-message" style="color: red; display: none;">
    L'entreprise semble ne plus être active (en cessation selon Pappers.com)
  </div>

  <!-- Numéro de TVA intra-communautaire (conditionnel) -->
<div id="tva-block" style="display: none;">
    <label for="tva">Numéro TVA intra-communautaire</label>
    <input type="text" id="tva" name="tva" readonly>
</div>

    <!-- E-mail -->
    <label for="email">E-mail <span class="required">*</span></label>
    <input type="email" id="email" name="email" required>

    <!-- Téléphone -->
    <label for="phone">Téléphone <span class="required">*</span></label>
    <input type="tel" id="phone" name="phone" required>

    <!-- Adresse -->
    <label for="adresse">Adresse <span class="required">*</span></label>
    <input id="adresse" type="text" name="add" placeholder="Votre adresse" required>


<!-- Sélection de l'étage -->
<div id="additional-fields">
    <label for="etage">À quel étage aura lieu la mission ?<span class="required">*</span></label>
    <select id="etage" name="etage">
        <option value="soussol3">Moins 3 et plus </option>
        <option value="soussol2">Moins 2</option>
        <option value="soussol1">Moins 1</option>
        <option value="RDC" selected>Rez-de-chaussée</option> <!-- Sélection par défaut -->
        <option value="1" data-produit="produit1">1er étage</option>
        <option value="2" data-produit="produit2">2ème étage</option>
        <option value="3" data-produit="produit3">3ème étage</option>
        <option value="4" data-produit="produit4">4ème étage</option>
        <option value="5" data-produit="produit5">5ème étage</option>
        <option value="6" data-produit="produit6">6ème étage</option>
        <option value="7">7ème étage et plus</option>
    </select>
</div>



    <!-- ascenseur -->
    <div id="ascenseur-block">
        <label for="ascenseur">Y a-t-il un ascenseur ?<span class="required">*</span></label>
        <select id="ascenseur" name="ascenseur">
            <option value="non">Non</option>
            <option value="oui">Oui</option>
        </select>
    </div>

<input type="hidden" id="hiddenProduitField" name="hiddenProduitField" value="">




    <!-- Description du besoin -->
<label for="description">Décrivez votre besoin <span class="required">*</span></label>
<textarea id="description" name="description" maxlength="500" placeholder="Merci de nous préciser les conditions d'accès : escaliers, ascenseur, étage..." required></textarea>

  <!-- Téléchargement de plusieurs fichiers -->
  <label for="file-upload">Afin de mieux appréhender votre demande, vous pouvez si vous le souhaitez nous fournir des photos (.jpg, .png, .tiff) ou autres fichiers (.pdf, .doc, .xls):</label><br><br>
  <input type="file" id="file-upload" name="file-upload[]" accept="image/*,application/pdf,.doc,.docx" multiple>





<div id="preview"></div>


    <!-- Bouton Soumettre -->
    <button type="submit">Soumettre</button>
  </div>
</form>

<!-- BOUTON D'AIDE -->
<div id="helpButton">Vous avez besoin d'aide ?</div>

<!-- CONTENEUR DU FORMULAIRE D'AIDE (MASQUÉ PAR DÉFAUT) -->
<div id="helpFormContainer">
    <form action="help_send.php" method="POST" id="helpForm">
        <p>Vous ne savez pas quel service choisir ? Vous avez besoin d'aide ? Décrivez-nous simplement votre besoin</p>
        
        <label for="helpName">Votre nom</label>
        <input type="text" id="helpName" name="helpName" required>

        <label for="helpEmail">Votre email</label>
        <input type="email" id="helpEmail" name="helpEmail" required>

        <label for="helpMessage">Votre demande</label>
        <textarea id="helpMessage" name="helpMessage" rows="4" required></textarea>

        <button type="submit">Envoyer</button>
    </form>
</div>

<!-- Lien vers le fichier JS -->
<script src="script.js"></script>

<script>
    function initAutocomplete() {
        const input = document.getElementById('adresse');
        const options = {
            types: ['geocode'], // Limit to geographic locations
            componentRestrictions: { country: 'fr' } // Restrict to France if needed
        };
        new google.maps.places.Autocomplete(input, options);
    }

    // Wait for the page to load before initializing
    google.maps.event.addDomListener(window, 'load', initAutocomplete);
</script>

<script>
    async function getEntrepriseInfo() {
        const siretInput = document.getElementById("siret");
        const apiKey = "d2f88b5bad910d64116d906bfd47d37fc8507b7cd8001a54"; // Remplacez par votre clé API Pappers

        if (siretInput) {
            let siret = siretInput.value;

            // Suppression des espaces dans le SIRET
            siret = siret.replace(/\s+/g, '');

            if (siret) {
                try {
                    // Requête à l'API Pappers
                    const response = await fetch(`https://api.pappers.fr/v2/entreprise?siren=${siret}`, {
                        headers: {
                            "api-key": apiKey
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();

                        // Vérification si l'entreprise est en cessation
                        const errorMessageElement = document.getElementById("error-message");
                        const raisonElement = document.getElementById("raison");
                        const tvaElement = document.getElementById("tva");
                        const tvaBlock = document.getElementById("tva-block");
                        const raisonBlock = document.getElementById("raison-block");

                        if (data.entreprise_cessee) {
                            if (errorMessageElement) {
                                errorMessageElement.style.display = "block";
                            }
                            if (raisonElement) {
                                raisonElement.value = "";
                            }
                            if (tvaElement) {
                                tvaElement.value = "";
                                tvaBlock.style.display = "none";
                            }
                        } else {
                            if (errorMessageElement) {
                                errorMessageElement.style.display = "none";
                            }
                            if (raisonElement) {
                                raisonElement.value = data.denomination;
                                raisonBlock.style.display = "block";
                            }
                            if (tvaElement && data.numero_tva_intracommunautaire) {
                                tvaElement.value = data.numero_tva_intracommunautaire;
                                tvaBlock.style.display = "block";
                            } else if (tvaElement) {
                                tvaElement.value = "Non disponible";
                                tvaBlock.style.display = "block";
                            }
                        }
                    } else {
                        console.error("Erreur de récupération des données de l'entreprise. Statut :", response.status);
                    }
                } catch (error) {
                    console.error("Erreur réseau ou autre :", error);
                }
            }
        } else {
            console.error("Le champ SIRET est introuvable dans le DOM.");
        }
    }
</script>



</body>
</html>
