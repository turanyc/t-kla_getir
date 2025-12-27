<?php
session_start();
header('Content-Type: application/json');

// Gelen promoyu SESSION'a yaz
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['code'], $input['discount_percent'], $input['free_delivery'])) {
    $_SESSION['promo'] = [
        'code'             => $input['code'],
        'discount_percent' => (float)$input['discount_percent'],
        'free_delivery'    => (bool)$input['free_delivery']
    ];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Eksik veri']);
}
?>