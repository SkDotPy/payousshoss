<?php
session_start();
require_once __DIR__ . '/../../backend/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || (int)($_SESSION['is_super_admin'] ?? 0) !== 1) {
  echo json_encode(['ok'=>false,'error'=>'Accès refusé']);
  exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
  echo json_encode(['ok'=>false,'error'=>'ID invalide']);
  exit;
}

try {
  $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

  $stmt = $pdo->prepare("DELETE FROM newsletter WHERE id = ?");
  $stmt->execute([$id]);
  echo json_encode(['ok'=>true]);
} catch (Throwable $e) {
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
