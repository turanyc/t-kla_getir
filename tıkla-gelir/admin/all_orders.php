<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Kurye değiştirme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_courier'])) {
    $order_id = $_POST['order_id'];
    $new_courier_id = $_POST['new_courier_id'] ?: null;
    $old_courier_id = $_POST['old_courier_id'];

    try {
        $pdo->beginTransaction();

        if ($old_courier_id) {
            $pdo->prepare("UPDATE couriers SET current_order_id = NULL WHERE id = ?")->execute([$old_courier_id]);
        }

        $pdo->prepare("UPDATE orders SET courier_id = ? WHERE id = ?")->execute([$new_courier_id, $order_id]);

        if ($new_courier_id) {
            $pdo->prepare("UPDATE couriers SET current_order_id = ? WHERE id = ?")->execute([$order_id, $new_courier_id]);
        }

        $pdo->commit();
        $_SESSION['success'] = "Kurye başarıyla değiştirildi!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Hata: " . $e->getMessage();
    }
    header("Location: all_orders.php");
    exit;
}

// Tüm siparişler - DÜZELTİLDİ
$orders = $pdo->query("SELECT o.*, b.name as business_name, u.name as cust_name, cu.name as courier_name
                      FROM orders o 
                      JOIN businesses b ON o.business_id = b.id 
                      JOIN users u ON o.customer_id = u.id 
                      LEFT JOIN couriers c ON o.courier_id = c.id
                      LEFT JOIN users cu ON c.user_id = cu.id
                      ORDER BY o.created_at DESC LIMIT 100")->fetchAll();

// Aktif kuryeler
$couriers = $pdo->query("SELECT c.id, u.name FROM couriers c JOIN users u ON c.user_id = u.id WHERE c.is_active = 1")->fetchAll();

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
    <title>Admin - Tüm Siparişler</title>
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
        body { background: #F5F7FA; color: #333; font-family: 'Poppins', sans-serif; padding: 15px; overflow-x: hidden; }
        .navbar { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); padding: clamp(12px, 3vw, 15px); margin-bottom: 25px; border-radius: 15px; box-shadow: 0 5px 20px rgba(255,107,53,0.3); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .navbar-brand { font-weight: 800; font-size: clamp(20px, 4vw, 24px); color: #fff !important; }
        .nav-link { color: rgba(255,255,255,0.9) !important; font-weight: 600; margin: 5px; font-size: clamp(12px, 2.5vw, 14px); }
        .nav-link:hover, .nav-link.active { color: #fff !important; }
        .main-content { max-width: 1400px; margin: 0 auto; }
        .page-title { font-size: clamp(24px, 4vw, 32px); font-weight: 700; margin-bottom: 25px; text-align: center; background: linear-gradient(45deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .section { background: #fff; border: 1px solid #E0E0E0; border-radius: 20px; padding: clamp(20px, 4vw, 30px); margin: 25px 0; box-shadow: 0 5px 20px rgba(0,0,0,0.08); }
        .table { background: #fff; border-radius: 15px; overflow: hidden; }
        .table th { background: #FFF8F0; border: none; padding: clamp(12px, 3vw, 18px); color: var(--primary); font-size: clamp(12px, 2.5vw, 14px); }
        .table td { border-color: #E0E0E0; padding: clamp(10px, 2.5vw, 15px); font-size: clamp(12px, 2.5vw, 14px); }
        .btn-primary-custom { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border: none; border-radius: 50px; padding: clamp(10px, 2vw, 12px) clamp(25px, 4vw, 30px); font-weight: 600; color: #fff; font-size: clamp(12px, 2.5vw, 14px); }
        .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,107,53,0.4); }
        .btn-outline-light { border-radius: 50px; border-color: var(--primary); color: var(--primary); font-size: clamp(11px, 2vw, 12px); padding: clamp(6px, 1.5vw, 8px) clamp(12px, 2.5vw, 15px); }
        .btn-outline-light:hover { background: var(--primary); color: #fff; }
        .btn-logout { background: white; color: var(--primary); border: none; padding: clamp(8px, 2vw, 10px) clamp(15px, 3vw, 20px); border-radius: 50px; font-weight: 600; font-size: clamp(12px, 2.5vw, 14px); }
        .btn-logout:hover { background: #f8f9fa; }
        .modal-content { background: #fff; border: 1px solid #E0E0E0; color: #333; }
        .form-select, .form-control { border-radius: 10px; font-size: clamp(14px, 2.5vw, 16px); }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { min-width: 1000px; }
        
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
            .navbar-nav { display: none !important; }
            .section { padding: 20px; }
            .table-responsive { margin: 0 -10px; padding: 0 10px; }
        }
        
        @media (max-width: 480px) {
            .section { padding: 15px; }
            .btn-outline-light { width: 100%; margin: 5px 0; }
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
            <a class="nav-link active" href="all_orders.php"><i class="bi bi-list-ul"></i> Tüm Siparişler</a>
        </div>
        <a href="../logout.php" class="btn btn-logout"><i class="bi bi-box-arrow-right"></i> Güvenli Çıkış</a>
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
    <h1 class="page-title"><i class="bi bi-list-ul"></i> TÜM SİPARİŞLER - KURYE YÖNETİMİ</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="section">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th><th>Müşteri</th><th>İşletme</th><th>Tutar</th>
                        <th>Ödeme</th><th>Atanan Kurye</th><th>Durum</th><th>Tarih</th><th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $o): ?>
                    <tr>
                        <td><strong>#<?= $o['id'] ?></strong></td>
                        <td><?= htmlspecialchars($o['cust_name']) ?></td>
                        <td><?= htmlspecialchars($o['business_name']) ?></td>
                        <td><?= formatTL($o['total_price']) ?></td>
                        <td><span class="badge bg-<?= $o['payment_method'] == 'online' ? 'info' : 'warning' ?>"><?= strtoupper($o['payment_method']) ?></span></td>
                        <td><?php if ($o['courier_name']): ?><span class="badge bg-info"><?= htmlspecialchars($o['courier_name']) ?></span><?php else: ?><span class="badge bg-warning">Atanmamış</span><?php endif; ?></td>
                        <td><span class="badge bg-<?= $o['status'] == 'teslim' ? 'success' : ($o['status'] == 'iptal' ? 'danger' : 'warning') ?>"><?= strtoupper($o['status']) ?></span></td>
                        <td><?= formatDate($o['created_at']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#courierModal<?= $o['id'] ?>">
                                <i class="bi bi-person-bicycle"></i> Kurye Değiştir
                            </button>
                        </td>
                    </tr>

                    <!-- Kurye Değiştirme Modal -->
                    <div class="modal fade" id="courierModal<?= $o['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Sipariş #<?= $o['id'] ?> - Kurye Değiştir</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                        <input type="hidden" name="old_courier_id" value="<?= $o['courier_id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Yeni Kurye Seçin</label>
                                            <select name="new_courier_id" class="form-select">
                                                <option value="">-- Kuryesiz --</option>
                                                <?php foreach($couriers as $c): ?>
                                                <option value="<?= $c['id'] ?>" <?= $c['id'] == $o['courier_id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Mevcut kurye atanacak siparişten alınacaktır!
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="change_courier" class="btn btn-primary-custom">Kuryeyi Güncelle</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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