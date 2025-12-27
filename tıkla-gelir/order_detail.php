<?php
session_start();
require_once "config/database.php";
require_once "functions.php";

// Giriş ve yetki
if (!hasRole('customer')) {
    header("Location: login.php?redirect=order_detail.php?id=" . ($_GET['id'] ?? ''));
    exit;
}

$order_id = safeInt($_GET['id'] ?? 0);
$user_id  = getUserId();

if ($order_id <= 0) die("Geçersiz sipariş ID!");

try {
    // Sipariş bilgileri (kullanıcıya ait)
    $stmt = $pdo->prepare("SELECT o.*, r.name as restaurant_name, u.name as customer_name
                          FROM orders o
                          JOIN restaurants r ON o.restaurant_id = r.id
                          JOIN users u ON o.customer_id = u.id
                          WHERE o.id = ? AND o.customer_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) die("Bu siparişi görüntüleme yetkiniz yok veya sipariş bulunamadı!");

    // Sipariş kalemleri
    $items_stmt = $pdo->prepare("SELECT oi.*, m.name as item_name
                                FROM order_items oi
                                JOIN menu_items m ON oi.menu_item_id = m.id
                                WHERE oi.order_id = ?");
    $items_stmt->execute([$order_id]);
    $order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Sistem Hatası: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Detayı #<?= $order_id ?> - Tıkla Gelsin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
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
            font-family: 'Poppins', sans-serif;
            background: #F5F7FA;
            color: var(--dark);
            padding: 15px;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .container { 
            max-width: 900px; 
            margin: 0 auto; 
            padding: 15px; 
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
        .mobile-menu a, .mobile-menu button { 
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
            cursor: pointer;
        }

        /* HEADER – Tıkla Gelsin */
        .top-bar {
            background: var(--primary);
            color: #fff;
            padding: 15px 20px;
            font-size: 20px;
            font-weight: 700;
            border-radius: 15px 15px 0 0;
        }

        /* Kartlar */
        .card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: clamp(20px, 4vw, 30px);
            margin-bottom: 20px;
            border: 1px solid #E0E0E0;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .card h4 {
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Ürün satırları */
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .item-row:last-child { border-bottom: none; }

        /* Toplam */
        .total-row {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            padding: 20px;
            border-radius: 15px;
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
        }

        /* Butonlar */
        .btn-home {
            background: var(--secondary);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            transition: 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-home:hover {
            background: #009624;
        }

        /* Durum rozeti */
        .status-badge {
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-yeni { background: #9C27B0; color: #fff; }
        .status-hazirlaniyor { background: #FFC107; color: #333; }
        .status-yolda { background: #2196F3; color: #fff; }
        .status-teslim { background: #4CAF50; color: #fff; }
        .status-iptal { background: #F44336; color: #fff; }
        
        @media (max-width: 768px) {
            body { padding: 10px; }
            .hamburger-menu { display: block; }
            .container { padding: 10px; }
            .card { padding: 20px; }
            .card h4 { font-size: clamp(16px, 3vw, 20px); }
            .item-row { 
                flex-direction: column; 
                align-items: flex-start; 
                gap: 10px; 
            }
            .total-row { 
                padding: 15px; 
                font-size: clamp(16px, 3vw, 20px); 
            }
            .btn-home { 
                padding: 10px 20px; 
                font-size: clamp(12px, 2.5vw, 14px); 
            }
        }
        
        @media (max-width: 480px) {
            .card { padding: 15px; }
            .status-badge { 
                padding: 6px 15px; 
                font-size: 12px; 
            }
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
    
    <!-- Geri Dön -->
    <a href="order_history.php" class="btn-home mb-4">
        <i class="fas fa-arrow-left"></i> Siparişlerime Dön
    </a>

<!-- MOBILE MENU -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
<nav class="mobile-menu" id="mobileMenu">
    <a href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
    <a href="order_history.php"><i class="fas fa-history"></i> Siparişlerim</a>
    <button onclick="if(confirm('Çıkış yapılsın mı?')) window.location='logout.php'"><i class="fas fa-sign-out-alt"></i> Çıkış</button>
</nav>

    <!-- Üst Başlık – Tıkla Gelsin -->
    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 style="color: var(--primary);">
                    <i class="fas fa-receipt"></i> Sipariş #<?= $order_id ?>
                </h2>
                <p class="text-muted mb-0">
                    <i class="fas fa-calendar"></i> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                </p>
            </div>
            <span class="status-badge status-<?= $order['status'] ?>">
                <?= ucfirst($order['status']) ?>
            </span>
        </div>

        <div class="row">
            <div class="col-md-6">
                <p><strong>Restoran:</strong><br>
                    <i class="fas fa-utensils"></i> <?= htmlspecialchars($order['restaurant_name']) ?>
                </p>
            </div>
            <div class="col-md-6">
                <p><strong>Ödeme Yöntemi:</strong><br>
                    <i class="fas fa-credit-card"></i>
                    <?= htmlspecialchars($order['payment_method'] ?: 'Kapıda Nakit') ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Takip Butonu -->
    <?php if ($order['status'] === 'yolda'): ?>
    <div class="text-center mb-4">
        <a href="order_track.php?id=<?= $order_id ?>" class="btn-home">
            <i class="fas fa-map-marker-alt"></i> Canlı Takip Et
        </a>
    </div>
    <?php endif; ?>

    <!-- Sipariş İçeriği -->
    <div class="card">
        <h4><i class="fas fa-shopping-cart"></i> Sipariş İçeriği</h4>
        <?php foreach ($order_items as $item): ?>
        <div class="item-row">
            <div>
                <strong><?= htmlspecialchars($item['item_name']) ?></strong><br>
                <small class="text-muted">Adet: <?= $item['quantity'] ?></small>
            </div>
            <div class="text-end">
                <strong><?= number_format($item['price'] * $item['quantity'], 2) ?> ₺</strong><br>
                <small class="text-muted">Birim: <?= number_format($item['price'], 2) ?> ₺</small>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="total-row">
            <div class="d-flex justify-content-between align-items-center">
                <span><i class="fas fa-coins"></i> Toplam Tutar:</span>
                <span style="font-size: 24px;"><?= number_format($order['total_price'], 2) ?> ₺</span>
            </div>
        </div>
    </div>

    <!-- Not -->
    <?php if ($order['note']): ?>
    <div class="card">
        <h4><i class="fas fa-sticky-note"></i> Sipariş Notu</h4>
        <p class="mb-0"><?= nl2br(htmlspecialchars($order['note'])) ?></p>
    </div>
    <?php endif; ?>

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