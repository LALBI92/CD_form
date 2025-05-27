// Fonction pour créer et gérer une modale d'information
function createInfoModal(productId, imageUrl) {
    // Créer le bouton d'information
    const infoButton = document.createElement('button');
    infoButton.className = 'info-button';
    infoButton.innerHTML = '<i class="fas fa-info-circle"></i>';
    infoButton.type = 'button'; // Empêche la soumission du formulaire
    
    // Créer la modale
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <button class="close-modal">&times;</button>
            <img src="" alt="Information produit">
        </div>
    `;

    // Ajouter la modale au body
    document.body.appendChild(modal);

    // Gérer l'ouverture de la modale
    infoButton.addEventListener('click', (e) => {
        e.preventDefault(); // Empêche la soumission du formulaire
        const modalImg = modal.querySelector('img');
        modalImg.src = imageUrl; // Charge l'image uniquement au clic
        modal.style.display = 'flex';
    });

    // Gérer la fermeture de la modale
    const closeButton = modal.querySelector('.close-modal');
    closeButton.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Fermer la modale en cliquant en dehors
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Trouver le conteneur .benne correspondant et y ajouter le bouton
    const benneContainer = document.querySelector(`.benne label[for="${productId}"]`)?.parentElement;
    if (benneContainer && benneContainer.classList.contains('benne')) {
        benneContainer.appendChild(infoButton);
    } else {
        console.error(`Conteneur .benne non trouvé pour l'ID: ${productId}`);
    }

    return { infoButton, modal };
}

// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM chargé, initialisation de la modale...');
    createInfoModal('benne_10m3_papiers', 'images/benne_10m3.png');
