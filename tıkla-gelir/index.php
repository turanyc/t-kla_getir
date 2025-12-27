<?php
session_start();
require_once "config/database.php";

$user_name   = $_SESSION['name'] ?? '';
$user_id     = $_SESSION['user_id'] ?? null;
$isLoggedIn  = isset($_SESSION['user_id']);

/* ---------- FİLTRE / ARAMA ---------- */
$category_id    = isset($_GET['category'])   ? (int)$_GET['category']   : 0;
$vendor_type_id = isset($_GET['vendor_type'])? (int)$_GET['vendor_type']: 0;
$status         = $_GET['status'] ?? 'all';
$min_rating     = isset($_GET['min_rating']) ? (float)$_GET['min_rating'] : 0;
$search         = isset($_GET['search']) ? trim($_GET['search']) : '';

/* ---------- TÜM İŞLETMELER (FİLTRELİ) ---------- */
$sql = "SELECT b.*, vt.name AS vt_name, vt.slug,
               AVG(r.rating) AS avg_rating, COUNT(r.id) AS review_count
        FROM businesses b
        JOIN vendor_types vt ON b.vendor_type_id = vt.id
        LEFT JOIN reviews r ON r.business_id = b.id
        WHERE b.is_approved = 1";

if ($vendor_type_id) $sql .= " AND b.vendor_type_id = $vendor_type_id";
if ($status === 'open')   $sql .= " AND b.is_open = 1";
if ($status === 'closed') $sql .= " AND b.is_open = 0";
if ($search) $sql .= " AND (b.name LIKE ? OR b.description LIKE ?)";
if ($min_rating > 0) $sql .= " HAVING avg_rating >= $min_rating";

$sql .= " GROUP BY b.id ORDER BY b.name";

$stmt = $pdo->prepare($sql);
if ($search) {
    $like = "%$search%";
    $stmt->execute([$like, $like]);
} else {
    $stmt->execute();
}
$businesses = $stmt->fetchAll();

