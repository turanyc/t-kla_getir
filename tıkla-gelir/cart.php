<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepetim - Tıkla Gelir</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght:400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #FF4500;
            --gradient: linear-gradient(135deg, #FF6B35, #FF4500);
            --light: #f8f9fa;
            --dark: #333;
            --shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { 
            font-family: 'Poppins', sans-serif; 
            background:#f5f7fa; 
            color:var(--dark); 
            min-height:100vh; 
            padding-top:70px; 
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
            max-width:1200px; 
            margin:0 auto; 
            padding:15px; 
            display:grid; 
            grid-template-columns:1fr 380px; 
            gap:20px; 
        }
        .cart-items { background:white; border-radius:25px; padding:30px; box-shadow:var(--shadow); }
        .cart-item { display:flex; align-items:center; padding:20px 0; border-bottom:1px solid #eee; gap:20px; }
        .cart-item:last-child { border:none; }
        .item-img { width:80px; height:80px; background:var(--gradient); border-radius:15px; display:flex; align-items:center; justify-content:center; color:white; font-size:32px; }
        .item-info { flex:1; }
        .item-name { font-weight:700; font-size:18px; }
        .item-price { color:var(--primary); font-weight:600; font-size:17px; }
        .qty-controls { display:flex; align-items:center; gap:12px; }
        .qty-btn { width:38px; height:38px; border:none; background:var(--gradient); color:white; border-radius:50%; cursor:pointer; font-size:16px; }
        .qty { min-width:40px; text-align:center; font-weight:600; font-size:18px; }
        .delete-btn { background:#e74c3c; color:white; border:none; width:40px; height:40px; border-radius:50%; cursor:pointer; }

        .summary { background:var(--gradient); color:white; border-radius:25px; padding:30px; height:fit-content; position:sticky; top:110px; box-shadow:var(--shadow); }
        .summary h3 { margin-bottom:20px; font-size:22px; }
        .sum-row { display:flex; justify-content:space-between; margin:15px 0; font-size:17px; }
        .total-row { font-size:24px; font-weight:700; padding-top:20px; border-top:2px solid rgba(255,255,255,0.4); margin-top:20px; }

        .checkout-btn { width:100%; padding:18px; background:white; color:var(--primary-dark); border:none; border-radius:50px; font-size:20px; font-weight:700; cursor:pointer; margin-top:25px; box-shadow:0 8px 20px rgba(0,0,0,0.2); }
        .checkout-btn:hover { transform:translateY(-3px); }

        .empty { text-align:center; padding:100px 20px; background:white; border-radius:25px; box-shadow:var(--shadow); }
        .empty i { font-size:90px; color:#ddd; margin-bottom:20px; }

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
            padding: 80px 0 20px 0;
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
        
        @media (max-width:992px) { 
            .container { 
                grid-template-columns:1fr; 
                gap: 20px;
            } 
            .summary { 
                position:static; 
            } 
        }
        
        @media (max-width:768px) {
            body { padding-top: 60px; }
            .header { padding: 10px 12px; }
            .hamburger-menu { display: block; }
            .container { 
                padding: 12px; 
                gap: 15px;
            }
            .cart-items, .summary { 
                padding: 20px; 
            }
            .item-img { 
                width: 60px; 
                height: 60px; 
                font-size: 24px; 
            }
            .item-name { 
                font-size: clamp(14px, 3vw, 18px); 
            }
            .item-price { 
                font-size: clamp(14px, 3vw, 17px); 
            }
            .qty-btn { 
                width: 32px; 
                height: 32px; 
                font-size: 14px; 
            }
            .summary h3 { 
                font-size: clamp(18px, 4vw, 22px); 
            }
            .sum-row { 
                font-size: clamp(14px, 3vw, 17px); 
            }
            .total-row { 
                font-size: clamp(18px, 4vw, 24px); 
            }
            .checkout-btn { 
                padding: 14px; 
                font-size: clamp(16px, 3.5vw, 20px); 
            }
        }
        
        @media (max-width:480px) {
            .header { flex-direction: column; align-items: flex-start; }
            .cart-item { 
                flex-direction: column; 
                align-items: flex-start; 
                gap: 10px; 
            }
            .qty-controls { 
                width: 100%; 
                justify-content: space-between; 
            }
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
    <div><strong><?= htmlspecialchars($user_name) ?></strong> - Sepetim</div>
</div>

<!-- MOBILE MENU -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
<nav class="mobile-menu" id="mobileMenu">
    <a href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a>
    <?php if($isLoggedIn): ?>
        <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'business'): ?>
            <a href="business/index.php"><i class="fas fa-store"></i> İşletme Paneli</a>
        <?php endif; ?>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="admin/index.php"><i class="fas fa-crown"></i> Admin Paneli</a>
        <?php endif; ?>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'courier'): ?>
            <a href="courier/index.php"><i class="fas fa-bicycle"></i> Kurye Paneli</a>
        <?php endif; ?>
        <button id="mobileCartButton"><i class="fas fa-shopping-cart"></i> Sepet</button>
        <button onclick="if(confirm('Çıkış yapılsın mı?')) window.location='logout.php'"><i class="fas fa-sign-out-alt"></i> Çıkış</button>
    <?php else: ?>
        <a href="login.php"><i class="fas fa-sign-in-alt"></i> Giriş Yap</a>
    <?php endif; ?>
</nav>

<div class="container" id="cartApp">
    <!-- İçerik buraya AJAX ile gelecek -->
</div>

<script>
async function loadCart() {
    try {
        const res = await fetch('api/get_cart.php');
        const data = await res.json();

        const app = document.getElementById('cartApp');

        if (!data.success || data.items.length === 0) {
            app.innerHTML = `
                <div class="empty">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>Sepetiniz Boş!</h2>
                    <p>Henüz ürün eklemediniz.</p>
                    <a href="index.php" style="background:var(--gradient); color:white; padding:15px 35px; border-radius:50px; text-decoration:none; font-weight:600;">
                        Alışverişe Başla
                    </a>
                </div>
            `;
            return;
        }

        let itemsHTML = '';
        let total = 0;

        data.items.forEach(item => {
            const lineTotal = item.price * item.quantity;
            total += lineTotal;

            itemsHTML += `
                <div class="cart-item">
                    <div class="item-img"><i class="fas fa-utensils"></i></div>
                    <div class="item-info">
                        <div class="item-name">${item.name}</div>
                        <div class="item-price">${item.price} ₺</div>
                    </div>
                    <div class="qty-controls">
                        <button class="qty-btn" onclick="updateQty(${item.menu_item_id}, ${item.quantity-1})" ${item.quantity<=1?'disabled':''}>-</button>
                        <div class="qty">${item.quantity}</div>
                        <button class="qty-btn" onclick="updateQty(${item.menu_item_id}, ${item.quantity+1})">+</button>
                    </div>
                    <button class="delete-btn" onclick="removeItem(${item.menu_item_id})"><i class="fas fa-trash"></i></button>
                </div>
            `;
        });

        app.innerHTML = `
            <div class="cart-items">
                <h2 style="color:var(--primary); margin-bottom:25px;">Sepetim (${data.items.length} ürün)</h2>
                ${itemsHTML}
            </div>
            <div class="summary">
                <h3>Sipariş Özeti</h3>
                <div class="sum-row"><span>Ara Toplam</span> <span>${total.toFixed(2)} ₺</span></div>
                <div class="sum-row"><span>Teslimat</span> <span>15.00 ₺</span></div>
                <div class="total-row"><span>Toplam</span> <span>${(total + 15).toFixed(2)} ₺</span></div>
                <button class="checkout-btn" onclick="window.location='checkout.php'">ÖDEMEYE GEÇ</button>
            </div>
        `;

    } catch (err) {
        document.getElementById('cartApp').innerHTML = '<div class="empty"><h2>Sepet yüklenemedi!</h2></div>';
    }
}

async function updateQty(menu_item_id, qty) {
    if (qty < 1) return removeItem(menu_item_id);
    await fetch('api/update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `menu_item_id=${menu_item_id}&quantity=${qty}`
    });
    loadCart();
}

async function removeItem(menu_item_id) {
    if (!confirm('Bu ürünü sepetten kaldırmak istiyor musunuz?')) return;
    await fetch('api/update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `menu_item_id=${menu_item_id}&quantity=0`
    });
    loadCart();
}

document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    
    // Mobile cart button
    const mobileCartBtn = document.getElementById('mobileCartButton');
    if (mobileCartBtn) {
        mobileCartBtn.addEventListener('click', function() {
            closeMobileMenu();
            // Zaten cart.php sayfasındayız, sadece menüyü kapat
        });
    }
});

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