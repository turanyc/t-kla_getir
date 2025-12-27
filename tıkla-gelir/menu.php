<?php
session_start();
require_once "config/database.php"; 



$restaurant_id = (int)($_GET['rid'] ?? 0);
if ($restaurant_id <= 0) {
    die("Geçersiz restoran!");
}

$user_id = $_SESSION['user_id'];

// Restoran bilgisi (is_open durumunu da çek)
$rest_stmt = $pdo->prepare("SELECT r.id, r.name, r.address, r.is_open,
                            COALESCE(AVG(rev.rating), 0) as average_rating,
                            COUNT(rev.id) as total_reviews
                            FROM restaurants r
                            LEFT JOIN reviews rev ON r.id = rev.restaurant_id
                            WHERE r.id = ?
                            GROUP BY r.id");
$rest_stmt->execute([$restaurant_id]);
$restaurant = $rest_stmt->fetch();

if (!$restaurant) {
    die("Restoran bulunamadı!");
}

// Menüyü çek
$stmt = $pdo->prepare("SELECT * FROM menu_items WHERE restaurant_id = ? AND stock > 0 ORDER BY category, name");
$stmt->execute([$restaurant_id]);
$menu_items = $stmt->fetchAll();

// Restoran kapalıysa uyarı
$isRestaurantOpen = isset($restaurant['is_open']) && $restaurant['is_open'] == 1;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($restaurant['name']) ?> - Menü</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #FF4500;
            --secondary: #1A1A2E;
            --success: #00C853;
            --danger: #F44336;
            --warning: #FFD700;
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
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light);
            color: var(--dark);
            min-height: 100vh;
            overflow-x: hidden;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 15px; 
            overflow-x: hidden;
        }
        
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
            position: relative;
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
        
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--gradient);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
            font-size: clamp(12px, 2.5vw, 14px);
            white-space: nowrap;
        }
        
        .back-button:hover {
            transform: translateX(-5px);
            box-shadow: var(--shadow-hover);
        }
        
        .cart-button {
            background: var(--gradient);
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 50px;
            cursor: pointer;
            font-size: clamp(12px, 2.5vw, 16px);
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: var(--shadow);
            position: relative;
            white-space: nowrap;
        }
        
        .cart-button:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--warning);
            color: #333;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
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
            background: var(--gradient); 
            color: white; 
            border: none; 
            border-radius: 0; 
            text-align: left; 
            text-decoration: none; 
            font-weight: 600; 
            margin-bottom: 10px; 
            cursor: pointer;
        }

        /* Restoran Durumu Uyarısı */
        .restaurant-closed-warning {
            background: #FFF3E0;
            border: 2px solid var(--primary);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: var(--shadow);
        }
        
        .restaurant-closed-warning h3 {
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .restaurant-header {
            text-align: center;
            padding: 40px 20px;
            background: var(--card-bg);
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }
        
        .restaurant-header h1 {
            font-size: clamp(24px, 5vw, 42px);
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        .rating-section {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        
        .stars {
            color: var(--warning);
            font-size: clamp(18px, 4vw, 24px);
        }
        
        .rating-text {
            color: var(--dark);
            font-size: clamp(14px, 3vw, 16px);
            font-weight: 500;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }
        
        .menu-card {
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            position: relative;
        }
        
        .menu-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }
        
        .menu-card.inactive {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .menu-img {
            height: 200px;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: rgba(255,255,255,0.7);
        }
        
        .menu-info {
            padding: 25px;
        }
        
        .menu-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--dark);
        }
        
        .menu-price {
            font-size: 28px;
            color: var(--primary);
            font-weight: bold;
            margin: 15px 0;
        }
        
        .add-btn {
            width: 100%;
            padding: 14px;
            background: var(--gradient);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }
        
        .add-btn:hover {
            transform: scale(1.02);
            box-shadow: var(--shadow-hover);
        }
        
        .add-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .cart-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .cart-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .cart-panel {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100%;
            background: var(--card-bg);
            z-index: 2001;
            transition: right 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: -5px 0 30px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
        }
        
        .cart-panel.active {
            right: 0;
        }
        
        .cart-header {
            background: var(--gradient);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .cart-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            background: #FFF8F0;
            margin-bottom: 10px;
            border-radius: 10px;
        }
        
        .cart-total {
            padding: 20px;
            border-top: 1px solid var(--border-color);
            background: var(--card-bg);
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        
        .checkout-btn {
            width: 100%;
            padding: 15px;
            background: var(--gradient);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }
        
        .checkout-btn:hover {
            transform: scale(1.02);
            box-shadow: var(--shadow-hover);
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .container { padding: 12px; }
            .hamburger-menu { display: block; }
            .main-header { padding: 12px 0; }
            .restaurant-header { padding: 25px 15px; }
            .restaurant-closed-warning { padding: 20px; }
            .menu-grid { grid-template-columns: 1fr; gap: 15px; }
            .menu-card { margin-bottom: 0; }
            .menu-img { height: 160px; font-size: 40px; }
            .menu-info { padding: 20px; }
            .menu-name { font-size: clamp(18px, 4vw, 24px); }
            .menu-price { font-size: clamp(20px, 4vw, 28px); }
            .add-btn { padding: 12px; font-size: 14px; }
            .cart-panel { width: 100%; right: -100%; }
            .cart-header { padding: 15px; }
            .cart-content { padding: 15px; }
        }
        
        @media (max-width: 480px) {
            .main-header { flex-direction: column; align-items: flex-start; }
            .back-button, .cart-button { width: 100%; justify-content: center; }
            .restaurant-header h1 { font-size: 22px; }
            .menu-img { height: 140px; }
            .menu-info { padding: 15px; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>

<!-- Sepet Paneli -->
<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-panel" id="cartPanel">
    <div class="cart-header">
        <h3><i class="fas fa-shopping-cart"></i> Sepetim</h3>
        <button class="close-cart" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer;">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="cart-content" id="cartContent">
        <p style="text-align:center;color:#999;">Sepetinizde ürün bulunmuyor.</p>
    </div>
    <div class="cart-total" id="cartTotal" style="display:none;">
        <div style="display:flex;justify-content:space-between;margin-bottom:15px;font-size:20px;font-weight:700;">
            <span>Toplam:</span>
            <span id="cartTotalPrice">0.00 ₺</span>
        </div>
        <button class="checkout-btn" onclick="checkout()">Siparişi Tamamla</button>
    </div>
</div>

<div class="container">
    <!-- Kapalı Restoran Uyarısı -->
    <?php if(!$isRestaurantOpen): ?>
        <div class="restaurant-closed-warning">
            <i class="fas fa-store-slash" style="font-size: 50px; color: var(--primary); margin-bottom: 15px;"></i>
            <h3>Restoran Şu An Kapalı</h3>
            <p>Bu restoran şu anda sipariş kabul etmiyor.</p>
        </div>
    <?php endif; ?>

    <div class="main-header">
        <button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>
        <a href="index.php" class="back-button"><i class="fas fa-arrow-left"></i> Geri Dön</a>
        <button class="cart-button" id="cartButton">
            <i class="fas fa-shopping-cart"></i> Sepet <span class="cart-count" id="cartCount">0</span>
        </button>
    </div>

<!-- MOBILE MENU -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
<nav class="mobile-menu" id="mobileMenu">
    <a href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
    <button id="mobileCartButton"><i class="fas fa-shopping-cart"></i> Sepet <span class="cart-count" id="mobileCartCount">0</span></button>
    <button onclick="if(confirm('Çıkış yapılsın mı?')) window.location='logout.php'"><i class="fas fa-sign-out-alt"></i> Çıkış</button>
</nav>

    <div class="restaurant-header">
        <h1><?= htmlspecialchars($restaurant['name']) ?></h1>
        <div style="color:#666;margin-bottom:10px;"><?= htmlspecialchars($restaurant['address']) ?></div>
        <div class="rating-section">
            <span class="stars"><?= str_repeat('<i class="fas fa-star"></i>', floor($restaurant['average_rating'])) ?></span>
            <span class="rating-text"><?= number_format($restaurant['average_rating'], 1) ?>/5</span>
            <span class="rating-text">(<?= $restaurant['total_reviews'] ?> yorum)</span>
            <?php if(false): // Review butonu şimdilik gizli ?>
            <a href="review.php?rid=<?= $restaurant_id ?>" class="review-button">
                Yorum Yap
            </a>
            <?php endif; ?>
        </div>
        <?php if(!$isRestaurantOpen): ?>
            <p style="color: var(--danger); font-weight: 700; margin-top: 15px;">
                <i class="fas fa-times-circle"></i> Restoran Şu An Kapalı
            </p>
        <?php endif; ?>
    </div>
    
    <?php if(empty($menu_items)): ?>
        <div style="text-align:center;padding:80px;color:#999;">
            <i class="fas fa-utensils" style="font-size:80px;margin-bottom:20px;"></i><br>
            Menüde ürün bulunmuyor.
        </div>
    <?php else: ?>
        <div class="menu-grid">
            <?php foreach($menu_items as $item): ?>
                <div class="menu-card <?= $isRestaurantOpen ? '' : 'inactive' ?>">
                    <div class="menu-img"><i class="fas fa-utensils"></i></div>
                    <div class="menu-info">
                        <div class="menu-name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="menu-price"><?= number_format($item['price'], 2) ?> ₺</div>
                        <form method="post" onsubmit="addToCart(event, <?= $item['id'] ?>, '<?= htmlspecialchars($item['name']) ?>', <?= $item['price'] ?>, <?= $item['stock'] ?>)">
                            <input type="number" name="quantity" value="1" min="1" max="<?= $item['stock'] ?>" style="width:70px;padding:8px;margin-right:10px;border:1px solid var(--border-color);border-radius:8px;" <?= $isRestaurantOpen ? '' : 'disabled' ?>>
                            <button type="submit" class="add-btn" <?= $isRestaurantOpen ? '' : 'disabled' ?>>
                                <?= $isRestaurantOpen ? 'SEPETE EKLE' : 'RESTORAN KAPALI' ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
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

// Sepet işlevselliği
let cart = JSON.parse(localStorage.getItem('cart') || '[]');
updateCartDisplay();

function updateCartDisplay() {
    const cartCount = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
    document.getElementById('cartCount').textContent = cartCount;
    const mobileCartCount = document.getElementById('mobileCartCount');
    if (mobileCartCount) mobileCartCount.textContent = cartCount;
    const mobileCartCount = document.getElementById('mobileCartCount');
    if (mobileCartCount) mobileCartCount.textContent = cartCount;
    
    const cartContent = document.getElementById('cartContent');
    const cartTotal = document.getElementById('cartTotal');
    
    if (cart.length === 0) {
        cartContent.innerHTML = '<p style="text-align:center;color:#999;">Sepetinizde ürün bulunmuyor.</p>';
        cartTotal.style.display = 'none';
        return;
    }
    
    let html = '';
    let total = 0;
    
    cart.forEach((item, index) => {
        const subtotal = item.price * item.quantity;
        total += subtotal;
        
        html += `
            <div class="cart-item">
                <div>
                    <strong>${item.name}</strong><br>
                    <small>${item.quantity} x ${item.price.toFixed(2)} ₺</small>
                </div>
                <div>
                    <strong>${subtotal.toFixed(2)} ₺</strong>
                    <button onclick="removeFromCart(${index})" style="background:#ff4444;color:white;border:none;border-radius:50%;width:25px;height:25px;margin-left:10px;cursor:pointer;font-size:12px;">×</button>
                </div>
            </div>
        `;
    });
    
    cartContent.innerHTML = html;
    document.getElementById('cartTotalPrice').textContent = total.toFixed(2) + ' ₺';
    cartTotal.style.display = 'block';
}

function addToCart(event, id, name, price, stock) {
    event.preventDefault();
    if (! <?= $isRestaurantOpen ? 'true' : 'false' ?>) {
        alert('❌ Restoran kapalı, sipariş veremezsiniz!');
        return;
    }
    
    const quantity = parseInt(event.target.querySelector('input[type="number"]').value);
    const existingIndex = cart.findIndex(item => item.id === id);
    
    if (existingIndex !== -1) {
        if (cart[existingIndex].quantity + quantity <= stock) {
            cart[existingIndex].quantity += quantity;
        } else {
            alert('⚠️ Stok limiti! Mevcut: ' + stock);
            return;
        }
    } else {
        cart.push({id: id, name: name, price: price, quantity: quantity});
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
    alert('✅ ' + name + ' sepete eklendi!');
}

function removeFromCart(index) {
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
}

function checkout() {
    if (cart.length === 0) {
        alert('❌ Sepetiniz boş!');
        return;
    }
    window.location.href = 'checkout.php';
}

// Sepet paneli olayları
document.getElementById('cartButton').addEventListener('click', function() {
    document.getElementById('cartPanel').classList.add('active');
    document.getElementById('cartOverlay').style.display = 'block';
    updateCartDisplay();
});

document.querySelector('.close-cart').addEventListener('click', function() {
    document.getElementById('cartPanel').classList.remove('active');
    document.getElementById('cartOverlay').style.display = 'none';
});

document.getElementById('cartOverlay').addEventListener('click', function() {
    document.getElementById('cartPanel').classList.remove('active');
    document.getElementById('cartOverlay').style.display = 'none';
});
</script>

</body>
</html>