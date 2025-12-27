<?php
session_start();
require_once "../config/database.php";
require_once "../functions.php";

// Giriş ve yetki
if (!hasRole('restaurant')) {
    header("Location: ../login.php");
    exit;
}

$order_id = safeInt($_GET['id'] ?? 0);
$user_id  = getUserId();

if ($order_id <= 0) die("Geçersiz sipariş ID!");

try {
    // Sipariş bilgileri (restorana ait olduğunu kontrol et)
    $stmt = $pdo->prepare("SELECT o.*, r.name as restaurant_name, u.name as customer_name
                          FROM orders o
                          JOIN restaurants r ON o.restaurant_id = r.id
                          JOIN users u ON o.customer_id = u.id
                          WHERE o.id = ? AND r.user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) die("Bu siparişi görüntüleme yetkiniz yok veya sipariş bulunamadı!");

    // Sipariş kalemleri
    $items_stmt = $pdo->prepare("SELECT oi.*, m.name as item_name
                                FROM order_items oi
                                JOIN menu_items m ON oi.menu_item_id = m.id
                                WHERE oi.order_id = ?");
    $items_stmt->execute([$order_id]);
    $order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Sistem Hatası: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Detayı #<?= $order_id ?> - Tıkla Gelsin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #FF4500;
            --secondary: #00C853;
            --accent: #FFD700;
            --light: #F8F9FA;
            --card-bg: #FFFFFF;
            --dark: #333333;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #F5F7FA;
            color: var(--dark);
            padding: 15px;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .container { max-width: 900px; margin: 0 auto; padding: clamp(15px, 3vw, 20px); }

        /* Geri dön butonu */
        .back-btn {
            background: var(--secondary);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: clamp(8px, 2vw, 10px) clamp(20px, 4vw, 25px);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            transition: 0.3s;
            font-size: clamp(14px, 2.5vw, 16px);
        }
        .back-btn:hover { background: #009624; }

        /* Kartlar */
        .card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: clamp(20px, 4vw, 30px);
            margin-bottom: 20px;
            border: 1px solid #E0E0E0;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .card h4 {
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: clamp(18px, 3vw, 20px);
        }
        .card h2 {
            font-size: clamp(20px, 4vw, 24px);
        }

        /* Ürün satırları */
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: clamp(12px, 2.5vw, 15px) 0;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
            gap: 10px;
        }
        .item-row:last-child { border-bottom: none; }
        .item-row > div {
            flex: 1;
            min-width: 150px;
        }
        .text-end {
            text-align: right;
        }

        /* Toplam */
        .total-row {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            padding: clamp(15px, 3vw, 20px);
            border-radius: 15px;
            font-size: clamp(18px, 3vw, 20px);
            font-weight: bold;
            margin-top: 20px;
        }
        .total-row .d-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Butonlar */
        .btn-success, .btn {
            background: var(--secondary);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: clamp(10px, 2vw, 12px) clamp(18px, 3vw, 24px);
            font-weight: 600;
            transition: 0.3s;
            font-size: clamp(14px, 2.5vw, 16px);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-success:hover, .btn:hover {
            background: #009624;
            transform: translateY(-2px);
        }

        /* Durum rozeti */
        .status-badge {
            padding: clamp(6px, 1.5vw, 8px) clamp(15px, 3vw, 20px);
            border-radius: 50px;
            font-size: clamp(12px, 2.5vw, 14px);
            font-weight: bold;
        }
        .status-yeni { background: #9C27B0; color: #fff; }
        .status-hazirlaniyor { background: #FFC107; color: #333; }
        .status-yolda { background: #2196F3; color: #fff; }
        .status-teslim { background: #4CAF50; color: #fff; }
        .status-iptal { background: #F44336; color: #fff; }
        
        .text-muted { color: #6c757d; }
        .mb-3 { margin-bottom: 15px; }
        .mb-4 { margin-bottom: 20px; }
        .mt-4 { margin-top: 20px; }
        .mb-0 { margin-bottom: 0; }
        .text-center { text-align: center; }
        .d-flex { display: flex; }
        .justify-content-between { justify-content: space-between; }
        .align-items-center { align-items: center; }
        .row { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 15px; }
        .col-md-6 { flex: 1; min-width: 250px; }
        #miniMap { height: clamp(250px, 40vh, 300px); border-radius: 15px; width: 100%; }
        
        @media (max-width: 768px) {
            .item-row { flex-direction: column; align-items: flex-start; }
            .text-end { text-align: left; }
            .total-row .d-flex { flex-direction: column; align-items: flex-start; }
            .row { flex-direction: column; }
            .col-md-6 { width: 100%; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>

<div class="container">
    <!-- Geri Dön -->
    <a href="order_history.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> Siparişlerime Dön
    </a>

    <!-- Üst Başlık – Tıkla Gelsin -->
    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 style="color: var(--primary);">
                    <i class="fas fa-receipt"></i> Sipariş #<?= $order_id ?>
                </h2>
                <p class="text-muted mb-0">
                    <i class="fas fa-calendar"></i> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                </p>
            </div>
            <span class="status-badge status-<?= $order['status'] ?>">
                <?= ucfirst($order['status']) ?>
            </span>
        </div>

        <div class="row">
            <div class="col-md-6">
                <p><strong>Restoran:</strong><br>
                    <i class="fas fa-utensils"></i> <?= htmlspecialchars($order['restaurant_name']) ?>
                </p>
            </div>
            <div class="col-md-6">
                <p><strong>Ödeme Yöntemi:</strong><br>
                    <i class="fas fa-credit-card"></i>
                    <?= htmlspecialchars($order['payment_method'] ?: 'Kapıda Nakit') ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Takip Butonu (yolda ise) -->
    <?php if ($order['status'] === 'yolda' && $order['courier_id']): ?>
    <div class="text-center mb-4">
        <a href="order_track.php?id=<?= $order_id ?>" class="btn btn-success">
            <i class="fas fa-map-marker-alt"></i> Canlı Takip Et
        </a>
    </div>
    <?php endif; ?>

    <!-- Sipariş İçeriği -->
    <div class="card">
        <h4><i class="fas fa-shopping-cart"></i> Sipariş İçeriği</h4>
        <?php foreach ($order_items as $item): ?>
        <div class="item-row">
            <div>
                <strong><?= htmlspecialchars($item['item_name']) ?></strong><br>
                <small class="text-muted">Adet: <?= $item['quantity'] ?></small>
            </div>
            <div class="text-end">
                <strong><?= number_format($item['price'] * $item['quantity'], 2) ?> ₺</strong><br>
                <small class="text-muted">Birim: <?= number_format($item['price'], 2) ?> ₺</small>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="total-row">
            <div class="d-flex justify-content-between align-items-center">
                <span><i class="fas fa-coins"></i> Toplam Tutar:</span>
                <span style="font-size: 24px;"><?= number_format($order['total_price'], 2) ?> ₺</span>
            </div>
        </div>
    </div>

    <!-- Not -->
    <?php if ($order['note']): ?>
    <div class="card">
        <h4><i class="fas fa-sticky-note"></i> Sipariş Notu</h4>
        <p class="mb-0"><?= nl2br(htmlspecialchars($order['note'])) ?></p>
    </div>
    <?php endif; ?>

    <!-- 1) Kurye Ödemesi Butonu (yolda ise) -->
    <?php if ($order['status'] === 'yolda' && $order['courier_id']): ?>
    <div class="card mt-4">
        <div class="card-body text-center">
            <h5><i class="fas fa-hand-holding-usd"></i> Kurye Ödemesi</h5>
            <p class="small">Kurye ödemeyi yaptığını belirtti, onaylayın:</p>
            <button class="btn btn-success" onclick="confirmReceived(<?= $order['id'] ?>)">
                <i class="fas fa-check"></i> Ödemeyi Aldım
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- 3) Canlı Kurye Haritası (yolda ise) -->
    <?php if ($order['status'] === 'yolda' && $order['courier_id']): ?>
    <div class="card mt-4">
        <div class="card-body">
            <h5><i class="fas fa-map"></i> Canlı Kurye Haritası</h5>
            <div id="miniMap" style="height:300px;border-radius:15px;"></div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const miniMap = L.map('miniMap').setView([<?= $order['dest_lat'] ?>, <?= $order['dest_lng'] ?>], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(miniMap);

        // Müşteri pin
        L.marker([<?= $order['dest_lat'] ?>, <?= $order['dest_lng'] ?>]).addTo(miniMap)
          .bindPopup("Teslimat Adresi<br><?= htmlspecialchars($order['address']) ?>").openPopup();

        // Kurye pin (canlı)
        const courierMarker = L.marker([0, 0], {icon: L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/1163/1163710.png',
            iconSize: [30, 30]
        })}).addTo(miniMap);

        function fetchCourierPos() {
            fetch('../api/get_live_courier_pos.php?order_id=<?= $order['id'] ?>')
              .then(r => r.json())
              .then(data => {
                  if (data.length) {
                      const p = [data[0].lat, data[0].lng];
                      courierMarker.setLatLng(p);
                      miniMap.setView(p, 16);
                  }
              });
        }
        fetchCourierPos();
        setInterval(fetchCourierPos, 15000);
    </script>
    <?php endif; ?>

</div>

<!-- ======= FCM & SERVICE WORKER ======= -->
<script src="../assets/firebase.js"></script>
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('../assets/firebase-messaging-sw.js')
            .then(reg => console.log('SW registered', reg))
            .catch(err => console.error('SW error', err));
    }
</script>

<!-- 2) Onay JavaScripti -->
<script>
function confirmReceived(orderId) {
  if (!confirm('Ödemeyi aldığınızı onaylıyor musunuz?')) return;
  fetch('../api/payment_confirm_restaurant.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'order_id=' + orderId
  })
  .then(r => r.text())
  .then(resp => {
    if (resp === 'OK') location.reload();
    else alert('Hata: ' + resp);
  });
}
</script>
</body>
</html>