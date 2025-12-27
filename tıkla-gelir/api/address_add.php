<?php
declare(strict_types=1);

session_start();
require_once "../config/database.php";
require_once "../functions.php"; // hasRole() ve sanitize() için

header('Content-Type: application/json');

// ==== HTTP Metodu Kontrolü ====
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Sadece POST kabul edilir']);
    exit;
}

// ==== Giriş Kontrolü ====
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Giriş yapmalısınız!']);
    exit;
}

// ==== JSON Veri Alımı ====
$input = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz JSON']);
    exit;
}

// ==== Verileri Al ve Temizle ====
$title       = trim($input['title'] ?? '');
$address     = trim($input['address'] ?? '');
$district    = trim($input['district'] ?? '');
$city        = trim($input['city'] ?? 'İstanbul');
$phone       = trim($input['phone'] ?? '');
$is_default  = !empty($input['is_default']);

if (empty($title) || strlen($title) < 3) {
    echo json_encode(['success' => false, 'message' => 'Adres başlığı en az 3 karakter olmalı!']);
    exit;
}

if (empty($address) || strlen($address) < 10) {
    echo json_encode(['success' => false, 'message' => 'Adres en az 10 karakter olmalı!']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();

    // Varsayılan adresse diğerlerini sıfırla
    if ($is_default) {
        $stmt = $pdo->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }

    // Yeni adresi ekle
    $stmt = $pdo->prepare("
        INSERT INTO addresses 
        (user_id, title, address, district, city, phone, is_default, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    $stmt->execute([
        $user_id,
        $title,
        $address,
        $district,
        $city,
        $phone,
        $is_default ? 1 : 0
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Adres başarıyla eklendi!'
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Adres ekleme hatası [User: $user_id]: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Adres eklenirken hata oluştu!'
    ]);
}
?>