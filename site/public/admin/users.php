<?php
session_start();
require_once __DIR__ . '/../backend/config.php';

if (!isset($_SESSION['user_id']) || (int)($_SESSION['is_super_admin'] ?? 0) !== 1) {
  http_response_code(403); die('Accès réservé aux super admins');
}
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

try {
  $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

  $rows = $pdo->query("
    SELECT id, nom, email, role, status, is_super_admin, created_at
    FROM users
    ORDER BY created_at DESC
  ")->fetchAll();
} catch (Throwable $e) {
  http_response_code(500); die('Erreur DB: '.$e->getMessage());
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Admin — Utilisateurs</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">Gestion des utilisateurs</h1>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="/profil.php">Retour profil</a>
        <a class="btn btn-outline-primary" href="/admin/refuges.php">Refuges</a>
        <a class="btn btn-outline-primary" href="/admin/captcha.php">Captchas</a>
      </div>
    </div>

    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-striped align-middle mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nom</th>
              <th>Email</th>
              <th>Rôle</th>
              <th>Statut</th>
              <th>SuperAdmin</th>
              <th>Créé le</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!$rows): ?>
            <tr><td colspan="8" class="text-center text-muted">Aucun utilisateur</td></tr>
          <?php else: foreach ($rows as $u): ?>
            <tr data-id="<?= (int)$u['id'] ?>">
              <td><?= (int)$u['id'] ?></td>
              <td><?= h($u['nom'] ?? '') ?></td>
              <td><?= h($u['email']) ?></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge bg-secondary text-uppercase"><?= h($u['role']) ?></span>
                  <select class="form-select form-select-sm w-auto role-select">
                    <?php
                      $roles = ['user'=>'Utilisateur','refuge'=>'Refuge','admin'=>'Admin'];
                      foreach ($roles as $val=>$label):
                    ?>
                      <option value="<?= h($val) ?>" <?= ($u['role']===$val?'selected':'') ?>><?= h($label) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button class="btn btn-sm btn-outline-secondary action-role">OK</button>
                </div>
              </td>
              <td>
                <?php
                  $badge = ['pending'=>'warning','active'=>'success','banned'=>'danger'];
                  $s = $u['status'] ?? 'unknown';
                  $color = $badge[$s] ?? 'secondary';
                ?>
                <span class="badge bg-<?= $color ?>"><?= h($s) ?></span>
              </td>
              <td><?= (int)$u['is_super_admin'] === 1 ? '<span class="badge bg-dark">oui</span>' : '—' ?></td>
              <td><?= h($u['created_at'] ?? '') ?></td>
              <td class="text-end">
                <?php if (($u['status'] ?? '') === 'pending' && $u['role'] === 'refuge'): ?>
                  <button class="btn btn-sm btn-success action-validate">Valider refuge</button>
                <?php endif; ?>

                <?php if (($u['status'] ?? '') !== 'banned'): ?>
                  <button class="btn btn-sm btn-warning action-ban">Bannir</button>
                <?php else: ?>
                  <button class="btn btn-sm btn-secondary action-unban">Débannir</button>
                <?php endif; ?>

                <button class="btn btn-sm btn-outline-danger action-delete">Supprimer</button>
              </td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<script>
async function callAction(userId, action, extra = {}) {
  const fd = new FormData();
  fd.append('user_id', userId);
  fd.append('action', action);
  Object.entries(extra).forEach(([k,v]) => fd.append(k, v));

  const r = await fetch('/backend/admin/user_action.php', { method:'POST', body: fd, credentials:'same-origin' });
  const data = await r.json().catch(()=>({ok:false,error:'Réponse invalide'}));
  if (!data.ok) throw new Error(data.error || 'Action refusée');
  location.reload();
}

document.addEventListener('click', async (e) => {
  const tr = e.target.closest('tr[data-id]');
  if (!tr) return;
  const id = tr.getAttribute('data-id');

  if (e.target.classList.contains('action-validate')) {
    e.preventDefault();
    try { await callAction(id, 'validate_refuge'); } catch(err){ alert(err.message); }
  }
  if (e.target.classList.contains('action-ban')) {
    e.preventDefault();
    if (!confirm('Bannir cet utilisateur ?')) return;
    try { await callAction(id, 'ban'); } catch(err){ alert(err.message); }
  }
  if (e.target.classList.contains('action-unban')) {
    e.preventDefault();
    try { await callAction(id, 'unban'); } catch(err){ alert(err.message); }
  }
  if (e.target.classList.contains('action-delete')) {
    e.preventDefault();
    if (!confirm('Supprimer définitivement cet utilisateur ?')) return;
    try { await callAction(id, 'delete'); } catch(err){ alert(err.message); }
  }
  if (e.target.classList.contains('action-role')) {
    e.preventDefault();
    const sel = tr.querySelector('.role-select');
    const newRole = sel?.value || 'user';
    try { await callAction(id, 'change_role', { new_role: newRole }); } catch(err){ alert(err.message); }
  }
});
</script>
</body>
</html>
