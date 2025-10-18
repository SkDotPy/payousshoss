<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

function send_mail(string $to, string $subject, string $html, string $text): array {
    $m = new PHPMailer(true);
    try {
        $m->isSMTP();
        $m->Host = SMTP_HOST;
        $m->Port = SMTP_PORT;
        $m->SMTPSecure = SMTP_SECURE;
        $m->SMTPAuth = true;
        $m->Username = SMTP_USER;
        $m->Password = SMTP_PASS;
        $m->CharSet  = 'UTF-8';
        $m->isHTML(true);
        $m->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $m->addAddress($to);
        $m->Subject = $subject;
        $m->Body    = $html;
        $m->AltBody = $text;
        $m->send();
        return ['ok'=>true];
    } catch (Exception $e) {
        error_log('MAIL ERROR: '.$e->getMessage());
        return ['ok'=>false,'error'=>$e->getMessage()];
    }
}
