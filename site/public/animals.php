<?php
$pdo = new PDO('mysql:host=localhost;dbname=esgi_site;charset=utf8mb4','esgiadmin','esgiadmin',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$stmt = $pdo->query("SELECT a.id,a.name,a.species,a.breed,a.size,a.color,a.status,COALESCE((SELECT path FROM animal_photos p WHERE p.animal_id=a.id AND p.is_primary=1 LIMIT 1),'') AS photo FROM animals a WHERE a.status='available' ORDER BY a.created_at DESC");
$animals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Animaux à l’adoption</title>
<link href="/assets/css/main.css" rel="stylesheet">
</head>
<body>
<div class="container p-4">
<h2>Animaux à l’adoption</h2>
<div class="row">
<?php foreach($animals as $an): ?>
<div class="col-md-4 mb-4">
<div class="card">
<?php if ($an['photo']): ?>
<img src="<?= htmlspecialchars($an['photo']) ?>" class="card-img-top" alt="<?= htmlspecialchars($an['name']) ?>">
<?php endif; ?>
<div class="card-body">
<h5 class="card-title"><?= htmlspecialchars($an['name']) ?></h5>
<p class="card-text"><?= htmlspecialchars($an['species']) ?> <?= $an['breed'] ? '• '.htmlspecialchars($an['breed']) : '' ?></p>
<a href="/animal.php?id=<?= (int)$an['id'] ?>" class="btn btn-primary">Voir</a>
</div>
</div>
</div>
<?php endforeach; ?>
</div>
</div>
</body>
</html>
