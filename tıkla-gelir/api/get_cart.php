<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'items' => [], 'message' => 'Oturum yok']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $sql = "
        SELECT 
            c.menu_item_id,
            c.quantity,
            c.notes,
            mi.name,
            mi.price,
            mi.image,
            b.name AS business_name
        FROM cart c
        JOIN menu_items mi ON c.menu_item_id = mi.id
        JOIN businesses b ON mi.business_id = b.id
        WHERE c.user_id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Toplam hesapla
    $total = 0;
    foreach ($items as &$item) {
        $item['price'] = floatval($item['price']);
        $item['quantity'] = intval($item['quantity']);
        $total += $item['price'] * $item['quantity'];
    }

    echo json_encode([
        'success' => true,
        'items' => $items,
        'count' => count($items),
        'total' => round($total, 2)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'items' => [],
        'error' => $e->getMessage()
    ]);
}
?>