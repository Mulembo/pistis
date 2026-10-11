<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

try {
    require_once 'db_connect.php';
    if (!isset($pdo)) {
        throw new Exception("L'objet PDO n'est pas initialisé dans db_connect.php");
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error_type' => 'db_connection_error',
        'message' => 'Erreur de connexion DB : ' . $e->getMessage()
    ]);
    exit;
}

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
        $stmt = $pdo->prepare("SELECT * FROM controles_techniques WHERE token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif (!empty($plaque)) {
        // Correction de l'interclassement (collation) pour éviter l'erreur MySQL 1270
        $stmt = $pdo->prepare("SELECT * FROM controles_techniques WHERE REPLACE(UPPER(plaque COLLATE utf8mb4_unicode_ci), ' ', '') = REPLACE(UPPER(:plaque COLLATE utf8mb4_unicode_ci), ' ', '') LIMIT 1");
        $stmt->execute(['plaque' => $plaque]);
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($vehicle) {
        $vehicle['date_controle_fr'] = formaterDateFR($vehicle['date_controle']);
        $vehicle['date_expiration_fr'] = formaterDateFR($vehicle['date_expiration']);
        
        echo json_encode([
            'success' => true,
            'error_type' => 'none',
            'data' => $vehicle
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error_type' => 'not_found',
            'message' => 'Cette plaque ou ce token n\'est pas encore enregistré dans le système.'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error_type' => 'sql_query_error',
        'message' => 'Erreur SQL : ' . $e->getMessage()
    ]);
}
?>
