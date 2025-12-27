<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "../config/database.php";

$user_name   = $_SESSION['name'] ?? '';
$user_id     = $_SESSION['user_id'] ?? null;
$isLoggedIn  = isset($_SESSION['user_id']);

// İşletme bilgisi
$stmt = $pdo->prepare("
    SELECT b.*, vt.name as vendor_type_name
    FROM businesses b
    JOIN vendor_types vt ON vt.id = b.vendor_type_id
    WHERE vt.slug = 'dried-goods' AND b.is_approved = 1
    LIMIT 1
");
$stmt->execute();
$dried_goods = $stmt->fetch();
if (!$dried_goods) {
    die("<div style='text-align:center;padding:50px;'><h2>Kuruyemiş mağazası şu anda hizmet dışı.</h2></div>");
}
$dried_goods_id = $dried_goods['id'];

// Filtreler
$search      = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$where = ["mi.business_id = ?"];
$params = [$dried_goods_id];
if ($search !== '') {
    $where[] = "(mi.name LIKE ? OR mi.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category_id) {
    $where[] = "mi.category_id = ?";
    $params[] = $category_id;
}
$where_sql = implode(" AND ", $where);

// Ürünler
$items = $pdo->prepare("
    SELECT mi.*, mc.name as category_name 
    FROM menu_items mi
    LEFT JOIN categories mc ON mi.category_id = mc.id
    WHERE $where_sql AND mi.is_available = 1
    ORDER BY mi.name
");
$items->execute($params);
$items = $items->fetchAll();

// Kuruyemiş kategorileri
$driedCategories = $pdo->prepare("
    SELECT DISTINCT mc.id, mc.name
    FROM categories mc
    JOIN menu_items mi ON mi.category_id = mc.id
    WHERE mi.business_id = ?
    ORDER BY mc.name
");
$driedCategories->execute([$dried_goods_id]);
$driedCategories = $driedCategories->fetchAll();

// Ana kategori butonları (index.php ile aynı)
$mainCats = $pdo->query("SELECT id, name, slug FROM vendor_types WHERE is_approved = 1 ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($dried_goods['name']) ?> - Tıkla Gelir</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        body { font-family: 'Poppins', sans-serif; background: var(--light); color: var(--dark); min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .main-header { display: flex; justify-content: space-between; align-items: center; padding: 25px 40px; margin: -20px -20px 30px -20px; background: var(--card-bg); border-radius: 0 0 25px 25px; box-shadow: var(--shadow); flex-wrap: wrap; gap: 20px; position: sticky; top: 0; z-index: 1000; }
        .logo-section h1 { font-size: 32px; color: var(--primary); font-weight: 700; }
        .logo-section a { text-decoration: none; color: inherit; }
        .header-actions { display: flex; gap: 15px; align-items: center; }
        .header-button, .login-button { background: var(--gradient); color: white; border: none; padding: 12px 24px; border-radius: 50px; cursor: pointer; font-size: 16px; font-weight: 600; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px; text-decoration: none; box-shadow: var(--shadow); position: relative; }
        .header-button:hover, .login-button:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }
        .login-button { background: linear-gradient(135deg, var(--success) 0%, #2E7D32 100%); }
        .cart-count { position: absolute; top: -8px; right: -8px; background: var(--warning); color: #333; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; }
        .welcome-section { text-align: center; padding: 60px 20px; background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%); border-radius: 25px; margin-bottom: 40px; border: 2px solid #FFCC80; }
        .welcome-section h2 { font-size: 36px; margin-bottom: 10px; color: var(--primary-dark); }
        .welcome-section p { font-size: 18px; color: var(--dark); font-weight: 500; }
        .search-bar-container { margin-bottom: 30px; }
        .search-bar { width: 100%; max-width: 600px; margin: 0 auto; display: flex; gap: 10px; }
        .search-bar input { flex: 1; padding: 15px 25px; border: 1px solid var(--border-color); border-radius: 50px; font-size: 16px; transition: all 0.3s; }
        .search-bar input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.2); }
        .search-bar button { padding: 15px 30px; background: var(--gradient); color: white; border: none; border-radius: 50px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .search-bar button:hover { transform: translateY(-2px); box-shadow: var(--shadow-hover); }

        /* ===== ANA KATEGORİ BUTONLARI (index.php ile aynı) ===== */
        .main-cat-bar { display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; margin-bottom: 25px; }
        .main-cat-btn {
            background: var(--gradient);
            color: white;
            padding: 14px 28px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .main-cat-btn:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }

        /* ===== KURUYEMİŞ ALT-KATEGORİ BUTONLARI ===== */
        .sub-cat-bar { display: flex; justify-content: center; flex-wrap: wrap; gap: 8px; margin-bottom: 30px; }
        .sub-cat-btn {
            background: var(--primary);
            color: white;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }
        .sub-cat-btn:hover,
        .sub-cat-btn.active { background: #FF4500; box-shadow: var(--shadow-hover); }

        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; margin-bottom: 50px; }
        .product-card { background: var(--card-bg); border-radius: 20px; overflow: hidden; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: var(--shadow); border: 1px solid var(--border-color); text-align: center; padding: 20px; }
        .product-card:hover { transform: translateY(-10px); box-shadow: var(--shadow-hover); }
        .product-img { height: 150px; background: var(--gradient); display: flex; align-items: center; justify-content: center; font-size: 50px; color: rgba(255,255,255,0.7); border-radius: 15px; margin-bottom: 15px; }
        .product-name { font-size: 18px; font-weight: 700; margin-bottom: 8px; color: var(--dark); }
        .product-price { font-size: 22px; font-weight: 700; color: var(--primary); margin-bottom: 15px; }
        .add-to-cart { padding: 12px 25px; background: var(--gradient); color: white; border: none; border-radius: 50px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: var(--shadow); }
        .add-to-cart:hover { transform: scale(1.05); box-shadow: var(--shadow-hover); }
        footer { text-align: center; padding: 30px; color: var(--primary); font-weight: 600; margin-top: 50px; background: #FFF3E0; border-top: 3px solid #FFE0B2; }
        .support-button { position: fixed; bottom: 30px; right: 30px; background: var(--gradient); color: white; border: none; border-radius: 50%; width: 60px; height: 60px; font-size: 24px; cursor: pointer; box-shadow: var(--shadow-hover); z-index: 9999; transition: all 0.3s ease; }
        .support-button:hover { transform: scale(1.1); }
        .support-dropdown { position: fixed; bottom: 100px; right: 30px; background: white; border-radius: 15px; box-shadow: var(--shadow-hover); padding: 15px; display: none; flex-direction: column; gap: 10px; z-index: 9998; min-width: 220px; }
        .support-dropdown a { display: flex; align-items: center; gap: 10px; padding: 10px 15px; border-radius: 10px; text-decoration: none; color: var(--dark); font-weight: 500; transition: background 0.3s; }
        .support-dropdown a:hover { background: #f0f0f0; }
        .support-dropdown.show { display: flex; }
        .navbar-toggler {
            display: none;
            background: var(--gradient);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 10px;
            font-size: 24px;
            cursor: pointer;
        }
        .logo-section h1 { font-size: clamp(24px, 5vw, 32px); }
        .logo-section p { font-size: clamp(12px, 2.5vw, 16px); margin: 0; }
        .header-button, .login-button { padding: clamp(10px, 2.5vw, 12px) clamp(16px, 4vw, 24px); font-size: clamp(14px, 3vw, 16px); white-space: nowrap; }
        .cart-count { width: clamp(20px, 4vw, 24px); height: clamp(20px, 4vw, 24px); font-size: clamp(10px, 2vw, 12px); }
        .welcome-section { padding: clamp(30px, 8vw, 60px) 20px; }
        .welcome-section h2 { font-size: clamp(24px, 5vw, 36px); }
        .welcome-section p { font-size: clamp(14px, 3vw, 18px); }
        .search-bar { flex-wrap: wrap; }
        .search-bar input { min-width: 150px; padding: clamp(12px, 3vw, 15px) clamp(15px, 4vw, 25px); font-size: clamp(14px, 3vw, 16px); }
        .search-bar button { padding: clamp(12px, 3vw, 15px) clamp(20px, 5vw, 30px); font-size: clamp(14px, 3vw, 16px); white-space: nowrap; }
        .main-cat-btn { padding: clamp(10px, 2.5vw, 14px) clamp(16px, 4vw, 28px); font-size: clamp(13px, 2.5vw, 15px); white-space: nowrap; }
        .sub-cat-btn { padding: clamp(6px, 1.5vw, 8px) clamp(12px, 3vw, 18px); font-size: clamp(12px, 2.5vw, 14px); white-space: nowrap; }
        .products-grid { grid-template-columns: repeat(auto-fill, minmax(clamp(140px, 25vw, 220px), 1fr)); gap: clamp(15px, 3vw, 25px); }
        .product-card { padding: clamp(15px, 3vw, 20px); }
        .product-img { height: clamp(100px, 20vw, 150px); font-size: clamp(30px, 6vw, 50px); }
        .product-name { font-size: clamp(14px, 3vw, 18px); word-break: break-word; }
        .product-price { font-size: clamp(18px, 4vw, 22px); }
        .add-to-cart { padding: clamp(10px, 2.5vw, 12px) clamp(18px, 4vw, 25px); font-size: clamp(12px, 2.5vw, 14px); width: 100%; }
        .support-button { bottom: clamp(20px, 5vw, 30px); right: clamp(20px, 5vw, 30px); width: clamp(50px, 10vw, 60px); height: clamp(50px, 10vw, 60px); font-size: clamp(20px, 4vw, 24px); }
        .support-dropdown { bottom: clamp(80px, 15vw, 100px); right: clamp(20px, 5vw, 30px); min-width: clamp(180px, 40vw, 220px); }
        .support-dropdown a { font-size: clamp(13px, 3vw, 15px); }
        @media (max-width: 768px) {
            .main-header { flex-direction: row; justify-content: space-between; padding: 15px 20px; }
            .navbar-toggler { display: block; }
            .header-actions { display: none; flex-direction: column; width: 100%; gap: 10px; padding-top: 15px; }
            .header-actions.active { display: flex; }
            .header-button, .login-button { width: 100%; justify-content: center; }
            .products-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px; }
            .logo-section h1 { font-size: clamp(20px, 5vw, 28px); }
            .welcome-section { padding: clamp(30px, 8vw, 50px) 15px; }
            .welcome-section h2 { font-size: clamp(22px, 5vw, 30px); }
            .main-cat-btn { padding: 10px 16px; font-size: 13px; }
            .search-bar { flex-direction: column; }
            .search-bar button { width: 100%; }
        }
        @media (max-width: 480px) {
            .main-header { padding: 12px 15px; }
            .products-grid { grid-template-columns: repeat(2, 1fr); }
            .logo-section h1 { font-size: 18px; }
            .logo-section p { font-size: 11px; }
            .welcome-section h2 { font-size: 20px; }
            .welcome-section p { font-size: 14px; }
        }
        * { max-width: 100%; }
    </style>
</head>
<body>

<!-- DESTEK BUTONU -->
<button class="support-button" onclick="toggleSupport()"><i class="fas fa-headset"></i></button>
<div class="support-dropdown" id="supportDropdown">
    <a href="tel:05441392254"><i class="fas fa-phone-alt" style="color: var(--success);"></i> 0544 139 22 54</a>
    <a href="mailto:destek@tiklagelir.com.tr"><i class="fas fa-envelope" style="color: var(--info);"></i> destek@tiklagelir.com.tr</a>
</div>

<div class="container">
    <!-- HEADER -->
    <header class="main-header">
        <div class="logo-section">
            <a href="../index.php" style="text-decoration: none; color: inherit;"><h1><i class="fas fa-seedling"></i> Tıkla Gelir</h1></a>
            <p><strong><?= htmlspecialchars($dried_goods['name']) ?></strong> - Online Kuruyemiş</p>
        </div>
        <button class="navbar-toggler" onclick="toggleMobileNav()" aria-label="Menü">
            <i class="fas fa-bars"></i>
        </button>
        <div class="header-actions" id="headerActions">
            <?php if($isLoggedIn): ?>
                <a href="../profile.php" class="header-button"><i class="fas fa-user"></i> Profil</a>
                <button class="header-button" id="cartButton"><i class="fas fa-shopping-cart"></i> Sepet <span class="cart-count" id="cartCount">0</span></button>
                <button class="header-button" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Çıkış</button>
            <?php else: ?>
                <a href="../login.php" class="login-button"><i class="fas fa-sign-in-alt"></i> Giriş Yap</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- WELCOME -->
    <section class="welcome-section">
        <h2>🥜 <?= htmlspecialchars($dried_goods['name']) ?></h2>
        <p>Taze kuruyemişler, kuru meyveler ve daha fazlası!</p>
    </section>

    <!-- SEARCH -->
    <div class="search-bar-container">
        <form method="get" class="search-bar">
            <input type="text" name="search" placeholder="Ürün ara..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit"><i class="fas fa-search"></i> Ara</button>
        </form>
    </div>

    <!-- ===== ANA KATEGORİ BUTONLARI (index.php ile aynı) ===== -->
    <div class="main-cat-bar">
        <?php
        $mainIcons = [
            'restaurant'  => 'fa-utensils',
            'market'      => 'fa-shopping-basket',
            'grocery'     => 'fa-carrot',
            'dried-goods' => 'fa-seedling',
            'default'     => 'fa-store'
        ];
        foreach ($mainCats as $mc):
            $icon = $mainIcons[$mc['slug']] ?? $mainIcons['default'];
            $link = match($mc['slug']){
                'restaurant'  => '../menu.php?rid=' . ($pdo->query("SELECT id FROM businesses WHERE vendor_type_id = {$mc['id']} AND is_approved = 1 LIMIT 1")->fetchColumn() ?? 0),
                'market'      => '../market/index.php',
                'grocery'     => '../grocery/index.php',
                'dried-goods' => '#',
                default       => '#'
            };
        ?>
            <a href="<?= $link ?>" class="main-cat-btn <?= $mc['slug']==='dried-goods' ? 'active' : '' ?>">
                <i class="fas <?= $icon ?>"></i> <?= htmlspecialchars($mc['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- ===== KURUYEMİŞ ALT-KATEGORİ BUTONLARI ===== -->
    <div class="sub-cat-bar">
        <a href="?category=0" class="sub-cat-btn <?= !$category_id ? 'active' : '' ?>">Tümü</a>
        <?php foreach ($driedCategories as $cat): ?>
            <a href="?category=<?= $cat['id'] ?>&search=<?= urlencode($search) ?>"
               class="sub-cat-btn <?= $category_id == $cat['id'] ? 'active' : '' ?>">
                <?= htmlspecialchars($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- PRODUCTS -->
    <?php if (empty($items)): ?>
        <div class="text-center text-muted py-5"><i class="fas fa-box-open" style="font-size: 60px;"></i><br>Ürün bulunamadı.</div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($items as $item): ?>
                <div class="product-card">
                    <div class="product-img"><i class="fas fa-seedling"></i></div>
                    <div class="product-name"><?= htmlspecialchars($item['name']) ?></div>
                    <div class="product-price"><?= number_format($item['price'], 2) ?> ₺</div>
                    <button class="add-to-cart" onclick="addToCart(<?= $item['id'] ?>, '<?= htmlspecialchars($item['name']) ?>', <?= $item['price'] ?>, <?= $dried_goods_id ?>)"><i class="fas fa-cart-plus"></i> Sepete Ekle</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<footer>© 2025 Tıkla Gelir | Tüm Hakları Saklıdır.</footer>

<script>
function addToCart(id, name, price, restaurantId){
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    if (cart.length && cart[0].restaurant_id !== restaurantId){
        if (!confirm('Sepette başka restoran ürünü var. Temizleyip yenisini eklensin mi?')) return;
        cart = [];
    }
    let existing = cart.find(item => item.id === id && item.restaurant_id === restaurantId);
    existing ? existing.quantity++ : cart.push({id, name, price, quantity: 1, restaurant_id: restaurantId});
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    alert('✅ Sepete eklendi!');
}
function updateCartCount(){
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    document.getElementById('cartCount').textContent = cart.reduce((s,i)=>s+i.quantity,0);
}
function toggleSupport(){
    document.getElementById('supportDropdown').classList.toggle('show');
}
document.addEventListener('click', e=>{
    const btn=document.querySelector('.support-button');
    const dropdown=document.getElementById('supportDropdown');
    if(!btn.contains(e.target)&&!dropdown.contains(e.target)) dropdown.classList.remove('show');
});
function logout(){
    if(confirm('Çıkış yapmak istediğinize emin misiniz?')) window.location.href='../logout.php';
}
function toggleMobileNav() {
    const nav = document.getElementById('headerActions');
    nav.classList.toggle('active');
}
document.addEventListener('click', e => {
    const nav = document.getElementById('headerActions');
    const toggler = document.querySelector('.navbar-toggler');
    if (!nav.contains(e.target) && toggler && !toggler.contains(e.target)) {
        nav.classList.remove('active');
    }
});
document.addEventListener('DOMContentLoaded', ()=>{
    updateCartCount();
    document.getElementById('cartButton').addEventListener('click',()=>window.location.href='../cart.php');
});
</script>

</body>
</html>