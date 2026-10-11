<?php
// api.php
require_once 'db.connect.php';
require_once 'licence_key.php';

$action = $_GET['action'] ?? '';
$data = getJsonInput();

function authenticate($pdo) {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    $token = str_replace('Bearer ', '', $authHeader);

    if (!$token) {
        echo json_encode(["success" => false, "message" => "Accès refusé. Token manquant."]);
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE session_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(["success" => false, "message" => "Session expirée. Veuillez vous reconnecter."]);
        exit();
    }
    return $user;
}

// 1. CONNEXION
if ($action === 'login') {
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Veuillez remplir tous les champs."]);
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(["success" => false, "message" => "Identifiant ou mot de passe incorrect. Veuillez réessayer."]);
        exit();
    }

    $sessionToken = bin2hex(random_bytes(32));
    $stmt = $pdo->prepare("UPDATE users SET session_token = ? WHERE id = ?");
    $stmt->execute([$sessionToken, $user['id']]);

    $expiresAt = $user['subscription_expires_at'] ? strtotime($user['subscription_expires_at']) : 0;
    $now = time();
    $daysLeft = ceil(($expiresAt - $now) / 86400);

    $isExpired = ($user['role'] === 'ADMIN') && ($expiresAt < $now);
    $warningExpiringSoon = ($user['role'] === 'ADMIN') && (!$isExpired && $daysLeft <= 3);

    $displayName = !empty($user['display_name']) ? $user['display_name'] : $user['username'];

    echo json_encode([
        "success" => true,
        "token" => $sessionToken,
        "role" => $user['role'],
        "username" => $user['username'],
        "display_name" => $displayName,
        "subscription_expires_at" => $user['subscription_expires_at'],
        "isExpired" => $isExpired,
        "warningExpiringSoon" => $warningExpiringSoon,
        "daysLeft" => $daysLeft > 0 ? $daysLeft : 0
    ]);
    exit();
}

// 2. CREER UN ADMIN
if ($action === 'create-admin') {
    $admin = authenticate($pdo);
    if ($admin['role'] !== 'SUPER_ADMIN') {
        echo json_encode(["success" => false, "message" => "Accès réservé au Super Administrateur."]);
        exit();
    }

    $username = trim($data['username'] ?? '');
    $displayName = trim($data['display_name'] ?? '');
    $rawPassword = $data['password'] ?? '';

    if (empty($username) || empty($displayName) || empty($rawPassword)) {
        echo json_encode(["success" => false, "message" => "Veuillez renseigner l'identifiant, le surnom et le mot de passe."]);
        exit();
    }

    $hashedPassword = password_hash($rawPassword, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, display_name, password, role) VALUES (?, ?, ?, 'ADMIN')");
        $stmt->execute([$username, $displayName, $hashedPassword]);
        echo json_encode(["success" => true, "message" => "Administrateur '$displayName' créé avec succès !"]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Cet identifiant de connexion existe déjà."]);
    }
    exit();
}

