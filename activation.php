<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$msg_err = ''; 
$msg_ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = strtoupper(trim($_POST['code_activation'] ?? ''));

    if (empty($code)) {
        $msg_err = "Veuillez saisir un code d'activation.";
    } else {
        // Recherche du code valide non utilisé
        $stmt = $pdo->prepare("SELECT * FROM cles_activation WHERE code = :code AND est_utilise = 0 LIMIT 1");
        $stmt->execute(['code' => $code]);
        $cle = $stmt->fetch();

        if ($cle) {
            $userId = $_SESSION['user_id'];
            $duree = intval($cle['duree_mois']);
            if ($duree < 1) { $duree = 1; }

            // Récupération de la date d'expiration actuelle de l'agent
            $stmtUser = $pdo->prepare("SELECT date_expiration FROM utilisateurs WHERE id = :id");
            $stmtUser->execute(['id' => $userId]);
            $curExp = $stmtUser->fetchColumn();

            $baseDate = ($curExp && $curExp > date('Y-m-d')) ? $curExp : date('Y-m-d');
            $newExp = date('Y-m-d', strtotime("{$baseDate} +{$duree} month"));

            // Marquer la clé comme utilisée
            $pdo->prepare("UPDATE cles_activation SET est_utilise = 1, utilise_par_user_id = :u, date_utilisation = NOW() WHERE id = :id")
                ->execute(['u' => $userId, 'id' => $cle['id']]);

            // Mettre à jour l'expiration du compte agent
            $pdo->prepare("UPDATE utilisateurs SET date_expiration = :exp WHERE id = :u")
                ->execute(['exp' => $newExp, 'u' => $userId]);

            $msg_ok = "Accès réactivé avec succès pour $duree mois (jusqu'au " . date('d/m/Y', strtotime($newExp)) . ") ! Redirection...";
            header("refresh:2;url=admin.php");
        } else {
            $msg_err = "Code d'activation invalide ou déjà utilisé.";
        }
    }
}

// Récupération de la date d'expiration
$stmtExp = $pdo->prepare("SELECT date_expiration FROM utilisateurs WHERE id = :id");
$stmtExp->execute(['id' => $_SESSION['user_id']]);
$date_actuelle = $stmtExp->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Abonnement Expiré | RFCK</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { 
      background: #f3f6fa; 
      font-family: 'Plus Jakarta Sans', sans-serif; 
      display: flex; 
      justify-content: center; 
      align-items: center; 
      min-height: 100vh; 
      margin: 0; 
      padding: 20px;
    }
    .card { 
      background: white; 
      padding: 32px 24px; 
      border-radius: 20px; 
      width: 100%;
      max-width: 440px; 
      text-align: center; 
      border: 1px solid #e2e8f0; 
      box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }
    .icon {
      width: 60px;
      height: 60px;
      background-color: #fef2f2;
      color: #dc2626;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px auto;
      font-size: 24px;
    }
    .title {
      font-size: 22px;
      font-weight: 800;
      color: #0b2341;
      margin-bottom: 8px;
    }
    .price-box {
      background-color: #eff6ff;
      border: 1px solid #bfdbfe;
      color: #1e40af;
      padding: 14px;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 700;
      margin: 16px 0 20px 0;
      line-height: 1.5;
    }
    .subtext {
      font-size: 13.5px;
      color: #64748b;
      line-height: 1.5;
      margin-bottom: 16px;
    }
    input { 
      width: 100%; 
      height: 50px; 
      border: 2px solid #cbd5e1; 
      border-radius: 12px; 
      text-align: center; 
      font-size: 17px; 
      font-weight: 800; 
      margin-bottom: 16px; 
      box-sizing: border-box; 
      text-transform: uppercase; 
      outline: none;
      letter-spacing: 1px;
    }
    input:focus {
      border-color: #024383;
    }
    button { 
      width: 100%; 
      height: 50px; 
      background: linear-gradient(180deg, #0252a3 0%, #003e7e 100%); 
      color: white; 
      border: none; 
      border-radius: 12px; 
      font-size: 15px;
      font-weight: 800; 
      cursor: pointer; 
    }
    .err { 
      color: #9f1239; 
      background: #fff1f2; 
      border: 1px solid #fecdd3; 
      padding: 10px; 
      border-radius: 8px; 
      font-size: 13px; 
      font-weight: 600; 
      margin-bottom: 16px; 
    }
    .ok { 
      color: #166534; 
      background: #f0fdf4; 
      border: 1px solid #bbf7d0; 
      padding: 10px; 
      border-radius: 8px; 
      font-size: 13px; 
      font-weight: 600; 
      margin-bottom: 16px; 
    }
  </style>
</head>
<body>

  <div class="card">
    <div class="icon">🔒</div>
    <h2 class="title">Abonnement Expiré</h2>
    
    <p class="subtext">
      Bonjour <strong><?= htmlspecialchars($_SESSION['nom_agent'] ?? '') ?></strong>.<br>
      Votre accès au système est actuellement suspendu.
    </p>

    <div class="price-box">
      Votre abonnement est expiré.<br>
      Veuillez le renouveler à :<br>
      👉 <strong>15 $ pour 1 mois</strong><br>
      👉 <strong>25 $ pour 2 mois</strong>
    </div>

    <p class="subtext">
      Une fois le règlement effectué auprès du Premier Administrateur, saisissez votre code d'activation ci-dessous :
    </p>

    <?php if ($msg_err): ?><div class="err"><?= $msg_err ?></div><?php endif; ?>
    <?php if ($msg_ok): ?><div class="ok"><?= $msg_ok ?></div><?php endif; ?>

    <form method="POST">
      <input type="text" name="code_activation" placeholder="RFCK-XXXX-XXXX" required autocomplete="off">
      <button type="submit">Valider et réactiver mon compte</button>
    </form>
  </div>

</body>
</html>