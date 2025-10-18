<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$uid = (int)$_SESSION['user_id'];
$role = $_SESSION['role'] ?? '';
$is_super = (int)($_SESSION['is_super_admin'] ?? 0);
$animal_id = (int)($_GET['id'] ?? 0);

if ($animal_id <= 0) {
    header('Location: /refuge/manage_animals.php?error=Animal invalide');
    exit;
}

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Récupérer l'animal
    $stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
    $stmt->execute([$animal_id]);
    $animal = $stmt->fetch();

    if (!$animal) {
        header('Location: /refuge/manage_animals.php?error=Animal introuvable');
        exit;
    }

    // Vérifier les permissions (seulement son refuge ou super admin)
    if ($role !== 'refuge' || ($animal['refuge_id'] != $uid && $is_super !== 1)) {
        header('Location: /refuge/manage_animals.php?error=Accès refusé');
        exit;
    }

} catch (PDOException $e) {
    header('Location: /refuge/manage_animals.php?error=' . urlencode('Erreur : ' . $e->getMessage()));
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modifier <?= htmlspecialchars($animal['name']) ?> - PawConnect</title>
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
.delete-photo-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
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
            <h4 class="mb-0">
              <i class="fas fa-edit me-2"></i> 
              Modifier <?= htmlspecialchars($animal['name']) ?>
            </h4>
          </div>
          <div class="card-body">
            <form action="/backend/animals/update.php" method="POST" enctype="multipart/form-data" id="editAnimalForm">
              <input type="hidden" name="animal_id" value="<?= $animal['id'] ?>">
              
              <!-- Informations de base -->
              <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i> Informations de base</h5>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label">Nom <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="name" required maxlength="120" 
                         value="<?= htmlspecialchars($animal['name']) ?>">
                </div>
                
                <div class="col-md-6">
                  <label class="form-label">Espèce <span class="text-danger">*</span></label>
                  <select class="form-select" name="species" required>
                    <option value="">-- Choisir --</option>
                    <option value="Chien" <?= $animal['species'] === 'Chien' ? 'selected' : '' ?>>Chien</option>
                    <option value="Chat" <?= $animal['species'] === 'Chat' ? 'selected' : '' ?>>Chat</option>
                    <option value="Lapin" <?= $animal['species'] === 'Lapin' ? 'selected' : '' ?>>Lapin</option>
                    <option value="Oiseau" <?= $animal['species'] === 'Oiseau' ? 'selected' : '' ?>>Oiseau</option>
                    <option value="Rongeur" <?= $animal['species'] === 'Rongeur' ? 'selected' : '' ?>>Rongeur</option>
                    <option value="Autre" <?= $animal['species'] === 'Autre' ? 'selected' : '' ?>>Autre</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Race</label>
                  <input type="text" class="form-control" name="breed" maxlength="120" 
                         value="<?= htmlspecialchars($animal['breed'] ?? '') ?>">
                  <small class="text-muted">Laissez vide si croisé ou inconnu</small>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Âge <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="age" required 
                         value="<?= htmlspecialchars($animal['age']) ?>"
                         placeholder="Ex: 2 ans, 6 mois...">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Sexe <span class="text-danger">*</span></label>
                  <select class="form-select" name="sex" required>
                    <option value="">-- Choisir --</option>
                    <option value="male" <?= $animal['sex'] === 'male' ? 'selected' : '' ?>>Mâle</option>
                    <option value="female" <?= $animal['sex'] === 'female' ? 'selected' : '' ?>>Femelle</option>
                    <option value="unknown" <?= $animal['sex'] === 'unknown' ? 'selected' : '' ?>>Inconnu</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Taille</label>
                  <select class="form-select" name="size">
                    <option value="">-- Choisir --</option>
                    <option value="small" <?= $animal['size'] === 'small' ? 'selected' : '' ?>>Petit</option>
                    <option value="medium" <?= $animal['size'] === 'medium' ? 'selected' : '' ?>>Moyen</option>
                    <option value="large" <?= $animal['size'] === 'large' ? 'selected' : '' ?>>Grand</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Couleur</label>
                  <input type="text" class="form-control" name="color" maxlength="50" 
                         value="<?= htmlspecialchars($animal['color'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Statut <span class="text-danger">*</span></label>
                  <select class="form-select" name="status" required>
                    <option value="available" <?= $animal['status'] === 'available' ? 'selected' : '' ?>>Disponible</option>
                    <option value="reserved" <?= $animal['status'] === 'reserved' ? 'selected' : '' ?>>Réservé</option>
                    <option value="adopted" <?= $animal['status'] === 'adopted' ? 'selected' : '' ?>>Adopté</option>
                  </select>
                </div>

                <div class="col-12">
                  <label class="form-label">Description</label>
                  <textarea class="form-control" name="description" rows="4" 
                            placeholder="Décrivez le caractère, les habitudes, les besoins spéciaux..."><?= htmlspecialchars($animal['description'] ?? '') ?></textarea>
                </div>
              </div>

              <!-- Photos -->
              <h5 class="mb-3"><i class="fas fa-camera me-2"></i> Photos (3 maximum)</h5>
              <div class="row g-3 mb-4">
                <?php for ($i = 1; $i <= 3; $i++): ?>
                  <div class="col-md-4">
                    <label class="form-label">
                      Photo <?= $i ?> <?= $i === 1 ? '(principale)' : '(optionnelle)' ?>
                    </label>
                    <div class="photo-preview <?= !empty($animal["photo{$i}"]) ? 'has-image' : '' ?>" 
                         id="preview<?= $i ?>">
                      <?php if (!empty($animal["photo{$i}"])): ?>
                        <img src="<?= htmlspecialchars($animal["photo{$i}"]) ?>" alt="Photo <?= $i ?>">
                        <button type="button" class="btn btn-sm btn-danger delete-photo-btn" 
                                onclick="deletePhoto(<?= $i ?>, <?= $animal['id'] ?>)">
                          <i class="fas fa-trash"></i>
                        </button>
                      <?php else: ?>
                        <span class="text-muted">
                          <i class="fas fa-image fa-2x mb-2 d-block"></i>
                          Cliquez pour ajouter
                        </span>
                      <?php endif; ?>
                    </div>
                    <input type="file" class="form-control mt-2" name="photo<?= $i ?>" accept="image/*" 
                           onchange="previewImage(this, 'preview<?= $i ?>')">
                    <input type="hidden" name="existing_photo<?= $i ?>" value="<?= htmlspecialchars($animal["photo{$i}"] ?? '') ?>">
                  </div>
                <?php endfor; ?>
              </div>

              <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <small>
                  Formats acceptés: JPG, PNG, WebP. Taille max: 5 Mo par photo.
                  Laissez vide pour conserver les photos existantes.
                </small>
              </div>

              <!-- Boutons -->
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                  <i class="fas fa-save me-1"></i> Enregistrer les modifications
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

function deletePhoto(photoNum, animalId) {
  if (confirm('Voulez-vous vraiment supprimer cette photo ?')) {
    fetch('/backend/animals/delete_photo.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        animal_id: animalId,
        photo_num: photoNum
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const preview = document.getElementById('preview' + photoNum);
        preview.innerHTML = '<span class="text-muted"><i class="fas fa-image fa-2x mb-2 d-block"></i>Cliquez pour ajouter</span>';
        preview.classList.remove('has-image');
        document.querySelector('[name="existing_photo' + photoNum + '"]').value = '';
        alert('Photo supprimée avec succès');
      } else {
        alert('Erreur: ' + (data.error || 'Impossible de supprimer la photo'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Erreur lors de la suppression');
    });
  }
}

// Validation du formulaire
document.getElementById('editAnimalForm').addEventListener('submit', function(e) {
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
