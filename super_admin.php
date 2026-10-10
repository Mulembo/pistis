<?php
session_start();
require_once 'db_connect.php';

// Sécurité : Seul le Super Admin a accès
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
    header('Location: login.php');
    exit();
}

// Adaptation automatique de la colonne SQL pour supporter les durées en semaines/mois
try {
    $pdo->exec("ALTER TABLE cles_activation MODIFY duree_mois VARCHAR(20) DEFAULT '1'");
} catch (Exception $e) {}

$msg_ok = ''; 
$msg_err = '';

// 1. ACTION : CRÉER UN AGENT
if (isset($_POST['creer_agent'])) {
    $nom = trim($_POST['nom_agent']);
    $id = trim($_POST['identifiant']);
    $pass = trim($_POST['mot_de_passe']);
    $mois = trim($_POST['mois_initial']);

    if (!empty($nom) && !empty($id) && !empty($pass)) {
        try {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            
            if ($mois === '1_semaine') {
                $date_exp = date('Y-m-d', strtotime("+7 days"));
                $libelle_duree = "1 semaine";
            } else {
                $m = intval($mois);
                $date_exp = date('Y-m-d', strtotime("+{$m} month"));
                $libelle_duree = "$m mois";
            }

            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_agent, identifiant, mot_de_passe, mot_de_passe_texte, role, date_expiration) VALUES (:n, :i, :h, :pt, 'agent', :de)");
            $stmt->execute(['n' => $nom, 'i' => $id, 'h' => $hash, 'pt' => $pass, 'de' => $date_exp]);
            $msg_ok = "L'agent <strong>" . htmlspecialchars($nom) . "</strong> a été créé avec succès ($libelle_duree d'accès) !";
        } catch (PDOException $e) {
            $msg_err = "Erreur : Cet identifiant est déjà utilisé.";
        }
    } else {
        $msg_err = "Veuillez remplir tous les champs.";
    }
}

// 2. ACTION : GÉNÉRER DES CLÉS
if (isset($_POST['generer_cles'])) {
    $nb = intval($_POST['nombre_cles']);
    $duree = trim($_POST['duree_mois']);

    for ($i = 0; $i < $nb; $i++) {
        $tokens = substr(md5(uniqid(mt_rand(), true)), 0, 8);
        $code = "RFCK-" . strtoupper(substr($tokens, 0, 4)) . "-" . strtoupper(substr($tokens, 4, 4));
        $stmt = $pdo->prepare("INSERT INTO cles_activation (code, duree_mois) VALUES (:c, :d)");
        $stmt->execute(['c' => $code, 'd' => $duree]);
    }
    
    $libelle_cle = ($duree === '1_semaine') ? "1 semaine" : "$duree mois";
    $msg_ok = "$nb clé(s) de $libelle_cle générée(s) avec succès !";
}

// 3. ACTION : DÉSACTIVER L'ABONNEMENT D'UN AGENT
if (isset($_GET['action']) && $_GET['action'] === 'desactiver' && isset($_GET['id'])) {
    $agent_id = intval($_GET['id']);
    $stmt = $pdo->prepare("UPDATE utilisateurs SET date_expiration = '2000-01-01' WHERE id = :id AND role = 'agent'");
    $stmt->execute(['id' => $agent_id]);
    $msg_ok = "L'abonnement de l'agent a été désactivé avec succès.";
}

// 4. ACTION : SUPPRIMER DÉFINITIVEMENT UN AGENT
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $agent_id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = :id AND role = 'agent'");
    $stmt->execute(['id' => $agent_id]);
    $msg_ok = "Le compte agent a été supprimé définitivement.";
}

// 5. ACTION : SUPPRIMER UNE CLÉ D'ACTIVATION
if (isset($_GET['action']) && $_GET['action'] === 'supprimer_cle' && isset($_GET['id'])) {
    $cle_id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM cles_activation WHERE id = :id");
    $stmt->execute(['id' => $cle_id]);
    $msg_ok = "La clé d'activation a été supprimée avec succès.";
}

