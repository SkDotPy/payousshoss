<?php
session_start();
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../lib/mailer.php';
header('Content-Type: application/json; charset=utf-8');

$email   = trim($_POST['email'] ?? '');
$name    = trim($_POST['name'] ?? '');
$consent = isset($_POST['consent']) ? 1 : 0;

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok'=>false,'error'=>'Email invalide']); exit;
}
if (!$consent) {
    echo json_encode(['ok'=>false,'error'=>'Veuillez accepter de recevoir nos emails']); exit;
}

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

    $pdo->prepare(
        "INSERT INTO newsletter_subscribers (email,name,status) VALUES (?,?, 'active')
         ON DUPLICATE KEY UPDATE name=VALUES(name), status='active'"
    )->execute([$email, $name]);

    $subject = 'Bienvenue à la newsletter PawConnect';
    $text    = "Bonjour ".($name ?: '👋').",\nMerci pour votre inscription à notre newsletter.";
    $html    = "<p>Bonjour <strong>".htmlspecialchars($name ?: '👋',ENT_QUOTES,'UTF-8')."</strong>,</p>
                <p>Merci pour votre inscription à la newsletter PawConnect.</p>";

    $sent = send_mail($email, $subject, $html, $text);

    $pdo->prepare(
        "INSERT INTO newsletter_logs (email,subject,body,type,status,error)
         VALUES (?,?,?,?,?,?)"
    )->execute([$email,$subject,$text,'subscribe_welcome',$sent['ok']?'sent':'failed',$sent['ok']?null:($sent['error']??'send')]);

    echo json_encode(['ok'=>true,'message'=>'Inscription confirmée. Bienvenue !']);
} catch (Throwable $e) {
    error_log('NEWSLETTER SUBSCRIBE ERROR: '.$e->getMessage());
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'Erreur serveur']);
}
