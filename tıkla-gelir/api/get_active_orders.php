<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Kurye ID
    $stmt = $pdo->prepare("SELECT id FROM couriers WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $courier = $stmt->fetch();
    
    if (!$courier) {
        echo json_encode(['orders' => []]);
        exit;
    }
    
    $courier_id = $courier['id'];
    
    // Aktif siparişler
    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            o.status,
            o.address,
            o.total_price,
            o.created_at,
            r.name as restaurant_name,
            u.name as customer_name,
            u.phone as customer_phone
        FROM orders o
        JOIN restaurants r ON o.restaurant_id = r.id
        JOIN users u ON o.customer_id = u.id
        WHERE o.courier_id = ? AND o.status IN ('hazirlaniyor', 'yolda')
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$courier_id]);
    $orders = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'orders' => $orders,
        'count' => count($orders)
    ]);
    
} catch(Exception $e) {
    error_log("Get active orders error: " . $e->getMessage());
    echo json_encode(['error' => 'Internal server error']);
}
?>