<?php
session_start();
require_once "../../config/database.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {
    echo json_encode(['success' => false]);
    exit;
}

$order_id = $_POST['order_id'] ?? 0;
$status = $_POST['status'] ?? '';

if (!$order_id || !in_array($status, ['yolda', 'teslim'])) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz parametreler']);
    exit;
}

$stmt = $pdo->prepare("UPDATE orders SET status = ?, delivered_at = CASE WHEN ? = 'teslim' THEN NOW() ELSE delivered_at END WHERE id = ? AND courier_id = (SELECT id FROM couriers WHERE user_id = ?)");
$stmt->execute([$status, $status, $order_id, $_SESSION['user_id']]);

echo json_encode(['success' => true]);
?>