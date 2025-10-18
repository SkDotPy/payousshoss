<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /login.php'); exit; }

$uid    = (int)($_SESSION['user_id'] ?? 0);
$email  = $_SESSION['email'] ?? '';
$nom    = $_SESSION['nom'] ?? '';
$age    = isset($_SESSION['age']) ? (int)$_SESSION['age'] : null;
$role   = $_SESSION['role'] ?? 'user';
$status = $_SESSION['status'] ?? 'active';
$is_super = (int)($_SESSION['is_super_admin'] ?? 0);
$ts = time();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil - PawConnect</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="/assets/css/main.css?v=1" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="/index.php"><i class="fas fa-paw"></i> PawConnect</a>
    <div class="ms-auto">
      <a href="/logout.php" class="btn btn-outline-secondary"><i class="fas fa-sign-out-alt me-1"></i> Déconnexion</a>
    </div>
  </div>
</nav>

<section class="py-4">
  <div class="container">
    <?php if (!empty($_GET['updated'])): ?>
      <div class="alert alert-success">Profil mis à jour.</div>
    <?php endif; ?>

    <div class="row g-4">
      <div class="col-lg-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-3">Avatar</h5>
            <div class="d-flex align-items-center gap-3 mb-3">
              <img id="avatarPreview"
                   src="/uploads/avatars/<?= $uid ?>/profile.webp?ts=<?= $ts ?>"
                   onerror="this.src='/assets/img/default-avatar.png'"
                   alt="Avatar"
                   style="width:120px;height:120px;border-radius:50%;object-fit:cover">
              <div class="flex-fill">
                <form id="avatarForm" action="/backend/user/update-avatar.php" method="POST" enctype="multipart/form-data">
                  <input type="hidden" name="from_form" value="1">
                  <input type="file" name="avatar" accept="image/*" class="form-control mb-2" required>
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-upload me-1"></i> Enregistrer</button>
                </form>
              </div>
            </div>
            <div class="small text-muted">PNG, JPG ou WebP, 5 Mo max. Recadrage carré automatique.</div>
          </div>
        </div>

        <div class="card shadow-sm mt-4">
          <div class="card-body">
            <h5 class="card-title mb-3">Informations</h5>
            <ul class="list-unstyled mb-0">
              <li><strong>ID :</strong> <?= $uid ?></li>
              <li><strong>Email :</strong> <?= htmlspecialchars($email) ?></li>
              <li><strong>Nom :</strong> <?= htmlspecialchars($nom) ?></li>
              <li><strong>Âge :</strong> <?= $age !== null ? (int)$age : '' ?></li>
              <li><strong>Rôle :</strong> <?= htmlspecialchars($role) ?></li>
              <li><strong>Statut :</strong> <?= htmlspecialchars($status) ?></li>
              <li><strong>Super admin :</strong> <?= $is_super ? 'oui' : 'non' ?></li>
            </ul>
          </div>
        </div>

        <?php if ($is_super === 1): ?>
        <div class="card shadow-sm mt-4">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title mb-0">Espace Super Admin</h5>
              <div class="text-muted small">Valider les refuges en attente</div>
            </div>
            <a href="/admin/refuges.php" class="btn btn-outline-primary">
              <i class="fas fa-shield-alt me-1"></i> Ouvrir
            </a>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-3">Modifier le profil</h5>
            <form id="updateInfoForm" action="/backend/user/update-info.php" method="POST" class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nom</label>
                <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Âge</label>
                <input type="number" min="0" class="form-control" name="age" value="<?= $age !== null ? (int)$age : '' ?>" required>
              </div>
              <div class="col-12 d-grid">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Enregistrer</button>
              </div>
            </form>
          </div>
        </div>

        <div class="card shadow-sm mt-4">
          <div class="card-body">
            <h5 class="card-title mb-3">Sécurité</h5>
            <form id="passwordForm" action="/backend/user/update-password.php" method="POST" class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" class="form-control" name="new_password" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Confirmer</label>
                <input type="password" class="form-control" name="confirm_password" required>
              </div>
              <div class="col-12 d-grid">
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-key me-1"></i> Mettre à jour</button>
              </div>
            </form>
          </div>
        </div>

        <div class="card shadow-sm mt-4">
          <div class="card-body">
            <h5 class="card-title text-danger mb-3">Supprimer le compte</h5>
            <p class="text-muted mb-3">Action irréversible. Toutes vos données seront supprimées.</p>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
              <i class="fas fa-trash me-1"></i> Supprimer mon compte
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> Supprimer le compte</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Attention :</strong> Cette action est irréversible !</p>
        <p>Toutes vos données, adoptions et historiques seront définitivement supprimés.</p>
        <p>Tapez <code>SUPPRIMER</code> pour confirmer :</p>
        <input type="text" class="form-control" id="deleteConfirm" placeholder="SUPPRIMER">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-dark" id="confirmDelete" disabled>Supprimer définitivement</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('deleteConfirm')?.addEventListener('input', function(e){
  document.getElementById('confirmDelete').disabled = e.target.value !== 'SUPPRIMER';
});
document.getElementById('confirmDelete')?.addEventListener('click', function(){
  if(confirm('Êtes-vous absolument certain de vouloir supprimer votre compte ?')){
    window.location.href = '/backend/user/delete-account.php';
  }
});
</script>
</body>
</html>
