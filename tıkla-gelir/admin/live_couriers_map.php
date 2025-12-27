<?php
session_start();

// DOĞRU YOL! (admin içindesin → ../../config)
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canlı Kurye Haritası - Tıkla Gelir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root{--primary:#FF6B35;--primary-dark:#FF4500;--secondary:#00C853;}
        body{font-family:'Poppins',sans-serif;background:#F5F7FA;margin:0;padding:15px;overflow-x:hidden;}
        .navbar{background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:clamp(12px, 3vw, 15px);margin-bottom:25px;border-radius:15px;box-shadow:0 5px 20px rgba(255,107,53,0.3);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
        .navbar-brand{font-weight:800;font-size:clamp(20px, 4vw, 24px);color:#fff !important;}
        .btn-back{background:white;color:var(--primary);border:none;padding:clamp(8px, 2vw, 10px) clamp(15px, 3vw, 20px);border-radius:50px;font-weight:600;font-size:clamp(12px, 2.5vw, 14px);}
        .map-container{background:white;border-radius:20px;padding:clamp(15px, 3vw, 20px);box-shadow:0 5px 20px rgba(0,0,0,0.1);margin-bottom:20px;}
        #map{height:clamp(400px, 50vh, 600px);border-radius:15px;width:100%;}
        .courier-list{background:white;border-radius:20px;padding:clamp(15px, 3vw, 20px);box-shadow:0 5px 20px rgba(0,0,0,0.1);margin-top:20px;}
        .courier-list h4{font-size:clamp(18px, 3vw, 20px);color:var(--primary);margin-bottom:15px;}
        .courier-item{padding:clamp(12px, 2.5vw, 15px);border-bottom:1px solid #E0E0E0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
        .courier-item:last-child{border-bottom:none;}
        .courier-item > div:first-child{flex:1;min-width:200px;}
        .courier-item strong{font-size:clamp(14px, 2.5vw, 16px);display:block;margin-bottom:5px;}
        .courier-item small{font-size:clamp(12px, 2vw, 14px);}
        .badge-active{background:var(--secondary);color:white;padding:clamp(5px, 1vw, 7px) clamp(12px, 2vw, 15px);border-radius:50px;font-size:clamp(11px, 2vw, 12px);}
        .badge-inactive{background:#ccc;color:#666;padding:clamp(5px, 1vw, 7px) clamp(12px, 2vw, 15px);border-radius:50px;font-size:clamp(11px, 2vw, 12px);}
        h2{font-size:clamp(20px, 4vw, 28px);color:var(--primary);font-weight:700;text-align:center;margin-bottom:20px;}
        
        .hamburger-menu { 
            display: none; 
            background: rgba(255,255,255,0.2); 
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
            display: block; 
            position: fixed; 
            top: 0; 
            right: -100%; 
            width: 280px; 
            max-width: 80vw;
            height: 100vh; 
            background: white; 
            box-shadow: -5px 0 20px rgba(0,0,0,0.2); 
            z-index: 9999; 
            transition: right 0.3s ease; 
            overflow-y: auto;
            padding: 100px 0 20px 0;
        }
        .mobile-menu.open { 
            right: 0;
            display: block;
        }
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
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)); 
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
            body { padding: 10px; }
            .hamburger-menu { display: block; }
            .btn-back { display: none; }
            #map { height: 400px; }
            .courier-item { flex-direction: column; align-items: flex-start; }
        }
        
        @media (max-width: 480px) {
            #map { height: 350px; }
            .courier-item { padding: 12px; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>
<button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMobileMenu()">
    <i class="bi bi-list"></i>
</button>

<nav class="navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">TIKLA GELİR</a>
        <a href="index.php" class="btn-back">Geri Dön</a>
    </div>
</nav>

<!-- MOBILE MENU -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
<nav class="mobile-menu" id="mobileMenu">
    <a href="index.php"><i class="bi bi-speedometer2"></i> Anasayfa</a>
    <a href="all_orders.php"><i class="bi bi-list-ul"></i> Siparişler</a>
    <a href="courier_advances.php"><i class="bi bi-cash-stack"></i> Kurye Finans</a>
    <a href="restaurant_payments.php"><i class="bi bi-bank"></i> Restoran Ödemeleri</a>
    <a href="reports.php"><i class="bi bi-graph-up"></i> Raporlar</a>
    <a href="promotions.php"><i class="bi bi-tag-fill"></i> Promosyonlar</a>
    <a href="vendor_management.php"><i class="bi bi-shop"></i> Vendor Yönetimi</a>
    <a href="category_management.php"><i class="bi bi-tags"></i> Kategoriler</a>
    <a href="admin_management.php"><i class="bi bi-people-fill"></i> Admin Yönetimi</a>
    <a href="live_couriers_map.php"><i class="bi bi-geo-alt-fill"></i> Canlı Harita</a>
    <a href="settings.php"><i class="bi bi-gear-fill"></i> Ayarlar</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Güvenli Çıkış</a>
</nav>

<div class="container-fluid">
    <h2 class="text-center mb-4" style="color:var(--primary);font-weight:700;">
        Canlı Kurye Haritası
    </h2>
    
    <div class="map-container">
        <div id="map"></div>
    </div>
    
    <div class="courier-list">
        <h4 style="color:var(--primary);">Aktif Kuryeler</h4>
        <div id="courierList">Yükleniyor...</div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([39.9334, 32.8597], 6);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

const courierMarkers = {};

const courierIcon = L.icon({
    iconUrl: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0Ij48cGF0aCBmaWxsPSIjRkY2QjM1IiBkPSJNMTIgMkM4LjEzIDIgNSA1LjEzIDUgOWMwIDUuMjUgNyAxMyA3IDEzczctNy43NSA3LTEzYzAtMy44Ny0zLjEzLTctNy03em0wIDkuNWMtMS4zOCAwLTIuNS0xLjEyLTIuNS0yLjVzMS4xMi0yLjUgMi41LTIuNSAyLjUgMS4xMiAyLjUgMi41LTEuMTIgMi41LTIuNSAyLjV6Ii8+PC9zdmc+',
    iconSize: [30, 30],
    iconAnchor: [15, 30],
    popupAnchor: [0, -30]
});

function timeAgoText(dateStr) {
    const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
    if (diff < 60) return diff + ' saniye önce';
    if (diff < 3600) return Math.floor(diff / 60) + ' dakika önce';
    if (diff < 86400) return Math.floor(diff / 3600) + ' saat önce';
    return Math.floor(diff / 86400) + ' gün önce';
}

function loadCouriers() {
    fetch('../courier/api/courier_location_push.php')
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(data => {
            const listEl = document.getElementById('courierList');
            if (!data.success || data.couriers.length === 0) {
                listEl.innerHTML = '<p class="text-center text-muted">Henüz konum paylaşan kurye yok</p>';
                return;
            }

            let html = '';
            data.couriers.forEach(c => {
                const statusBadge = c.is_active ? '<span class="badge-active">AKTİF</span>' : '<span class="badge-inactive">PASİF</span>';
                const timeAgo = c.recorded_at ? timeAgoText(c.recorded_at) : 'Bilinmiyor';

                html += `
                    <div class="courier-item">
                        <div>
                            <strong>${c.courier_name}</strong><br>
                            <small class="text-muted">${c.phone || '-'}</small><br>
                            <small>Son güncelleme: ${timeAgo}</small>
                        </div>
                        <div>
                            ${statusBadge}
                            ${c.current_order_id ? '<br><span class="badge bg-warning mt-1">SİPARİŞTE</span>' : ''}
                        </div>
                    </div>
                `;

                if (c.lat && c.lng) {
                    const key = c.courier_id;
                    if (courierMarkers[key]) {
                        courierMarkers[key].setLatLng([c.lat, c.lng]);
                    } else {
                        L.marker([c.lat, c.lng], {icon: courierIcon})
                            .addTo(map)
                            .bindPopup(`<b>${c.courier_name}</b><br>${statusBadge}<br>${c.current_order_id ? 'Sipariş: #' + c.current_order_id : 'Boşta'}`);
                        courierMarkers[key] = true;
                    }
                }
            });

            listEl.innerHTML = html;

            if (Object.keys(courierMarkers).length > 0) {
                const group = new L.featureGroup(Object.values(courierMarkers));
                map.fitBounds(group.getBounds().pad(0.2));
            }
        })
        .catch(err => {
            console.error('Hata:', err);
            document.getElementById('courierList').innerHTML = '<p class="text-center text-danger">Sunucu hatası</p>';
        });
}

window.addEventListener('DOMContentLoaded', () => {
    loadCouriers();
    setInterval(loadCouriers, 10000);
});

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
</script>

</body>
</html>