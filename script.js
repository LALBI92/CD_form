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
    document.getElementById('step-2').style.display = 'block';
    document.getElementById('step-1').style.display = 'none';

    // Faire défiler la page vers le haut
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Ajoute l'événement au clic pour les camions
const choisirButtons = document.querySelectorAll(".choisir-camion");
choisirButtons.forEach(button => {
    button.addEventListener("click", function () {
        const camionId = this.closest(".camion").id;
        selectCamion(camionId);
    });
});


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
        "ampoules_lampes_neons_domicile": "ampoules_domicile_wrapper",
        "informatiques_bureautiques_depot": "informatiques_bureautiques_depot_wrapper",
        "cartouches_encres_toners_depot": "cartouches_depot_encres_wrapper",
        "accumulateurs_batteries_piles_depot": "piles_depot_wrapper",
        "electromenager_chaud_froid_depot": "electromenager_depot_wrapper",
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

function hideAllDeeeSections() {
    // Masquer les sections domicile et entrepôt
    hideDeeeDomicileSections();
    hideDeeedepotSections();

    // Masquer les sélecteurs domicile et entrepôt eux-mêmes
    const typeDeeeDomicileWrapper = document.getElementById("type_deee_domicile_wrapper");
    const typeDeeedepotWrapper = document.getElementById("type_deee_depot_wrapper");

    if (typeDeeeDomicileWrapper) typeDeeeDomicileWrapper.style.display = "none";
    if (typeDeeedepotWrapper) typeDeeedepotWrapper.style.display = "none";
}

function resetDeeeSelectFields() {
    const deeeDomicileSelect = document.getElementById("deee_domicile_select");
    const deeedepotSelect = document.getElementById("deee_depot_select");

    if (deeeDomicileSelect) deeeDomicileSelect.value = ""; // Réinitialiser à la valeur par défaut
    if (deeedepotSelect) deeedepotSelect.value = ""; // Réinitialiser à la valeur par défaut
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
    ["dib_chantier", "bois_chantier", /* autres sections pertinentes */].forEach(section => {
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

     // Function to update the total volume
function updateTotalVolume() {
    let totalVolume = 0;
    // Cibler uniquement les inputs avec la classe 'volume-control' et un attribut 'data-volume'
    const volumeInputs = document.querySelectorAll("input.volume-control[data-volume]");

    volumeInputs.forEach(input => {
        const quantity = parseInt(input.value);
        const volumePerItem = parseFloat(input.getAttribute("data-volume"));
        if (!isNaN(quantity) && !isNaN(volumePerItem)) {
            totalVolume += quantity * volumePerItem;
        }
    });

    document.getElementById("volume-result").textContent = totalVolume.toFixed(2);
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

// Ajouter les événements de clic pour les boutons "Choisir ce camion"
const choisirButtons = document.querySelectorAll(".choisir-camion");
choisirButtons.forEach(button => {
    button.addEventListener("click", function () {
        const camionId = this.closest(".camion").id; // Trouver l'ID du camion parent
        selectCamion(camionId);
    });
});


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

// Validation du formulaire
document.getElementById("devisForm").addEventListener("submit", function(e) {
    if (professionSelect.value === "2" && !siretInput.value.trim()) {
        e.preventDefault();
        alert("Le SIRET est obligatoire pour un professionnel");
        siretInput.focus();
    }
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

            // Ajouter un écouteur pour l'étage afin de gérer l'affichage de l'ascenseur
            etageSelect.addEventListener('change', function () {
                const selectedEtage = etageSelect.value;
                console.log("Étage sélectionné : " + selectedEtage);

                // Si l'étage est supérieur ou égal à 1, afficher le champ ascenseur
                if (parseInt(selectedEtage) >= 1) {
                    ascenseurBlock.style.display = 'block';
                } else {
                    ascenseurBlock.style.display = 'none';
                }
            });
        }

        // Bouton Retour (de Step 2 vers Step 1)
    document.getElementById('previous-step').addEventListener('click', function(event) {
        event.preventDefault(); // Empêche tout comportement par défaut du bouton
        document.getElementById('step-2').style.display = 'none';
        document.getElementById('step-1').style.display = 'block';
    });

        // Ajout de la validation du bouton "Suivant"
        document.getElementById('next-step').addEventListener('click', function(event) {
            // Empêcher le comportement par défaut
            event.preventDefault();

            // Vérification du type de besoin (déjà existant)
            const typeBesoin = document.getElementById('type_besoin');
            let typeBesoinMessage = document.getElementById('type_besoin_message');

            if (typeBesoin.value === "") {
                if (!typeBesoinMessage) {
                    typeBesoinMessage = document.createElement('div');
                    typeBesoinMessage.id = 'type_besoin_message';
                    typeBesoinMessage.style.color = 'red';
                    typeBesoinMessage.style.marginTop = '5px';
                    typeBesoinMessage.innerText = 'Veuillez sélectionner un type de besoin.';
                    typeBesoin.parentElement.appendChild(typeBesoinMessage);
                }
                return;
            } else if (typeBesoinMessage) {
                typeBesoinMessage.remove();
            }

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
            document.getElementById('step-2').style.display = 'block';
            document.getElementById('step-1').style.display = 'none';

            // Faire défiler la page vers le haut
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
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









