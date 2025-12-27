<?php
// api/add_to_cart.php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapmadınız']);
    exit;
}

$user_id = $_SESSION['user_id'];
$menu_item_id = intval($_POST['menu_item_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 1);

if ($menu_item_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz ürün']);
    exit;
}

try {
    // Senin update_cart.php mantığına uygun şekilde ekleyelim
    $stmt = $pdo->prepare("
        INSERT INTO cart (user_id, menu_item_id, quantity, created_at, updated_at)
        VALUES (?, ?, ?, NOW(), NOW())
        ON DUPLICATE KEY UPDATE 
            quantity = quantity + VALUES(quantity),
            updated_at = NOW()
    ");
    $stmt->execute([$user_id, $menu_item_id, $quantity]);

    echo json_encode(['success' => true, 'message' => 'Sepete eklendi']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Hata oluştu']);
}
?>