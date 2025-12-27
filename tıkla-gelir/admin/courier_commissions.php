<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit;
}

$liste = $pdo->query("
   SELECT c.id, u.name, u.phone, c.is_active, c.commission_rate
   FROM couriers c
   JOIN users u ON c.user_id = u.id
   ORDER BY u.name
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurye Komisyon Oranları</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #FF6B35;
            --success: #00C853;
            --card-bg: #FFFFFF;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #F5F7FA;
            margin: 0;
            padding: 15px;
            overflow-x: hidden;
        }
        .top-bar {
            background: var(--card-bg);
            padding: clamp(12px, 3vw, 15px) clamp(20px, 4vw, 30px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            flex-wrap: wrap;
            gap: 15px;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: clamp(18px, 4vw, 24px);
            font-weight: 700;
            color: var(--primary);
        }
        .nav-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .nav-buttons a {
            color: #333;
            text-decoration: none;
            padding: clamp(8px, 2vw, 10px) clamp(15px, 3vw, 18px);
            border-radius: 50px;
            background: #F8F9FA;
            transition: .3s;
            font-size: clamp(12px, 2.5vw, 14px);
        }
        .nav-buttons a:hover {
            background: var(--primary);
            color: #fff;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 0 15px;
        }
        .card {
            background: var(--card-bg);
            padding: clamp(20px, 4vw, 30px);
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        h2 {
            font-size: clamp(20px, 4vw, 24px);
            margin-bottom: 20px;
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
            padding: clamp(8px, 2vw, 12px);
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: clamp(12px, 2.5vw, 14px);
        }
        th {
            background: #FFF8F0;
            color: var(--primary);
            font-weight: 600;
        }
        .inline-edit {
            border: 1px solid var(--primary);
            border-radius: 50px;
            padding: clamp(4px, 1vw, 6px) clamp(8px, 2vw, 10px);
            width: clamp(60px, 10vw, 70px);
            text-align: center;
            font-size: clamp(12px, 2.5vw, 14px);
        }
        .btn-save {
            background: var(--success);
            color: white;
            border: none;
            padding: clamp(6px, 1.5vw, 8px) clamp(12px, 2.5vw, 15px);
            border-radius: 50px;
            cursor: pointer;
            font-size: clamp(11px, 2vw, 12px);
            transition: all 0.3s;
        }
        .btn-save:hover {
            background: #009624;
            transform: scale(1.05);
        }
        .updated {
            background-color: #d4edda !important;
            transition: background-color 0.5s;
        }
        
        .hamburger-menu { 
            display: none; 
            background: rgba(255,107,53,0.2); 
            color: var(--primary); 
            border: none; 
            padding: 10px 15px; 
            border-radius: 10px; 
            font-size: 24px; 
            cursor: pointer; 
            z-index: 1001;
            margin-bottom: 15px;
        }
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
            padding: 100px 0 20px 0;
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
        .mobile-menu a { 
            display: block !important; 
            width: 100%; 
            padding: 15px 20px; 
            background: linear-gradient(135deg, var(--primary), #FF4500) !important; 
            color: white !important; 
            border: none; 
            border-radius: 0; 
            text-align: left; 
            text-decoration: none; 
            font-weight: 600; 
            margin-bottom: 5px; 
            font-size: 14px;
        }
        .mobile-menu a:hover {
            opacity: 0.9;
        }
        .mobile-menu a i {
            margin-right: 10px;
            width: 20px;
            display: inline-block;
        }
        @media (max-width: 768px) {
            body { padding: 10px; }
            .hamburger-menu { display: block; }
            .top-bar {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
            .nav-buttons {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
            .card { padding: 15px; }
            table { font-size: 12px; }
            .inline-edit { width: 60px; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>
    <button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMobileMenu()">
        <i class="fas fa-bars"></i>
    </button>
    
    <div class="top-bar">
        <div class="logo"><i class="fas fa-percent"></i> Kurye Komisyonları</div>
        <div class="nav-buttons">
            <a href="index.php"><i class="fas fa-tachometer-alt"></i> Ana Sayfa</a>
            <a href="live_couriers_map.php"><i class="fas fa-map-marked-alt"></i> Canlı Harita</a>
        </div>
    </div>
    
    <!-- MOBILE MENU -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
    <nav class="mobile-menu" id="mobileMenu">
        <a href="index.php"><i class="bi bi-speedometer2"></i> Anasayfa</a>
        <a href="all_orders.php"><i class="bi bi-list-ul"></i> Siparişler</a>
        <a href="courier_advances.php"><i class="bi bi-cash-stack"></i> Kurye Finans</a>
        <a href="restaurant_payments.php"><i class="bi bi-bank"></i> Restoran Ödemeleri</a>
        <a href="reports.php"><i class="bi bi-graph-up"></i> Raporlar</a>
        <a href="promotions.php"><i class="bi bi-tag-fill"></i> Promosyonlar</a>
        <a href="vendor_management.php"><i class="bi bi-shop"></i> Vendor Yönetimi</a>
        <a href="category_management.php"><i class="bi bi-tags"></i> Kategoriler</a>
        <a href="admin_management.php"><i class="bi bi-people-fill"></i> Admin Yönetimi</a>
        <a href="live_couriers_map.php"><i class="bi bi-geo-alt-fill"></i> Canlı Harita</a>
        <a href="settings.php"><i class="bi bi-gear-fill"></i> Ayarlar</a>
        <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Güvenli Çıkış</a>
    </nav>

    <div class="container">
        <div class="card">
            <h2>Komisyon Oranlarını Düzenle</h2>
            <table>
                <thead>
                    <tr><th>Kurye</th><th>Telefon</th><th>Durum</th><th>Oran (%)</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($liste as $l): ?>
                        <tr id="row-<?= $l['id'] ?>">
                            <td><?= htmlspecialchars($l['name']) ?></td>
                            <td><?= htmlspecialchars($l['phone']) ?></td>
                            <td><?= $l['is_active'] ? 'Aktif' : 'Pasif' ?></td>
                            <td>
                                <input type="number" class="inline-edit" id="rate-<?= $l['id'] ?>"
                                       value="<?= $l['commission_rate'] ?>" min="0" max="100" step="0.01"
                                       onkeypress="if(event.key==='Enter') saveRate(<?= $l['id'] ?>)">
                            </td>
                            <td>
                                <button class="btn-save" onclick="saveRate(<?= $l['id'] ?>)">
                                    <i class="fas fa-save"></i> Kaydet
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function saveRate(id) {
            const rate = document.getElementById('rate-' + id).value;
            fetch('courier_commission_save.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + id + '&rate=' + rate
            })
            .then(r => r.text())
            .then(resp => {
                const row = document.getElementById('row-' + id);
                if (resp === 'OK') {
                    row.classList.add('updated');
                    setTimeout(() => row.classList.remove('updated'), 1000);
                } else {
                    alert('❌ Hata: ' + resp);
                }
            });
        }
        
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