<?php
session_start();
require_once 'db_connect.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = trim($_POST['identifiant'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($identifiant) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE identifiant = :id LIMIT 1");
        $stmt->execute(['id' => $identifiant]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['nom_agent'] = $user['nom_agent'];

            // Redirection selon le rôle
            if ($user['role'] === 'super_admin') {
                header('Location: super_admin.php');
            } else {
                header('Location: admin.php');
            }
            exit();
        } else {
            $erreur = "Identifiant ou mot de passe incorrect.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion | RFCK</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;800&display=swap" rel="stylesheet">
  <style>
    body { 
      background: #0b2341; 
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
      padding: 32px; 
      border-radius: 16px; 
      width: 100%; 
      max-width: 340px; 
      text-align: center; 
      box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
    }
    h2 { 
      color: #0b2341; 
      margin-bottom: 20px; 
      font-size: 22px; 
      font-weight: 800; 
    }
    input { 
      width: 100%; 
      height: 46px; 
      border: 1px solid #cbd5e1; 
      border-radius: 8px; 
      margin-bottom: 14px; 
      padding: 0 12px; 
      box-sizing: border-box; 
      font-size: 14px; 
      outline: none; 
    }
    input:focus { 
      border-color: #024383; 
    }
    button { 
      width: 100%; 
      height: 46px; 
      background: #024383; 
      color: white; 
      border: none; 
      border-radius: 8px; 
      font-weight: bold; 
      font-size: 15px; 
      cursor: pointer; 
    }
    .error { 
      color: #dc2626; 
      background: #fef2f2; 
      border: 1px solid #fecdd3; 
      padding: 10px; 
      border-radius: 8px; 
      font-size: 13px; 
      font-weight: bold; 
      margin-bottom: 14px; 
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Connexion RFCK</h2>
    <?php if ($erreur): ?>
      <div class="error"><?= $erreur ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="identifiant" placeholder="Identifiant" required autocomplete="off">
      <input type="password" name="password" placeholder="Mot de passe" required>
      <button type="submit">Se connecter</button>
    </form>
  </div>
</body>
</html>
