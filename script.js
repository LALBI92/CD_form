// ======== SUIVI DU TUNNEL (dataLayer) ========
// Pousse les étapes du devis dans window.dataLayer. Aucun conteneur GTM n'est chargé
// ici : les événements ne seront exploités que si un conteneur est ajouté plus tard.
function trackDevis(eventName, params) {
    window.dataLayer = window.dataLayer || [];
    var payload = { event: eventName };
    for (var k in (params || {})) payload[k] = params[k];
    window.dataLayer.push(payload);
}

function currentCategory() {
    var jr = document.getElementById('je_recycle');
    return jr ? (jr.value || '(aucune)') : '(aucune)';
}

function toggleAccordion(id) {
    console.log("ID passé à toggleAccordion :", id);
    const element = document.getElementById(id);
    if (!element) {
        console.error(`Aucun élément trouvé pour l'ID : ${id}`);
        return;
    }
    const icon = element.previousElementSibling.querySelector('.accordion-icon');
    
    if (element.style.display === "none" || element.style.display === "") {
        element.style.display = "flex";
        icon.textContent = "-"; 
    } else {
        element.style.display = "none";
        icon.textContent = "+";
    }
}


// Sélecteur pour l'étage et l'ascenseur
const etageSelect = document.getElementById('etage');
const ascenseurSelect = document.getElementById('ascenseur');
let produitFinal = '';

// Fonction pour mettre à jour le produit selon l'étage, l'ascenseur, et le camion
function updateProduit() {
    console.log("updateProduit() appelé");

    const selectedEtage = etageSelect.value;
    const ascenseurValue = ascenseurSelect.value;
    const camionId = document.getElementById('camion_selectionne').value;

    // Si l'étage est soussol1 ou soussol2
    if (selectedEtage === 'soussol1') {
        produitFinal = `soussol1_${ascenseurValue === 'oui' ? 'avec_ascenseur' : 'sans_ascenseur'}_${camionId}`;
    } else if (selectedEtage === 'soussol2') {
        produitFinal = `soussol2_${ascenseurValue === 'oui' ? 'avec_ascenseur' : 'sans_ascenseur'}_${camionId}`;
    }
    // Si l'étage est Sous-sol 3 (sur devis)
    else if (selectedEtage === 'soussol3') {
        produitFinal = `soussol3_sur_devis_${camionId}`;
    }
    // Si l'étage est RDC
    else if (selectedEtage === 'RDC') {
        produitFinal = `etageRDC_sans_ascenseur_${camionId}`; // Toujours "sans_ascenseur" pour le RDC
    }
    // Si l'étage est entre 1 et 6
    else if (parseInt(selectedEtage) >= 1 && parseInt(selectedEtage) <= 6) {
        produitFinal = `etage${selectedEtage}_${ascenseurValue === 'oui' ? 'avec_ascenseur' : 'sans_ascenseur'}_${camionId}`;
    }
    // Si l'étage est 7 et plus (sur devis)
    else if (selectedEtage === '7') {
        produitFinal = `etage7plus_sur_devis_${camionId}`;
    }

    // Affichage du produit généré dans la console pour débogage
    console.log("Produit généré :", produitFinal);

    // Mettre à jour le champ caché avec la valeur du produit
    const hiddenProduitField = document.getElementById('hiddenProduitField');
    if (hiddenProduitField) {
        hiddenProduitField.value = produitFinal;
        console.log("Champ caché mis à jour :", hiddenProduitField.value);
    } else {
        console.error("L'élément 'hiddenProduitField' n'existe pas dans le DOM.");
    }
}

// Mettre à jour l'affichage du champ ascenseur en fonction de l'étage
function handleEtageChange() {
    const selectedEtage = etageSelect.value;

    // Si l'étage est supérieur ou égal à 1, afficher le champ ascenseur
    if (parseInt(selectedEtage) >= 1) {
        document.getElementById('ascenseur-block').style.display = 'block';
    } else {
        document.getElementById('ascenseur-block').style.display = 'none';
        // Si l'ascenseur est masqué, forcer la valeur "non"
        ascenseurSelect.value = 'non';
    }

    // Mettre à jour le produit après changement d'étage
    updateProduit();
}

// Ajouter des événements pour mettre à jour le produit lorsque l'étage ou l'ascenseur change
etageSelect.addEventListener('change', handleEtageChange);
ascenseurSelect.addEventListener('change', updateProduit);

// Initialiser le produit et gérer l'affichage de l'ascenseur dès le chargement de la page
document.addEventListener('DOMContentLoaded', function () {
    handleEtageChange(); // Gérer l'affichage de l'ascenseur
    updateProduit();     // Générer le produit initial
});

// Étage / ascenseur : utiles seulement quand l'équipe monte chercher les déchets sur place.
// Masqués pour les bennes (posées en pied d'immeuble) et le dépôt chez City Debarras ;
// dans ce cas on remet les valeurs par défaut (RDC / non) pour garder le même payload.
var CATEGORIES_SANS_ETAGE = ['louer_benne', 'dechets_chantiers', 'dechets_non_dangereux'];

function updatePickupFields() {
    var jeRecycle = document.getElementById('je_recycle');
    var lieuDeee = document.getElementById('lieu_deee');
    var lieuArchives = document.getElementById('lieu_archives');
    var additionalFields = document.getElementById('additional-fields');
    if (!jeRecycle || !additionalFields) return;
    var cat = jeRecycle.value;
    var hide = CATEGORIES_SANS_ETAGE.indexOf(cat) !== -1
        || (cat === 'deee' && lieuDeee && lieuDeee.value === 'type_deee_depot')
        || (cat === 'destruction_archives' && lieuArchives && lieuArchives.value === 'destruction_archives_depot');
    if (hide) {
        additionalFields.style.display = 'none';
        document.getElementById('ascenseur-block').style.display = 'none';
        etageSelect.value = 'RDC';
        ascenseurSelect.value = 'non';
        updateProduit();
    } else {
        additionalFields.style.display = 'block';
        handleEtageChange();
    }
}

