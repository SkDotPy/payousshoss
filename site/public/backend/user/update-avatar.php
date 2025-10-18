<?php
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(401); exit; }

$uid = (int)$_SESSION['user_id'];
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    if (!empty($_POST['from_form'])) { header('Location: /profil.php?updated=0'); exit; }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok'=>false,'error'=>'upload']); exit;
}

$f = $_FILES['avatar']['tmp_name'];
$info = @getimagesize($f);
if (!$info) {
    if (!empty($_POST['from_form'])) { header('Location: /profil.php?updated=0'); exit; }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok'=>false,'error'=>'format']); exit;
}
$mime = $info['mime'];
switch ($mime) {
    case 'image/jpeg': $src = imagecreatefromjpeg($f); break;
    case 'image/png':  $src = imagecreatefrompng($f);  break;
    case 'image/webp': $src = imagecreatefromwebp($f); break;
    default:
        if (!empty($_POST['from_form'])) { header('Location: /profil.php?updated=0'); exit; }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok'=>false,'error'=>'mime']); exit;
}

$w = imagesx($src); $h = imagesy($src);
$side = min($w,$h);
$ox = (int)(($w-$side)/2);
$oy = (int)(($h-$side)/2);

$dstSize = 512;
$dst = imagecreatetruecolor($dstSize, $dstSize);
imagealphablending($dst, false);
imagesavealpha($dst, true);
imagecopyresampled($dst, $src, 0, 0, $ox, $oy, $dstSize, $dstSize, $side, $side);

$dir = __DIR__ . '/../../uploads/avatars/' . $uid;
if (!is_dir($dir)) { mkdir($dir, 0775, true); }
$target = $dir . '/profile.webp';
@unlink($target);

imagewebp($dst, $target, 85);
imagedestroy($src); imagedestroy($dst);

@chmod($target, 0664);

if (!empty($_POST['from_form'])) {
    header('Location: /profil.php?updated=1');
    exit;
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['ok'=>true,'path'=>'/uploads/avatars/'.$uid.'/profile.webp']);
