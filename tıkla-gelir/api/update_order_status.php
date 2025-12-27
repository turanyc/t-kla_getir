<?php
session_start();
require_once "../config/database.php";

// 1. Yetki kontrolü (SADECE yetkili roller)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'business', 'courier'])) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
    exit;
}

// 2. POST verilerini al (form-urlencoded)
$order_id = (int)($_POST['order_id'] ?? 0);
$status   = $_POST['status'] ?? '';

// 3. Geçerli durumları kontrol et
$valid_statuses = ['yeni', 'hazirlaniyor', 'yolda', 'teslim', 'iptal'];
if (!$order_id || !in_array($status, $valid_statuses, true)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Geçersiz sipariş ID veya durum']);
    exit;
}

try {
    $user_id = $_SESSION['user_id'];
    $role    = $_SESSION['role'];

    // 4. Role göre yetki kontrolü
    if ($role === 'business') {
        $stmt = $pdo->prepare("
            SELECT o.id FROM orders o 
            JOIN businesses b ON o.business_id = b.id 
            WHERE o.id = ? AND b.user_id = ?
        ");
        $stmt->execute([$order_id, $user_id]);
    } elseif ($role === 'courier') {
        $stmt = $pdo->prepare("
            SELECT o.id FROM orders o
            JOIN couriers c ON o.courier_id = c.id
            WHERE o.id = ? AND c.user_id = ?
        ");
        $stmt->execute([$order_id, $user_id]);
    } else { // admin
        $stmt = $pdo->prepare("SELECT id FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
    }

    $order = $stmt->fetch();
    if (!$order) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Bu siparişi güncelleme yetkiniz yok']);
        exit;
    }

    // 5. Siparişi güncelle
    $pdo->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?")
        ->execute([$status, $order_id]);

    // 6. Müşteriye bildirim gönder
    $message_map = [
        'hazirlaniyor' => '🍳 Siparişiniz hazırlanıyor',
        'yolda' => '🚀 Siparişiniz yola çıktı',
        'teslim' => '✅ Sipariş teslim edildi',
        'iptal' => '❌ Sipariş iptal edildi'
    ];

    if (isset($message_map[$status])) {
        $customer_stmt = $pdo->prepare("SELECT customer_id FROM orders WHERE id = ?");
        $customer_stmt->execute([$order_id]);
        $customer_id = $customer_stmt->fetchColumn();

        $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'order')")
            ->execute([$customer_id, 'Sipariş Güncellemesi', $message_map[$status]]);
    }

    // 7. Başarılı yanıt
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>