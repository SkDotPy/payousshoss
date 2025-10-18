<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || (int)($_SESSION['is_super_admin'] ?? 0) !== 1) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'Accès interdit']);
    exit;
}

$action = $_POST['action'] ?? '';
$id = (int)($_POST['id'] ?? 0);
if ($id <= 0 || !in_array($action, ['validate', 'ban', 'delete'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'Requête invalide']);
    exit;
}

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    switch ($action) {
        case 'validate':
            $stmt = $pdo->prepare("UPDATE users SET status='active' WHERE id=? AND role='refuge'");
            break;
        case 'ban':
            $stmt = $pdo->prepare("UPDATE users SET status='banned' WHERE id=? AND role='refuge'");
            break;
        case 'delete':
            $stmt = $pdo->prepare("DELETE FROM users WHERE id=? AND role='refuge'");
            break;
    }

    $stmt->execute([$id]);
    echo json_encode(['ok' => true, 'msg' => 'Action effectuée avec succès.']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Erreur serveur: '.$e->getMessage()]);
}
