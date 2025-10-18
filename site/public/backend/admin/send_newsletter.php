<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');
error_reporting(E_ALL);

function jerr(string $msg, int $code = 400): never {
    http_response_code($code);
    // S'assurer qu'aucune sortie parasite ne précède le JSON
    if (function_exists('ob_get_length') && ob_get_length()) { @ob_end_clean(); }
    echo json_encode(['ok'=>false,'error'=>$msg], JSON_UNESCAPED_UNICODE);
    exit;
}

function jout(array $data): never {
    if (function_exists('ob_get_length') && ob_get_length()) { @ob_end_clean(); }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jerr('Méthode non autorisée', 405);

// Auth super-admin obligatoire
if (!isset($_SESSION['user_id']) || (int)($_SESSION['is_super_admin'] ?? 0) !== 1) {
    jerr('Non autorisé', 403);
}

$subject = trim($_POST['subject'] ?? '');
$body    = trim($_POST['body'] ?? '');
$dryRun  = isset($_POST['test']) && $_POST['test'] !== '';

if ($subject === '' || $body === '') {
    jerr('Sujet et message requis');
}

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Récupère les abonnés actifs
    $subs = $pdo->query("SELECT email FROM newsletter_subscribers WHERE status='active'")->fetchAll();
    $emails = array_values(array_unique(array_map(static fn($r) => trim((string)$r['email']), $subs)));
    $emails = array_filter($emails, static fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL));

    if ($dryRun) {
        jout(['ok'=>true, 'mode'=>'test', 'recipients'=>count($emails)]);
    }

    // PHPMailer
    require_once __DIR__ . '/../../../vendor/autoload.php';
    $mailer = new PHPMailer\PHPMailer\PHPMailer(true);
    $mailer->isSMTP();
    $mailer->Host       = SMTP_HOST;
    $mailer->SMTPAuth   = true;
    $mailer->Username   = SMTP_USER;
    $mailer->Password   = SMTP_PASS;
    if (defined('SMTP_SECURE') && SMTP_SECURE) { $mailer->SMTPSecure = SMTP_SECURE; }
    if (defined('SMTP_PORT') && SMTP_PORT) { $mailer->Port = SMTP_PORT; }
    $mailer->CharSet    = 'UTF-8';
    $mailer->setFrom(SMTP_FROM, SMTP_FROM_NAME ?? 'Paw Connect');
    $mailer->isHTML(true);

    $sent = 0; $failed = 0;

    // Prépare l’insert log
    $stmtLog = $pdo->prepare(
        "INSERT INTO newsletter_logs (email, subject, body, type, status, error)
         VALUES (?,?,?,?,?,?)"
    );

    foreach ($emails as $email) {
        try {
            $mailer->clearAddresses();
            $mailer->addAddress($email);
            $mailer->Subject = $subject;
            $mailer->Body    = nl2br($body);
            $mailer->AltBody = $body;
            $mailer->send();
            $stmtLog->execute([$email, $subject, $body, 'blast', 'sent', null]);
            $sent++;
        } catch (Throwable $ex) {
            $stmtLog->execute([$email, $subject, $body, 'blast', 'failed', $ex->getMessage()]);
            $failed++;
        }
    }

    jout(['ok'=>true, 'sent'=>$sent, 'failed'=>$failed, 'total'=>count($emails)]);

} catch (Throwable $e) {
    jerr('Erreur serveur: '.$e->getMessage(), 500);
}
