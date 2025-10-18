<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../backend/lib/mailer.php';
header('Content-Type: application/json; charset=utf-8');

if (!($_SESSION['is_super_admin'] ?? 0)) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'forbidden']); exit; }

$subject = trim($_POST['subject'] ?? '');
$body    = trim($_POST['body'] ?? '');
$scope   = trim($_POST['scope'] ?? 'all');
$target  = trim($_POST['email'] ?? '');

if ($subject==='' || $body==='') { echo json_encode(['ok'=>false,'error'=>'champs requis']); exit; }

try {
  $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

  if ($scope === 'one' && $target !== '') {
    $emails = [ $target ];
  } else {
    $stmt = $pdo->query("SELECT email FROM newsletter_subscribers WHERE status='active'");
    $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
  }

  $sent = 0; $fail = 0;
  foreach ($emails as $em) {
    $res = send_mail($em, $subject, nl2br($body), $body);
    $status = $res['ok'] ? 'sent' : 'failed';
    $err = $res['ok'] ? null : ($res['error'] ?? 'mail');
    $log = $pdo->prepare("INSERT INTO newsletter_logs (email,subject,body,type,status,error) VALUES (?,?,?,?,?,?)");
    $log->execute([$em,$subject,$body,'campaign',$status,$err]);
    if ($res['ok']) $sent++; else $fail++;
  }

  echo json_encode(['ok'=>true,'sent'=>$sent,'failed'=>$fail]);
} catch (Throwable $e) {
  echo json_encode(['ok'=>false,'error'=>'server']);
}
