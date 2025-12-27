<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
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
    header("Location: vendor_management.php");
    exit;
}

// Tüm işletmeleri çek – businesses tablosu
$vendors = $pdo->query("
    SELECT b.*, vt.name AS vendor_name, vt.slug AS vendor_slug, u.name AS owner_name, u.email AS owner_email, u.phone
    FROM businesses b
    JOIN vendor_types vt ON b.vendor_type_id = vt.id
    JOIN users u ON b.user_id = u.id
    ORDER BY b.created_at DESC
")->fetchAll();

// Onay bekleyen işletmeler
$pending = $pdo->query("
    SELECT b.*, vt.name AS vendor_type_name, u.name AS owner_name
    FROM businesses b
    JOIN vendor_types vt ON b.vendor_type_id = vt.id
    JOIN users u ON b.user_id = u.id
    WHERE b.is_approved = 0
    ORDER BY b.created_at DESC
")->fetchAll();

// Vendor tipleri
$vendor_types = $pdo->query("SELECT * FROM vendor_types ORDER BY name")->fetchAll();

function formatTL($amount) {
    return number_format($amount, 2, ',', '.') . ' ₺';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - İşletme Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--primary:#FF6B35;--primary-dark:#FF4500;--secondary:#00C853;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Poppins',sans-serif;background:#F5F7FA;padding:15px;overflow-x:hidden;}
        .navbar{background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:clamp(12px,3vw,15px);margin-bottom:25px;border-radius:20px;box-shadow:0 10px 40px rgba(255,107,53,0.4);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
        .navbar-brand{font-weight:900;font-size:clamp(20px,4vw,28px);color:#fff !important;}
        .section{background:#fff;border-radius:20px;padding:clamp(20px,4vw,30px);margin:25px 0;box-shadow:0 15px 40px rgba(0,0,0,0.08);}
        .vendor-card{background:#fff;border:2px solid #E0E0E0;border-radius:20px;padding:clamp(15px,3vw,20px);margin-bottom:15px;transition:all .3s;}
        .vendor-card:hover{border-color:var(--primary);transform:translateY(-3px);box-shadow:0 10px 30px rgba(0,0,0,0.15);}
        .badge-vendor{padding:clamp(6px,1.5vw,8px) clamp(12px,2.5vw,15px);border-radius:50px;font-size:clamp(11px,2vw,12px);font-weight:600;}
        .btn-approve{background:var(--secondary);color:#fff;border:none;padding:clamp(8px,2vw,10px) clamp(15px,3vw,20px);border-radius:50px;font-weight:600;font-size:clamp(12px,2.5vw,14px);}
        .btn-reject{background:#F44336;color:#fff;border:none;padding:clamp(8px,2vw,10px) clamp(15px,3vw,20px);border-radius:50px;font-weight:600;font-size:clamp(12px,2.5vw,14px);}
        .table-responsive{overflow-x:auto;-webkit-overflow-scrolling:touch;}
        table{min-width:800px;}
        th,td{padding:clamp(8px,2vw,12px);font-size:clamp(12px,2.5vw,14px);}
        h1{font-size:clamp(28px,5vw,42px);}
        h2{font-size:clamp(20px,4vw,28px);}
        h4{font-size:clamp(16px,3vw,20px);}
        
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
        
        @media (max-width:768px){
            body{padding:10px;}
            .hamburger-menu{display:block;}
            .section{padding:20px;}
            .vendor-card{flex-direction:column;gap:15px;}
            .vendor-card .d-flex{flex-direction:column !important;align-items:flex-start !important;}
        }
        @media (max-width:480px){
            .section{padding:15px;}
            .btn-approve,.btn-reject{width:100%;margin:5px 0;}
        }
        *{max-width:100%;}
    </style>
</head>
<body>
<button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMobileMenu()">
    <i class="bi bi-list"></i>
</button>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><i class="bi bi-crown-fill"></i> TIKLA GELİR - ADMIN</a>
        <a href="index.php" class="btn" style="background:#fff;color:var(--primary);border-radius:50px;padding:clamp(8px,2vw,10px) clamp(15px,3vw,20px);font-weight:600;font-size:clamp(12px,2.5vw,14px);"><i class="bi bi-arrow-left"></i> Geri</a>
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
    <h1 class="text-center mb-5" style="color:var(--primary);font-weight:800;font-size:42px;"><i class="bi bi-shop"></i> İŞLETME YÖNETİMİ</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center">
            <?php if ($_GET['success'] === 'approved'): ?>
                ✅ İşletme başarıyla onaylandı!
            <?php elseif ($_GET['success'] === 'rejected'): ?>
                🗑️ İşletme reddedildi.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- ONAY BEKLEYEN İŞLETMELER -->
    <?php if (!empty($pending)): ?>
    <div class="section">
        <h2 style="color:var(--primary);margin-bottom:30px;"><i class="bi bi-hourglass-split"></i> Onay Bekleyen İşletmeler (<?= count($pending) ?>)</h2>
        <?php foreach($pending as $v): ?>
            <div class="vendor-card" style="border-color:#FFC107;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4><i class="<?= $v['vendor_slug'] === 'restaurant' ? 'fa-utensils' : ($v['vendor_slug'] === 'market' ? 'fa-shopping-basket' : ($v['vendor_slug'] === 'grocery' ? 'fa-carrot' : 'fa-seedling')) ?>"></i> <?= htmlspecialchars($v['name']) ?></h4>
                        <p class="mb-1"><strong>Tip:</strong> <span class="badge-vendor" style="background:rgba(255,107,53,0.2);color:var(--primary);"><?= $v['vendor_name'] ?></span></p>
                        <p class="mb-1"><strong>Sahibi:</strong> <?= htmlspecialchars($v['owner_name']) ?></p>
                        <p class="mb-0"><small class="text-muted">Kayıt: <?= date('d.m.Y H:i', strtotime($v['created_at'])) ?></small></p>
                    </div>
                    <div>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $v['id'] ?>">
                            <button type="submit" name="action" value="approve" class="btn-approve me-2" onclick="return confirm('Bu işletmeyi onaylamak istediğinize emin misiniz?')">✅ Onayla</button>
                            <button type="submit" name="action" value="reject" class="btn-reject" onclick="return confirm('Bu işletmeyi reddetmek istediğinize emin misiniz?')">❌ Reddet</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- TÜM İŞLETMELER -->
    <div class="section">
        <h2 style="color:var(--primary);margin-bottom:30px;"><i class="bi bi-list-ul"></i> Tüm İşletmeler (<?= count($vendors) ?>)</h2>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead style="background:rgba(255,107,53,0.1);">
                    <tr>
                        <th>ID</th>
                        <th>İsim</th>
                        <th>Tip</th>
                        <th>Sahibi</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Durum</th>
                        <th>Onay</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($vendors as $v): ?>
                        <tr>
                            <td><strong>#<?= $v['id'] ?></strong></td>
                            <td><i class="<?= $v['vendor_slug'] === 'restaurant' ? 'fa-utensils' : ($v['vendor_slug'] === 'market' ? 'fa-shopping-basket' : ($v['vendor_slug'] === 'grocery' ? 'fa-carrot' : 'fa-seedling')) ?>"></i> <?= htmlspecialchars($v['name']) ?></td>
                            <td><span class="badge-vendor" style="background:rgba(255,107,53,0.2);color:var(--primary);"><?= $v['vendor_name'] ?></span></td>
                            <td><?= htmlspecialchars($v['owner_name']) ?></td>
                            <td><?= htmlspecialchars($v['owner_email']) ?></td>
                            <td><?= htmlspecialchars($v['phone'] ?? '') ?></td>
                            <td>
                                <?php if ($v['is_open']): ?>
                                    <span class="badge bg-success">Açık</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Kapalı</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($v['is_approved']): ?>
                                    <span class="badge bg-success">✅ Onaylı</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">⏳ Bekliyor</span>
                                <?php endif; ?>
                            </td>
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