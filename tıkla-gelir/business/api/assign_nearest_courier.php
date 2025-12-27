<?php
session_start();
require_once "../../config/database.php";

$order_id = $_POST['order_id'] ?? 0;
if (!$order_id) {
    echo json_encode(['success' => false]);
    exit;
}

// Siparişin business_id'sini al
$stmt = $pdo->prepare("SELECT business_id FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    echo json_encode(['success' => false]);
    exit;
}

// İşletme koordinatı
$stmt = $pdo->prepare("SELECT latitude, longitude FROM businesses WHERE id = ?");
$stmt->execute([$order['business_id']]);
$biz = $stmt->fetch();

if (!$biz['latitude'] || !$biz['longitude']) {
    echo json_encode(['success' => false, 'msg' => 'İşletme konumu eksik']);
    exit;
}

// En yakın pasif kuryeyi bul (15 km içinde)
$stmt = $pdo->prepare("
    SELECT c.id, c.user_id,
           (6371 * acos(cos(radians(?)) * cos(radians(c.latitude)) * 
                        cos(radians(c.longitude) - radians(?)) + 
                        sin(radians(?)) * sin(radians(c.latitude)))) AS distance
    FROM couriers c 
    WHERE c.status = 'passive' AND c.is_active = 1 AND c.latitude IS NOT NULL AND c.longitude IS NOT NULL
    HAVING distance <= 15
    ORDER BY distance ASC 
    LIMIT 1
");
$stmt->execute([$biz['latitude'], $biz['longitude'], $biz['latitude']]);
$courier = $stmt->fetch();

if ($courier) {
    // Siparişi ata
    $pdo->prepare("UPDATE orders SET courier_id = ?, status = 'yolda' WHERE id = ?")
        ->execute([$courier['id'], $order_id]);
    
    // Kuryeyi aktif yap
    $pdo->prepare("UPDATE couriers SET status = 'active', current_order_id = ? WHERE id = ?")
        ->execute([$order_id, $courier['id']]);
    
    echo json_encode(['success' => true, 'courier_id' => $courier['id']]);
} else {
    echo json_encode(['success' => false, 'msg' => 'Yakında kurye yok']);
}
?>