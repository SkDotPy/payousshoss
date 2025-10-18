<?php
session_start();
require_once __DIR__ . '/config.php';

// Récupérer l'ID de l'animal
$animal_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($animal_id === 0) {
    header('Location: search.php');
    exit;
}

// Récupérer les vraies données depuis la BDD
try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            u.nom as refuge_name,
            u.email as refuge_email
        FROM animals a
        LEFT JOIN users u ON a.refuge_id = u.id
        WHERE a.id = ?
    ");
    $stmt->execute([$animal_id]);
    $animal = $stmt->fetch();

    if (!$animal) {
        header('Location: search.php');
        exit;
    }

    // Convertir le sexe en français
    $sexLabels = [
        'male' => 'Mâle',
        'female' => 'Femelle',
        'unknown' => 'Inconnu'
    ];
    $animal['sex_label'] = $sexLabels[$animal['sex']] ?? 'Inconnu';

    // Convertir la taille en français
    $sizeLabels = [
        'small' => 'Petit',
        'medium' => 'Moyen',
        'large' => 'Grand'
    ];
    $animal['size_label'] = $sizeLabels[$animal['size']] ?? '';

    // Convertir le statut
    $statusLabels = [
        'available' => 'Disponible',
        'reserved' => 'Réservé',
        'adopted' => 'Adopté'
    ];
    $animal['status_label'] = $statusLabels[$animal['status']] ?? 'Inconnu';

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($animal['name']); ?> - Paw Connect</title>

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
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($animal['name']); ?></li>
                </ol>
            </nav>

            <div class="row">
                <!-- GALERIE PHOTOS -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-lg">
                        <?php $mainPhoto = $animal['photo1'] ?? 'assets/images/animals/placeholder.jpg'; ?>
                        <img src="<?php echo htmlspecialchars($mainPhoto); ?>" 
                             alt="<?php echo htmlspecialchars($animal['name']); ?>" 
                             class="card-img-top" 
                             style="height: 500px; object-fit: cover;"
                             onerror="this.src='assets/images/animals/placeholder.jpg'">

                        <!-- Badge statut -->
                        <?php if($animal['status'] === 'available'): ?>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success fs-6 p-2">
                                    <i class="fas fa-check-circle me-1"></i> Disponible
                                </span>
                            </div>
                        <?php elseif($animal['status'] === 'reserved'): ?>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning fs-6 p-2">
                                    <i class="fas fa-clock me-1"></i> Réservé
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Photos supplémentaires -->
                    <?php if (!empty($animal['photo2']) || !empty($animal['photo3'])): ?>
                    <div class="row g-2 mt-3">
                        <?php if (!empty($animal['photo2'])): ?>
                        <div class="col-6">
                            <img src="<?php echo htmlspecialchars($animal['photo2']); ?>" 
                                 class="img-fluid rounded" 
                                 style="height: 150px; width: 100%; object-fit: cover;"
                                 onerror="this.style.display='none'">
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($animal['photo3'])): ?>
                        <div class="col-6">
                            <img src="<?php echo htmlspecialchars($animal['photo3']); ?>" 
                                 class="img-fluid rounded" 
                                 style="height: 150px; width: 100%; object-fit: cover;"
                                 onerror="this.style.display='none'">
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

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
                        <h1 class="mb-3"><?php echo htmlspecialchars($animal['name']); ?></h1>

                        <div class="mb-4">
                            <span class="badge bg-primary me-2"><?php echo htmlspecialchars($animal['species']); ?></span>
                            <?php if (!empty($animal['breed'])): ?>
                            <span class="badge bg-secondary me-2"><?php echo htmlspecialchars($animal['breed']); ?></span>
                            <?php endif; ?>
                            <span class="badge" style="background:#F59E0B"><?php echo htmlspecialchars($animal['age']); ?></span>
                        </div>

                        <?php if (!empty($animal['description'])): ?>
                        <div class="mb-4">
                            <h5>Description</h5>
                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($animal['description'])); ?></p>
                        </div>
                        <?php endif; ?>

                        <!-- Caractéristiques -->
                        <div class="mb-4">
                            <h5>Caractéristiques</h5>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-venus-mars fa-2x text-primary me-3"></i>
                                        <div>
                                            <small class="text-muted">Sexe</small>
                                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($animal['sex_label']); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($animal['color'])): ?>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-palette fa-2x text-primary me-3"></i>
                                        <div>
                                            <small class="text-muted">Couleur</small>
                                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($animal['color']); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($animal['size'])): ?>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-ruler fa-2x text-primary me-3"></i>
                                        <div>
                                            <small class="text-muted">Taille</small>
                                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($animal['size_label']); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($animal['refuge_name'])): ?>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-home fa-2x text-primary me-3"></i>
                                        <div>
                                            <small class="text-muted">Refuge</small>
                                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($animal['refuge_name']); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Informations santé (placeholder - à adapter selon vos données) -->
                        <div class="mb-4">
                            <h5>Santé & Vaccination</h5>
                            <div class="list-group">
                                <div class="list-group-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Vacciné
                                </div>
                                <div class="list-group-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Stérilisé
                                </div>
                                <div class="list-group-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Sociable avec les enfants
                                </div>
                                <div class="list-group-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Sociable avec les autres animaux
                                </div>
                            </div>
                        </div>

                        <!-- Bouton d'adoption -->
                        <?php if($animal['status'] === 'available'): ?>
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <a href="adoption-form.php?animal_id=<?php echo $animal['id']; ?>" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-heart me-2"></i> Je veux adopter <?php echo htmlspecialchars($animal['name']); ?>
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i> Connectez-vous pour adopter
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-lg w-100" disabled>
                                <i class="fas fa-ban me-2"></i> <?php echo htmlspecialchars($animal['status_label']); ?>
                            </button>
                        <?php endif; ?>
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
                title: '<?php echo addslashes($animal['name']); ?> - Paw Connect',
                text: 'Découvrez <?php echo addslashes($animal['name']); ?>, un adorable <?php echo addslashes($animal['species']); ?> à adopter !',
                url: window.location.href
            }).catch(err => console.log('Erreur partage:', err));
        } else {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('Lien copié dans le presse-papier !');
            });
        }
    }

    async function addToFavorites(animalId) {
        <?php if(!isset($_SESSION['user_id'])): ?>
            window.location.href = 'login.php';
            return;
        <?php endif; ?>

        alert('Fonctionnalité en cours de développement');
    }
    </script>
</body>
</html>
