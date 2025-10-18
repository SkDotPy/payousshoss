<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/backend/lib/mailer.php';

$to = $_GET['to'] ?? 'sasafefo@icloud.com';
$prenom = 'Test';
$subject = 'Test PawConnect (SMTP)';
$html = '<h2>Hello 👋</h2><p>Ceci est un test SMTP depuis PawConnect.</p>';

$ok = send_mail($to, $prenom, $subject, $html);
echo $ok ? "✅ Mail envoyé à $to via port " . SMTP_PORT : "❌ Échec (voir logs Apache)";
