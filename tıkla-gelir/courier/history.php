<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Kurye ID'sini al
$courier = $pdo->prepare("SELECT id FROM couriers WHERE user_id = ?");
$courier->execute([$user_id]);
$courier = $courier->fetch();

// Teslim edilmiş tüm siparişleri al
$history = $pdo->prepare("SELECT o.*, r.name as restaurant_name, u.name as customer_name,
                          o.total_price * 0.15 as courier_earnings
                          FROM orders o 
                          JOIN restaurants r ON o.restaurant_id = r.id 
                          JOIN users u ON o.customer_id = u.id 
                          WHERE o.courier_id = ? AND o.status = 'teslim' 
                          ORDER BY o.created_at DESC");
$history->execute([$courier['id']]);
$history = $history->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teslimat Geçmişi - Kral Kurye</title>
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
        .history-card {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 25px;
            padding: clamp(25px, 5vw, 40px);
            backdrop-filter: blur(20px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
            max-width: 1200px;
            margin: clamp(30px, 8vw, 50px) auto;
        }
        .history-card h2 {
            background: linear-gradient(45deg, #FFD700, #FFA000);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            margin-bottom: 25px;
            font-size: clamp(24px, 4vw, 28px);
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table {
            background: rgba(0,0,0,0.2);
            border-radius: 20px;
            overflow: hidden;
            min-width: 800px;
        }
        .table th {
            background: linear-gradient(135deg, #ff0066, #00C853);
            border: none;
            padding: clamp(15px, 3vw, 20px);
            font-size: clamp(12px, 2vw, 14px);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table td {
            border-color: rgba(255,255,255,0.08);
            padding: clamp(15px, 3vw, 20px);
            vertical-align: middle;
            transition: background 0.3s;
            font-size: clamp(12px, 2.5vw, 14px);
        }
        .table tr:hover {
            background: rgba(255,255,255,0.05);
        }
        .badge {
            font-size: clamp(11px, 2vw, 0.85em);
            padding: clamp(6px, 1.5vw, 8px) clamp(12px, 2.5vw, 15px);
            border-radius: 50px;
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
        .text-center {
            text-align: center;
        }
        .py-5 {
            padding: 40px 0;
        }
        .mt-3 {
            margin-top: 15px;
        }
        .my-4 {
            margin: 25px 0;
        }
        .text-success {
            color: #00C853 !important;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                margin: 0 -15px;
                padding: 0 15px;
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

<div class="history-card">
    <h2 class="text-center mb-4"><i class="bi bi-clock-history"></i> TESLİMAT GEÇMİŞİN</h2>
    
    <?php if (empty($history)): ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 60px; opacity: 0.3;"></i>
            <p class="mt-3" style="font-size: 18px; opacity: 0.7;">Henüz tamamlanmış teslimatın bulunmuyor.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Müşteri</th>
                        <th>Restoran</th>
                        <th>Tutar</th>
                        <th>Kazanç</th>
                        <th>Tarih</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($history as $row): ?>
                    <tr>
                        <td><strong>#<?= $row['id'] ?></strong></td>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td><?= htmlspecialchars($row['restaurant_name']) ?></td>
                        <td><?= number_format($row['total_price'], 2) ?> ₺</td>
                        <td class="text-success"><?= number_format($row['courier_earnings'], 2) ?> ₺</td>
                        <td><?= date('d.m.Y H:i', strtotime($row['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
    <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
    
    <div class="text-center">
        <a href="index.php" class="btn btn-primary-custom">
            <i class="bi bi-arrow-left-circle-fill me-2"></i> Panele Dön
        </a>
    </div>
</div>

</body>
</html>