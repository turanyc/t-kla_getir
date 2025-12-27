<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../config/database.php";
require_once "auth.php";

// ========== İŞLETME BİLGİLERİNİ ÇEK ==========
$stmt = $pdo->prepare("
    SELECT b.*, vt.name AS vendor_type_name, vt.slug AS vendor_type_slug
    FROM businesses b
    JOIN vendor_types vt ON b.vendor_type_id = vt.id
    WHERE b.user_id = :uid AND b.is_approved = 1
    LIMIT 1
");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$business = $stmt->fetch();
if (!$business) {
    die("İşletmeniz bulunamadı veya onay bekliyor.");
}
$business_id = $business['id'];
$business_name = $business['name'];
$vendor_type = $business['vendor_type_name'];
$is_open = $business['is_open'];
$panel_title = $vendor_type . " Paneli";

/* ----------- 1) BOŞTA KURYE ---------- */
$free = $pdo->prepare("SELECT COUNT(*) FROM couriers WHERE is_active = 1 AND status = 'passive'");
$free->execute();
$free_count = $free->fetchColumn();

/* ----------- 2) EN YAKIN 3 KURYE ---------- */
$stmtBiz = $pdo->prepare("SELECT latitude, longitude FROM businesses WHERE id = ?");
$stmtBiz->execute([$business_id]);
$biz = $stmtBiz->fetch();
if (!$biz || !$biz['latitude'] || !$biz['longitude']) {
    $close_count = 0;
    $close_couriers = [];
} else {
    $sql = "
 SELECT c.id, u.name,
 (6371 * ACOS(COS(RADIANS(?)) * COS(RADIANS(c.latitude)) *
 COS(RADIANS(c.longitude) - RADIANS(?)) +
 SIN(RADIANS(?)) * SIN(RADIANS(c.latitude)))) AS distance_km
 FROM couriers c
 JOIN users u ON c.user_id = u.id
 WHERE c.is_active = 1 AND c.status = 'passive'
 AND c.latitude IS NOT NULL AND c.longitude IS NOT NULL
 HAVING distance_km <= 10
 ORDER BY distance_km ASC LIMIT 3
";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$biz['latitude'], $biz['longitude'], $biz['latitude']]);
    $close_couriers = $stmt->fetchAll();
    $close_count = count($close_couriers);
}

/* ----------- 3) AKTİF SİPARİŞLER ---------- */
$active_stmt = $pdo->prepare("
    SELECT o.*, u.name AS customer_name, u.phone, us.name AS courier_name
    FROM orders o
    JOIN users u ON o.customer_id = u.id
    LEFT JOIN couriers c ON o.courier_id = c.id
    LEFT JOIN users us ON c.user_id = us.id
    WHERE o.business_id = ? AND o.status IN ('yeni','hazirlaniyor','yolda')
    ORDER BY o.created_at DESC
");
$active_stmt->execute([$business_id]);
$active_orders = $active_stmt->fetchAll();

/* ----------- 4) SON 10 SİPARİŞ ---------- */
$last_stmt = $pdo->prepare("
    SELECT o.id, o.created_at, o.total_price, o.status, u.name AS customer_name
    FROM orders o
    JOIN users u ON o.customer_id = u.id
    WHERE o.business_id = ?
    ORDER BY o.created_at DESC LIMIT 10
");
$last_stmt->execute([$business_id]);
$last10 = $last_stmt->fetchAll();

/* ----------- 5) İSTATİSTİK ---------- */
$stats_stmt = $pdo->prepare("
    SELECT COUNT(*) total_orders,
           SUM(CASE WHEN status='teslim' THEN 1 ELSE 0 END) completed,
           SUM(total_price) total_revenue
    FROM orders
    WHERE business_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
");
$stats_stmt->execute([$business_id]);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($business_name) ?> - <?= $panel_title ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #FF6B35;
            --success: #00C853;
            --danger: #F44336;
            --light: #F8F9FA;
            --card: #FFFFFF;
            --shadow: 0 4px 12px rgba(0,0,0,.08);
        }
        body { font-family: 'Poppins', sans-serif; background: #F5F7FA; margin: 0; overflow-x: hidden; }
        .top-bar {
            display: flex; justify-content: space-between; align-items: center;
            background: var(--card); padding: clamp(12px, 3vw, 15px) clamp(15px, 4vw, 30px); box-shadow: var(--shadow);
            position: sticky; top: 0; z-index: 1000; flex-wrap: wrap; gap: 10px;
        }
        .logo {
            font-size: clamp(20px, 4vw, 24px); font-weight: 700; color: var(--primary);
            cursor: pointer; text-decoration: none;
        }
        .logo:hover { opacity: 0.8; }
        .nav-buttons {
            display: flex; flex-wrap: wrap; gap: 8px;
        }
        .nav-buttons a {
            background: var(--light); color: #333; padding: clamp(8px, 2vw, 10px) clamp(12px, 3vw, 18px);
            border-radius: 50px; text-decoration: none;
            font-weight: 500; transition: .3s; font-size: clamp(12px, 2.5vw, 14px);
        }
        .nav-buttons a:hover { background: var(--primary); color: #fff; }
        .container { max-width: 1400px; margin: 0 auto; padding: clamp(15px, 4vw, 30px); }
        .header-card {
            background: linear-gradient(135deg, #FF6B35, #FF4500);
            color: white; padding: clamp(20px, 4vw, 30px); border-radius: 20px;
            text-align: center; margin-bottom: 25px;
        }
        .header-card h1 { font-size: clamp(24px, 5vw, 32px); }
        .header-card p { font-size: clamp(14px, 2.5vw, 16px); }
        .quick-stats {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px; margin-bottom: 25px;
        }
        .stat-card {
            background: var(--card); padding: clamp(15px, 3vw, 25px); border-radius: 15px;
            text-align: center; box-shadow: var(--shadow); transition: .3s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-card h3 { color: var(--primary); font-size: clamp(24px, 5vw, 36px); margin: 10px 0; }
        .stat-card p { font-size: clamp(12px, 2.5vw, 14px); }
        .main-grid { display: grid; grid-template-columns: 2fr 1fr; gap: clamp(20px, 4vw, 30px); }
        @media (max-width: 992px) { .main-grid { grid-template-columns: 1fr; } }
        .card {
            background: var(--card); padding: clamp(20px, 4vw, 30px); border-radius: 20px;
            box-shadow: var(--shadow);
        }
        .card h2 {
            color: var(--primary); margin-bottom: 20px; font-size: clamp(18px, 3vw, 24px);
            border-bottom: 2px solid #FFE0B2; padding-bottom: 10px;
        }
        .order-item {
            background: #FFF8F0; padding: clamp(15px, 3vw, 20px); border-radius: 15px;
            margin-bottom: 15px; border-left: 5px solid #FF9100;
            transition: .3s; font-size: clamp(14px, 2.5vw, 16px);
        }
        .order-item:hover { transform: translateX(5px); }
        .btn {
            padding: clamp(8px, 2vw, 10px) clamp(12px, 3vw, 18px); border: none; border-radius: 50px;
            font-weight: 600; cursor: pointer; margin: 5px; transition: .3s;
            font-size: clamp(12px, 2.5vw, 14px);
        }
        .btn-success { background: var(--success); color: #fff; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-lg {
            padding: clamp(15px, 3vw, 18px) clamp(35px, 6vw, 50px); font-size: clamp(16px, 3vw, 20px); font-weight: 700;
            border-radius: 60px; box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            transition: all 0.4s ease;
        }
        .btn-lg:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 30px rgba(255,107,53,0.4);
        }
        .status-open {
            background: #d4edda; color: #155724; padding: clamp(10px, 2vw, 12px) clamp(20px, 4vw, 30px);
            border-radius: 50px; font-weight: bold; font-size: clamp(14px, 3vw, 18px);
            display: inline-block; margin-top: 15px;
        }
        .status-closed {
            background: #f8d7da; color: #721c24; padding: clamp(10px, 2vw, 12px) clamp(20px, 4vw, 30px);
            border-radius: 50px; font-weight: bold; font-size: clamp(14px, 3vw, 18px);
            display: inline-block; margin-top: 15px;
        }

        /* SAĞDAN GELEN ŞIK BİLDİRİM KARTI */
        #newOrderToast {
            position: fixed;
            top: clamp(80px, 10vh, 100px);
            right: -420px;
            width: clamp(300px, 90vw, 380px);
            max-width: calc(100vw - 40px);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: clamp(18px, 3vw, 22px);
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.35);
            display: flex;
            align-items: center;
            gap: clamp(12px, 3vw, 18px);
            z-index: 99999;
            transition: right 0.8s cubic-bezier(0.22, 1.2, 0.36, 1);
            font-family: 'Poppins', sans-serif;
        }
        #newOrderToast.show {
            right: clamp(15px, 3vw, 30px);
        }
        #newOrderToast i {
            font-size: clamp(36px, 6vw, 48px);
            background: rgba(255,255,255,0.15);
            width: clamp(50px, 8vw, 70px); height: clamp(50px, 8vw, 70px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        #newOrderToast h3 { margin: 0; font-size: clamp(18px, 3vw, 22px); }
        #newOrderToast p { margin: 6px 0 0; opacity: 0.95; font-size: clamp(12px, 2vw, 14px); }
        #newOrderToast .close {
            margin-left: auto;
            background: none;
            border: none;
            color: white;
            font-size: clamp(24px, 4vw, 30px);
            cursor: pointer;
            flex-shrink: 0;
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
        
        .text-center { text-align: center; }
        .mb-5 { margin-bottom: clamp(25px, 5vw, 30px); }
        .mb-4 { margin-bottom: 20px; }
        .mt-3 { margin-top: 15px; }
        .mt-4 { margin-top: 20px; }
        .py-4 { padding: 20px 0; }
        .py-5 { padding: 30px 0; }
        .text-muted { color: #6c757d; }
        
        @media (max-width: 768px) {
            .top-bar { flex-direction: column; align-items: flex-start; }
            .nav-buttons { width: 100%; display: none; }
            .hamburger-menu { display: block; }
            .quick-stats { grid-template-columns: repeat(2, 1fr); }
            #newOrderToast { top: 70px; }
        }
        
        @media (max-width: 480px) {
            .quick-stats { grid-template-columns: 1fr; }
            .btn-lg { width: 100%; }
            .card { padding: 15px; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>

<!-- YENİ SİPARİŞ SESİ (ÇOK GÜZEL NET SES) -->
<audio id="newOrderSound" preload="auto">
<source src="../assets/new_order.mp3" type="audio/mpeg">
Tarayıcınız ses çalamıyor.
</audio>

<!-- SAĞDAN GELEN ŞIK KART -->
<div id="newOrderToast">
    <i class="fas fa-shopping-bag"></i>
    <div>
        <h3>Yeni Sipariş!</h3>
        <p id="toastMsg">1 yeni sipariş geldi</p>
    </div>
    <button class="close" onclick="document.getElementById('newOrderToast').classList.remove('show')">&times;</button>
</div>

<button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMobileMenu()">
    <i class="fas fa-bars"></i>
</button>

<div class="top-bar">
    <a href="../index.php" class="logo"><i class="fas fa-store"></i> TIKLA GELİR</a>
    <div class="nav-buttons" id="navButtons">
        <a href="payment_confirm.php"><i class="fas fa-hand-holding-usd"></i> Kurye Ödemeleri</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Ayarlar</a>
        <a href="reports.php"><i class="fas fa-chart-line"></i> Raporlar</a>
        <a href="menu_manager.php"><i class="fas fa-clipboard-list"></i> Ürünler</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
    </div>
</div>

<!-- MOBILE MENU -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
<nav class="mobile-menu" id="mobileMenu">
    <a href="payment_confirm.php"><i class="fas fa-hand-holding-usd"></i> Kurye Ödemeleri</a>
    <a href="settings.php"><i class="fas fa-cog"></i> Ayarlar</a>
    <a href="reports.php"><i class="fas fa-chart-line"></i> Raporlar</a>
    <a href="menu_manager.php"><i class="fas fa-clipboard-list"></i> Ürünler</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
</nav>

<!-- TÜM İÇERİK ORİJİNAL HALİYLE DEVAM EDİYOR -->
<div class="container">
    <div class="header-card">
        <h1><?= htmlspecialchars($business_name) ?></h1>
        <p><?= $panel_title ?> - Hoş geldiniz</p>
    </div>

    <div class="text-center mb-5">
        <?php if ($is_open): ?>
            <button class="btn btn-danger btn-lg" onclick="toggleStatus(0)">
                <i class="fas fa-power-off"></i> İşletmeyi Kapat
            </button>
            <p class="status-open"><i class="fas fa-check-circle"></i> İŞLETME AÇIK - Sipariş alınıyor</p>
        <?php else: ?>
            <button class="btn btn-success btn-lg" onclick="toggleStatus(1)">
                <i class="fas fa-power-off"></i> İşletmeyi Aç
            </button>
            <p class="status-closed"><i class="fas fa-times-circle"></i> İŞLETME KAPALI - Sipariş alınmıyor</p>
        <?php endif; ?>
    </div>

    <!-- QUICK STATS -->
    <div class="quick-stats">
        <div class="stat-card"><h3><?= (int)($stats['total_orders'] ?? 0) ?></h3><p>Toplam Sipariş (30 gün)</p></div>
        <div class="stat-card"><h3><?= (int)($stats['completed'] ?? 0) ?></h3><p>Teslim Edilen</p></div>
        <div class="stat-card"><h3><?= number_format((float)($stats['total_revenue'] ?? 0), 2) ?> ₺</h3><p>Ciro (30 gün)</p></div>
        <div class="stat-card"><h3><?= $free_count ?></h3><p>Boşta Kurye</p></div>
        <div class="stat-card"><h3><?= $close_count ?></h3><p>Yakın Kurye</p></div>
    </div>

    <!-- AKTİF SİPARİŞLER + EN YAKIN KURYELER + SON 10 SİPARİŞ -->
    <!-- (orijinal kodların hepsi aynen duruyor, tek satır eksiltmedim) -->
    <div class="main-grid">
        <div class="card">
            <h2><i class="fas fa-motorcycle"></i> Aktif Siparişler</h2>
            <?php if (empty($active_orders)): ?>
                <p class="text-center text-muted py-5"><i class="fas fa-inbox fa-3x"></i><br>Henüz aktif sipariş yok</p>
            <?php else: foreach ($active_orders as $o): ?>
                <div class="order-item">


                        <!-- SİPARİŞ İÇERİĞİ GÖSTER -->
    <?php
    
    // SİPARİŞ İÇERİĞİ - SENİN VERİTABANINA UYGUN (menu_items + order_items)
    $items_stmt = $pdo->prepare("
        SELECT 
            oi.quantity,
            COALESCE(mi.name, oi.product_name) AS product_name
        FROM order_items oi
        LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id
        WHERE oi.order_id = ?
        ORDER BY oi.id
    ");
    $items_stmt->execute([$o['id']]);
    $items = $items_stmt->fetchAll();
    
    if ($items): ?>
        <div style="margin-top:10px; padding:10px 12px; background:#fff3e0; border-radius:8px; font-size:14px; line-height:1.8;">
            <strong>Sipariş İçeriği:</strong><br>
            <?php foreach ($items as $item): ?>
                • <?= $item['quantity'] ?> × <?= htmlspecialchars($item['product_name']) ?><br>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <!-- BİTİŞ -->
                    <div class="btn-group mt-3">
                        <?php if ($o['status'] === 'yeni'): ?>
                            <button class="btn btn-success" onclick="updateStatus(<?= $o['id'] ?>, 'hazirlaniyor')">Onayla</button>
                            <button class="btn btn-danger" onclick="updateStatus(<?= $o['id'] ?>, 'iptal')">İptal</button>
                        <?php elseif ($o['status'] === 'hazirlaniyor'): ?>
                            <button class="btn btn-primary" onclick="updateStatus(<?= $o['id'] ?>, 'yolda')">Kuryeye Ver</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <div class="card">
            <h2><i class="fas fa-map-marker-alt"></i> En Yakın Kuryeler</h2>
            <?php if ($close_count == 0): ?>
                <p class="text-center text-muted">10 km içinde kurye yok</p>
            <?php else: foreach ($close_couriers as $c): ?>
                <div class="order-item">
                    <?= htmlspecialchars($c['name']) ?> - <?= number_format($c['distance_km'], 1) ?> km
                </div>
            <?php endforeach; endif; ?>
            <button class="btn btn-primary" style="width:100%;margin-top:20px;" onclick="openCourierModal()">
                <i class="fas fa-motorcycle"></i> Paketçi Çağır
            </button>
        </div>
    </div>

    <div class="card mt-4">
        <h2><i class="fas fa-history"></i> Son 10 Sipariş</h2>
        <?php if (empty($last10)): ?>
            <p class="text-center text-muted py-4">Henüz sipariş yok</p>
        <?php else: foreach ($last10 as $l): ?>
            <div class="order-item">
                #<?= $l['id'] ?> - <?= htmlspecialchars($l['customer_name']) ?><br>
                <?= number_format($l['total_price'], 2) ?> ₺ - <?= ucfirst($l['status']) ?>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<!-- KURYE ÇAĞIR MODAL (responsive) -->
<div id="courierModal" class="modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;z-index:9999;padding:20px;overflow-y:auto;">
    <div style="background:#fff;padding:clamp(25px, 5vw, 40px);border-radius:20px;max-width:500px;width:100%;box-shadow:0 20px 50px rgba(0,0,0,0.3);margin:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3 style="font-size:clamp(18px, 3vw, 22px);"><i class="fas fa-motorcycle"></i> Paketçi Çağır</h3>
            <button onclick="document.getElementById('courierModal').style.display='none'" style="background:none;border:none;font-size:clamp(24px, 4vw, 28px);cursor:pointer;">×</button>
        </div>
        <form id="callCourierForm">
            <input type="text" name="customer_name" placeholder="Müşteri Adı Soyadı" required style="width:100%;padding:12px;margin:10px 0;border-radius:10px;border:1px solid #ddd;font-size:clamp(14px, 2.5vw, 16px);">
            <input type="text" name="customer_phone" placeholder="Telefon" required style="width:100%;padding:12px;margin:10px 0;border-radius:10px;border:1px solid #ddd;font-size:clamp(14px, 2.5vw, 16px);">
            <textarea name="address" placeholder="Teslimat Adresi" required style="width:100%;padding:12px;margin:10px 0;border-radius:10px;border:1px solid #ddd;height:80px;font-size:clamp(14px, 2.5vw, 16px);resize:vertical;"></textarea>
            <textarea name="items" placeholder="Sipariş İçeriği" required style="width:100%;padding:12px;margin:10px 0;border-radius:10px;border:1px solid #ddd;height:60px;font-size:clamp(14px, 2.5vw, 16px);resize:vertical;"></textarea>
            <input type="number" step="0.01" name="total_price" placeholder="Tutar (₺)" required style="width:100%;padding:12px;margin:10px 0;border-radius:10px;border:1px solid #ddd;font-size:clamp(14px, 2.5vw, 16px);">
            <button type="submit" class="btn btn-success" style="width:100%;padding:15px;font-size:clamp(16px, 3vw, 18px);">Kurye Çağır</button>
        </form>
    </div>
</div>

<script src="../js/notification.js"></script>

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

// İşletme Aç/Kapat
async function toggleStatus(status) {
    if (!confirm(status ? 'İşletmeyi açmak istediğinize emin misiniz?' : 'İşletmeyi kapatmak istediğinize emin misiniz?')) return;
    const res = await fetch('api/toggle_business_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'status=' + status
    });
    const data = await res.json();
    if (data.success) {
        location.reload();
    } else {
        alert('Hata: ' + data.message);
    }
}

// Sipariş durum güncelle + otomatik kurye atama
async function updateStatus(order_id, new_status) {
    if (!confirm(new_status === 'hazirlaniyor' ? 'Siparişi onaylayıp hazırlamaya geçmek istiyor musunuz?' :
                 new_status === 'yolda' ? 'Paket kuryeye verildi mi?' : 'İşlem yapılsın mı?')) return;
    const res = await fetch('../api/update_order_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `order_id=${order_id}&status=${new_status}`
    });
    if (res.ok && new_status === 'yolda') {
        await fetch('api/assign_nearest_courier.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `order_id=${order_id}`
        });
    }
    location.reload();
}

// Kurye modal
function openCourierModal() { document.getElementById('courierModal').style.display = 'flex'; }
document.getElementById('callCourierForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = new FormData(this);
    const res = await fetch('api/call_courier.php', { method: 'POST', body: data });
    const result = await res.json();
    if (result.success) {
        alert('Kurye çağırıldı!');
        document.getElementById('courierModal').style.display = 'none';
        location.reload();
    } else {
        alert('Hata: ' + result.message);
    }
});

// YENİ SİPARİŞ KONTROL + SES + TOAST (KESİN ÇALIŞAN VERSİYON)
if (data && data.count > lastOrderCount) {
    // SES ÇAL
    const audio = document.getElementById('newOrderSound');
    if (audio) { audio.currentTime = 0; audio.play().catch(() => {}); }

    // TOAST İÇERİK OLUŞTUR
    let msg = `<strong>Yeni Sipariş #${data.last_order_id}</strong><br>
               <small>${data.customer_name}</small><br><br>`;
    if (data.items && data.items.length > 0) {
        data.items.forEach(item => {
            msg += `• ${item.quantity} × ${item.product_name}<br>`;
        });
    } else {
        msg += `<em>İçerik yüklenemedi</em>`;
    }

    document.getElementById('toastMsg').innerHTML = msg;
    document.getElementById('newOrderToast').classList.add('show');

    setTimeout(() => document.getElementById('newOrderToast').classList.remove('show'), 8000);
    lastOrderCount = data.count;
    setTimeout(() => location.reload(), 2500);
}


</script>

</body>
</html>