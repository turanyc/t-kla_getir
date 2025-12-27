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

$last_id = (int)($_GET['last_id'] ?? 0);



try {

    // Kurye ID'sini al

    $courier = $pdo->prepare("SELECT id, is_active FROM couriers WHERE user_id = ?");

    $courier->execute([$user_id]);

    $courier = $courier->fetch();



    // Aktif siparişler (atanmış olanlar)

    $stmt = $pdo->prepare("SELECT o.*, 

                                  r.name as restaurant_name, 

                                  r.phone as restaurant_phone,

                                  u.name as customer_name, 

                                  u.phone as customer_phone,

                                  r.address as restaurant_address

                           FROM orders o

                           JOIN restaurants r ON o.restaurant_id = r.id

                           JOIN users u ON o.customer_id = u.id

                           WHERE o.courier_id = ? AND o.id > ? AND o.status NOT IN ('teslim', 'iptal')

                           ORDER BY o.id DESC");

    $stmt->execute([$courier['id'], $last_id]);

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);



    echo json_encode([

        'success' => true,

        'orders' => $orders,

        'highest_id' => !empty($orders) ? max(array_column($orders, 'id')) : $last_id,

        'courier_status' => $courier['is_active'] ? 'active' : 'passive'

    ]);



} catch (Exception $e) {

    http_response_code(500);

    echo json_encode(['success' => false, 'message' => $e->getMessage()]);

}