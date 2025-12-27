<?php
session_start();
require_once "../../config/database.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {
    http_response_code(403);
    echo json_encode(['success' => false]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM couriers WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $courier_id = $stmt->fetchColumn();

    if (!$courier_id) {
        echo json_encode(['success' => true, 'today_orders' => 0, 'today_earnings' => '0.00', 'total_earnings' => '0.00', 'avg_rating' => 0]);
        exit;
    }

    $today = date('Y-m-d');
    $stats = $pdo->prepare("
        SELECT 
            COUNT(*) as today_orders,
            COALESCE(SUM(courier_commission), 0) as today_earnings,
            COALESCE(SUM(courier_commission), 0) as total_earnings
        FROM orders 
        WHERE courier_id = ? AND status = 'teslim' AND DATE(delivered_at) = ?
    ");
    $stats->execute([$courier_id, $today]);
    $row = $stats->fetch();

    $total = $pdo->prepare("SELECT COALESCE(SUM(courier_commission), 0) FROM orders WHERE courier_id = ? AND status = 'teslim'");
    $total->execute([$courier_id]);
    $total_earnings = $total->fetchColumn();

    echo json_encode([
        'success' => true,
        'today_orders' => (int)$row['today_orders'],
        'today_earnings' => number_format((float)$row['today_earnings'], 2),
        'total_earnings' => number_format((float)$total_earnings, 2),
        'avg_rating' => 4.9
    ]);

} catch(Exception $e) {
    error_log("courier_stats error: " . $e->getMessage());
    echo json_encode(['success' => false]);
}
?>