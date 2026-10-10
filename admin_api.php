<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé. Veuillez vous connecter.']);
    exit;
}

require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plaque = strtoupper(trim($_POST['plaque'] ?? ''));
    $proprietaire = trim($_POST['proprietaire'] ?? '');
    $marque = trim($_POST['marque'] ?? '');
    $modele = trim($_POST['modele'] ?? '');
    $centre_controle = trim($_POST['centre_controle'] ?? '');
    $date_controle = $_POST['date_controle'] ?? '';
    $date_expiration = $_POST['date_expiration'] ?? '';
    $numero_vignette = trim($_POST['numero_vignette'] ?? '');

    if (empty($plaque) || empty($proprietaire) || empty($marque) || empty($modele) || empty($centre_controle) || empty($date_controle) || empty($date_expiration) || empty($numero_vignette)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires.']);
        exit;
    }

    // VÉRIFICATION STRICTE DES DOUBLONS DE PLAQUE
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM controles_techniques WHERE REPLACE(plaque, ' ', '') = REPLACE(:plaque, ' ', '')");
    $checkStmt->execute(['plaque' => $plaque]);
    
    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode([
            'success' => false, 
            'message' => "Impossible d'ajouter : Le véhicule avec la plaque '$plaque' existe déjà dans la base de données !"
        ]);
        exit;
    }

    // INSERTION EN BASE DE DONNÉES SI NOUVEAU VÉHICULE
    try {
        $insertStmt = $pdo->prepare("INSERT INTO controles_techniques 
            (plaque, proprietaire, marque, modele, centre_controle, date_controle, date_expiration, numero_vignette, statut) 
            VALUES (:plaque, :proprietaire, :marque, :modele, :centre, :date_c, :date_e, :vignette, 'valide')");

        $insertStmt->execute([
            'plaque' => $plaque,
            'proprietaire' => $proprietaire,
            'marque' => $marque,
            'modele' => $modele,
            'centre' => $centre_controle,
            'date_c' => $date_controle,
            'date_e' => $date_expiration,
            'vignette' => $numero_vignette
        ]);

        echo json_encode([
            'success' => true, 
            'message' => "Le véhicule immatriculé $plaque a été ajouté avec succès au registre !"
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Erreur d'enregistrement : " . $e->getMessage()]);
    }
}