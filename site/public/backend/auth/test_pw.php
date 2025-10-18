<?php
require_once __DIR__ . '/../../config.php';
header('Content-Type: application/json; charset=utf-8');

$email = 'allababidi.nawal@gmail.com';
$plain = 'Test1234!';

try {
  $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  $stmt = $pdo->prepare("SELECT id, email, password_hash FROM users WHERE email=? LIMIT 1");
  $stmt->execute([$email]);
  $u = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$u) { echo json_encode(['ok'=>false,'step'=>'fetch','msg'=>'user not found']); exit; }

  $match = password_verify($plain, $u['password_hash']);
  echo json_encode([
    'ok' => true,
    'user_id' => (int)$u['id'],
    'match' => $match,
    'hash_len' => strlen($u['password_hash'])
  ]);
} catch (Throwable $e) {
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
