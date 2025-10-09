// Mettre à jour également l'avatar dans la sidebar si présent
    const sidebarAvatar = document.querySelector('.avatar-display');
    if (sidebarAvatar) {
        sidebarAvatar.style.backgroundColor = color;
        sidebarAvatar.style.transition = 'background-color 0.3s ease';
    }

/**
 * Initialiser le formulaire d'avatar
 */
function initAvatarForm() {
    const avatarForm = document.getElementById('avatarForm');
    
    if (!avatarForm) return;
    
    avatarForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Vérifier qu'une couleur est sélectionnée
        if (!selectedColor) {
            PawConnect.showToast('Veuillez sélectionner une couleur', 'warning');
            return;
        }
        
        // Bouton de soumission
        const submitBtn = this.querySelector('button[type="submit"]');
        
        // Activer le loading
        PawConnect.setButtonLoading(submitBtn, true);
        
        try {
            // Préparer les données
            const formData = new FormData(this);
            
            // Envoyer la requête
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                PawConnect.showToast('Avatar mis à jour avec succès !', 'success');
                
                // Mettre à jour tous les avatars de la page
                updateAllAvatars(selectedColor);
                
                // Logger l'activité
                PawConnect.logActivity('avatar_updated', { color: selectedColor });
                
            } else {
                PawConnect.showToast(data.message || 'Erreur lors de la mise à jour', 'error');
            }
            
        } catch (error) {
            console.error('Erreur:', error);
            PawConnect.showToast('Une erreur est survenue', 'error');
        } finally {
            PawConnect.setButtonLoading(submitBtn, false);
        }
    });
}

/**
 * Mettre à jour tous les avatars de la page
 */
function updateAllAvatars(color) {
    // Avatar principal
    const mainAvatar = document.querySelector('.avatar-display');
    if (mainAvatar) {
        mainAvatar.style.backgroundColor = color;
    }
    
    // Avatar dans le menu
    const menuAvatar = document.querySelector('.navbar .avatar-display');
    if (menuAvatar) {
        menuAvatar.style.backgroundColor = color;
    }
    
    // Tous les aperçus d'avatar
    document.querySelectorAll('[id^="avatarPreview"]').forEach(preview => {
        preview.style.backgroundColor = color;
    });
}

/**
 * Générer un avatar aléatoire
 */
function generateRandomAvatar() {
    const randomColor = AVATAR_COLORS[Math.floor(Math.random() * AVATAR_COLORS.length)];
    
    // Sélectionner la couleur dans la palette
    const colorElement = document.querySelector(`.avatar-color[data-color="${randomColor}"]`);
    if (colorElement) {
        selectAvatarColor(colorElement);
    }
    
    PawConnect.showToast('Couleur aléatoire sélectionnée !', 'info');
}

/**
 * Copier le code couleur
 */
function copyColorCode() {
    if (!selectedColor) {
        PawConnect.showToast('Aucune couleur sélectionnée', 'warning');
        return;
    }
    
    PawConnect.copyToClipboard(selectedColor);
}

/**
 * Obtenir les initiales à partir du nom
 */
