<?php
// api/update_address.php - Adres Güncelleme API
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
$title = sanitize($input['title'] ?? '');
$address_text = sanitize($input['address'] ?? '');
$is_default = filter_var($input['is_default'] ?? false, FILTER_VALIDATE_BOOLEAN);

// Hata Kontrolleri
if ($address_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz adres ID!']);
    exit;
}

if (empty($title) || strlen($title) < 3) {
    echo json_encode(['success' => false, 'message' => 'Adres başlığı en az 3 karakter olmalı!']);
    exit;
}

if (empty($address_text) || strlen($address_text) < 10) {
    echo json_encode(['success' => false, 'message' => 'Adres açıklaması en az 10 karakter olmalı!']);
    exit;
}

// Adresi Güncelle
$updated = updateAddress($pdo, $address_id, $user_id, $title, $address_text, $is_default);

if ($updated) {
    echo json_encode(['success' => true, 'message' => 'Adres başarıyla güncellendi!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Adres güncellenemedi veya yetkiniz yok!']);
}