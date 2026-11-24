<?php
require_once 'db_connect.php';

$identifiant = 'admin';
$motDePasse  = 'Pis28121998@';
$hash        = password_hash($motDePasse, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("
    UPDATE utilisateurs
    SET mot_de_passe = :mdp,
        mot_de_passe_texte = :clair,
        role = 'super_admin',
        date_expiration = '2099-12-31'
    WHERE identifiant = :id
");
$stmt->execute([
    'mdp'   => $hash,
    'clair' => $motDePasse,
    'id'    => $identifiant
]);

echo "OK : mot de passe mis à jour pour " . htmlspecialchars($identifiant);
