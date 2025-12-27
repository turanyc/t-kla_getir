<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant') {
    header("Location: ../login.php"); exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id FROM restaurants WHERE user_id = ?");
$stmt->execute([$user_id]);
$rest = $stmt->fetch();
$restaurant_id = $rest['id'] ?? 0;

// waiting
$stmt_wait = $pdo->prepare("
   SELECT cpc.id, cpc.amount, cpc.created_at, u.name courier_name
   FROM courier_payment_confirm cpc
   JOIN couriers cr ON cpc.courier_id = cr.id
   JOIN users u ON cr.user_id = u.id
   WHERE cpc.restaurant_id = ? AND cpc.status = 'waiting'
   ORDER BY cpc.created_at DESC
");
$stmt_wait->execute([$restaurant_id]);
$wait = $stmt_wait->fetchAll();

// confirmed
$stmt_done = $pdo->prepare("
   SELECT cpc.amount, cpc.restaurant_confirmed_at, u.name courier_name
   FROM courier_payment_confirm cpc
   JOIN couriers cr ON cpc.courier_id = cr.id
   JOIN users u ON cr.user_id = u.id
   WHERE cpc.restaurant_id = ? AND cpc.status = 'confirmed'
   ORDER BY cpc.restaurant_confirmed_at DESC
   LIMIT 50
");
$stmt_done->execute([$restaurant_id]);
$done = $stmt_done->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kurye Ödeme Onayları - Tıkla Gelir</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FF6B35; 
            --primary-dark: #FF4500;
            --success: #00C853; 
            --danger: #F44336;
            --card-bg: #FFFFFF; 
            --light: #F8F9FA;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #F5F7FA;
            margin: 0;
            padding: 15px;
            overflow-x: hidden;
        }
        .top-bar {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: clamp(12px, 3vw, 15px) clamp(20px, 4vw, 30px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            flex-wrap: wrap;
            gap: 15px;
        }
        .logo {
            font-size: clamp(20px, 4vw, 24px);
            font-weight: 700;
            color: white;
        }
        .btn {
            background: white;
            color: var(--primary);
            padding: clamp(8px, 2vw, 10px) clamp(15px, 3vw, 20px);
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            font-size: clamp(12px, 2.5vw, 14px);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .container {
            max-width: 1200px;
            margin: 25px auto;
            padding: 0 clamp(15px, 3vw, 20px);
        }
        .card {
            background: var(--card-bg);
            padding: clamp(20px, 4vw, 25px);
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        h2 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: clamp(20px, 3vw, 24px);
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }
        th, td {
            padding: clamp(10px, 2vw, 12px);
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: clamp(12px, 2.5vw, 14px);
        }
        th {
            background: #FFF8F0;
            color: var(--primary);
            font-weight: 600;
        }
        .btn-success {
            background: var(--success);
            color: white;
            border: none;
            padding: clamp(8px, 1.5vw, 10px) clamp(14px, 2.5vw, 18px);
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            font-size: clamp(12px, 2.5vw, 14px);
            transition: all 0.3s;
        }
        .btn-success:hover {
            background: #009624;
            transform: translateY(-2px);
        }
        .empty {
            text-align: center;
            padding: clamp(30px, 6vw, 40px);
            color: #999;
            font-size: clamp(14px, 2.5vw, 16px);
        }
        
        @media (max-width: 768px) {
            .top-bar { flex-direction: column; align-items: flex-start; }
            .table-responsive { margin: 0 -15px; padding: 0 15px; }
        }
        
        @media (max-width: 480px) {
            .btn-success { width: 100%; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="logo"><i class="fas fa-utensils"></i> TIKLA GELİR</div>
        <a href="index.php" class="btn"><i class="fas fa-arrow-left"></i> Geri</a>
    </div>

    <div class="container">
        <!-- Bekleyen -->
        <div class="card">
            <h2><i class="fas fa-clock"></i> Onay Bekleyen Ödemeler</h2>
            <?php if (empty($wait)): ?>
                <div class="empty">Onay bekleyen ödeme yok.</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr><th>Kurye</th><th>Tutar</th><th>Tarih</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($wait as $w): ?>
                            <tr id="row-<?= $w['id'] ?>">
                                <td><?= htmlspecialchars($w['courier_name']) ?></td>
                                <td><?= number_format($w['amount'], 2) ?> ₺</td>
                                <td><?= date('d.m.Y H:i', strtotime($w['created_at'])) ?></td>
                                <td>
                                    <button class="btn-success" onclick="confirmPay(<?= $w['id'] ?>)">
                                        <i class="fas fa-check"></i> Ödemeyi Aldım
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Geçmiş -->
        <div class="card">
            <h2><i class="fas fa-history"></i> Geçmiş Ödemeler</h2>
            <?php if (empty($done)): ?>
                <div class="empty">Henüz onaylanmış ödeme yok.</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr><th>Kurye</th><th>Tutar</th><th>Onay Tarihi</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($done as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['courier_name']) ?></td>
                                <td><?= number_format($d['amount'], 2) ?> ₺</td>
                                <td><?= date('d.m.Y H:i', strtotime($d['restaurant_confirmed_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function confirmPay(id) {
            if (!confirm('Ödemeyi aldığınızı onaylıyor musunuz?')) return;
            fetch('../api/payment_confirm_restaurant.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + id
            })
            .then(r => r.text())
            .then(resp => {
                if (resp === 'OK') {
                    document.getElementById('row-' + id).remove();
                    alert('✅ Ödeme onaylandı!');
                } else {
                    alert('Hata: ' + resp);
                }
            });
        }
    </script>
</body>
</html>