// 3. SUPPRIMER UN ADMIN
if ($action === 'delete-admin') {
    $admin = authenticate($pdo);
    if ($admin['role'] !== 'SUPER_ADMIN') exit();

    $id = (int)($data['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'ADMIN'");
    $stmt->execute([$id]);

    echo json_encode(["success" => true, "message" => "Compte Administrateur supprimé."]);
    exit();
}

// 4. GENERER CLES MULTIPLES
if ($action === 'generate-key') {
    $admin = authenticate($pdo);
    if ($admin['role'] !== 'SUPER_ADMIN') exit();

    $type = $data['duration_type'] ?? '1_month';
    $quantity = max(1, (int)($data['quantity'] ?? 1));

    $days = LicenseManager::getDurationDays($type);
    $price = LicenseManager::getPrice($type);
    $createdAt = date('Y-m-d H:i:s');
    $expiresAt = LicenseManager::calculateExpiryDate($createdAt, $type);

    $generatedKeys = [];
    $stmt = $pdo->prepare("INSERT INTO license_keys (license_key, duration_days, expires_at, price_usd, created_at) VALUES (?, ?, ?, ?, ?)");

    for ($i = 0; $i < $quantity; $i++) {
        $key = LicenseManager::generateKey($type);
        $stmt->execute([$key, $days, $expiresAt, $price, $createdAt]);
        $generatedKeys[] = $key;
    }

    echo json_encode([
        "success" => true,
        "quantity" => count($generatedKeys),
        "keys" => $generatedKeys,
        "days" => $days,
        "price" => $price,
        "created_at" => date('d/m/Y H:i', strtotime($createdAt)),
        "expires_at" => date('d/m/Y H:i', strtotime($expiresAt))
    ]);
    exit();
}

// 5. SUPER ADMIN DATA
if ($action === 'superadmin-data') {
    $admin = authenticate($pdo);
    if ($admin['role'] !== 'SUPER_ADMIN') exit();

    $admins = $pdo->query("SELECT id, username, display_name, subscription_expires_at, created_at FROM users WHERE role = 'ADMIN'")->fetchAll();
    
    $keys = $pdo->query("
        SELECT l.*, COALESCE(u.display_name, u.username) as used_by_name 
        FROM license_keys l 
        LEFT JOIN users u ON l.used_by_user_id = u.id 
        ORDER BY l.id DESC
    ")->fetchAll();

    echo json_encode(["success" => true, "admins" => $admins, "keys" => $keys]);
    exit();
}

// 6. ACTIVATION CLÉ
if ($action === 'activate-key') {
    $user = authenticate($pdo);
    $licenseKey = trim($data['license_key'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM license_keys WHERE license_key = ? AND is_used = 0");
    $stmt->execute([$licenseKey]);
    $key = $stmt->fetch();

    if (!$key) {
        echo json_encode(["success" => false, "message" => "Clé invalide ou déjà utilisée."]);
        exit();
    }

    $currentExpiry = ($user['subscription_expires_at'] && strtotime($user['subscription_expires_at']) > time()) 
        ? strtotime($user['subscription_expires_at']) 
        : time();

    $newExpiry = date('Y-m-d H:i:s', strtotime("+{$key['duration_days']} days", $currentExpiry));

    $pdo->prepare("UPDATE users SET subscription_expires_at = ? WHERE id = ?")->execute([$newExpiry, $user['id']]);
    $pdo->prepare("UPDATE license_keys SET is_used = 1, used_by_user_id = ?, used_at = NOW() WHERE id = ?")->execute([$user['id'], $key['id']]);

    echo json_encode(["success" => true, "message" => "Abonnement activé avec succès !"]);
    exit();
}

// 7. CREER UN DOCUMENT ET GENERER URL
if ($action === 'add-document') {
    $user = authenticate($pdo);

    if (!$user['subscription_expires_at'] || strtotime($user['subscription_expires_at']) < time()) {
        echo json_encode(["success" => false, "isExpired" => true, "message" => "Votre abonnement a expiré."]);
        exit();
    }

    $token = bin2hex(random_bytes(16));

    $stmt = $pdo->prepare("
        INSERT INTO documents 
        (admin_id, token, autorite, immatriculation, chassis, remorque, nombre_places, date_effet, date_echeance) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $user['id'], 
        $token,
        $data['autorite'] ?? '', 
        $data['immatriculation'] ?? '', 
        $data['chassis'] ?? '',
        $data['remorque'] ?? 'Non', 
        $data['nombre_places'] ?? 2, 
        $data['date_effet'] ?? '', 
        $data['date_echeance'] ?? ''
    ]);

    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $qrUrl = "$protocol://$host" . rtrim($scriptDir, '/') . "/scan.html?token=$token";

    echo json_encode(["success" => true, "qrUrl" => $qrUrl, "token" => $token]);
    exit();
}

// 8. MES DOCUMENTS (HISTORIQUE ADMIN)
if ($action === 'my-documents') {
    $user = authenticate($pdo);

    $stmt = $pdo->prepare("SELECT id, admin_id, token, autorite, immatriculation, chassis, remorque, nombre_places, DATE_FORMAT(date_effet, '%d/%m/%Y') as date_effet, DATE_FORMAT(date_echeance, '%d/%m/%Y') as date_echeance FROM documents WHERE admin_id = ? ORDER BY id DESC");
    $stmt->execute([$user['id']]);
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $baseUrl = "$protocol://$host" . rtrim($scriptDir, '/');

    foreach ($docs as &$d) {
        $d['verify_url'] = "$baseUrl/scan.html?token={$d['token']}";
    }

    echo json_encode(["success" => true, "documents" => $docs], JSON_UNESCAPED_UNICODE);
    exit();
}

// 9. SCAN VÉRIFICATION PUBLIQUE
if ($action === 'verify') {
    $token = $_GET['token'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM documents WHERE token = ?");
    $stmt->execute([$token]);
    $doc = $stmt->fetch();

    if (!$doc) {
        echo json_encode(["success" => false, "message" => "Document non reconnu."]);
        exit();
    }

    echo json_encode(["success" => true, "doc" => $doc]);
    exit();
}