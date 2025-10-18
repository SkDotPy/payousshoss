<?php
session_start();

// Récupérer l'ID de l'animal
$animal_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($animal_id === 0) {
    header('Location: search.php');
    exit;
}

// Simulation des données (à remplacer par requête BDD)
$animal = [
    'id' => $animal_id,
    'nom' => 'Max',
    'species' => 'Chien',
    'race' => 'Labrador',
    'age' => 3,
    'sex' => 'Mâle',
    'color' => 'Doré',
    'description' => 'Max est un adorable Labrador de 3 ans, très affectueux et sociable. Il adore jouer et est parfait pour une famille active. Il est vacciné, stérilisé et en excellente santé.',
    'state' => 'Disponible',
    'image' => '/assets/images/animals/dog1.jpg',
    'refuge_name' => 'Refuge de Paris',
    'location' => 'Paris (75)',
    'date_arrivee' => '2024-12-15',
    'vaccins' => true,
    'sterilise' => true,
    'sociable_enfants' => true,
    'sociable_animaux' => true
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($animal['nom']); ?> - Paw Connect</title>
    
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
                    <li class="nav-item"><a class="nav-link active" href="search.php">Adopter</a></li>
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

    <!-- CONTENU PRINCIPAL -->
    <section class="py-5">
        <div class="container">
            <!-- Fil d'ariane -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="search.php">Adopter</a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($animal['nom']); ?></li>
                </ol>
            </nav>

            <div class="row">
                <!-- GALERIE PHOTOS -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg">
                        <img src="<?php echo $animal['image']; ?>" alt="<?php echo htmlspecialchars($animal['nom']); ?>" class="card-img-top" style="height: 500px; object-fit: cover;">
                        
                        <!-- Badge statut -->
                        <?php if($animal['state'] === 'Disponible'): ?>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success fs-6 p-2">
                                    <i class="fas fa-check-circle me-1"></i> Disponible
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Boutons partage -->
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-outline-primary flex-fill" onclick="shareAnimal()">
                            <i class="fas fa-share-alt me-2"></i> Partager
                        </button>
                        <button class="btn btn-outline-secondary" onclick="addToFavorites(<?php echo $animal['id']; ?>)">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                </div>

                <!-- INFORMATIONS -->
                <div class="col-lg-6">
                    <div class="card shadow-lg p-4">
                        <h1 class="mb-3"><?php echo htmlspecialchars($animal['nom']); ?></h1>
                        
                        <div class="mb-4">
                            <span class="badge bg-primary me-2"><?php echo $animal['species']; ?></span>
                            <span class="badge bg-secondary me-2"><?php echo $animal['race']; ?></span>
                            <span class="badge" style="background:#F59E0B"><?php echo $animal['age']; ?> ans</span>
                        </div>

                        <div class="mb-4">
                            <h5>Description</h5>
                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($animal['description'])); ?></p>
                        </div>

                        <!-- Caractéristiques -->
                        <div class="mb-4">
                            <h5>Caractéristiques</h5>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-venus-mars fa-2x text-primary me-3"></i>
                                        <div>
                                            <small class="text-muted">Sexe</small>
                                            <p class="mb-0 fw-bold"><?php echo $animal['sex']; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-palette fa-2x text-primary me-3"></i>
                                        <div>
                                            <small class="text-muted">Couleur</small>
                                            <p class="mb-0 fw-bold"><?php echo $animal['color']; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt fa-2x text-primary me-3"></i>
                                        <div>
                                            <small class="text-muted">Localisation</small>
                                            <p class="mb-0 fw-bold"><?php echo $animal['location']; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-home fa-2x text-primary me-3"></i>
                                        <div>
                                            <small class="text-muted">Refuge</small>
                                            <p class="mb-0 fw-bold"><?php echo $animal['refuge_name']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations santé -->
                        <div class="mb-4">
                            <h5>Santé & Vaccination</h5>
                            <div class="list-group">
                                <div class="list-group-item">
                                    <i class="fas fa-<?php echo $animal['vaccins'] ? 'check-circle text-success' : 'times-circle text-danger'; ?> me-2"></i>
                                    Vacciné
                                </div>
                                <div class="list-group-item">
                                    <i class="fas fa-<?php echo $animal['sterilise'] ? 'check-circle text-success' : 'times-circle text-danger'; ?> me-2"></i>
                                    Stérilisé
                                </div>
                                <div class="list-group-item">
                                    <i class="fas fa-<?php echo $animal['sociable_enfants'] ? 'check-circle text-success' : 'times-circle text-danger'; ?> me-2"></i>
                                    Sociable avec les enfants
                                </div>
                                <div class="list-group-item">
                                    <i class="fas fa-<?php echo $animal['sociable_animaux'] ? 'check-circle text-success' : 'times-circle text-danger'; ?> me-2"></i>
                                    Sociable avec les autres animaux
                                </div>
                            </div>
                        </div>

                        <!-- Bouton d'adoption -->
                        <?php if($animal['state'] === 'Disponible'): ?>
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <a href="adoption-form.php?animal_id=<?php echo $animal['id']; ?>" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-heart me-2"></i> Je veux adopter <?php echo $animal['nom']; ?>
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i> Connectez-vous pour adopter
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-lg w-100" disabled>
                                <i class="fas fa-ban me-2"></i> Non disponible
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ANIMAUX SIMILAIRES -->
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="mb-4">Animaux similaires</h3>
                </div>
                
                <!-- Carrousel d'animaux similaires (à implémenter dynamiquement) -->
                <div class="col-md-4">
                    <div class="card card-animal">
                        <img src="/assets/images/animals/placeholder-dog.jpg" alt="Animal" class="card-img-top">
                        <span class="badge-available">Disponible</span>
                        <div class="card-body">
                            <h5 class="card-title">Rocky</h5>
                            <p class="text-muted mb-2">
                                <i class="fas fa-dog"></i> Labrador • 2 ans • Mâle
                            </p>
                            <a href="animal-detail.php?id=2" class="btn btn-primary btn-sm w-100">Voir plus</a>
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
    function shareAnimal() {
        if (navigator.share) {
            navigator.share({
                title: '<?php echo addslashes($animal['nom']); ?> - Paw Connect',
                text: 'Découvrez <?php echo addslashes($animal['nom']); ?>, un adorable <?php echo addslashes($animal['species']); ?> à adopter !',
                url: window.location.href
            }).catch(err => console.log('Erreur partage:', err));
        } else {
            PawConnect.copyToClipboard(window.location.href);
        }
    }
    
    async function addToFavorites(animalId) {
        <?php if(!isset($_SESSION['user_id'])): ?>
            window.location.href = 'login.php';
            return;
        <?php endif; ?>
        
        try {
            const response = await fetch('/backend/favorites/add.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ animal_id: animalId })
            });
            
            const data = await response.json();
            
            if(data.success) {
                PawConnect.showToast('Ajouté aux favoris !', 'success');
                event.target.innerHTML = '<i class="fas fa-heart"></i>';
            } else {
                PawConnect.showToast(data.message, 'error');
            }
        } catch(error) {
            PawConnect.showToast('Erreur lors de l\'ajout aux favoris', 'error');
        }
    }
    </script>
</body>
</html>