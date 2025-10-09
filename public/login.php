<?php
session_start();

// Si déjà connecté, rediriger vers le profil
if(isset($_SESSION['user_id'])) {
    header('Location: profil.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Paw Connect</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

    <!-- NAVIGATION (simplifié) -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-paw"></i> Paw Connect
            </a>
            <button class="dark-mode-toggle" id="darkModeToggle">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </nav>

    <!-- SECTION LOGIN/REGISTER -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-lg">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <!-- IMAGE COLONNE GAUCHE -->
                                <div class="col-md-5 d-none d-md-block" style="background: linear-gradient(135deg, #1E3A8A 0%, #60A5FA 100%); border-radius: 1rem 0 0 1rem;">
                                    <div class="d-flex flex-column justify-content-center align-items-center h-100 p-5 text-white text-center">
                                        <i class="fas fa-paw fa-5x mb-4"></i>
                                        <h3>Bienvenue sur Paw Connect</h3>
                                        <p class="mt-3">Rejoignez notre communauté et donnez une seconde chance à un animal en détresse.</p>
                                    </div>
                                </div>
                                
                                <!-- FORMULAIRES COLONNE DROITE -->
                                <div class="col-md-7">
                                    <div class="p-5">
                                        <!-- TOGGLE CONNEXION/INSCRIPTION -->
                                        <ul class="nav nav-pills nav-justified mb-4" id="authTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="login-tab" data-bs-toggle="pill" data-bs-target="#login" type="button" role="tab">
                                                    <i class="fas fa-sign-in-alt me-2"></i> Connexion
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="register-tab" data-bs-toggle="pill" data-bs-target="#register" type="button" role="tab">
                                                    <i class="fas fa-user-plus me-2"></i> Inscription
                                                </button>
                                            </li>
                                        </ul>

                                        <div class="tab-content" id="authTabsContent">
                                            <!-- FORMULAIRE CONNEXION -->
                                            <div class="tab-pane fade show active" id="login" role="tabpanel">
                                                <h3 class="mb-4">Bon retour !</h3>
                                                
                                                <form id="loginForm" action="backend/auth/login.php" method="POST">
                                                    <div class="mb-3">
                                                        <label for="loginEmail" class="form-label">Email</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                            <input type="email" class="form-control" id="loginEmail" name="email" placeholder="votre@email.fr" required>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="loginPassword" class="form-label">Mot de passe</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="••••••••" required>
                                                            <button class="btn btn-outline-secondary" type="button" id="toggleLoginPassword">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3 form-check">
                                                        <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                                                        <label class="form-check-label" for="rememberMe">Se souvenir de moi</label>
                                                    </div>
                                                    
                                                    <div class="d-grid mb-3">
                                                        <button type="submit" class="btn btn-primary btn-lg">
                                                            <i class="fas fa-sign-in-alt me-2"></i> Se connecter
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="text-center">
                                                        <a href="forgot-password.php" class="text-muted">Mot de passe oublié ?</a>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- FORMULAIRE INSCRIPTION -->
                                            <div class="tab-pane fade" id="register" role="tabpanel">
                                                <h3 class="mb-4">Créer un compte</h3>
                                                
                                                <form id="registerForm" action="backend/auth/register.php" method="POST">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="registerNom" class="form-label">Nom</label>
                                                            <input type="text" class="form-control" id="registerNom" name="nom" placeholder="Dupont" required>
                                                        </div>
                                                        
                                                        <div class="col-md-6 mb-3">
                                                            <label for="registerAge" class="form-label">Âge</label>
                                                            <input type="number" class="form-control" id="registerAge" name="age" placeholder="25" min="18" required>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="registerEmail" class="form-label">Email</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                            <input type="email" class="form-control" id="registerEmail" name="email" placeholder="votre@email.fr" required>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="registerPassword" class="form-label">Mot de passe</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                            <input type="password" class="form-control" id="registerPassword" name="password" placeholder="••••••••" required>
                                                            <button class="btn btn-outline-secondary" type="button" id="toggleRegisterPassword">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </div>
                                                        <small class="form-text text-muted">Minimum 8 caractères</small>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="registerPasswordConfirm" class="form-label">Confirmer le mot de passe</label>
                                                        <input type="password" class="form-control" id="registerPasswordConfirm" name="password_confirm" placeholder="••••••••" required>
                                                    </div>
                                                    
                                                    <!-- CAPTCHA QUESTION/RÉPONSE -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Question de sécurité</label>
                                                        <p class="form-text mb-2" id="captchaQuestion">Quelle est la capitale de la France ?</p>
                                                        <input type="text" class="form-control" id="captchaAnswer" name="captcha_answer" placeholder="Votre réponse" required>
                                                        <input type="hidden" id="captchaId" name="captcha_id" value="1">
                                                    </div>
                                                    
                                                    <div class="mb-3 form-check">
                                                        <input type="checkbox" class="form-check-input" id="acceptCGV" name="accept_cgv" required>
                                                        <label class="form-check-label" for="acceptCGV">
                                                            J'accepte les <a href="cgv.php" target="_blank">conditions générales</a>
                                                        </label>
                                                    </div>
                                                    
                                                    <div class="d-grid">
                                                        <button type="submit" class="btn btn-primary btn-lg">
                                                            <i class="fas fa-user-plus me-2"></i> S'inscrire
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <!-- MESSAGE RETOUR -->
                                        <div id="authMessage" class="alert d-none mt-3" role="alert"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="index.php" class="text-muted">
                            <i class="fas fa-arrow-left me-2"></i> Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/auth.js"></script>
    
    <script>
    // Toggle password visibility
    document.getElementById('toggleLoginPassword')?.addEventListener('click', function() {
        const input = document.getElementById('loginPassword');
        const icon = this.querySelector('i');
        if(input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
    
    document.getElementById('toggleRegisterPassword')?.addEventListener('click', function() {
        const input = document.getElementById('registerPassword');
        const icon = this.querySelector('i');
        if(input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
    
    // Charger une question captcha aléatoire au chargement
    window.addEventListener('DOMContentLoaded', async function() {
        try {
            const response = await fetch('backend/captcha/get-question.php');
            const data = await response.json();
            if(data.success) {
                document.getElementById('captchaQuestion').textContent = data.question;
                document.getElementById('captchaId').value = data.id;
            }
        } catch(error) {
            console.error('Erreur chargement captcha:', error);
        }
    });
    </script>
</body>
</html>