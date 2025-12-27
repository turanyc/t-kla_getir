<?php
session_start();
require_once "config/database.php";  // ../ KALDIRILDI ✅
header('Content-Type: application/json');

// Güvenlik: Oturum kontrolü
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'restaurant'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Yetkisiz erişim']);
    exit;
}

// Ayın kazanç raporu
try {
    $data = $pdo->query("SELECT DATE(created_at) as gun, SUM(total_price) as kazanc 
                         FROM orders WHERE MONTH(created_at)=MONTH(CURDATE()) 
                         GROUP BY DATE(created_at) ORDER BY gun")->fetchAll();
    
    $labels = []; 
    $values = [];
    foreach($data as $d){ 
        $labels[] = date('d.m',strtotime($d['gun'])); 
        $values[] = (float)$d['kazanc']; 
    }
    
    echo json_encode([
        'labels' => $labels, 
        'values' => $values,
        'success' => true
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Veritabanı hatası',
        'message' => $e->getMessage()
    ]);
}
exit; // exit; EKLENDİ ✅