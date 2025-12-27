<?php
session_start();
require_once "../../config/database.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {
    echo json_encode(['success' => true, 'orders' => []]);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM couriers WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$courier_id = $stmt->fetchColumn();

if (!$courier_id) {
    echo json_encode(['success' => true, 'orders' => []]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT o.*, b.name as business_name, o.address
    FROM orders o
    JOIN businesses b ON o.business_id = b.id
    WHERE o.courier_id = ? AND o.status IN ('hazirlaniyor', 'yolda')
    ORDER BY o.created_at DESC
");
$stmt->execute([$courier_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'orders' => $orders]);
?>