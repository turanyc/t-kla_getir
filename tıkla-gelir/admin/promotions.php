<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// CRUD işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id               = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $code             = trim($_POST['code']);
    $discount_percent = (float)$_POST['discount_percent'];
    $free_delivery    = isset($_POST['free_delivery']) ? 1 : 0;
    $valid_until      = $_POST['valid_until'];
    $is_active        = isset($_POST['is_active']) ? 1 : 0;

    if ($id) {
        // Güncelle
        $stmt = $pdo->prepare("UPDATE promotions SET code = ?, discount_percent = ?, free_delivery = ?, valid_until = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$code, $discount_percent, $free_delivery, $valid_until, $is_active, $id]);
    } else {
        // Ekle
        $stmt = $pdo->prepare("INSERT INTO promotions (code, discount_percent, free_delivery, valid_until, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$code, $discount_percent, $free_delivery, $valid_until, $is_active]);
    }
    header("Location: promotions.php");
    exit;
}

// Sil
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM promotions WHERE id = ?")->execute([$id]);
    header("Location: promotions.php");
    exit;
}

// Düzenleme modu
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit = $pdo->prepare("SELECT * FROM promotions WHERE id = ?")->execute([$id]);
    $edit = $pdo->fetch();
}

// Tüm promosyonlar
$promos = $pdo->query("SELECT * FROM promotions ORDER BY created_at DESC")->fetchAll();

// Yardımcı fonksiyon
function formatTL($amount) { return number_format($amount, 2, ',', '.') . ' ₺'; }
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Promosyon Yönetimi - Tıkla Gelsin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #FF4500;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #f5f7fa;
            font-family: 'Poppins', sans-serif;
            padding: 15px;
            overflow-x: hidden;
        }
        .navbar {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 20px;
            padding: clamp(15px, 3vw, 20px);
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.4);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .navbar-brand {
            font-weight: 900;
            font-size: clamp(20px, 4vw, 28px);
            color: #fff !important;
            letter-spacing: -1px;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), #e6005c);
            border: none;
            border-radius: 50px;
            padding: clamp(10px, 2vw, 12px) clamp(25px, 4vw, 30px);
            font-weight: 700;
            color: #fff;
            transition: all .3s;
            box-shadow: 0 5px 20px rgba(255,0,102,0.4);
            font-size: clamp(12px, 2.5vw, 14px);
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255,0,102,0.6);
        }
        .card {
            border: none;
            border-radius: 25px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        .card-body {
            padding: clamp(20px, 4vw, 30px);
        }
        .table-hover tbody tr:hover {
            background: rgba(255,107,53,0.05);
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        table {
            min-width: 700px;
        }
        th, td {
            padding: clamp(8px, 2vw, 12px);
            font-size: clamp(12px, 2.5vw, 14px);
        }
        .form-control, .form-select {
            font-size: clamp(14px, 2.5vw, 16px);
        }
        h5 {
            font-size: clamp(18px, 3vw, 20px);
        }
        .btn-light {
            font-size: clamp(12px, 2.5vw, 14px);
            padding: clamp(8px, 2vw, 10px) clamp(15px, 3vw, 20px);
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
            .section { padding: 20px; }
            .table-responsive { margin: 0 -10px; padding: 0 10px; }
        }
        
        @media (max-width: 480px) {
            .card-body { padding: 20px; }
            .btn-primary-custom { width: 100%; }
            .btn-sm { width: 100%; margin: 3px 0; }
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
        <a class="navbar-brand" href="index.php"><i class="bi bi-tag-fill"></i> TIKLA GELİR - Promosyonlar</a>
        <a href="index.php" class="btn btn-light ms-auto">Anasayfa</a>
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

<div class="container">
    <!-- EKLE / DÜZENLE FORMU -->
    <div class="card mb-5">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-plus-circle-fill"></i> <?= $edit ? 'Düzenle' : 'Yeni Promosyon Kodu' ?></h5>
            <form method="post">
                <?php if ($edit): ?>
                    <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                <?php endif; ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Kod</label>
                        <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($edit['code'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">İndirim (%)</label>
                        <input type="number" step="0.01" name="discount_percent" class="form-control" value="<?= $edit['discount_percent'] ?? 0 ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Geçerlilik Tarihi</label>
                        <input type="date" name="valid_until" class="form-control" value="<?= $edit['valid_until'] ?? date('Y-m-d', strtotime('+30 days')) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="free_delivery" id="free_delivery" <?= ($edit['free_delivery'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="free_delivery">Ücretsiz Teslimat</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary-custom"><?= $edit ? 'Güncelle' : 'Ekle' ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- LİSTE -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-list-ul"></i> Tüm Promosyon Kodları</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Kod</th>
                            <th>İndirim</th>
                            <th>Ücretsiz Teslimat</th>
                            <th>Geçerlilik</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($promos as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['code']) ?></strong></td>
                            <td>%<?= number_format($p['discount_percent'], 2) ?></td>
                            <td><?= $p['free_delivery'] ? '<span class="badge bg-success">Evet</span>' : '<span class="badge bg-secondary">Hayır</span>' ?></td>
                            <td><?= date('d.m.Y', strtotime($p['valid_until'])) ?></td>
                            <td><?= $p['is_active'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Pasif</span>' ?></td>
                            <td>
                                <a href="?edit=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Düzenle</a>
                                <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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