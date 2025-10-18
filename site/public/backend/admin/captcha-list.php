<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../config.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || (int)($_SESSION['is_super_admin'] ?? 0) !== 1) {
  http_response_code(403); echo json_encode(['ok'=>false,'error'=>'forbidden']); exit;
}
try {
  $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
  $stmt = $pdo->query("SELECT id, question, answer, is_active, created_at FROM captcha_questions ORDER BY id DESC");
  echo json_encode(['ok'=>true,'items'=>$stmt->fetchAll()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500); echo json_encode(['ok'=>false,'error'=>'Erreur serveur']); 
}
