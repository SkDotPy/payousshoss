<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paw Connect - Agir ensemble, les protéger</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

    <!-- NAVIGATION -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-paw"></i>
                Paw Connect
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="search.php">Adopter</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="signalement.php">Signaler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="newsletter.php">Newsletter</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> Mon compte
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profil.php">Mon profil</a></li>
                                <li><a class="dropdown-item" href="mes-adoptions.php">Mes adoptions</a></li>
                                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="admin/index.php"><i class="fas fa-cog"></i> Administration</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Déconnexion</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-outline-primary" href="login.php">Connexion</a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item ms-2">
                        <button class="dark-mode-toggle" id="darkModeToggle">
                            <i class="fas fa-moon"></i>
                            <span>Dark</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="container">
            <h1 class="fade-in">Agir ensemble, les protéger</h1>
            <p class="fade-in">Donnez une seconde chance à un animal en détresse. Adoptez, signalez, soutenez.</p>
            <div class="mt-4">
                <a href="search.php" class="btn btn-light btn-lg me-3">
                    <i class="fas fa-search"></i> Trouver mon compagnon
                </a>
                <a href="signalement.php" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-exclamation-triangle"></i> Signaler un animal
                </a>
            </div>
        </div>
    </section>

    <!-- SECTIONS PRINCIPALES -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2>Comment ça marche ?</h2>
                    <p class="text-muted">Trois étapes simples pour changer une vie</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card text-center p-4 h-100">
                        <div class="mb-3">
                            <i class="fas fa-search fa-3x text-primary"></i>
                        </div>
                        <h4>1. Recherchez</h4>
                        <p class="text-muted">Parcourez notre catalogue d'animaux disponibles et utilisez nos filtres pour trouver votre compagnon idéal.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-center p-4 h-100">
                        <div class="mb-3">
                            <i class="fas fa-heart fa-3x" style="color: #10B981;"></i>
                        </div>
                        <h4>2. Adoptez</h4>
                        <p class="text-muted">Remplissez votre demande d'adoption et signez électroniquement votre contrat en toute sécurité.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-center p-4 h-100">
                        <div class="mb-3">
                            <i class="fas fa-home fa-3x" style="color: #60A5FA;"></i>
                        </div>
                        <h4>3. Accueillez</h4>
                        <p class="text-muted">Recevez votre contrat PDF et préparez-vous à accueillir votre nouvel ami dans votre foyer.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ANIMAUX RÉCENTS -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h2>Nos nouveaux arrivants</h2>
                    <p class="text-muted">Découvrez les animaux récemment ajoutés</p>
                </div>
            </div>
            
            <div class="row g-4" id="recentAnimals">
                <!-- Les animaux seront chargés dynamiquement via PHP/JS -->
                
                <!-- Exemple de carte animal (template) -->
                <div class="col-md-4">
                    <div class="card card-animal">
                        <img src="assets/images/animals/placeholder-dog.jpg" alt="Chien" class="card-img-top">
                        <span class="badge-available">Disponible</span>
                        <div class="card-body">
                            <h5 class="card-title">Max</h5>
                            <p class="text-muted mb-2">
                                <i class="fas fa-dog"></i> Labrador • 3 ans • Mâle
                            </p>
                            <p class="card-text">Chien adorable et joueur, parfait pour une famille active.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="fas fa-map-marker-alt"></i> Paris (75)</span>
                                <a href="animal-detail.php?id=1" class="btn btn-primary btn-sm">Voir plus</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-animal">
                        <img src="assets/images/animals/placeholder-cat.jpg" alt="Chat" class="card-img-top">
                        <span class="badge-available">Disponible</span>
                        <div class="card-body">
                            <h5 class="card-title">Luna</h5>
                            <p class="text-muted mb-2">
                                <i class="fas fa-cat"></i> Européen • 2 ans • Femelle
                            </p>
                            <p class="card-text">Chatte câline et indépendante, idéale pour un appartement.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="fas fa-map-marker-alt"></i> Lyon (69)</span>
                                <a href="animal-detail.php?id=2" class="btn btn-primary btn-sm">Voir plus</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-animal">
                        <img src="assets/images/animals/placeholder-rabbit.jpg" alt="Lapin" class="card-img-top">
                        <span class="badge-available">Disponible</span>
                        <div class="card-body">
                            <h5 class="card-title">Flocon</h5>
                            <p class="text-muted mb-2">
                                <i class="fas fa-rabbit"></i> Nain • 1 an • Mâle
                            </p>
                            <p class="card-text">Petit lapin doux et sociable, parfait pour les enfants.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="fas fa-map-marker-alt"></i> Marseille (13)</span>
                                <a href="animal-detail.php?id=3" class="btn btn-primary btn-sm">Voir plus</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <a href="search.php" class="btn btn-primary btn-lg">
                    Voir tous les animaux <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- CTA DONS -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Soutenez notre mission</h2>
                    <p class="lead">Vos dons permettent de sauver des vies et d'offrir un avenir meilleur aux animaux en détresse.</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Soins vétérinaires</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Nourriture et hébergement</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Stérilisation et vaccins</li>
                    </ul>
                    <a href="dons.php" class="btn btn-secondary btn-lg mt-3">
                        <i class="fas fa-heart"></i> Faire un don
                    </a>
                </div>
                <div class="col-md-6">
                    <img src="assets/images/donation-illustration.jpg" alt="Faire un don" class="img-fluid rounded-xl shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/app.js"></script>
</body>
</html>