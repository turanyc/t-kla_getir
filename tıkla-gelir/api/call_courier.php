<?php
session_start();
require_once "../config/database.php";  // DÜZELTİLDİ: ../../ → ../
require_once "../business/auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$csrf = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
    http_response_code(403);
    exit('CSRF Hatası');
}

$phone = preg_replace('/\D/', '', $_POST['customer_phone']);

// Müşteri var mı?
$stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ? LIMIT 1");
$stmt->execute([$phone]);
$customer = $stmt->fetch();

if (!$customer) {
    $ins = $pdo->prepare("INSERT INTO users (name, phone, role, created_at) VALUES (?, ?, 'customer', NOW())");
    $ins->execute([$_POST['customer_name'], $phone]);
    $customer_id = $pdo->lastInsertId();
} else {
    $customer_id = $customer['id'];
}

// Sipariş oluştur
$stmt = $pdo->prepare("
    INSERT INTO orders 
        (business_id, customer_id, address, customer_name, customer_phone, 
         total_price, payment_method, status, created_at) 
    VALUES 
        (?, ?, ?, ?, ?, ?, 'kapida_nakit', 'yeni', NOW())
");
$stmt->execute([
    BUSINESS_ID,
    $customer_id,
    $_POST['address'],
    $_POST['customer_name'],
    $phone,
    $_POST['total_price']
]);
$order_id = $pdo->lastInsertId();

// İşletme konumu
$biz = $pdo->prepare("SELECT latitude, longitude FROM businesses WHERE id = ?");
$biz->execute([BUSINESS_ID]);
$biz = $biz->fetch();

if (!$biz['latitude'] || !$biz['longitude']) {
    echo json_encode(['success' => false, 'error' => 'İşletme konumu eksik']);
    exit;
}

// En yakın boşta kurye (10 km içinde)
$sql = "
    SELECT 
        c.id, 
        u.name,
        (6371 * acos(cos(radians(?)) * cos(radians(c.latitude)) * 
                     cos(radians(c.longitude) - radians(?)) + 
                     sin(radians(?)) * sin(radians(c.latitude)))) AS distance_km
    FROM couriers c
    JOIN users u ON c.user_id = u.id
    WHERE c.is_active = 1 
      AND c.status = 'passive'
      AND c.is_available = 1
      AND c.latitude IS NOT NULL 
      AND c.longitude IS NOT NULL
    HAVING distance_km <= 10
    ORDER BY distance_km ASC
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$biz['latitude'], $biz['longitude'], $biz['latitude']]);
$courier = $stmt->fetch();

if (!$courier) {
    echo json_encode(['success' => false, 'error' => 'Yakında boşta kurye yok']);
    exit;
}

// Kuryeye ata + meşgul yap
$pdo->prepare("UPDATE orders SET courier_id = ?, status = 'onaylandi' WHERE id = ?")
   ->execute([$courier['id'], $order_id]);

$pdo->prepare("UPDATE couriers SET is_available = 0, current_order_id = ? WHERE id = ?")
   ->execute([$order_id, $courier['id']]);

echo json_encode([
    'success' => true,
    'order_id' => $order_id,
    'courier_name' => $courier['name'],
    'distance_km' => round($courier['distance_km'], 2),
    'message' => 'Kurye atandı!'
]);
?>