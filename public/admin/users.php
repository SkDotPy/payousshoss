    <?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateurs - Paw Connect Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <div class="admin-wrapper">
        <!-- SIDEBAR (identique à index.php) -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-logo">
                <i class="fas fa-paw fa-2x mb-2"></i>
                <h3>Paw Admin</h3>
            </div>
            
            <nav class="admin-nav">
                <a href="index.php" class="admin-nav-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="users.php" class="admin-nav-item active">
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

        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- CONTENU -->
        <main class="admin-content" id="adminContent">
            <div class="admin-header">
                <div>
                    <h1><i class="fas fa-users me-2"></i> Gestion des Utilisateurs</h1>
                    <p class="text-muted mb-0">Gérer les comptes, rôles et droits d'accès</p>
                </div>
                <button class="dark-mode-toggle" id="darkModeToggle">
                    <i class="fas fa-moon"></i>
                </button>
            </div>

            <!-- FILTRES ET RECHERCHE -->
            <div class="data-table mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchUser" placeholder="Rechercher par nom, email...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterRole">
                            <option value="">Tous les rôles</option>
                            <option value="admin">Administrateurs</option>
                            <option value="user">Utilisateurs</option>
                            <option value="refuge">Refuges</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterStatus">
                            <option value="">Tous les statuts</option>
                            <option value="active">Actifs</option>
                            <option value="inactive">Inactifs</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- TABLEAU UTILISATEURS -->
            <div class="data-table">
                <div class="table-header">
                    <h4 class="mb-0">Liste des utilisateurs (1,250)</h4>
                    <div class="table-actions">
                        <button class="btn btn-outline-secondary" onclick="exportUsers()">
                            <i class="fas fa-download me-2"></i> Exporter
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-plus me-2"></i> Ajouter
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Date inscription</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <!-- Exemple de lignes -->
                            <tr>
                                <td><input type="checkbox" class="user-checkbox" value="1"></td>
                                <td>#001</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:35px;height:35px;background:#1E3A8A;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:600;">JD</div>
                                        <strong>Jean Dupont</strong>
                                    </div>
                                </td>
                                <td>jean.dupont@example.com</td>
                                <td><span class="badge badge-primary">Admin</span></td>
                                <td>15/01/2024</td>
                                <td><span class="badge badge-success">Actif</span></td>
                                <td>
                                    <button class="action-btn" onclick="viewUser(1)" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn" onclick="editUser(1)" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn delete" onclick="deleteUser(1)" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td><input type="checkbox" class="user-checkbox" value="2"></td>
                                <td>#002</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:35px;height:35px;background:#10B981;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:600;">MM</div>
                                        <strong>Marie Martin</strong>
                                    </div>
                                </td>
                                <td>marie.martin@example.com</td>
                                <td><span class="badge" style="background:#D1FAE5;color:#065F46">User</span></td>
                                <td>20/03/2024</td>
                                <td><span class="badge badge-success">Actif</span></td>
                                <td>
                                    <button class="action-btn" onclick="viewUser(2)"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn" onclick="editUser(2)"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete" onclick="deleteUser(2)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>

                            <tr>
                                <td><input type="checkbox" class="user-checkbox" value="3"></td>
                                <td>#003</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:35px;height:35px;background:#F59E0B;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:600;">PD</div>
                                        <strong>Pierre Durant</strong>
                                    </div>
                                </td>
                                <td>pierre.durant@example.com</td>
                                <td><span class="badge" style="background:#FEF3C7;color:#92400E">Refuge</span></td>
                                <td>10/05/2024</td>
                                <td><span class="badge badge-success">Actif</span></td>
                                <td>
                                    <button class="action-btn" onclick="viewUser(3)"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn" onclick="editUser(3)"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete" onclick="deleteUser(3)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>

                            <tr>
                                <td><input type="checkbox" class="user-checkbox" value="4"></td>
                                <td>#004</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:35px;height:35px;background:#EF4444;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:600;">SB</div>
                                        <strong>Sophie Bernard</strong>
                                    </div>
                                </td>
                                <td>sophie.bernard@example.com</td>
                                <td><span class="badge" style="background:#D1FAE5;color:#065F46">User</span></td>
                                <td>25/06/2024</td>
                                <td><span class="badge badge-danger">Inactif</span></td>
                                <td>
                                    <button class="action-btn" onclick="viewUser(4)"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn" onclick="editUser(4)"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete" onclick="deleteUser(4)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Affichage de 1 à 4 sur 1,250 utilisateurs
                    </div>
                    <nav>
                        <ul class="pagination mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#"><i class="fas fa-chevron-left"></i></a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">...</a></li>
                            <li class="page-item"><a class="page-link" href="#">313</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL AJOUT UTILISATEUR -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i> Ajouter un utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom complet</label>
                                <input type="text" class="form-control" name="nom" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Âge</label>
                                <input type="number" class="form-control" name="age" min="18" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                            </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rôle</label>
                                <select class="form-select" name="role" required>
                                    <option value="user">Utilisateur</option>
                                    <option value="admin">Administrateur</option>
                                    <option value="refuge">Refuge</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" onclick="saveUser()">
                        <i class="fas fa-save me-2"></i> Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
    <script src="../assets/js/admin.js"></script>
    
    <script>
    // Toggle sidebar
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('adminSidebar').classList.toggle('collapsed');
        document.getElementById('adminContent').classList.toggle('expanded');
    });

    // Fonctions CRUD utilisateurs
    function viewUser(id) {
        window.location.href = `user-detail.php?id=${id}`;
    }

    function editUser(id) {
        console.log('Edit user:', id);
        // Ouvrir modal d'édition
    }

    async function deleteUser(id) {
        if(!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) return;
        
        try {
            const response = await fetch(`../backend/admin/delete-user.php?id=${id}`, {
                method: 'DELETE'
            });
            const data = await response.json();
            
            if(data.success) {
                alert('Utilisateur supprimé avec succès');
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        } catch(error) {
            alert('Erreur lors de la suppression');
        }
    }

    function exportUsers() {
        window.location.href = '../backend/admin/export-users.php';
    }

    function saveUser() {
        const form = document.getElementById('addUserForm');
        // Validation et envoi (à implémenter)
        alert('Utilisateur ajouté (fonctionnalité à implémenter)');
    }

    // Recherche en temps réel
    document.getElementById('searchUser').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        // Filtrer le tableau (à implémenter avec fetch)
    });

    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function(e) {
        document.querySelectorAll('.user-checkbox').forEach(cb => {
            cb.checked = e.target.checked;
        });
    });
    </script>
</body>
</html>