function getInitials(name) {
    if (!name) return '?';
    
    const parts = name.trim().split(' ');
    if (parts.length === 1) {
        return parts[0].substring(0, 2).toUpperCase();
    }
    
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

/**
 * Créer un élément avatar
 */
function createAvatarElement(name, color, size = 50) {
    const avatar = document.createElement('div');
    avatar.className = 'avatar-display';
    avatar.style.cssText = `
        width: ${size}px;
        height: ${size}px;
        background-color: ${color};
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: ${size * 0.4}px;
        text-transform: uppercase;
    `;
    avatar.textContent = getInitials(name);
    
    return avatar;
}

/**
 * Prévisualiser l'avatar en temps réel pendant la saisie du nom
 */
function initNamePreview() {
    const nameInput = document.getElementById('nom');
    const preview = document.getElementById('avatarPreview');
    
    if (nameInput && preview) {
        nameInput.addEventListener('input', function() {
            const initials = getInitials(this.value);
            preview.textContent = initials;
        });
    }
}

// Initialiser la prévisualisation du nom
document.addEventListener('DOMContentLoaded', initNamePreview);

/**
 * Sauvegarder la préférence de couleur dans le localStorage
 */
function saveColorPreference(color) {
    try {
        localStorage.setItem('paw_avatar_color', color);
    } catch (error) {
        console.error('Erreur sauvegarde localStorage:', error);
    }
}

/**
 * Charger la préférence de couleur depuis le localStorage
 */
function loadColorPreference() {
    try {
        return localStorage.getItem('paw_avatar_color');
    } catch (error) {
        console.error('Erreur chargement localStorage:', error);
        return null;
    }
}

/**
 * Ajouter un bouton pour générer une couleur aléatoire
 */
function addRandomColorButton() {
    const palette = document.querySelector('.avatar-palette');
    
    if (!palette) return;
    
    const randomBtn = document.createElement('button');
    randomBtn.type = 'button';
    randomBtn.className = 'btn btn-outline-secondary btn-sm mt-3 w-100';
    randomBtn.innerHTML = '<i class="fas fa-random me-2"></i> Couleur aléatoire';
    randomBtn.addEventListener('click', generateRandomAvatar);
    
    palette.parentElement.appendChild(randomBtn);
}

// Ajouter le bouton aléatoire au chargement
document.addEventListener('DOMContentLoaded', addRandomColorButton);

/**
 * Animation de pulsation pour l'avatar sélectionné
 */
function animateSelectedAvatar() {
    const selected = document.querySelector('.avatar-color.selected');
    
    if (selected) {
        selected.style.animation = 'pulse 1s ease-in-out';
        setTimeout(() => {
            selected.style.animation = '';
        }, 1000);
    }
}

// Style pour l'animation de pulsation
const pulseStyle = document.createElement('style');
pulseStyle.textContent = `
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(30, 58, 138, 0.7);
        }
        50% {
            transform: scale(1.1);
            box-shadow: 0 0 0 10px rgba(30, 58, 138, 0);
        }
    }
`;
document.head.appendChild(pulseStyle);

/**
 * Validation de la couleur hexadécimale
 */
function isValidHexColor(color) {
    return /^#[0-9A-F]{6}$/i.test(color);
}

/**
 * Convertir une couleur hex en RGB
 */
function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

/**
 * Déterminer si une couleur est claire ou foncée
 */
function isLightColor(hex) {
    const rgb = hexToRgb(hex);
    if (!rgb) return false;
    
    // Formule de luminance
    const luminance = (0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b) / 255;
    return luminance > 0.5;
}

/**
 * Ajuster la couleur du texte selon le fond
 */
function adjustTextColor(backgroundColor) {
    return isLightColor(backgroundColor) ? '#000000' : '#FFFFFF';
}

/**
 * Exporter la configuration d'avatar
 */
function exportAvatarConfig() {
    const config = {
        color: selectedColor,
        colors: AVATAR_COLORS,
        timestamp: new Date().toISOString()
    };
    
    const dataStr = JSON.stringify(config, null, 2);
    const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
    
    const exportLink = document.createElement('a');
    exportLink.setAttribute('href', dataUri);
    exportLink.setAttribute('download', 'avatar-config.json');
    exportLink.click();
    
    PawConnect.showToast('Configuration exportée !', 'success');
}

/**
 * Réinitialiser la sélection d'avatar
 */
function resetAvatarSelection() {
    selectedColor = null;
    
    document.querySelectorAll('.avatar-color').forEach(el => {
        el.classList.remove('selected');
    });
    
    const hiddenInput = document.getElementById('selectedColor');
    if (hiddenInput) {
        hiddenInput.value = '';
    }
    
    PawConnect.showToast('Sélection réinitialisée', 'info');
}

/**
 * Prévisualiser l'avatar avant sauvegarde
 */
function previewAvatarInContext() {
    if (!selectedColor) {
        PawConnect.showToast('Sélectionnez d\'abord une couleur', 'warning');
        return;
    }
    
    // Créer une modal de prévisualisation
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Aperçu de votre avatar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-5">
                    <div style="width: 150px; height: 150px; background-color: ${selectedColor}; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: white; font-weight: 700;">
                        ${getInitials(document.getElementById('nom')?.value || 'User')}
                    </div>
                    <p class="mt-4 text-muted">Votre avatar apparaîtra ainsi dans votre profil et vos commentaires</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('avatarForm').submit()">
                        <i class="fas fa-save me-2"></i> Valider ce choix
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    modal.addEventListener('hidden.bs.modal', function() {
        modal.remove();
    });
}

/**
 * Ajouter des effets de hover sur les couleurs
 */
function addHoverEffects() {
    const colors = document.querySelectorAll('.avatar-color');
    
    colors.forEach(color => {
        color.addEventListener('mouseenter', function() {
            if (!this.classList.contains('selected')) {
                this.style.transform = 'scale(1.15)';
                this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
            }
        });
        
        color.addEventListener('mouseleave', function() {
            if (!this.classList.contains('selected')) {
                this.style.transform = 'scale(1)';
                this.style.boxShadow = 'none';
            }
        });
    });
}

// Ajouter les effets de hover au chargement
document.addEventListener('DOMContentLoaded', addHoverEffects);

/**
 * Créer un sélecteur de couleur custom
 */
function createCustomColorPicker() {
    const palette = document.querySelector('.avatar-palette');
    
    if (!palette) return;
    
    const customPicker = document.createElement('div');
    customPicker.className = 'mt-3 text-center';
    customPicker.innerHTML = `
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="AvatarModule.openColorPicker()">
            <i class="fas fa-palette me-2"></i> Couleur personnalisée
        </button>
    `;
    
    palette.parentElement.appendChild(customPicker);
}

/**
 * Ouvrir le sélecteur de couleur natif
 */
function openColorPicker() {
    const input = document.createElement('input');
    input.type = 'color';
    input.value = selectedColor || '#1E3A8A';
    
    input.addEventListener('change', function() {
        const color = this.value.toUpperCase();
        
        // Ajouter la couleur personnalisée à la palette
        if (!AVATAR_COLORS.includes(color)) {
            addCustomColorToPalette(color);
        }
        
        // Sélectionner la couleur
        const colorElement = document.querySelector(`.avatar-color[data-color="${color}"]`);
        if (colorElement) {
            selectAvatarColor(colorElement);
        }
    });
    
    input.click();
}

/**
 * Ajouter une couleur personnalisée à la palette
 */
function addCustomColorToPalette(color) {
    const palette = document.querySelector('.avatar-palette');
    
    if (!palette) return;
    
    const colorDiv = document.createElement('div');
    colorDiv.className = 'avatar-color';
    colorDiv.style.backgroundColor = color;
    colorDiv.dataset.color = color;
    colorDiv.title = 'Couleur personnalisée';
    
    colorDiv.addEventListener('click', function() {
        selectAvatarColor(this);
    });
    
    palette.appendChild(colorDiv);
    
    // Sélectionner automatiquement
    selectAvatarColor(colorDiv);
    
    PawConnect.showToast('Couleur personnalisée ajoutée !', 'success');
}

/**
 * Sauvegarder l'historique des couleurs utilisées
 */
function saveColorHistory(color) {
    try {
        let history = JSON.parse(localStorage.getItem('paw_color_history') || '[]');
        
        // Ajouter la nouvelle couleur au début
        history = [color, ...history.filter(c => c !== color)];
        
        // Garder seulement les 5 dernières
        history = history.slice(0, 5);
        
        localStorage.setItem('paw_color_history', JSON.stringify(history));
    } catch (error) {
        console.error('Erreur sauvegarde historique:', error);
    }
}

/**
 * Afficher l'historique des couleurs
 */
function showColorHistory() {
    try {
        const history = JSON.parse(localStorage.getItem('paw_color_history') || '[]');
        
        if (history.length === 0) {
            PawConnect.showToast('Aucun historique de couleurs', 'info');
            return;
        }
        
        const palette = document.querySelector('.avatar-palette');
        if (!palette) return;
        
        // Créer une section d'historique
        let historySection = document.querySelector('.color-history');
        
        if (!historySection) {
            historySection = document.createElement('div');
            historySection.className = 'color-history mt-4';
            historySection.innerHTML = '<p class="text-muted mb-2"><small>Couleurs récentes :</small></p>';
            palette.parentElement.appendChild(historySection);
        }
        
        const historyPalette = document.createElement('div');
        historyPalette.className = 'avatar-palette';
        historyPalette.style.gridTemplateColumns = 'repeat(5, 1fr)';
        
        history.forEach(color => {
            const colorDiv = document.createElement('div');
            colorDiv.className = 'avatar-color';
            colorDiv.style.backgroundColor = color;
            colorDiv.dataset.color = color;
            colorDiv.style.width = '50px';
            colorDiv.style.height = '50px';
            
            colorDiv.addEventListener('click', function() {
                selectAvatarColor(this);
            });
            
            historyPalette.appendChild(colorDiv);
        });
        
        historySection.innerHTML = '<p class="text-muted mb-2"><small>Couleurs récentes :</small></p>';
        historySection.appendChild(historyPalette);
        
    } catch (error) {
        console.error('Erreur affichage historique:', error);
    }
}

// Afficher l'historique au chargement
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(showColorHistory, 500);
});

