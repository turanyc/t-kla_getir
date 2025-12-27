<?php
session_start();
require_once "config/database.php";

$customer_id = $_SESSION['user_id'] ?? 0;
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

// Son 10 sipariş + takip linkleri
$orders = $pdo->prepare("
   SELECT o.id, o.created_at, o.total_price, o.status, r.name rest_name,
          o.dest_lat, o.dest_lng
   FROM orders o
   JOIN restaurants r ON o.restaurant_id = r.id
   WHERE o.customer_id = ?
   ORDER BY o.created_at DESC
   LIMIT 10
");
$orders->execute([$customer_id]);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Siparişlerim - Tıkla Gelsin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 15px; 
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: clamp(20px, 4vw, 25px) clamp(20px, 4vw, 30px);
            background: var(--card-bg);
            border-radius: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border: 1px solid #E0E0E0;
            flex-wrap: wrap;
            gap: 15px;
        }
        .logo {
            font-size: clamp(20px, 4vw, 32px);
            font-weight: 800;
            color: var(--primary);
        }
        .nav-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .nav-links a {
            color: var(--dark);
            text-decoration: none;
            padding: clamp(10px, 2vw, 12px) clamp(15px, 3vw, 25px);
            border-radius: 50px;
            background: var(--light);
            transition: 0.3s;
            font-weight: 600;
            font-size: clamp(12px, 2.5vw, 14px);
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
            margin-bottom: 10px; 
        }
        .nav-links a:hover {
            background: var(--primary);
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(255,107,53,0.3);
        }
        .section {
            background: var(--card-bg);
            border-radius: 25px;
            padding: clamp(25px, 4vw, 35px);
            margin-bottom: 30px;
            border: 1px solid #E0E0E0;
            box-shadow: 0 10px 35px rgba(0,0,0,0.1);
        }
        .section h2 {
            font-size: clamp(18px, 3vw, 24px);
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            transition: 0.3s;
            box-shadow: 0 5px 20px rgba(255,107,53,0.4);
        }
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255,107,53,0.6);
        }
        .table-hover tbody tr:hover {
            background: rgba(255,107,53,0.05);
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        table {
            width: 100%;
            min-width: 600px;
        }
        th, td {
            padding: clamp(8px, 2vw, 12px);
            font-size: clamp(12px, 2.5vw, 14px);
            white-space: nowrap;
        }
        
        @media (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 10px; }
            .hamburger-menu { display: block; }
            .header { flex-direction: column; align-items: flex-start; }
            .nav-links { display: none; }
            .section { padding: 20px; }
        }
        
        @media (max-width: 480px) {
            .section { padding: 15px; }
            table { font-size: 12px; }
            th, td { padding: 8px 5px; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>
<div class="container">
    <button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMobileMenu()">
        <i class="fas fa-bars"></i>
    </button>
    
    <div class="header">
        <div class="logo"><i class="fas fa-utensils"></i> TIKLA GELİR</div>
        <div class="nav-links">
            <a href="order_history.php"><i class="fas fa-history"></i> Sipariş Geçmişim</a>
            <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
        </div>
    </div>

<!-- MOBILE MENU -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
<nav class="mobile-menu" id="mobileMenu">
    <a href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
    <a href="order_history.php"><i class="fas fa-history"></i> Sipariş Geçmişim</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
</nav>

    <div class="section">
        <h2><i class="fas fa-list"></i> Siparişlerim</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Restoran</th>
                        <th>Tutar</th>
                        <th>Durum</th>
                        <th>Tarih</th>
                        <th>Takip</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><strong><?= $o['id'] ?></strong></td>
                        <td><?= htmlspecialchars($o['rest_name']) ?></td>
                        <td><strong><?= number_format($o['total_price'], 2) ?> ₺</strong></td>
                        <td>
                            <span class="badge bg-<?= $o['status'] == 'teslim' ? 'success' : ($o['status'] == 'iptal' ? 'danger' : 'warning') ?>">
                                <?= strtoupper($o['status']) ?>
                            </span>
                        </td>
                        <td><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
                        <td>
                            <a href="order_track.php?id=<?= $o['id'] ?>" class="btn btn-primary-custom btn-sm">
                                <i class="fas fa-map-marker-alt"></i> Takip Et
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ======= FCM & SERVICE WORKER ======= -->
<script src="assets/firebase.js"></script>
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

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('assets/firebase-messaging-sw.js')
            .then(reg => console.log('SW registered', reg))
            .catch(err => console.error('SW error', err));
    }
</script>
</body>
</html>