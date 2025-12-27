<?php

ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);



session_start();

require_once "../config/database.php";



$user_name   = $_SESSION['name'] ?? '';

$user_id     = $_SESSION['user_id'] ?? null;

$isLoggedIn  = isset($_SESSION['user_id']);



// Restoran bilgisi

$stmt = $pdo->prepare("

    SELECT b.*, vt.name as vendor_type_name

    FROM businesses b

    JOIN vendor_types vt ON vt.id = b.vendor_type_id

    WHERE vt.slug = 'restaurant' AND b.is_approved = 1

    LIMIT 1

");

$stmt->execute();

$restaurant = $stmt->fetch();

if (!$restaurant) {

    die("<div style='text-align:center;padding:50px;'><h2>Restoran şu anda hizmet dışı.</h2></div>");

}

$restaurant_id = $restaurant['id'];



// Filtreler

$search      = isset($_GET['search']) ? trim($_GET['search']) : '';

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;



$where = ["mi.business_id = ?"];

$params = [$restaurant_id];

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



// Menüler

$items = $pdo->prepare("

    SELECT mi.*, mc.name as category_name 

    FROM menu_items mi

    LEFT JOIN categories mc ON mi.category_id = mc.id

    WHERE $where_sql AND mi.is_available = 1

    ORDER BY mi.name

");

$items->execute($params);

$items = $items->fetchAll();



// Restoran kategorileri

$restaurantCategories = $pdo->prepare("

    SELECT DISTINCT mc.id, mc.name

    FROM categories mc

    JOIN menu_items mi ON mi.category_id = mc.id

    WHERE mi.business_id = ?

    ORDER BY mc.name

");

$restaurantCategories->execute([$restaurant_id]);

$restaurantCategories = $restaurantCategories->fetchAll();



// Ana kategori butonları

$mainCats = $pdo->query("SELECT id, name, slug FROM vendor_types WHERE is_approved = 1 ORDER BY name")->fetchAll();

?>

<!DOCTYPE html>

<html lang="tr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($restaurant['name']) ?> - Tıkla Gelir</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        :root {

            --primary: #FF6B35; --primary-dark: #FF4500; --success: #00C853; --danger: #F44336;

            --warning: #FF9100; --info: #2196F3; --light: #F8F9FA; --dark: #333333; --card-bg: #FFFFFF;

            --border-color: #E0E0E0; --shadow: 0 4px 12px rgba(0,0,0,0.08); --shadow-hover: 0 8px 25px rgba(0,0,0,0.15);

            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);

        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { font-family: 'Poppins', sans-serif; background: var(--light); color: var(--dark); min-height: 100vh; }

        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }

        .main-header { display: flex; justify-content: space-between; align-items: center; padding: 25px 40px; margin: -20px -20px 30px -20px; background: var(--card-bg); border-radius: 0 0 25px 25px; box-shadow: var(--shadow); flex-wrap: wrap; gap: 20px; position: sticky; top: 0; z-index: 1000; }

        .logo-section h1 { font-size: clamp(24px, 5vw, 32px); color: var(--primary); font-weight: 700; }
        .logo-section p { font-size: clamp(12px, 2.5vw, 16px); margin: 0; }
        .logo-section a { text-decoration: none; color: inherit; }

        .header-actions { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }

        .header-button, .login-button { background: var(--gradient); color: white; border: none; padding: clamp(10px, 2.5vw, 12px) clamp(16px, 4vw, 24px); border-radius: 50px; cursor: pointer; font-size: clamp(14px, 3vw, 16px); font-weight: 600; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px; text-decoration: none; box-shadow: var(--shadow); position: relative; white-space: nowrap; }

        .header-button:hover, .login-button:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }

        .login-button { background: linear-gradient(135deg, var(--success) 0%, #2E7D32 100%); }

        .cart-count { position: absolute; top: -8px; right: -8px; background: var(--warning); color: #333; border-radius: 50%; width: clamp(20px, 4vw, 24px); height: clamp(20px, 4vw, 24px); display: flex; align-items: center; justify-content: center; font-size: clamp(10px, 2vw, 12px); font-weight: bold; }

        .welcome-section { text-align: center; padding: clamp(30px, 8vw, 60px) 20px; background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%); border-radius: 25px; margin-bottom: 40px; border: 2px solid #FFCC80; }

        .welcome-section h2 { font-size: clamp(24px, 5vw, 36px); margin-bottom: 10px; color: var(--primary-dark); }

        .welcome-section p { font-size: clamp(14px, 3vw, 18px); color: var(--dark); font-weight: 500; }

        .search-bar-container { margin-bottom: 30px; }

        .search-bar { width: 100%; max-width: 600px; margin: 0 auto; display: flex; gap: 10px; flex-wrap: wrap; }

        .search-bar input { flex: 1; min-width: 150px; padding: clamp(12px, 3vw, 15px) clamp(15px, 4vw, 25px); border: 1px solid var(--border-color); border-radius: 50px; font-size: clamp(14px, 3vw, 16px); transition: all 0.3s; }

        .search-bar input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.2); }

        .search-bar button { padding: clamp(12px, 3vw, 15px) clamp(20px, 5vw, 30px); background: var(--gradient); color: white; border: none; border-radius: 50px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: clamp(14px, 3vw, 16px); white-space: nowrap; }

        .search-bar button:hover { transform: translateY(-2px); box-shadow: var(--shadow-hover); }



        /* ===== ANA KATEGORİ BUTONLARI (Tema Uyumlu) ===== */

        .main-cat-bar { display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; margin-bottom: 25px; }

        .main-cat-btn {

            background: var(--gradient);

            color: white;

            padding: clamp(10px, 2.5vw, 14px) clamp(16px, 4vw, 28px);

            border-radius: 50px;

            font-size: clamp(13px, 2.5vw, 15px);

            font-weight: 600;

            text-decoration: none;

            transition: all 0.3s ease;

            box-shadow: var(--shadow);

            display: inline-flex;

            align-items: center;

            gap: 8px;

            white-space: nowrap;

        }

        .main-cat-btn:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }



        /* ===== RESTORAN ALT-KATEGORİ BUTONLARI (Tema Uyumlu) ===== */

        .sub-cat-bar { display: flex; justify-content: center; flex-wrap: wrap; gap: 8px; margin-bottom: 30px; }

        .sub-cat-btn {

            background: var(--primary);

            color: white;

            padding: clamp(6px, 1.5vw, 8px) clamp(12px, 3vw, 18px);

            border-radius: 50px;

            font-size: clamp(12px, 2.5vw, 14px);

            font-weight: 500;

            text-decoration: none;

            transition: all 0.3s ease;

            box-shadow: var(--shadow);

            white-space: nowrap;

        }

        .sub-cat-btn:hover,

        .sub-cat-btn.active { background: #FF4500; box-shadow: var(--shadow-hover); }



        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(clamp(140px, 25vw, 220px), 1fr)); gap: clamp(15px, 3vw, 25px); margin-bottom: 50px; }

        .product-card { background: var(--card-bg); border-radius: 20px; overflow: hidden; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: var(--shadow); border: 1px solid var(--border-color); text-align: center; padding: clamp(15px, 3vw, 20px); }

        .product-card:hover { transform: translateY(-10px); box-shadow: var(--shadow-hover); }

        .product-img { height: clamp(100px, 20vw, 150px); background: var(--gradient); display: flex; align-items: center; justify-content: center; font-size: clamp(30px, 6vw, 50px); color: rgba(255,255,255,0.7); border-radius: 15px; margin-bottom: 15px; }

        .product-name { font-size: clamp(14px, 3vw, 18px); font-weight: 700; margin-bottom: 8px; color: var(--dark); word-break: break-word; }

        .product-price { font-size: clamp(18px, 4vw, 22px); font-weight: 700; color: var(--primary); margin-bottom: 15px; }

        .add-to-cart { padding: clamp(10px, 2.5vw, 12px) clamp(18px, 4vw, 25px); background: var(--gradient); color: white; border: none; border-radius: 50px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: var(--shadow); font-size: clamp(12px, 2.5vw, 14px); width: 100%; }

        .add-to-cart:hover { transform: scale(1.05); box-shadow: var(--shadow-hover); }

        footer { text-align: center; padding: 30px; color: var(--primary); font-weight: 600; margin-top: 50px; background: #FFF3E0; border-top: 3px solid #FFE0B2; }

        .support-button { position: fixed; bottom: clamp(20px, 5vw, 30px); right: clamp(20px, 5vw, 30px); background: var(--gradient); color: white; border: none; border-radius: 50%; width: clamp(50px, 10vw, 60px); height: clamp(50px, 10vw, 60px); font-size: clamp(20px, 4vw, 24px); cursor: pointer; box-shadow: var(--shadow-hover); z-index: 9999; transition: all 0.3s ease; }

        .support-button:hover { transform: scale(1.1); }

        .support-dropdown { position: fixed; bottom: clamp(80px, 15vw, 100px); right: clamp(20px, 5vw, 30px); background: white; border-radius: 15px; box-shadow: var(--shadow-hover); padding: 15px; display: none; flex-direction: column; gap: 10px; z-index: 9998; min-width: clamp(180px, 40vw, 220px); }

        .support-dropdown a { display: flex; align-items: center; gap: 10px; padding: 10px 15px; border-radius: 10px; text-decoration: none; color: var(--dark); font-weight: 500; transition: background 0.3s; font-size: clamp(13px, 3vw, 15px); }

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

            <a href="../index.php" style="text-decoration: none; color: inherit;"><h1><i class="fas fa-utensils"></i> Tıkla Gelir</h1></a>

            <p><strong><?= htmlspecialchars($restaurant['name']) ?></strong> - Online Restoran</p>

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

        <h2>🍽️ <?= htmlspecialchars($restaurant['name']) ?></h2>

        <p>Lezzetli menüler, hızlı teslimat!</p>

    </section>



    <!-- SEARCH -->

    <div class="search-bar-container">

        <form method="get" class="search-bar">

            <input type="text" name="search" placeholder="Menü ara..." value="<?= htmlspecialchars($search) ?>">

            <button type="submit"><i class="fas fa-search"></i> Ara</button>

        </form>

    </div>



    <!-- ===== ANA KATEGORİ BUTONLARI (Tema Uyumlu) ===== -->

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

                'restaurant'  => '#',                               // ← kendi sayfamızda kal

                'market'      => '../market/index.php',

                'grocery'     => '../grocery/index.php',

                'dried-goods' => '../dried_goods/index.php',

                default       => '#'

            };

        ?>

            <a href="<?= $link ?>" class="main-cat-btn <?= $mc['slug']==='restaurant' ? 'active' : '' ?>">

                <i class="fas <?= $icon ?>"></i> <?= htmlspecialchars($mc['name']) ?>

            </a>

        <?php endforeach; ?>

    </div>



    <!-- ===== RESTORAN ALT-KATEGORİ BUTONLARI (Tema Uyumlu) ===== -->

    <div class="sub-cat-bar">

        <a href="?category=0" class="sub-cat-btn <?= !$category_id ? 'active' : '' ?>">Tümü</a>

        <?php foreach ($restaurantCategories as $cat): ?>

            <a href="?category=<?= $cat['id'] ?>&search=<?= urlencode($search) ?>"

               class="sub-cat-btn <?= $category_id == $cat['id'] ? 'active' : '' ?>">

                <?= htmlspecialchars($cat['name']) ?>

            </a>

        <?php endforeach; ?>

    </div>



    <!-- PRODUCTS (MENÜ) -->

    <?php if (empty($items)): ?>

        <div class="text-center text-muted py-5"><i class="fas fa-box-open" style="font-size: 60px;"></i><br>Menü bulunamadı.</div>

    <?php else: ?>

        <div class="products-grid">

            <?php foreach ($items as $item): ?>

                <div class="product-card">

                    <div class="product-img"><i class="fas fa-utensils"></i></div>

                    <div class="product-name"><?= htmlspecialchars($item['name']) ?></div>

                    <div class="product-price"><?= number_format($item['price'], 2) ?> ₺</div>

                    <button class="add-to-cart" onclick="addToCart(<?= $item['id'] ?>, '<?= htmlspecialchars($item['name']) ?>', <?= $item['price'] ?>, <?= $restaurant_id ?>)"><i class="fas fa-cart-plus"></i> Sepete Ekle</button>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>