/**
 * Appliquer un effet arc-en-ciel sur la palette
 */
function rainbowEffect() {
    const colors = document.querySelectorAll('.avatar-color');
    
    colors.forEach((color, index) => {
        setTimeout(() => {
            color.style.transform = 'scale(1.2) rotate(360deg)';
            color.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                color.style.transform = 'scale(1) rotate(0deg)';
            }, 500);
        }, index * 100);
    });
}

/**
 * Easter egg - Code Konami pour débloquer des couleurs secrètes
 */
let konamiCode = [];
const konamiSequence = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65]; // ↑↑↓↓←→←→BA

document.addEventListener('keydown', function(e) {
    konamiCode.push(e.keyCode);
    konamiCode = konamiCode.slice(-10);
    
    if (JSON.stringify(konamiCode) === JSON.stringify(konamiSequence)) {
        unlockSecretColors();
    }
});

function unlockSecretColors() {
    const secretColors = ['#FF1493', '#00CED1', '#FFD700', '#FF6347', '#9370DB'];
    
    secretColors.forEach(color => {
        if (!AVATAR_COLORS.includes(color)) {
            AVATAR_COLORS.push(color);
            addCustomColorToPalette(color);
        }
    });
    
    rainbowEffect();
    PawConnect.showToast('🎉 Couleurs secrètes débloquées !', 'success');
}

