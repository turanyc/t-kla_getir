<?php
session_start();
require_once "../../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {
    echo "ERROR";
    exit;
}

$token = $_POST['token'] ?? '';
if ($token) {
    $stmt = $pdo->prepare("UPDATE couriers SET fcm_token = ? WHERE user_id = ?");
    $stmt->execute([$token, $_SESSION['user_id']]);
    echo "OK";
} else {
    echo "NO_TOKEN";
}
?>