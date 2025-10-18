<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$to = filter_var($_GET['to'] ?? '', FILTER_VALIDATE_EMAIL);
$mode = ($_GET['mode'] ?? '465') === '587' ? '587' : '465';

if (!$to) {
    http_response_code(400);
    echo "Paramètre 'to' invalide";
    exit;
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.ionos.fr';
    $mail->SMTPAuth = true;
    $mail->Username = 'contact@paw-connect.org';
    $mail->Password = 'sasaFEFOnana20$';
    if ($mode === '465') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
    }
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('contact@paw-connect.org', 'Paw Connect');
    $mail->addAddress($to);
    $mail->Subject = 'Test SMTP Paw Connect';
    $mail->Body = "Hello,\nCeci est un test d'envoi via IONOS SMTP (mode {$mode}).";
    $mail->send();
    echo "OK: mail envoyé à {$to} via port {$mode}";
} catch (Exception $e) {
    http_response_code(500);
    echo "Erreur: " . $mail->ErrorInfo;
}