// Exposer les fonctions au scope global
window.AvatarModule = {
    selectAvatarColor,
    updateAvatarPreview,
    generateRandomAvatar,
    copyColorCode,
    getInitials,
    createAvatarElement,
    isValidHexColor,
    hexToRgb,
    isLightColor,
    exportAvatarConfig,
    resetAvatarSelection,
    previewAvatarInContext,
    openColorPicker,
    showColorHistory,
    rainbowEffect,
    AVATAR_COLORS
};

console.log('🎨 Avatar Module chargé avec succès');/**
 * PAW CONNECT - AVATAR MANAGEMENT
 * Gestion de la palette de couleurs d'avatars
 */

// Palette de couleurs disponibles
const AVATAR_COLORS = [
    '#1E3A8A', // Bleu foncé
    '#60A5FA', // Bleu clair
    '#10B981', // Vert
    '#F59E0B', // Orange
    '#EF4444', // Rouge
    '#8B5CF6', // Violet
    '#EC4899', // Rose
    '#14B8A6'  // Turquoise
];

let selectedColor = null;

/**
 * Initialisation
 */
document.addEventListener('DOMContentLoaded', function() {
    initAvatarPalette();
    initAvatarForm();
    initNamePreview();
    addHoverEffects();
    addRandomColorButton();
    createCustomColorPicker();
    showColorHistory();
});

/**
 * Initialiser la palette d'avatars
 */
function initAvatarPalette() {
    const avatarPalette = document.querySelector('.avatar-palette');
    
    if (!avatarPalette) return;
    
    // Récupérer la couleur actuellement sélectionnée
    const currentColor = document.getElementById('selectedColor')?.value;
    
    // Créer les cercles de couleur
    avatarPalette.innerHTML = '';
    
    AVATAR_COLORS.forEach(color => {
        const colorDiv = document.createElement('div');
        colorDiv.className = 'avatar-color';
        colorDiv.style.backgroundColor = color;
        colorDiv.dataset.color = color;
        
        // Marquer comme sélectionné si c'est la couleur actuelle
        if (color === currentColor) {
            colorDiv.classList.add('selected');
            selectedColor = color;
        }
        
        // Événement de clic
        colorDiv.addEventListener('click', function() {
            selectAvatarColor(this);
        });
        
        avatarPalette.appendChild(colorDiv);
    });
}

