<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Récupérer les paramètres de recherche
    $query = trim($_GET['q'] ?? '');
    $species = trim($_GET['species'] ?? '');
    $sex = trim($_GET['sex'] ?? '');
    $age = trim($_GET['age'] ?? '');
    $race = trim($_GET['race'] ?? '');
    $color = trim($_GET['color'] ?? '');
    $sort = $_GET['sort'] ?? 'recent';

    // Construire la requête SQL
    $sql = "
        SELECT 
            a.id,
            a.name as nom,
            a.species,
            a.breed as race,
            a.age,
            a.sex,
            a.size,
            a.color,
            a.description,
            a.status as state,
            a.photo1 as image,
            a.created_at,
            u.nom as refuge_nom,
            u.email as refuge_email
        FROM animals a
        LEFT JOIN users u ON a.refuge_id = u.id
        WHERE a.status = 'available'
    ";
    
    $params = [];

    // Filtre par recherche textuelle (nom, race, description)
    if (!empty($query)) {
        $sql .= " AND (
            a.name LIKE ? OR 
            a.breed LIKE ? OR 
            a.description LIKE ? OR
            a.species LIKE ?
        )";
        $searchTerm = "%{$query}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    // Filtre par espèce
    if (!empty($species)) {
        $sql .= " AND LOWER(a.species) = LOWER(?)";
        $params[] = $species;
    }

    // Filtre par sexe
    if (!empty($sex)) {
        if ($sex === 'male') {
            $sql .= " AND a.sex = 'male'";
        } elseif ($sex === 'femelle') {
            $sql .= " AND a.sex = 'female'";
        }
    }

    // Filtre par race
    if (!empty($race)) {
        $sql .= " AND a.breed LIKE ?";
        $params[] = "%{$race}%";
    }

    // Filtre par couleur
    if (!empty($color)) {
        $sql .= " AND a.color LIKE ?";
        $params[] = "%{$color}%";
    }

    // Tri
    switch ($sort) {
        case 'name':
            $sql .= " ORDER BY a.name ASC";
            break;
        case 'age':
            $sql .= " ORDER BY a.created_at ASC";
            break;
        case 'recent':
        default:
            $sql .= " ORDER BY a.created_at DESC";
            break;
    }

    // Exécuter la requête
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $animals = $stmt->fetchAll();

    // Formater les résultats
    $formattedAnimals = array_map(function($animal) {
        // Convertir le sexe
        $sexLabel = 'Inconnu';
        if ($animal['sex'] === 'male') {
            $sexLabel = 'Mâle';
        } elseif ($animal['sex'] === 'female') {
            $sexLabel = 'Femelle';
        }
        
        return [
            'id' => (int)$animal['id'], // IMPORTANT : l'ID réel
            'nom' => $animal['nom'],
            'species' => strtolower($animal['species']),
            'race' => $animal['race'] ?? 'Non spécifié',
            'age' => $animal['age'], // Garder tel quel (contient déjà "X ans")
            'sex' => $sexLabel,
            'size' => $animal['size'],
            'color' => $animal['color'],
            'description' => $animal['description'] ?? 'Adorable compagnon à adopter',
            'state' => $animal['state'] === 'available' ? 'Disponible' : 'Non disponible',
            'image' => $animal['image'] ?? 'assets/images/animals/placeholder.jpg',
            'location' => $animal['refuge_nom'] ?? 'Non spécifié',
            'refuge_email' => $animal['refuge_email'] ?? null,
            'created_at' => $animal['created_at']
        ];
    }, $animals);

    echo json_encode([
        'success' => true,
        'animals' => $formattedAnimals,
        'count' => count($formattedAnimals)
    ]);

} catch (PDOException $e) {
    error_log("Erreur recherche animaux: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la recherche',
        'animals' => []
    ]);
}
