<?php
session_start();
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');

// Vérifications de sécurité
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'refuge') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

$uid = (int)$_SESSION['user_id'];
$is_super = (int)($_SESSION['is_super_admin'] ?? 0);

// Récupérer les données JSON
$input = json_decode(file_get_contents('php://input'), true);
$animal_id = (int)($input['animal_id'] ?? 0);
$photo_num = (int)($input['photo_num'] ?? 0);

if ($animal_id <= 0 || $photo_num < 1 || $photo_num > 3) {
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
}

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
// Récupérer l'animal pour vérifier les permissions
    $stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
    $stmt->execute([$animal_id]);
    $animal = $stmt->fetch();

    if (!$animal) {
        echo json_encode(['success' => false, 'error' => 'Animal introuvable']);
        exit;
    }

    // Vérifier que l'animal appartient au refuge (ou super admin)
    if ($animal['refuge_id'] != $uid && $is_super !== 1) {
        echo json_encode(['success' => false, 'error' => 'Accès refusé']);
        exit;
    }

    $photo_field = "photo{$photo_num}";
    $photo_path = $animal[$photo_field];

    if (empty($photo_path)) {
        echo json_encode(['success' => false, 'error' => 'Aucune photo à supprimer']);
        exit;
    }

    // Supprimer le fichier physique
    $file_path = __DIR__ . '/../..' . $photo_path;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // Mettre à jour la base de données
    $stmt = $pdo->prepare("UPDATE animals SET {$photo_field} = NULL WHERE id = ?");
    $stmt->execute([$animal_id]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    error_log("Erreur suppression photo: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Erreur serveur']);
}
