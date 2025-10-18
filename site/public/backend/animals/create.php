<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
if (!isset($_SESSION['user_id'])) { http_response_code(401); exit; }
$role = $_SESSION['role'] ?? 'user';
$status = $_SESSION['status'] ?? 'active';
$is_super = $_SESSION['is_super_admin'] ?? 0;
if (!($is_super || ($role==='refuge' && $status==='active'))) { http_response_code(403); exit; }

$name = trim($_POST['name'] ?? '');
$species = trim($_POST['species'] ?? '');
$breed = trim($_POST['breed'] ?? '');
$sex = $_POST['sex'] ?? 'unknown';
$age_years = $_POST['age_years'] !== '' ? floatval($_POST['age_years']) : null;
$size = $_POST['size'] ?? null;
$color = trim($_POST['color'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($name==='' || $species==='') { die('Champs obligatoires manquants.'); }

$pdo = new PDO('mysql:host=localhost;dbname=esgi_site;charset=utf8mb4','esgiadmin','esgiadmin',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->beginTransaction();
$stmt = $pdo->prepare('INSERT INTO animals (name,species,breed,sex,age_years,size,color,description,status,created_by) VALUES (?,?,?,?,?,?,?,?,?,?)');
$stmt->execute([$name,$species,$breed,$sex,$age_years,$size,$color,$description,'available',$_SESSION['user_id']]);
$animal_id = $pdo->lastInsertId();

$upload_dir = __DIR__ . '/../../uploads/animals/' . $animal_id;
if (!is_dir($upload_dir)) { mkdir($upload_dir, 0775, true); }

$primary_set = false;
if (!empty($_FILES['photos']) && is_array($_FILES['photos']['name'])) {
    for ($i=0; $i<count($_FILES['photos']['name']); $i++) {
        $err = $_FILES['photos']['error'][$i];
        if ($err !== UPLOAD_ERR_OK) { continue; }
        $tmp = $_FILES['photos']['tmp_name'][$i];
        $orig = $_FILES['photos']['name'][$i];
        $type = mime_content_type($tmp);
        if (!in_array($type, ['image/jpeg','image/png'])) { continue; }
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png'])) { continue; }
        if (filesize($tmp) > 5*1024*1024) { continue; }
        $filename = uniqid('img_', true) . '.' . $ext;
        $dest_fs = $upload_dir . '/' . $filename;
        $dest_rel = '/uploads/animals/' . $animal_id . '/' . $filename;
        if (move_uploaded_file($tmp, $dest_fs)) {
            $stmtP = $pdo->prepare('INSERT INTO animal_photos (animal_id,path,is_primary) VALUES (?,?,?)');
            $stmtP->execute([$animal_id, $dest_rel, $primary_set ? 0 : 1]);
            if (!$primary_set) { $primary_set = true; }
        }
    }
}
$pdo->commit();
header('Location: /admin/animal_new.php?ok=1');
