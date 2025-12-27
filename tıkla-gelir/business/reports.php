<?php
session_start();
require_once "../config/database.php";
require_once "auth.php";

// Tarih filtresi (aynı)
$filter = $_GET['filter'] ?? 'monthly';
$date_from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$date_to   = $_GET['to']   ?? date('Y-m-d');

switch ($filter) {
    case 'today': $date_from = $date_to = date('Y-m-d'); break;
    case 'weekly': $date_from = date('Y-m-d', strtotime('-7 days')); break;
    case 'yearly': $date_from = date('Y-m-d', strtotime('-1 year')); break;
}

// Siparişler
$orders_stmt = $pdo->prepare("
    SELECT o.*, u.name AS customer_name, u.phone,
           CASE o.payment_method 
               WHEN 'online' THEN 'Online POS'
               WHEN 'kapida_pos' THEN 'Kapıda POS'
               ELSE 'Kapıda Nakit'
           END AS payment_type
    FROM orders o
    JOIN users u ON o.customer_id = u.id
    WHERE o.business_id = ? AND o.created_at BETWEEN ? AND ?
    ORDER BY o.created_at DESC
");
$orders_stmt->execute([BUSINESS_ID, $date_from, $date_to . ' 23:59:59']);
$orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);

// İstatistikler
$stats_stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'teslim' THEN 1 ELSE 0 END) as delivered,
        SUM(CASE WHEN payment_method = 'online' THEN 1 ELSE 0 END) as online,
        SUM(CASE WHEN payment_method = 'kapida_pos' THEN 1 ELSE 0 END) as pos,
        SUM(CASE WHEN payment_method = 'kapida_nakit' THEN 1 ELSE 0 END) as cash,
        SUM(total_price) as revenue
    FROM orders 
    WHERE business_id = ? AND created_at BETWEEN ? AND ?