['je_recycle', 'lieu_deee', 'lieu_archives'].forEach(function (id) {
    var el = document.getElementById(id);
    if (el) el.addEventListener('change', updatePickupFields);
});

// Gérer la sélection d'un camion
let selectedCamion = null;

function selectCamion(camionId) {
    // Retirer la sélection précédente, s'il y en a une
    if (selectedCamion) {
        selectedCamion.classList.remove('selected');
    }

    // Sélectionner le nouveau camion et ajouter un effet visuel (bordure)
    const camion = document.getElementById(camionId);
    camion.classList.add('selected');
    selectedCamion = camion;

    // Mettre à jour le champ caché avec l'ID du camion sélectionné
    document.getElementById('camion_selectionne').value = camionId;
    console.log("Camion sélectionné : " + camionId);

    // Afficher les champs supplémentaires pour l'étage et l'ascenseur
    document.getElementById('additional-fields').style.display = 'block';

    // Mettre à jour le produit après la sélection du camion
    updateProduit();

    // Passer automatiquement à l'étape suivante
    goToStep2('camion');
}

// Affichage de l'étape 2 (depuis « Suivant » ou le choix d'un camion)
function goToStep2(via) {
    // Résumé compact de la sélection au-dessus du bouton d'envoi (ancienne étape « récap »)
    buildRecap();
    updatePickupFields();

    document.getElementById('step-2').style.display = 'block';
    document.getElementById('step-1').style.display = 'none';

    trackDevis('devis_step1_complete', { category: currentCategory(), via: via || '' });
    trackDevis('devis_step2_view', { category: currentCategory() });

    // Précharger Google Maps pour l'autocomplétion de l'adresse
    if (typeof loadGoogleMaps === 'function') loadGoogleMaps();

    // Faire défiler la page vers le haut
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}



