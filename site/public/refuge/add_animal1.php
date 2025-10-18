<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$uid = (int)$_SESSION['user_id'];
$role = $_SESSION['role'] ?? '';

// Vérifier que l'utilisateur est un refuge
if ($role !== 'refuge') {
    header('Location: /profil.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ajouter un animal - PawConnect</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="/assets/css/main.css?v=1" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.photo-preview {
    width: 100%;
    height: 200px;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    overflow: hidden;
    position: relative;
}
.photo-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
}
.photo-preview.has-image {
    border-color: #0d6efd;
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="/index.php"><i class="fas fa-paw"></i> PawConnect</a>
    <div class="ms-auto d-flex gap-2">
      <a href="/refuge/manage_animals.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour
      </a>
      <a href="/logout.php" class="btn btn-outline-secondary">
        <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
      </a>
    </div>
  </div>
</nav>

<section class="py-4">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Ajouter un nouvel animal</h4>
          </div>
          <div class="card-body">
            <form action="/backend/animals/add.php" method="POST" enctype="multipart/form-data" id="addAnimalForm">
              
              <!-- Informations de base -->
              <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i> Informations de base</h5>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label">Nom <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="name" required maxlength="120">
                </div>
                
                <div class="col-md-6">
                  <label class="form-label">Espèce <span class="text-danger">*</span></label>
                  <select class="form-select" name="species" required>
                    <option value="">-- Choisir --</option>
                    <option value="Chien">Chien</option>
                    <option value="Chat">Chat</option>
                    <option value="Lapin">Lapin</option>
                    <option value="Oiseau">Oiseau</option>
                    <option value="Rongeur">Rongeur</option>
                    <option value="Autre">Autre</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Race</label>
                  <input type="text" class="form-control" name="breed" maxlength="120">
                  <small class="text-muted">Laissez vide si croisé ou inconnu</small>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Âge <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="age" required placeholder="Ex: 2 ans, 6 mois...">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Sexe <span class="text-danger">*</span></label>
                  <select class="form-select" name="sex" required>
                    <option value="">-- Choisir --</option>
                    <option value="male">Mâle</option>
                    <option value="female">Femelle</option>
                    <option value="unknown">Inconnu</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Taille</label>
                  <select class="form-select" name="size">
                    <option value="">-- Choisir --</option>
                    <option value="small">Petit</option>
                    <option value="medium">Moyen</option>
                    <option value="large">Grand</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Couleur</label>
                  <input type="text" class="form-control" name="color" maxlength="50">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Statut <span class="text-danger">*</span></label>
                  <select class="form-select" name="status" required>
                    <option value="available">Disponible</option>
                    <option value="reserved">Réservé</option>
                    <option value="adopted">Adopté</option>
                  </select>
                </div>

                <div class="col-12">
                  <label class="form-label">Description</label>
                  <textarea class="form-control" name="description" rows="4" placeholder="Décrivez le caractère, les habitudes, les besoins spéciaux..."></textarea>
                </div>
              </div>

              <!-- Photos -->
              <h5 class="mb-3"><i class="fas fa-camera me-2"></i> Photos (3 maximum)</h5>
              <div class="row g-3 mb-4">
                <div class="col-md-4">
                  <label class="form-label">Photo 1 (principale)</label>
                  <div class="photo-preview" id="preview1">
                    <span class="text-muted"><i class="fas fa-image fa-2x mb-2 d-block"></i>Cliquez pour ajouter</span>
                  </div>
                  <input type="file" class="form-control mt-2" name="photo1" accept="image/*" onchange="previewImage(this, 'preview1')">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Photo 2 (optionnelle)</label>
                  <div class="photo-preview" id="preview2">
                    <span class="text-muted"><i class="fas fa-image fa-2x mb-2 d-block"></i>Cliquez pour ajouter</span>
                  </div>
                  <input type="file" class="form-control mt-2" name="photo2" accept="image/*" onchange="previewImage(this, 'preview2')">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Photo 3 (optionnelle)</label>
                  <div class="photo-preview" id="preview3">
                    <span class="text-muted"><i class="fas fa-image fa-2x mb-2 d-block"></i>Cliquez pour ajouter</span>
                  </div>
                  <input type="file" class="form-control mt-2" name="photo3" accept="image/*" onchange="previewImage(this, 'preview3')">
                </div>
              </div>

              <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <small>
                  Formats acceptés: JPG, PNG, WebP. Taille max: 5 Mo par photo.
                  La première photo sera utilisée comme photo principale.
                </small>
              </div>

              <!-- Boutons -->
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                  <i class="fas fa-check me-1"></i> Enregistrer l'animal
                </button>
                <a href="/refuge/manage_animals.php" class="btn btn-outline-secondary">
                  <i class="fas fa-times me-1"></i> Annuler
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewImage(input, previewId) {
  const preview = document.getElementById(previewId);
  const file = input.files[0];
  
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
      preview.classList.add('has-image');
    };
    reader.readAsDataURL(file);
  }
}

// Validation du formulaire
document.getElementById('addAnimalForm').addEventListener('submit', function(e) {
  const name = this.querySelector('[name="name"]').value.trim();
  const species = this.querySelector('[name="species"]').value;
  const age = this.querySelector('[name="age"]').value.trim();
  const sex = this.querySelector('[name="sex"]').value;
  
  if (!name || !species || !age || !sex) {
    e.preventDefault();
    alert('Veuillez remplir tous les champs obligatoires (*)');
    return false;
  }
});
</script>
</body>
</html>
