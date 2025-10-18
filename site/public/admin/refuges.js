(function(){
  function post(action, id){
    const fd = new FormData();
    fd.append('action', action);
    fd.append('id', id);
    return fetch('/admin/refuges_action.php', { method:'POST', body: fd })
      .then(r => r.json());
  }

  function rowSetStatus(tr, status){
    const badge = tr.querySelector('.badge');
    if(!badge) return;
    const map = { pending:'warning', active:'success', banned:'danger' };
    badge.className = 'badge bg-' + (map[status] || 'secondary');
    badge.textContent = status;

    // Affichage des boutons selon le statut
    const btnValidate = tr.querySelector('.action-validate');
    const btnBan = tr.querySelector('.action-ban');
    if(btnValidate) btnValidate.style.display = (status === 'pending') ? 'inline-block' : 'none';
    if(btnBan)      btnBan.style.display      = (status !== 'banned')  ? 'inline-block' : 'none';
  }

  document.addEventListener('click', async function(e){
    const btn = e.target.closest('.action-validate, .action-ban, .action-delete');
    if(!btn) return;

    const tr = btn.closest('tr[data-id]');
    if(!tr) return;
    const id = parseInt(tr.dataset.id, 10);
    if(!id) return;

    let action = 'validate';
    if(btn.classList.contains('action-ban')) action = 'ban';
    if(btn.classList.contains('action-delete')) action = 'delete';

    if(action === 'ban' && !confirm('Confirmer le bannissement ?')) return;
    if(action === 'delete' && !confirm('Supprimer définitivement ce refuge ?')) return;

    btn.disabled = true;
    try{
      const res = await post(action, id);
      if(!res || res.ok !== true){
        alert(res && res.msg ? res.msg : 'Erreur lors de la requête.');
        return;
      }
      if(action === 'delete'){
        tr.remove();
      } else if(action === 'ban'){
        rowSetStatus(tr, 'banned');
      } else if(action === 'validate'){
        rowSetStatus(tr, 'active');
      }
    }catch(err){
      console.error(err);
      alert('Erreur réseau.');
    }finally{
      btn.disabled = false;
    }
  });
})();
