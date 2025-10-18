<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: /index.php');
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        .auth-section{padding:80px 0;min-height:calc(100vh - 200px)}
        .auth-card{border-radius:20px;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,0.1)}
        .auth-sidebar{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:50px 30px;text-align:center}
        .auth-sidebar i{font-size:5rem;margin-bottom:30px;opacity:.9}
        .auth-sidebar h3{font-weight:600;margin-bottom:15px}
        .auth-content{padding:50px 40px}
        .nav-pills .nav-link{border-radius:50px;padding:12px 30px;font-weight:500;color:#666;transition:all .3s}
        .nav-pills .nav-link.active{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff}
        .input-group-text{background:#f8f9fa;border-right:none}
        .form-control{border-left:none}
        .form-control:focus{border-color:#667eea;box-shadow:none}
        @media (max-width:768px){.auth-content{padding:30px 20px}}
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-paw"></i> Paw Connect</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="search.php">Adopter</a></li>
                <li class="nav-item"><a class="nav-link" href="signalement.php">Signaler</a></li>
                <li class="nav-item"><a class="nav-link" href="newsletter.php">Newsletter</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                <li class="nav-item"><a class="btn btn-outline-primary active" href="login.php">Connexion</a></li>
                <li class="nav-item ms-2">
                    <button class="dark-mode-toggle" id="darkModeToggle"><i class="fas fa-moon"></i><span>Dark</span></button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card auth-card">
                    <div class="row g-0">
                        <div class="col-md-5 d-none d-md-block">
                            <div class="auth-sidebar">
                                <i class="fas fa-paw"></i>
                                <h3>Bienvenue sur Paw Connect</h3>
                                <p>Rejoignez notre communauté et donnez une seconde chance à un animal en détresse.</p>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="auth-content">
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
                                    <div class="tab-pane fade show active" id="login" role="tabpanel">
                                        <h3 class="mb-4">Bon retour !</h3>
                                        <form id="loginForm" action="backend/auth/login.php" method="POST">
                                            <input type="hidden" name="from_form" value="1">
                                            <div class="mb-3">
                                                <label for="loginEmail" class="form-label">Email</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                    <input type="email" class="form-control" id="loginEmail" name="email" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="loginPassword" class="form-label">Mot de passe</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                    <input type="password" class="form-control" id="loginPassword" name="password" required>
                                                    <button class="btn btn-outline-secondary" type="button" id="toggleLoginPassword"><i class="fas fa-eye"></i></button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Captcha</label>
                                                <p id="captchaQuestionLogin" class="form-text mb-2">Chargement…</p>
                                                <input type="hidden" name="captcha_id" id="captchaIdLogin" value="">
                                                <input type="text" class="form-control" name="captcha" id="captchaAnswerLogin" placeholder="Réponse" required>
                                            </div>
                                            <div class="d-grid mb-3">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    <i class="fas fa-sign-in-alt me-2"></i> Se connecter
                                                </button>
                                            </div>
                                            <div class="text-center">
                                                <a href="#" class="text-muted small">Mot de passe oublié ?</a>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="register" role="tabpanel">
                                        <h3 class="mb-4">Créer un compte</h3>
                                        <form id="registerForm" action="backend/auth/register.php" method="POST">
                                            <input type="hidden" name="from_form" value="1">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="registerNom" class="form-label">Nom</label>
                                                    <input type="text" class="form-control" id="registerNom" name="nom" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="registerAge" class="form-label">Âge</label>
                                                    <input type="number" class="form-control" id="registerAge" name="age" min="18" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="registerEmail" class="form-label">Email</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                    <input type="email" class="form-control" id="registerEmail" name="email" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="registerPassword" class="form-label">Mot de passe</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                    <input type="password" class="form-control" id="registerPassword" name="password" required>
                                                    <button class="btn btn-outline-secondary" type="button" id="toggleRegisterPassword"><i class="fas fa-eye"></i></button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="registerPasswordConfirm" class="form-label">Confirmer le mot de passe</label>
                                                <input type="password" class="form-control" id="registerPasswordConfirm" name="password_confirm" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Captcha</label>
                                                <p id="captchaQuestionRegister" class="form-text mb-2">Chargement…</p>
                                                <input type="hidden" name="captcha_id" id="captchaIdRegister" value="">
                                                <input type="text" class="form-control" name="captcha" id="captchaAnswerRegister" placeholder="Réponse" required>
                                            </div>
                                            <div class="form-check mb-4">
                                                <input class="form-check-input" type="checkbox" id="is_refuge" name="is_refuge" value="1">
                                                <label class="form-check-label" for="is_refuge">Je m'inscris en tant que refuge</label>
                                            </div>
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    <i class="fas fa-user-plus me-2"></i> S'inscrire
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div id="authMessage" class="alert d-none mt-3" role="alert"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="text-muted"><i class="fas fa-arrow-left me-2"></i> Retour à l'accueil</a>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5>Paw Connect</h5>
                <p class="text-muted">Agir ensemble pour protéger et offrir une seconde chance aux animaux en détresse.</p>
                <div class="social-links mt-3">
                    <a href="#" class="me-3"><i class="fab fa-facebook fa-2x"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-instagram fa-2x"></i></a>
                    <a href="#"><i class="fab fa-twitter fa-2x"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-4">
                <h5>Navigation</h5>
                <a href="index.php">Accueil</a>
                <a href="search.php">Adopter</a>
                <a href="signalement.php">Signaler</a>
                <a href="contact.php">Contact</a>
            </div>
            <div class="col-md-3 mb-4">
                <h5>Informations</h5>
                <a href="mentions-legales.php">Mentions légales</a>
                <a href="politique-confidentialite.php">Confidentialité</a>
                <a href="cgv.php">CGV</a>
                <a href="faq.php">FAQ</a>
            </div>
            <div class="col-md-3 mb-4">
                <h5>Contact</h5>
                <p class="text-muted mb-1"><i class="fas fa-envelope"></i> contact@pawconnect.fr</p>
                <p class="text-muted mb-1"><i class="fas fa-phone"></i> 01 23 45 67 89</p>
                <p class="text-muted"><i class="fas fa-map-marker-alt"></i> Paris, France</p>
            </div>
        </div>
        <hr style="border-color: rgba(255,255,255,0.1);">
        <div class="text-center py-3">
            <p class="mb-0 text-muted">&copy; 2025 Paw Connect. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script>
document.getElementById('toggleLoginPassword')?.addEventListener('click',function(){
  const i=document.getElementById('loginPassword');const ic=this.querySelector('i');
  i.type=i.type==='password'?'text':'password';ic.classList.toggle('fa-eye');ic.classList.toggle('fa-eye-slash');
});
document.getElementById('toggleRegisterPassword')?.addEventListener('click',function(){
  const i=document.getElementById('registerPassword');const ic=this.querySelector('i');
  i.type=i.type==='password'?'text':'password';ic.classList.toggle('fa-eye');ic.classList.toggle('fa-eye-slash');
});
async function loadCaptcha(target){
  try{
    const r=await fetch('/backend/captcha/get-question.php');
    const d=await r.json();
    if(d.ok){
      document.getElementById('captchaQuestion'+target).textContent=d.question;
      document.getElementById('captchaId'+target).value=d.id;
    }else{
      document.getElementById('captchaQuestion'+target).textContent='Captcha indisponible';
    }
  }catch(e){
    document.getElementById('captchaQuestion'+target).textContent='Erreur de chargement du captcha';
  }
}
document.addEventListener('DOMContentLoaded',()=>{
  loadCaptcha('Login');
  loadCaptcha('Register');
});
document.getElementById('login-tab')?.addEventListener('shown.bs.tab',()=>loadCaptcha('Login'));
document.getElementById('register-tab')?.addEventListener('shown.bs.tab',()=>loadCaptcha('Register'));
</script>
</body>
</html>
