<?php
session_start();
require_once "../../config/database.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {
    echo json_encode(['count' => 0]);
    exit;
}

try {
    // Kurye ID'sini al
    $stmt = $pdo->prepare("SELECT id FROM couriers WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $courier_id = $stmt->fetchColumn();

    if (!$courier_id) {
        echo json_encode(['count' => 0]);
        exit;
    }

    // Aktif sipariş sayısı
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM orders 
        WHERE courier_id = ? AND status IN ('hazirlaniyor', 'yolda')
    ");
    $count_stmt->execute([$courier_id]);
    $count = $count_stmt->fetchColumn();

    echo json_encode(['count' => (int)$count]);

} catch (Exception $e) {
    error_log("get_courier_orders_count error: " . $e->getMessage());
    echo json_encode(['count' => 0]);
}
?>