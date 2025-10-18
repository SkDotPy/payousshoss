<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../config.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $row = $pdo->query("SELECT id, question FROM captcha_questions WHERE is_active=1 ORDER BY RAND() LIMIT 1")->fetch();
    if (!$row) {
        echo json_encode(['ok'=>false,'error'=>'Aucune question active']); exit;
    }

    echo json_encode(['ok'=>true,'id'=>(int)$row['id'],'question'=>$row['question']], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'Erreur serveur'], JSON_UNESCAPED_UNICODE);
}
