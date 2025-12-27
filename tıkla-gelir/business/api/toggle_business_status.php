<?php
session_start();
require_once "../../config/database.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Yetkisiz']);
    exit;
}

$status = $_POST['status'] ?? null;
if ($status === '0' || $status === '1') {
    $status = (int)$status;
    $stmt = $pdo->prepare("UPDATE businesses SET is_open = ? WHERE user_id = ? AND is_approved = 1");
    $stmt->execute([$status, $_SESSION['user_id']]);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz durum']);
}
?>