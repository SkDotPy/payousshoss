<?php
session_start();
require_once __DIR__ . '/../../config.php';

// Vérifications de sécurité
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'refuge') {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /refuge/add_animal.php');
    exit;
}

$uid = (int)$_SESSION['user_id'];
$errors = [];

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
    header('Location: /refuge/add_animal.php?error=' . urlencode(implode(', ', $errors)));
    exit;
}

// Champs optionnels
$breed = trim($_POST['breed'] ?? '');
$size = $_POST['size'] ?? null;
$color = trim($_POST['color'] ?? '');
$description = trim($_POST['description'] ?? '');

// Champs de santé
$vaccinated = isset($_POST['vaccinated']) ? 1 : 0;
$sterilized = isset($_POST['sterilized']) ? 1 : 0;
$good_with_kids = isset($_POST['good_with_kids']) ? 1 : 0;
$good_with_animals = isset($_POST['good_with_animals']) ? 1 : 0;

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Insertion de l'animal
    $stmt = $pdo->prepare("
        INSERT INTO animals (
            name, species, breed, age, sex, size, color, description, 
            status, created_by, refuge_id, created_at,
            vaccinated, sterilized, good_with_kids, good_with_animals
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)
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
        $uid,
        $uid,
        $vaccinated,
        $sterilized,
        $good_with_kids,
        $good_with_animals
    ]);

    $animal_id = $pdo->lastInsertId();

    // Traitement des photos
    $upload_dir = __DIR__ . '/../../uploads/animals/' . $animal_id;
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $photo_fields = [];
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_FILES["photo{$i}"]) && $_FILES["photo{$i}"]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES["photo{$i}"];
            
            // Validation du fichier
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $max_size = 5 * 1024 * 1024; // 5 Mo
            
            if (!in_array($file['type'], $allowed_types)) {
                continue;
            }
            
            if ($file['size'] > $max_size) {
                continue;
            }
            
            // Extension du fichier
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = "photo{$i}." . strtolower($extension);
            $destination = $upload_dir . '/' . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $photo_fields["photo{$i}"] = "/uploads/animals/{$animal_id}/{$new_filename}";
            }
        }
    }

    // Mise à jour des chemins des photos
    if (!empty($photo_fields)) {
        $set_clause = [];
        $values = [];
        
        foreach ($photo_fields as $field => $path) {
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

    header('Location: /refuge/manage_animals.php?success=added');
    exit;

} catch (PDOException $e) {
    error_log("Erreur ajout animal: " . $e->getMessage());
    header('Location: /refuge/add_animal.php?error=' . urlencode('Erreur lors de l\'ajout: ' . $e->getMessage()));
    exit;
}