<footer>© 2025 Tıkla Gelir | Tüm Hakları Saklıdır.</footer>



<script>
// YENİ SİSTEM – VERİTABANINA BAĞLI SEPET (localStorage KALDIRILDI)
async function addToCart(id, name, price, businessId) {
    if (!<?= json_encode($isLoggedIn) ?>) {
        if (confirm('Sepete eklemek için giriş yapmalısınız. Giriş sayfasına yönlendirilsin mi?')) {
            window.location.href = '../login.php';
        }
        return;
    }

    try {
        const response = await fetch('../api/update_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `menu_item_id=${id}&quantity=1`
        });

        const data = await response.json();

        if (data.success) {
            updateCartCount();
            alert(name + ' sepete eklendi!');
        } else {
            alert('Hata: ' + (data.message || 'Ürün eklenemedi'));
        }
    } catch (err) {
        alert('Bağlantı hatası!');
        console.error(err);
    }
}

// Sepet sayısını veritabanından çek
async function updateCartCount() {
    try {
        const res = await fetch('../api/get_cart.php');
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();
        const count = data.count || 0;
        document.getElementById('cartCount').textContent = count;
    } catch (err) {
        console.log('Sepet sayısı alınamadı:', err);
    }
}

// Sayfa açılınca + ürün eklendikten sonra sepet sayısını güncelle
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();

    // Sepet butonuna tıklayınca cart.php'ye git
    document.getElementById('cartButton').addEventListener('click', () => {
        window.location.href = '../cart.php';
    });
});

// Eski fonksiyonlar (destek butonu, çıkış) aynı kalıyor
function toggleSupport() {
    document.getElementById('supportDropdown').classList.toggle('show');
}
document.addEventListener('click', e => {
    const btn = document.querySelector('.support-button');
    const dropdown = document.getElementById('supportDropdown');
    if (!btn || !dropdown) return;
    if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});
function logout() {
    if (confirm('Çıkış yapmak istediğinize emin misiniz?')) {
        window.location.href = '../logout.php';
    }
}
function toggleMobileNav() {
    const nav = document.getElementById('headerActions');
    nav.classList.toggle('active');
}
document.addEventListener('click', e => {
    const nav = document.getElementById('headerActions');
    const toggler = document.querySelector('.navbar-toggler');
    if (!nav.contains(e.target) && !toggler.contains(e.target)) {
        nav.classList.remove('active');
    }
});
</script>



</body>

</html>