/**
 * Sélectionner une couleur d'avatar
 */
function selectAvatarColor(element) {
    // Retirer la sélection précédente
    document.querySelectorAll('.avatar-color').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Ajouter la sélection à l'élément cliqué
    element.classList.add('selected');
    
    // Récupérer la couleur
    selectedColor = element.dataset.color;
    
    // Mettre à jour l'input caché
    const hiddenInput = document.getElementById('selectedColor');
    if (hiddenInput) {
        hiddenInput.value = selectedColor;
    }
    
    // Mettre à jour l'aperçu
    updateAvatarPreview(selectedColor);
    
    // Sauvegarder dans l'historique
    saveColorHistory(selectedColor);
    
    // Animation
    element.style.transform = 'scale(1.2)';
    setTimeout(() => {
        element.style.transform = 'scale(1)';
    }, 200);
}

/**
 * Mettre à jour l'aperçu de l'avatar
 */
function updateAvatarPreview(color) {
    const preview = document.getElementById('avatarPreview');
    
    if (preview) {
        preview.style.backgroundColor = color;
        preview.style.transition = 'background-color 0.3s ease';
    }
    
    // Mettre à jour également l'avatar dans la sidebar si présent
    const sidebarAvatar = document.querySelector('.avatar-display');
    if (sidebarAvatar) {
        sidebarAvatar.style.backgroundColor = color;
        sidebarAvatar.style.transition = 'background-color 0.3s ease';
    }
}

/**
 * Initialiser le formulaire d'avatar
 */
function initAvatarForm() {
    const avatarForm = document.getElementById('avatarForm');
    
    if (!avatarForm) return;
    
    avatarForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Vérifier qu'une couleur est sélectionnée
        if (!selectedColor) {
            PawConnect.showToast('Veuillez sélectionner une couleur', 'warning');
            return;
        }
        
        // Bouton de soumission
        const submitBtn = this.querySelector('button[type="submit"]');
        
        // Activer le loading
        PawConnect.setButtonLoading(submitBtn, true);
        
        try {
            // Préparer les données
            const formData = new FormData(this);
            
            // Envoyer la requête
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                PawConnect.showToast('Avatar mis à jour avec succès !', 'success');
                
                // Mettre à jour tous les avatars de la page
                updateAllAvatars(selectedColor);
                
                // Logger l'activité
                if (window.PawConnect && PawConnect.logActivity) {
                    PawConnect.logActivity('avatar_updated', { color: selectedColor });
                }
                
            } else {
                PawConnect.showToast(data.message || 'Erreur lors de la mise à jour', 'error');
            }
            
        } catch (error) {
            console.error('Erreur:', error);
            PawConnect.showToast('Une erreur est survenue', 'error');
        } finally {
            PawConnect.setButtonLoading(submitBtn, false);
        }
    });
}

/**
 * Mettre à jour tous les avatars de la page
 */
function updateAllAvatars(color) {
    document.querySelectorAll('.avatar-display, #avatarPreview').forEach(avatar => {
        avatar.style.backgroundColor = color;
    });
}

/**
 * Générer un avatar aléatoire
 */
function generateRandomAvatar() {
    const randomColor = AVATAR_COLORS[Math.floor(Math.random() * AVATAR_COLORS.length)];
    
    const colorElement = document.querySelector(`.avatar-color[data-color="${randomColor}"]`);
    if (colorElement) {
        selectAvatarColor(colorElement);
    }
    
    PawConnect.showToast('Couleur aléatoire sélectionnée !', 'info');
}

/**
 * Copier le code couleur
 */
function copyColorCode() {
    if (!selectedColor) {
        PawConnect.showToast('Aucune couleur sélectionnée', 'warning');
        return;
    }
    
    PawConnect.copyToClipboard(selectedColor);
}

/**
 * Obtenir les initiales à partir du nom
 */
