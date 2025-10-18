<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login.php'); exit; }
$role = $_SESSION['role'] ?? 'user';
$status = $_SESSION['status'] ?? 'active';
$is_super = $_SESSION['is_super_admin'] ?? 0;
if (!($is_super || ($role==='refuge' && $status==='active'))) { http_response_code(403); exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ajouter un animal</title>
<link href="/assets/css/main.css" rel="stylesheet">
</head>
<body>
<div class="container p-4" style="max-width:800px">
<h2>Ajouter un animal</h2>
<form action="/backend/animals/create.php" method="POST" enctype="multipart/form-data">
<div class="mb-3"><label>Nom</label><input type="text" name="name" class="form-control" required></div>
<div class="mb-3"><label>Espèce</label><input type="text" name="species" class="form-control" placeholder="chien, chat..." required></div>
<div class="mb-3"><label>Race</label><input type="text" name="breed" class="form-control"></div>
<div class="mb-3"><label>Sexe</label>
<select name="sex" class="form-select">
<option value="unknown">Inconnu</option>
<option value="male">Mâle</option>
<option value="female">Femelle</option>
</select></div>
<div class="mb-3"><label>Âge (années, ex 2.5)</label><input type="number" step="0.1" name="age_years" class="form-control"></div>
<div class="mb-3"><label>Taille</label>
<select name="size" class="form-select">
<option value="">—</option>
<option value="small">Petit</option>
<option value="medium">Moyen</option>
<option value="large">Grand</option>
</select></div>
<div class="mb-3"><label>Couleur</label><input type="text" name="color" class="form-control"></div>
<div class="mb-3"><label>Description</label><textarea name="description" class="form-control" rows="4"></textarea></div>
<div class="mb-3"><label>Photos</label><input type="file" name="photos[]" class="form-control" accept=".jpg,.jpeg,.png" multiple></div>
<button type="submit" class="btn btn-primary">Enregistrer</button>
</form>
</div>
</body>
</html>