");
$stats_stmt->execute([BUSINESS_ID, $date_from, $date_to . ' 23:59:59']);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// CSRF
if (!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Raporlar - <?= htmlspecialchars(BUSINESS_NAME) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF6B35; --primary-dark: #FF4500; --success: #00C853; --danger: #F44336;
            --light: #F8F9FA; --dark: #333333; --card-bg: #FFFFFF; --border-color: #E0E0E0;
            --shadow: 0 4px 12px rgba(0,0,0,0.08); --shadow-hover: 0 8px 25px rgba(0,0,0,0.15);
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--light); color: var(--dark); min-height: 100vh; overflow-x: hidden; }
        .top-bar { background: var(--card-bg); padding: clamp(12px, 3vw, 15px) clamp(20px, 4vw, 30px); display: flex; justify-content: space-between; align-items: center; box-shadow: var(--shadow); position: sticky; top: 0; z-index: 1000; flex-wrap: wrap; gap: 15px; }
        .logo { font-size: clamp(20px, 4vw, 24px); font-weight: 700; color: var(--primary); }
        .nav-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
        .nav-buttons a { color: var(--dark); text-decoration: none; padding: clamp(8px, 2vw, 10px) clamp(15px, 3vw, 18px); border-radius: 50px; transition: all 0.3s; font-weight: 500; background: var(--light); font-size: clamp(12px, 2.5vw, 14px); }
        .nav-buttons a:hover { background: var(--primary); color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255,107,53,0.3); }
        .container { max-width: 1200px; margin: 0 auto; padding: clamp(20px, 4vw, 30px); }
        .header-card { background: var(--card-bg); padding: clamp(25px, 5vw, 40px); border-radius: 25px; margin-bottom: 25px; box-shadow: var(--shadow); }
        .header-card h1 { font-size: clamp(24px, 4vw, 28px); }
        .header-card p { font-size: clamp(14px, 2.5vw, 16px); }
        .card { background: var(--card-bg); padding: clamp(20px, 4vw, 30px); border-radius: 20px; box-shadow: var(--shadow); border: 1px solid var(--border-color); margin-bottom: 25px; }
        .card h2 { color: var(--primary); margin-bottom: 20px; font-size: clamp(20px, 3vw, 24px); padding-bottom: 10px; border-bottom: 2px solid #FFE0B2; }
        .filter-form { display: flex; flex-wrap: wrap; gap: 15px; align-items: center; }
        select, input[type="date"] { padding: clamp(10px, 2vw, 12px) clamp(15px, 3vw, 18px); border: 1px solid var(--border-color); border-radius: 50px; font-size: clamp(14px, 2.5vw, 15px); transition: all 0.3s; }
        select:focus, input[type="date"]:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.2); }
        .btn { padding: clamp(10px, 2vw, 12px) clamp(20px, 3vw, 24px); border: none; border-radius: 50px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; color: white; font-size: clamp(12px, 2.5vw, 14px); }
        .btn-primary { background: var(--gradient); }
        .btn-success { background: var(--success); }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(clamp(200px, 25vw, 250px), 1fr)); gap: clamp(20px, 3vw, 25px); margin-bottom: 25px; }
        .stat-card { background: var(--card-bg); padding: clamp(20px, 4vw, 30px); border-radius: 20px; text-align: center; border: 1px solid var(--border-color); transition: all 0.4s; }
        .stat-card:hover { transform: translateY(-10px); border-color: var(--primary); box-shadow: var(--shadow-hover); }
        .stat-card i { font-size: clamp(24px, 4vw, 30px); }
        .stat-card h2 { color: var(--primary); font-size: clamp(32px, 5vw, 42px); margin: 15px 0; border: none; }
        .stat-card p { font-size: clamp(14px, 2.5vw, 16px); }
        .table-container { max-height: 600px; overflow-y: auto; border-radius: 20px; border: 1px solid var(--border-color); }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        table { width: 100%; background: var(--card-bg); border-collapse: collapse; min-width: 900px; }
        th, td { padding: clamp(12px, 2.5vw, 15px); text-align: left; border-bottom: 1px solid var(--border-color); font-size: clamp(12px, 2.5vw, 14px); }
        th { background: var(--gradient); color: white; font-weight: 600; position: sticky; top: 0; z-index: 10; }
        .no-data { text-align: center; padding: clamp(50px, 10vw, 80px) 20px; color: var(--primary); background: #FFF3E0; border-radius: 20px; border: 2px solid #FFE0B2; }
        .no-data h3 { font-size: clamp(18px, 3vw, 22px); }
        
        @media (max-width: 768px) {
            .top-bar { flex-direction: column; align-items: flex-start; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .filter-form { flex-direction: column; align-items: stretch; }
            select, input[type="date"], .btn { width: 100%; }
            .table-responsive { margin: 0 -20px; padding: 0 20px; }
        }
        
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>
<div class="container">
    <div class="top-bar">
        <div class="logo"><i class="fas fa-chart-line"></i> Raporlar - <?= htmlspecialchars(BUSINESS_NAME) ?></div>
        <div class="nav-buttons">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Geri Dön</a>
        </div>
    </div>

    <div class="header-card">
        <h1><i class="fas fa-chart-bar"></i> Raporlar</h1>
        <p>Sipariş performansınızı ve gelirlerinizi buradan takip edebilirsiniz.</p>
    </div>

    <!-- Filtreleme -->
    <div class="card">
        <h2><i class="fas fa-filter"></i> Tarih Aralığı</h2>
        <form method="GET" class="filter-form">
            <select name="filter" onchange="this.form.submit()">
                <option value="today" <?= $filter=='today'?'selected':'' ?>>📅 Bugün</option>
                <option value="weekly" <?= $filter=='weekly'?'selected':'' ?>>📊 Son 7 Gün</option>
                <option value="monthly" <?= $filter=='monthly'?'selected':'' ?>>📈 Son 30 Gün</option>
                <option value="yearly" <?= $filter=='yearly'?'selected':'' ?>>🗓️ Son 1 Yıl</option>
                <option value="custom" <?= $filter=='custom'?'selected':'' ?>>⚙️ Özel Tarih</option>
            </select>

            <?php if($filter == 'custom'): ?>
                <input type="date" name="from" value="<?= htmlspecialchars($date_from) ?>">
                <input type="date" name="to" value="<?= htmlspecialchars($date_to) ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrele</button>
            <?php endif; ?>

            <!-- ✅ EXCEL ÇIKTI BUTONU (PHAR) -->
            <a href="../excel.php?filter=<?= htmlspecialchars($filter) ?>&from=<?= htmlspecialchars($date_from) ?>&to=<?= htmlspecialchars($date_to) ?>&token=<?= htmlspecialchars($token) ?>" class="btn btn-success">
    <i class="fas fa-file-excel"></i> Excel İndir (PhpSpreadsheet-5.3.0)
</a>
        </form>
    </div>

    <!-- İstatistik Kartları -->
    <div class="stats-grid">
        <div class="stat-card"><i class="fas fa-shopping-basket" style="font-size: 30px; opacity: 0.7;"></i><h2><?= $stats['total'] ?? 0 ?></h2><p>Toplam Sipariş</p></div>
        <div class="stat-card"><i class="fas fa-check-circle" style="font-size: 30px; opacity: 0.7; color: var(--success);"></i><h2><?= $stats['delivered'] ?? 0 ?></h2><p>Teslim Edilen</p></div>
        <div class="stat-card"><i class="fas fa-turkish-lira-sign" style="font-size: 30px; opacity: 0.7; color: var(--primary);"></i><h2><?= number_format($stats['revenue'] ?? 0, 2) ?></h2><p>Toplam Ciro</p></div>
        <div class="stat-card"><i class="fas fa-credit-card" style="font-size: 30px; opacity: 0.7; color: #4CAF50;"></i><h2><?= $stats['online'] ?? 0 ?></h2><p>Online POS</p></div>
        <div class="stat-card"><i class="fas fa-mobile-alt" style="font-size: 30px; opacity: 0.7; color: #2196F3;"></i><h2><?= $stats['pos'] ?? 0 ?></h2><p>Kapıda POS</p></div>
        <div class="stat-card"><i class="fas fa-money-bill-wave" style="font-size: 30px; opacity: 0.7; color: #FFC107;"></i><h2><?= $stats['cash'] ?? 0 ?></h2><p>Kapıda Nakit</p></div>
    </div>

    <!-- Sipariş Detayları -->
    <div class="card">
        <h2><i class="fas fa-list"></i> Sipariş Detayları (<?= count($orders) ?>)</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th><th><i class="fas fa-user"></i> Müşteri</th><th><i class="fas fa-phone"></i> Telefon</th>
                        <th><i class="fas fa-turkish-lira-sign"></i> Tutar</th><th><i class="fas fa-credit-card"></i> Ödeme</th>
                        <th><i class="fas fa-info-circle"></i> Durum</th><th><i class="fas fa-calendar"></i> Tarih</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($orders)): ?>
                        <tr><td colspan="7" class="no-data"><i class="fas fa-inbox"></i><h3>Bu tarih aralığında sipariş bulunamadı</h3></td></tr>
                    <?php else: ?>
                        <?php foreach($orders as $o):
                            $badge = match($o['status']) {
                                'yeni' => '<span style="color: orange;">🆕 Yeni</span>',
                                'hazirlaniyor' => '<span style="color: blue;">👨‍🍳 Hazırlanıyor</span>',
                                'yolda' => '<span style="color: purple;">🚚 Yolda</span>',
                                'teslim' => '<span style="color: var(--success);">✅ Teslim</span>',
                                'iptal' => '<span style="color: var(--danger);">❌ İptal</span>',
                                default => $o['status']
                            };
                        ?>
                            <tr>
                                <td><strong>#<?= $o['id'] ?></strong></td>
                                <td><?= htmlspecialchars($o['customer_name']) ?></td>
                                <td><?= htmlspecialchars($o['phone']) ?></td>
                                <td><strong><?= number_format($o['total_price'], 2) ?> ₺</strong></td>
                                <td><?= htmlspecialchars($o['payment_type']) ?></td>
                                <td><?= $badge ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>