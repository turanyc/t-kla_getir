<?php
require_once "config/database.php";

$orderId = (int)($_GET['id'] ?? 0);
$order = $pdo->prepare("
   SELECT o.id, o.status, o.dest_lat, o.dest_lng, o.address,
          c.id courier_id, u.name courier_name
   FROM orders o
   LEFT JOIN couriers c ON o.courier_id = c.id
   LEFT JOIN users u ON c.user_id = u.id
   WHERE o.id = ?
")->execute([$orderId])->fetch();

if (!$order) die('Sipariş bulunamadı');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sipariş Takip - #<?= $orderId ?> | Tıkla Gelsin</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
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
            margin: 0;
            background: #F5F7FA;
            color: var(--dark);
            overflow-x: hidden;
        }
        .top-bar {
            background: var(--primary);
            color: #fff;
            padding: clamp(12px, 3vw, 15px) clamp(15px, 4vw, 20px);
            font-size: clamp(16px, 4vw, 20px);
            font-weight: 700;
        }
        #map {
            height: 60vh;
            min-height: 400px;
            width: 100%;
        }
        
        .hamburger-menu { 
            display: none; 
            background: var(--primary); 
            color: white; 
            border: none; 
            padding: 10px 15px; 
            border-radius: 10px; 
            font-size: 24px; 
            cursor: pointer; 
            z-index: 1001;
            margin: 10px;
            position: absolute;
            top: 0;
            left: 0;
        }
        
        /* MOBILE MENU */
        .mobile-menu { 
            display: none; 
            position: fixed; 
            top: 0; 
            right: -100%; 
            width: 280px; 
            height: 100vh; 
            background: white; 
            box-shadow: -5px 0 20px rgba(0,0,0,0.2); 
            z-index: 9999; 
            transition: right 0.3s ease; 
            overflow-y: auto;
            padding-top: 70px;
        }
        .mobile-menu.open { right: 0; }
        .mobile-menu-overlay { 
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            right: 0; 
            bottom: 0; 
            background: rgba(0,0,0,0.5); 
            z-index: 9998; 
            opacity: 0; 
            visibility: hidden; 
            transition: all 0.3s; 
        }
        .mobile-menu-overlay.active { 
            display: block;
            opacity: 1; 
            visibility: visible; 
        }
        .mobile-menu a, .mobile-menu button { 
            display: block; 
            width: 100%; 
            padding: 15px 20px; 
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)); 
            color: white; 
            border: none; 
            border-radius: 0; 
            text-align: left; 
            text-decoration: none; 
            font-weight: 600; 
            margin-bottom: 10px; 
            cursor: pointer;
        }
        .status-bar {
            display: flex;
            justify-content: space-around;
            padding: clamp(15px, 3vw, 20px);
            background: var(--card-bg);
            border-radius: 15px;
            margin: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            flex-wrap: wrap;
            gap: 10px;
        }
        .status-item {
            text-align: center;
            font-weight: 600;
            color: #999;
            font-size: clamp(12px, 2.5vw, 14px);
            flex: 1;
            min-width: 70px;
        }
        .status-item.active {
            color: var(--primary);
            font-weight: 700;
        }
        .btn-home {
            background: var(--secondary);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: clamp(10px, 2.5vw, 12px) clamp(20px, 4vw, 25px);
            font-weight: 600;
            transition: 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: clamp(12px, 2.5vw, 14px);
            margin: 15px;
        }
        .btn-home:hover {
            background: #009624;
        }
        
        @media (max-width: 768px) {
            .hamburger-menu { display: block; }
            .top-bar { 
                padding-left: 60px; 
                position: relative;
            }
            #map { 
                height: 50vh; 
                min-height: 300px; 
            }
            .status-bar { 
                margin: 10px; 
                padding: 15px 10px;
            }
        }
        
        @media (max-width: 480px) {
            .status-item { 
                font-size: 11px; 
                min-width: 60px;
            }
            #map { 
                height: 45vh; 
                min-height: 250px; 
            }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>
<button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMobileMenu()">
    <i class="fas fa-bars"></i>
</button>

<div class="top-bar">
    <i class="fas fa-utensils"></i> TIKLA GELİR - Sipariş Takip #<?= $orderId ?>
</div>

<!-- MOBILE MENU -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
<nav class="mobile-menu" id="mobileMenu">
    <a href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
    <a href="order_history.php"><i class="fas fa-history"></i> Siparişlerim</a>
    <button onclick="if(confirm('Çıkış yapılsın mı?')) window.location='logout.php'"><i class="fas fa-sign-out-alt"></i> Çıkış</button>
</nav>

<!-- Durum çizgisi -->
<div class="status-bar">
    <?php
    $steps = ['yeni','hazirlaniyor','yolda','teslim'];
    foreach ($steps as $s):
    ?>
        <div class="status-item <?= $order['status'] == $s ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><br><?= ucfirst($s) ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- Harita -->
<div id="map"></div>

<!-- Geri dön -->
<div class="text-center m-4">
    <a href="customer_dashboard.php" class="btn-home">
        <i class="fas fa-arrow-left"></i> Siparişlerime Dön
    </a>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"></script>
<script>
    const map = L.map('map').setView([<?= $order['dest_lat'] ?>, <?= $order['dest_lng'] ?>], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // Müşteri pin
    L.marker([<?= $order['dest_lat'] ?>, <?= $order['dest_lng'] ?>]).addTo(map)
      .bindPopup("Teslimat Adresi<br><?= htmlspecialchars($order['address']) ?>").openPopup();

    // Sadece "yolda" ise kurye konumunu canlı çek
    <?php if ($order['status'] === 'yolda' && $order['courier_id']): ?>
    const courierMarker = L.marker([0, 0], {icon: L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/1163/1163710.png',
        iconSize: [40, 40]
    })}).addTo(map);

    function fetchCourierPos() {
        fetch('api/get_live_courier_pos.php?order_id=<?= $order['id'] ?>')
          .then(r => r.json())
          .then(data => {
              if (data.length) {
                  const p = [data[0].lat, data[0].lng];
                  courierMarker.setLatLng(p);
                  map.setView(p, 16);
              }
          });
    }
    fetchCourierPos();
    setInterval(fetchCourierPos, 15000);
    <?php endif; ?>
</script>

<!-- ======= FCM & SERVICE WORKER ======= -->
<script src="assets/firebase.js"></script>
<script>
// MOBILE MENU
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('mobileMenuOverlay');
    const isOpen = menu.classList.contains('open');
    
    if (isOpen) {
        menu.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    } else {
        menu.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('mobileMenuOverlay');
    menu.classList.remove('open');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('assets/firebase-messaging-sw.js')
            .then(reg => console.log('SW registered', reg))
            .catch(err => console.error('SW error', err));
    }
</script>
</body>
</html>