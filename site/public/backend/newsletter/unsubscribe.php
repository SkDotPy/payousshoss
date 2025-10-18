<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Logs dédiés newsletter (site/storage/logs/newsletter.log)
function nlog(string $msg): void {
    $logDir = __DIR__ . '/../../../storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0775, true);
        @chmod($logDir, 0775);
    }
    $file = $logDir . '/newsletter.log';
    @file_put_contents($file, '['.date('Y-m-d H:i:s')."] unsubscribe: $msg\n", FILE_APPEND);
}

// Réponse JSON erreur
function jerr(string $message, int $code = 500): void {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jerr('Méthode non autorisée', 405);
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jerr('Email invalide', 400);
}

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $pdo->beginTransaction();

    // Passe (ou crée) le statut à 'unsub'
    $sql = "INSERT INTO newsletter_subscribers (email, status, created_at)
            VALUES (?, 'unsub', NOW())
            ON DUPLICATE KEY UPDATE status='unsub'";
    $pdo->prepare($sql)->execute([$email]);

    // Log propre (non NULL)
    $subject = 'Désinscription newsletter';
    $body    = 'L’utilisateur '.$email.' s’est désinscrit le '.date('Y-m-d H:i:s').'.';
    $type    = 'unsubscribe';
    $status  = 'ok';

    $pdo->prepare("INSERT INTO newsletter_logs (email, subject, body, type, status, error)
                   VALUES (?,?,?,?,?,?)")
        ->execute([$email, $subject, $body, $type, $status, null]);

    $pdo->commit();

    echo json_encode(['ok' => true, 'status' => 'unsubscribed'], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    nlog('ERROR: '.$e->getMessage());
    jerr('Erreur serveur');
}
