<?php
session_start();
require_once __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée');
}

if (!isset($_SESSION['user_id']) || (int)($_SESSION['is_super_admin'] ?? 0) !== 1) {
    http_response_code(403);
    exit('Accès refusé');
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$action = $_POST['action'] ?? '';

if ($id < 1 || !in_array($action, ['approve', 'block'], true)) {
    http_response_code(400);
    exit('Requête invalide');
}

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE users SET status='active' WHERE id=? AND role='refuge' AND status='pending'");
        $stmt->execute([$id]);
        $msg = 'Refuge activé.';
    } else {
        $stmt = $pdo->prepare("UPDATE users SET status='blocked' WHERE id=? AND role='refuge'");
        $stmt->execute([$id]);
        $msg = 'Refuge bloqué.';
    }
    header('Location: /admin/refuges.php?msg=' . urlencode($msg));
    exit;
} catch (Throwable $e) {
    error_log('VALIDATE_REFUGE: ' . $e->getMessage());
    header('Location: /admin/refuges.php?msg=' . urlencode('Erreur serveur.'));
    exit;
}
