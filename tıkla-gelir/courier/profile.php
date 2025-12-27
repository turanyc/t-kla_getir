<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Kurye bilgileri
$courier = $pdo->prepare("SELECT c.*, u.name, u.email, u.phone FROM couriers c JOIN users u ON c.user_id = u.id WHERE c.user_id = ?");
$courier->execute([$user_id]);
$courier = $courier->fetch();

// Toplam istatistikler
$stats = $pdo->prepare("SELECT 
    COUNT(*) as total_deliveries,
    COALESCE(SUM(total_price * 0.15), 0) as total_earnings
    FROM orders WHERE courier_id = ? AND status = 'teslim'");
$stats->execute([$courier['id']]);
$stats = $stats->fetch();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurye Profili - Kral Kurye</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 100%); 
            color: #fff; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 15px;
            overflow-x: hidden;
        }
        .profile-card {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 25px;
            padding: clamp(25px, 5vw, 40px);
            backdrop-filter: blur(20px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
            max-width: 600px;
            margin: clamp(30px, 8vw, 50px) auto;
        }
        .profile-card h2 {
            background: linear-gradient(45deg, #ff0066, #00C853);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            margin-bottom: 25px;
            font-size: clamp(24px, 4vw, 28px);
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: clamp(12px, 2.5vw, 15px) 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            flex-wrap: wrap;
            gap: 10px;
            font-size: clamp(14px, 2.5vw, 16px);
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-row span:first-child {
            flex: 1;
            min-width: 150px;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #ff0066, #e6005c);
            border: none;
            border-radius: 50px;
            padding: clamp(10px, 2vw, 12px) clamp(25px, 4vw, 30px);
            font-weight: 600;
            color: #fff;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: clamp(14px, 2.5vw, 16px);
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255,0,102,0.4);
        }
        .badge {
            font-size: clamp(12px, 2vw, 14px);
        }
        .text-success {
            color: #00C853 !important;
        }
        .text-danger {
            color: #f44336 !important;
        }
        
        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }
            .btn-primary-custom {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>

<div class="profile-card">
    <h2 class="text-center mb-4"><i class="bi bi-person-circle"></i> KURYEM PROFİLİ</h2>
    
    <div class="info-row">
        <span><i class="bi bi-person-fill"></i> Ad Soyad</span>
        <strong><?= htmlspecialchars($courier['name']) ?></strong>
    </div>
    
    <div class="info-row">
        <span><i class="bi bi-envelope-fill"></i> Email</span>
        <span><?= htmlspecialchars($courier['email']) ?></span>
    </div>
    
    <div class="info-row">
        <span><i class="bi bi-telephone-fill"></i> Telefon</span>
        <span><?= htmlspecialchars($courier['phone']) ?></span>
    </div>
    
    <div class="info-row">
        <span><i class="bi bi-bicycle"></i> Durum</span>
        <span class="badge bg-<?= $courier['is_active'] ? 'success' : 'danger' ?>">
            <?= $courier['is_active'] ? 'Aktif' : 'Pasif' ?>
        </span>
    </div>
    
    <div class="info-row">
        <span><i class="bi bi-currency-dollar"></i> Toplam Teslimat</span>
        <strong><?= $stats['total_deliveries'] ?> adet</strong>
    </div>
    
    <div class="info-row">
        <span><i class="bi bi-wallet2"></i> Kazanç</span>
        <strong><?= number_format($stats['total_earnings'], 2) ?> ₺</strong>
    </div>
    
    <div class="info-row">
        <span><i class="bi bi-piggy-bank"></i> Avans Bakiye</span>
        <strong class="<?= $courier['advance_balance'] > 0 ? 'text-danger' : 'text-success' ?>">
            <?= number_format($courier['advance_balance'], 2) ?> ₺
        </strong>
    </div>
    
    <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
    
    <div class="text-center">
        <a href="index.php" class="btn btn-primary-custom">
            <i class="bi bi-arrow-left-circle-fill me-2"></i> Panele Dön
        </a>
    </div>
</div>

</body>
</html>