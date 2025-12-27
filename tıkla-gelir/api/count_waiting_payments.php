<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'restaurant') {
    echo json_encode(['count' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Restoran ID'yi al
    $stmt = $pdo->prepare("SELECT id FROM restaurants WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $rest = $stmt->fetch();
    $restaurant_id = $rest['id'] ?? 0;

    if (!$restaurant_id) {
        echo json_encode(['count' => 0]);
        exit;
    }

    // Bekleyen ödeme sayısını al
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM courier_payment_confirm WHERE restaurant_id = ? AND status = 'waiting'");
    $stmt->execute([$restaurant_id]);
    $cnt = $stmt->fetchColumn();

    echo json_encode(['count' => (int)$cnt]);
} catch (Exception $e) {
    echo json_encode(['count' => 0, 'error' => $e->getMessage()]);
}
?>