function getInitials(name) {
    if (!name) return '?';
    
    const parts = name.trim().split(' ');
    if (parts.length === 1) {
        return parts[0].substring(0, 2).toUpperCase();
    }
    
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

/**
 * Créer un élément avatar
 */
function createAvatarElement(name, color, size = 50) {
    const avatar = document.createElement('div');
    avatar.className = 'avatar-display';
    avatar.style.cssText = `
        width: ${size}px;
        height: ${size}px;
        background-color: ${color};
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: ${size * 0.4}px;
        text-transform: uppercase;
    `;
    avatar.textContent = getInitials(name);
    
    return avatar;
}

/**
 * Prévisualiser l'avatar en temps réel pendant la saisie du nom
 */
function initNamePreview() {
    const nameInput = document.getElementById('nom');
    const preview = document.getElementById('avatarPreview');
    
    if (nameInput && preview) {
        nameInput.addEventListener('input', function() {
            const initials = getInitials(this.value);
            preview.textContent = initials;
        });
    }
}

/**
 * Sauvegarder la préférence de couleur dans le localStorage
 */
function saveColorHistory(color) {
    try {
        let history = JSON.parse(localStorage.getItem('paw_color_history') || '[]');
        history = [color, ...history.filter(c => c !== color)];
        history = history.slice(0, 5);
        localStorage.setItem('paw_color_history', JSON.stringify(history));
    } catch (error) {
        console.error('Erreur sauvegarde historique:', error);
    }
}

/**
 * Afficher l'historique des couleurs
 */
function showColorHistory() {
    try {
        const history = JSON.parse(localStorage.getItem('paw_color_history') || '[]');
        
        if (history.length === 0) return;
        
        const palette = document.querySelector('.avatar-palette');
        if (!palette) return;
        
        let historySection = document.querySelector('.color-history');
        
        if (!historySection) {
            historySection = document.createElement('div');
            historySection.className = 'color-history mt-4';
            palette.parentElement.appendChild(historySection);
        }
        
        const historyPalette = document.createElement('div');
        historyPalette.className = 'avatar-palette';
        historyPalette.style.gridTemplateColumns = 'repeat(5, 1fr)';
        
        history.forEach(color => {
            const colorDiv = document.createElement('div');
            colorDiv.className = 'avatar-color';
            colorDiv.style.backgroundColor = color;
            colorDiv.style.width = '50px';
            colorDiv.style.height = '50px';
            colorDiv.dataset.color = color;
            
            colorDiv.addEventListener('click', function() {
                selectAvatarColor(this);
            });
            
            historyPalette.appendChild(colorDiv);
        });
        
        historySection.innerHTML = '<p class="text-muted mb-2"><small>Couleurs récentes :</small></p>';
        historySection.appendChild(historyPalette);
        
    } catch (error) {
        console.error('Erreur affichage historique:', error);
    }
}

/**
 * Ajouter un bouton pour générer une couleur aléatoire
 */
function addRandomColorButton() {
    const palette = document.querySelector('.avatar-palette');
    
    if (!palette) return;
    
    const randomBtn = document.createElement('button');
    randomBtn.type = 'button';
    randomBtn.className = 'btn btn-outline-secondary btn-sm mt-3 w-100';
    randomBtn.innerHTML = '<i class="fas fa-random me-2"></i> Couleur aléatoire';
    randomBtn.addEventListener('click', generateRandomAvatar);
    
    palette.parentElement.appendChild(randomBtn);
}

/**
 * Ajouter des effets de hover sur les couleurs
 */
function addHoverEffects() {
    const colors = document.querySelectorAll('.avatar-color');
    
    colors.forEach(color => {
        color.addEventListener('mouseenter', function() {
            if (!this.classList.contains('selected')) {
                this.style.transform = 'scale(1.15)';
                this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
            }
        });
        
        color.addEventListener('mouseleave', function() {
            if (!this.classList.contains('selected')) {
                this.style.transform = 'scale(1)';
                this.style.boxShadow = 'none';
            }
        });
    });
}

/**
 * Créer un sélecteur de couleur custom
 */
function createCustomColorPicker() {
    const palette = document.querySelector('.avatar-palette');
    
    if (!palette) return;
    
    const customPicker = document.createElement('div');
    customPicker.className = 'mt-3 text-center';
    customPicker.innerHTML = `
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="AvatarModule.openColorPicker()">
            <i class="fas fa-palette me-2"></i> Couleur personnalisée
        </button>
    `;
    
    palette.parentElement.appendChild(customPicker);
}

/**
 * Ouvrir le sélecteur de couleur natif
 */
function openColorPicker() {
    const input = document.createElement('input');
    input.type = 'color';
    input.value = selectedColor || '#1E3A8A';
    
    input.addEventListener('change', function() {
        const color = this.value.toUpperCase();
        
        if (!AVATAR_COLORS.includes(color)) {
            addCustomColorToPalette(color);
        }
        
        const colorElement = document.querySelector(`.avatar-color[data-color="${color}"]`);
        if (colorElement) {
            selectAvatarColor(colorElement);
        }
    });
    
    input.click();
}

/**
 * Ajouter une couleur personnalisée à la palette
 */
function addCustomColorToPalette(color) {
    const palette = document.querySelector('.avatar-palette');
    
    if (!palette) return;
    
    const colorDiv = document.createElement('div');
    colorDiv.className = 'avatar-color';
    colorDiv.style.backgroundColor = color;
    colorDiv.dataset.color = color;
    colorDiv.title = 'Couleur personnalisée';
    
    colorDiv.addEventListener('click', function() {
        selectAvatarColor(this);
    });
    
    palette.appendChild(colorDiv);
    selectAvatarColor(colorDiv);
    
    PawConnect.showToast('Couleur personnalisée ajoutée !', 'success');
}

/**
 * Validation de la couleur hexadécimale
 */
function isValidHexColor(color) {
    return /^#[0-9A-F]{6}$/i.test(color);
}

/**
 * Convertir une couleur hex en RGB
 */
function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

/**
 * Déterminer si une couleur est claire ou foncée
 */
function isLightColor(hex) {
    const rgb = hexToRgb(hex);
    if (!rgb) return false;
    
    const luminance = (0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b) / 255;
    return luminance > 0.5;
}

/**
 * Réinitialiser la sélection d'avatar
 */
function resetAvatarSelection() {
    selectedColor = null;
    
    document.querySelectorAll('.avatar-color').forEach(el => {
        el.classList.remove('selected');
    });
    
    const hiddenInput = document.getElementById('selectedColor');
    if (hiddenInput) {
        hiddenInput.value = '';
    }
    
    PawConnect.showToast('Sélection réinitialisée', 'info');
}

/**
 * Effet arc-en-ciel sur la palette
 */
function rainbowEffect() {
    const colors = document.querySelectorAll('.avatar-color');
    
    colors.forEach((color, index) => {
        setTimeout(() => {
            color.style.transform = 'scale(1.2) rotate(360deg)';
            color.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                color.style.transform = 'scale(1) rotate(0deg)';
            }, 500);
        }, index * 100);
    });
}

