<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signaler un animal - Paw Connect</title>
    
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
                    <li class="nav-item"><a class="nav-link active" href="signalement.php">Signaler</a></li>
                    <li class="nav-item"><a class="nav-link" href="newsletter.php">Newsletter</a></li>
                    
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

    <!-- HERO -->
    <section class="hero" style="padding: 3rem 0;">
        <div class="container text-center">
            <i class="fas fa-exclamation-triangle fa-4x mb-4"></i>
            <h1>Signaler un animal</h1>
            <p class="lead">Vous avez trouvé ou perdu un animal ? Signalez-le ici pour l'aider à retrouver son foyer</p>
        </div>
    </section>

    <!-- CONTENU PRINCIPAL -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- STATISTIQUES -->
                <div class="col-12 mb-5">
                    <div class="row text-center g-4">
                        <div class="col-md-4">
                            <div class="card p-4">
                                <div class="mb-3">
                                    <i class="fas fa-search fa-3x text-primary"></i>
                                </div>
                                <h3 class="text-primary">247</h3>
                                <p class="text-muted mb-0">Animaux recherchés</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-4">
                                <div class="mb-3">
                                    <i class="fas fa-check-circle fa-3x text-success"></i>
                                </div>
                                <h3 class="text-success">189</h3>
                                <p class="text-muted mb-0">Retrouvailles réussies</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-4">
                                <div class="mb-3">
                                    <i class="fas fa-clock fa-3x" style="color: #F59E0B;"></i>
                                </div>
                                <h3 style="color: #F59E0B;">58</h3>
                                <p class="text-muted mb-0">En attente</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FORMULAIRE DE SIGNALEMENT -->
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-lg">
                        <div class="card-body p-5">
                            <h3 class="mb-4 text-center">Formulaire de signalement</h3>
                            
                            <!-- Toggle Type -->
                            <div class="text-center mb-4">
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="signalementType" id="typeTrouve" value="trouve" checked>
                                    <label class="btn btn-outline-primary" for="typeTrouve">
                                        <i class="fas fa-hand-holding-heart me-2"></i> J'ai trouvé un animal
                                    </label>

                                    <input type="radio" class="btn-check" name="signalementType" id="typePerdu" value="perdu">
                                    <label class="btn btn-outline-danger" for="typePerdu">
                                        <i class="fas fa-sad-tear me-2"></i> J'ai perdu mon animal
                                    </label>
                                </div>
                            </div>

                            <form id="signalementForm" action="backend/signalement/create.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="type" id="typeSignalement" value="trouve">

                                <!-- Informations personnelles -->
                                <div class="mb-4">
                                    <h5 class="border-bottom pb-2"><i class="fas fa-user me-2"></i> Vos informations</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="nom" class="form-label">Nom complet *</label>
                                            <input type="text" class="form-control" id="nom" name="nom" required 
                                                   value="<?php echo isset($_SESSION['user_nom']) ? htmlspecialchars($_SESSION['user_nom']) : ''; ?>">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email *</label>
                                            <input type="email" class="form-control" id="email" name="email" required
                                                   value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="telephone" class="form-label">Téléphone *</label>
                                        <input type="tel" class="form-control" id="telephone" name="telephone" 
                                               placeholder="06 12 34 56 78" required>
                                    </div>
                                </div>

                                <!-- Informations sur l'animal -->
                                <div class="mb-4">
                                    <h5 class="border-bottom pb-2"><i class="fas fa-paw me-2"></i> Informations sur l'animal</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="espece" class="form-label">Espèce *</label>
                                            <select class="form-select" id="espece" name="espece" required>
                                                <option value="">Sélectionnez...</option>
                                                <option value="chien">🐕 Chien</option>
                                                <option value="chat">🐈 Chat</option>
                                                <option value="lapin">🐇 Lapin</option>
                                                <option value="oiseau">🦜 Oiseau</option>
                                                <option value="autre">🦎 Autre</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="race" class="form-label">Race (si connue)</label>
                                            <input type="text" class="form-control" id="race" name="race" placeholder="Ex: Labrador, Siamois...">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="couleur" class="form-label">Couleur *</label>
                                            <input type="text" class="form-control" id="couleur" name="couleur" placeholder="Ex: Noir, Roux..." required>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="taille" class="form-label">Taille</label>
                                            <select class="form-select" id="taille" name="taille">
                                                <option value="">Sélectionnez...</option>
                                                <option value="petit">Petit</option>
                                                <option value="moyen">Moyen</option>
                                                <option value="grand">Grand</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="sexe" class="form-label">Sexe</label>
                                            <select class="form-select" id="sexe" name="sexe">
                                                <option value="">Non déterminé</option>
                                                <option value="male">Mâle</option>
                                                <option value="femelle">Femelle</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description / Signes distinctifs *</label>
                                        <textarea class="form-control" id="description" name="description" rows="4" 
                                                  placeholder="Décrivez l'animal : couleur des yeux, marques spéciales, comportement, collier..." required></textarea>
                                        <small class="form-text text-muted">Plus la description est précise, plus vous avez de chances de retrouver l'animal</small>
                                    </div>
                                </div>

                                <!-- Lieu et date -->
                                <div class="mb-4">
                                    <h5 class="border-bottom pb-2"><i class="fas fa-map-marker-alt me-2"></i> Localisation</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="lieu" class="form-label">Lieu *</label>
                                            <input type="text" class="form-control" id="lieu" name="lieu" 
                                                   placeholder="Ex: Parc de la Villette, Paris 19e" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="codePostal" class="form-label">Code postal *</label>
                                            <input type="text" class="form-control" id="codePostal" name="code_postal" 
                                                   placeholder="75000" pattern="[0-9]{5}" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="dateSignalement" class="form-label">Date *</label>
                                            <input type="date" class="form-control" id="dateSignalement" name="date" 
                                                   max="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="heureSignalement" class="form-label">Heure (approximative)</label>
                                            <input type="time" class="form-control" id="heureSignalement" name="heure">
                                        </div>
                                    </div>
                                </div>

                                <!-- Photo -->
                                <div class="mb-4">
                                    <h5 class="border-bottom pb-2"><i class="fas fa-camera me-2"></i> Photo de l'animal</h5>
                                    
                                    <div class="mb-3">
                                        <label for="photo" class="form-label">Ajouter une photo</label>
                                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                        <small class="form-text text-muted">Formats acceptés : JPG, PNG, GIF (max 5 MB)</small>
                                    </div>
                                    
                                    <!-- Prévisualisation -->
                                    <div id="photoPreview" style="display: none;">
                                        <img id="previewImage" src="" alt="Aperçu" class="img-fluid rounded shadow" style="max-height: 300px;">
                                        <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removePhoto()">
                                            <i class="fas fa-times me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>

                                <!-- Informations complémentaires (si animal perdu) -->
                                <div id="infoPerdu" style="display: none;">
                                    <div class="mb-4">
                                        <h5 class="border-bottom pb-2"><i class="fas fa-info-circle me-2"></i> Informations complémentaires</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">L'animal possède-t-il :</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="puce" name="puce" value="1">
                                                <label class="form-check-label" for="puce">
                                                    Une puce électronique
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="tatouage" name="tatouage" value="1">
                                                <label class="form-check-label" for="tatouage">
                                                    Un tatouage
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="collier" name="collier" value="1">
                                                <label class="form-check-label" for="collier">
                                                    Un collier
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="numPuce" class="form-label">Numéro de puce (si connu)</label>
                                            <input type="text" class="form-control" id="numPuce" name="num_puce" placeholder="250269...">
                                        </div>
                                    </div>
                                </div>

                                <!-- Message -->
                                <div id="formMessage" class="alert d-none" role="alert"></div>

                                <!-- Boutons -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i> Envoyer le signalement
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo me-2"></i> Réinitialiser
                                    </button>
                                </div>

                                <p class="text-muted text-center mt-3 mb-0">
                                    <small>* Champs obligatoires</small>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- ANIMAUX RÉCEMMENT SIGNALÉS -->
                <div class="col-12 mt-5">
                    <h3 class="mb-4 text-center">Animaux récemment signalés</h3>
                    
                    <div class="row g-4" id="recentSignalements">
                        <!-- Exemples de signalements -->
                        <div class="col-md-4">
                            <div class="card card-animal">
                                <div class="position-relative">
                                    <img src="assets/images/animals/placeholder-dog.jpg" alt="Chien" class="card-img-top">
                                    <span class="badge bg-warning position-absolute top-0 end-0 m-3">
                                        <i class="fas fa-search me-1"></i> Perdu
                                    </span>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Chien perdu - Labrador</h5>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-map-marker-alt"></i> Paris 15e
                                    </p>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-calendar"></i> Signalé le 08/10/2025
                                    </p>
                                    <p class="card-text small">Chien beige, collier bleu, très affectueux</p>
                                    <a href="signalement-detail.php?id=1" class="btn btn-sm btn-outline-primary w-100">
                                        Voir les détails
                                    </a>
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
    
    <script>
    // Toggle entre "Trouvé" et "Perdu"
    document.querySelectorAll('input[name="signalementType"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('typeSignalement').value = this.value;
            const infoPerdu = document.getElementById('infoPerdu');
            
            if(this.value === 'perdu') {
                infoPerdu.style.display = 'block';
            } else {
                infoPerdu.style.display = 'none';
            }
        });
    });

    // Prévisualisation de la photo
    document.getElementById('photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if(file) {
            // Vérifier la taille (5 MB max)
            if(file.size > 5 * 1024 * 1024) {
                alert('La photo ne doit pas dépasser 5 MB');
                this.value = '';
                return;
            }
            
            // Vérifier le format
            if(!file.type.match('image.*')) {
                alert('Veuillez sélectionner une image valide');
                this.value = '';
                return;
            }
            
            // Afficher l'aperçu
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImage').src = e.target.result;
                document.getElementById('photoPreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Supprimer la photo
    function removePhoto() {
        document.getElementById('photo').value = '';
        document.getElementById('photoPreview').style.display = 'none';
    }

    // Définir la date d'aujourd'hui par défaut
    document.getElementById('dateSignalement').value = new Date().toISOString().split('T')[0];

    // Soumission du formulaire
    document.getElementById('signalementForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const messageDiv = document.getElementById('formMessage');
        
        // Loading
        PawConnect.setButtonLoading(submitBtn, true);
        
        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            messageDiv.classList.remove('d-none', 'alert-danger', 'alert-success');
            
            if(data.success) {
                messageDiv.classList.add('alert-success');
                messageDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + data.message;
                this.reset();
                document.getElementById('photoPreview').style.display = 'none';
                
                // Scroll vers le message
                messageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Rediriger après 3 secondes
                setTimeout(() => {
                    window.location.href = 'signalements-liste.php';
                }, 3000);
            } else {
                messageDiv.classList.add('alert-danger');
                messageDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + data.message;
            }
            
        } catch(error) {
            console.error('Erreur:', error);
            messageDiv.classList.remove('d-none');
            messageDiv.classList.add('alert-danger');
            messageDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Une erreur est survenue lors de l\'envoi';
        } finally {
            PawConnect.setButtonLoading(submitBtn, false);
        }
    });

    // Validation du code postal
    document.getElementById('codePostal').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5);
    });

    // Validation du téléphone
    document.getElementById('telephone').addEventListener('input', function() {
        // Format automatique: 06 12 34 56 78
        let value = this.value.replace(/\D/g, '');
        if(value.length > 0) {
            value = value.match(/.{1,2}/g).join(' ');
        }
        this.value = value.slice(0, 14);
    });
    </script>
</body>
</html>