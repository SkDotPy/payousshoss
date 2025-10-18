<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechercher un animal - Paw Connect</title>

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
        .search-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0 40px;
        }

        .search-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .search-filters {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .search-filters .form-label {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 8px;
        }

        #loadingIndicator {
            display: block;
        }

        #resultsGrid, #noResults {
            display: none;
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
                        <a class="nav-link active" href="search.php">Adopter</a>
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
    <section class="search-hero">
        <div class="container">
            <h1><i class="fas fa-search me-2"></i> Trouvez votre compagnon</h1>
            <p class="lead">Recherchez parmi nos animaux disponibles à l'adoption</p>
        </div>
    </section>

    <!-- SECTION RECHERCHE -->
    <section class="py-5">
        <div class="container">
            <!-- FILTRES -->
            <div class="search-filters">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Recherche par nom, race ou description</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Ex: Labrador, Max, chat roux...">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">La recherche se met à jour automatiquement pendant la saisie</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Type d'animal</label>
                        <select class="form-select form-select-lg" id="filterSpecies">
                            <option value="">Tous les animaux</option>
                            <option value="chien">🐕 Chiens</option>
                            <option value="chat">🐈 Chats</option>
                            <option value="lapin">🐇 Lapins</option>
                            <option value="autre">🦜 Autres</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Sexe</label>
                        <select class="form-select" id="filterSex">
                            <option value="">Tous</option>
                            <option value="male">Mâle</option>
                            <option value="femelle">Femelle</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Âge</label>
                        <select class="form-select" id="filterAge">
                            <option value="">Tous les âges</option>
                            <option value="0-1">Moins d'1 an</option>
                            <option value="1-3">1 à 3 ans</option>
                            <option value="3-7">3 à 7 ans</option>
                            <option value="7+">Plus de 7 ans</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Race</label>
                        <input type="text" class="form-control" id="filterRace" placeholder="Ex: Labrador">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Couleur</label>
                        <input type="text" class="form-control" id="filterColor" placeholder="Ex: Roux, Noir">
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-secondary" id="resetFilters">
                            <i class="fas fa-redo me-2"></i> Réinitialiser les filtres
                        </button>
                    </div>
                </div>
            </div>

            <!-- RÉSULTATS -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 id="resultsCount">Chargement...</h4>
                        <div>
                            <label class="me-2">Trier par :</label>
                            <select class="form-select d-inline-block" style="width: auto;" id="sortBy">
                                <option value="recent">Plus récent</option>
                                <option value="name">Nom (A-Z)</option>
                                <option value="age">Âge croissant</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ZONE D'AFFICHAGE DES RÉSULTATS -->
            <div id="loadingIndicator" class="text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-3 text-muted">Recherche en cours...</p>
            </div>

            <div id="resultsGrid" class="row g-4">
                <!-- Les cartes animaux seront injectées ici dynamiquement -->
            </div>

            <div id="noResults" class="text-center py-5">
                <i class="fas fa-search fa-4x text-muted mb-3"></i>
                <h4>Aucun résultat trouvé</h4>
                <p class="text-muted">Essayez de modifier vos critères de recherche</p>
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
    // Système de recherche avec API Fetch
    let searchTimeout = null;
    const searchInput = document.getElementById('searchInput');
    const resultsGrid = document.getElementById('resultsGrid');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const noResults = document.getElementById('noResults');
    const resultsCount = document.getElementById('resultsCount');

    // Fonction de recherche principale
    async function performSearch() {
        const query = searchInput.value.trim();
        const species = document.getElementById('filterSpecies').value;
        const sex = document.getElementById('filterSex').value;
        const age = document.getElementById('filterAge').value;
        const race = document.getElementById('filterRace').value;
        const color = document.getElementById('filterColor').value;
        const sort = document.getElementById('sortBy').value;

        // Construire l'URL avec les paramètres
        const params = new URLSearchParams({
            q: query,
            species: species,
            sex: sex,
            age: age,
            race: race,
            color: color,
            sort: sort
        });

        // Afficher le loader
        loadingIndicator.style.display = 'block';
        resultsGrid.style.display = 'none';
        noResults.style.display = 'none';

        try {
            const response = await fetch(`backend/search/animals.php?${params.toString()}`);
            const data = await response.json();

            loadingIndicator.style.display = 'none';

            if(data.success && data.animals.length > 0) {
                displayResults(data.animals);
                resultsCount.textContent = `${data.animals.length} résultat(s) trouvé(s)`;
            } else {
                noResults.style.display = 'block';
                resultsCount.textContent = 'Aucun résultat';
            }
        } catch(error) {
            console.error('Erreur de recherche:', error);
            loadingIndicator.style.display = 'none';
            noResults.style.display = 'block';
            resultsCount.textContent = 'Erreur de chargement';
        }
    }

    // Afficher les résultats
    function displayResults(animals) {
        resultsGrid.innerHTML = '';
        resultsGrid.style.display = 'flex';

        animals.forEach(animal => {
            const card = createAnimalCard(animal);
            resultsGrid.appendChild(card);
        });
    }

    // Créer une carte animal
    function createAnimalCard(animal) {
        const col = document.createElement('div');
        col.className = 'col-md-4';

        const speciesIcon = {
            'chien': 'fa-dog',
            'chat': 'fa-cat',
            'lapin': 'fa-rabbit',
            'autre': 'fa-paw'
        };

        const icon = speciesIcon[animal.species] || 'fa-paw';

        col.innerHTML = `
            <div class="card card-animal h-100">
                <img src="${animal.image || 'assets/images/animals/placeholder.jpg'}" alt="${animal.nom}" class="card-img-top">
                <span class="badge-available">${animal.state || 'Disponible'}</span>
                <div class="card-body">
                    <h5 class="card-title">${animal.nom}</h5>
                    <p class="text-muted mb-2">
                        <i class="fas ${icon}"></i> ${animal.race} • ${animal.age} ans • ${animal.sex === 'male' ? 'Mâle' : 'Femelle'}
                    </p>
                    <p class="card-text">${animal.description || 'Adorable compagnon à adopter'}</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-muted"><i class="fas fa-map-marker-alt"></i> ${animal.location || 'Non spécifié'}</span>
                        <a href="animal-detail.php?id=${animal.id}" class="btn btn-primary btn-sm">Voir plus</a>
                    </div>
                </div>
            </div>
        `;

        return col;
    }

    // Recherche avec debounce (attendre que l'utilisateur finisse de taper)
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });

    // Filtres en temps réel
    document.getElementById('filterSpecies').addEventListener('change', performSearch);
    document.getElementById('filterSex').addEventListener('change', performSearch);
    document.getElementById('filterAge').addEventListener('change', performSearch);
    document.getElementById('filterRace').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });
    document.getElementById('filterColor').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });
    document.getElementById('sortBy').addEventListener('change', performSearch);

    // Clear search
    document.getElementById('clearSearch').addEventListener('click', function() {
        searchInput.value = '';
        performSearch();
    });

    // Reset tous les filtres
    document.getElementById('resetFilters').addEventListener('click', function() {
        searchInput.value = '';
        document.getElementById('filterSpecies').value = '';
        document.getElementById('filterSex').value = '';
        document.getElementById('filterAge').value = '';
        document.getElementById('filterRace').value = '';
        document.getElementById('filterColor').value = '';
        document.getElementById('sortBy').value = 'recent';
        performSearch();
    });

    // Lancer la recherche initiale au chargement de la page
    window.addEventListener('DOMContentLoaded', performSearch);
    </script>
</body>
</html>
