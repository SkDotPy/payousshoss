<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../config.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || (int)($_SESSION['is_super_admin'] ?? 0) !== 1) {
  http_response_code(403); echo json_encode(['ok'=>false,'error'=>'forbidden']); exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'bad id']); exit; }

try {
  $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
  ]);
  // ne pas modifier un super admin
  $st = $pdo->prepare("SELECT is_super_admin FROM users WHERE id=?");
  $st->execute([$id]);
  $r = $st->fetch();
  if (!$r) { http_response_code(404); echo json_encode(['ok'=>false,'error'=>'not found']); exit; }
  if ((int)$r['is_super_admin'] === 1) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'target is superadmin']); exit; }

  $pdo->prepare("UPDATE users SET role='refuge', status='active' WHERE id=?")->execute([$id]);
  echo json_encode(['ok'=>true]);
} catch (Throwable $e) {
  http_response_code(500); echo json_encode(['ok'=>false,'error'=>'server']);
}
