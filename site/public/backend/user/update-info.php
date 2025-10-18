<?php
session_start();
require_once __DIR__ . '/../../config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
if (!isset($_SESSION['user_id'])) { http_response_code(401); exit; }

$userId = (int) $_SESSION['user_id'];
$nom = trim($_POST['nom'] ?? '');
$age = intval($_POST['age'] ?? 0);
$email = trim($_POST['email'] ?? '');

if ($nom === '' || $email === '') { echo 'Champs obligatoires manquants.'; exit; }

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

    $dup = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1');
    $dup->execute([$email, $userId]);
    if ($dup->fetch()) { echo 'Cet email est déjà utilisé.'; exit; }

    $upd = $pdo->prepare('UPDATE users SET nom = ?, age = ?, email = ? WHERE id = ?');
    $upd->execute([$nom, $age, $email, $userId]);

    header('Location: /profil.php');
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Erreur serveur.';
}
