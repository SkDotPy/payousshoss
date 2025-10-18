<?php
session_start();

$fromForm = !empty($_POST['from_form']);
if (!$fromForm) {
    header('Content-Type: application/json; charset=utf-8');
}

require_once __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

$email    = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode(['ok' => false, 'error' => 'Email et mot de passe requis']);
    exit;
}

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("SELECT id, nom, age, email, password_hash, role, status, is_super_admin 
                           FROM users 
                           WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['ok' => false, 'error' => 'Identifiants invalides']);
        exit;
    }

    if (!password_verify($password, $user['password_hash'])) {
        echo json_encode(['ok' => false, 'error' => 'Identifiants invalides']);
        exit;
    }

    if ($user['status'] !== 'active') {
        $msg = ($user['status'] === 'pending')
            ? 'Compte en attente de validation par un super admin'
            : 'Compte bloqué';
        echo json_encode(['ok' => false, 'error' => $msg]);
        exit;
    }

    $_SESSION['user_id']        = (int)$user['id'];
    $_SESSION['email']          = $user['email'];
    $_SESSION['role']           = $user['role'];
    $_SESSION['status']         = $user['status'];
    $_SESSION['nom']            = $user['nom'];
    $_SESSION['age']            = (int)$user['age'];
    $_SESSION['is_super_admin'] = (int)$user['is_super_admin'];

    session_regenerate_id(true);

    unset($_SESSION['captcha_answer']);
if ($fromForm) {
        header('Content-Type: text/html; charset=utf-8');

        if (!empty($_SESSION['is_super_admin']) && $_SESSION['is_super_admin'] == 1) {
            header('Location: /admin/refuges.php');
        } else {
            header('Location: /index.php');
        }

        exit;
    }

    echo json_encode(['ok' => true]);
} catch (Throwable $e) {
    error_log('LOGIN ERROR: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erreur serveur.']);
}
