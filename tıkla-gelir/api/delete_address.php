<?php
// api/delete_address.php - Adres Silme API
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions.php';

header('Content-Type: application/json');

// HTTP Metodu Kontrolü
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Giriş Kontrolü
if (!hasRole('customer')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Giriş yapmalısınız!']);
    exit;
}

// JSON Veri
$input = json_decode(file_get_contents('php://input'), true);
$address_id = safeInt($input['id'] ?? 0);
$user_id = getUserId();

if ($address_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz adres ID!']);
    exit;
}

// Adresi Sil
$deleted = deleteAddress($pdo, $address_id, $user_id);

if ($deleted) {
    echo json_encode(['success' => true, 'message' => 'Adres başarıyla silindi!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Adres silinemedi veya yetkiniz yok!']);
}