document.addEventListener("DOMContentLoaded", function () {

    const sectionsToShow = {
        // Sections principales
        "dechets_non_dangereux": "dechets_nd_wrapper",
        "dechets_chantiers": "dechets_chantiers_wrapper",
        "dechets_bureau": "dechets_bureau_wrapper",
        "deee": "deee_wrapper",
        "louer_benne": "louer_benne_wrapper",
        "gravats_propres": "gravats_propres_wrapper",
        "destruction_archives": "destruction_archives_wrapper",
        "destruction_archives_domicile": "destruction_archives_domicile_wrapper",
        "destruction_archives_depot": "destruction_archives_depot_wrapper",
        "volume_section": "volume-section",
        "part_pro": "part_pro_wrapper",

        // Sous-sections liées aux catégories principales
        // Déchets non dangereux
        "cartons": "dnd_cartons_wrapper",
        "papiers": "dnd_papiers_wrapper",
        "plastiques": "dnd_plastiques_wrapper",
        "palettes": "dnd_palettes_wrapper",
        "encombrants": "dnd_encombrants_wrapper",
        "dib": "dnd_dib_wrapper",
        "bois": "dnd_bois_wrapper",
        "ferrailles": "dnd_ferrailles_wrapper",
        "dechets_vert": "dnd_dechets_verts_wrapper",
        // Déchets de chantier
        "dib_chantier": "dib_chantier_wrapper",
        "bois_chantier": "bois_chantier_wrapper",
        "platre_chantier": "platre_chantier_wrapper",
        "gravats_melange_chantier": "gravats_melange_chantier_wrapper",
        "gravats_propres_chantier": "gravats_propres_chantier_wrapper",
        // D3E / DEEE
        "informatiques_bureautiques": "informatiques_bureautiques_wrapper",
        "cartouches_encres_toners": "cartouches_encres_wrapper",
        "accumulateurs_batteries_piles": "piles_wrapper",
        "electromenager_chaud_froid": "electromenager_wrapper",
        "ampoules_lampes_neons": "ampoules_wrapper",
        "informatiques_bureautiques_domicile": "informatiques_bureautiques_domicile_wrapper",
        "cartouches_encres_toners_domicile": "cartouches_encres_domicile_wrapper",
        "accumulateurs_batteries_piles_domicile": "piles_domicile_wrapper",
        "electromenager_chaud_froid_domicile": "electromenager_domicile_wrapper",
        "climatisation_chaud_froid_domicile": "climatisation_chaud_froid_domicile_wrapper",
        "ampoules_lampes_neons_domicile": "ampoules_domicile_wrapper",
        "informatiques_bureautiques_depot": "informatiques_bureautiques_depot_wrapper",
        "cartouches_encres_toners_depot": "cartouches_depot_encres_wrapper",
        "accumulateurs_batteries_piles_depot": "piles_depot_wrapper",
        "electromenager_chaud_froid_depot": "electromenager_depot_wrapper",
        "climatisation_chaud_froid_depot": "climatisation_chaud_froid_depot_wrapper",
        "ampoules_lampes_neons_depot": "ampoules_depot_wrapper",
        // Destruction d'archives (sections supplémentaires déjà incluses ci-dessus)
        // Louer une benne
        "gravats_beton": "gravats_propres_wrapper",
        "dnd_bennes":"dechets_non_dangereux_wrapper"
    };



    // Fonction pour masquer toutes les sections des bennes
    function hideAllBenneSections() {
        for (let sectionId in sectionsToShow) {
            const sectionElement = document.getElementById(sectionsToShow[sectionId]);
            if (sectionElement) {
                sectionElement.style.display = "none";
            }
        }
    }

    // Masquer toutes les sections au démarrage
    hideAllBenneSections();

    // Gérer le champ "JE RECYCLE"
    const jeRecycle = document.getElementById("je_recycle");
    if (jeRecycle) {
        jeRecycle.addEventListener("change", function () {
            hideAllBenneSections(); // Masquer toutes les sections au début
            hideAllDeeeSections();

            // Afficher la bonne section selon le choix de recyclage
            const selectedValue = jeRecycle.value;
            if (sectionsToShow[selectedValue]) {
                const sectionToShow = document.getElementById(sectionsToShow[selectedValue]);
                if (sectionToShow) {
                    sectionToShow.style.display = "block";
                }
            }

            // Afficher des sections supplémentaires si besoin (ex: volume et partPro)
            if (selectedValue === "mobilier_bureau" || selectedValue === "debarrasser_local") {
                const partProWrapper = document.getElementById(sectionsToShow["part_pro"]);
                const volumeSection = document.getElementById(sectionsToShow["volume_section"]);
                if (partProWrapper) partProWrapper.style.display = "block";
                if (volumeSection) volumeSection.style.display = "block";
            }
        });
    }

    // Fonction pour masquer uniquement les sections liées aux choix d'images
function hideChoiceSections() {
    ["cartons", "papiers", "plastiques", "palettes", "encombrants", "dib", "bois", "ferrailles", "dechets_vert", "dib_chantier", "bois_chantier", "platre_chantier", "gravats_melange_chantier", "gravats_propres_chantier",   /* Ajoute toutes les sections pertinentes ici */].forEach(section => {
        const sectionElement = document.getElementById(sectionsToShow[section]);
        if (sectionElement) {
            sectionElement.style.display = "none";
        }
    });
}

function resetDeeeSelectFields() {
    const deeeDomicileSelect = document.getElementById("deee_domicile_select");
    const deeedepotSelect = document.getElementById("deee_depot_select");

    if (deeeDomicileSelect) deeeDomicileSelect.value = ""; // Réinitialiser à la valeur par défaut
    if (deeedepotSelect) deeedepotSelect.value = ""; // Réinitialiser à la valeur par défaut
    syncTypeTiles(deeeDomicileSelect); // Les tuiles suivent la remise à zéro
    syncTypeTiles(deeedepotSelect);
}

function hideAllDeeeSections() {
    // Masquer les sous-sections
    hideDeeeDomicileSections();
    hideDeeedepotSections();

    // Masquer les sélecteurs domicile et entrepôt
    const typeDeeeDomicileWrapper = document.getElementById("type_deee_domicile_wrapper");
    const typeDeeedepotWrapper = document.getElementById("type_deee_depot_wrapper");

    if (typeDeeeDomicileWrapper) typeDeeeDomicileWrapper.style.display = "none";
    if (typeDeeedepotWrapper) typeDeeedepotWrapper.style.display = "none";

    // Réinitialiser les champs select
    resetDeeeSelectFields();
}


// Fonction pour masquer les DEEE
function hideDeeeDomicileSections() {
    Object.entries(sectionsToShow).forEach(([key, value]) => {
        if (key.includes("_domicile")) { // Si c'est une clé pour Domicile
            const sectionElement = document.getElementById(value);
            if (sectionElement) {
                sectionElement.style.display = "none";
            }
        }
    });
}

function hideDeeedepotSections() {
    Object.entries(sectionsToShow).forEach(([key, value]) => {
        if (key.includes("_depot")) { // Si c'est une clé pour Entrepôt
            const sectionElement = document.getElementById(value);
            if (sectionElement) {
                sectionElement.style.display = "none";
            }
        }
    });
}


// Fonction pour masquer uniquement les sections liées aux types de bennes
function hideBenneSections() {
    // gravats_beton / dnd_bennes : changer de type de benne masque les contenants de l'autre type
    ["dib_chantier", "bois_chantier", "gravats_beton", "dnd_bennes"].forEach(section => {
        const sectionElement = document.getElementById(sectionsToShow[section]);
        if (sectionElement) {
            sectionElement.style.display = "none";
        }
    });
}

// Modifications des événements (exemple pour choices)
const choices = document.querySelectorAll(".choice");
choices.forEach(choice => {
    choice.addEventListener("click", function () {
        hideChoiceSections(); // Masquer seulement les sections liées à choices
        choices.forEach(c => c.classList.remove("selected"));
        this.classList.add("selected");
        const sectionId = sectionsToShow[this.dataset.value];
        if (sectionId) {
            document.getElementById(sectionId).style.display = "block";
        }
    });
});

// Gérer la sélection du lieu des DEEE
const lieuDeee = document.getElementById("lieu_deee");
if (lieuDeee) {
    lieuDeee.addEventListener("change", function () {
        // Sélection des sous-sections
        const typeDeeeDomicileWrapper = document.getElementById("type_deee_domicile_wrapper");
        const typeDeeeDepotWrapper = document.getElementById("type_deee_depot_wrapper");

        // Masquer les sous-sections par défaut
        if (typeDeeeDomicileWrapper) typeDeeeDomicileWrapper.style.display = "none";
        if (typeDeeeDepotWrapper) typeDeeeDepotWrapper.style.display = "none";

        // Afficher la section correcte selon la sélection
        if (lieuDeee.value === "type_deee_domicile") {
            if (typeDeeeDomicileWrapper) typeDeeeDomicileWrapper.style.display = "block";
        } else if (lieuDeee.value === "type_deee_depot") {
            if (typeDeeeDepotWrapper) typeDeeeDepotWrapper.style.display = "block";
        }
    });
}

const deeeDomicileSelect = document.getElementById("deee_domicile_select");
if (deeeDomicileSelect) {
    deeeDomicileSelect.addEventListener("change", function () {
        hideDeeeDomicileSections(); // Masquer toutes les sections de domicile
        const sectionId = sectionsToShow[this.value]; // Trouver l'ID complet
        if (sectionId) {
            const sectionElement = document.getElementById(sectionId);
            if (sectionElement) {
                sectionElement.style.display = "block"; // Afficher la section correspondante
            }
        }
    });
}

const deeedepotSelect = document.getElementById("deee_depot_select");
if (deeedepotSelect) {
    deeedepotSelect.addEventListener("change", function () {
        hideDeeedepotSections(); // Masquer toutes les sections d'entrepôt
        const sectionId = sectionsToShow[this.value]; // Trouver l'ID complet
        if (sectionId) {
            const sectionElement = document.getElementById(sectionId);
            if (sectionElement) {
                sectionElement.style.display = "block"; // Afficher la section correspondante
            }
        }
    });
}

// Gérer la sélection du type de benne
const typeBenneSelect = document.getElementById("type_benne");
if (typeBenneSelect) {
    typeBenneSelect.addEventListener("change", function () {
        hideBenneSections(); // Masquer seulement les sections liées aux bennes
        const sectionId = sectionsToShow[typeBenneSelect.value];
        if (sectionId) {
            document.getElementById(sectionId).style.display = "block";
        }
    });
}

// Gérer la sélection du lieu des archives
const lieuArchivesSelect = document.getElementById("lieu_archives");
if (lieuArchivesSelect) {
    lieuArchivesSelect.addEventListener("change", function () {
        // Sélection des sous-sections
        const destructionArchivesDomicileWrapper = document.getElementById("destruction_archives_domicile_wrapper");
        const destructionArchivesDepotWrapper = document.getElementById("destruction_archives_depot_wrapper");

        // Masquer les sous-sections par défaut
        if (destructionArchivesDomicileWrapper) destructionArchivesDomicileWrapper.style.display = "none";
        if (destructionArchivesDepotWrapper) destructionArchivesDepotWrapper.style.display = "none";

        // Afficher la section correcte selon la sélection
        if (lieuArchivesSelect.value === "destruction_archives_domicile") {
            if (destructionArchivesDomicileWrapper) destructionArchivesDomicileWrapper.style.display = "block";
        } else if (lieuArchivesSelect.value === "destruction_archives_depot") {
            if (destructionArchivesDepotWrapper) destructionArchivesDepotWrapper.style.display = "block";
        }
    });
}




    // Gérer les boutons + et - pour ajuster la quantité
const qtyButtons = document.querySelectorAll(".qty-plus, .qty-minus");

qtyButtons.forEach(button => {
    button.addEventListener("click", function () {
        const targetId = this.getAttribute("data-target");
        const input = document.getElementById(targetId);
        let currentValue = parseInt(input.value);

        if (isNaN(currentValue)) {
            currentValue = 0;
        }

        if (this.classList.contains("qty-plus")) {
            input.value = currentValue + 1;
        } else if (this.classList.contains("qty-minus") && currentValue > 0) {
            input.value = currentValue - 1;
        }

        // Animation micro-interaction sur le chiffre
        input.classList.remove("qty-bump");
        void input.offsetWidth; // force reflow
        input.classList.add("qty-bump");

        // Si l'input concerné a la classe 'volume-control', on met à jour le volume total
        if (input.classList.contains("volume-control")) {
            updateTotalVolume();
        }
    });
});

// Fonction pour mettre à jour les suggestions de camions
function updateVehicleSuggestions() {
    const estimatedVolume = parseFloat(document.getElementById("volume-result").textContent);
    const allVehicles = document.querySelectorAll(".camion");

    // Produits spécifiques qui nécessitent une exclusion de camions
    const restrictedProducts = {
        "armoire_rideaux_1m2_qty": ["camion1"],
        "armoire_battantes_1m_qty": ["camion1"],
        "bibliotheque_qty": ["camion1"],
        "canape_3p_qty": ["camion1"],
        "canape_angle_qty": ["camion1"],
        "armoire_3portes_qty": ["camion1"],
        "armoire_4portes_qty": ["camion1"],
        "cadre_lit_2p_qty": ["camion1"],
        "matelas_2p_qty": ["camion1"],
        "sommier_2p_qty": ["camion1"],
        "lit_barreaux_2p_qty": ["camion1"],
        "vestiaires_banc_qty": ["camion1"],
        "banc_rembourre_qty": ["camion1"],
        "grande_bibliotheque_qty": ["camion1"],
        "commode_qty": ["camion1"],
        "buffet_bas_qty": ["camion1"],
        "buffet_haut_qty": ["camion1"],
        "table_reunion_12_qty": ["camion1"],
        "banque_comptoir_qty": ["camion1"],
        "comptoir_bar_qty": ["camion1"],
        "vitrine_presentation_qty": ["camion1"],
        "vitrine_verre_qty": ["camion1"],
        "plv_basse_qty": ["camion1"],
        "plv_haute_qty": ["camion1"],
        "podiums_3niveaux_qty": ["camion1"],
        "vitrine_comptoir_qty": ["camion1"],
        "congelateur_coffre_qty": ["camion1", "camion2"], // Congélateur coffre exclut camion1 et camion2
        "refrigerateur_americain_qty": ["camion1", "camion2"]
    };

    // Vérifier si un produit restreint est sélectionné
    let restrictedCamions = new Set();
    Object.keys(restrictedProducts).forEach(productId => {
        const productInput = document.getElementById(productId);
        if (productInput && parseInt(productInput.value) > 0) {
            restrictedProducts[productId].forEach(camionId => restrictedCamions.add(camionId));
        }
    });

    // Mettre à jour l'affichage des camions
    allVehicles.forEach(vehicle => {
        const vehicleVolume = parseFloat(vehicle.getAttribute("data-volume"));
        if (vehicleVolume >= estimatedVolume && !restrictedCamions.has(vehicle.id)) {
            vehicle.style.display = "block"; // Afficher le camion si sa capacité est suffisante et qu'il n'est pas restreint
        } else {
            vehicle.style.display = "none"; // Cacher le camion si sa capacité est insuffisante ou s'il est restreint
        }
    });
}

// Appeler cette fonction après la mise à jour du volume estimé
function updateTotalVolume() {
    let totalVolume = 0;
    const volumeInputs = document.querySelectorAll("input.volume-control[data-volume]");

    volumeInputs.forEach(input => {
        const quantity = parseInt(input.value);
        const volumePerItem = parseFloat(input.getAttribute("data-volume"));
        if (!isNaN(quantity) && !isNaN(volumePerItem)) {
            totalVolume += quantity * volumePerItem;
        }
    });

    document.getElementById("volume-result").textContent = totalVolume.toFixed(2);

    // Mise à jour des camions suggérés en fonction du volume
    updateVehicleSuggestions();
}



// Gérer l'affichage conditionnel du SIRET et de la Raison Sociale
const professionSelect = document.getElementById("profession");
const siretBlock = document.getElementById("siret-block");
const siretInput = document.getElementById("siret");

if (professionSelect && siretBlock) {
    professionSelect.addEventListener("change", function () {
        if (this.value === "2") { // Un professionnel
            siretBlock.style.display = "block";
            siretInput.setAttribute("required", "required");
        } else {
            siretBlock.style.display = "none";
            siretInput.removeAttribute("required");
        }
    });
}

// ======== VALIDATION EN LIGNE (remplace les alert()) ========
function setFieldError(input, message) {
    var id = 'err-' + input.id;
    var msg = document.getElementById(id);
    if (!msg) {
        msg = document.createElement('div');
        msg.id = id;
        msg.className = 'field-error';
        msg.setAttribute('role', 'alert');
        input.insertAdjacentElement('afterend', msg);
    }
    if (!input.dataset.errorWired) {
        // Le message disparaît dès que l'utilisateur corrige le champ
        input.dataset.errorWired = '1';
        input.addEventListener('input', function () { clearFieldError(input); });
    }
    msg.textContent = message;
    input.setAttribute('aria-invalid', 'true');
    input.setAttribute('aria-describedby', id);
}

function clearFieldError(input) {
    var msg = document.getElementById('err-' + input.id);
    if (msg) msg.remove();
    input.removeAttribute('aria-invalid');
    input.removeAttribute('aria-describedby');
}

// Mêmes règles qu'avant (champs requis, format e-mail, SIREN pour un pro), messages sous chaque champ
function validateStep2() {
    var checks = [
        ['name', 'Indiquez votre nom.'],
        ['email', 'Indiquez votre e-mail.'],
        ['phone', 'Indiquez votre numéro de téléphone.'],
        ['adresse', "Indiquez l'adresse de l'intervention."]
    ];
    var firstInvalid = null;
    checks.forEach(function (c) {
        var el = document.getElementById(c[0]);
        if (!el) return;
        var message = '';
        if (!el.value.trim()) message = c[1];
        else if (el.type === 'email' && el.validity.typeMismatch) message = "Cette adresse e-mail n'est pas valide.";
        if (message) { setFieldError(el, message); firstInvalid = firstInvalid || el; }
        else clearFieldError(el);
    });
    var prof = document.getElementById('profession');
    var siret = document.getElementById('siret');
    if (prof && siret) {
        if (prof.value === '2' && !siret.value.trim()) {
            setFieldError(siret, 'Le SIREN est obligatoire pour un professionnel.');
            firstInvalid = firstInvalid || siret;
        } else {
            clearFieldError(siret);
        }
    }
    if (firstInvalid) {
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstInvalid.focus({ preventScroll: true });
        return false;
    }
    return true;
}

// Validation du formulaire avec protection anti-double soumission
let isSubmitting = false;

document.getElementById("devisForm").addEventListener("submit", function(e) {
    // Protection contre les double-soumissions
    if (isSubmitting) {
        e.preventDefault();
        console.log("Soumission déjà en cours, ignorée");
        return;
    }

    var formValid = validateStep2();
    trackDevis('devis_submit_click', { category: currentCategory(), valid: formValid });
    if (!formValid) {
        e.preventDefault();
        return;
    }

    // Marquer comme en cours de soumission
    isSubmitting = true;

    // Désactiver le bouton de soumission
    const submitButton = document.getElementById('submit-btn');
    const submitLabel = submitButton ? submitButton.textContent : '';
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = "Envoi en cours...";
    }

    console.log("Formulaire soumis - protection activée");

    // Réactiver après 10 secondes (sécurité)
    setTimeout(() => {
        isSubmitting = false;
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.textContent = submitLabel;
        }
    }, 10000);
});



        // Bouton Retour (de Step 2 vers Step 1)
    document.getElementById('previous-step').addEventListener('click', function(event) {
        event.preventDefault(); // Empêche tout comportement par défaut du bouton
        document.getElementById('step-2').style.display = 'none';
        document.getElementById('step-1').style.display = 'block';
    });

        // Ajout de la validation du bouton "Suivant" avec protection anti-double clic
        document.getElementById('next-step').addEventListener('click', function(event) {
            // Empêcher le comportement par défaut
            event.preventDefault();
            
            // Protection anti-double clic
            if (this.disabled) return;
            
            this.disabled = true;
            setTimeout(() => {
                this.disabled = false;
            }, 1000);

            // Vérification de la sélection d'un camion
            const camionSelectionne = document.getElementById('camion_selectionne').value;

            // Vérification de la sélection d'un produit
            let produitSelectionne = false;

            // 1. Vérifier les produits avec quantité (input type="number")
            const productInputs = document.querySelectorAll('.benne input[type="number"]');
            productInputs.forEach(input => {
                if (parseInt(input.value, 10) > 0) {
                    produitSelectionne = true;
                }
            });

            // 2. Vérifier également les produits qui n'ont pas de quantité (input type="checkbox")
            const productCheckboxes = document.querySelectorAll('.benne input[type="checkbox"]');
            productCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    produitSelectionne = true;
                }
            });

            // Vérifier qu'au moins un camion ou un produit est sélectionné
            let selectionMessage = document.getElementById('selection_message');
            if (camionSelectionne === "none" && !produitSelectionne) {
                if (!selectionMessage) {
                    selectionMessage = document.createElement('div');
                    selectionMessage.id = 'selection_message';
                    selectionMessage.style.color = 'red';
                    selectionMessage.style.marginTop = '5px';
                    selectionMessage.innerText = 'Veuillez sélectionner un produit et/ou un camion pour continuer.';
                    // Insertion du message juste au-dessus du bouton "Suivant"
                    const nextStepBtn = document.getElementById('next-step');
                    const step1Container = document.getElementById('step-1');
                    step1Container.insertBefore(selectionMessage, nextStepBtn);
                }
                return; // Bloquer la progression vers l'étape suivante
            } else if (selectionMessage) {
                selectionMessage.remove();
            }

            // Toutes les conditions étant remplies, passez à l'étape suivante
            goToStep2('suivant');
        });

    // Tuiles de sous-type à la place des listes déroulantes (DEEE, benne)
    Object.keys(TYPE_TILES).forEach(buildTypeTiles);

    // Pré-sélection depuis l'URL (?besoin=...), une fois tous les écouteurs posés
    applyUrlPreselection();
    trackDevis('devis_view', { besoin: devisBesoin || '(aucun)' });
    });



    // Quand on clique sur le bouton, on affiche ou on masque le formulaire
    document.getElementById('helpButton').addEventListener('click', function() {
        const formContainer = document.getElementById('helpFormContainer');
        if (formContainer.style.display === 'none' || formContainer.style.display === '') {
            formContainer.style.display = 'block';
        } else {
            formContainer.style.display = 'none';
        }
    });

    // ======== RÉSUMÉ (étape 2) - Event Listeners ========
    // « Modifier ma sélection » ramène à l'étape 1, comme le bouton Retour
    document.getElementById('recap-edit').addEventListener('click', function () {
        document.getElementById('previous-step').click();
    });
    document.getElementById('recap-header').addEventListener('click', toggleRecap);


