<?php
session_start();
require_once "../../config/database.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {
    echo json_encode(['success' => false]);
    exit;
}

$status = $_POST['status'] ?? '';
if (!in_array($status, ['active', 'passive'])) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz durum']);
    exit;
}

$stmt = $pdo->prepare("UPDATE couriers SET status = ? WHERE user_id = ?");
$stmt->execute([$status, $_SESSION['user_id']]);

echo json_encode(['success' => true]);
?>