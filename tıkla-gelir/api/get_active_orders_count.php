<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'business') {
    echo json_encode(['count' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];

// İşletme ID'sini al
$stmt = $pdo->prepare("SELECT id FROM businesses WHERE user_id = ? LIMIT 1");
$stmt->execute([$user_id]);
$business = $stmt->fetch();

if (!$business) {
    echo json_encode(['count' => 0]);
    exit;
}

$business_id = $business['id'];

$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM orders 
    WHERE business_id = ? 
      AND status IN ('yeni', 'hazirlaniyor', 'yolda')
");
$stmt->execute([$business_id]);
$count = (int)$stmt->fetchColumn();

echo json_encode(['count' => $count]);
?>