// RÉCUPÉRATION DES DONNÉES
$agents = $pdo->query("SELECT * FROM utilisateurs WHERE role = 'agent' ORDER BY id DESC")->fetchAll();
$cles = $pdo->query("SELECT * FROM cles_activation WHERE est_utilise = 0 ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Premier Administrateur | RFCK</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f3f6fa; padding: 24px; margin: 0; color: #1e293b; }
    .container { max-width: 1080px; margin: 0 auto; }
    .card { background: white; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
    input, select { width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 10px; margin-bottom: 10px; box-sizing: border-box; font-size: 14px; }
    .btn { background: #024383; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #e2e8f0; padding: 10px; font-size: 13px; text-align: left; }
    th { background: #f8fafc; font-weight: 800; color: #0b2341; }
    .code { font-family: monospace; font-weight: bold; background: #eef5fc; color: #024383; padding: 3px 6px; border-radius: 4px; }
    
    .btn-action { display: inline-block; padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: bold; text-decoration: none; margin-right: 4px; }
    .btn-desactiver { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .btn-desactiver:hover { background-color: #fde68a; }
    .btn-supprimer { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .btn-supprimer:hover { background-color: #fecaca; }
    .badge-expired { color: #dc2626; font-weight: bold; background: #fef2f2; padding: 2px 6px; border-radius: 4px; }
    .badge-warning { color: #b45309; font-weight: bold; background: #fffbeb; padding: 2px 6px; border-radius: 4px; border: 1px solid #fcd34d; }
    .badge-active { color: #166534; font-weight: bold; background: #f0fdf4; padding: 2px 6px; border-radius: 4px; }
  </style>
</head>
<body>
<div class="container">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; background:white; padding:16px 20px; border-radius:12px; border:1px solid #e2e8f0;">
    <div>
      <h2 style="margin:0; color:#0b2341;">Panneau Premier Administrateur</h2>
      <span style="font-size:12px; color:#64748b;">Gestion des agents, expiration automatique et suppression de clés</span>
    </div>
    <a href="logout.php" style="color:#dc2626; font-weight:bold; text-decoration:none;">Déconnexion</a>
  </div>

  <?php if ($msg_ok): ?>
    <div style="background:#f0fdf4; color:#166534; padding:12px; border-radius:8px; margin-bottom:15px; font-weight:bold; border:1px solid #bbf7d0;"><?= $msg_ok ?></div>
  <?php endif; ?>
  <?php if ($msg_err): ?>
    <div style="background:#fff1f2; color:#9f1239; padding:12px; border-radius:8px; margin-bottom:15px; font-weight:bold; border:1px solid #fecdd3;"><?= $msg_err ?></div>
  <?php endif; ?>

  <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
    <!-- CRÉATION AGENT -->
    <div class="card">
      <h3 style="color:#024383; margin-top:0;">1. Créer un Agent</h3>
      <form method="POST">
        <input type="text" name="nom_agent" placeholder="Nom complet de l'agent" required>
        <input type="text" name="identifiant" placeholder="Identifiant" required autocomplete="off">
        <input type="text" name="mot_de_passe" placeholder="Mot de passe" required autocomplete="off">
        <select name="mois_initial">
          <option value="1_semaine">1 semaine offerte (Test alerte)</option>
          <option value="1">1 mois offert</option>
          <option value="2">2 mois offerts</option>
          <option value="0">0 mois (Bloqué immédiatement)</option>
        </select>
        <button type="submit" name="creer_agent" class="btn">Créer l'agent</button>
      </form>
    </div>

    <!-- GÉNÉRATION ET STOCK DE CLÉS -->
    <div class="card">
      <h3 style="color:#024383; margin-top:0;">2. Générer des clés (Stock)</h3>
      <form method="POST">
        <label style="font-size:12px; font-weight:bold; color:#475569;">Durée de la clé :</label>
        <select name="duree_mois">
          <option value="1_semaine">Clé de 1 Semaine (Test)</option>
          <option value="1">Clé de 1 Mois (15 $)</option>
          <option value="2">Clé de 2 Mois (25 $)</option>
        </select>

        <label style="font-size:12px; font-weight:bold; color:#475569;">Nombre de clés à créer :</label>
        <select name="nombre_cles">
          <option value="1">1 Clé</option>
          <option value="5">5 Clés</option>
          <option value="10">10 Clés</option>
        </select>
        <button type="submit" name="generer_cles" class="btn" style="background:#0077c8;">Générer les clés</button>
      </form>

      <h4 style="margin-top:15px; margin-bottom:8px;">Clés en stock avec calcul d'expiration :</h4>
      <div style="max-height:180px; overflow-y:auto;">
        <table>
          <thead>
            <tr>
              <th>Code</th>
              <th>Durée</th>
              <th>Expiration calculée</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cles as $c): 
              $time_creation = strtotime($c['date_creation']);
              $d_str = $c['duree_mois'];
              
              if ($d_str === '1_semaine') {
                  $date_expiration_calculee = date('d/m/Y', strtotime("+7 days", $time_creation));
                  $libelle_badge = "1 semaine";
              } else {
                  $m = intval($d_str);
                  if ($m < 1) $m = 1;
                  $date_expiration_calculee = date('d/m/Y', strtotime("+{$m} month", $time_creation));
                  $libelle_badge = "$m mois";
              }
            ?>
              <tr>
                <td><span class="code"><?= htmlspecialchars($c['code']) ?></span></td>
                <td><strong><?= $libelle_badge ?></strong></td>
                <td style="color:#024383; font-weight:bold;"><?= $date_expiration_calculee ?></td>
                <td>
                  <a href="super_admin.php?action=supprimer_cle&id=<?= $c['id'] ?>" 
                     class="btn-action btn-supprimer" 
                     onclick="return confirm('Voulez-vous vraiment supprimer cette clé d\'activation ?');">
                     Supprimer
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($cles)): ?>
              <tr><td colspan="4" style="text-align:center; color:#94a3b8;">Aucune clé disponible en stock.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- REPERTOIRE DES AGENTS -->
  <div class="card">
    <h3 style="color:#024383; margin-top:0;">3. Répertoire Général des Agents</h3>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>Nom</th>
            <th>Identifiant</th>
            <th>Mot de passe</th>
            <th>Expiration</th>
            <th>Statut & Alerte</th>
            <th style="width:160px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($agents as $a): 
            $today = new DateTime(date('Y-m-d'));
            $exp_dt = new DateTime($a['date_expiration']);
            $interval = $today->diff($exp_dt);
            $jours_restants = (int)$interval->format("%r%a");
          ?>
          <tr>
            <td><strong><?= htmlspecialchars($a['nom_agent']) ?></strong></td>
            <td><code><?= htmlspecialchars($a['identifiant']) ?></code></td>
            <td><span class="code" style="background:#f1f5f9; color:#334155;"><?= htmlspecialchars($a['mot_de_passe_texte'] ?? '-') ?></span></td>
            <td><strong><?= date('d/m/Y', strtotime($a['date_expiration'])) ?></strong></td>
            <td>
              <?php if ($jours_restants < 0): ?>
                <span class="badge-expired">Expiré</span>
              <?php elseif ($jours_restants <= 7): ?>
                <span class="badge-warning">⚠️ Alerte active (<?= $jours_restants == 0 ? "Aujourd'hui" : "$jours_restants j" ?>)</span>
              <?php else: ?>
                <span class="badge-active">Actif (<?= $jours_restants ?> j)</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($jours_restants >= 0): ?>
                <a href="super_admin.php?action=desactiver&id=<?= $a['id'] ?>" 
                   class="btn-action btn-desactiver" 
                   onclick="return confirm('Voulez-vous vraiment désactiver l\'abonnement de cet agent ?');">
                   Désactiver
                </a>
              <?php endif; ?>
              
              <a href="super_admin.php?action=supprimer&id=<?= $a['id'] ?>" 
                 class="btn-action btn-supprimer" 
                 onclick="return confirm('ÊTES-VOUS SÛR ? Cette action supprimera définitivement le compte de cet agent !');">
                 Supprimer
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($agents)): ?>
            <tr><td colspan="6" style="text-align:center; color:#94a3b8;">Aucun agent créé pour le moment.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</body>
</html>