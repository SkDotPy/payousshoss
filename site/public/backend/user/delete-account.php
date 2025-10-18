<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login.php'); exit; }
require_once __DIR__ . '/../../config.php';

$uid = (int)$_SESSION['user_id'];

function rrmdir($dir) {
    if (!is_dir($dir)) return;
    $items = scandir($dir);
    foreach ($items as $f) {
        if ($f === '.' || $f === '.') continue;
        $p = $dir . DIRECTORY_SEPARATOR . $f;
        if (is_dir($p)) rrmdir($p); else @unlink($p);
    }
    @rmdir($dir);
}

try {
    $dir = __DIR__ . '/../../uploads/avatars/' . $uid;
    rrmdir($dir);

    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->prepare('DELETE FROM users WHERE id=?');
    $stmt->execute([$uid]);

    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();

    header('Location: /index.php?msg=compte_supprime');
} catch (Throwable $e) {
    error_log('DELETE ACCOUNT ERROR: '.$e->getMessage());
    header('Location: /profil.php?msg=server_error');
}
