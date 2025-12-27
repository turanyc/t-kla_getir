<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Yetkisiz erişim']);
    exit;
}

try {
    $stmt = $pdo->query("
        SELECT 
            c.id AS courier_id,
            u.name AS courier_name,
            u.phone,
            c.is_active,
            c.is_available,
            c.status,
            COALESCE(cl.latitude, c.latitude) AS lat,
            COALESCE(cl.longitude, c.longitude) AS lng,
            COALESCE(cl.timestamp, c.location_updated_at) AS recorded_at,
            o.id AS current_order_id,
            o.status AS order_status
        FROM couriers c
        JOIN users u ON c.user_id = u.id
        LEFT JOIN (
            SELECT 
                courier_id, 
                latitude, 
                longitude, 
                timestamp
            FROM courier_location cl1
            WHERE timestamp = (
                SELECT MAX(timestamp) 
                FROM courier_location cl2 
                WHERE cl2.courier_id = cl1.courier_id
            )
        ) cl ON c.id = cl.courier_id
        LEFT JOIN orders o ON c.current_order_id = o.id
        WHERE c.is_active = 1 
           OR c.latitude IS NOT NULL 
           OR cl.latitude IS NOT NULL
        ORDER BY COALESCE(cl.timestamp, c.location_updated_at) DESC
    ");

    $couriers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'couriers' => $couriers,
        'count' => count($couriers),
        'generated_at' => date('Y-m-d H:i:s')
    ]);

} catch(Exception $e) {
    error_log("Get courier locations error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Sunucu hatası']);
}
?>