/**
 * Exporter la configuration d'avatar
 */
function exportAvatarConfig() {
    const config = {
        color: selectedColor,
        colors: AVATAR_COLORS,
        timestamp: new Date().toISOString()
    };
    
    const dataStr = JSON.stringify(config, null, 2);
    const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
    
    const exportLink = document.createElement('a');
    exportLink.setAttribute('href', dataUri);
    exportLink.setAttribute('download', 'avatar-config.json');
    exportLink.click();
    
    PawConnect.showToast('Configuration exportée !', 'success');
}

// Style pour l'animation
const pulseStyle = document.createElement('style');
pulseStyle.textContent = `
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(30, 58, 138, 0.7);
        }
        50% {
            transform: scale(1.1);
            box-shadow: 0 0 0 10px rgba(30, 58, 138, 0);
        }
    }
`;
document.head.appendChild(pulseStyle);

// Exposer les fonctions au scope global
window.AvatarModule = {
    selectAvatarColor,
    updateAvatarPreview,
    generateRandomAvatar,
    copyColorCode,
    getInitials,
    createAvatarElement,
    isValidHexColor,
    hexToRgb,
    isLightColor,
    exportAvatarConfig,
    resetAvatarSelection,
    openColorPicker,
    showColorHistory,
    rainbowEffect,
    AVATAR_COLORS
};

console.log('🎨 Avatar Module chargé avec succès');