<?php
/**
 * API de création de signalement
 * Route : POST /backend/signalement/create.php
 */

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Inclure la connexion BDD
require_once '../config/database.php';

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

try {
    // Récupérer les données du formulaire
    $type = isset($_POST['type']) ? trim($_POST['type']) : '';
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
    $espece = isset($_POST['espece']) ? trim($_POST['espece']) : '';
    $race = isset($_POST['race']) ? trim($_POST['race']) : '';
    $couleur = isset($_POST['couleur']) ? trim($_POST['couleur']) : '';
    $taille = isset($_POST['taille']) ? trim($_POST['taille']) : '';
    $sexe = isset($_POST['sexe']) ? trim($_POST['sexe']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $lieu = isset($_POST['lieu']) ? trim($_POST['lieu']) : '';
    $code_postal = isset($_POST['code_postal']) ? trim($_POST['code_postal']) : '';
    $date = isset($_POST['date']) ? trim($_POST['date']) : '';
    $heure = isset($_POST['heure']) ? trim($_POST['heure']) : '';
    
    // Infos complémentaires (pour perdu)
    $puce = isset($_POST['puce']) ? 1 : 0;
    $tatouage = isset($_POST['tatouage']) ? 1 : 0;
    $collier = isset($_POST['collier']) ? 1 : 0;
    $num_puce = isset($_POST['num_puce']) ? trim($_POST['num_puce']) : '';
    
    // Validation des champs obligatoires
    if (empty($nom) || empty($email) || empty($telephone) || empty($espece) || 
        empty($couleur) || empty($description) || empty($lieu) || empty($code_postal) || empty($date)) {
        throw new Exception("Tous les champs obligatoires doivent être remplis");
    }
    
    // Validation email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Email invalide");
    }
    
    // Validation code postal
    if (!preg_match('/^[0-9]{5}$/', $code_postal)) {
        throw new Exception("Code postal invalide");
    }
    
    // Gestion de l'upload de photo
    $photo_path = null;
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../public/uploads/signalements/';
        
        // Créer le dossier s'il n'existe pas
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Vérifier le type de fichier
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['photo']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            throw new Exception("Format de fichier non autorisé. Utilisez JPG, PNG ou GIF");
        }
        
        // Vérifier la taille (5 MB max)
        if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            throw new Exception("La photo ne doit pas dépasser 5 MB");
        }
        
        // Générer un nom unique
        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = 'signal_' . time() . '_' . uniqid() . '.' . $extension;
        $photo_path = $upload_dir . $filename;
        
        // Déplacer le fichier
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            throw new Exception("Erreur lors de l'upload de la photo");
        }
        
        // Stocker le chemin relatif
        $photo_path = 'uploads/signalements/' . $filename;
    }
    
    // Connexion BDD
    $pdo = getDBConnection();
    
    // Insérer le signalement
    $sql = "INSERT INTO signalement (
        type,
        nom_declarant,
        email_declarant,
        telephone_declarant,
        espece,
        race,
        couleur,
        taille,
        sexe,
        description,
        lieu,
        code_postal,
        date_signalement,
        heure_signalement,
        photo,
        puce,
        tatouage,
        collier,
        num_puce,
        statut,
        date_creation
    ) VALUES (
        :type,
        :nom,
        :email,
        :telephone,
        :espece,
        :race,
        :couleur,
        :taille,
        :sexe,
        :description,
        :lieu,
        :code_postal,
        :date_signalement,
        :heure,
        :photo,
        :puce,
        :tatouage,
        :collier,
        :num_puce,
        'en_attente',
        NOW()
    )";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':type' => $type,
        ':nom' => $nom,
        ':email' => $email,
        ':telephone' => $telephone,
        ':espece' => $espece,
        ':race' => $race,
        ':couleur' => $couleur,
        ':taille' => $taille,
        ':sexe' => $sexe,
        ':description' => $description,
        ':lieu' => $lieu,
        ':code_postal' => $code_postal,
        ':date_signalement' => $date,
        ':heure' => $heure,
        ':photo' => $photo_path,
        ':puce' => $puce,
        ':tatouage' => $tatouage,
        ':collier' => $collier,
        ':num_puce' => $num_puce
    ]);
    
    $signalement_id = $pdo->lastInsertId();
    
    // Logger l'action
    logActivity($pdo, 'signalement_create', [
        'signalement_id' => $signalement_id,
        'type' => $type,
        'espece' => $espece,
        'lieu' => $lieu
    ]);
    
    // Envoyer email de confirmation (optionnel)
    sendConfirmationEmail($email, $nom, $type, $signalement_id);
    
    // Réponse succès
    echo json_encode([
        'success' => true,
        'message' => 'Votre signalement a été enregistré avec succès ! Vous recevrez un email de confirmation.',
        'signalement_id' => $signalement_id
    ], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    error_log("Erreur SQL signalement: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'enregistrement du signalement'
    ], JSON_UNESCAPED_UNICODE);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Logger l'activité
 */
function logActivity($pdo, $action, $details) {
    try {
        $sql = "INSERT INTO historique (idUser, changIn, temps, type) 
                VALUES (:user_id, :details, NOW(), :action)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
            ':details' => json_encode($details),
            ':action' => $action
        ]);
    } catch(PDOException $e) {
        error_log("Erreur log activité: " . $e->getMessage());
    }
}

/**
 * Envoyer email de confirmation
 */
function sendConfirmationEmail($email, $nom, $type, $signalement_id) {
    // Cette fonction nécessite PHPMailer ou la fonction mail()
    // Exemple basique avec mail() :
    
    $subject = "Confirmation de votre signalement - Paw Connect";
    $type_text = ($type === 'perdu') ? 'perdu' : 'trouvé';
    
    $message = "
    Bonjour $nom,
    
    Nous avons bien reçu votre signalement d'animal $type_text.
    
    Numéro de signalement : #$signalement_id
    
    Nous vous contacterons rapidement si nous avons des informations.
    
    Cordialement,
    L'équipe Paw Connect
    ";
    
    $headers = "From: noreply@pawconnect.fr\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Décommenter pour activer l'envoi d'email
    // mail($email, $subject, $message, $headers);
}

/**
 * Nettoyer les données
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
?>