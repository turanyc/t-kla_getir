<?php
session_start();
require_once "config/database.php";
require_once "functions.php";

// Giriş kontrolü
requireCustomerLogin();

$user_id = getUserId();

// Sayfalama
$page  = isset($_GET['page']) ? safeInt($_GET['page'], 1) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Toplam sipariş sayısı
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE customer_id = ?");
$count_stmt->execute([$user_id]);
$total_orders = $count_stmt->fetchColumn();
$total_pages  = ceil($total_orders / $limit);

// Siparişleri çek
$stmt = $pdo->prepare("SELECT o.*, r.name as restaurant_name,
                      COUNT(oi.id) as item_count
                      FROM orders o
                      JOIN restaurants r ON o.restaurant_id = r.id
                      LEFT JOIN order_items oi ON o.id = oi.order_id
                      WHERE o.customer_id = ?
                      GROUP BY o.id
                      ORDER BY o.created_at DESC
                      LIMIT ? OFFSET ?");
$stmt->bindParam(1, $user_id, PDO::PARAM_INT);
$stmt->bindParam(2, $limit, PDO::PARAM_INT);
$stmt->bindParam(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Geçmişi - Tıkla Gelir</title>
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
            font-family: 'Poppins', sans-serif;
            background: #F5F7FA;
            color: var(--dark);
            padding: 15px;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        
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

        /* Header – Tıkla Gelsin */
        .header-card {
            background: var(--card-bg);
            border-radius: 25px;
            padding: clamp(20px, 4vw, 30px);
            margin-bottom: 20px;
            border: 1px solid #E0E0E0;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            text-align: center;
        }
        .header-card h1 { 
            color: var(--primary); 
            margin-bottom: 10px; 
            font-size: clamp(20px, 4vw, 28px);
        }
        .header-card p { 
            color: #666; 
            font-size: clamp(14px, 2.5vw, 16px);
        }

        /* Sipariş kartı */
        .order-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: clamp(20px, 4vw, 25px);
            margin-bottom: 15px;
            border: 1px solid #E0E0E0;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .order-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }

        /* Durum rozeti */
        .status-badge {
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-yeni { background: #9C27B0; color: #fff; }
        .status-hazirlaniyor { background: #FFC107; color: #333; }
        .status-yolda { background: #2196F3; color: #fff; }
        .status-teslim { background: #4CAF50; color: #fff; }
        .status-iptal { background: #F44336; color: #fff; }

        /* Sayfalama */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }
        .pagination a {
            background: var(--card-bg);
            padding: 10px 15px;
            border-radius: 10px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            border: 1px solid #E0E0E0;
            transition: 0.3s;
        }
        .pagination a:hover,
        .pagination a.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border-color: var(--primary);
        }

        /* Boş durum */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .empty-state i {
            font-size: 80px;
            color: var(--primary);
            opacity: 0.3;
        }
        .empty-state h3 { margin: 20px 0 10px; color: var(--dark); }
        
        @media (max-width: 768px) {
            body { padding: 10px; }
            .hamburger-menu { display: block; }
            .container { padding: 0; }
            .header-card { padding: 20px; margin-bottom: 15px; }
            .order-card { padding: 20px; }
            .order-card h4 { font-size: clamp(16px, 3vw, 20px); }
            .status-badge { 
                padding: 5px 12px; 
                font-size: 12px; 
            }
            .pagination { 
                flex-wrap: wrap; 
                gap: 5px; 
            }
            .pagination a { 
                padding: 8px 12px; 
                font-size: 14px; 
            }
        }
        
        @media (max-width: 480px) {
            .header-card { padding: 15px; }
            .order-card { padding: 15px; }
            .status-badge { 
                padding: 4px 10px; 
                font-size: 11px; 
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
    
    <!-- Header – Tıkla Gelsin -->
    <div class="header-card">
        <h1><i class="fas fa-history"></i> Sipariş Geçmişim</h1>
        <p>Toplam <strong><?= $total_orders ?></strong> sipariş bulundu.</p>
    </div>

    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 style="color: var(--primary);">
                        <i class="fas fa-utensils"></i> <?= htmlspecialchars($order['restaurant_name']) ?>
                    </h4>
                    <span class="status-badge status-<?= $order['status'] ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Sipariş #<?= $order['id'] ?></strong><br>
                        <small class="text-muted">
                            <i class="fas fa-calendar"></i> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <strong><?= number_format($order['total_price'], 2) ?> ₺</strong><br>
                        <small class="text-muted"><?= $order['item_count'] ?> ürün</small>
                    </div>
                </div>

                <div class="text-end">
                    <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-primary-custom btn-sm">
                        <i class="fas fa-eye"></i> Detaylar
                    </a>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Sayfalama -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>">&laquo; Önceki</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>">Sonraki &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-shopping-basket"></i>
            <h3>Henüz sipariş vermemişsiniz.</h3>
            <p>Hemen restoranlara göz atın!</p>
            <a href="index.php" class="btn btn-primary-custom">
                <i class="fas fa-utensils"></i> Restoranları Keşfet
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- MOBILE MENU -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
<nav class="mobile-menu" id="mobileMenu">
    <a href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
    <a href="order_history.php"><i class="fas fa-history"></i> Siparişlerim</a>
    <button onclick="if(confirm('Çıkış yapılsın mı?')) window.location='logout.php'"><i class="fas fa-sign-out-alt"></i> Çıkış</button>
</nav>

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