<?php
session_start();
require_once "../../config/database.php";

/* ---------- 1. CORS (harita / JS erişimi) ---------- */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

/* ---------- 2. YETKİ – sadece kurye ---------- */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz']);
    exit;
}

try {
    /* ---------- 3. GELEN KONUMU AL (FORM DATA veya JSON) ---------- */
    $lat = null;
    $lng = null;
    
    // Önce POST parametrelerini kontrol et
    if (isset($_POST['lat']) && isset($_POST['lng'])) {
        $lat = (float)$_POST['lat'];
        $lng = (float)$_POST['lng'];
    } else {
        // JSON gönderildiyse
        $data = json_decode(file_get_contents('php://input'), true);
        $lat = isset($data['lat']) ? (float)$data['lat'] : null;
        $lng = isset($data['lng']) ? (float)$data['lng'] : null;
    }

    if ($lat === null || $lng === null) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Eksik koordinat']);
        exit;
    }

    /* ---------- 4. KURYE ID'SİNİ BUL ---------- */
    $stmt = $pdo->prepare("SELECT id FROM couriers WHERE user_id = ? LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $courier_id = $stmt->fetchColumn();
    if (!$courier_id) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Kurye bulunamadı']);
        exit;
    }

    /* ---------- 5. KONUMU KAYDET & ZAMAN DAMGASI (Türkiye) ---------- */
    $now = (new DateTime('now', new DateTimeZone('Europe/Istanbul')))->format('Y-m-d H:i:s');

    $upd = $pdo->prepare("UPDATE couriers 
                          SET latitude = ?, longitude = ?, location_updated_at = ? 
                          WHERE id = ?");
    $upd->execute([$lat, $lng, $now, $courier_id]);

    /* ---------- 6. BAŞARILI CEVAP ---------- */
    echo json_encode(['success' => true, 'recorded_at' => $now]);

} catch (Exception $e) {
    error_log("courier_location_push error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Sunucu hatası']);
}
?>