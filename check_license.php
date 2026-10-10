<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Le Super Admin n'est jamais bloqué
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'super_admin') {
    return;
}

$stmt = $pdo->prepare("SELECT date_expiration FROM utilisateurs WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || date('Y-m-d') > $user['date_expiration']) {
    if (basename($_SERVER['PHP_SELF']) !== 'activation.php') {
        header('Location: activation.php');
        exit();
    }
}
?>