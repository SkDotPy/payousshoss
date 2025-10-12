<?php
/**
 * Ajouter un animal aux favoris
 * POST /backend/favorites/add.php
 */

session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

// Vérifier l'authentification
if (!isset($_SESSION['user_id'])) {
    sendJsonResponse(false, 'Non authentifié', null, 401);
}

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Méthode non autorisée', null, 405);
}

// Récupérer les données
$input = json_decode(file_get_contents('php://input'), true);
$animalId = intval($input['animal_id'] ?? 0);
$userId = $_SESSION['user_id'];

if ($animalId === 0) {
    sendJsonResponse(false, 'ID animal invalide', null, 400);
}

try {
    $pdo = getDbConnection();
    
    // Vérifier si l'animal existe
    $checkAnimalSql = "SELECT id FROM animal WHERE id = :animalId";
    $checkAnimalStmt = $pdo->prepare($checkAnimalSql);
    $checkAnimalStmt->execute([':animalId' => $