// ======== FONCTIONS RÉCAPITULATIF ========

function toggleRecap() {
    var content = document.getElementById('recap-content');
    var icon = document.getElementById('recap-toggle-icon');
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.classList.remove('collapsed');
    } else {
        content.style.display = 'none';
        icon.classList.add('collapsed');
    }
}

function buildRecap() {
    // -- Besoin --
    var jeRecycle = document.getElementById('je_recycle');
    var besoinHtml = '';
    besoinHtml += '<div class="recap-line"><span class="recap-label">Catégorie :</span> <span class="recap-value">' + escapeHtml(jeRecycle.options[jeRecycle.selectedIndex].text) + '</span></div>';
    document.getElementById('recap-besoin-details').innerHTML = besoinHtml;

    // -- Produits sélectionnés --
    var produitsList = document.getElementById('recap-produits-list');
    produitsList.innerHTML = '';
    var hasProducts = false;

    // Bennes/contenants avec quantité > 0
    var qtyInputs = document.querySelectorAll('.benne input[type="number"]');
    qtyInputs.forEach(function(input) {
        var qty = parseInt(input.value, 10);
        if (qty > 0) {
            hasProducts = true;
            var benneEl = input.closest('.benne');
            var labelEl = benneEl.querySelector('label span') || benneEl.querySelector('span');
            var productName = labelEl ? labelEl.textContent.trim() : input.id;
            // Remonter pour trouver le wrapper parent et son label
            var wrapper = benneEl.closest('[id$="_wrapper"]');
            var category = '';
            if (wrapper) {
                var wrapperLabel = wrapper.querySelector(':scope > label');
                if (wrapperLabel) category = wrapperLabel.textContent.trim() + ' — ';
            }
            var li = document.createElement('li');
            li.innerHTML = '<span>' + escapeHtml(category + productName) + '</span><span class="recap-qty">x' + qty + '</span>';
            produitsList.appendChild(li);
        }
    });

    // Choix sélectionnés (choices avec classe .selected)
    var selectedChoices = document.querySelectorAll('.choice.selected');
    selectedChoices.forEach(function(choice) {
        hasProducts = true;
        var choiceText = choice.querySelector('span') ? choice.querySelector('span').textContent.trim() : choice.dataset.value;
        var li = document.createElement('li');
        li.innerHTML = '<span>' + escapeHtml(choiceText) + '</span>';
        produitsList.appendChild(li);
    });

    // Mobilier avec quantité > 0
    var furnitureInputs = document.querySelectorAll('.furniture-item input[type="number"]');
    furnitureInputs.forEach(function(input) {
        var qty = parseInt(input.value, 10);
        if (qty > 0) {
            hasProducts = true;
            var row = input.closest('.furniture-item');
            var nameEl = row.querySelector('span');
            var productName = nameEl ? nameEl.textContent.trim() : input.id;
            var li = document.createElement('li');
            li.innerHTML = '<span>' + escapeHtml(productName) + '</span><span class="recap-qty">x' + qty + '</span>';
            produitsList.appendChild(li);
        }
    });

    if (!hasProducts) {
        var li = document.createElement('li');
        li.textContent = 'Aucun produit sélectionné';
        produitsList.appendChild(li);
    }

    // -- Camion --
    var camionValue = document.getElementById('camion_selectionne').value;
    var recapCamionBlock = document.getElementById('recap-camion');
    if (camionValue && camionValue !== 'none') {
        recapCamionBlock.style.display = 'block';
        var camionEl = document.getElementById(camionValue);
        var camionName = camionEl ? camionEl.querySelector('h4').textContent : camionValue;
        var camionVolume = camionEl ? camionEl.dataset.volume + 'm³' : '';
        document.getElementById('recap-camion-details').innerHTML =
            '<div class="recap-line"><span class="recap-label">' + escapeHtml(camionName) + '</span> <span class="recap-value">(' + escapeHtml(camionVolume) + ')</span></div>';
    } else {
        recapCamionBlock.style.display = 'none';
    }

}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}







