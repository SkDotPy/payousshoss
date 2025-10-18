<?php
session_start();
require_once __DIR__ . '/../../config.php';

// Vérifications de sécurité
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'refuge') {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /refuge/manage_animals.php');
    exit;
}

$uid = (int)$_SESSION['user_id'];
$is_super = (int)($_SESSION['is_super_admin'] ?? 0);
$animal_id = (int)($_POST['animal_id'] ?? 0);

if ($animal_id <= 0) {
    header('Location: /refuge/manage_animals.php?error=Animal invalide');
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
        header('Location: /refuge/manage_animals.php?error=Animal introuvable');
        exit;
    }

    // Vérifier que l'animal appartient au refuge (ou super admin)
    if ($animal['refuge_id'] != $uid && $is_super !== 1) {
        header('Location: /refuge/manage_animals.php?error=Accès refusé');
        exit;
    }

    // Supprimer les photos physiques
    $upload_dir = __DIR__ . '/../../uploads/animals/' . $animal_id;
    if (is_dir($upload_dir)) {
        // Supprimer tous les fichiers du dossier
        $files = glob($upload_dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        // Supprimer le dossier
        rmdir($upload_dir);
    }

    // Supprimer l'animal de la base de données
    $stmt = $pdo->prepare("DELETE FROM animals WHERE id = ?");
    $stmt->execute([$animal_id]);

    header('Location: /refuge/manage_animals.php?success=deleted');
    exit;

} catch (PDOException $e) {
    error_log("Erreur suppression animal: " . $e->getMessage());
    header('Location: /refuge/manage_animals.php?error=' . urlencode('Erreur lors de la suppression'));
    exit;
}
