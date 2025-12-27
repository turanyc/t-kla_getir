<?php

session_start();

require_once "../config/database.php";



if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {

    header("Location: login.php");

    exit;

}



// Ayarları kaydet

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $merchant_id   = $_POST['paytr_merchant_id']   ?? '';
    $merchant_key  = $_POST['paytr_merchant_key']  ?? '';
    $merchant_salt = $_POST['paytr_merchant_salt'] ?? '';
    $test_mode     = isset($_POST['paytr_test_mode']) ? 1 : 0;

    // KEY-VALUE sistemi – doğru şekilde
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value, setting_type, category) VALUES
                  ('paytr_merchant_id',   ?, 'text', 'payment'),
                  ('paytr_merchant_key',  ?, 'text', 'payment'),
                  ('paytr_merchant_salt', ?, 'text', 'payment'),
                  ('paytr_test_mode',     ?, 'number', 'payment')")
         ->execute([$merchant_id, $merchant_key, $merchant_salt, $test_mode]);

    $_SESSION['success'] = "PayTR ayarları güncellendi!";
    header("Location: settings.php");
    exit;
}



// Mevcut ayarlar

$settings = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();



function formatDate($date) {

    return date('d.m.Y H:i', strtotime($date));

}

?>

<!DOCTYPE html>

<html lang="tr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin - Sistem Ayarları</title>

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

        .btn-primary-custom { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border: none; border-radius: 50px; padding: clamp(10px, 2vw, 12px) clamp(25px, 4vw, 30px); font-weight: 600; color: #fff; font-size: clamp(12px, 2.5vw, 14px); }

        .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,107,53,0.4); }

        .btn-logout { background: white; color: var(--primary); border: none; padding: clamp(8px, 2vw, 10px) clamp(15px, 3vw, 20px); border-radius: 50px; font-weight: 600; font-size: clamp(12px, 2.5vw, 14px); }

        .form-control { border-radius: 10px; border: 1px solid #E0E0E0; font-size: clamp(14px, 2.5vw, 16px); }

        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 0.2rem rgba(255,107,53,0.25); }

        .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }

        .alert-info { background-color: #FFF8F0; border-color: var(--primary); color: #333; font-size: clamp(14px, 2.5vw, 16px); }

        .btn-outline-light { border-color: var(--primary); color: var(--primary); border-radius: 10px; font-size: clamp(12px, 2.5vw, 14px); }

        .btn-outline-light:hover { background: var(--primary); color: #fff; }
        h5 { font-size: clamp(18px, 3vw, 20px); }
        li { font-size: clamp(14px, 2.5vw, 16px); }
        
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
        }
        
        @media (max-width: 480px) {
            .section { padding: 15px; }
            .btn-primary-custom { width: 100%; }
            .btn-outline-light { width: 100%; }
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
            <a class="nav-link active" href="settings.php"><i class="bi bi-gear-fill"></i> Ayarlar</a>
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

    <h1 class="page-title"><i class="bi bi-gear-fill"></i> SİSTEM AYARLARI & PayTR Entegrasyonu</h1>



    <?php if (isset($_SESSION['success'])): ?>

        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>

    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>

        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>

    <?php endif; ?>



    <div class="row">

        <div class="col-lg-6">

            <div class="section">

                <h5 class="mb-3" style="color: var(--primary);"><i class="bi bi-credit-card-2-front-fill"></i> PayTR Ödeme Ayarları</h5>

                <form method="POST">

                    <div class="mb-3">

                        <label class="form-label">Merchant ID</label>

                        <input type="text" name="paytr_merchant_id" class="form-control" value="<?= htmlspecialchars($settings['paytr_merchant_id']) ?>" required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Merchant Key</label>

                        <input type="text" name="paytr_merchant_key" class="form-control" value="<?= htmlspecialchars($settings['paytr_merchant_key']) ?>" required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Merchant Salt</label>

                        <input type="text" name="paytr_merchant_salt" class="form-control" value="<?= htmlspecialchars($settings['paytr_merchant_salt']) ?>" required>

                    </div>

                    <div class="form-check form-switch mb-3">

                        <input class="form-check-input" type="checkbox" name="paytr_test_mode" <?= $settings['paytr_test_mode'] ? 'checked' : '' ?>>

                        <label class="form-check-label">Test Modu (Gerçek ödeme alma)</label>

                    </div>

                    <div class="alert alert-info">

                        <i class="bi bi-info-circle-fill"></i> PayTR bilgilerinizi <a href="https://www.paytr.com/magaza/ayarlar" target="_blank" style="color: var(--primary);">PayTR Mağaza Paneli</a>'nden alın.

                    </div>

                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-save-fill me-2"></i>Ayarları Kaydet</button>

                </form>

            </div>

        </div>

        

        <div class="col-lg-6">

            <div class="section">

                <h5 class="mb-3" style="color: var(--primary);"><i class="bi bi-info-circle-fill"></i> Sistem Bilgileri</h5>

                <ul class="list-unstyled">

                    <li class="mb-3"><i class="bi bi-calendar-check-fill text-success me-2"></i><strong>Son Güncelleme:</strong> <?= formatDate($settings['updated_at']) ?></li>

                    <li class="mb-3"><i class="bi bi-shield-lock-fill text-primary me-2"></i><strong>Güvenlik:</strong> SSL Aktif, Güvenli Oturum</li>

                    <li class="mb-3"><i class="bi bi-database-fill text-warning me-2"></i><strong>Veritabanı:</strong> <?= $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) ?></li>

                    <li class="mb-3"><i class="bi bi-person-fill me-2" style="color: var(--primary);"></i><strong>Admin Kullanıcı:</strong> <?= htmlspecialchars($_SESSION['user_name'] ?? 'Bilinmiyor') ?></li>

                    <li class="mb-3"><i class="bi bi-gear-fill text-secondary me-2"></i><strong>Komisyon Oranı:</strong> <?= $settings['commission'] ?>%</li>

                </ul>

                

                <hr class="my-4">

                

                <h6 class="text-center mb-3" style="color: var(--primary);">Hızlı Kısayollar</h6>

                <div class="d-grid gap-2">

                    <a href="reports.php" class="btn btn-outline-light"><i class="bi bi-graph-up me-2"></i>Raporlara Git</a>

                    <a href="restaurant_payments.php" class="btn btn-outline-light"><i class="bi bi-bank me-2"></i>Restoran Ödemeleri</a>

                    <a href="courier_advances.php" class="btn btn-outline-light"><i class="bi bi-cash-stack me-2"></i>Kurye Finans</a>

                </div>

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