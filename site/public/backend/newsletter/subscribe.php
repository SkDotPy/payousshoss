<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../config.php';
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors','0');
error_reporting(E_ALL);

function jerr(string $m,int $c=400){http_response_code($c);echo json_encode(['ok'=>false,'message'=>$m],JSON_UNESCAPED_UNICODE);exit;}
if($_SERVER['REQUEST_METHOD']!=='POST'){jerr('Méthode non autorisée',405);}

$raw=file_get_contents('php://input')?:'';
$ctype=$_SERVER['CONTENT_TYPE']??'';
$parsed=[];
if(stripos($ctype,'application/json')!==false){
  $j=json_decode($raw,true);
  if(is_array($j)){$parsed=$j;}
}
$data=array_merge($_POST,$parsed);

$email=trim((string)($data['email']??''));
$name =trim((string)($data['name']??''));

$consentKeys=['consent','newsletter','newsletter_consent','accept','accept_newsletter','accept_cgv','rgpd','gdpr'];
$consentVal=null;
foreach($consentKeys as $k){ if(array_key_exists($k,$data)){ $consentVal=$data[$k]; break; } }
$consentStr=strtolower(trim((string)($consentVal??'')));
$truthy=['1','on','true','yes','oui','y'];
$consent=in_array($consentStr,$truthy,true);

if($email==='' || !filter_var($email,FILTER_VALIDATE_EMAIL)){jerr('Email invalide',400);}
if(!$consent){jerr('Veuillez accepter de recevoir les emails.',400);}

try{
  $pdo=new PDO(DB_DSN,DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

  $stmt=$pdo->prepare("INSERT INTO newsletter_subscribers (email,name,status) VALUES (?,?, 'active')
                       ON DUPLICATE KEY UPDATE name=VALUES(name), status='active'");
  $stmt->execute([$email, $name!==''?$name:null]);

  $subject='Bienvenue sur PawConnect 🐾';
  $body="Bonjour ".($name!==''?$name:'cher membre').",\n\nMerci pour votre inscription à la newsletter PawConnect !";

  $pdo->prepare("INSERT INTO newsletter_logs (email,subject,body,type,status,error) VALUES (?,?,?,?,?,?)")
      ->execute([$email,$subject,$body,'welcome','sent',null]);

  echo json_encode(['ok'=>true,'message'=>'Inscription réussie ! ✅'],JSON_UNESCAPED_UNICODE);
}catch(Throwable $e){
  error_log('NEWSLETTER subscribe error: '.$e->getMessage());
  jerr('Erreur serveur',500);
}
