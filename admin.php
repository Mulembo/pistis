<?php
require_once 'check_license.php';
require_once 'db_connect.php';

$msg = '';
$msg_ok_cle = '';
$msg_err_cle = '';
$recent_vehicle = null; 
$conflict_vehicle = null; 

// Fonction pour convertir JJ-MM-AA, JJ/MM/AAAA, JJ-MM-AAAA en AAAA-MM-JJ pour MySQL
function convertirDateVersMySQL($dateStr) {
    $dateStr = trim($dateStr);
    if (preg_match('/^(\d{1,2})[\-\/](\d{1,2})[\-\/](\d{2,4})$/', $dateStr, $matches)) {
        $jour = $matches[1];
        $mois = $matches[2];
        $annee = $matches[3];
        
        if (strlen($annee) === 2) {
            $annee = '20' . $annee;
        }
        
        return sprintf('%04d-%02d-%02d', $annee, $mois, $jour);
    }
    return $dateStr;
}

// 1. TRAITEMENT DU CODE DE RENOUVELLEMENT DEPUIS LA POP-UP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code_activation'])) {
    $code = strtoupper(trim($_POST['code_activation'] ?? ''));

    if (!empty($code)) {
        $stmt = $pdo->prepare("SELECT * FROM cles_activation WHERE code = :code AND est_utilise = 0 LIMIT 1");
        $stmt->execute(['code' => $code]);
        $cle = $stmt->fetch();

        if ($cle) {
            $userId = $_SESSION['user_id'];
            $duree_val = $cle['duree_mois'];

            $stmtUser = $pdo->prepare("SELECT date_expiration FROM utilisateurs WHERE id = :id");
            $stmtUser->execute(['id' => $userId]);
            $curExp = $stmtUser->fetchColumn();

            $baseDate = ($curExp && $curExp > date('Y-m-d')) ? $curExp : date('Y-m-d');
            
            if ($duree_val === '1_semaine') {
                $newExp = date('Y-m-d', strtotime("{$baseDate} +7 days"));
                $txt_duree = "1 semaine";
            } else {
                $m = intval($duree_val);
                if ($m < 1) $m = 1;
                $newExp = date('Y-m-d', strtotime("{$baseDate} +{$m} month"));
                $txt_duree = "$m mois";
            }

            $pdo->prepare("UPDATE cles_activation SET est_utilise = 1, utilise_par_user_id = :u, date_utilisation = NOW() WHERE id = :id")
                ->execute(['u' => $userId, 'id' =>$cle['id']]);

            $pdo->prepare("UPDATE utilisateurs SET date_expiration = :exp WHERE id = :u")
                ->execute(['exp' => $newExp, 'u' =>$userId]);

            $msg_ok_cle = "Votre serveur a été réactivé avec succès pour $txt_duree !";
        } else {
            $msg_err_cle = "Code d'activation invalide ou déjà utilisé.";
        }
    } else {
        $msg_err_cle = "Veuillez saisir un code d'activation.";
    }
}

