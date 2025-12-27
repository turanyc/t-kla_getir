<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/config/database.php';

if (($_SESSION['role'] ?? '') !== 'courier')  exit('Yetkisiz');

$courierId = $pdo->prepare("SELECT id FROM couriers WHERE user_id = ?")
                  ->execute([$_SESSION['user_id']])
                  ->fetchColumn();

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data))  exit('Veri hatalı');

try {
    $pdo->beginTransaction();
    foreach ($data as $row) {
        $restId = (int) $row['restaurant_id'];
        $amount = (float) $row['amount'];
        if ($amount <= 0)  throw new Exception('Negatif tutar');

        $pdo->prepare(
            "INSERT INTO courier_payment_confirm
               (courier_id, restaurant_id, amount, status, created_at)
             VALUES (?, ?, ?, 'waiting', NOW())")
          ->execute([$courierId, $restId, $amount]);
    }
    $pdo->commit();
    echo 'OK';
} catch (Throwable $e) {
    $pdo->rollBack();
    http_response_code(400);
    echo $e->getMessage();
}