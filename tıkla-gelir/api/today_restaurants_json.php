<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/config/database.php';

if (($_SESSION['role'] ?? '') !== 'courier')  exit('[]');

$courierId = $pdo->prepare("SELECT id FROM couriers WHERE user_id = ?")
                  ->execute([$_SESSION['user_id']])
                  ->fetchColumn();

$stmt = $pdo->prepare(
    "SELECT DISTINCT r.id, r.name
       FROM orders o
       JOIN restaurants r ON r.id = o.restaurant_id
      WHERE o.courier_id = ? 
        AND DATE(o.created_at) = CURDATE()
      ORDER BY r.name");
$stmt->execute([$courierId]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));