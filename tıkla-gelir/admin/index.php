<?php

ini_set('display_errors', 1);

error_reporting(E_ALL);

session_start();

require_once "../config/database.php";



if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {

    header("Location: ../login.php");

    exit;

}



// İSTATİSTİKLER

$total_orders     = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

$total_income     = $pdo->query("SELECT COALESCE(SUM(total_price),0) FROM orders WHERE status != 'iptal'")->fetchColumn();

$today_orders     = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn();

$today_income     = $pdo->query("SELECT COALESCE(SUM(total_price),0) FROM orders WHERE DATE(created_at) = CURDATE() AND status != 'iptal'")->fetchColumn();

$active_couriers  = $pdo->query("SELECT COUNT(*) FROM couriers WHERE is_active = 1")->fetchColumn();

$open_businesses  = $pdo->query("SELECT COUNT(*) FROM businesses WHERE is_open = 1")->fetchColumn();



// YENİ: ÜYE, KURYE, İŞLETME SAYILARI

$total_customers  = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();

$total_couriers   = $pdo->query("SELECT COUNT(*) FROM couriers")->fetchColumn();

$total_businesses = $pdo->query("SELECT COUNT(*) FROM businesses")->fetchColumn();



// Son 10 sipariş - DÜZELTİLDİ

$recent = $pdo->query("

   SELECT o.*, b.name as business_name, u.name as cust_name, cu.name as courier_name

   FROM orders o 

   JOIN businesses b ON o.business_id = b.id 

   JOIN users u ON o.customer_id = u.id 

   LEFT JOIN couriers c ON o.courier_id = c.id

   LEFT JOIN users cu ON c.user_id = cu.id 

   ORDER BY o.created_at DESC LIMIT 10

")->fetchAll();



// Aktif kuryeler

$couriers = $pdo->query("

   SELECT c.id, u.name, u.phone, c.is_active, c.current_order_id 

   FROM couriers c 

   JOIN users u ON c.user_id = u.id

")->fetchAll();



// Promosyonlar (son 10)

$promos = $pdo->query("SELECT * FROM promotions ORDER BY created_at DESC LIMIT 10")->fetchAll();



// Para & tarih formatı

function formatTL($amount)   { return number_format($amount, 2, ',', '.') . ' ₺'; }

function formatDate($date)   { return date('d.m.Y H:i', strtotime($date)); }

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli - Tıkla Gelir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
            background: #F5F7FA;
            color: var(--dark);
            font-family: 'Poppins', sans-serif;
            padding: 15px;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .navbar {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: clamp(15px, 3vw, 20px);
            margin-bottom: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(255,107,53,0.4);
        }
        .navbar-brand {
            font-weight: 900;
            font-size: clamp(20px, 4vw, 28px);
            color: #fff !important;
            letter-spacing: -1px;
        }
        .navbar-nav {
            flex-wrap: wrap;
        }
        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 600;
            margin: 5px;
            padding: clamp(8px, 2vw, 10px) clamp(15px, 3vw, 20px) !important;
            border-radius: 50px;
            transition: all .3s;
            font-size: clamp(12px, 2.5vw, 14px);
        }
        
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
            margin-right: 15px;
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
            display: block !important; 
            width: 100%; 
            padding: 15px 20px; 
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important; 
            color: white !important; 
            border: none; 
            border-radius: 0; 
            text-align: left; 
            text-decoration: none; 
            font-weight: 600; 
            margin-bottom: 5px; 
            font-size: 14px;
        }
        .mobile-menu a:hover {
            opacity: 0.9;
        }
        .mobile-menu a i {
            margin-right: 10px;
            width: 20px;
            display: inline-block;
        }
        .mobile-menu a:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: #fff !important;
            transform: translateY(-2px);
        }
        .page-title {
            font-size: clamp(28px, 5vw, 42px);
            font-weight: 900;
            margin-bottom: 30px;
            text-align: center;
            background: linear-gradient(45deg, var(--primary), var(--secondary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1.5px;
        }
        .stat-card {
            background: var(--card-bg);
            border: 1px solid #E0E0E0;
            border-radius: 25px;
            padding: clamp(25px, 4vw, 40px) clamp(15px, 3vw, 20px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.1);
            transition: all .4s;
            text-align: center;
        }
        .stat-card:hover {
            transform: translateY(-5px) scale(1.01);
            border-color: var(--primary);
        }
        .stat-card i {
            font-size: clamp(36px, 5vw, 50px);
            margin-bottom: 15px;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .stat-number {
            font-size: clamp(32px, 5vw, 44px);
            font-weight: 900;
            background: linear-gradient(45deg, var(--secondary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 10px 0;
        }
        .section {
            background: var(--card-bg);
            border: 1px solid #E0E0E0;
            border-radius: 25px;
            padding: clamp(25px, 4vw, 40px);
            margin: 30px 0;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        .section h2 {
            font-size: clamp(22px, 4vw, 32px);
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 25px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .table-hover tbody tr:hover {
            background: rgba(255,107,53,0.05);
        }
        /* ===== DÜZENLİ ve UYUMLU BUTONLAR ===== */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 50px;
            padding: 12px 24px;          /* küçültüldü */
            font-weight: 600;
            font-size: 14px;             /* dengeli boyut */
            color: #fff;
            transition: all .3s;
            box-shadow: 0 4px 15px rgba(255,107,53,0.4);
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255,107,53,0.6);
        }
        .btn-logout {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border: none;
            padding: clamp(8px, 2vw, 10px) clamp(15px, 3vw, 20px);
            border-radius: 50px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all .3s;
            font-size: clamp(12px, 2.5vw, 14px);
        }
        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255,107,53,0.6);
        }
        
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        table {
            min-width: 800px;
        }
        th, td {
            padding: clamp(8px, 2vw, 12px);
            font-size: clamp(12px, 2.5vw, 14px);
        }
        
        @media (max-width: 768px) {
            body { padding: 10px; }
            .hamburger-menu { display: block !important; }
            .btn-logout { display: none !important; }
            .navbar-nav { display: none !important; }
            .navbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
            }
            .section { padding: 20px; }
        }
        
        @media (max-width: 480px) {
            .stat-card { padding: 20px 15px; }
            .btn-logout { width: 100%; justify-content: center; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMobileMenu()">
        <i class="bi bi-list"></i>
    </button>
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><i class="bi bi-crown-fill"></i> TIKLA GELİR</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link active" href="index.php"><i class="bi bi-speedometer2"></i> Anasayfa</a>
            <a class="nav-link" href="all_orders.php"><i class="bi bi-list-ul"></i> Siparişler</a>
            <a class="nav-link" href="courier_advances.php"><i class="bi bi-cash-stack"></i> Kurye Finans</a>
            <a class="nav-link" href="restaurant_payments.php"><i class="bi bi-bank"></i> Restoran Ödemeleri</a>
            <a class="nav-link" href="reports.php"><i class="bi bi-graph-up"></i> Raporlar</a>
            <a class="nav-link" href="promotions.php"><i class="bi bi-tag-fill"></i> Promosyonlar</a>
            <a class="nav-link" href="vendor_management.php"><i class="bi bi-shop"></i> Vendor Yönetimi</a>
            <a class="nav-link" href="category_management.php"><i class="bi bi-tags"></i> Kategoriler</a>
            <a class="nav-link" href="admin_management.php"><i class="bi bi-people-fill"></i> Admin Yönetimi</a>
            <a class="nav-link" href="live_couriers_map.php"><i class="bi bi-geo-alt-fill"></i> Canlı Harita</a>
            <a class="nav-link" href="settings.php"><i class="bi bi-gear-fill"></i> Ayarlar</a>
        </div>
        <a href="../logout.php" class="btn btn-logout">
            <i class="bi bi-box-arrow-right"></i> Güvenli Çıkış
        </a>
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

<div class="main-content">
    <h1 class="page-title"><i class="bi bi-speedometer2"></i> ADMIN PANELİ</h1>

    <!-- İSTATİSTİK KARTLARI -->
    <div class="row g-4 mb-5">
        <?php
        $stats = [
            ['icon' => 'bi-cart3',           'value' => $total_orders,     'label' => 'Toplam Sipariş'],
            ['icon' => 'bi-currency-dollar', 'value' => formatTL($total_income), 'label' => 'Toplam Hasılat'],
            ['icon' => 'bi-calendar-check',  'value' => $today_orders,     'label' => 'Bugünkü Sipariş'],
            ['icon' => 'bi-graph-up-arrow',  'value' => formatTL($today_income), 'label' => 'Bugünkü Kazanç'],
            ['icon' => 'bi-person-bicycle',  'value' => $active_couriers,  'label' => 'Aktif Kurye'],
            ['icon' => 'bi-shop',            'value' => $open_businesses,  'label' => 'Açık İşletme'],
            ['icon' => 'bi-people-fill',     'value' => $total_customers,  'label' => 'Toplam Üye'],
            ['icon' => 'bi-bicycle',         'value' => $total_couriers,   'label' => 'Toplam Kurye'],
            ['icon' => 'bi-shop-window',     'value' => $total_businesses, 'label' => 'Toplam İşletme']
        ];
        foreach ($stats as $stat):
        ?>
        <div class="col-lg-4 col-md-6">
            <div class="stat-card">
                <i class="bi <?= $stat['icon'] ?>"></i>
                <div class="stat-number"><?= $stat['value'] ?></div>
                <p><?= $stat['label'] ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- HIZLI EYLEM BUTONLARI -->
    <div class="section text-center">
        <h2><i class="bi bi-lightning-fill"></i> Hızlı Eylemler</h2>
        <div class="row g-3 justify-content-center">
            <div class="col-md-6 col-lg-3">
                <a href="add_restaurant.php" class="btn btn-primary-custom">
                    <i class="bi bi-plus-circle-fill me-2"></i> Yeni Restoran
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="add_courier.php" class="btn btn-primary-custom">
                    <i class="bi bi-person-plus-fill me-2"></i> Yeni Kurye
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="live_couriers_map.php" class="btn btn-primary-custom">
                    <i class="bi bi-geo-alt-fill me-2"></i> Canlı Kurye Haritası
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="courier_commissions.php" class="btn btn-primary-custom">
                    <i class="bi bi-percent me-2"></i> Komisyon Oranları
                </a>
            </div>
        </div>
    </div>

    <!-- PROMOSYON YÖNETİMİ BÖLÜMÜ -->
    <div class="section">
        <h2><i class="bi bi-tag-fill"></i> Promosyon Kodları</h2>
        <div class="text-end mb-3">
            <a href="promotions.php" class="btn btn-primary-custom">
                <i class="bi bi-plus-circle-fill me-2"></i> Yeni Promosyon Kodu Ekle
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Kod</th>
                        <th>İndirim (%)</th>
                        <th>Ücretsiz Teslimat</th>
                        <th>Geçerlilik</th>
                        <th>Durum</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($promos as $p): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($p['code']) ?></strong></td>
                        <td>%<?= number_format($p['discount_percent'], 2) ?></td>
                        <td><?= $p['free_delivery'] ? '<span class="badge bg-success">Evet</span>' : '<span class="badge bg-secondary">Hayır</span>' ?></td>
                        <td><?= date('d.m.Y', strtotime($p['valid_until'])) ?></td>
                        <td><?= $p['is_active'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Pasif</span>' ?></td>
                        <td>
                            <a href="promotions.php?edit=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Düzenle</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SON SİPARİŞLER -->
    <div class="section">
        <h2><i class="bi bi-clock-history"></i> Son Siparişler</h2>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th><th>Müşteri</th><th>İşletme</th><th>Tutar</th>
                        <th>Ödeme</th><th>Kurye</th><th>Durum</th><th>Tarih</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recent as $o): ?>
                    <tr>
                        <td><strong>#<?= $o['id'] ?></strong></td>
                        <td><?= htmlspecialchars($o['cust_name']) ?></td>
                        <td><?= htmlspecialchars($o['business_name']) ?></td>
                        <td><strong><?= formatTL($o['total_price']) ?></strong></td>
                        <td><span class="badge bg-<?= $o['payment_method'] == 'online' ? 'info' : 'warning' ?>"><?= strtoupper($o['payment_method']) ?></span></td>
                        <td><?= $o['courier_name'] ? '<span class="badge bg-info">' . htmlspecialchars($o['courier_name']) . '</span>' : '<span class="badge bg-warning">Atanmamış</span>' ?></td>
                        <td><span class="badge bg-<?= $o['status'] == 'teslim' ? 'success' : ($o['status'] == 'iptal' ? 'danger' : 'warning') ?>"><?= strtoupper($o['status']) ?></span></td>
                        <td><?= formatDate($o['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
</script>
</body>
</html>