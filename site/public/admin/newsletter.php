<?php
session_start();
require_once __DIR__ . '/../backend/config.php';

if (!isset($_SESSION['user_id']) || (int)($_SESSION['is_super_admin'] ?? 0) !== 1) {
  http_response_code(403); die('Accès réservé aux super admins');
}

try {
  $pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  $subs = $pdo->query("SELECT id, email, created_at FROM newsletter ORDER BY created_at DESC")->fetchAll();
} catch (Throwable $e) {
  http_response_code(500); die('Erreur DB: '.$e->getMessage());
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Admin — Newsletter</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Gestion des newsletters</h1>
    <a class="btn btn-outline-secondary" href="/profil.php">Retour profil</a>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <h5>Envoyer un message à tous les abonnés</h5>
      <form id="sendMailForm" class="mt-3">
        <div class="mb-3">
          <label class="form-label">Sujet</label>
          <input type="text" class="form-control" name="subject" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Message</label>
          <textarea class="form-control" name="message" rows="5" required></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Envoyer à tous</button>
      </form>
      <div id="mailResult" class="mt-3"></div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <h5>Liste des abonnés (<?= count($subs) ?>)</h5>
      <div class="table-responsive mt-3">
        <table class="table table-striped">
          <thead>
            <tr><th>ID</th><th>Email</th><th>Inscrit le</th><th></th></tr>
          </thead>
          <tbody>
          <?php if (!$subs): ?>
            <tr><td colspan="4" class="text-center text-muted">Aucun abonné</td></tr>
          <?php else: foreach ($subs as $s): ?>
            <tr data-id="<?= (int)$s['id'] ?>">
              <td><?= (int)$s['id'] ?></td>
              <td><?= htmlspecialchars($s['email'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($s['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-danger action-del">Supprimer</button>
              </td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
document.querySelector('#sendMailForm')?.addEventListener('submit', async e => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const r = await fetch('/backend/admin/send_newsletter.php', {method:'POST', body: fd});
  const data = await r.json().catch(()=>({ok:false,error:'Réponse invalide'}));
  const div = document.querySelector('#mailResult');
  if (data.ok) div.innerHTML = '<div class="alert alert-success">✔ Message envoyé à tous les abonnés.</div>';
  else div.innerHTML = '<div class="alert alert-danger">❌ '+(data.error||'Erreur')+'</div>';
});

document.addEventListener('click', async e => {
  if (!e.target.classList.contains('action-del')) return;
  const tr = e.target.closest('tr[data-id]');
  const id = tr?.dataset.id;
  if (!id || !confirm('Supprimer cet abonné ?')) return;
  const fd = new FormData(); fd.append('id', id);
  const r = await fetch('/backend/admin/delete_newsletter.php', {method:'POST', body: fd});
  const data = await r.json().catch(()=>({ok:false,error:'Réponse invalide'}));
  if (data.ok) tr.remove(); else alert(data.error||'Erreur');
});
</script>
</body>
</html>
