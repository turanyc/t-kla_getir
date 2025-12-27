<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['name'];

// SEPETİ DOĞRU ŞEKİLDE ÇEK (menu_item_id üzerinden!)
try {
    $stmt = $pdo->prepare("
        SELECT c.*, mi.name, mi.price, mi.image, b.name as business_name
        FROM cart c
        JOIN menu_items mi ON c.menu_item_id = mi.id
        JOIN businesses b ON mi.business_id = b.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sub_total = 0;
    foreach ($cart_items as $item) {
        $sub_total += $item['price'] * $item['quantity'];
    }
} catch(Exception $e) {
    $cart_items = [];
    $sub_total = 0;
}

// Varsayılan adres
$stmt_addr = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? AND is_default = 1 LIMIT 1");
$stmt_addr->execute([$user_id]);
$default_address = $stmt_addr->fetch();

// Promosyon kontrolü
$promo = null;
$discount = 0;
$delivery_fee = 15.00;

if (isset($_SESSION['applied_promo'])) {
    $promo = $_SESSION['applied_promo'];
    $discount = round($sub_total * ($promo['discount_percent'] / 100), 2);
    if ($promo['free_delivery']) $delivery_fee = 0;
}

$final_total = $sub_total + $delivery_fee - $discount;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Özeti - Tıkla Gelir</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #FF4500;
            --success: #00C853;
            --light: #f8f9fa;
            --dark: #333;
            --gradient: linear-gradient(135deg, #FF6B35, #FF4500);
            --card-bg: #ffffff;
            --border: #e0e0e0;
            --shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { 
            font-family: 'Poppins', sans-serif; 
            background: #f5f7fa; 
            color: var(--dark); 
            padding-top: 70px; 
            min-height: 100vh; 
            overflow-x: hidden;
        }
        
        .header { 
            position:fixed; 
            top:0; 
            left:0; 
            right:0; 
            background:white; 
            padding:12px 15px; 
            box-shadow:0 4px 15px rgba(0,0,0,0.1); 
            z-index:1000; 
            display:flex; 
            justify-content:space-between; 
            align-items:center; 
            flex-wrap: wrap;
            gap: 10px;
        }
        .back-btn { 
            background:var(--gradient); 
            color:white; 
            border:none; 
            padding:8px 16px; 
            border-radius:50px; 
            cursor:pointer; 
            font-weight:600; 
            font-size: clamp(12px, 2.5vw, 14px);
            white-space: nowrap;
        }
        .header > div { 
            font-size: clamp(12px, 2.5vw, 14px);
            text-align: right;
        }
        .hamburger-menu { 
            display: none; 
            background: var(--gradient); 
            color: white; 
            border: none; 
            padding: 8px 12px; 
            border-radius: 10px; 
            font-size: 20px; 
            cursor: pointer; 
            z-index: 1001;
        }

        .container { 
            max-width:900px; 
            margin:0 auto; 
            padding:15px; 
        }
        .checkout-card { background:var(--card-bg); border-radius:25px; padding:35px; box-shadow:var(--shadow); }

        .section-title { font-size:24px; color:var(--primary-dark); margin:25px 0 15px; font-weight:700; }
        .order-item { display:flex; justify-content:space-between; padding:15px 0; border-bottom:1px solid #eee; }
        .order-item:last-child { border-bottom:2px solid var(--primary); font-weight:600; }

        .summary-box { background:#fff8f0; padding:20px; border-radius:15px; margin:20px 0; border:2px solid #ffccbc; }
        .total-row { background:var(--gradient); color:white; padding:20px; border-radius:15px; font-size:22px; font-weight:700; text-align:center; margin:25px 0; }

        .promo-box { background:#fff0f0; border:2px dashed var(--primary); border-radius:15px; padding:20px; text-align:center; }
        .promo-input { padding:12px; border:2px solid var(--primary); border-radius:10px; width:70%; font-size:16px; }
        .promo-btn { padding:12px 25px; background:var(--gradient); color:white; border:none; border-radius:10px; margin-left:10px; cursor:pointer; font-weight:600; }

        .address-box { background:#e8f5e8; padding:20px; border-radius:15px; border-left:5px solid var(--success); }
        .payment-options { display:flex; gap:15px; margin:20px 0; }
        .pay-opt { flex:1; padding:20px; text-align:center; border:2px solid #ddd; border-radius:15px; cursor:pointer; transition:all .3s; }
        .pay-opt:hover, .pay-opt.selected { border-color:var(--primary); background:#fff0f0; }

        .order-btn { width:100%; padding:18px; background:var(--gradient); color:white; border:none; border-radius:50px; font-size:20px; font-weight:700; cursor:pointer; margin-top:30px; box-shadow:var(--shadow); transition:all .3s; }
        .order-btn:hover { transform:translateY(-3px); box-shadow:0 15px 30px rgba(255,107,53,0.4); }

        .empty-cart { text-align:center; padding:100px 20px; color:#999; }
        .empty-cart i { font-size:80px; color:#ddd; margin-bottom:20px; }
        
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
        
        @media (max-width:768px) {
            body { padding-top: 60px; }
            .header { padding: 10px 12px; }
            .hamburger-menu { display: block; }
            .container { padding: 12px; }
            .checkout-card { padding: 20px; }
            .section-title { font-size: clamp(18px, 4vw, 24px); }
            .order-item { 
                flex-direction: column; 
                align-items: flex-start; 
                gap: 5px; 
            }
            .promo-box { padding: 15px; }
            .promo-input { width: 100%; margin-bottom: 10px; }
            .promo-btn { width: 100%; margin-left: 0; }
            .payment-options { flex-direction: column; }
            .pay-opt { padding: 15px; }
            .order-btn { 
                padding: 14px; 
                font-size: clamp(16px, 3.5vw, 20px); 
            }
            .summary-box { padding: 15px; }
            .total-row { 
                padding: 15px; 
                font-size: clamp(18px, 4vw, 22px); 
            }
        }
        
        @media (max-width:480px) {
            .header { flex-direction: column; align-items: flex-start; }
            .checkout-card { padding: 15px; }
            .section-title { font-size: 18px; margin: 20px 0 10px; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>

<div class="header">
    <button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMobileMenu()">
        <i class="fas fa-bars"></i>
    </button>
    <button class="back-btn" onclick="history.back()">Geri Dön</button>
    <div><strong><?= htmlspecialchars($user_name) ?></strong> - Sipariş Özeti</div>
</div>

<!-- MOBILE MENU -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
<nav class="mobile-menu" id="mobileMenu">
    <a href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
    <a href="cart.php"><i class="fas fa-shopping-cart"></i> Sepetim</a>
    <button onclick="if(confirm('Çıkış yapılsın mı?')) window.location='logout.php'"><i class="fas fa-sign-out-alt"></i> Çıkış</button>
</nav>

<div class="container">
    <?php if (!empty($cart_items)): ?>
        <div class="checkout-card">
            <h2 class="section-title">Sepetiniz (<?= count($cart_items) ?> ürün)</h2>
            
            <?php foreach($cart_items as $item): ?>
            <div class="order-item">
                <div>
                    <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                    <small style="color:#666;"><?= htmlspecialchars($item['business_name']) ?></small>
                </div>
                <div>
                    <strong><?= $item['quantity'] ?> × <?= number_format($item['price'], 2) ?> ₺</strong><br>
                    <span style="color:var(--primary); font-weight:600;">
                        <?= number_format($item['price'] * $item['quantity'], 2) ?> ₺
                    </span>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- PROMOSYON KODU -->
            <div class="promo-box">
                <h3>Promosyon Kodu</h3>
                <form method="post" action="apply_promo.php" style="display:inline;">
                    <input type="text" name="code" class="promo-input" placeholder="Kodu girin" required>
                    <button type="submit" class="promo-btn">Uygula</button>
                </form>
                <?php if ($promo): ?>
                    <div style="margin-top:10px; color:var(--success); font-weight:600;">
                        Kod uygulandı: <?= htmlspecialchars($promo['code']) ?> (-<?= $promo['discount_percent'] ?>%)
                        <?php if($promo['free_delivery']): ?> + Ücretsiz Kargo! <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ÖZET -->
            <div class="summary-box">
                <div class="order-item"><span>Ara Toplam</span> <span><?= number_format($sub_total, 2) ?> ₺</span></div>
                <?php if($discount > 0): ?>
                <div class="order-item" style="color:var(--success);"><span>İndirim</span> <span>-<?= number_format($discount, 2) ?> ₺</span></div>
                <?php endif; ?>
                <div class="order-item"><span>Teslimat Ücreti</span> <span><?= $delivery_fee == 0 ? 'ÜCRETSİZ' : number_format($delivery_fee, 2).' ₺' ?></span></div>
                
                <div class="total-row">
                    Toplam: <?= number_format($final_total, 2) ?> ₺
                </div>
            </div>

            <!-- TESLİMAT ADRESİ -->
            <div class="address-box">
                <h3>Teslimat Adresi</h3>
                <?php if($default_address): ?>
                    <strong><?= htmlspecialchars($default_address['title']) ?></strong><br>
                    <?= nl2br(htmlspecialchars($default_address['address'])) ?><br>
                    <small>Telefon: <?= htmlspecialchars($default_address['phone'] ?? 'Belirtilmemiş') ?></small>
                <?php else: ?>
                    <p style="color:#d32f2f;">Adres eklenmemiş! <a href="profile.php#addresses">Buraya tıklayın</a></p>
                <?php endif; ?>
            </div>

            <!-- ÖDEME YÖNTEMİ -->
            <h3 class="section-title">Ödeme Yöntemi</h3>
            <div class="payment-options">
                <div class="pay-opt selected" data-method="kapida_nakit">Kapıda Nakit</div>
                <div class="pay-opt" data-method="kapida_pos">Kapıda Kart</div>
            </div>

            <button class="order-btn" onclick="placeOrder()">
                Siparişi Tamamla - <?= number_format($final_total, 2) ?> ₺ Öde
            </button>
        </div>

    <?php else: ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h2>Sepetiniz Boş!</h2>
            <p>Henüz ürün eklemediniz.</p>
            <a href="index.php" style="background:var(--gradient); color:white; padding:15px 30px; border-radius:50px; text-decoration:none; font-weight:600;">
                Alışverişe Başla
            </a>
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

document.querySelectorAll('.pay-opt').forEach(el => {
    el.addEventListener('click', function() {
        document.querySelectorAll('.pay-opt').forEach(e => e.classList.remove('selected'));
        this.classList.add('selected');
    });
});

async function placeOrder() {
    if (!confirm('Siparişiniz alınsın mı?')) return;

    const btn = document.querySelector('.order-btn');
    btn.disabled = true;
    btn.innerHTML = 'İşleniyor...';

    try {
        // Sepet verisini al
        const cartRes = await fetch('api/get_cart.php');
        const cartData = await cartRes.json();

        if (!cartData.success || cartData.items.length === 0) {
            alert('Sepetiniz boş!');
            return;
        }

        const paymentMethod = document.querySelector('.pay-opt.selected').dataset.method;

        // place_order.php'nin tam olarak beklediği formatta veri gönder
        const orderData = {
            items: cartData.items.map(item => ({
                id: item.menu_item_id,
                name: item.name,
                price: parseFloat(item.price),
                quantity: parseInt(item.quantity),
                restaurant_id: item.business_id || 1
            })),
            payment_method: paymentMethod,
            address: <?= $default_address ? json_encode($default_address['address']) : '""' ?>,
            note: ''
        };

        const response = await fetch('../place_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(orderData)
        });

        const result = await response.json();

        if (result.success) {
            alert('SİPARİŞİNİZ BAŞARIYLA ALINDI! #' + result.order_id);
            window.location.href = 'order_tracking.php?id=' + result.order_id;
        } else {
            alert('Hata: ' + result.message);
        }
    } catch (err) {
        console.error(err);
        alert('Bağlantı hatası! Lütfen tekrar deneyin.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Siparişi Tamamla - <?= number_format($final_total, 2) ?> ₺ Öde';
    }
}
</script>
</body>
</html>