// ======== TUILES DE SOUS-TYPE (remplacent visuellement un <select>) ========
// Le <select> d'origine reste dans le DOM (masqué, même name) : le payload ne change pas.
// Un clic sur une tuile positionne sa valeur et déclenche « change », donc toute la logique
// existante (sections affichées, résumé, validation, dataLayer) s'exécute comme avant.
var TYPE_TILES = {
    deee_domicile_select: { labelId: 'label_deee_domicile', icons: {
        informatiques_bureautiques_domicile: 'fa-laptop', cartouches_encres_toners_domicile: 'fa-print',
        accumulateurs_batteries_piles_domicile: 'fa-car-battery', electromenager_chaud_froid_domicile: 'fa-blender',
        climatisation_chaud_froid_domicile: 'fa-fan', ampoules_lampes_neons_domicile: 'fa-lightbulb' } },
    deee_depot_select: { labelId: 'label_deee_depot', icons: {
        informatiques_bureautiques_depot: 'fa-laptop', cartouches_encres_toners_depot: 'fa-print',
        accumulateurs_batteries_piles_depot: 'fa-car-battery', electromenager_chaud_froid_depot: 'fa-blender',
        climatisation_chaud_froid_depot: 'fa-fan', ampoules_lampes_neons_depot: 'fa-lightbulb' } },
    type_benne: { labelId: 'label_type_benne',
        icons: { gravats_beton: 'fa-cubes', dnd_bennes: 'fa-recycle' },
        labels: { gravats_beton: 'Gravats, béton, parpaings, tuiles, terre, pierres',
                  dnd_bennes: 'Déchets non dangereux : bois, plâtre, plastiques, cartons, métaux' } }
};

