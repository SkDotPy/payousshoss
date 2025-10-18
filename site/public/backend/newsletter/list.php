<?php
session_start();
require_once __DIR__ . '/../../config.php';
header('Content-Type: application/json; charset=utf-8');
if (!($_SESSION['is_super_admin'] ?? 0)) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'forbidden']); exit; }

try {
  $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
  $stmt = $pdo->query("SELECT id,email,subject,type,status,sent_at FROM newsletter_logs ORDER BY id DESC LIMIT 200");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(['ok'=>true,'rows'=>$rows]);
} catch (Throwable $e) {
  echo json_encode(['ok'=>false,'error'=>'server']);
}
