const AVATAR_COLORS = [
    '#1E3A8A',
    '#60A5FA',
    '#10B981',
    '#F59E0B',
    '#EF4444',
    '#8B5CF6',
    '#EC4899',
    '#14B8A6'
];

let selectedColor = null;

document.addEventListener('DOMContentLoaded', function() {
    initAvatarPalette();
    initAvatarForm();
    initNamePreview();
});

function initAvatarPalette() {
    const avatarPalette = document.querySelector('.avatar-palette');
    
    if (!avatarPalette) return;
    
    const currentColor = document.getElementById('selectedColor')?.value;
    
    avatarPalette.innerHTML = '';
    
    AVATAR_COLORS.forEach(color => {
        const colorDiv = document.createElement('div');
        colorDiv.className = 'avatar-color';
        colorDiv.style.backgroundColor = color;
        colorDiv.dataset.color = color;
        
        if (color === currentColor) {
            colorDiv.classList.add('selected');
            selectedColor = color;
        }
        
        colorDiv.addEventListener('click', function() {
            selectAvatarColor(this);
        });
        
        avatarPalette.appendChild(colorDiv);
    });
}

function selectAvatarColor(element) {
    document.querySelectorAll('.avatar-color').forEach(el => {
        el.classList.remove('selected');
    });
    
    element.classList.add('selected');
    
    selectedColor = element.dataset.color;
    
    const hiddenInput = document.getElementById('selectedColor');
    if (hiddenInput) {
        hiddenInput.value = selectedColor;
    }
    
    updateAvatarPreview(selectedColor);
}

function updateAvatarPreview(color) {
    const preview = document.getElementById('avatarPreview');
    
    if (preview) {
        preview.style.backgroundColor = color;
        preview.style.transition = 'background-color 0.3s ease';
    }
}

function initAvatarForm() {
    const avatarForm = document.getElementById('avatarForm');
    
    if (!avatarForm) return;
    
    avatarForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!selectedColor) {
            PawConnect.showToast('Veuillez sélectionner une couleur', 'warning');
            return;
        }
        
        const submitBtn = this.querySelector('button[type="submit"]');
        
        PawConnect.setButtonLoading(submitBtn, true);
        
        try {
            const formData = new FormData(this);
            
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                PawConnect.showToast('Avatar mis à jour avec succès !', 'success');
                updateAllAvatars(selectedColor);
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

function updateAllAvatars(color) {
    document.querySelectorAll('.avatar-display, #avatarPreview').forEach(avatar => {
        avatar.style.backgroundColor = color;
    });
}

function getInitials(name) {
    if (!name) return '?';
    
    const parts = name.trim().split(' ');
    if (parts.length === 1) {
        return parts[0].substring(0, 2).toUpperCase();
    }
    
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

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

window.AvatarModule = {
    selectAvatarColor,
    updateAvatarPreview,
    getInitials,
    AVATAR_COLORS
};