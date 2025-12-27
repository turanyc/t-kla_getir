<?php
// Çalışma zamanı: her 30 dk
// wget -q -O /dev/null https://siten.com/api/cron_30min_reminder.php
require_once "../config/database.php";

// 24 saat önce "waiting" durumunda kalan ödemeleri al
$waiting = $pdo->query("
   SELECT DISTINCT cpc.courier_id, cpc.restaurant_id
   FROM courier_payment_confirm cpc
   WHERE cpc.status = 'waiting'
     AND cpc.courier_paid_at <= DATE_SUB(NOW(), INTERVAL 24 HOUR)
")->fetchAll();

foreach ($waiting as $w) {
    // Kurye bildirim
    $userC = $pdo->query("SELECT u.id, u.fcm_token FROM couriers c JOIN users u ON c.user_id = u.id WHERE c.id = {$w['courier_id']}")->fetch();
    if ($userC && $userC['fcm_token']) {
        sendPush($userC['fcm_token'], 'Hesap Kesimi', 'Lütfen restorandaki ödemeyi yap ve onay bekletme!');
    }

    // Restoran bildirim
    $userR = $pdo->query("SELECT u.id, u.fcm_token FROM restaurants r JOIN users u ON r.user_id = u.id WHERE r.id = {$w['restaurant_id']}")->fetch();
    if ($userR && $userR['fcm_token']) {
        sendPush($userR['fcm_token'], 'Kurye Ödemesi', 'Kurye ödemeyi yaptığını belirtti, lütfen onaylayın.');
    }
}

function sendPush($token, $title, $body) {
    // Firebase Cloud Messaging v1 API
    $serverKey = 'AAAA0x7xxxx:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; // Firebase > Proje ayarları > Cloud Messaging
    $data = [
        'to' => $token,
        'notification' => [
            'title' => $title,
            'body'  => $body,
            'icon'  => 'https://siten.com/assets/logo192.png',
            'click_action' => 'https://siten.com' // tıklayınca açılacak URL
        ],
        'priority' => 'high'
    ];

    $headers = [
        'Authorization: key=' . $serverKey,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_exec($ch);
    curl_close($ch);
}
?>