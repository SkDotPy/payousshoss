<?php
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(401); exit; }
if (($_SESSION['is_super_admin'] ?? 0) != 1) { http_response_code(403); exit; }

$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
if ($user_id <= 0) { http_response_code(400); exit; }

$pdo = new PDO('mysql:host=localhost;dbname=esgi_site;charset=utf8mb4','esgiadmin','esgiadmin',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$stmt = $pdo->prepare("UPDATE users SET status='active' WHERE id=? AND role='refuge'");
$stmt->execute([$user_id]);

header('Location: /admin/users.php');
