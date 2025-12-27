<?php
session_start();
require_once "../../config/database.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {
    echo json_encode([]);
    exit;
}

$c = $pdo->prepare("SELECT id FROM couriers WHERE user_id = ?");
$c->execute([$_SESSION['user_id']]);
$cid = $c->fetchColumn();

if (!$cid) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT DISTINCT b.id, b.name FROM orders o JOIN businesses b ON o.business_id = b.id WHERE o.courier_id = ? AND DATE(o.created_at) = CURDATE()");
$stmt->execute([$cid]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));