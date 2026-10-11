<?php
// setup_admin.php
require_once 'db.connect.php';

$username = 'pistis';
$password = 'Pis28121998@';
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

try {
    // Supprime l'ancien super administrateur
    $pdo->exec("DELETE FROM users WHERE role = 'SUPER_ADMIN'");

    // Insère le nouveau compte Super Admin
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, subscription_expires_at) VALUES (?, ?, 'SUPER_ADMIN', '2099-12-31 23:59:59')");
    $stmt->execute([$username, $hashedPassword]);

    echo "<h2 style='color:green;'>Compte Super Admin mis à jour avec succès !</h2>";
    echo "<p>Identifiant : <b>pistis</b><br>Mot de passe : <b>Pis28121998@</b></p>";
    echo "<a href='login.html'>Cliquer ici pour vous connecter</a>";
} catch (Exception $e) {
    echo "<h2 style='color:red;'>Erreur : " . $e->getMessage() . "</h2>";
}