<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter - Paw Connect</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

    <!-- NAVIGATION -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-paw"></i> Paw Connect
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="search.php">Adopter</a></li>
                    <li class="nav-item"><a class="nav-link active" href="newsletter.php">Newsletter</a></li>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> Mon compte
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profil.php">Mon profil</a></li>
                                <li><a class="dropdown-item" href="logout.php">Déconnexion</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="btn btn-outline-primary" href="login.php">Connexion</a></li>
                    <?php endif; ?>
                    
                    <li class="nav-item ms-2">
                        <button class="dark-mode-toggle" id="darkModeToggle">
                            <i class="fas fa-moon"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO NEWSLETTER -->
    <section class="hero" style="padding: 3rem 0;">
        <div class="container text-center">
            <i class="fas fa-envelope fa-4x mb-4"></i>
            <h1>Restez informé avec notre newsletter</h1>
            <p class="lead">Recevez les dernières nouvelles sur nos animaux et nos actions</p>
        </div>
    </section>

    <!-- SECTION PRINCIPALE -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <!-- FORMULAIRE INSCRIPTION -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg">
                        <div class="card-body p-5">
                            <h3 class="text-center mb-4">
                                <i class="fas fa-bell text-primary me-2"></i>
                                S'abonner à la newsletter
                            </h3>
                            
                            <p class="text-muted text-center mb-4">
                                Inscrivez-vous pour recevoir nos actualités et être informé des nouveaux animaux disponibles
                            </p>
                            
                            <form id="subscribeForm" action="backend/newsletter/subscribe.php" method="POST">
                                <div class="mb-3">
                                    <label for="subEmail" class="form-label">Adresse email</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="subEmail" name="email" placeholder="votre@email.fr" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subNom" class="form-label">Nom (optionnel)</label>
                                    <input type="text" class="form-control" id="subNom" name="nom" placeholder="Votre nom">
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="acceptNewsletter" required>
                                        <label class="form-check-label" for="acceptNewsletter">
                                            J'accepte de recevoir des emails de Paw Connect
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i> S'abonner
                                    </button>
                                </div>
                            </form>
                            
                            <div id="subscribeMessage" class="alert d-none mt-3" role="alert"></div>
                        </div>
                    </div>
                </div>

                <!-- FORMULAIRE DÉSINSCRIPTION -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg">
                        <div class="card-body p-5">
                            <h3 class="text-center mb-4">
                                <i class="fas fa-bell-slash text-danger me-2"></i>
                                Se désabonner
                            </h3>
                            
                            <p class="text-muted text-center mb-4">
                                Vous ne souhaitez plus recevoir nos newsletters ? Entrez votre email ci-dessous
                            </p>
                            
                            <form id="unsubscribeForm" action="backend/newsletter/unsubscribe.php" method="POST">
                                <div class="mb-3">
                                    <label for="unsubEmail" class="form-label">Adresse email</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="unsubEmail" name="email" placeholder="votre@email.fr" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="unsubReason" class="form-label">Raison (optionnel)</label>
                                    <select class="form-select" id="unsubReason" name="reason">
                                        <option value="">Sélectionnez une raison</option>
                                        <option value="trop_emails">Trop d'emails</option>
                                        <option value="non_pertinent">Contenu non pertinent</option>
                                        <option value="adopte">J'ai déjà adopté</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Attention :</strong> Vous ne recevrez plus nos actualités
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-outline-danger btn-lg">
                                        <i class="fas fa-times-circle me-2"></i> Se désabonner
                                    </button>
                                </div>
                            </form>
                            
                            <div id="unsubscribeMessage" class="alert d-none mt-3" role="alert"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AVANTAGES NEWSLETTER -->
            <div class="row mt-5">
                <div class="col-12 text-center mb-4">
                    <h2>Pourquoi s'abonner ?</h2>
                </div>
                
                <div class="col-md-4 text-center mb-4">
                    <div class="p-4">
                        <i class="fas fa-paw fa-3x text-primary mb-3"></i>
                        <h4>Nouveaux animaux</h4>
                        <p class="text-muted">Soyez les premiers informés quand de nouveaux animaux arrivent dans nos refuges</p>
                    </div>
                </div>
                
                <div class="col-md-4 text-center mb-4">
                    <div class="p-4">
                        <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
                        <h4>Événements</h4>
                        <p class="text-muted">Découvrez nos journées d'adoption et événements spéciaux en avant-première</p>
                    </div>
                </div>
                
                <div class="col-md-4 text-center mb-4">
                    <div class="p-4">
                        <i class="fas fa-heart fa-3x text-primary mb-3"></i>
                        <h4>Histoires réussies</h4>
                        <p class="text-muted">Suivez les belles histoires d'adoption et de sauvetages réussis</p>
                    </div>
                </div>
            </div>

            <!-- STATISTIQUES -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body text-center py-5">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <h2 class="text-primary mb-2">2,500+</h2>
                                    <p class="text-muted mb-0">Abonnés actifs</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h2 class="text-primary mb-2">1x / semaine</h2>
                                    <p class="text-muted mb-0">Fréquence d'envoi</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h2 class="text-primary mb-2">95%</h2>
                                    <p class="text-muted mb-0">Taux de satisfaction</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <h5>Paw Connect</h5>
                    <p class="text-muted">Agir ensemble pour protéger les animaux</p>
                </div>
                <div class="col-md-6 text-md-end mb-4">
                    <p class="text-muted mb-0">&copy; 2025 Paw Connect. Tous droits réservés.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/newsletter.js"></script>
    
    <script>
    // Gestion formulaire inscription
    document.getElementById('subscribeForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const messageDiv = document.getElementById('subscribeMessage');
        
        try {
            const response = await fetch('backend/newsletter/subscribe.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            messageDiv.classList.remove('d-none', 'alert-danger', 'alert-success');
            
            if(data.success) {
                messageDiv.classList.add('alert-success');
                messageDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + data.message;
                this.reset();
            } else {
                messageDiv.classList.add('alert-danger');
                messageDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + data.message;
            }
        } catch(error) {
            messageDiv.classList.remove('d-none');
            messageDiv.classList.add('alert-danger');
            messageDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Erreur lors de l\'inscription';
        }
    });
    
    // Gestion formulaire désinscription
    document.getElementById('unsubscribeForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if(!confirm('Êtes-vous sûr de vouloir vous désabonner ?')) {
            return;
        }
        
        const formData = new FormData(this);
        const messageDiv = document.getElementById('unsubscribeMessage');
        
        try {
            const response = await fetch('backend/newsletter/unsubscribe.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            messageDiv.classList.remove('d-none', 'alert-danger', 'alert-success');
            
            if(data.success) {
                messageDiv.classList.add('alert-success');
                messageDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + data.message;
                this.reset();
            } else {
                messageDiv.classList.add('alert-danger');
                messageDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + data.message;
            }
        } catch(error) {
            messageDiv.classList.remove('d-none');
            messageDiv.classList.add('alert-danger');
            messageDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Erreur lors de la désinscription';
        }
    });
    </script>
</body>
</html>