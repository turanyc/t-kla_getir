<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = (int)($_GET['id'] ?? 0);

if ($order_id <= 0) {
    die("Geçersiz sipariş numarası");
}

// Sipariş bilgisini çek
$stmt = $pdo->prepare("
    SELECT o.*, b.name as business_name 
    FROM orders o 
    LEFT JOIN businesses b ON o.business_id = b.id 
    WHERE o.id = ? AND o.customer_id = ?
");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Sipariş bulunamadı veya yetkiniz yok");
}

// Sipariş ürünleri
$stmt = $pdo->prepare("
    SELECT oi.*, mi.name 
    FROM order_items oi 
    JOIN menu_items mi ON oi.menu_item_id = mi.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Takip #<?= $order_id ?> - Tıkla Gelir</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght:400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --primary: #FF6B35; --gradient: linear-gradient(135deg, #FF6B35, #FF4500); }
        body { 
            font-family: 'Poppins', sans-serif; 
            background:#f5f7fa; 
            padding:clamp(20px, 5vw, 40px) clamp(15px, 4vw, 20px); 
            text-align:center; 
            overflow-x: hidden;
        }
        .card { 
            max-width:700px; 
            margin:0 auto; 
            background:white; 
            border-radius:25px; 
            padding:clamp(25px, 5vw, 40px); 
            box-shadow:0 15px 40px rgba(0,0,0,0.1); 
            min-width: 0;
        }
        .success { 
            color:#00C853; 
            font-size:clamp(50px, 10vw, 80px); 
        }
        h1 { 
            font-size:clamp(24px, 5vw, 36px); 
            color:var(--primary); 
            margin:20px 0; 
        }
        .order-id { 
            font-size:clamp(18px, 4vw, 24px); 
            background:var(--gradient); 
            color:white; 
            padding:clamp(12px, 3vw, 15px) clamp(20px, 4vw, 30px); 
            border-radius:50px; 
            display:inline-block; 
        }
        .status { 
            font-size:clamp(16px, 3vw, 22px); 
            padding:clamp(12px, 3vw, 15px); 
            border-radius:15px; 
            margin:20px 0; 
        }
        .status.yeni { background:#FFF3E0; color:#FF6B35; }
        .items { 
            text-align:left; 
            margin:clamp(20px, 4vw, 30px) 0; 
        }
        .item { 
            padding:clamp(12px, 3vw, 15px); 
            border-bottom:1px solid #eee; 
            font-size: clamp(14px, 2.5vw, 16px);
        }
        .total { 
            font-size:clamp(20px, 4vw, 28px); 
            font-weight:700; 
            color:var(--primary); 
            margin:clamp(20px, 4vw, 30px) 0; 
        }
        a { 
            background:var(--gradient); 
            color:white; 
            padding:clamp(12px, 3vw, 15px) clamp(30px, 6vw, 40px); 
            border-radius:50px; 
            text-decoration:none; 
            font-weight:600; 
            display:inline-block; 
            margin-top:20px; 
            font-size: clamp(14px, 2.5vw, 16px);
        }
        
        @media (max-width: 480px) {
            body { padding: 15px 10px; }
            .card { padding: 20px; border-radius: 20px; }
            .item { 
                display: flex; 
                flex-direction: column; 
                gap: 5px; 
            }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>

<div class="card">
    <div class="success">
        <i class="fas fa-check-circle"></i>
    </div>
    <h1>Siparişiniz Alındı!</h1>
    <div class="order-id">Sipariş No: #<?= $order_id ?></div>
    
    <div class="status <?= $order['status'] ?>">
        Durum: <?= ucfirst($order['status']) ?>
    </div>

    <p>Restoran: <strong><?= htmlspecialchars($order['business_name'] ?? 'Bilinmiyor') ?></strong></p>
    <p>Teslimat Adresi: <strong><?= htmlspecialchars($order['address']) ?></strong></p>

    <div class="items">
        <h3>Sipariş İçeriği</h3>
        <?php foreach($items as $item): ?>
        <div class="item">
            <?= $item['quantity'] ?> × <?= htmlspecialchars($item['name']) ?> 
            <strong style="float:right;"><?= number_format($item['total_price'], 2) ?> ₺</strong>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="total">
        Toplam Ödenen: <?= number_format($order['total_price'], 2) ?> ₺
    </div>

    <p>Teşekkür ederiz! Siparişiniz hazırlanıyor...</p>
    <a href="index.php">Ana Sayfaya Dön</a>
</div>

</body>
</html>