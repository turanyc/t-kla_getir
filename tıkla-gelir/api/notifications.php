<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false]);
    exit;
}

if (!in_array($_SESSION['role'] ?? '', ['business', 'restaurant'])) {
    echo json_encode(['success' => true, 'notifications' => [], 'highest_id' => 0, 'sound' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$last_id = (int)($_GET['last_id'] ?? 0);

try {
    $stmt = $pdo->prepare("SELECT id FROM businesses WHERE user_id = ? AND is_approved = 1 LIMIT 1");
    $stmt->execute([$user_id]);
    $business = $stmt->fetch();

    if (!$business) {
        echo json_encode(['success' => true, 'notifications' => [], 'highest_id' => 0, 'sound' => false]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            o.created_at,
            'order' as type,
            'YENİ SİPARİŞ!' as title,
            CONCAT('Sipariş #', o.id, ' • ', o.total_price, ' ₺ • ', u.name) as message,
            u.name as sender_name
        FROM orders o
        JOIN users u ON o.customer_id = u.id
        WHERE o.business_id = ? AND o.status = 'yeni' AND o.id > ?
        ORDER BY o.id DESC LIMIT 10
    ");
    $stmt->execute([$business['id'], $last_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $highest_id = $last_id;
    $play_sound = !empty($notifications);
    foreach ($notifications as $n) {
        if ($n['id'] > $highest_id) $highest_id = $n['id'];
    }

    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'highest_id' => $highest_id,
        'sound' => $play_sound
    ]);

} catch (Exception $e) {
    error_log("notifications.php hatası: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false]);
}
?>