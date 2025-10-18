<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../config.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || (int)($_SESSION['is_super_admin'] ?? 0) !== 1) {
  http_response_code(403); echo json_encode(['ok'=>false,'error'=>'forbidden']); exit;
}
$q = trim($_POST['question'] ?? '');
$a = trim($_POST['answer'] ?? '');
if ($q === '' || $a === '') { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Champs requis']); exit; }

try {
  $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);
  $stmt = $pdo->prepare("INSERT INTO captcha_questions(question,answer,is_active,created_at) VALUES(?,?,1,NOW())");
  $stmt->execute([$q,$a]);
  echo json_encode(['ok'=>true,'id'=>$pdo->lastInsertId()]);
} catch (Throwable $e) {
  http_response_code(500); echo json_encode(['ok'=>false,'error'=>'Erreur serveur']);
}
