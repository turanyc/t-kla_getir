<?php
session_start();
require_once "../../config/database.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $pdo->prepare("SELECT status FROM couriers WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$status = $stmt->fetchColumn() ?: 'passive';

echo json_encode(['success' => true, 'status' => $status]);
?>