<?php
session_start();
require_once "../config/database.php";

if ($_SESSION['role'] !== 'restaurant') exit('Yetkisiz');

$order_id = (int)($_POST['order_id'] ?? 0);
$rest_id  = $pdo->query("SELECT id FROM restaurants WHERE user_id = " . $_SESSION['user_id'])->fetchColumn();

$upd = $pdo->prepare("UPDATE courier_payment_confirm 
                      SET status = 'confirmed', restaurant_confirmed_at = NOW() 
                      WHERE order_id = ? AND restaurant_id = ? AND status = 'waiting'");
$upd->execute([$order_id, $rest_id]);

echo $upd->rowCount() ? 'OK' : 'Bulunamadı';
?>