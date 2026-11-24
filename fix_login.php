<?php
// fixlogin.php (pour le site utilisant db.connect.php et la table users)
require_once 'db.connect.php';

$username    = 'pistis'; // Tu peux remplacer par 'admin' si tu préfères
$displayName = 'Super Admin';
$motDePasse  = 'Pis28121998@';
$role        = 'SUPER_ADMIN';
$dateExp     = '2099-12-31 23:59:59';

$hash = password_hash($motDePasse, PASSWORD_BCRYPT);

try {
    // Vérifie si l'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $existing = $stmt->fetch();

    if ($existing) {
        $update = $pdo->prepare("
            UPDATE users
            SET password = ?,
                display_name = ?,
                role = ?,
                subscription_expires_at = ?
            WHERE id = ?
        ");
        $update->execute([$hash, $displayName, $role, $dateExp, $existing['id']]);
        echo "Compte '$username' mis à jour avec succès !";
    } else {
        $insert = $pdo->prepare("
            INSERT INTO users (username, display_name, password, role, subscription_expires_at)
            VALUES (?, ?, ?, ?, ?)
        ");
        $insert->execute([$username, $displayName, $hash, $role, $dateExp]);
        echo "Compte '$username' créé avec succès !";
    }
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
```[cite: 1, 2, 7]

### Étapes d'utilisation

1. Téléverse `fixlogin.php` à côté de `db.connect.php` et vérifie que `$db` dans `db.connect.php` correspond bien au nom exact de ta base (`docverif_db` ou le nom préfixé sur ton hébergeur)[cite: 1, 2].
2. Ouvre `fixlogin.php` dans ton navigateur et vérifie que le message de succès (`Compte '...' créé/mis à jour avec succès !`) s'affiche sans erreur SQL.
3. **Supprime `fixlogin.php`** du serveur immédiatement après exécution, puis connecte-toi depuis `login.html`[cite: 5].
