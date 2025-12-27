<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';
$success = false;

// Yeni vendor tipi ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vendor_type'])) {
    $name = trim($_POST['name']);
    $slug = strtolower(trim($_POST['slug']));
    $icon = trim($_POST['icon']);
    $panel_path = trim($_POST['panel_path']);
    
    if ($name && $slug) {
        $stmt = $pdo->prepare("INSERT INTO vendor_types (name, slug, icon, panel_path) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $slug, $icon, $panel_path])) {
            $message = 'Yeni vendor tipi başarıyla eklendi!';
            $success = true;
        } else {
            $message = 'Hata: Eklenemedi!';
        }
    }
}

// Vendor tipini sil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_vendor_type'])) {
    $id = (int)$_POST['vendor_type_id'];
    $pdo->prepare("DELETE FROM vendor_types WHERE id = ?")->execute([$id]);
    $message = 'Vendor tipi silindi!';
    $success = true;
}

// Vendor tipleri
$vendor_types = $pdo->query("SELECT * FROM vendor_types ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Yönetimi - Tıkla Gelir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--primary:#FF6B35;--primary-dark:#FF4500;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Poppins',sans-serif;background:#F5F7FA;padding:15px;overflow-x:hidden;}
        .navbar{background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:clamp(15px,3vw,20px);margin-bottom:25px;border-radius:20px;box-shadow:0 10px 40px rgba(255,107,53,0.4);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
        .navbar-brand{font-weight:900;font-size:clamp(20px,4vw,28px);color:#fff !important;}
        .section{background:#fff;border-radius:25px;padding:clamp(25px,4vw,40px);margin:25px 0;box-shadow:0 15px 40px rgba(0,0,0,0.1);}
        .category-card{background:#f8f9fa;border:2px solid #E0E0E0;border-radius:15px;padding:clamp(15px,3vw,20px);margin-bottom:15px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;}
        .btn-delete{background:#F44336;color:#fff;border:none;padding:clamp(8px,2vw,10px) clamp(15px,3vw,20px);border-radius:50px;font-weight:600;font-size:clamp(12px,2.5vw,14px);}
        h1{font-size:clamp(28px,5vw,42px);}
        h3{font-size:clamp(20px,4vw,24px);}
        h5{font-size:clamp(16px,3vw,18px);}
        .form-control,.form-select{font-size:clamp(14px,2.5vw,16px);}
        .form-label{font-size:clamp(13px,2.5vw,14px);}
        
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
        
        @media (max-width:768px){
            body{padding:10px;}
            .hamburger-menu{display:block;}
            .section{padding:20px;}
            .category-card{flex-direction:column;align-items:flex-start;}
        }
        @media (max-width:480px){
            .section{padding:15px;}
            .btn-delete{width:100%;}
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

<div class="container">
    <h1 class="text-center mb-5" style="color:var(--primary);font-weight:800;font-size:42px;"><i class="bi bi-tags"></i> KATEGORİ YÖNETİMİ</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-<?= $success ? 'success' : 'danger' ?> text-center"><?= $message ?></div>
    <?php endif; ?>
    
    <!-- YENİ VENDOR TİPİ EKLE -->
    <div class="section">
        <h3 style="color:var(--primary);margin-bottom:25px;"><i class="bi bi-plus-circle"></i> Yeni Vendor Tipi Ekle</h3>
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">İsim</label>
                    <input type="text" name="name" class="form-control" placeholder="örn: Kafe" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Slug (URL)</label>
                    <input type="text" name="slug" class="form-control" placeholder="örn: cafe" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">İkon (FontAwesome)</label>
                    <input type="text" name="icon" class="form-control" placeholder="fas fa-coffee" value="fas fa-store">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Panel Yolu</label>
                    <input type="text" name="panel_path" class="form-control" placeholder="cafe/">
                </div>
            </div>
            <button type="submit" name="add_vendor_type" class="btn btn-lg mt-3" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff;border-radius:50px;padding:15px 40px;font-weight:700;"><i class="bi bi-plus-circle"></i> Ekle</button>
        </form>
    </div>
    
    <!-- MEVCUT VENDOR TİPLERİ -->
    <div class="section">
        <h3 style="color:var(--primary);margin-bottom:25px;"><i class="bi bi-list-ul"></i> Mevcut Vendor Tipleri (<?= count($vendor_types) ?>)</h3>
        
        <?php foreach($vendor_types as $vt): ?>
            <div class="category-card">
                <div>
                    <h5><i class="<?= $vt['icon'] ?>"></i> <?= htmlspecialchars($vt['name']) ?></h5>
                    <p class="mb-0"><strong>Slug:</strong> <?= $vt['slug'] ?> | <strong>Panel:</strong> <?= $vt['panel_path'] ?: 'Yok' ?></p>
                </div>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="vendor_type_id" value="<?= $vt['id'] ?>">
                    <button type="submit" name="delete_vendor_type" class="btn-delete" onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')"><i class="bi bi-trash"></i> Sil</button>
                </form>
            </div>
        <?php endforeach; ?>
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