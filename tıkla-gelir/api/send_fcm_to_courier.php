<?php
session_start();
require_once "../config/database.php";
require_once "../lib/fcm_helper.php";   // aşağıda

$order_id = $_POST['order_id'] ?? 0;
if (!$order_id)  exit('no order_id');

/* siparişe atanmış kuryeyi al */
$stmt = $pdo->prepare("SELECT c.user_id, u.name courier_name, c.id courier_id
                       FROM orders o
                       JOIN couriers c ON c.id = o.courier_id
                       JOIN users u ON u.id = c.user_id
                       WHERE o.id = ?");
$stmt->execute([$order_id]);
$row = $stmt->fetch();

if (!$row)  exit('courier not found');

/* kuryenin token’ları */
$tokens = $pdo->prepare("SELECT fcm_token FROM courier_fcm_tokens WHERE user_id = ?");
$tokens->execute([$row['user_id']]);
$tks = $tokens->fetchAll(PDO::FETCH_COLUMN);

if ($tks) {
    sendMulticastFCM($tks,
        'Yeni paketin var',
        "Sipariş #$order_id hazır, almak için yola çık.",
        ['order_id' => $order_id]
    );
}

/* işletmeye de “kurye atandı” yanıtı */
echo json_encode(['success' => true, 'courier_name' => $row['courier_name']]);