<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || (int)($_SESSION['is_super_admin'] ?? 0) !== 1) {
  http_response_code(403); die('Accès réservé aux super admins');
}

$err = $ok = '';
try {
  $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
      $q = trim($_POST['question'] ?? '');
      $a = trim($_POST['answer'] ?? '');
      if ($q === '' || $a === '') throw new RuntimeException('Champs requis.');
      $stmt = $pdo->prepare("INSERT INTO captcha_questions (question,answer,is_active,created_at) VALUES (?,?,1,NOW())");
      $stmt->execute([$q, $a]);
      $ok = 'Question ajoutée.';
    } elseif ($action === 'delete') {
      $id = (int)($_POST['id'] ?? 0);
      if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM captcha_questions WHERE id=?");
        $stmt->execute([$id]);
        $ok = 'Question supprimée.';
      }
    } elseif ($action === 'toggle') {
      $id = (int)($_POST['id'] ?? 0);
      $val = (int)($_POST['val'] ?? 0);
      if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE captcha_questions SET is_active=? WHERE id=?");
        $stmt->execute([$val ? 1 : 0, $id]);
        $ok = 'Statut mis à jour.';
      }
    }
  }

  $rows = $pdo->query("SELECT id,question,answer,is_active,created_at FROM captcha_questions ORDER BY id DESC")->fetchAll();
} catch (Throwable $e) {
  http_response_code(500); die('Erreur DB: '.$e->getMessage());
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Admin — Captchas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">Gestion des captchas</h1>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="/admin/refuges.php">Refuges & Utilisateurs</a>
        <a class="btn btn-outline-secondary" href="/profil.php">Retour profil</a>
      </div>
    </div>

    <?php if ($ok): ?><div class="alert alert-success py-2"><?= htmlspecialchars($ok,ENT_QUOTES,'UTF-8') ?></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($err,ENT_QUOTES,'UTF-8') ?></div><?php endif; ?>

    <div class="card mb-4">
      <div class="card-header">Ajouter une question</div>
      <div class="card-body">
        <form method="post" class="row g-3">
          <input type="hidden" name="action" value="add">
          <div class="col-md-7">
            <label class="form-label">Question</label>
            <input type="text" name="question" class="form-control" placeholder="Quelle est la capitale de la France ?" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Réponse</label>
            <input type="text" name="answer" class="form-control" placeholder="Paris" required>
          </div>
          <div class="col-md-2 d-grid">
            <label class="form-label">&nbsp;</label>
            <button class="btn btn-primary">Ajouter</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">Questions existantes</div>
      <div class="card-body table-responsive">
        <table class="table table-striped align-middle mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Question</th>
              <th>Réponse</th>
              <th>Active</th>
              <th>Créée le</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$rows): ?>
              <tr><td colspan="6" class="text-center text-muted">Aucune question.</td></tr>
            <?php else: foreach ($rows as $r): ?>
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= htmlspecialchars($r['question'],ENT_QUOTES,'UTF-8') ?></td>
                <td><code><?= htmlspecialchars($r['answer'],ENT_QUOTES,'UTF-8') ?></code></td>
                <td>
                  <form method="post" class="d-inline">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <input type="hidden" name="val" value="<?= $r['is_active'] ? 0 : 1 ?>">
                    <button class="btn btn-sm <?= $r['is_active'] ? 'btn-success' : 'btn-outline-secondary' ?>">
                      <?= $r['is_active'] ? 'Oui' : 'Non' ?>
                    </button>
                  </form>
                </td>
                <td><?= htmlspecialchars($r['created_at'] ?? '',ENT_QUOTES,'UTF-8') ?></td>
                <td class="text-end">
                  <form method="post" onsubmit="return confirm('Supprimer cette question ?');" class="d-inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <button class="btn btn-sm btn-danger">Supprimer</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
