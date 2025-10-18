<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signaler un animal - Paw Connect</title>

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
        .signalement-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0 60px;
            text-align: center;
        }

        .signalement-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .form-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .form-section h3 {
            color: #667eea;
            margin-bottom: 25px;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .form-label.required::after {
            content: " *";
            color: #dc3545;
        }

        .alert-info-custom {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
        }

        .file-preview {
            margin-top: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .file-preview img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
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
                        <a class="nav-link active" href="signalement.php">Signaler</a>
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
    <section class="signalement-hero">
        <div class="container">
            <h1><i class="fas fa-exclamation-triangle"></i> Signaler un animal</h1>
            <p class="lead">Aidez-nous à protéger les animaux en détresse</p>
        </div>
    </section>

    <!-- FORMULAIRE DE SIGNALEMENT -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <!-- Message de succès -->
                    <div class="alert alert-success d-none" id="successMessage">
                        <i class="fas fa-check-circle"></i> Votre signalement a été enregistré avec succès ! Notre équipe vous contactera dans les plus brefs délais.
                    </div>

                    <!-- Message d'erreur -->
                    <div class="alert alert-danger d-none" id="errorMessage">
                        <i class="fas fa-exclamation-circle"></i> Une erreur est survenue. Veuillez réessayer.
                    </div>

                    <!-- Info box -->
                    <div class="alert-info-custom">
                        <p class="mb-0"><i class="fas fa-info-circle"></i> Vous avez trouvé un animal errant ou souhaitez confier un animal à un refuge ? Remplissez ce formulaire pour nous permettre d'agir rapidement et efficacement.</p>
                    </div>

                    <form id="signalementForm" enctype="multipart/form-data">
                        
                        <!-- Type de signalement -->
                        <div class="form-section">
                            <h3><i class="fas fa-clipboard-list"></i> Type de signalement</h3>
                            
                            <div class="mb-3">
                                <label class="form-label required">Nature du signalement</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_signalement" id="trouve" value="trouve" checked required>
                                    <label class="form-check-label" for="trouve">
                                        <strong>Animal trouvé</strong> - J'ai trouvé un animal errant
                                    </label>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="type_signalement" id="remise" value="remise" required>
                                    <label class="form-check-label" for="remise">
                                        <strong>Remise à un refuge</strong> - Je souhaite confier un animal à un refuge
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Informations sur l'animal -->
                        <div class="form-section">
                            <h3><i class="fas fa-paw"></i> Informations sur l'animal</h3>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="espece" class="form-label required">Espèce</label>
                                    <select class="form-select" id="espece" name="espece" required>
                                        <option value="">Sélectionnez...</option>
                                        <option value="chien">Chien</option>
                                        <option value="chat">Chat</option>
                                        <option value="lapin">Lapin</option>
                                        <option value="oiseau">Oiseau</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="race" class="form-label">Race (si connue)</label>
                                    <input type="text" class="form-control" id="race" name="race" placeholder="Ex: Labrador, Européen...">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sexe" class="form-label">Sexe</label>
                                    <select class="form-select" id="sexe" name="sexe">
                                        <option value="">Non identifié</option>
                                        <option value="male">Mâle</option>
                                        <option value="femelle">Femelle</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="age_estime" class="form-label">Âge estimé</label>
                                    <input type="text" class="form-control" id="age_estime" name="age_estime" placeholder="Ex: 2 ans, jeune, adulte...">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label required">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Décrivez l'apparence de l'animal, son comportement, son état de santé..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="photos" class="form-label">Photos</label>
                                <input type="file" class="form-control" id="photos" name="photos[]" accept="image/*" multiple>
                                <small class="text-muted">Vous pouvez ajouter plusieurs photos</small>
                                <div class="file-preview" id="photoPreview"></div>
                            </div>
                        </div>

                        <!-- Localisation -->
                        <div class="form-section">
                            <h3><i class="fas fa-map-marker-alt"></i> Localisation</h3>
                            
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="lieu" class="form-label required">Lieu de découverte/localisation</label>
                                    <input type="text" class="form-control" id="lieu" name="lieu" placeholder="Adresse, ville..." required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="date_signalement" class="form-label required">Date</label>
                                    <input type="date" class="form-control" id="date_signalement" name="date_signalement" required>
                                </div>
                            </div>
                        </div>

                        <!-- Vos coordonnées -->
                        <div class="form-section">
                            <h3><i class="fas fa-user"></i> Vos coordonnées</h3>
                            
                            <div class="mb-3">
                                <label for="nom" class="form-label required">Nom complet</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label required">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="telephone" class="form-label required">Téléphone</label>
                                    <input type="tel" class="form-control" id="telephone" name="telephone" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="commentaire" class="form-label">Informations complémentaires</label>
                                <textarea class="form-control" id="commentaire" name="commentaire" rows="3" placeholder="Toute information utile..."></textarea>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-submit">
                                <i class="fas fa-paper-plane"></i> Envoyer le signalement
                            </button>
                        </div>
                    </form>

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

    <script>
        // Prévisualisation des photos
        document.getElementById('photos').addEventListener('change', function(e) {
            const preview = document.getElementById('photoPreview');
            preview.innerHTML = '';
            
            const files = e.target.files;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                }
                
                reader.readAsDataURL(file);
            }
        });

        // Date par défaut (aujourd'hui)
        document.getElementById('date_signalement').valueAsDate = new Date();

        // Gestion du formulaire
        document.getElementById('signalementForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const successMsg = document.getElementById('successMessage');
            const errorMsg = document.getElementById('errorMessage');
            
            // Réinitialiser les messages
            successMsg.classList.add('d-none');
            errorMsg.classList.add('d-none');
            
            // Désactiver le bouton
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
            
            try {
                const formData = new FormData(this);
                
                // ⚠️ REMPLACEZ L'URL CI-DESSOUS PAR VOTRE ENDPOINT BACKEND
                const response = await fetch('api/signalement.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    successMsg.classList.remove('d-none');
                    this.reset();
                    document.getElementById('photoPreview').innerHTML = '';
                    document.getElementById('date_signalement').valueAsDate = new Date();
                    
                    // Scroll vers le haut
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    throw new Error('Erreur serveur');
                }
            } catch (error) {
                errorMsg.classList.remove('d-none');
                console.error('Erreur:', error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer le signalement';
            }
        });
    </script>
</body>
</html>