function syncTypeTiles(select) {
    var group = select && select._typeTiles;
    if (!group) return;
    group.querySelectorAll('.type-tile').forEach(function (btn) {
        var on = btn.dataset.value === select.value;
        btn.classList.toggle('selected', on);
        btn.setAttribute('aria-pressed', on ? 'true' : 'false');
    });
}

function buildTypeTiles(selectId) {
    var conf = TYPE_TILES[selectId];
    var select = document.getElementById(selectId);
    if (!conf || !select || select._typeTiles) return;
    var group = document.createElement('div');
    group.className = 'type-tiles';
    group.setAttribute('role', 'group');
    if (conf.labelId) group.setAttribute('aria-labelledby', conf.labelId);
    var count = 0;
    Array.prototype.forEach.call(select.options, function (opt) {
        if (!opt.value) return;
        count++;
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'type-tile';
        btn.dataset.value = opt.value;
        btn.setAttribute('aria-pressed', 'false');
        var icon = conf.icons && conf.icons[opt.value];
        if (icon) {
            var i = document.createElement('i');
            i.className = 'fa-solid ' + icon;
            i.setAttribute('aria-hidden', 'true');
            btn.appendChild(i);
        }
        var span = document.createElement('span');
        span.textContent = (conf.labels && conf.labels[opt.value]) || opt.text.trim();
        btn.appendChild(span);
        btn.addEventListener('click', function () {
            if (select.value === opt.value) return; // déjà choisi : rien ne change
            select.value = opt.value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
        });
        group.appendChild(btn);
    });
    if (count <= 2) group.classList.add('type-tiles--2');
    select.insertAdjacentElement('afterend', group);
    select.style.display = 'none';
    select.setAttribute('aria-hidden', 'true');
    select.tabIndex = -1;
    select._typeTiles = group;
    select.addEventListener('change', function () { syncTypeTiles(select); });
    syncTypeTiles(select);
}

