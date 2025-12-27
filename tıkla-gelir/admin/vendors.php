<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit;
}

// Onay / Pasif / Aç / Kapat işlemleri
if (isset($_POST['action']) && isset($_POST['id'])) {
    $id   = (int)$_POST['id'];
    $action = $_POST['action'];
    switch ($action) {
        case 'approve':
            $pdo->prepare("UPDATE businesses SET is_approved = 1 WHERE id = ?")->execute([$id]);
            break;
        case 'reject':
            $pdo->prepare("UPDATE businesses SET is_approved = 0 WHERE id = ?")->execute([$id]);
            break;
        case 'open':
            $pdo->prepare("UPDATE businesses SET is_open = 1 WHERE id = ?")->execute([$id]);
            break;
        case 'close':
            $pdo->prepare("UPDATE businesses SET is_open = 0 WHERE id = ?")->execute([$id]);
            break;
    }
    header("Location: vendors.php"); exit;
}

// Tüm vendorları çek (tek sorgu)
$vendors = $pdo->query("
    SELECT b.*, vt.name AS vendor_name, vt.slug AS vendor_slug, u.name AS owner_name
    FROM businesses b
    JOIN vendor_types vt ON b.vendor_type_id = vt.id
    JOIN users u ON b.user_id = u.id
    ORDER BY b.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tüm Vendorlar - Onay Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --primary: #FF6B35; --success: #00C853; --danger: #F44336; }
        body { font-family: 'Poppins', sans-serif; background: #f5f7fa; padding: 15px; overflow-x: hidden; }
        .card { border-radius: 15px; overflow: hidden; }
        .btn-xs { padding: clamp(4px,1vw,6px) clamp(8px,2vw,12px); font-size: clamp(11px,2vw,12px); border-radius: 20px; }
        .status-on { color: var(--success); }
        .status-off { color: var(--danger); }
        h2 { font-size: clamp(22px,4vw,32px); margin-bottom: 20px; }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { min-width: 700px; }
        th, td { padding: clamp(8px,2vw,12px); font-size: clamp(12px,2.5vw,14px); }
        
        .navbar{background:linear-gradient(135deg,var(--primary),#FF4500);padding:clamp(12px,3vw,15px);margin-bottom:25px;border-radius:20px;box-shadow:0 10px 40px rgba(255,107,53,0.4);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
        .navbar-brand{font-weight:900;font-size:clamp(20px,4vw,28px);color:#fff !important;text-decoration:none;}
        
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
            background: linear-gradient(135deg, var(--primary), #FF4500) !important; 
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
        
        @media (max-width:768px){
            body{padding:10px;}
            .hamburger-menu{display:block;}
            .navbar{flex-direction:column;align-items:flex-start;}
        }
        @media (max-width:480px){
            .btn-xs{width:100%;margin:3px 0;}
            h2{font-size:20px;}
        }
        *{max-width:100%;}
    </style>
</head>
<body class="p-4">
<button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMobileMenu()">
    <i class="bi bi-list"></i>
</button>

<nav class="navbar">
    <a class="navbar-brand" href="index.php"><i class="bi bi-crown-fill"></i> TIKLA GELİR - ADMIN</a>
    <a href="index.php" style="background:#fff;color:var(--primary);border-radius:50px;padding:clamp(8px,2vw,10px) clamp(15px,3vw,20px);font-weight:600;font-size:clamp(12px,2.5vw,14px);text-decoration:none;"><i class="bi bi-arrow-left"></i> Geri</a>
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
    <h2 class="mb-4"><i class="fas fa-store"></i> Tüm Vendorlar – Onay Yönetimi</h2>

    <!-- Özet Kartları -->
    <div class="row mb-4">
        <div class="col-md-3"><div class="card text-center p-3"><h5><?= $pdo->query("SELECT COUNT(*) FROM businesses WHERE is_approved = 1")->fetchColumn() ?></h5><span class="text-muted">Onaylı</span></div></div>
        <div class="col-md-3"><div class="card text-center p-3"><h5><?= $pdo->query("SELECT COUNT(*) FROM businesses WHERE is_approved = 0")->fetchColumn() ?></h5><span class="text-muted">Bekliyor</span></div></div>
        <div class="col-md-3"><div class="card text-center p-3"><h5><?= $pdo->query("SELECT COUNT(*) FROM businesses WHERE is_open = 1")->fetchColumn() ?></h5><span class="text-muted">Açık</span></div></div>
        <div class="col-md-3"><div class="card text-center p-3"><h5><?= $pdo->query("SELECT COUNT(*) FROM businesses")->fetchColumn() ?></h5><span class="text-muted">Toplam</span></div></div>
    </div>

    <!-- Tablo -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>İşletme</th>
                        <th>Tür</th>
                        <th>Sahip</th>
                        <th>Onay</th>
                        <th>Durum</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vendors as $v): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($v['name']) ?></strong><br><small class="text-muted"><?= htmlspecialchars($v['address']) ?></small></td>
                            <td><i class="<?= htmlspecialchars($v['vendor_slug'] === 'restaurant' ? 'fa-utensils' : ($v['vendor_slug'] === 'market' ? 'fa-shopping-basket' : ($v['vendor_slug'] === 'grocery' ? 'fa-carrot' : 'fa-seedling'))) ?>"></i> <?= htmlspecialchars($v['vendor_name']) ?></td>
                            <td><?= htmlspecialchars($v['owner_name']) ?></td>
                            <td>
                                <?php if ($v['is_approved']): ?>
                                    <span class="status-on"><i class="fas fa-check-circle"></i> Onaylı</span>
                                <?php else: ?>
                                    <span class="status-off"><i class="fas fa-clock"></i> Bekliyor</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($v['is_open']): ?>
                                    <span class="status-on"><i class="fas fa-door-open"></i> Açık</span>
                                <?php else: ?>
                                    <span class="status-off"><i class="fas fa-door-closed"></i> Kapalı</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                    <?php if (!$v['is_approved']): ?>
                                        <button type="submit" name="action" value="approve" class="btn btn-success btn-xs">Onayla</button>
                                    <?php else: ?>
                                        <button type="submit" name="action" value="reject" class="btn btn-warning btn-xs">Pasif Yap</button>
                                    <?php endif; ?>
                                    <?php if ($v['is_open']): ?>
                                        <button type="submit" name="action" value="close" class="btn btn-secondary btn-xs">Kapat</button>
                                    <?php else: ?>
                                        <button type="submit" name="action" value="open" class="btn btn-info btn-xs">Aç</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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