<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit;
}

// Tarih filtresi
$start_date = $_GET['start'] ?? date('Y-m-01');
$end_date = $_GET['end'] ?? date('Y-m-d');

// İşletme ödeme listesi
$payments = $pdo->prepare("
    SELECT bp.id, b.name, bp.payment_amount, bp.paid_at, bp.status, bp.notes
    FROM business_payments bp
    JOIN businesses b ON bp.business_id = b.id
    WHERE DATE(bp.paid_at) BETWEEN ? AND ?
    ORDER BY bp.paid_at DESC
");
$payments->execute([$start_date, $end_date]);
$payments = $payments->fetchAll();

// İşletme bakiye özetleri
$balance_summary = $pdo->query("
    SELECT b.id, b.name,
           COALESCE(SUM(CASE WHEN o.status = 'teslim' THEN o.total_price * 0.8 END), 0) as gross_income,
           COALESCE(SUM(bp.payment_amount), 0) as total_paid,
           (COALESCE(SUM(CASE WHEN o.status = 'teslim' THEN o.total_price * 0.8 END), 0) - COALESCE(SUM(bp.payment_amount), 0)) as balance
    FROM businesses b
    LEFT JOIN orders o ON b.id = o.business_id AND o.status = 'teslim'
    LEFT JOIN business_payments bp ON b.id = bp.business_id
    GROUP BY b.id
")->fetchAll();

// Excel export – GEÇİCİ PASİF (vendor yok)
if (isset($_GET['export'])) {
    $_SESSION['error'] = 'Excel kütüphanesi yüklenmedi – rapor ekranından kopyala-yapıştır kullanabilirsiniz.';
    header('Location: restaurant_payments.php');
    exit;
}

function formatTL($amount) {
    return number_format($amount, 2, ',', '.') . ' ₺';
}
function formatDate($date) {
    return date('d.m.Y H:i', strtotime($date));
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - İşletme Ödeme Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #FF4500;
            --secondary: #00C853;
            --light: #F8F9FA;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #F5F7FA;
            color: #333;
            font-family: 'Poppins', sans-serif;
            padding: 15px;
            overflow-x: hidden;
        }
        .navbar {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: clamp(12px, 3vw, 15px);
            margin-bottom: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(255,107,53,0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: clamp(20px, 4vw, 24px);
            color: #fff !important;
        }
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 600;
            margin: 5px;
            font-size: clamp(12px, 2.5vw, 14px);
        }
        .nav-link:hover, .nav-link.active {
            color: #fff !important;
        }
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
        }
        .page-title {
            font-size: clamp(24px, 4vw, 32px);
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .section {
            background: #fff;
            border: 1px solid #E0E0E0;
            border-radius: 20px;
            padding: clamp(20px, 4vw, 30px);
            margin: 25px 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        .table {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
        }
        .table th {
            background: #FFF8F0;
            border: none;
            padding: clamp(12px, 3vw, 18px);
            color: var(--primary);
            font-size: clamp(12px, 2.5vw, 14px);
        }
        .table td {
            border-color: #E0E0E0;
            padding: clamp(10px, 2.5vw, 15px);
            font-size: clamp(12px, 2.5vw, 14px);
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 50px;
            padding: clamp(10px, 2vw, 12px) clamp(25px, 4vw, 30px);
            font-weight: 600;
            color: #fff;
            font-size: clamp(12px, 2.5vw, 14px);
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,107,53,0.4);
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid #E0E0E0;
            font-size: clamp(14px, 2.5vw, 16px);
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        table {
            min-width: 800px;
        }
        h5 {
            font-size: clamp(18px, 3vw, 20px);
        }
        .btn-logout {
            background: white;
            color: var(--primary);
            border: none;
            padding: clamp(8px, 2vw, 10px) clamp(15px, 3vw, 20px);
            border-radius: 50px;
            font-weight: 600;
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
        
        @media (max-width: 768px) {
            body { padding: 10px; }
            .hamburger-menu { display: block; }
            .navbar-nav { display: none !important; }
            .section { padding: 20px; }
            .table-responsive { margin: 0 -10px; padding: 0 10px; }
        }
        
        @media (max-width: 480px) {
            .section { padding: 15px; }
            .btn-primary-custom { width: 100%; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>
<button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMobileMenu()">
    <i class="bi bi-list"></i>
</button>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><i class="bi bi-truck"></i> TIKLA GELİR</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Anasayfa</a>
            <a class="nav-link active" href="restaurant_payments.php"><i class="bi bi-bank"></i> İşletme Ödemeleri</a>
        </div>
        <a href="../logout.php" class="btn btn-logout"><i class="bi bi-box-arrow-right"></i> Çıkış</a>
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
    <h1 class="page-title"><i class="bi bi-bank"></i> İŞLETME ÖDEME YÖNETİMİ</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- TARİH FİLTRESİ -->
    <div class="section">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Başlangıç Tarihi</label>
                <input type="date" name="start" class="form-control" value="<?= $start_date ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Bitiş Tarihi</label>
                <input type="date" name="end" class="form-control" value="<?= $end_date ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary-custom"><i class="bi bi-funnel-fill me-2"></i>Filtrele</button>
            </div>
        </form>
    </div>

    <!-- ÖDEME LİSTESİ -->
    <div class="section">
        <h5 class="mb-3"><i class="bi bi-list-ul"></i> Ödeme Hareketleri</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>İşletme</th>
                        <th>Tutar</th>
                        <th>Ödeme Tarihi</th>
                        <th>Durum</th>
                        <th>Not</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                            <td><strong><?= formatTL($p['payment_amount']) ?></strong></td>
                            <td><?= formatDate($p['paid_at']) ?></td>
                            <td>
                                <?php if ($p['status'] == 'paid'): ?>
                                    <span class="badge bg-success">Ödendi</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Bekliyor</span>
                                <?php endif; ?>
                            </td>
                            <td><small><?= htmlspecialchars($p['notes']) ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- BAKİYE ÖZETİ -->
    <div class="section">
        <h5 class="mb-3"><i class="bi bi-calculator"></i> İşletme Bakiye Özeti</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>İşletme</th>
                        <th>Brüt Kazanç</th>
                        <th>Ödenen</th>
                        <th>KALAN BAKİYE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($balance_summary as $b):
                        $net = $b['gross_income'] - $b['total_paid'];
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($b['name']) ?></strong></td>
                            <td><?= formatTL($b['gross_income']) ?></td>
                            <td><?= formatTL($b['total_paid']) ?></td>
                            <td class="fw-bold <?= $net > 0 ? 'text-warning' : 'text-success' ?>"><?= formatTL($net) ?></td>
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