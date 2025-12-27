<?php
session_start();
require_once "../config/database.php";

// Yetki kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Yetkisiz');
}

// Girdi kontrolü
$id   = isset($_POST['id'])   ? (int)$_POST['id']   : 0;
$rate = isset($_POST['rate']) ? (float)$_POST['rate'] : -1;

if ($id <= 0 || $rate < 0 || $rate > 100) {
    http_response_code(400);
    exit('Geçersiz oran');
}

try {
    $pdo->beginTransaction();

    // Güncelleme
    $upd = $pdo->prepare("UPDATE couriers SET commission_rate = ? WHERE id = ?");
    $upd->execute([$rate, $id]);

    // Admin log kaydı
    $log = $pdo->prepare("INSERT INTO admin_activity_logs (admin_id, action, description, ip_address) VALUES (?, 'kurye_komisyon_guncelle', ?, ?)");
    $log->execute([$_SESSION['user_id'], "Kurye ID: $id, Yeni Oran: %$rate", $_SERVER['REMOTE_ADDR']]);

    $pdo->commit();

    echo $upd->rowCount() ? 'OK' : 'Bulunamadı';
} catch (Throwable $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo 'Hata: ' . $e->getMessage();
}
?>