// 2. SAISIE D'UN CONTRÔLE TECHNIQUE & VÉRIFICATION DES DOUBLONS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plaque'])) {
    $plaque = strtoupper(trim(str_replace(' ', '',$_POST['plaque'])));
    $proprietaire = trim($_POST['proprietaire']);
    $marque = trim($_POST['marque']);
    $modele = trim($_POST['modele']);
    $centre = trim($_POST['centre_controle']);
    
    // Conversion des dates saisies
    $date_ctrl = convertirDateVersMySQL(trim($_POST['date_controle']));$date_exp = convertirDateVersMySQL(trim($_POST['date_expiration']));$vignette = trim($_POST['numero_vignette']);$token_existant = $_POST['token_existant'] ?? '';$forcer_remplacement = isset($_POST['forcer_remplacement']) &&$_POST['forcer_remplacement'] === '1';

    try {
        $stmtCheck =$pdo->prepare("SELECT * FROM controles_techniques WHERE plaque = :p LIMIT 1");
        $stmtCheck->execute(['p' =>$plaque]);
        $existing =$stmtCheck->fetch();

        if ($existing && !$forcer_remplacement) {$conflict_vehicle = [
                'plaque' => $plaque,
                'proprietaire' => $proprietaire,
                'marque' => $marque,
                'modele' => $modele,
                'centre_controle' => $centre,
                'date_controle' => $_POST['date_controle'],
                'date_expiration' => $_POST['date_expiration'],
                'numero_vignette' => $vignette,
                'token' => $existing['token']
            ];
        } else {
            $token = !empty($token_existant) ? $token_existant : (($existing && !empty($existing['token'])) ?$existing['token'] : bin2hex(random_bytes(16)));

            // Enregistrement avec double écriture pour compatibilité totale (centre_controle / centre et numero_vignette / vignette_no)
            $stmt =$pdo->prepare("
                INSERT INTO controles_techniques 
                (plaque, proprietaire, marque, modele, centre_controle, centre, date_controle, date_expiration, numero_vignette, vignette_no, token)
                VALUES (:p, :prop, :m, :mod, :c, :c, :dc, :de, :v, :v, :t)
                ON DUPLICATE KEY UPDATE 
                proprietaire=VALUES(proprietaire), marque=VALUES(marque), modele=VALUES(modele),
                centre_controle=VALUES(centre_controle), centre=VALUES(centre),
                date_controle=VALUES(date_controle), date_expiration=VALUES(date_expiration), 
                numero_vignette=VALUES(numero_vignette), vignette_no=VALUES(vignette_no), token=VALUES(token)
            ");
            $stmt->execute([
                'p' => $plaque, 'prop' =>$proprietaire, 'm' => $marque, 'mod' =>$modele,
                'c' => $centre, 'dc' => $date_ctrl, 'de' =>$date_exp, 'v' => $vignette, 't' =>$token
            ]);
            
            $msg = "Véhicule $plaque enregistré avec succès !";
            
            $stmtGet =$pdo->prepare("SELECT * FROM controles_techniques WHERE plaque = :p LIMIT 1");
            $stmtGet->execute(['p' =>$plaque]);
            $recent_vehicle =$stmtGet->fetch();
        }

    } catch (PDOException $e) {$msg = "Erreur d'enregistrement : " . $e->getMessage();
    }
}

// Récupération de la date d'expiration de l'agent
$stmtExp =$pdo->prepare("SELECT date_expiration FROM utilisateurs WHERE id = :id");
$stmtExp->execute(['id' =>$_SESSION['user_id']]);
$date_expiration_agent =$stmtExp->fetchColumn();

// Calcul des jours restants
$aujourdhui = new DateTime(date('Y-m-d'));$date_exp_dt = new DateTime($date_expiration_agent);$interval = $aujourdhui->diff($date_exp_dt);
$jours_restants = (int)$interval->format("%r%a");

