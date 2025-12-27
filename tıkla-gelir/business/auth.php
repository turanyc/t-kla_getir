<?php
// business/auth.php
// Kullanıcı işletme mi? -> BUSINESS_ID, BUSINESS_NAME, BUSINESS_TYPE tanımlar
// + CSRF token üretir (excel.php vs. için)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* CSRF token yoksa oluştur (excel.php, reports.php vs. için) */
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . "/../config/database.php";   // $pdo

/* Giriş kontrolü */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'business') {
    header("Location: ../login.php");
    exit;
}

/* İşletme kaydını çek */
$stmt = $pdo->prepare("
    SELECT b.id, b.name, vt.slug AS business_type
    FROM businesses b
    LEFT JOIN vendor_types vt ON b.vendor_type_id = vt.id
    WHERE b.user_id = ?
    LIMIT 1
");
$stmt->execute([$_SESSION['user_id']]);
$business = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$business) {
    die('<div style="padding:20px;color:#ff4444;">❌ İşletme kaydınız bulunamadı!</div>');
}

/* Sabitler */
define('BUSINESS_ID',   $business['id']);
define('BUSINESS_NAME', $business['name']);
define('BUSINESS_TYPE', $business['business_type'] ?? 'restaurant');