<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pdo = new PDO('mysql:host=localhost;dbname=esgi_site;charset=utf8mb4','esgiadmin','esgiadmin',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$stmt = $pdo->prepare("SELECT * FROM animals WHERE id=? LIMIT 1");
$stmt->execute([$id]);
$an = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$an) { http_response_code(404); exit; }
$photos = $pdo->prepare("SELECT path FROM animal_photos WHERE animal_id=? ORDER BY is_primary DESC,id ASC");
$photos->execute([$id]);
$photos = $photos->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($an['name']) ?></title>
<link href="/assets/css/main.css" rel="stylesheet">
</head>
<body>
<div class="container p-4">
<h2><?= htmlspecialchars($an['name']) ?></h2>
<div class="mb-3">
<?php foreach($photos as $p): ?>
<img src="<?= htmlspecialchars($p) ?>" style="max-width:200px;margin-right:8px" alt="">
<?php endforeach; ?>
</div>
<ul>
<li>Espèce: <?= htmlspecialchars($an['species']) ?></li>
<li>Race: <?= htmlspecialchars($an['breed'] ?? '') ?></li>
<li>Taille: <?= htmlspecialchars($an['size'] ?? '') ?></li>
<li>Couleur: <?= htmlspecialchars($an['color'] ?? '') ?></li>
<li>Statut: <?= htmlspecialchars($an['status']) ?></li>
</ul>
<p><?= nl2br(htmlspecialchars($an['description'] ?? '')) ?></p>
</div>
</body>
</html>

