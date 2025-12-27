<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {
    header("Location: ../login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Komisyon oranını al
$comm = $pdo->prepare("SELECT commission_rate FROM couriers WHERE user_id = ?");
$comm->execute([$user_id]);
$_SESSION['commission_rate'] = $comm->fetchColumn() ?: 10.00;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurye Paneli - Tıkla Gelir</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root{--primary:#FF6B35;--secondary:#00C853;--accent:#FFD700;--light:#F8F9FA;--card-bg:#FFFFFF;--dark:#333333;}
        body{background:#F5F7FA;color:var(--dark);font-family:'Poppins',sans-serif;min-height:100vh;padding:15px;overflow-x:hidden;}
        .container{max-width:1400px;margin:auto;}
        .header{display:flex;justify-content:space-between;align-items:center;padding:clamp(20px, 4vw, 25px) clamp(20px, 4vw, 30px);background:var(--card-bg);border-radius:25px;margin-bottom:30px;box-shadow:0 10px 40px rgba(0,0,0,.1);flex-wrap:wrap;gap:15px;}
        .logo{font-size:clamp(24px, 4vw, 32px);font-weight:800;color:var(--primary);}
        .nav-links{display:flex;gap:10px;flex-wrap:wrap;}
        .nav-links a{color:var(--dark);text-decoration:none;padding:clamp(10px, 2vw, 12px) clamp(20px, 3vw, 25px);border-radius:50px;background:var(--light);transition:.3s;font-weight:600;font-size:clamp(12px, 2.5vw, 14px);}
        .nav-links a:hover{background:var(--primary);color:#fff;}
        .status-section{text-align:center;margin:30px 0;}
        .status-btn{padding:clamp(20px, 3vw, 25px) clamp(50px, 8vw, 70px);font-size:clamp(20px, 3.5vw, 26px);border:none;border-radius:60px;cursor:pointer;transition:all .4s;box-shadow:0 15px 40px rgba(0,0,0,.15);font-weight:800;width:100%;max-width:500px;}
        .status-active{background:linear-gradient(135deg,var(--secondary),#009624);color:white;}
        .status-passive{background:linear-gradient(135deg,#ff4444,#d32f2f);color:white;}
        #statusText{margin-top:15px;font-size:clamp(16px, 2.5vw, 18px);font-weight:600;}
        .gps-section{text-align:center;margin:25px 0;}
        .btn-gps{background:linear-gradient(135deg,var(--accent),#FFC107);color:#333;border:none;border-radius:50px;padding:clamp(10px, 2vw, 12px) clamp(25px, 4vw, 30px);font-weight:600;font-size:clamp(14px, 2.5vw, 16px);}
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(clamp(200px, 25vw, 250px),1fr));gap:clamp(20px, 3vw, 25px);margin-bottom:40px;}
        .stat-card{background:var(--card-bg);padding:clamp(25px, 4vw, 35px) clamp(15px, 3vw, 20px);border-radius:25px;text-align:center;box-shadow:0 10px 35px rgba(0,0,0,.1);}
        .stat-card i{font-size:clamp(36px, 6vw, 45px);color:var(--accent);margin-bottom:15px;}
        .stat-card h3{font-size:clamp(32px, 5vw, 42px);color:var(--secondary);margin:10px 0;font-weight:800;}
        .stat-card p{font-size:clamp(14px, 2.5vw, 16px);}
        .order-item{background:var(--light);border-radius:20px;padding:clamp(20px, 3vw, 25px);margin-bottom:20px;border-left:6px solid var(--accent);}
        .order-item h4{font-size:clamp(18px, 3vw, 20px);margin-bottom:10px;}
        .order-item p{font-size:clamp(14px, 2.5vw, 16px);margin:8px 0;}
        .action-buttons{display:flex;gap:15px;margin-top:20px;flex-wrap:wrap;}
        .btn{padding:clamp(10px, 2vw, 12px) clamp(18px, 3vw, 24px);border:none;border-radius:50px;color:white;font-weight:600;cursor:pointer;font-size:clamp(12px, 2.5vw, 14px);transition:all 0.3s;}
        .btn-success{background:var(--secondary);}
        .btn-success:hover{background:#009624;transform:translateY(-2px);}
        .btn-primary{background:var(--primary);}
        .btn-primary:hover{background:#e65100;transform:translateY(-2px);}
        .commission-card{background:var(--card-bg);padding:clamp(20px, 3vw, 25px);border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,.1);margin:25px 0;text-align:center;}
        .commission-card h5{font-size:clamp(16px, 2.5vw, 18px);}
        .commission-card h2{font-size:clamp(24px, 4vw, 32px);color:var(--primary);}
        .orders-card{background:var(--card-bg);padding:clamp(20px, 4vw, 30px);border-radius:20px;box-shadow:0 10px 35px rgba(0,0,0,.1);}
        .orders-card h2{font-size:clamp(20px, 3vw, 24px);color:var(--primary);margin-bottom:20px;}
        
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
            margin-bottom: 15px;
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
        .mobile-menu a { 
            display: block; 
            width: 100%; 
            padding: 15px 20px; 
            background: linear-gradient(135deg, var(--primary), #FF4500); 
            color: white; 
            border: none; 
            border-radius: 0; 
            text-align: left; 
            text-decoration: none; 
            font-weight: 600; 
            margin-bottom: 5px; 
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .header{flex-direction:column;align-items:flex-start;}
            .nav-links{width:100%;justify-content:center;}
            .hamburger-menu{display:block;}
            .stats-grid{grid-template-columns:repeat(2,1fr);}
            .status-btn{width:100%;}
        }
        
        @media (max-width: 480px) {
            .stats-grid{grid-template-columns:1fr;}
            .action-buttons{flex-direction:column;}
            .btn{width:100%;}
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>

<!-- YENİ SİPARİŞ SESİ -->
<audio id="newOrderSound" src="https://assets.mixkit.co/sfx/preview/mixkit-alarm-tone-1065.mp3" preload="auto"></audio>

<button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMobileMenu()">
    <i class="fas fa-bars"></i>
</button>

<div class="container">
    <div class="header">
        <div class="logo">TIKLA GELİR</div>
        <div class="nav-links" id="navLinks">
            <a href="reports.php">Raporlarım</a>
            <a href="profile.php">Profil</a>
            <a href="history.php">Geçmiş</a>
            <a href="../logout.php">Çıkış</a>
        </div>
    </div>
    
    <!-- MOBILE MENU -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
    <nav class="mobile-menu" id="mobileMenu">
        <a href="reports.php"><i class="fas fa-chart-line"></i> Raporlarım</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
        <a href="history.php"><i class="fas fa-history"></i> Geçmiş</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
    </nav>

    <div class="status-section">
        <button id="statusBtn" class="status-btn status-passive" onclick="toggleStatus()">PASİF</button>
        <p id="statusText" style="margin-top:15px;font-size:18px;font-weight:600;color:#ff4444;">Durum yükleniyor...</p>
    </div>

    <div class="gps-section">
        <button id="btnLocation" class="btn-gps" onclick="startLocation()">Konum Paylaşımını Aç</button>
        <p id="locStatus">Kapalı</p>
    </div>

    <div class="commission-card">
        <h5>Komisyon Oranım</h5>
        <h2><?= $_SESSION['commission_rate'] ?> %</h2>
    </div>

    <div class="stats-grid">
        <div class="stat-card"><i class="fas fa-box"></i><h3 id="todayOrders">0</h3><p>Bugünkü Teslimat</p></div>
        <div class="stat-card"><i class="fas fa-lira-sign"></i><h3 id="todayEarnings">0 ₺</h3><p>Bugünkü Kazanç</p></div>
        <div class="stat-card"><i class="fas fa-wallet"></i><h3 id="totalEarnings">0 ₺</h3><p>Toplam Kazanç</p></div>
        <div class="stat-card"><i class="fas fa-star"></i><h3 id="avgRating">0.0</h3><p>Puan</p></div>
    </div>

    <div class="orders-card">
        <h2>Üstündeki Paketler</h2>
        <div id="orderList">Yükleniyor...</div>
    </div>
</div>

<script>
let currentStatus = 'passive';
let watchId = null;

// Sayfa yüklendiğinde durumu ve konumunu al
async function loadCurrentStatus() {
    try {
        const res = await fetch('api/get_status.php');
        const data = await res.json();
        if (data.success) {
            currentStatus = data.status;
            updateStatusButton(currentStatus === 'active');
            if (currentStatus === 'active') startLocation();
        }
    } catch(e) {
        console.log('Durum yükleme hatası:', e);
    }
}

// Aktif/Pasif butonu
async function toggleStatus() {
    const newStatus = currentStatus === 'active' ? 'passive' : 'active';
    await fetch('api/toggle_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'status=' + newStatus
    });
    currentStatus = newStatus;
    updateStatusButton(currentStatus === 'active');
}

function updateStatusButton(active) {
    const btn = document.getElementById('statusBtn');
    const txt = document.getElementById('statusText');
    if (active) {
        btn.className = 'status-btn status-active';
        btn.innerHTML = 'AKTİF - Paket Alıyorum';
        txt.textContent = 'Aktifsin, paketler geliyor!';
        txt.style.color = '#00C853';
    } else {
        btn.className = 'status-btn status-passive';
        btn.innerHTML = 'PASİF - Yoğunum';
        txt.textContent = 'Pasifsin, yeni paket gelmez.';
        txt.style.color = '#ff4444';
    }
}

// Konum paylaşımı
function startLocation() {
    if (watchId) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
        document.getElementById('locStatus').textContent = 'Kapalı';
        document.getElementById('btnLocation').innerHTML = 'Konum Paylaşımını Aç';
        return;
    }
    watchId = navigator.geolocation.watchPosition(pos => {
        fetch('api/courier_location_push.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `lat=${pos.coords.latitude}&lng=${pos.coords.longitude}`
        });
        document.getElementById('locStatus').textContent = 'Açık';
        document.getElementById('btnLocation').innerHTML = 'Konum Açık';
    }, err => console.log('Konum hatası:', err), {enableHighAccuracy: true});
}

// Sipariş durum güncelle (Yola Çık + Teslim Et)
async function updateOrderStatus(order_id, new_status) {
    if (!confirm(new_status === 'yolda' ? 'Yola çıktın mı?' : 'Teslim ettin mi?')) return;

    const res = await fetch('../api/update_order_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `order_id=${order_id}&status=${new_status}`
    });

    if (res.ok) {
        loadActiveOrders();
        loadStats();
    } else {
        alert('Hata oluştu!');
    }
}

// Aktif siparişleri yükle
async function loadActiveOrders() {
    try {
        const res = await fetch('api/get_courier_orders.php');
        const data = await res.json();

        const list = document.getElementById('orderList');
        if (data.success && data.orders.length > 0) {
            let html = '';
            data.orders.forEach(o => {
                html += `<div class="order-item">
                    <h4>Sipariş #${o.id}</h4>
                    <p><strong>İşletme:</strong> ${o.business_name || 'Bilinmiyor'}</p>
                    <p><strong>Müşteri:</strong> ${o.customer_name}</p>
                    <p><strong>Adres:</strong> ${o.address}</p>
                    <p><strong>Tutar:</strong> ${o.total_price} ₺</p>
                    <div class="action-buttons">
                        ${o.status === 'hazirlaniyor' ? `<button class="btn btn-success" onclick="updateOrderStatus(${o.id}, 'yolda')">Yola Çık</button>` : ''}
                        ${o.status === 'yolda' ? `<button class="btn btn-primary" onclick="updateOrderStatus(${o.id}, 'teslim')">Teslim Et</button>` : ''}
                    </div>
                </div>`;
            });
            list.innerHTML = html;
        } else {
            list.innerHTML = '<p style="text-align:center;color:#999;padding:40px 0;">Aktif paket yok</p>';
        }
    } catch(e) {
        console.log('Sipariş yükleme hatası:', e);
    }
}

// İstatistikleri yükle
async function loadStats() {
    try {
        const res = await fetch('api/courier_stats.php');
        const d = await res.json();
        if (d.success) {
            document.getElementById('todayOrders').textContent = d.today_orders || 0;
            document.getElementById('todayEarnings').textContent = (d.today_earnings || 0) + ' ₺';
            document.getElementById('totalEarnings').textContent = (d.total_earnings || 0) + ' ₺';
            document.getElementById('avgRating').textContent = d.avg_rating || '0.0';
        }
    } catch(e) {
        console.log('İstatistik yükleme hatası:', e);
    }
}

// CANLI BİLDİRİM + SES
let lastCount = 0;
setInterval(async () => {
    try {
        const res = await fetch('api/get_courier_orders_count.php');
        const data = await res.json();
        if (data.count > lastCount) {
            document.getElementById('newOrderSound').play();
            alert('YENİ PAKET VAR!');
            loadActiveOrders();
        }
        lastCount = data.count;
    } catch(e) {
        console.log('Bildirim hatası:', e);
    }
}, 5000);

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

window.addEventListener('DOMContentLoaded', () => {
    loadCurrentStatus();
    loadStats();
    loadActiveOrders();
});
</script>

<!-- ========== FCM CANLI BİLDİRİM ========== -->
<script type="module">
import { initializeApp } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging.js";

const firebaseConfig = {
  apiKey: "AIzaSyD184l78jHw1fwU70jmxLPOiHeZj3Y0",
  authDomain: "kral-kurye.firebaseapp.com",
  projectId: "kral-kurye",
  storageBucket: "kral-kurye.firebasestorage.app",
  messagingSenderId: "842745909494",
  appId: "1:842745909494:web:5b6257cc51f31235039cd7"
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

/* token'ı kaydet */
async function saveToken() {
  try {
    const token = await getToken(messaging, {
      vapidKey: "BB0k6OVcIDftJuxVAb6XTUVnGUUJCUlfAJCpPCgmfiGQcyO8b9Lhnwb3QbUv0K9OZe2QNmJYQmA6acbcIDcEYXY"
    });
    if (token) {
      await fetch('api/save_fcm_token.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'token=' + encodeURIComponent(token)
      });
    }
  } catch(e) {
    console.log('FCM token hatası:', e);
  }
}
document.addEventListener('DOMContentLoaded', saveToken);

/* foreground mesajı */
onMessage(messaging, payload => {
  document.getElementById('newOrderSound').play();
  alert('YENİ PAKET: ' + payload.notification.body);
  loadActiveOrders();   // listeyi yenile
});
</script>
<!-- ========== FCM BİTİŞ ========== -->

</body>
</html>
