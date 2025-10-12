<?php
/**
 * Configuration de la base de données
 * Paw Connect - Configuration PDO
 */

// Constantes de connexion
define('DB_HOST', 'localhost');
define('DB_NAME', 'pawconnect');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Constantes générales
define('SITE_NAME', 'Paw Connect');
define('SITE_URL', 'http://localhost:8000');
define('SESSION_LIFETIME', 1800); // 30 minutes
define('UPLOAD_MAX_SIZE', 5242880); // 5 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

/**
 * Fonction pour obtenir une connexion PDO
 */
function getDbConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        return $pdo;
        
    } catch(PDOException $e) {
        error_log("Erreur de connexion BDD: " . $e->getMessage());
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur de connexion à la base de données'
        ]);
        exit;
    }
}

/**
 * Fonction pour envoyer une réponse JSON
 */
function sendJsonResponse($success, $message, $data = null, $httpCode = 200) {
    http_response_code($httpCode);
    header('Content-Type: application/json');
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit;
}

/**
 * Fonction pour valider un email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Fonction pour nettoyer les inputs
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Fonction pour enregistrer un log
 */
function logActivity($pdo, $userId, $type, $action, $details = null) {
    try {
        $sql = "INSERT INTO historique (idUser, type, changIn, temps) 
                VALUES (:userId, :type, :action, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':userId' => $userId,
            ':type' => $type,
            ':action' => $action
        ]);
        
        return true;
    } catch(PDOException $e) {
        error_log("Erreur log: " . $e->getMessage());
        return false;
    }
}