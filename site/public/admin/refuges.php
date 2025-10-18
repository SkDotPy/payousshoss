<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || (int)($_SESSION['is_super_admin'] ?? 0) !== 1) {
  http_response_code(403);
  die('Accès refusé');
}

try {
  $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);

  $users = $pdo->query("SELECT id, nom, email, role, status, is_super_admin, created_at FROM users ORDER BY created_at DESC")->fetchAll();
  $captchas = $pdo->query("SELECT id, question, answer, is_active, created_at FROM captcha_questions ORDER BY created_at DESC")->fetchAll();
  $subs = $pdo->query("SELECT id, email, created_at FROM newsletter_subscribers ORDER BY created_at DESC")->fetchAll();

} catch (Throwable $e) {
  http_response_code(500);
  die('Erreur DB: '.$e->getMessage());
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Super Admin — Tableau complet</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Panneau Super Admin</h1>
    <a href="/profil.php" class="btn btn-outline-secondary">Retour profil</a>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <h5>Comptes utilisateurs & refuges</h5>
      <div class="table-responsive mt-3">
        <table class="table table-striped align-middle">
          <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Type</th>
            <th>Statut</th>
            <th>Créé le</th>
            <th class="text-end">Actions</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($users as $u): ?>
            <tr data-id="<?= (int)$u['id'] ?>">
              <td><?= (int)$u['id'] ?></td>
              <td><?= htmlspecialchars($u['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= ($u['role'] ?? '') === 'refuge' ? 'Refuge' : 'Utilisateur' ?></td>
              <td>
                <?php
                  $s = $u['status'] ?? 'unknown';
                  $map = ['pending'=>'warning','active'=>'success','banned'=>'danger'];
                  $color = $map[$s] ?? 'secondary';
                ?>
                <span class="badge bg-<?= $color ?>"><?= htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?></span>
              </td>
              <td><?= htmlspecialchars($u['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td class="text-end">
                <?php $isBanned = (($u['status'] ?? '') === 'banned'); ?>
                <button class="btn btn-sm <?= $isBanned ? 'btn-warning' : 'btn-outline-warning' ?> action-ban"
                        data-id="<?= (int)$u['id'] ?>"
                        data-current="<?= $isBanned ? 'banned' : 'active' ?>">
                  <?= $isBanned ? 'Débannir' : 'Bannir' ?>
                </button>
                <button class="btn btn-sm btn-outline-danger action-del ms-1"
                        data-id="<?= (int)$u['id'] ?>">
                  Supprimer
                </button>
                <?php if (($u['status'] ?? '') === 'pending'): ?>
                  <button class="btn btn-sm btn-success action-validate ms-1"
                          data-id="<?= (int)$u['id'] ?>">
                    Valider
                  </button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <h5>Gestion des captchas</h5>
        <a class="btn btn-outline-primary btn-sm" href="/admin/captcha.php">Ouvrir la gestion</a>
      </div>
      <div class="table-responsive mt-3">
        <table class="table table-sm table-striped">
          <thead>
          <tr>
            <th>ID</th>
            <th>Question</th>
            <th>Réponse</th>
            <th>Actif</th>
            <th>Créé le</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($captchas as $c): ?>
            <tr>
              <td><?= (int)$c['id'] ?></td>
              <td><?= htmlspecialchars($c['question'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($c['answer'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= (int)$c['is_active'] === 1 ? 'Oui' : 'Non' ?></td>
              <td><?= htmlspecialchars($c['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <h5>Gestion des newsletters</h5>
      <form id="sendMailForm" class="row g-2 mt-1">
        <div class="col-md-4">
          <input type="text" name="subject" class="form-control" placeholder="Objet" required>
        </div>
        <div class="col-md-6">
          <input type="text" name="body" class="form-control" placeholder="Contenu (texte brut)" required>
        </div>
        <div class="col-md-2 d-grid">
          <button class="btn btn-primary">Envoyer</button>
        </div>
      </form>
      <div id="mailResult" class="mt-2"></div>

      <div class="table-responsive mt-3">
        <table class="table table-sm table-striped align-middle">
          <thead>
          <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Inscrit le</th>
            <th class="text-end">Actions</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($subs as $s): ?>
            <tr data-id="<?= (int)$s['id'] ?>">
              <td><?= (int)$s['id'] ?></td>
              <td><?= htmlspecialchars($s['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($s['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-danger action-del-sub" data-id="<?= (int)$s['id'] ?>">Supprimer</button>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
async function post(url, data) {
  const fd = new FormData();
  for (const k in data) fd.append(k, data[k]);
  const r = await fetch(url, { method: 'POST', body: fd });
  try { return await r.json(); } catch { return { ok:false, error:'Réponse invalide' }; }
}

document.addEventListener('click', async (e) => {
  const bBan = e.target.closest('.action-ban');
  if (bBan) {
    const tr = bBan.closest('tr');
    const id = tr?.dataset.id;
    if (!id) return;
    if (!confirm('Confirmer le basculement bannir/débannir ?')) return;
    bBan.disabled = true;
    const res = await post('/backend/admin/toggle_ban.php', { id });
    if (!res.ok) { alert(res.error || 'Erreur'); bBan.disabled = false; return; }
    const badge = tr.querySelector('span.badge');
    if (res.status === 'banned') {
      if (badge) { badge.className = 'badge bg-danger'; badge.textContent = 'banned'; }
      bBan.textContent = 'Débannir';
      bBan.classList.remove('btn-outline-warning');
      bBan.classList.add('btn-warning');
    } else {
      if (badge) { badge.className = 'badge bg-success'; badge.textContent = 'active'; }
      bBan.textContent = 'Bannir';
      bBan.classList.add('btn-outline-warning');
      bBan.classList.remove('btn-warning');
    }
    bBan.disabled = false;
  }

  const bDel = e.target.closest('.action-del');
  if (bDel) {
    const tr = bDel.closest('tr');
    const id = tr?.dataset.id;
    if (!id) return;
    if (!confirm('Supprimer cet utilisateur ?')) return;
    bDel.disabled = true;
    const res = await post('/backend/admin/delete_user.php', { id });
    if (!res.ok) { alert(res.error || 'Erreur'); bDel.disabled = false; return; }
    tr.remove();
  }

  const bSub = e.target.closest('.action-del-sub');
  if (bSub) {
    const tr = bSub.closest('tr');
    const id = tr?.dataset.id;
    if (!id) return;
    if (!confirm('Supprimer cet abonné ?')) return;
    bSub.disabled = true;
    const res = await post('/backend/admin/delete_newsletter.php', { id });
    if (!res.ok) { alert(res.error || 'Erreur'); bSub.disabled = false; return; }
    tr.remove();
  }
});

document.querySelector('#sendMailForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const res = await fetch('/backend/admin/send_newsletter.php', { method: 'POST', body: fd });
  let data = {};
  try { data = await res.json(); } catch { data = { ok:false, error:'Réponse invalide' }; }
  const box = document.querySelector('#mailResult');
  if (data.ok) box.innerHTML = '<div class="alert alert-success mb-0">Message envoyé.</div>';
  else box.innerHTML = '<div class="alert alert-danger mb-0">'+(data.error||'Erreur')+'</div>';
});
</script>
</body>
</html>