$vehicules =$pdo->query("SELECT * FROM controles_techniques ORDER BY id DESC LIMIT 20")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Saisie Contrôle Technique | Agent RFCK</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;800&display=swap" rel="stylesheet">
  <!-- Bibliothèque QRCode.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f3f6fa; padding: 24px; margin: 0; }
    .container { max-width: 980px; margin: 0 auto; }
    .card { background: white; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 20px; }
    input, select { width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 10px; margin-bottom: 12px; box-sizing: border-box; font-size: 14px; }
    .btn { background: #024383; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #e2e8f0; padding: 10px; font-size: 13px; text-align: left; vertical-align: middle; }
    th { background: #f8fafc; font-weight: 800; color: #0b2341; }
    
    /* STYLES DES POP-UPS */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(11, 35, 65, 0.7); backdrop-filter: blur(4px); display: flex; justify-content: center; align-items: center; z-index: 9999; padding: 20px; box-sizing: border-box; }
    .modal-card { background: white; border-radius: 20px; padding: 30px 24px; max-width: 440px; width: 100%; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.25); }
    .modal-icon { font-size: 40px; margin-bottom: 8px; }
    .modal-title { color: #0b2341; font-size: 20px; font-weight: 800; margin: 0 0 10px 0; }
    .modal-text { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 20px; }
    .modal-actions { display: flex; gap: 10px; justify-content: center; margin-top: 15px; }
    
    .btn-renew { background: #024383; color: white; border: none; padding: 12px 16px; border-radius: 10px; font-weight: bold; cursor: pointer; flex: 1; font-size: 14px; }
    .btn-ignore { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 12px 16px; border-radius: 10px; font-weight: bold; cursor: pointer; flex: 1; font-size: 14px; }
    
    .modal-input { width: 100%; height: 46px; border: 2px solid #024383; border-radius: 10px; text-align: center; font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; box-sizing: border-box; outline: none; }
    .btn-submit-code { width: 100%; height: 44px; background: #166534; color: white; border: none; border-radius: 10px; font-weight: bold; font-size: 14px; cursor: pointer; margin-bottom: 10px; }
    .err-box { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: bold; margin-bottom: 12px; }
    .ok-box { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: bold; margin-bottom: 15px; }
    
    /* Styles spécifiques Pop-up QR Code */
    .popup-qrcode-box { background: #fff; padding: 10px; border-radius: 10px; border: 1px solid #cbd5e1; display: inline-block; margin: 10px 0; }
    .btn-download-jpg { background-color: #024383; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; }
    .btn-download-jpg:hover { background-color: #003366; }
    .btn-close-modal { background-color: #64748b; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: bold; cursor: pointer; }
    .verify-link-popup { display: block; margin-top: 10px; font-size: 13px; color: #166534; font-weight: bold; text-decoration: underline; word-break: break-all; }
    
    /* Boutons de la pop-up de doublon */
    .btn-replace { background-color: #dc2626; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: bold; cursor: pointer; flex: 1; }
    .btn-cancel { background-color: #e2e8f0; color: #334155; border: none; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: bold; cursor: pointer; flex: 1; }

    .clickable-row { cursor: pointer; transition: background 0.2s; }
    .clickable-row:hover { background-color: #f8fafc; }
  </style>
</head>
<body>

  <!-- POP-UP ALERTE ABONNEMENT -->
  <?php if ($jours_restants >= 0 && $jours_restants <= 7 && empty($msg_ok_cle)): ?>
  <div id="renewalModal" class="modal-overlay">
    <div class="modal-card">
      <div class="modal-icon">⚠️</div>
      <h3 class="modal-title">Attention : Renouvellement Requis</h3>
      <p class="modal-text">
        Votre serveur expire le <strong><?= date('d/m/Y', strtotime($date_expiration_agent)) ?></strong> 
        (dans <strong><?= $jours_restants == 0 ? "aujourd'hui" : "$jours_restants jour(s)" ?></strong>).<br><br>
        Veuillez réactiver votre abonnement auprès de votre hébergeur dès maintenant afin d'éviter toute interruption.
      </p>

      <?php if (!empty($msg_err_cle)): ?>
        <div class="err-box"><?= $msg_err_cle ?></div>
      <?php endif; ?>

      <div id="codeFormArea" style="<?= !empty($msg_err_cle) ? 'display:block;' : 'display:none;' ?>">
        <form method="POST">
          <input type="text" name="code_activation" placeholder="RFCK-XXXX-XXXX" required class="modal-input" autocomplete="off">
          <button type="submit" class="btn-submit-code">Valider et Réactiver le Serveur</button>
        </form>
      </div>

      <div class="modal-actions" id="modalButtonsArea">
        <button type="button" class="btn-renew" onclick="showCodeInput()">Renouveler maintenant</button>
        <button type="button" class="btn-ignore" onclick="closeModal()">Ignorer</button>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- POP-UP AVERTISSEMENT DOUBLON (Remplacer / Annuler) -->
  <?php if ($conflict_vehicle): ?>
  <div id="conflictModal" class="modal-overlay" style="display:flex;">
    <div class="modal-card" style="max-width: 420px;">
      <div class="modal-icon">🔄</div>
      <h3 class="modal-title" style="color:#b45309;">Véhicule déjà enregistré</h3>
      <p class="modal-text">
        La plaque <strong><?= htmlspecialchars($conflict_vehicle['plaque']) ?></strong> existe déjà dans la base de données. Voulez-vous la remplacer avec ces nouvelles informations ?
      </p>

      <form method="POST">
        <input type="hidden" name="plaque" value="<?= htmlspecialchars($conflict_vehicle['plaque']) ?>">
        <input type="hidden" name="proprietaire" value="<?= htmlspecialchars($conflict_vehicle['proprietaire']) ?>">
        <input type="hidden" name="marque" value="<?= htmlspecialchars($conflict_vehicle['marque']) ?>">
        <input type="hidden" name="modele" value="<?= htmlspecialchars($conflict_vehicle['modele']) ?>">
        <input type="hidden" name="centre_controle" value="<?= htmlspecialchars($conflict_vehicle['centre_controle']) ?>">
        <input type="hidden" name="numero_vignette" value="<?= htmlspecialchars($conflict_vehicle['numero_vignette']) ?>">
        <input type="hidden" name="date_controle" value="<?= htmlspecialchars($conflict_vehicle['date_controle']) ?>">
        <input type="hidden" name="date_expiration" value="<?= htmlspecialchars($conflict_vehicle['date_expiration']) ?>">
        <input type="hidden" name="token_existant" value="<?= htmlspecialchars($conflict_vehicle['token']) ?>">
        <input type="hidden" name="forcer_remplacement" value="1">

        <div class="modal-actions">
          <button type="submit" class="btn-replace">Remplacer</button>
          <a href="admin.php" class="btn-cancel" style="display:inline-block; line-height:36px; text-decoration:none; text-align:center;">Annuler</a>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <!-- POP-UP D'AFFICHAGE DU CODE QR (Succès enregistrement ou clic tableau) -->
  <div id="qrModal" class="modal-overlay" style="<?= $recent_vehicle ? 'display:flex;' : 'display:none;' ?>">
    <div class="modal-card" style="max-width: 400px;">
      <h3 class="modal-title" style="color:#024383;">🔑 Code QR du Véhicule</h3>
      <p id="popup-vehicule-info" class="modal-text" style="margin-bottom:5px; font-weight:bold; color:#1e293b;">
        <?= $recent_vehicle ? "Plaque : " . htmlspecialchars($recent_vehicle['plaque']) : "" ?>
      </p>

      <div class="popup-qrcode-box" id="popup-qrcode-container"></div>

      <div>
        <a id="popup-verify-link" href="<?= $recent_vehicle ? 'scan.php?token=' . $recent_vehicle['token'] : '#' ?>" target="_blank" class="verify-link-popup">
          <?= $recent_vehicle ? '🔍 Vérifier dans scan.php (Lien direct)' : '' ?>
        </a>
      </div>

      <div class="modal-actions">
        <button type="button" id="popup-download-btn" class="btn-download-jpg" onclick="downloadPopupQR()">📥 Enregistrer JPG</button>
        <button type="button" class="btn-close-modal" onclick="closeQrModal()">Fermer</button>
      </div>
    </div>
  </div>

<div class="container">

  <div class="card" style="display:flex; justify-content:space-between; align-items:center;">
    <div>
      <h2 style="margin:0; color:#0b2341;">Espace Agent : <?= htmlspecialchars($_SESSION['nom_agent']) ?></h2>
      <span style="font-size:12px; color:#64748b;">Accès actif jusqu'au : <strong><?= date('d/m/Y', strtotime($date_expiration_agent)) ?></strong></span>
    </div>
    <a href="logout.php" style="color:#dc2626; font-weight:bold; text-decoration:none;">Déconnexion</a>
  </div>

  <?php if ($msg_ok_cle): ?>
    <div class="ok-box"><?= $msg_ok_cle ?></div>
  <?php endif; ?>

  <?php if ($msg): ?>
    <div style="background:#f0fdf4; color:#166534; padding:12px; border-radius:8px; margin-bottom:15px; font-weight:bold; border:1px solid #bbf7d0;"><?= $msg ?></div>
  <?php endif; ?>

  <div class="card">
    <h3 style="color:#024383; margin-top:0;">Enregistrer un contrôle technique & Générer le QR Code</h3>
    <form method="POST">
      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
        <input type="text" name="plaque" placeholder="Plaque (ex: 5409BG01)" required>
        <input type="text" name="proprietaire" placeholder="Propriétaire" required>
        <input type="text" name="marque" placeholder="Marque" required>
        <input type="text" name="modele" placeholder="Modèle" required>
        <input type="text" name="centre_controle" value="CENTRE RFCK" required>
        <input type="text" name="numero_vignette" placeholder="N° Vignette (ex: RFCK-12345)" required>
        
        <!-- Saisie des dates avec filigrane -->
        <input type="text" name="date_controle" placeholder="Date contrôle (ex: 10-10-26)" required>
        <input type="text" name="date_expiration" placeholder="Date expiration (ex: 20-12-27)" required>
      </div>
      <button type="submit" class="btn">Enregistrer et générer le QR Code</button>
    </form>
  </div>

  <div class="card">
    <h3 style="color:#024383; margin-top:0;">Dernières saisies (Cliquez sur une ligne pour voir le QR Code)</h3>
    <table>
      <thead>
        <tr>
          <th>Plaque</th>
          <th>Propriétaire</th>
          <th>Marque / Modèle</th>
          <th>Expiration</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vehicules as $v):$protocol = isset($_SERVER['HTTPS']) &&$_SERVER['HTTPS'] === 'on' ? "https" : "http";
          $host = $_SERVER['HTTP_HOST'];$path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');$scan_url = "$protocol://$host$path/scan.php?token=" . ($v['token'] ?? '');
        ?>
        <tr class="clickable-row" onclick="openQrModal('<?= htmlspecialchars($v['plaque']) ?>', '<?=$scan_url ?>')">
          <td><strong><?= htmlspecialchars($v['plaque']) ?></strong></td>
          <td><?= htmlspecialchars($v['proprietaire']) ?></td>
          <td><?= htmlspecialchars($v['marque']) ?> <?= htmlspecialchars($v['modele']) ?></td>
          <td><?= htmlspecialchars($v['date_expiration']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>

<script>
  let currentPlaque = '';

  <?php if ($recent_vehicle): ?>
    document.addEventListener("DOMContentLoaded", function() {
      const protocol = window.location.protocol;
      const host = window.location.host;
      const path = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
      const scanUrl = `${protocol}//${host}${path}/scan.php?token=<?= $recent_vehicle['token'] ?>`;
      
      openQrModal('<?= htmlspecialchars($recent_vehicle['plaque']) ?>', scanUrl);
    });
  <?php endif; ?>

  function openQrModal(plaque, scanUrl) {
    currentPlaque = plaque;
    document.getElementById('popup-vehicule-info').innerText = "Plaque : " + plaque;
    document.getElementById('popup-verify-link').href = scanUrl;
    document.getElementById('popup-verify-link').innerText = "🔍 Vérifier dans scan.php (Lien direct)";
    
    generatePopupQR(scanUrl);
    document.getElementById('qrModal').style.display = 'flex';
  }

  function closeQrModal() {
    document.getElementById('qrModal').style.display = 'none';
  }

  function generatePopupQR(url) {
    const container = document.getElementById('popup-qrcode-container');
    container.innerHTML = ""; 
    
    new QRCode(container, {
      text: url,
      width: 180,
      height: 180,
      colorDark : "#000000",
      colorLight : "#ffffff",
      correctLevel : QRCode.CorrectLevel.H
    });
  }

  async function downloadPopupQR() {
    const container = document.getElementById('popup-qrcode-container');
    const canvas = container.querySelector("canvas");
    const img = container.querySelector("img");

    let dataUrl = "";
    if (canvas) {
      dataUrl = canvas.toDataURL("image/jpeg", 1.0);
    } else if (img && img.src) {
      dataUrl = img.src;
    }

    if (!dataUrl) {
      alert("Le QR code est en cours de chargement. Veuillez patienter une seconde et réessayer.");
      return;
    }

    const response = await fetch(dataUrl);
    const blob = await response.blob();

    if ('showSaveFilePicker' in window) {
      try {
        const options = {
          suggestedName: 'QR-Code-Plaque-' + (currentPlaque || 'Vehicule') + '.jpg',
          types: [{
            description: 'Images JPEG',
            accept: { 'image/jpeg': ['.jpg'] },
          }],
        };
        const handle = await window.showSaveFilePicker(options);
        const writable = await handle.createWritable();
        await writable.write(blob);
        await writable.close();
        return;
      } catch (err) {
        if (err.name === 'AbortError') return; 
      }
    }

    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'QR-Code-Plaque-' + (currentPlaque || 'Vehicule') + '.jpg';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  function showCodeInput() {
    document.getElementById('codeFormArea').style.display = 'block';
  }
  
  function closeModal() {
    document.getElementById('renewalModal').style.display = 'none';
  }
</script>
</body>
</html>