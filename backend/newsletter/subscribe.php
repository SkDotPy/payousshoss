<?php
/**
 * Inscription à la newsletter
 * POST /backend/newsletter/subscribe.php
 */

header('Content-Type: application/json');
require_once '../config/database.php';

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Méthode non autorisée', null, 405);
}

$email = cleanInput($_POST['email'] ?? '');
$nom = cleanInput($_POST['nom'] ?? '');

// Validation
if (empty($email)) {
    sendJsonResponse(false, 'Email requis', null, 400);
}

if (!isValidEmail($email)) {
    sendJsonResponse(false, 'Email invalide', null, 400);
}

try {
    $pdo = getDbConnection();
    
    // Vérifier si l'email existe déjà
    $checkSql = "SELECT id, actif FROM newsletter WHERE email = :email";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([':email' => $email]);
    $existing = $checkStmt->fetch();
    
    if ($existing) {
        if ($existing['actif'] == 1) {
            sendJsonResponse(false, 'Cet email est déjà inscrit à la newsletter', null, 409);
        } else {
            // Réactiver l'abonnement
            $updateSql = "UPDATE newsletter SET actif = 1, date_inscription = NOW() WHERE id = :id";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([':id' => $existing['id']]);
            
            sendJsonResponse(true, 'Réinscription réussie ! Vous recevrez à nouveau nos newsletters');
        }
    } else {
        // Nouvelle inscription
        $insertSql = "INSERT INTO newsletter (email, nom, actif, date_inscription) 
                      VALUES (:email, :nom, 1, NOW())";
        
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([
            ':email' => $email,
            ':nom' => $nom
        ]);
        
        sendJsonResponse(true, 'Inscription réussie ! Vous recevrez nos prochaines newsletters');
    }
    
} catch(PDOException $e) {
    error_log("Erreur newsletter subscribe: " . $e->getMessage());
    sendJsonResponse(false, 'Erreur lors de l\'inscription', null, 500);
}