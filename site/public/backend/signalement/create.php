<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../utils/mailer.php';

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données du formulaire
$type_signalement = $_POST['type_signalement'] ?? '';
$espece = $_POST['espece'] ?? '';
$race = $_POST['race'] ?? null;
$sexe = $_POST['sexe'] ?? '';
$age_estime = $_POST['age_estime'] ?? null;
$description = $_POST['description'] ?? '';
$lieu = $_POST['lieu'] ?? '';
$date_signalement = $_POST['date_signalement'] ?? '';
$nom = $_POST['nom'] ?? '';
$email = $_POST['email'] ?? '';
$telephone = $_POST['telephone'] ?? '';
$commentaire = $_POST['commentaire'] ?? null;

// Validation des champs obligatoires
if (empty($type_signalement) || empty($espece) || empty($description) || 
    empty($lieu) || empty($date_signalement) || empty($nom) || 
    empty($email) || empty($telephone)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Tous les champs obligatoires doivent être remplis']);
    exit;
}

// Validation de l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Email invalide']);
    exit;
}

try {
    // Connexion à la base de données
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Gérer l'upload des photos
    $uploadedPhotos = [];
    $uploadDir = __DIR__ . '/../../uploads/signalements/';
    
    // Créer le dossier s'il n'existe pas
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!empty($_FILES['photos']['name'][0])) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        foreach ($_FILES['photos']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                $originalName = $_FILES['photos']['name'][$key];
                $fileSize = $_FILES['photos']['size'][$key];
                $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                // Vérifier l'extension
                if (!in_array($fileExtension, $allowedExtensions)) {
                    continue;
                }

                // Vérifier la taille
                if ($fileSize > $maxFileSize) {
                    continue;
                }

                // Générer un nom unique
                $newFileName = uniqid('signalement_', true) . '.' . $fileExtension;
                $destination = $uploadDir . $newFileName;

                // Déplacer le fichier
                if (move_uploaded_file($tmpName, $destination)) {
                    $uploadedPhotos[] = 'uploads/signalements/' . $newFileName;
                }
            }
        }
    }

    // Convertir les photos en JSON
    $photosJson = !empty($uploadedPhotos) ? json_encode($uploadedPhotos) : null;

    // Insérer dans la base de données
    $stmt = $pdo->prepare("
        INSERT INTO signalements (
            type_signalement, espece, race, sexe, age_estime, 
            description, lieu, date_signalement, nom_declarant, 
            email_declarant, telephone_declarant, commentaire, photos
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $type_signalement, $espece, $race, $sexe, $age_estime,
        $description, $lieu, $date_signalement, $nom,
        $email, $telephone, $commentaire, $photosJson
    ]);

    $signalementId = $pdo->lastInsertId();

    // Préparer l'email
    $typeLabel = ($type_signalement === 'trouve') ? 'Animal trouvé' : 'Remise à un refuge';
    $sexeLabel = $sexe === 'male' ? 'Mâle' : ($sexe === 'femelle' ? 'Femelle' : 'Non identifié');

    $emailSubject = "[Paw Connect] Nouveau signalement : $typeLabel - $espece";
    
    $emailBody = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 10px 10px; }
            .info-block { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #667eea; }
            .info-label { font-weight: bold; color: #667eea; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>🐾 Nouveau signalement reçu</h2>
            </div>
            <div class='content'>
                <div class='info-block'>
                    <p><span class='info-label'>Type de signalement :</span> $typeLabel</p>
                </div>
                
                <h3>📋 Informations sur l'animal</h3>
                <div class='info-block'>
                    <p><span class='info-label'>Espèce :</span> " . ucfirst($espece) . "</p>
                    <p><span class='info-label'>Race :</span> " . ($race ?: 'Non spécifiée') . "</p>
                    <p><span class='info-label'>Sexe :</span> $sexeLabel</p>
                    <p><span class='info-label'>Âge estimé :</span> " . ($age_estime ?: 'Non spécifié') . "</p>
                    <p><span class='info-label'>Description :</span><br>" . nl2br(htmlspecialchars($description)) . "</p>
                </div>
                
                <h3>📍 Localisation</h3>
                <div class='info-block'>
                    <p><span class='info-label'>Lieu :</span> " . htmlspecialchars($lieu) . "</p>
                    <p><span class='info-label'>Date :</span> " . date('d/m/Y', strtotime($date_signalement)) . "</p>
                </div>
                
                <h3>👤 Coordonnées du déclarant</h3>
                <div class='info-block'>
                    <p><span class='info-label'>Nom :</span> " . htmlspecialchars($nom) . "</p>
                    <p><span class='info-label'>Email :</span> <a href='mailto:$email'>$email</a></p>
                    <p><span class='info-label'>Téléphone :</span> " . htmlspecialchars($telephone) . "</p>
                </div>
                " . ($commentaire ? "
                <h3>💬 Commentaire</h3>
                <div class='info-block'>
                    <p>" . nl2br(htmlspecialchars($commentaire)) . "</p>
                </div>
                " : "") . "
                " . (!empty($uploadedPhotos) ? "
                <h3>📷 Photos jointes</h3>
                <div class='info-block'>
                    <p>" . count($uploadedPhotos) . " photo(s) uploadée(s)</p>
                    <p>Consultez-les sur le serveur ou dans le panneau admin.</p>
                </div>
                " : "") . "
                
                <div class='footer'>
                    <p>Signalement #$signalementId enregistré le " . date('d/m/Y à H:i') . "</p>
                    <p><a href='" . APP_URL . "'>Paw Connect</a> - Agir ensemble, les protéger</p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";

    // Envoyer l'email
    $emailSent = sendEmail('contact@paw-connect.org', $emailSubject, $emailBody);

    if (!$emailSent) {
        error_log("Erreur lors de l'envoi de l'email pour le signalement #$signalementId");
    }

    // Réponse succès
    echo json_encode([
        'ok' => true,
        'message' => 'Signalement enregistré avec succès',
        'signalement_id' => $signalementId
    ]);

} catch (PDOException $e) {
    error_log('SIGNALEMENT DB ERROR: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erreur lors de l\'enregistrement']);
} catch (Throwable $e) {
    error_log('SIGNALEMENT ERROR: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erreur serveur']);
}
