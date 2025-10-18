<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Paw Connect</title>

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

    <style>
        .contact-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0 60px;
            text-align: center;
        }

        .contact-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .contact-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .contact-card h3 {
            color: #667eea;
            margin-bottom: 25px;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .contact-info-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 30px;
            height: 100%;
        }

        .contact-info-item {
            display: flex;
            align-items-start;
            margin-bottom: 25px;
        }

        .contact-info-item:last-child {
            margin-bottom: 0;
        }

        .contact-info-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .contact-info-content h5 {
            margin-bottom: 5px;
            color: #333;
            font-weight: 600;
        }

        .contact-info-content p {
            margin: 0;
            color: #666;
        }

        .contact-info-content a {
            color: #667eea;
            text-decoration: none;
            transition: color 0.3s;
        }

        .contact-info-content a:hover {
            color: #764ba2;
        }

        .form-label.required::after {
            content: " *";
            color: #dc3545;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .social-contact {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-contact a {
            width: 45px;
            height: 45px;
            background: #667eea;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s;
        }

        .social-contact a:hover {
            background: #764ba2;
            transform: translateY(-3px);
        }

        .map-container {
            border-radius: 15px;
            overflow: hidden;
            height: 350px;
            margin-top: 30px;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>
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
                        <a class="nav-link" href="index.php">Accueil</a>
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
                        <a class="nav-link active" href="contact.php">Contact</a>
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
    <section class="contact-hero">
        <div class="container">
            <h1><i class="fas fa-envelope me-2"></i> Contactez-nous</h1>
            <p class="lead">Une question ? Besoin d'aide ? Nous sommes là pour vous !</p>
        </div>
    </section>

    <!-- CONTACT SECTION -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Formulaire de contact -->
                <div class="col-lg-7 mb-4">
                    <div class="contact-card">
                        <h3><i class="fas fa-comment-dots me-2"></i> Envoyez-nous un message</h3>

                        <!-- Messages -->
                        <div class="alert alert-success d-none" id="successMessage">
                            <i class="fas fa-check-circle"></i> Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.
                        </div>

                        <div class="alert alert-danger d-none" id="errorMessage">
                            <i class="fas fa-exclamation-circle"></i> Une erreur est survenue. Veuillez réessayer.
                        </div>

                        <form id="contactForm" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label required">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="prenom" class="form-label required">Prénom</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label required">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" id="telephone" name="telephone">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="sujet" class="form-label required">Sujet</label>
                                <select class="form-select" id="sujet" name="sujet" required>
                                    <option value="">Sélectionnez un sujet</option>
                                    <option value="adoption">Question sur l'adoption</option>
                                    <option value="signalement">Signalement d'un animal</option>
                                    <option value="partenariat">Partenariat</option>
                                    <option value="benevole">Devenir bénévole</option>
                                    <option value="don">Question sur les dons</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label required">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="6" required placeholder="Décrivez votre demande..."></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-submit">
                                    <i class="fas fa-paper-plane me-2"></i> Envoyer le message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="col-lg-5">
                    <div class="contact-card">
                        <h3><i class="fas fa-info-circle me-2"></i> Nos coordonnées</h3>

                        <div class="contact-info-box">
                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-info-content">
                                    <h5>Email</h5>
                                    <p><a href="mailto:contact@paw-connect.org">contact@paw-connect.org</a></p>
                                </div>
                            </div>

                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="contact-info-content">
                                    <h5>Téléphone</h5>
                                    <p><a href="tel:+33123456789">01 23 45 67 89</a></p>
                                    <small class="text-muted">Lun - Ven : 9h - 18h</small>
                                </div>
                            </div>

                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="contact-info-content">
                                    <h5>Adresse</h5>
                                    <p>123 Rue de l'Amitié Animale<br>75001 Paris, France</p>
                                </div>
                            </div>

                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="contact-info-content">
                                    <h5>Horaires d'ouverture</h5>
                                    <p>
                                        Lundi - Vendredi : 9h - 18h<br>
                                        Samedi : 10h - 16h<br>
                                        Dimanche : Fermé
                                    </p>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3">Suivez-nous</h5>
                            <div class="social-contact">
                                <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                                <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                                <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="contact-card">
                        <h3><i class="fas fa-map-marked-alt me-2"></i> Où nous trouver</h3>
                        <div class="map-container">
                            <!-- Carte Google Maps (remplacez l'URL par votre adresse réelle) -->
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.991441499827!2d2.3351509156744!3d48.858844979287104!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2964e34e2d%3A0x8ddca9ee380ef7e0!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1234567890123!5m2!1sfr!2sfr" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
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
                    <p class="text-muted mb-1"><i class="fas fa-envelope"></i> contact@paw-connect.org</p>
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

    <script>
        // Gestion du formulaire de contact
        document.getElementById('contactForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const successMsg = document.getElementById('successMessage');
            const errorMsg = document.getElementById('errorMessage');
            
            // Réinitialiser les messages
            successMsg.classList.add('d-none');
            errorMsg.classList.add('d-none');
            
            // Désactiver le bouton
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Envoi en cours...';
            
            try {
                const formData = new FormData(this);
                
                const response = await fetch('backend/contact/send.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    successMsg.classList.remove('d-none');
                    this.reset();
                    
                    // Scroll vers le message de succès
                    successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    throw new Error(data.message || 'Erreur serveur');
                }
            } catch (error) {
                errorMsg.classList.remove('d-none');
                errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                console.error('Erreur:', error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Envoyer le message';
            }
        });
    </script>
</body>
</html>