/* ---------- EN POPÜLER ---------- */
$popular = $pdo->query("
    SELECT b.*, vt.name AS vt_name, AVG(r.rating) AS avg_rating, COUNT(r.id) AS review_count
    FROM businesses b
    JOIN vendor_types vt ON b.vendor_type_id = vt.id
    LEFT JOIN reviews r ON b.id = r.business_id
    WHERE b.is_approved = 1 AND b.is_open = 1
    GROUP BY b.id
    HAVING avg_rating >= 4 AND review_count >= 3
    ORDER BY avg_rating DESC LIMIT 6
")->fetchAll();

/* ---------- EN ÇOK TERCİH EDİLEN ---------- */
$most_ordered = $pdo->query("
    SELECT b.*, vt.name AS vt_name, COUNT(o.id) AS order_count
    FROM businesses b
    JOIN vendor_types vt ON b.vendor_type_id = vt.id
    LEFT JOIN orders o ON o.business_id = b.id AND o.status = 'teslim'
    WHERE b.is_approved = 1 AND b.is_open = 1
    GROUP BY b.id
    ORDER BY order_count DESC LIMIT 6
")->fetchAll();

/* ---------- EN ÇOK SATAN MENÜLER ---------- */
$top_menus = $pdo->query("
    SELECT mi.name, mi.price, b.name AS business_name, SUM(oi.quantity) AS sold
    FROM order_items oi
    JOIN orders o ON o.id = oi.order_id
    JOIN menu_items mi ON mi.id = oi.menu_item_id
    JOIN businesses b ON b.id = mi.business_id
    WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND o.status = 'teslim'
    GROUP BY mi.id
    ORDER BY sold DESC LIMIT 8
")->fetchAll();

/* ---------- VENDOR TYPES ---------- */
$vendors = $pdo->query("SELECT id, name, slug FROM vendor_types WHERE is_approved = 1 ORDER BY name")->fetchAll();
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tıkla Gelir</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #FF4500;
            --success: #00C853;
            --danger: #F44336;
            --warning: #FF9100;
            --info: #2196F3;
            --light: #F8F9FA;
            --dark: #333333;
            --card-bg: #FFFFFF;
            --border-color: #E0E0E0;
            --shadow: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-hover: 0 8px 25px rgba(0,0,0,0.15);
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #F5F7FA; color: var(--dark); min-height: 100vh; }

        /* HEADER */
        .main-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 15px 20px; 
            background: var(--card-bg); 
            box-shadow: var(--shadow); 
            position: sticky; 
            top: 0; 
            z-index: 1000; 
            flex-wrap: wrap;
            overflow-x: hidden;
        }
        .logo-section { 
            display: flex; 
            align-items: center; 
            gap: 15px;
            flex: 1;
            min-width: 0;
        }
        .logo-section h1 { 
            font-size: clamp(20px, 5vw, 32px); 
            color: var(--primary); 
            font-weight: 700; 
            margin: 0;
            white-space: nowrap;
        }
        .logo-section p { 
            font-size: clamp(12px, 3vw, 14px); 
            margin: 0;
            white-space: nowrap;
        }
        .hamburger-menu { 
            display: none; 
            background: var(--gradient); 
            color: white; 
            border: none; 
            padding: 10px 15px; 
            border-radius: 10px; 
            font-size: 24px; 
            cursor: pointer; 
            z-index: 1001;
        }
        .header-actions { 
            display: flex; 
            align-items: center; 
            gap: 8px;
            flex-wrap: wrap;
        }
        .header-actions a, .header-actions button { 
            background: var(--gradient); 
            color: white; 
            padding: 8px 16px; 
            border: none; 
            border-radius: 50px; 
            font-weight: 600; 
            text-decoration: none; 
            cursor: pointer; 
            transition: all .3s; 
            box-shadow: var(--shadow); 
            display: inline-flex; 
            align-items: center; 
            gap: 6px; 
            font-size: clamp(12px, 2.5vw, 14px);
            white-space: nowrap;
        }
        .header-actions a:hover, .header-actions button:hover { 
            transform: translateY(-2px); 
            box-shadow: var(--shadow-hover); 
        }
        .cart-count { 
            margin-left: 4px; 
            background: var(--warning); 
            color: #333; 
            border-radius: 50%; 
            width: 20px; 
            height: 20px; 
            font-size: 11px; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
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
            background: var(--card-bg); 
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
        .mobile-menu a, .mobile-menu button { 
            display: block !important; 
            width: 100%; 
            padding: 15px 20px; 
            background: var(--gradient) !important; 
            color: white !important; 
            border: none; 
            border-radius: 0; 
            text-align: left; 
            text-decoration: none; 
            font-weight: 600; 
            margin-bottom: 5px; 
            cursor: pointer;
            font-size: 14px;
        }
        .mobile-menu a:hover, .mobile-menu button:hover {
            opacity: 0.9;
        }
        .mobile-menu a i, .mobile-menu button i {
            margin-right: 10px;
            width: 20px;
            display: inline-block;
        }
        .mobile-menu button {
            background: var(--gradient) !important;
            color: white !important;
        }

        /* LAYOUT */
        .layout { 
            display: flex; 
            gap: 20px; 
            margin: 20px; 
            max-width: 100%;
            overflow-x: hidden;
        }
        .filter-sidebar { 
            width: 260px; 
            min-width: 260px;
            background: var(--card-bg); 
            border-radius: 20px; 
            padding: 20px; 
            box-shadow: var(--shadow); 
            align-self: flex-start; 
        }
        .filter-sidebar h3 { 
            margin-bottom: 15px; 
            color: var(--primary-dark); 
            font-size: clamp(16px, 3vw, 20px);
        }
        .filter-sidebar label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: 500; 
            font-size: 14px;
        }
        .filter-sidebar select, .filter-sidebar input { 
            width: 100%; 
            padding: 10px 12px; 
            border: 1px solid var(--border-color); 
            border-radius: 10px; 
            margin-bottom: 15px; 
            font-size: 14px;
            box-sizing: border-box;
        }
        .filter-sidebar button { 
            width: 100%; 
            padding: 12px; 
            background: var(--gradient); 
            color: white; 
            border: none; 
            border-radius: 50px; 
            font-weight: 600; 
            cursor: pointer; 
            font-size: 14px;
        }

        .content { 
            flex: 1; 
            min-width: 0;
            overflow-x: hidden;
        }
        .category-buttons { 
            display: flex; 
            justify-content: center; 
            gap: 10px; 
            margin-bottom: 30px; 
            flex-wrap: wrap; 
        }
        .cat-btn { 
            background: var(--gradient); 
            color: white; 
            padding: 12px 20px; 
            border-radius: 50px; 
            font-size: clamp(12px, 2.5vw, 16px); 
            font-weight: 600; 
            text-decoration: none; 
            transition: all .3s; 
            box-shadow: var(--shadow); 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            white-space: nowrap;
        }

        .cards-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 20px; 
        }
        .card-item { 
            background: var(--card-bg); 
            border-radius: 20px; 
            overflow: hidden; 
            box-shadow: var(--shadow); 
            transition: all .4s; 
            min-width: 0;
        }
        .card-item:hover { 
            transform: translateY(-5px); 
            box-shadow: var(--shadow-hover); 
        }
        .card-img { 
            height: 160px; 
            background: var(--gradient); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: clamp(40px, 8vw, 60px); 
            color: rgba(255,255,255,.8); 
        }
        .card-content { 
            padding: 20px; 
            text-align: center; 
        }
        .card-title { 
            font-size: clamp(16px, 3vw, 20px); 
            font-weight: 700; 
            margin-bottom: 8px; 
            word-wrap: break-word;
        }
        .card-sub { 
            color: #666; 
            font-size: clamp(12px, 2.5vw, 14px); 
            margin-bottom: 15px; 
        }
        .card-rating { 
            color: var(--warning); 
            font-size: clamp(14px, 3vw, 16px); 
            margin-bottom: 15px; 
        }
        .card-btn { 
            background: var(--gradient); 
            color: white; 
            padding: 10px 20px; 
            border-radius: 50px; 
            text-decoration: none; 
            font-weight: 600; 
            font-size: clamp(12px, 2.5vw, 14px);
            display: inline-block;
        }

        .mini-menu-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); 
            gap: 15px; 
            margin-top: 30px; 
        }
        .mini-menu-card { 
            background: #FFF8F0; 
            border: 1px solid #FFE0B2; 
            border-radius: 15px; 
            padding: 15px; 
            text-align: center; 
            transition: all .3s; 
        }
        .mini-menu-name { 
            font-size: clamp(14px, 3vw, 16px); 
            font-weight: 600; 
            margin-bottom: 5px; 
        }
        .mini-menu-price { 
            color: var(--primary); 
            font-size: clamp(16px, 3vw, 18px); 
            font-weight: 700; 
        }
        .section-title { 
            text-align: center; 
            font-size: clamp(24px, 5vw, 32px); 
            margin: 40px 0 25px; 
            font-weight: 700; 
        }
        .section-title i { 
            color: var(--primary); 
        }

        /* SEPET SİDEBAR */
        .cart-sidebar { position: fixed; top: 0; right: -450px; width: 420px; height: 100%; background: white; box-shadow: -5px 0 20px rgba(0,0,0,0.15); z-index: 9999; transition: right 0.4s ease; display: flex; flex-direction: column; }
        .cart-sidebar.open { right: 0; }
        .cart-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9998; opacity: 0; visibility: hidden; transition: all 0.4s; }
        .cart-overlay.active { opacity: 1; visibility: visible; }
        .cart-header { padding: 20px; background: var(--primary); color: white; display: flex; justify-content: space-between; align-items: center; }
        .close-cart { background: none; border: none; font-size: 30px; color: white; cursor: pointer; }
        .cart-items { flex: 1; padding: 20px; overflow-y: auto; }
        .cart-items .empty-cart { text-align: center; color: #999; margin-top: 50px; font-size: 18px; }
        .cart-item { display: flex; gap: 15px; padding: 15px 0; border-bottom: 1px solid #eee; }
        .item-info h4 { font-size: 15px; margin: 0 0 5px; }
        .item-info .price { color: var(--primary); font-weight: 600; }
        .quantity-controls { display: flex; align-items: center; gap: 10px; margin-top: 8px; }
        .quantity-controls button { width: 30px; height: 30px; border: none; background: var(--primary); color: white; border-radius: 50%; cursor: pointer; }
        .cart-footer { padding: 20px; border-top: 1px solid #eee; background: #f9f9f9; }
        .cart-total { font-size: 20px; margin-bottom: 15px; text-align: center; }
        .checkout-btn { width: 100%; padding: 15px; background: var(--gradient); color: white; border: none; border-radius: 50px; font-size: 18px; font-weight: 600; cursor: pointer; }

        .main-footer { background: var(--primary); color: white; padding: 40px 20px 20px; text-align: center; margin-top: 80px; }
        .support-button { position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; background: var(--gradient); color: white; border: none; font-size: 24px; cursor: pointer; box-shadow: var(--shadow-hover); z-index: 9999; }
        .support-dropdown { position: fixed; bottom: 100px; right: 30px; background: white; border-radius: 15px; box-shadow: var(--shadow-hover); padding: 15px; display: none; flex-direction: column; gap: 10px; z-index: 9998; min-width: 220px; }
        .support-dropdown.show { display: flex; }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .filter-sidebar { width: 100%; min-width: auto; }
            .layout { flex-direction: column; margin: 15px; }
            .cards-grid { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        }
        
        @media (max-width: 768px) {
            .main-header { padding: 12px 15px; }
            .hamburger-menu { display: block; }
            .header-actions { display: none; }
            .logo-section h1 { font-size: 22px; }
            .logo-section p { font-size: 12px; }
            .layout { margin: 10px; gap: 15px; }
            .filter-sidebar { padding: 15px; }
            .cards-grid { grid-template-columns: 1fr; }
            .category-buttons { gap: 8px; }
            .cat-btn { padding: 10px 16px; font-size: 13px; }
            .section-title { font-size: 22px; margin: 30px 0 20px; }
            .cart-sidebar { width: 100%; right: -100%; }
            .support-button { width: 50px; height: 50px; font-size: 20px; bottom: 20px; right: 20px; }
            .support-dropdown { bottom: 80px; right: 20px; min-width: 200px; }
            .main-footer { padding: 30px 15px 15px; margin-top: 40px; }
        }
        
        @media (max-width: 480px) {
            .logo-section { flex-direction: column; align-items: flex-start; gap: 5px; }
            .logo-section h1 { font-size: 18px; }
            .cards-grid { grid-template-columns: 1fr; gap: 12px; }
            .card-img { height: 140px; }
            .card-content { padding: 15px; }
            .category-buttons { flex-direction: column; }
            .cat-btn { width: 100%; justify-content: center; }
        }
        
        /* OVERFLOW PREVENTION */
        body { overflow-x: hidden; }
        * { max-width: 100%; }
        img { max-width: 100%; height: auto; }
    </style>
</head>
<body>

<!-- HEADER -->
<header class="main-header">
    <button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMobileMenu()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="logo-section">
        <a href="index.php" style="text-decoration:none;color:inherit"><h1>Tıkla Gelir</h1></a>
        <?php if($isLoggedIn): ?>
            <p>Merhaba, <strong><?= htmlspecialchars($user_name) ?></strong>!</p>
        <?php else: ?>
            <p>Hoş geldin, <strong>Ziyaretçi</strong>!</p>
        <?php endif; ?>
    </div>
    <div class="header-actions">
        <?php if($isLoggedIn): ?>
            <a href="profile.php">Profil</a>
            <?php if($_SESSION['role'] === 'business'): ?>
                <a href="business/index.php">İşletme Paneli</a>
            <?php endif; ?>
            <button id="cartButton">Sepet <span class="cart-count" id="cartCount">0</span></button>
            <button onclick="if(confirm('Çıkış yapılsın mı?')) window.location='logout.php'">Çıkış</button>
        <?php else: ?>
            <a href="login.php">Giriş Yap</a>
        <?php endif; ?>
    </div>
</header>

<!-- MOBILE MENU -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
<nav class="mobile-menu" id="mobileMenu">
    <a href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
    <?php if($isLoggedIn): ?>
        <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
        <?php if($_SESSION['role'] === 'business'): ?>
            <a href="business/index.php"><i class="fas fa-store"></i> İşletme Paneli</a>
        <?php endif; ?>
        <button id="mobileCartButton"><i class="fas fa-shopping-cart"></i> Sepet <span class="cart-count" id="mobileCartCount">0</span></button>
        <button onclick="if(confirm('Çıkış yapılsın mı?')) window.location='logout.php'"><i class="fas fa-sign-out-alt"></i> Çıkış</button>
    <?php else: ?>
        <a href="login.php"><i class="fas fa-sign-in-alt"></i> Giriş Yap</a>
    <?php endif; ?>
</nav>

<div class="layout">
    <!-- SOL FİLTRE -->
    <aside class="filter-sidebar">
        <h3>Filtrele</h3>
        <form method="get">
            <label>Kategori</label>
            <select name="category">
                <option value="0">Tümü</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $category_id == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>İşletme Türü</label>
            <select name="vendor_type">
                <option value="0">Tümü</option>
                <?php foreach($vendors as $v): ?>
                    <option value="<?= $v['id'] ?>" <?= $vendor_type_id == $v['id'] ? 'selected' : '' ?>><?= htmlspecialchars($v['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Durum</label>
            <select name="status">
                <option value="all" <?= $status==='all'?'selected':'' ?>>Tümü</option>
                <option value="open" <?= $status==='open'?'selected':'' ?>>Açık</option>
                <option value="closed" <?= $status==='closed'?'selected':'' ?>>Kapalı</option>
            </select>

            <label>Min. Puan</label>
            <input type="number" step="0.1" min="0" max="5" name="min_rating" value="<?= $min_rating ?>">

            <label>Arama</label>
            <input type="text" name="search" placeholder="Restoran, ürün..." value="<?= htmlspecialchars($search) ?>">

            <button type="submit">Filtrele</button>
        </form>
    </aside>

    <!-- İÇERİK -->
    <main class="content">
        <!-- KATEGORİ BUTONLARI -->
        <section class="category-buttons">
            <?php foreach ($vendors as $vt):
                $icon = $vt['slug']==='restaurant' ? 'fa-utensils' : ($vt['slug']==='market' ? 'fa-shopping-basket' : ($vt['slug']==='grocery' ? 'fa-carrot' : 'fa-seedling'));
                $link = $vt['slug']==='restaurant' ? 'restaurant/index.php' : ($vt['slug']==='market' ? 'market/index.php' : ($vt['slug']==='grocery' ? 'grocery/index.php' : 'dried_goods/index.php'));
            ?>
                <a href="<?= $link ?>" class="cat-btn"><i class="fas <?= $icon ?>"></i> <?= htmlspecialchars($vt['name']) ?></a>
            <?php endforeach; ?>
        </section>

        <!-- TÜM İŞLETMELER (FİLTRELİ) -->
        <?php if($businesses): ?>
        <section>
            <h2 class="section-title">Tüm İşletmeler (<?= count($businesses) ?>)</h2>
            <div class="cards-grid">
                <?php foreach($businesses as $b): ?>
                <div class="card-item">
                    <div class="card-img"><i class="fas fa-store"></i></div>
                    <div class="card-content">
                        <div class="card-title"><?= htmlspecialchars($b['name']) ?></div>
                        <div class="card-sub"><?= htmlspecialchars($b['vt_name']) ?></div>
                        <div class="card-rating"> <?= number_format($b['avg_rating']??0, 1) ?>/5</div>
                        <a href="restaurant/index.php?rid=<?= $b['id'] ?>" class="card-btn">Menüyü Gör</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- EN POPÜLER, EN ÇOK TERCİH EDİLEN vs. aynı kalıyor... -->
        <?php if ($popular): ?>
        <section>
            <h2 class="section-title">En Popüler İşletmeler</h2>
            <div class="cards-grid">
                <?php foreach ($popular as $p): ?>
                <div class="card-item">
                    <div class="card-img"><i class="fas fa-crown"></i></div>
                    <div class="card-content">
                        <div class="card-title"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="card-sub"><?= htmlspecialchars($p['vt_name']) ?></div>
                        <div class="card-rating"><?= number_format($p['avg_rating'],1) ?>/5 (<?= $p['review_count'] ?>)</div>
                        <a href="restaurant/index.php?rid=<?= $p['id'] ?>" class="card-btn">Hemen Sipariş Ver</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- diğer bölümler aynı -->
    </main>
</div>

<!-- SEPET SİDEBAR -->
<div id="cartSidebar" class="cart-sidebar">
    <div class="cart-header">
        <h3>Sepetim</h3>
        <button class="close-cart" onclick="closeCart()">×</button>
    </div>
    <div class="cart-items" id="cartItems">
        <p class="empty-cart">Sepetiniz boş</p>
    </div>
    <div class="cart-footer">
        <div class="cart-total"><strong>Toplam: <span id="cartTotal">0.00</span> ₺</strong></div>
        <button class="checkout-btn" onclick="window.location='checkout.php'">Siparişi Tamamla</button>
    </div>
</div>
<div class="cart-overlay" onclick="closeCart()"></div>

<footer class="main-footer">
    <div class="footer-links">
        <a href="kvkk.php">KVKK</a> <a href="uyelik-sozlesmesi.php">Sözleşme</a> <a href="iletisim.php">İletişim</a> <a href="sss.php">SSS</a>
    </div>
    <div class="footer-copy">© 2025 Tıkla Gelir | Tüm Hakları Saklıdır</div>
</footer>

<button class="support-button" onclick="document.getElementById('supportDropdown').classList.toggle('show')">
    Destek
</button>
<div class="support-dropdown" id="supportDropdown">
    <a href="tel:05441392254">0544 139 22 54</a>
    <a href="mailto:destek@tiklagelir.com.tr">destek@tiklagelir.com.tr</a>
</div>

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

// Mobile cart button
document.getElementById('mobileCartButton')?.addEventListener('click', function() {
    closeMobileMenu();
    document.getElementById('cartButton').click();
});

// YENİ SİSTEM – SENİN APİ'NE UYUMLU (api/ klasörü)
async function loadCart() {
    try {
        const res = await fetch('api/get_cart.php');
        const data = await res.json();

        const itemsDiv = document.getElementById('cartItems');
        const totalSpan = document.getElementById('cartTotal');
        const countSpan = document.getElementById('cartCount');

        if (!data.success || data.items.length === 0) {
            itemsDiv.innerHTML = '<p class="empty-cart">Sepetiniz boş</p>';
            totalSpan.textContent = '0.00';
            countSpan.textContent = '0';
            return;
        }

        let html = '';
        let total = 0;
        let qtyTotal = 0;

        data.items.forEach(item => {
            total += item.price * item.quantity;
            qtyTotal += item.quantity;

            html += `
                <div class="cart-item">
                    <div class="item-info">
                        <h4>${item.name}</h4>
                        <div class="price">${item.price} ₺ x ${item.quantity}</div>
                    </div>
                    <div class="quantity-controls">
                        <button onclick="updateQuantity(${item.menu_item_id}, ${item.quantity-1})">-</button>
                        <span>${item.quantity}</span>
                        <button onclick="updateQuantity(${item.menu_item_id}, ${item.quantity+1})">+</button>
                    </div>
                    <button class="delete-btn" onclick="removeFromCart(${item.menu_item_id})">×</button>
                </div>`;
        });

        itemsDiv.innerHTML = html;
        totalSpan.textContent = total.toFixed(2);
        countSpan.textContent = qtyTotal;
        document.getElementById('mobileCartCount').textContent = qtyTotal;

    } catch (err) {
        console.error('Sepet yüklenemedi:', err);
        document.getElementById('cartItems').innerHTML = '<p class="empty-cart">Sepet yüklenemedi</p>';
    }
}

async function updateQuantity(menu_item_id, qty) {
    if (qty < 1) return removeFromCart(menu_item_id);
    await fetch('api/update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `menu_item_id=${menu_item_id}&quantity=${qty}`
    });
    loadCart();
}

async function removeFromCart(menu_item_id) {
    if (!confirm('Ürünü sepetten kaldırmak istiyor musunuz?')) return;
    await fetch('api/update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `menu_item_id=${menu_item_id}&quantity=0`
    });
    loadCart();
}

// Sayfa yüklendiğinde sepeti göster
document.addEventListener('DOMContentLoaded', loadCart);

// Sepet butonuna tıklandığında aç
document.getElementById('cartButton')?.addEventListener('click', () => {
    document.getElementById('cartSidebar').classList.add('open');
    document.querySelector('.cart-overlay').classList.add('active');
    loadCart();

// SEPET KAPATMA FONKSİYONU (EKSİK OLAN)
function closeCart() {
    document.getElementById('cartSidebar').classList.remove('open');
    document.querySelector('.cart-overlay')?.classList.remove('active');
}

// ESC tuşu ile kapatma (bonus)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeCart();
});

// Overlay'e tıklayınca kapatma
document.querySelector('.cart-overlay')?.addEventListener('click', closeCart);

});
</script>
</body>
</html>