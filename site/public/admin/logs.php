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
    <title>Logs Système - Paw Connect Admin</title>
    
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
                <a href="index.php" class="admin-nav-item">
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
                <a href="logs.php" class="admin-nav-item active">
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
                    <h1><i class="fas fa-list me-2"></i> Logs Système</h1>
                    <p class="text-muted mb-0">Historique des activités et événements</p>
                </div>
                <button class="dark-mode-toggle" id="darkModeToggle">
                    <i class="fas fa-moon"></i>
                </button>
            </div>

            <!-- FILTRES -->
            <div class="data-table mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Type d'événement</label>
                        <select class="form-select" id="filterType">
                            <option value="">Tous les types</option>
                            <option value="connexion">Connexions</option>
                            <option value="changement">Modifications</option>
                            <option value="erreur">Erreurs</option>
                            <option value="adoption">Adoptions</option>
                            <option value="inscription">Inscriptions</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date de début</label>
                        <input type="date" class="form-control" id="dateStart">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date de fin</label>
                        <input type="date" class="form-control" id="dateEnd">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Recherche</label>
                        <input type="text" class="form-control" id="searchLogs" placeholder="ID utilisateur, IP...">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button class="btn btn-primary" onclick="applyFilters()">
                            <i class="fas fa-filter me-2"></i> Filtrer
                        </button>
                        <button class="btn btn-outline-secondary" onclick="resetFilters()">
                            <i class="fas fa-redo me-2"></i> Réinitialiser
                        </button>
                        <button class="btn btn-outline-success" onclick="exportLogs()">
                            <i class="fas fa-download me-2"></i> Exporter
                        </button>
                    </div>
                </div>
            </div>

            <!-- STATISTIQUES RAPIDES -->
            <div class="stats-grid mb-4">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="totalConnexions">247</h3>
                        <p>Connexions aujourd'hui</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon red">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="totalErreurs">12</h3>
                        <p>Erreurs</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="totalModifications">89</h3>
                        <p>Modifications</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon yellow">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="totalLogs">15,429</h3>
                        <p>Logs totaux</p>
                    </div>
                </div>
            </div>

            <!-- TABLEAU DES LOGS -->
            <div class="data-table">
                <div class="table-header">
                    <h4 class="mb-0">Historique des logs</h4>
                    <div>
                        <button class="btn btn-sm btn-outline-danger" onclick="clearOldLogs()" title="Nettoyer les logs de +30 jours">
                            <i class="fas fa-trash-alt me-2"></i> Nettoyer
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date & Heure</th>
                                <th>Type</th>
                                <th>Utilisateur</th>
                                <th>Action</th>
                                <th>IP</th>
                                <th>Détails</th>
                            </tr>
                        </thead>
                        <tbody id="logsTableBody">
                            <!-- Exemples de logs -->
                            <tr>
                                <td>#15429</td>
                                <td>09/10/2025 14:32:15</td>
                                <td><span class="badge badge-success">Connexion</span></td>
                                <td>jean.dupont@example.com</td>
                                <td>Connexion réussie</td>
                                <td>192.168.1.1</td>
                                <td>
                                    <button class="action-btn" onclick="viewLogDetails(15429)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td>#15428</td>
                                <td>09/10/2025 14:28:03</td>
                                <td><span class="badge badge-primary">Modification</span></td>
                                <td>admin@pawconnect.fr</td>
                                <td>Mise à jour utilisateur #142</td>
                                <td>192.168.1.10</td>
                                <td>
                                    <button class="action-btn" onclick="viewLogDetails(15428)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td>#15427</td>
                                <td>09/10/2025 14:15:22</td>
                                <td><span class="badge badge-danger">Erreur</span></td>
                                <td>System</td>
                                <td>Erreur connexion BDD</td>
                                <td>Serveur</td>
                                <td>
                                    <button class="action-btn" onclick="viewLogDetails(15427)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td>#15426</td>
                                <td>09/10/2025 14:10:45</td>
                                <td><span class="badge" style="background:#D1FAE5;color:#065F46">Inscription</span></td>
                                <td>marie.martin@example.com</td>
                                <td>Nouvel utilisateur créé</td>
                                <td>192.168.1.25</td>
                                <td>
                                    <button class="action-btn" onclick="viewLogDetails(15426)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td>#15425</td>
                                <td>09/10/2025 13:58:12</td>
                                <td><span class="badge" style="background:#FEF3C7;color:#92400E">Adoption</span></td>
                                <td>pierre.durant@example.com</td>
                                <td>Demande adoption animal #45</td>
                                <td>192.168.1.50</td>
                                <td>
                                    <button class="action-btn" onclick="viewLogDetails(15425)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td>#15424</td>
                                <td>09/10/2025 13:45:30</td>
                                <td><span class="badge badge-warning">Tentative</span></td>
                                <td>Inconnu</td>
                                <td>Tentative connexion échouée</td>
                                <td>45.12.78.92</td>
                                <td>
                                    <button class="action-btn" onclick="viewLogDetails(15424)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Affichage de 1 à 6 sur 15,429 logs
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
                            <li class="page-item"><a class="page-link" href="#">2572</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- GRAPHIQUE D'ACTIVITÉ -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="chart-container">
                        <h4 class="mb-4">Activité des 7 derniers jours</h4>
                        <div id="activityChart" style="height: 300px; display: flex; align-items: center; justify-content: center; background: #F3F4F6; border-radius: 0.5rem;">
                            <p class="text-muted">Graphique de l'activité (à implémenter avec Chart.js)</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL DÉTAILS LOG -->
    <div class="modal fade" id="logDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i> Détails du log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="logDetailsContent">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>ID Log :</strong> #15429
                            </div>
                            <div class="col-md-6">
                                <strong>Date :</strong> 09/10/2025 14:32:15
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Type :</strong> <span class="badge badge-success">Connexion</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Utilisateur :</strong> jean.dupont@example.com
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Adresse IP :</strong> 192.168.1.1
                            </div>
                            <div class="col-md-6">
                                <strong>User Agent :</strong> Mozilla/5.0...
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <strong>Action :</strong>
                            <p class="mt-2">Connexion réussie depuis l'adresse IP 192.168.1.1</p>
                        </div>
                        <div class="mb-3">
                            <strong>Données additionnelles :</strong>
                            <pre class="bg-light p-3 rounded mt-2"><code>{
  "success": true,
  "user_id": 142,
  "session_id": "abc123xyz",
  "method": "POST"
}</code></pre>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
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

    // Appliquer les filtres
    async function applyFilters() {
        const type = document.getElementById('filterType').value;
        const dateStart = document.getElementById('dateStart').value;
        const dateEnd = document.getElementById('dateEnd').value;
        const search = document.getElementById('searchLogs').value;
        
        const params = new URLSearchParams({
            type: type,
            date_start: dateStart,
            date_end: dateEnd,
            search: search
        });
        
        try {
            const response = await fetch(`../backend/admin/get-logs.php?${params.toString()}`);
            const data = await response.json();
            
            if(data.success) {
                updateLogsTable(data.logs);
                PawConnect.showToast('Logs filtrés avec succès', 'success');
            }
        } catch(error) {
            console.error('Erreur filtres:', error);
            PawConnect.showToast('Erreur lors du filtrage', 'error');
        }
    }

    // Réinitialiser les filtres
    function resetFilters() {
        document.getElementById('filterType').value = '';
        document.getElementById('dateStart').value = '';
        document.getElementById('dateEnd').value = '';
        document.getElementById('searchLogs').value = '';
        applyFilters();
    }

    // Mettre à jour le tableau des logs
    function updateLogsTable(logs) {
        const tbody = document.getElementById('logsTableBody');
        
        if(!logs || logs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Aucun log trouvé</td></tr>';
            return;
        }
        
        tbody.innerHTML = logs.map(log => {
            const badgeColors = {
                'connexion': 'badge-success',
                'modification': 'badge-primary',
                'erreur': 'badge-danger',
                'inscription': 'badge-info',
                'adoption': 'badge-warning'
            };
            
            return `
                <tr>
                    <td>#${log.id}</td>
                    <td>${log.date}</td>
                    <td><span class="badge ${badgeColors[log.type] || 'badge-secondary'}">${log.type}</span></td>
                    <td>${log.user || 'System'}</td>
                    <td>${log.action}</td>
                    <td>${log.ip}</td>
                    <td>
                        <button class="action-btn" onclick="viewLogDetails(${log.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Voir les détails d'un log
    async function viewLogDetails(logId) {
        try {
            const response = await fetch(`../backend/admin/get-log-detail.php?id=${logId}`);
            const data = await response.json();
            
            if(data.success) {
                // Remplir le modal avec les détails
                const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
                modal.show();
            }
        } catch(error) {
            console.error('Erreur détails log:', error);
            PawConnect.showToast('Erreur lors du chargement des détails', 'error');
        }
    }

    // Exporter les logs
    async function exportLogs() {
        const type = document.getElementById('filterType').value;
        const dateStart = document.getElementById('dateStart').value;
        const dateEnd = document.getElementById('dateEnd').value;
        
        const params = new URLSearchParams({
            type: type,
            date_start: dateStart,
            date_end: dateEnd,
            format: 'csv'
        });
        
        window.location.href = `../backend/admin/export-logs.php?${params.toString()}`;
        PawConnect.showToast('Export des logs en cours...', 'info');
    }

    // Nettoyer les anciens logs
    async function clearOldLogs() {
        if(!confirm('Êtes-vous sûr de vouloir supprimer tous les logs de plus de 30 jours ?')) {
            return;
        }
        
        try {
            const response = await fetch('../backend/admin/clear-old-logs.php', {
                method: 'POST'
            });
            
            const data = await response.json();
            
            if(data.success) {
                PawConnect.showToast(`${data.deleted} logs supprimés`, 'success');
                applyFilters();
            } else {
                PawConnect.showToast(data.message || 'Erreur lors du nettoyage', 'error');
            }
        } catch(error) {
            console.error('Erreur nettoyage:', error);
            PawConnect.showToast('Erreur lors du nettoyage', 'error');
        }
    }

    // Charger les statistiques
    async function loadStats() {
        try {
            const response = await fetch('../backend/admin/get-log-stats.php');
            const data = await response.json();
            
            if(data.success) {
                document.getElementById('totalConnexions').textContent = data.stats.connexions;
                document.getElementById('totalErreurs').textContent = data.stats.erreurs;
                document.getElementById('totalModifications').textContent = data.stats.modifications;
                document.getElementById('totalLogs').textContent = PawConnect.formatNumber(data.stats.total);
            }
        } catch(error) {
            console.error('Erreur stats:', error);
        }
    }

    // Auto-refresh toutes les 30 secondes
    setInterval(loadStats, 30000);

    // Charger les stats au démarrage
    window.addEventListener('DOMContentLoaded', loadStats);

    // Recherche en temps réel
    document.getElementById('searchLogs').addEventListener('input', PawConnect.debounce(function() {
        applyFilters();
    }, 500));

    // Définir les dates par défaut (aujourd'hui)
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('dateEnd').value = today;
        
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);
        document.getElementById('dateStart').value = weekAgo.toISOString().split('T')[0];
    });
    </script>
</body>
</html>