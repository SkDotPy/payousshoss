<?php
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(401); exit; }
require_once __DIR__ . '/../../config.php';

$pwd = $_POST['password'] ?? '';
$cfm = $_POST['confirm'] ?? '';

if ($pwd === '' || $cfm === '') { header('Location: /profil.php?msg=champs_requis'); exit; }
if ($pwd !== $cfm) { header('Location: /profil.php?msg=mdp_diff'); exit; }
if (strlen($pwd) < 8) { header('Location: /profil.php?msg=mdp_court'); exit; }

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $hash = password_hash($pwd, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?');
    $stmt->execute([$hash, (int)$_SESSION['user_id']]);
    header('Location: /profil.php?msg=mdp_ok');
} catch (Throwable $e) {
    error_log('UPDATE PASSWORD ERROR: '.$e->getMessage());
    header('Location: /profil.php?msg=server_error');
}