createInfoModal('benne_15m3_papiers', 'images/benne_15m3.png');
createInfoModal('benne_30m3_papiers', 'images/benne_30m3.png');
createInfoModal('benne_10m3_cartons', 'images/benne_10m3.png');
createInfoModal('benne_15m3_cartons', 'images/benne_15m3.png');
createInfoModal('benne_30m3_cartons', 'images/benne_30m3.png');
createInfoModal('benne_10m3_plastiques', 'images/benne_10m3.png');
createInfoModal('benne_15m3_plastiques', 'images/benne_15m3.png');
createInfoModal('benne_30m3_plastiques', 'images/benne_30m3.png');
createInfoModal('benne_10m3_palettes', 'images/benne_10m3.png');
createInfoModal('benne_15m3_palettes', 'images/benne_15m3.png');
createInfoModal('benne_30m3_palettes', 'images/benne_30m3.png');
createInfoModal('benne_10m3_encombrants', 'images/benne_10m3.png');
createInfoModal('benne_15m3_encombrants', 'images/benne_15m3.png');
createInfoModal('benne_30m3_encombrants', 'images/benne_30m3.png');
createInfoModal('benne_10m3_dib', 'images/benne_10m3.png');
createInfoModal('benne_15m3_dib', 'images/benne_15m3.png');
createInfoModal('benne_30m3_dib', 'images/benne_30m3.png');
createInfoModal('benne_10m3_bois', 'images/benne_10m3.png');
createInfoModal('benne_15m3_bois', 'images/benne_15m3.png');
createInfoModal('benne_30m3_bois', 'images/benne_30m3.png');
createInfoModal('benne_10m3_ferrailles', 'images/benne_10m3.png');
createInfoModal('benne_15m3_ferrailles', 'images/benne_15m3.png');
createInfoModal('benne_30m3_ferrailles', 'images/benne_30m3.png');
createInfoModal('benne_10m3_dechets_verts', 'images/benne_10m3.png');
createInfoModal('benne_15m3_dechets_verts', 'images/benne_15m3.png');
createInfoModal('benne_30m3_dechets_verts', 'images/benne_30m3.png');
createInfoModal('box_90L_archives_domicile', 'images/box_securise_90.png');
createInfoModal('box_240L_archives_domicile', 'images/box_securise_240.png');
createInfoModal('box_480L_archives_domicile', 'images/box_securise_480.png');
createInfoModal('benne_8m3_dechets', 'images/benne_8m3.png');
createInfoModal('benne_10m3_dechets', 'images/benne_10m3.png');
createInfoModal('benne_15m3_dechets', 'images/benne_15m3.png');
createInfoModal('benne_30m3_dechets', 'images/benne_30m3.png');
createInfoModal('box_d3e_domicile', 'images/box_D3E_70.png');
createInfoModal('caisse_palette_domicile', 'images/caisse_grillage_deee.png');
createInfoModal('bac_550l_domicile', 'images/bac_deee_550.png');
createInfoModal('box_ampoules_domicile', 'images/box_ampoules.png');
createInfoModal('box_neons_domicile', 'images/box_neons.png');
createInfoModal('bac_550l_ampoules_domicile', 'images/box_ampoules_neos_550.png');
createInfoModal('box_cartouche_domicile', 'images/box_cartouches_70.png');
createInfoModal('bac_550l_cartouche_domicile', 'images/box_cartouches _550.png');
createInfoModal('box_90L_archives_depot', 'images/box_securise_90.png');
createInfoModal('box_240L_archives_depot', 'images/box_securise_240.png');
createInfoModal('box_480L_archives_depot', 'images/box_securise_480.png');
createInfoModal('box_d3e', 'images/box_D3E_70.png');
createInfoModal('box_secure_90', 'images/box_securise_90.png');
createInfoModal('box_secure_120', 'images/box_120L.png');
createInfoModal('box_secure_240', 'images/box_securise_240.png');
createInfoModal('box_secure_480', 'images/box_securise_480.png');
createInfoModal('bac_secure_240_ouvert_domicile', 'images/bac_240.png');
createInfoModal('bac_secure_550_ouvert_domicile', 'images/bac_550.png');
createInfoModal('box_130L_archives_depot_2', 'images/120?');
createInfoModal('benne_20m3_Dib', 'images/benne_2Om3.png');
createInfoModal('benne_20m3_encombrants', 'images/benne_2Om3.png');
createInfoModal('benne_20m3_palettes', 'images/benne_2Om3.png');
createInfoModal('benne_10m3_platres', 'images/benne_10m3.png');
createInfoModal('bac_industriel_200_DEEE_domicile', 'images/bac_deee_200.png');
createInfoModal('bac_toner_200l_domicile', 'images/box_cartouches_200.png');
createInfoModal('bac_200l_ampoules_domicile', 'images/box_ampoules_neons_200.png');
createInfoModal('bac_secure_550_ouvert_depot', 'images/bac_550.png');
createInfoModal('box_d3e_depot', 'images/box_D3E_70.png');
createInfoModal('bac_550l_depot', 'images/bac_deee_550.png');
createInfoModal('caisse_palette_depot', 'images/caisse_grillage_deee.png');
createInfoModal('bac_industriel_200_DEEE_depot', 'images/bac_deee_200.png');
createInfoModal('bac_550l_ampoules_depot', 'images/box_ampoules_neos_550.png');
createInfoModal('box_neons_depot', 'images/box_neons.png');
createInfoModal('box_ampoules_depot', 'images/box_ampoules.png');
createInfoModal('bac_200l_ampoules_depot', 'images/box_ampoules_neons_200.png');
createInfoModal('box_cartouche_depot', 'images/box_cartouches_70.png');
createInfoModal('bac_550l_cartouche_depot', 'images/box_cartouches _550.png');
createInfoModal('bac_toner_200l_depot', 'images/box_cartouches_200.png');
createInfoModal('bac_secure_240_ouvert_depot', 'images/bac_240.png');
createInfoModal('palette_660l_papiers',       'images/palette_660.png');
createInfoModal('bac_roulant_770l_papiers',   'images/bac_770.png');
createInfoModal('palette_660l_cartons',       'images/palette_660.png');
createInfoModal('bac_roulant_770l_cartons',   'images/bac_770.png');
createInfoModal('palette_660l_plastiques',    'images/palette_660.png');
createInfoModal('bac_roulant_770l_plastiques','images/bac_770.png');
createInfoModal('palette_660l_bois',          'images/palette_660.png');
createInfoModal('palette_660l_ferrailles',    'images/palette_660.png');
createInfoModal('benne_chaine_8m3_dib_chantier',            'images/benne_chaine_8m3.png');
createInfoModal('benne_chaine_8m3_gravats_melange_chantier','images/benne_chaine_8m3.png');
createInfoModal('benne_chaine_8m3_gravats_propres_chantier','images/benne_chaine_8m3.png');
createInfoModal('benne_chaine_8m3_bois_chantier',           'images/benne_chaine_8m3.png');
createInfoModal('benne_chaine_8m3_platre_chantier',         'images/benne_chaine_8m3.png');
createInfoModal('benne_8m3_gravats',                        'images/benne_chaine_8m3.png');
createInfoModal('box_130L_archives_depot',     'images/box_120L.png');
createInfoModal('box_130L_archives_domicile',  'images/box_120L.png');
createInfoModal('benne_chaine_15m3_bois_chantier',  'images/benne_15m3.png');
createInfoModal('benne_chaine_15m3_dib_chantier',   'images/benne_15m3.png');
createInfoModal('benne_ampliroll_30m3_dib_chantier','images/benne_30m3.png');
createInfoModal('benne_ampliroll_30m3_bois_chantier','images/benne_30m3.png');
createInfoModal('big_bag_1m3_dib_chantier',                 'images/bigbag.png');
createInfoModal('big_bag_1m3_bois_chantier',                'images/bigbag.png');
createInfoModal('big_bag_1m3_platre_chantier',              'images/bigbag.png');
createInfoModal('big_bag_1m3_gravats_melange_chantier',     'images/bigbag.png');
createInfoModal('big_bag_1m3_gravats_propres_chantier',     'images/bigbag.png');
createInfoModal('benne_ampliroll_3m3_bois_chantier',        'images/benne_ampliroll_3m3.png');
createInfoModal('benne_ampliroll_3m3_gravats_melange_chantier','images/benne_ampliroll_3m3.png');
createInfoModal('benne_ampliroll_3m3_gravats_propres_chantier','images/benne_ampliroll_3m3.png');



}); 