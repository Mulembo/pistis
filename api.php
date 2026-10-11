<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once 'db_connect.php';

// Récupération du token ou de la plaque selon la requête
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$plaque = isset($_GET['plaque']) ? trim($_GET['plaque']) : '';

function formaterDateFR($dateString) {
    if (empty($dateString) || $dateString === '0000-00-00') return '-';
    $time = strtotime($dateString);
    return $time ? date('d/m/Y', $time) : $dateString;
}

try {
    $vehicle = false;

    if (!empty($token)) {
        // Recherche par token unique (Utilisé par le QR Code et scan.php)
        $stmt = $pdo->prepare("SELECT * FROM controles_techniques WHERE token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif (!empty($plaque)) {
        // Recherche par plaque (tolérante aux espaces et majuscules pour index.php)
        $stmt = $pdo->prepare("SELECT * FROM controles_techniques WHERE REPLACE(UPPER(plaque), ' ', '') = REPLACE(UPPER(:plaque), ' ', '') LIMIT 1");
        $stmt->execute(['plaque' => $plaque]);
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($vehicle) {
        // Formater les dates pour l'affichage tout en gardant les formats originaux
        $vehicle['date_controle_fr'] = formaterDateFR($vehicle['date_controle']);
        $vehicle['date_expiration_fr'] = formaterDateFR($vehicle['date_expiration']);
        
        echo json_encode([
            'status' => 'success',
            'success' => true,
            'data' => $vehicle
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'success' => false,
            'error_type' => 'not_found',
            'message' => 'Vignette ou véhicule non trouvé dans le système RFCK.'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'success' => false,
        'error_type' => 'db_error',
        'message' => 'Erreur lors de la requête MySQL.'
    ]);
}
?>
