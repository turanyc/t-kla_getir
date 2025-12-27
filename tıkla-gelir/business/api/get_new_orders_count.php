<?php
session_start();
require_once "../config/database.php";

$business_id = $_SESSION['business_id'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE business_id = ? AND status = 'yeni'");
$stmt->execute([$business_id]);
$count = $stmt->fetchColumn();

$last_order = null;
$items = [];

if ($count > 0) {
    $stmt2 = $pdo->prepare("
        SELECT o.id, u.name AS customer_name
        FROM orders o
        JOIN users u ON o.customer_id = u.id
        WHERE o.business_id = ? AND o.status = 'yeni'
        ORDER BY o.created_at DESC LIMIT 1
    ");
    $stmt2->execute([$business_id]);
    $last_order = $stmt2->fetch();

    if ($last_order) {
        $items_stmt = $pdo->prepare("
            SELECT 
                oi.quantity,
                COALESCE(mi.name, oi.product_name) AS product_name
            FROM order_items oi
            LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id
            WHERE oi.order_id = ?
        ");
        $items_stmt->execute([$last_order['id']]);
        $items = $items_stmt->fetchAll();
    }
}

echo json_encode([
    'count' => (int)$count,
    'last_order_id' => $last_order['id'] ?? null,
    'customer_name' => $last_order['customer_name'] ?? 'Bilinmiyor',
    'items' => $items
]);