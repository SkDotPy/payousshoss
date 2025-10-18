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
$errors = [];

if ($animal_id <= 0) {
    header('Location: /refuge/manage_animals.php?error=Animal invalide');
    exit;
}

// Validation des champs obligatoires
$name = trim($_POST['name'] ?? '');
$species = trim($_POST['species'] ?? '');
$age = trim($_POST['age'] ?? '');
$sex = $_POST['sex'] ?? '';
$status = $_POST['status'] ?? 'available';

if (empty($name)) $errors[] = "Le nom est obligatoire";
if (empty($species)) $errors[] = "L'espèce est obligatoire";
if (empty($age)) $errors[] = "L'âge est obligatoire";
if (empty($sex)) $errors[] = "Le sexe est obligatoire";

if (!empty($errors)) {
    header('Location: /refuge/edit_animal.php?id=' . $animal_id . '&error=' . urlencode(implode(', ', $errors)));
    exit;
}

// Champs optionnels
$breed = trim($_POST['breed'] ?? '');
$size = $_POST['size'] ?? null;
$color = trim($_POST['color'] ?? '');
$description = trim($_POST['description'] ?? '');

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Vérifier que l'animal appartient au refuge (ou super admin)
    $stmt = $pdo->prepare("SELECT refuge_id FROM animals WHERE id = ?");
    $stmt->execute([$animal_id]);
    $animal = $stmt->fetch();

    if (!$animal) {
        header('Location: /refuge/manage_animals.php?error=Animal introuvable');
        exit;
    }

    if ($animal['refuge_id'] != $uid && $is_super !== 1) {
        header('Location: /refuge/manage_animals.php?error=Accès refusé');
        exit;
    }

    // Mise à jour des informations de base
    $stmt = $pdo->prepare("
        UPDATE animals 
        SET name = ?, species = ?, breed = ?, age = ?, sex = ?, 
            size = ?, color = ?, description = ?, status = ?, updated_at = NOW()
        WHERE id = ?
    ");

    $stmt->execute([
        $name,
        $species,
        $breed ?: null,
        $age,
        $sex,
        $size,
        $color ?: null,
        $description ?: null,
        $status,
        $animal_id
    ]);

    // Traitement des photos
    $upload_dir = __DIR__ . '/../../uploads/animals/' . $animal_id;
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $photo_updates = [];
    for ($i = 1; $i <= 3; $i++) {
        // Si une nouvelle photo est uploadée
        if (isset($_FILES["photo{$i}"]) && $_FILES["photo{$i}"]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES["photo{$i}"];
            
            // Validation du fichier
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5 Mo
            
            if (!in_array($file['type'], $allowed_types)) {
                continue;
            }
            
            if ($file['size'] > $max_size) {
                continue;
            }
            
            // Supprimer l'ancienne photo si elle existe
            $existing_photo = $_POST["existing_photo{$i}"] ?? '';
            if (!empty($existing_photo)) {
                $old_file = __DIR__ . '/../..' . $existing_photo;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            
            // Extension du fichier
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = "photo{$i}." . strtolower($extension);
            $destination = $upload_dir . '/' . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $photo_updates["photo{$i}"] = "/uploads/animals/{$animal_id}/{$new_filename}";
            }
        }
    }

    // Mise à jour des chemins des photos si nécessaire
    if (!empty($photo_updates)) {
        $set_clause = [];
        $values = [];
        
        foreach ($photo_updates as $field => $path) {
            $set_clause[] = "{$field} = ?";
            $values[] = $path;
        }
        
        $values[] = $animal_id;
        
        $stmt = $pdo->prepare("
            UPDATE animals 
            SET " . implode(', ', $set_clause) . " 
            WHERE id = ?
        ");
        $stmt->execute($values);
    }

    header('Location: /refuge/manage_animals.php?success=updated');
    exit;

} catch (PDOException $e) {
    error_log("Erreur mise à jour animal: " . $e->getMessage());
    header('Location: /refuge/edit_animal.php?id=' . $animal_id . '&error=' . urlencode('Erreur lors de la mise à jour'));
    exit;
}
