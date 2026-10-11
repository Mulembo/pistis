<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panneau Super Administrateur</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: white; padding: 20px; margin: 0; }
    .container { max-width: 1000px; margin: auto; }
    .card { background: #1e293b; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #334155; }
    h2 { color: #38bdf8; margin-top: 0; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    input, select { width: 100%; padding: 10px; margin-bottom: 10px; background: #334155; border: 1px solid #475569; color: white; border-radius: 4px; box-sizing: border-box; }
    button { padding: 10px; background: #0284c7; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
    th, td { border: 1px solid #334155; padding: 10px; text-align: left; }
    th { background: #0f172a; color: #38bdf8; }
    .btn-del { background: #ef4444; width: auto; padding: 5px 10px; font-size: 12px; }
    .key-list-box { background: #0f172a; padding: 12px; margin-top: 10px; border-radius: 5px; max-height: 150px; overflow-y: auto; font-family: monospace; }
  </style>
</head>
<body>

<div class="container">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
    <h1>Panneau Super Administrateur</h1>
    <button onclick="logout()" style="background:#ef4444; width:auto;">Déconnexion</button>
  </div>

  <div class="grid">
    <!-- CRÉATION ADMIN -->
    <div class="card">
      <h2>Créer un Administrateur</h2>
      <form id="createAdminForm">
        <input type="text" id="newUsername" placeholder="Identifiant de connexion" required>
        <input type="text" id="newDisplayName" placeholder="Surnom / Nom d'affichage (ex: Franck)" required>
        <input type="password" id="newPassword" placeholder="Mot de passe" required>
        <button type="submit">Créer le Compte Admin</button>
      </form>
    </div>

    <!-- GÉNÉRATION CLÉS MULTIPLES -->
    <div class="card">
      <h2>Générer des Clés d'Activation</h2>
      <form id="genKeyForm">
        <select id="durationSelect">
          <option value="7_days">1 Semaine — Essai (Gratuit)</option>
          <option value="1_month">1 Mois — 10 $ USD</option>
          <option value="2_months">2 Mois — 15 $ USD</option>
        </select>
        <input type="number" id="keyQuantity" value="1" min="1" max="50" required placeholder="Nombre de clés">
        <button type="submit">Générer Clés</button>
      </form>

      <div id="keyBox" style="display: none;" class="key-list-box">
        <div id="keyDetails" style="font-size: 11px; color: #cbd5e1; margin-bottom: 5px;"></div>
        <ul id="generatedKeysList" style="list-style: none; padding: 0; margin: 0; color: #4ade80;"></ul>
      </div>
    </div>
  </div>

  <div class="card">
    <h2>Gestion des Administrateurs</h2>
    <table>
      <thead>
        <tr><th>ID</th><th>Identifiant</th><th>Surnom</th><th>Expiration</th><th>Action</th></tr>
      </thead>
      <tbody id="adminsTable"></tbody>
    </table>
  </div>

  <div class="card">
    <h2>Historique des Clés de Licence</h2>
    <table>
      <thead>
        <tr><th>Clé</th><th>Durée</th><th>Création</th><th>Date de Fin</th><th>Prix</th><th>Statut</th><th>Utilisée par</th></tr>
      </thead>
      <tbody id="keysTable"></tbody>
    </table>
  </div>
</div>

<script>
  const token = localStorage.getItem('token');
  if(!token) window.location.href = 'login.html';

  function formatDate(dateStr) {
    if(!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('fr-FR') + ' à ' + d.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'});
  }

  async function loadData() {
    const res = await fetch('api.php?action=superadmin-data', {
      headers: { 'Authorization': 'Bearer ' + token }
    });
    const data = await res.json();
    if(data.success) {
      document.getElementById('adminsTable').innerHTML = data.admins.map(a => `
        <tr>
          <td>${a.id}</td>
          <td>${a.username}</td>
          <td><b>Compte : ${a.display_name}</b></td>
          <td>${a.subscription_expires_at ? formatDate(a.subscription_expires_at) : '<span style="color:#ef4444;">Inactif</span>'}</td>
          <td><button class="btn-del" onclick="deleteAdmin(${a.id})">Supprimer</button></td>
        </tr>
      `).join('');

      document.getElementById('keysTable').innerHTML = data.keys.map(k => `
        <tr>
          <td style="font-family: monospace; color: #38bdf8;">${k.license_key}</td>
          <td>${k.duration_days} Jours</td>
          <td>${formatDate(k.created_at)}</td>
          <td><b style="color: #f59e0b;">${formatDate(k.expires_at)} fin</b></td>
          <td>${k.price_usd} $</td>
          <td><span style="color: ${k.is_used ? '#ef4444' : '#4ade80'};">${k.is_used ? 'UTILISÉE' : 'DISPONIBLE'}</span></td>
          <td>${k.used_by_name ? 'Compte : ' + k.used_by_name : '-'}</td>
        </tr>
      `).join('');
    }
  }

  document.getElementById('createAdminForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const res = await fetch('api.php?action=create-admin', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
      body: JSON.stringify({
        username: document.getElementById('newUsername').value,
        display_name: document.getElementById('newDisplayName').value,
        password: document.getElementById('newPassword').value
      })
    });
    const data = await res.json();
    alert(data.message);
    loadData();
  });

  document.getElementById('genKeyForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const res = await fetch('api.php?action=generate-key', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
      body: JSON.stringify({
        duration_type: document.getElementById('durationSelect').value,
        quantity: document.getElementById('keyQuantity').value
      })
    });
    const data = await res.json();
    if(data.success) {
      document.getElementById('keyBox').style.display = 'block';
      document.getElementById('keyDetails').innerHTML = `${data.quantity} clé(s) | Expire le : <b style="color:#f59e0b;">${data.expires_at} fin</b>`;
      document.getElementById('generatedKeysList').innerHTML = data.keys.map(k => `<li>${k}</li>`).join('');
      loadData();
    }
  });

  async function deleteAdmin(id) {
    if(confirm("Supprimer cet administrateur ?")) {
      await fetch('api.php?action=delete-admin', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
        body: JSON.stringify({ id })
      });
      loadData();
    }
  }

  function logout() { localStorage.clear(); window.location.href = 'login.html'; }
  loadData();
</script>

</body>
</html>
