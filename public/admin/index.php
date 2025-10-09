<?php
session_start();

// Vérifier si l'utilisateur est admin
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Statistiques (simulées)
$stats = [
    'total_users' => 1250,
    'total_animals' => 87,
    'total_adoptions' => 342,
    'pending_adoptions' => 12
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Paw Connect</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <div class="admin-wrapper">
        <!-- SIDEBAR -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-logo">
                <i class="fas fa-paw fa-2x mb-2"></i>
                <h3>Paw Admin</h3>
            </div>
            
            <nav class="admin-nav">
                <a href="index.php" class="admin-nav-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="users.php" class="admin-nav-item">
                    <i class="fas fa-users"></i>
                    <span>Utilisateurs</span>
                </a>
                <a href="animals.php" class="admin-nav-item">
                    <i class="fas fa-paw"></i>
                    <span>Animaux</span>
                </a>
                <a href="adoptions.php" class="admin-nav-item">
                    <i class="fas fa-heart"></i>
                    <span>Adoptions</span>
                </a>
                <a href="refuges.php" class="admin-nav-item">
                    <i class="fas fa-home"></i>
                    <span>Refuges</span>
                </a>
                <a href="newsletter.php" class="admin-nav-item">
                    <i class="fas fa-envelope"></i>
                    <span>Newsletter</span>
                </a>
                <a href="captcha.php" class="admin-nav-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Captcha</span>
                </a>
                <a href="logs.php" class="admin-nav-item">
                    <i class="fas fa-list"></i>
                    <span>Logs</span>
                </a>
                <a href="settings.php" class="admin-nav-item">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
                
                <hr style="border-color: rgba(255,255,255,0.1); margin: 1rem 0;">
                
                <a href="../index.php" class="admin-nav-item">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour au site</span>
                </a>
                <a href="../logout.php" class="admin-nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </nav>
        </aside>

        <!-- TOGGLE SIDEBAR BUTTON -->
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- CONTENU PRINCIPAL -->
        <main class="admin-content" id="adminContent">
            <!-- HEADER -->
            <div class="admin-header">
                <div>
                    <h1>Dashboard</h1>
                    <p class="text-muted mb-0">Bienvenue, <?php echo htmlspecialchars($_SESSION['user_nom']); ?></p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button class="dark-mode-toggle" id="darkModeToggle">
                        <i class="fas fa-moon"></i>
                    </button>
                    <span class="text-muted"><?php echo date('d/m/Y H:i'); ?></span>
                </div>
            </div>

            <!-- STATISTIQUES -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_users']; ?></h3>
                        <p>Utilisateurs</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-paw"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_animals']; ?></h3>
                        <p>Animaux disponibles</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon yellow">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_adoptions']; ?></h3>
                        <p>Adoptions réalisées</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon red">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['pending_adoptions']; ?></h3>
                        <p>Adoptions en attente</p>
                    </div>
                </div>
            </div>

            <!-- GRAPHIQUES -->
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="chart-container">
                        <h4 class="mb-4">Adoptions par mois</h4>
                        <div id="adoptionsChart" style="height: 300px; display: flex; align-items: center; justify-content: center; background: #F3F4F6; border-radius: 0.5rem;">
                            <p class="text-muted">Graphique des adoptions (à implémenter avec Chart.js)</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="chart-container">
                        <h4 class="mb-4">Animaux par espèce</h4>
                        <div id="speciesChart" style="height: 300px; display: flex; align-items: center; justify-content: center; background: #F3F4F6; border-radius: 0.5rem;">
                            <p class="text-muted">Diagramme circulaire (à implémenter)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTIVITÉ RÉCENTE -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="data-table">
                        <div class="table-header">
                            <h4 class="mb-0">Dernières inscriptions</h4>
                            <a href="users.php" class="btn btn-sm btn-outline-primary">Voir tout</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Marie Dupont</td>
                                        <td>marie@example.com</td>
                                        <td>09/10/2025</td>
                                        <td>
                                            <button class="action-btn" title="Voir"><i class="fas fa-eye"></i></button>
                                            <button class="action-btn delete" title="Supprimer"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jean Martin</td>
                                        <td>jean@example.com</td>
                                        <td>08/10/2025</td>
                                        <td>
                                            <button class="action-btn" title="Voir"><i class="fas fa-eye"></i></button>
                                            <button class="action-btn delete" title="Supprimer"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Sophie Bernard</td>
                                        <td>sophie@example.com</td>
                                        <td>07/10/2025</td>
                                        <td>
                                            <button class="action-btn" title="Voir"><i class="fas fa-eye"></i></button>
                                            <button class="action-btn delete" title="Supprimer"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="data-table">
                        <div class="table-header">
                            <h4 class="mb-0">Adoptions en attente</h4>
                            <a href="adoptions.php" class="btn btn-sm btn-outline-primary">Voir tout</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Animal</th>
                                        <th>Adoptant</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Max</strong> (Chien)</td>
                                        <td>Pierre Durant</td>
                                        <td>09/10/2025</td>
                                        <td><span class="badge badge-warning">En attente</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Luna</strong> (Chat)</td>
                                        <td>Alice Moreau</td>
                                        <td>08/10/2025</td>
                                        <td><span class="badge badge-warning">En attente</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Flocon</strong> (Lapin)</td>
                                        <td>Marc Petit</td>
                                        <td>07/10/2025</td>
                                        <td><span class="badge badge-success">Validée</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- UTILISATEURS CONNECTÉS -->
            <div class="row">
                <div class="col-12">
                    <div class="data-table">
                        <div class="table-header">
                            <h4 class="mb-0"><i class="fas fa-circle text-success me-2"></i> Utilisateurs connectés en temps réel</h4>
                            <span class="badge badge-success">5 en ligne</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Dernière activité</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Admin Système</td>
                                        <td>admin@pawconnect.fr</td>
                                        <td><span class="badge badge-primary">Admin</span></td>
                                        <td>Il y a 1 min</td>
                                        <td><i class="fas fa-circle text-success"></i> En ligne</td>
                                    </tr>
                                    <tr>
                                        <td>Julie Rousseau</td>
                                        <td>julie@example.com</td>
                                        <td><span class="badge" style="background:#D1FAE5;color:#065F46">User</span></td>
                                        <td>Il y a 3 min</td>
                                        <td><i class="fas fa-circle text-success"></i> En ligne</td>
                                    </tr>
                                    <tr>
                                        <td>Thomas Leroy</td>
                                        <td>thomas@example.com</td>
                                        <td><span class="badge" style="background:#D1FAE5;color:#065F46">User</span></td>
                                        <td>Il y a 5 min</td>
                                        <td><i class="fas fa-circle text-success"></i> En ligne</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTIONS RAPIDES -->
            <div class="row mt-4">
                <div class="col-12">
                    <h4 class="mb-3">Actions rapides</h4>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="animals.php?action=add" class="btn btn-primary w-100 btn-lg">
                        <i class="fas fa-plus-circle me-2"></i> Ajouter un animal
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="users.php" class="btn btn-outline-primary w-100 btn-lg">
                        <i class="fas fa-users me-2"></i> Gérer utilisateurs
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="newsletter.php?action=send" class="btn btn-outline-primary w-100 btn-lg">
                        <i class="fas fa-paper-plane me-2"></i> Envoyer newsletter
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="logs.php" class="btn btn-outline-primary w-100 btn-lg">
                        <i class="fas fa-list me-2"></i> Voir les logs
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
    <script src="../assets/js/admin.js"></script>
    
    <script>
    // Toggle sidebar
    const sidebar = document.getElementById('adminSidebar');
    const content = document.getElementById('adminContent');
    const toggleBtn = document.getElementById('sidebarToggle');

    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('expanded');
        this.classList.toggle('collapsed');
    });

    // Responsive sidebar mobile
    if(window.innerWidth <= 1024) {
        sidebar.classList.add('collapsed');
        content.classList.add('expanded');
    }

    // Auto-refresh des stats toutes les 30 secondes
    setInterval(async function() {
        try {
            const response = await fetch('../backend/admin/get-stats.php');
            const data = await response.json();
            if(data.success) {
                // Mettre à jour les statistiques (à implémenter)
                console.log('Stats mises à jour', data);
            }
        } catch(error) {
            console.error('Erreur refresh stats:', error);
        }
    }, 30000);
    </script>
</body>
</html>