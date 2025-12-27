<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit;
}

// Tüm admin kullanıcıları
$admins = $pdo->query("
    SELECT u.id, u.name, u.email, u.phone, u.is_active, ar.role_name, ar.permissions
    FROM users u
    LEFT JOIN admin_roles ar ON u.id = ar.user_id
    WHERE u.role = 'admin'
    ORDER BY u.created_at DESC
")->fetchAll();

// Rol listesi
$roles = $pdo->query("SELECT * FROM admin_roles ORDER BY role_name")->fetchAll();

// Yeni admin ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'] ?? '';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = (int)($_POST['role_id'] ?? 0);

    $pdo->beginTransaction();
    try {
        // Kullanıcı ekle
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, is_active, email_verified) VALUES (?, ?, ?, ?, 'admin', 1, 1)");
        $stmt->execute([$name, $email, $phone, $password]);
        $user_id = $pdo->lastInsertId();

        // Rol ata
        if ($role_id) {
            $pdo->prepare("INSERT INTO admin_roles (user_id, role_name, permissions) VALUES (?, (SELECT role_name FROM admin_roles WHERE id = ?), (SELECT permissions FROM admin_roles WHERE id = ?))")->execute([$user_id, $role_id, $role_id]);
        }

        $pdo->commit();
        $_SESSION['success'] = "Yeni admin kullanıcısı eklendi!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Hata: " . $e->getMessage();
    }
    header("Location: admin_management.php");
    exit;
}

function formatTL($amount) {
    return number_format($amount, 2, ',', '.') . ' ₺';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Yönetici Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{--primary:#FF6B35;--primary-dark:#FF4500;--secondary:#00C853;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Poppins',sans-serif;background:#F5F7FA;padding:15px;overflow-x:hidden;}
        .navbar{background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:clamp(12px,3vw,15px);margin-bottom:25px;border-radius:20px;box-shadow:0 10px 40px rgba(255,107,53,0.4);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
        .navbar-brand{font-weight:900;font-size:clamp(20px,4vw,28px);color:#fff !important;}
        .section{background:#fff;border-radius:20px;padding:clamp(20px,4vw,30px);margin:25px 0;box-shadow:0 15px 40px rgba(0,0,0,0.08);}
        .table{background:#fff;border-radius:15px;overflow:hidden;}
        .table th{background:#FFF8F0;border:none;padding:clamp(12px,3vw,18px);color:var(--primary);font-size:clamp(12px,2.5vw,14px);}
        .table td{border-color:#E0E0E0;padding:clamp(10px,2.5vw,15px);font-size:clamp(12px,2.5vw,14px);}
        .btn-primary-custom{background:linear-gradient(135deg,var(--primary),var(--primary-dark));border:none;border-radius:50px;padding:clamp(10px,2vw,12px) clamp(25px,4vw,30px);font-weight:600;color:#fff;font-size:clamp(12px,2.5vw,14px);}
        .btn-primary-custom:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(255,107,53,0.4);}
        .form-control{border-radius:10px;border:1px solid #E0E0E0;font-size:clamp(14px,2.5vw,16px);}
        .table-responsive{overflow-x:auto;-webkit-overflow-scrolling:touch;}
        table{min-width:700px;}
        h1{font-size:clamp(28px,5vw,42px);}
        
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
        }
        @media (max-width:480px){
            .section{padding:15px;}
            .btn-primary-custom{width:100%;}
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

<div class="main-content">
    <h1 class="text-center mb-5" style="color:var(--primary);font-weight:800;font-size:42px;"><i class="bi bi-people-fill"></i> YÖNETİCİ YÖNETİMİ</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success text-center"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- YENİ YÖNETİCİ EKLE -->
    <div class="section">
        <h5 class="mb-3"><i class="bi bi-person-plus-fill"></i> Yeni Yönetici Ekle</h5>
        <form method="POST" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Ad Soyad</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">E-posta</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Telefon</label>
                <input type="text" name="phone" class="form-control" placeholder="Opsiyonel">
            </div>
            <div class="col-md-4">
                <label class="form-label">Şifre</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Rol</label>
                <select name="role_id" class="form-select">
                    <option value="">Seçiniz</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['role_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" name="add_admin" class="btn btn-primary-custom"><i class="bi bi-save-fill me-2"></i>Ekle</button>
            </div>
        </form>
    </div>

    <!-- MEVCUT YÖNETİCİLER -->
    <div class="section">
        <h5 class="mb-3"><i class="bi bi-list-ul"></i> Mevcut Yöneticiler</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ad</th>
                        <th>E-posta</th>
                        <th>Telefon</th>
                        <th>Rol</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $a): ?>
                        <tr>
                            <td><strong>#<?= $a['id'] ?></strong></td>
                            <td><strong><?= htmlspecialchars($a['name']) ?></strong></td>
                            <td><?= htmlspecialchars($a['email']) ?></td>
                            <td><?= htmlspecialchars($a['phone'] ?? '') ?></td>
                            <td><span class="badge bg-info"><?= htmlspecialchars($a['role_name'] ?? 'Yönetici') ?></span></td>
                            <td>
                                <?php if ($a['is_active']): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Pasif</span>
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