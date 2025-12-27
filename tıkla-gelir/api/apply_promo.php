<?php
session_start();
require_once "../config/database.php";

header('Content-Type: application/json');

// Gelen veriyi al
$input = json_decode(file_get_contents('php://input'), true);
$code = trim($input['code'] ?? '');

if (!$code) {
    echo json_encode(['success' => false, 'message' => 'Kod girilmedi.']);
    exit;
}

// Veritabanında kodu kontrol et
$stmt = $pdo->prepare("SELECT * FROM promotions WHERE code = ? AND is_active = 1 AND valid_until >= NOW()");
$stmt->execute([$code]);
$promo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$promo) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz veya süresi dolmuş kod.']);
    exit;
}

// Başarılı
echo json_encode([
    'success' => true,
    'promo' => [
        'code' => $promo['code'],
        'discount_percent' => (float)$promo['discount_percent'],
        'free_delivery' => (bool)$promo['free_delivery']
    ]
]);
?>