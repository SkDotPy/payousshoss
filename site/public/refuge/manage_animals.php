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

// Vérifier que l'utilisateur est un refuge ou un super admin
if ($role !== 'refuge' && $is_super !== 1) {
    header('Location: /profil.php');
    exit;
}

try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Si super admin, afficher tous les animaux, sinon seulement ceux du refuge
    if ($is_super === 1) {
        $stmt = $pdo->prepare("
            SELECT a.*, u.nom as refuge_nom 
            FROM animals a 
            LEFT JOIN users u ON a.refuge_id = u.id 
            ORDER BY a.created_at DESC
        ");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT * FROM animals 
            WHERE refuge_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$uid]);
    }
    
    $animals = $stmt->fetchAll();

} catch (PDOException $e) {
    $error = "Erreur de connexion : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gérer mes animaux - PawConnect</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="/assets/css/main.css?v=1" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="/index.php"><i class="fas fa-paw"></i> PawConnect</a>
    <div class="ms-auto d-flex gap-2">
      <a href="/profil.php" class="btn btn-outline-secondary">
        <i class="fas fa-user me-1"></i> Profil
      </a>
      <a href="/logout.php" class="btn btn-outline-secondary">
        <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
      </a>
    </div>
  </div>
</nav>

<section class="py-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2><i class="fas fa-paw me-2"></i> Mes Animaux</h2>
      <a href="/refuge/add_animal.php" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Ajouter un animal
      </a>
    </div>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <?php
        switch ($_GET['success']) {
            case 'added':
                echo '<i class="fas fa-check-circle me-2"></i>Animal ajouté avec succès !';
                break;
            case 'updated':
                echo '<i class="fas fa-check-circle me-2"></i>Animal modifié avec succès !';
                break;
            case 'deleted':
                echo '<i class="fas fa-check-circle me-2"></i>Animal supprimé avec succès !';
                break;
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if (empty($animals)): ?>
      <div class="card shadow-sm text-center py-5">
        <div class="card-body">
          <i class="fas fa-paw fa-4x text-muted mb-3"></i>
          <h4>Aucun animal pour le moment</h4>
          <p class="text-muted mb-4">Commencez par ajouter votre premier animal !</p>
          <a href="/refuge/add_animal.php" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Ajouter un animal
          </a>
        </div>
      </div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($animals as $animal): ?>
          <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100">
              <?php
              $mainPhoto = $animal['photo1'] ?? '/assets/img/default-animal.png';
              ?>
              <img src="<?= htmlspecialchars($mainPhoto) ?>" 
                   class="card-img-top" 
                   alt="<?= htmlspecialchars($animal['name']) ?>"
                   style="height: 250px; object-fit: cover;"
                   onerror="this.src='/assets/img/default-animal.png'">
              
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <h5 class="card-title mb-0"><?= htmlspecialchars($animal['name']) ?></h5>
                  <?php
                  $statusBadge = [
                      'available' => '<span class="badge bg-success">Disponible</span>',
                      'reserved' => '<span class="badge bg-warning">Réservé</span>',
                      'adopted' => '<span class="badge bg-secondary">Adopté</span>'
                  ];
                  echo $statusBadge[$animal['status']] ?? '';
                  ?>
                </div>

                <div class="mb-2">
                  <small class="text-muted">
                    <i class="fas fa-tag me-1"></i> <?= htmlspecialchars($animal['species']) ?>
                    <?php if (!empty($animal['breed'])): ?>
                      - <?= htmlspecialchars($animal['breed']) ?>
                    <?php endif; ?>
                  </small>
                </div>

                <div class="mb-2">
                  <small class="text-muted">
                    <i class="fas fa-birthday-cake me-1"></i> <?= htmlspecialchars($animal['age']) ?>
                    <span class="ms-2">
                      <i class="fas fa-venus-mars me-1"></i> 
                      <?php
                      $sexIcons = [
                          'male' => 'Mâle',
                          'female' => 'Femelle',
                          'unknown' => 'Inconnu'
                      ];
                      echo $sexIcons[$animal['sex']] ?? 'Inconnu';
                      ?>
                    </span>
                  </small>
                </div>

                <?php if (!empty($animal['size'])): ?>
                <div class="mb-2">
                  <small class="text-muted">
                    <i class="fas fa-ruler me-1"></i> 
                    <?php
                    $sizes = [
                        'small' => 'Petit',
                        'medium' => 'Moyen',
                        'large' => 'Grand'
                    ];
                    echo $sizes[$animal['size']] ?? htmlspecialchars($animal['size']);
                    ?>
                  </small>
                </div>
                <?php endif; ?>

                <?php if ($is_super === 1 && !empty($animal['refuge_nom'])): ?>
                <div class="mb-2">
                  <small class="text-primary">
                    <i class="fas fa-home me-1"></i> <?= htmlspecialchars($animal['refuge_nom']) ?>
                  </small>
                </div>
                <?php endif; ?>

                <?php if (!empty($animal['description'])): ?>
                <p class="card-text small text-muted mb-3">
                  <?= nl2br(htmlspecialchars(substr($animal['description'], 0, 100))) ?>
                  <?php if (strlen($animal['description']) > 100): ?>...<?php endif; ?>
                </p>
                <?php endif; ?>
              </div>

              <div class="card-footer bg-white border-0 pt-0">
                <div class="d-flex gap-2">
                  <a href="/refuge/edit_animal.php?id=<?= $animal['id'] ?>" 
                     class="btn btn-sm btn-outline-primary flex-fill">
                    <i class="fas fa-edit me-1"></i> Modifier
                  </a>
                  <button class="btn btn-sm btn-outline-danger" 
                          onclick="confirmDelete(<?= $animal['id'] ?>, '<?= htmlspecialchars(addslashes($animal['name'])) ?>')">
                    <i class="fas fa-trash me-1"></i> Supprimer
                  </button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-exclamation-triangle text-danger me-2"></i>
          Confirmer la suppression
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer <strong id="animalNameDelete"></strong> ?</p>
        <p class="text-danger mb-0">Cette action est irréversible.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <form id="deleteForm" action="/backend/animals/delete.php" method="POST" style="display:inline;">
          <input type="hidden" name="animal_id" id="deleteAnimalId">
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash me-1"></i> Supprimer
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let deleteModal;
document.addEventListener('DOMContentLoaded', function() {
  deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
});

function confirmDelete(id, name) {
  document.getElementById('deleteAnimalId').value = id;
  document.getElementById('animalNameDelete').textContent = name;
  deleteModal.show();
}
</script>
</body>
</html>
