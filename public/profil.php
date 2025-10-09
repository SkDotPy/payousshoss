<?php
session_start();

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Récupérer les infos utilisateur depuis la BDD (simulation)
$user = [
    'id' => $_SESSION['user_id'],
    'nom' => $_SESSION['user_nom'] ?? 'Utilisateur',
    'email' => $_SESSION['user_email'] ?? 'user@example.com',
    'age' => $_SESSION['user_age'] ?? 25,
    'actif' => $_SESSION['user_actif'] ?? 1,
    'security' => $_SESSION['user_security'] ?? 'medium',
    'role' => $_SESSION['user_role'] ?? 'user',
    'avatar_color' => $_SESSION['avatar_color'] ?? '#1E3A8A',
    'date_creation' => $_SESSION['date_creation'] ?? date('Y-m-d')
];

// Palette de couleurs pour l'avatar
$avatarColors = ['#1E3A8A', '#60A5FA', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Paw Connect</title>
    
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
                    <li class="nav-item"><a class="nav-link" href="newsletter.php">Newsletter</a></li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> Mon compte
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item active" href="profil.php">Mon profil</a></li>
                            <li><a class="dropdown-item" href="mes-adoptions.php">Mes adoptions</a></li>
                            <?php if($user['role'] === 'admin'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="admin/index.php"><i class="fas fa-cog"></i> Administration</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Déconnexion</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item ms-2">
                        <button class="dark-mode-toggle" id="darkModeToggle">
                            <i class="fas fa-moon"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- PROFIL SECTION -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- SIDEBAR -->
                <div class="col-lg-3 mb-4">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <!-- Avatar avec couleur personnalisée -->
                            <div class="mb-3">
                                <div class="avatar-display" style="width: 120px; height: 120px; background-color: <?php echo $user['avatar_color']; ?>; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; font-weight: 700;">
                                    <?php echo strtoupper(substr($user['nom'], 0, 2)); ?>
                                </div>
                            </div>
                            
                            <h4 class="mb-1"><?php echo htmlspecialchars($user['nom']); ?></h4>
                            <p class="text-muted mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                            
                            <span class="badge badge-success mb-3">
                                <i class="fas fa-check-circle"></i> Compte actif
                            </span>
                            
                            <hr>
                            
                            <div class="text-start">
                                <p class="mb-2"><strong>Membre depuis :</strong><br><?php echo date('d/m/Y', strtotime($user['date_creation'])); ?></p>
                                <p class="mb-2"><strong>Rôle :</strong><br>
                                    <?php if($user['role'] === 'admin'): ?>
                                        <span class="badge badge-primary">Administrateur</span>
                                    <?php else: ?>
                                        <span class="badge" style="background-color: #D1FAE5; color: #065F46;">Utilisateur</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Menu latéral -->
                    <div class="card shadow mt-3">
                        <div class="list-group list-group-flush">
                            <a href="#infos" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                                <i class="fas fa-user me-2"></i> Informations
                            </a>
                            <a href="#avatar" class="list-group-item list-group-item-action" data-bs-toggle="list">
                                <i class="fas fa-palette me-2"></i> Avatar
                            </a>
                            <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">
                                <i class="fas fa-lock me-2"></i> Sécurité
                            </a>
                            <a href="mes-adoptions.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-heart me-2"></i> Mes adoptions
                            </a>
                        </div>
                    </div>
                </div>

                <!-- CONTENU PRINCIPAL -->
                <div class="col-lg-9">
                    <div class="tab-content">
                        <!-- ONGLET INFORMATIONS -->
                        <div class="tab-pane fade show active" id="infos">
                            <div class="card shadow">
                                <div class="card-header bg-white">
                                    <h4 class="mb-0"><i class="fas fa-user me-2"></i> Mes informations personnelles</h4>
                                </div>
                                <div class="card-body">
                                    <form id="updateInfoForm" action="backend/user/update-info.php" method="POST">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="nom" class="form-label">Nom complet</label>
                                                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="age" class="form-label">Âge</label>
                                                <input type="number" class="form-control" id="age" name="age" value="<?php echo $user['age']; ?>" min="18" required>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Note :</strong> Si vous modifiez votre email, vous devrez le vérifier à nouveau.
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Enregistrer les modifications
                                            </button>
                                            <button type="reset" class="btn btn-outline-secondary">
                                                <i class="fas fa-undo me-2"></i> Annuler
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- ONGLET AVATAR -->
                        <div class="tab-pane fade" id="avatar">
                            <div class="card shadow">
                                <div class="card-header bg-white">
                                    <h4 class="mb-0"><i class="fas fa-palette me-2"></i> Personnaliser mon avatar</h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-4">Choisissez une couleur pour votre avatar</p>
                                    
                                    <form id="avatarForm" action="backend/user/update-avatar.php" method="POST">
                                        <div class="avatar-palette">
                                            <?php foreach($avatarColors as $color): ?>
                                                <div class="avatar-color <?php echo $color === $user['avatar_color'] ? 'selected' : ''; ?>" 
                                                     style="background-color: <?php echo $color; ?>;"
                                                     data-color="<?php echo $color; ?>"
                                                     onclick="selectAvatarColor(this)">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <input type="hidden" id="selectedColor" name="avatar_color" value="<?php echo $user['avatar_color']; ?>">
                                        
                                        <!-- Aperçu -->
                                        <div class="mt-4 text-center">
                                            <p class="mb-2"><strong>Aperçu :</strong></p>
                                            <div id="avatarPreview" style="width: 100px; height: 100px; background-color: <?php echo $user['avatar_color']; ?>; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: white; font-weight: 700;">
                                                <?php echo strtoupper(substr($user['nom'], 0, 2)); ?>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid mt-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i> Sauvegarder l'avatar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- ONGLET SÉCURITÉ -->
                        <div class="tab-pane fade" id="security">
                            <div class="card shadow">
                                <div class="card-header bg-white">
                                    <h4 class="mb-0"><i class="fas fa-lock me-2"></i> Sécurité du compte</h4>
                                </div>
                                <div class="card-body">
                                    <h5 class="mb-3">Changer le mot de passe</h5>
                                    
                                    <form id="passwordForm" action="backend/user/update-password.php" method="POST">
                                        <div class="mb-3">
                                            <label for="currentPassword" class="form-label">Mot de passe actuel</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('currentPassword', this)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="newPassword" class="form-label">Nouveau mot de passe</label>
                                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                            <div class="form-text">Minimum 8 caractères</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="confirmPassword" class="form-label">Confirmer le nouveau mot de passe</label>
                                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-key me-2"></i> Changer le mot de passe
                                        </button>
                                    </form>
                                    
                                    <hr class="my-4">
                                    
                                    <h5 class="mb-3">Niveau de sécurité</h5>
                                    <div class="alert alert-warning">
                                        <strong>Actuel :</strong> Sécurité moyenne
                                        <div class="progress mt-2" style="height: 10px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 60%"></div>
                                        </div>
                                    </div>
                                    
                                    <hr class="my-4">
                                    
                                    <h5 class="text-danger mb-3">Zone dangereuse</h5>
                                    <p class="text-muted">La suppression de votre compte est définitive et irréversible.</p>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                        <i class="fas fa-trash me-2"></i> Supprimer mon compte
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL SUPPRESSION COMPTE -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> Supprimer le compte</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Attention :</strong> Cette action est irréversible !</p>
                    <p>Toutes vos données, adoptions et historiques seront définitivement supprimés.</p>
                    <p>Tapez <code>SUPPRIMER</code> pour confirmer :</p>
                    <input type="text" class="form-control" id="deleteConfirm" placeholder="SUPPRIMER">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete" disabled>Supprimer définitivement</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/avatar.js"></script>
    
    <script>
    // Sélection couleur avatar
    function selectAvatarColor(element) {
        document.querySelectorAll('.avatar-color').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
        
        const color = element.dataset.color;
        document.getElementById('selectedColor').value = color;
        document.getElementById('avatarPreview').style.backgroundColor = color;
    }
    
    // Toggle password visibility
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        
        if(input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
    
    // Confirmation suppression compte
    document.getElementById('deleteConfirm')?.addEventListener('input', function(e) {
        const btn = document.getElementById('confirmDelete');
        btn.disabled = e.target.value !== 'SUPPRIMER';
    });
    
    document.getElementById('confirmDelete')?.addEventListener('click', function() {
        if(confirm('Êtes-vous absolument certain de vouloir supprimer votre compte ?')) {
            window.location.href = 'backend/user/delete-account.php';
        }
    });
    </script>
</body>
</html