<?php

session_start();

require_once "../../config/database.php";

header('Content-Type: application/json');



if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {

    http_response_code(403);

    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);

    exit;

}



$user_id = $_SESSION['user_id'];

$order_id = $_POST['order_id'] ?? 0;



try {

    // Kurye ID'sini al

    $courier = $pdo->prepare("SELECT id FROM couriers WHERE user_id = ? AND is_active = 1");

    $courier->execute([$user_id]);

    $courier = $courier->fetch();



    if (!$courier) {

        throw new Exception("Kurye aktif değil veya kayıt bulunamadı!");

    }



    // Siparişi ata

    $pdo->beginTransaction();

    

    $pdo->prepare("UPDATE orders SET courier_id = ?, status = 'hazirlaniyor' WHERE id = ? AND courier_id IS NULL")

        ->execute([$courier['id'], $order_id]);

    

    $pdo->prepare("UPDATE couriers SET current_order_id = ? WHERE id = ?")

        ->execute([$order_id, $courier['id']]);

    

    $pdo->commit();

    

    echo json_encode(['success' => true, 'message' => 'Paket başarıyla alındı']);



} catch (Exception $e) {

    $pdo->rollBack();

    http_response_code(500);

    echo json_encode(['success' => false, 'message' => $e->getMessage()]);

}