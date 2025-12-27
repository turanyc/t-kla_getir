<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']))  http_response_code(401);

$token = $_POST['token'] ?? '';
if (!$token)  http_response_code(400);

$table = null;
$id    = null;

/* role göre tablo seç */
if ($_SESSION['role'] === 'business') {
    $table = 'business_fcm_tokens';
    $stmt  = $pdo->prepare("SELECT id FROM businesses WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $id = $stmt->fetchColumn();
} elseif ($_SESSION['role'] === 'courier') {
    $table = 'courier_fcm_tokens';
    $stmt  = $pdo->prepare("SELECT id FROM couriers WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $id = $stmt->fetchColumn();
}

if (!$id)  http_response_code(404);

/* upsert */
$ups = $pdo->prepare("INSERT INTO $table (user_id, fcm_token) VALUES (?,?)
                      ON DUPLICATE KEY UPDATE fcm_token = ?");
$ups->execute([$id, $token, $token]);

echo json_encode(['success' => true]);