// ======== PRÉ-SÉLECTION PAR L'URL ========
// ?besoin=  catégorie (deee, archives, ferraille, bureau, mobilier, debarras, benne, chantier…)
// ?lieu=    site | depot            (DEEE et archives : récupération sur place ou dépôt)
// Le lien s'arrête à la catégorie : aucun sous-type (DEEE, benne, chantier) n'est présélectionné,
// le visiteur choisit lui-même sa tuile. ?type= est ignoré. Voir docs/parametres-url.md
// Exception : ferraille / cartons / papiers… sont des tuiles visibles de « déchets non dangereux »,
// présélectionnées parmi les autres tuiles affichées (rien n'est masqué).
var BESOIN_MAP = {
    deee: { recycle: 'deee' },
    d3e: { recycle: 'deee' },
    informatique: { recycle: 'deee' },
    cartouches: { recycle: 'deee' },
    archives: { recycle: 'destruction_archives' },
    ferraille: { recycle: 'dechets_non_dangereux', choice: 'ferrailles' },
    ferrailles: { recycle: 'dechets_non_dangereux', choice: 'ferrailles' },
    metaux: { recycle: 'dechets_non_dangereux', choice: 'ferrailles' },
    bureau: { recycle: 'dechets_bureau' },
    mobilier: { recycle: 'mobilier_bureau' },
    debarras: { recycle: 'debarrasser_local' },
    benne: { recycle: 'louer_benne' },
    chantier: { recycle: 'dechets_chantiers' },
    dnd: { recycle: 'dechets_non_dangereux' },
    cartons: { recycle: 'dechets_non_dangereux', choice: 'cartons' },
    papiers: { recycle: 'dechets_non_dangereux', choice: 'papiers' },
    plastiques: { recycle: 'dechets_non_dangereux', choice: 'plastiques' },
    palettes: { recycle: 'dechets_non_dangereux', choice: 'palettes' },
    encombrants: { recycle: 'dechets_non_dangereux', choice: 'encombrants' },
    bois: { recycle: 'dechets_non_dangereux', choice: 'bois' },
    dib: { recycle: 'dechets_non_dangereux', choice: 'dib' },
    dechets_verts: { recycle: 'dechets_non_dangereux', choice: 'dechets_vert' }
};
var LIEU_MAP = { site: 'domicile', sur_site: 'domicile', domicile: 'domicile', oui: 'domicile', depot: 'depot', non: 'depot' };

