/**
 * PAW CONNECT - MAIN JAVASCRIPT
 * Gestion du Dark Mode, utilitaires et fonctions communes
 */

// ==========================================
// DARK MODE
// ==========================================

// Vérifier la préférence sauvegardée
const currentTheme = localStorage.getItem('theme') || 'light';
document.documentElement.setAttribute('data-theme', currentTheme);

// Bouton toggle Dark Mode
const darkModeToggle = document.getElementById('darkModeToggle');

if (darkModeToggle) {
    // Mettre à jour l'icône selon le thème actuel
    updateDarkModeIcon();
    
    darkModeToggle.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        // Appliquer le nouveau thème
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        // Mettre à jour l'icône
        updateDarkModeIcon();
        
        // Animation de transition
        document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
    });
}

function updateDarkModeIcon() {
    if (!darkModeToggle) return;
    
    const theme = document.documentElement.getAttribute('data-theme');
    const icon = darkModeToggle.querySelector('i');
    const text = darkModeToggle.querySelector('span');
    
    if (theme === 'dark') {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
        if (text) text.textContent = 'Light';
    } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
        if (text) text.textContent = 'Dark';
    }
}

// ==========================================
// ANIMATIONS AU SCROLL
// ==========================================

// Intersection Observer pour les animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observer tous les éléments avec la classe à animer
document.addEventListener('DOMContentLoaded', function() {
    const elementsToAnimate = document.querySelectorAll('.card, .stat-card, .data-table');
    elementsToAnimate.forEach(el => observer.observe(el));
});

// ==========================================
// UTILITAIRES
// ==========================================

/**
 * Afficher un toast notification
 */
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    toast.innerHTML = `
        <i class="fas ${icons[type]} me-2"></i>
        <span>${message}</span>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    toastContainer.appendChild(toast);
    
    // Animation d'entrée
    setTimeout(() => toast.classList.add('show'), 10);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    `;
    document.body.appendChild(container);
    return container;
}

// Styles pour les toasts
const toastStyles = document.createElement('style');
toastStyles.textContent = `
    .toast-notification {
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 300px;
        transform: translateX(400px);
        transition: transform 0.3s ease;
    }
    
    .toast-notification.show {
        transform: translateX(0);
    }
    
    [data-theme="dark"] .toast-notification {
        background: #1F2937;
        color: #F9FAFB;
    }
    
    .toast-success { border-left: 4px solid #10B981; }
    .toast-error { border-left: 4px solid #EF4444; }
    .toast-warning { border-left: 4px solid #F59E0B; }
    .toast-info { border-left: 4px solid #1E3A8A; }
    
    .toast-close {
        background: none;
        border: none;
        cursor: pointer;
        color: inherit;
        margin-left: auto;
        opacity: 0.6;
        transition: opacity 0.2s;
    }
    
    .toast-close:hover {
        opacity: 1;
    }
`;
document.head.appendChild(toastStyles);

/**
 * Formater une date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

/**
 * Formater un nombre avec séparateurs
 */
function formatNumber(num) {
    return new Intl.NumberFormat('fr-FR').format(num);
}

/**
 * Débounce pour optimiser les recherches
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Valider un email
 */
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Valider un mot de passe (min 8 caractères)
 */
function isValidPassword(password) {
    return password.length >= 8;
}

/**
 * Copier dans le presse-papiers
 */
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        showToast('Copié dans le presse-papiers', 'success');
        return true;
    } catch (err) {
        showToast('Erreur lors de la copie', 'error');
        return false;
    }
}

// ==========================================
// GESTION DES FORMULAIRES
// ==========================================

/**
 * Validation en temps réel des formulaires
 */
document.addEventListener('DOMContentLoaded', function() {
    // Validation email
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !isValidEmail(this.value)) {
                this.classList.add('is-invalid');
                showValidationError(this, 'Email invalide');
            } else {
                this.classList.remove('is-invalid');
                removeValidationError(this);
            }
        });
    });
    
    // Validation mot de passe
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        if (input.name === 'password' || input.name === 'new_password') {
            input.addEventListener('input', function() {
                const strength = getPasswordStrength(this.value);
                updatePasswordStrength(this, strength);
            });
        }
    });
    
    // Confirmation mot de passe
    const confirmPasswordInput = document.querySelector('input[name="password_confirm"], input[name="confirm_password"]');
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = document.querySelector('input[name="password"], input[name="new_password"]');
            if (password && this.value !== password.value) {
                this.classList.add('is-invalid');
                showValidationError(this, 'Les mots de passe ne correspondent pas');
            } else {
                this.classList.remove('is-invalid');
                removeValidationError(this);
            }
        });
    }
});

function showValidationError(input, message) {
    removeValidationError(input);
    
    const error = document.createElement('div');
    error.className = 'invalid-feedback d-block';
    error.textContent = message;
    input.parentElement.appendChild(error);
}

function removeValidationError(input) {
    const error = input.parentElement.querySelector('.invalid-feedback');
    if (error) error.remove();
}

function getPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    return strength;
}

function updatePasswordStrength(input, strength) {
    let strengthIndicator = input.parentElement.querySelector('.password-strength');
    
    if (!strengthIndicator) {
        strengthIndicator = document.createElement('div');
        strengthIndicator.className = 'password-strength mt-2';
        input.parentElement.appendChild(strengthIndicator);
    }
    
    const labels = ['Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'];
    const colors = ['#EF4444', '#F59E0B', '#F59E0B', '#10B981', '#10B981'];
    
    strengthIndicator.innerHTML = `
        <div class="d-flex gap-1 mb-1">
            ${Array(5).fill(0).map((_, i) => 
                `<div style="flex:1;height:4px;background:${i < strength ? colors[strength-1] : '#E5E7EB'};border-radius:2px;"></div>`
            ).join('')}
        </div>
        <small style="color:${colors[strength-1] || '#9CA3AF'}">${labels[strength-1] || 'Entrez un mot de passe'}</small>
    `;
}

// ==========================================
// CONFIRMATION AVANT ACTIONS CRITIQUES
// ==========================================

document.addEventListener('click', function(e) {
    // Boutons avec confirmation
    if (e.target.closest('[data-confirm]')) {
        const btn = e.target.closest('[data-confirm]');
        const message = btn.getAttribute('data-confirm');
        
        if (!confirm(message)) {
            e.preventDefault();
            e.stopPropagation();
        }
    }
});

// ==========================================
// LOADING STATES
// ==========================================

/**
 * Afficher un loader sur un bouton
 */
function setButtonLoading(button, loading = true) {
    if (loading) {
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Chargement...';
    } else {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText;
    }
}

// ==========================================
// MENU MOBILE
// ==========================================

// Fermer le menu mobile au clic sur un lien
document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
    link.addEventListener('click', function() {
        const navbarCollapse = document.querySelector('.navbar-collapse');
        if (navbarCollapse && navbarCollapse.classList.contains('show')) {
            const bsCollapse = new bootstrap.Collapse(navbarCollapse);
            bsCollapse.hide();
        }
    });
});

// ==========================================
// SCROLL TO TOP
// ==========================================

// Créer le bouton scroll to top
const scrollTopBtn = document.createElement('button');
scrollTopBtn.id = 'scrollToTop';
scrollTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
scrollTopBtn.style.cssText = `
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    border: none;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    z-index: 999;
    transition: all 0.3s ease;
`;

document.body.appendChild(scrollTopBtn);

// Afficher/masquer selon le scroll
window.addEventListener('scroll', function() {
    if (window.pageYOffset > 300) {
        scrollTopBtn.style.display = 'flex';
    } else {
        scrollTopBtn.style.display = 'none';
    }
});

// Action au clic
scrollTopBtn.addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

scrollTopBtn.addEventListener('mouseenter', function() {
    this.style.transform = 'scale(1.1)';
});

scrollTopBtn.addEventListener('mouseleave', function() {
    this.style.transform = 'scale(1)';
});

// ==========================================
// AUTO-LOGOUT (déconnexion automatique)
// ==========================================

let inactivityTimer;
const INACTIVITY_TIMEOUT = 30 * 60 * 1000; // 30 minutes

function resetInactivityTimer() {
    clearTimeout(inactivityTimer);
    
    // Vérifier si l'utilisateur est connecté
    if (document.body.dataset.userLoggedIn === 'true') {
        inactivityTimer = setTimeout(() => {
            showToast('Session expirée par inactivité', 'warning');
            setTimeout(() => {
                window.location.href = '/logout.php';
            }, 2000);
        }, INACTIVITY_TIMEOUT);
    }
}

// Réinitialiser le timer à chaque activité
['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
    document.addEventListener(event, resetInactivityTimer, true);
});

// Initialiser le timer
resetInactivityTimer();

// ==========================================
// LOGS UTILISATEUR (pour le back-office)
// ==========================================

/**
 * Logger l'activité utilisateur
 */
async function logActivity(action, details = {}) {
    try {
        await fetch('/backend/logs/log-activity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action,
                details,
                timestamp: new Date().toISOString(),
                page: window.location.pathname
            })
        });
    } catch (error) {
        console.error('Erreur log activité:', error);
    }
}

// Logger la visite de page
if (document.body.dataset.userLoggedIn === 'true') {
    logActivity('page_view', {
        url: window.location.href,
        referrer: document.referrer
    });
}

// ==========================================
// CONSOLE LOG (seulement en dev)
// ==========================================

if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    console.log('%c🐾 Paw Connect', 'color: #1E3A8A; font-size: 20px; font-weight: bold;');
    console.log('%cDéveloppé avec ❤️ pour les animaux', 'color: #10B981; font-size: 14px;');
}

// Exporter les fonctions utilitaires
window.PawConnect = {
    showToast,
    formatDate,
    formatNumber,
    debounce,
    isValidEmail,
    isValidPassword,
    copyToClipboard,
    setButtonLoading,
    logActivity
};