<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapılmadı']);
    exit;
}

$user_id = $_SESSION['user_id'];
$menu_item_id = intval($_POST['menu_item_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);

if ($menu_item_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz ürün']);
    exit;
}

try {
    if ($quantity <= 0) {
        // Sil
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND menu_item_id = ?");
        $stmt->execute([$user_id, $menu_item_id]);
    } else {
        // Ekle / Güncelle
        $stmt = $pdo->prepare("
            INSERT INTO cart (user_id, menu_item_id, quantity, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE 
                quantity = VALUES(quantity),
                updated_at = NOW()
        ");
        $stmt->execute([$user_id, $menu_item_id, $quantity]);
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
}
?>