function normalizeParam(v) {
    return (v || '').toString().trim().toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[\s-]+/g, '_');
}

// Positionne un <select> et déclenche « change » pour que la logique existante s'exécute
function setSelectValue(id, value) {
    var el = document.getElementById(id);
    if (!el || !Array.prototype.some.call(el.options, function (o) { return o.value === value; })) return false;
    el.value = value;
    el.dispatchEvent(new Event('change', { bubbles: true }));
    return true;
}

var devisBesoin = '';

function applyUrlPreselection() {
    var params = new URLSearchParams(window.location.search);
    var besoin = normalizeParam(params.get('besoin'));
    // Accepte aussi directement les valeurs internes de « Je recycle »
    var conf = BESOIN_MAP[besoin];
    if (!conf) {
        var jr = document.getElementById('je_recycle');
        var isInternal = jr && besoin && Array.prototype.some.call(jr.options, function (o) { return o.value === besoin; });
        conf = isInternal ? { recycle: besoin } : null;
    }
    if (!conf) return; // valeur absente ou inconnue : comportement par défaut
    devisBesoin = besoin;

    var lieu = LIEU_MAP[normalizeParam(params.get('lieu'))] || '';
    var target = null;

    if (!setSelectValue('je_recycle', conf.recycle)) return;
    target = document.getElementById({
        deee: 'deee_wrapper', destruction_archives: 'destruction_archives_wrapper',
        louer_benne: 'louer_benne_wrapper', dechets_chantiers: 'dechets_chantiers_wrapper',
        dechets_non_dangereux: 'dechets_nd_wrapper', dechets_bureau: 'dechets_bureau_wrapper',
        mobilier_bureau: 'volume-section', debarrasser_local: 'volume-section'
    }[conf.recycle]);

    // Lieu seulement s'il est donné explicitement ; le type reste au choix du visiteur
    if (conf.recycle === 'deee' && lieu) {
        setSelectValue('lieu_deee', 'type_deee_' + lieu);
        target = document.getElementById('type_deee_' + lieu + '_wrapper') || target;
    } else if (conf.recycle === 'destruction_archives' && lieu) {
        setSelectValue('lieu_archives', 'destruction_archives_' + lieu);
        target = document.getElementById('destruction_archives_' + lieu + '_wrapper') || target;
    }

    var choice = conf.choice;
    if (choice) {
        var choiceEl = document.querySelector('.choice[data-value="' + choice + '"]');
        if (choiceEl) {
            choiceEl.click();
            var wrapper = document.getElementById({
                ferrailles: 'dnd_ferrailles_wrapper', cartons: 'dnd_cartons_wrapper', papiers: 'dnd_papiers_wrapper',
                plastiques: 'dnd_plastiques_wrapper', palettes: 'dnd_palettes_wrapper', encombrants: 'dnd_encombrants_wrapper',
                dib: 'dnd_dib_wrapper', bois: 'dnd_bois_wrapper', dechets_vert: 'dnd_dechets_verts_wrapper'
            }[choice]);
            if (wrapper) target = wrapper;
        }
    }

    if (target) {
        setTimeout(function () {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }
}
