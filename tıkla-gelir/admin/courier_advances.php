<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$courier_id = $_GET['id'] ?? null;

// Finans işlemi ekle
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_finance'])) {
    $courier_id = $_POST['courier_id'];
    $amount = floatval($_POST['amount']);
    $type = $_POST['type']; // avans veya borc
    $description = $_POST['description'];
    $created_by = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        // Mevcut bakiyeyi al
        $stmt = $pdo->prepare("SELECT advance_balance FROM couriers WHERE id = ?");
        $stmt->execute([$courier_id]);
        $oldBalance = (float) $stmt->fetchColumn();

        // Türü çevir
        $transactionType = ($type === 'borc') ? 'withdrawal' : 'earning';

        // Yeni bakiye
        $newBalance = $oldBalance + ($type === 'borc' ? $amount : -$amount);

        // Finans tablosuna kayıt
        $stmt = $pdo->prepare("INSERT INTO courier_finances 
                              (courier_id, transaction_type, amount, balance_before, balance_after, description, created_by, status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, 'completed')");
        $stmt->execute([$courier_id, $transactionType, $amount, $oldBalance, $newBalance, $description, $created_by]);

        // Kurye bakiyesini güncelle
        $pdo->prepare("UPDATE couriers SET advance_balance = ? WHERE id = ?")
            ->execute([$newBalance, $courier_id]);

        $pdo->commit();
        $_SESSION['success'] = "İşlem başarıyla kaydedildi!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Hata: " . $e->getMessage();
    }
    header("Location: courier_advances.php?id=" . $courier_id);
    exit;
}

// Finans işlemi sil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_finance'])) {
    $finance_id = $_POST['finance_id'];
    $courier_id = $_POST['courier_id'];

    try {
        $pdo->beginTransaction();

        // İşlem detaylarını al
        $stmt = $pdo->prepare("SELECT transaction_type, amount, balance_before, balance_after FROM courier_finances WHERE id = ?");
        $stmt->execute([$finance_id]);
        $finance = $stmt->fetch();

        if ($finance) {
            // İşlemi sil
            $pdo->prepare("DELETE FROM courier_finances WHERE id = ?")->execute([$finance_id]);

            // Kurye bakiyesini **tersine** güncelle
            $reverseAmount = ($finance['transaction_type'] === 'withdrawal') ? -$finance['amount'] : $finance['amount'];
            $newBalance = $finance['balance_before'] + $reverseAmount;

            $pdo->prepare("UPDATE couriers SET advance_balance = ? WHERE id = ?")
                ->execute([$newBalance, $courier_id]);

            $pdo->commit();
            $_SESSION['success'] = "İşlem başarıyla silindi!";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Hata: " . $e->getMessage();
    }
    header("Location: courier_advances.php?id=" . $courier_id);
    exit;
}

// Kurye listesi
$couriers = $pdo->query("SELECT c.id, u.name, c.advance_balance, u.phone FROM couriers c JOIN users u ON c.user_id = u.id ORDER BY u.name")->fetchAll();

// Seçili kurye detayı
$selected = null;
$history = [];
if ($courier_id) {
    $stmt = $pdo->prepare("SELECT c.id, u.name, u.phone, c.advance_balance FROM couriers c JOIN users u ON c.user_id = u.id WHERE c.id = ?");
    $stmt->execute([$courier_id]);
    $selected = $stmt->fetch();

    $history = $pdo->prepare("SELECT id, transaction_type, amount, description, created_at FROM courier_finances WHERE courier_id = ? ORDER BY created_at DESC");
    $history->execute([$courier_id]);
    $history = $history->fetchAll();
}

function formatTL($amount) {
    return number_format($amount, 2, ',', '.') . ' ₺';
}
function formatDate($date) {
    return date('d.m.Y H:i', strtotime($date));
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kurye Finans Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #FF4500;
            --secondary: #00C853;
            --light: #F8F9FA;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #F5F7FA; color: #333; font-family: 'Poppins', sans-serif; padding: 15px; overflow-x: hidden; }
        .navbar { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); padding: clamp(12px, 3vw, 15px); margin-bottom: 25px; border-radius: 15px; box-shadow: 0 5px 20px rgba(255,107,53,0.3); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .navbar-brand { font-weight: 800; font-size: clamp(20px, 4vw, 24px); color: #fff !important; }
        .nav-link { color: rgba(255,255,255,0.9) !important; font-weight: 600; margin: 5px; font-size: clamp(12px, 2.5vw, 14px); }
        .nav-link:hover, .nav-link.active { color: #fff !important; }
        .main-content { max-width: 1400px; margin: 0 auto; }
        .page-title { font-size: clamp(24px, 4vw, 32px); font-weight: 700; margin-bottom: 25px; text-align: center; background: linear-gradient(45deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .section { background: #fff; border: 1px solid #E0E0E0; border-radius: 20px; padding: clamp(20px, 4vw, 30px); margin: 25px 0; box-shadow: 0 5px 20px rgba(0,0,0,0.08); }
        .list-group-item { background: #f8f9fa; border-color: #E0E0E0; font-size: clamp(14px, 2.5vw, 16px); }
        .list-group-item:hover { background: #FFF8F0; }
        .btn-primary-custom { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border: none; border-radius: 50px; padding: clamp(10px, 2vw, 12px) clamp(25px, 4vw, 30px); font-weight: 600; color: #fff; font-size: clamp(12px, 2.5vw, 14px); }
        .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,107,53,0.4); }
        .btn-logout { background: white; color: var(--primary); border: none; padding: clamp(8px, 2vw, 10px) clamp(15px, 3vw, 20px); border-radius: 50px; font-weight: 600; font-size: clamp(12px, 2.5vw, 14px); }
        .alert-info { background-color: #FFF8F0; border-color: var(--primary); color: #333; font-size: clamp(14px, 2.5vw, 16px); }
        .form-control, .form-select { border-radius: 10px; border: 1px solid #E0E0E0; font-size: clamp(14px, 2.5vw, 16px); }
        .table { background: #fff; border-radius: 15px; overflow: hidden; }
        .table th { background: #FFF8F0; border: none; padding: clamp(12px, 3vw, 18px); color: var(--primary); font-size: clamp(12px, 2.5vw, 14px); }
        .table td { border-color: #E0E0E0; padding: clamp(10px, 2.5vw, 15px); font-size: clamp(12px, 2.5vw, 14px); }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { min-width: 700px; }
        h5 { font-size: clamp(18px, 3vw, 20px); }
        
        .hamburger-menu { 
            display: none; 
            background: rgba(255,255,255,0.2); 
            color: white; 
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
            display: block; 
            width: 100%; 
            padding: 15px 20px; 
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)); 
            color: white; 
            border: none; 
            border-radius: 0; 
            text-align: left; 
            text-decoration: none; 
            font-weight: 600; 
            margin-bottom: 5px; 
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            body { padding: 10px; }
            .hamburger-menu { display: block; }
            .navbar-nav { display: none !important; }
            .section { padding: 20px; }
            .table-responsive { margin: 0 -10px; padding: 0 10px; }
        }
        
        @media (max-width: 480px) {
            .section { padding: 15px; }
            .btn-primary-custom { width: 100%; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>
<button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMobileMenu()">
    <i class="bi bi-list"></i>
</button>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><i class="bi bi-truck"></i> TIKLA GELİR</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Anasayfa</a>
            <a class="nav-link active" href="courier_advances.php"><i class="bi bi-cash-stack"></i> Kurye Finans</a>
        </div>
        <a href="../logout.php" class="btn btn-logout"><i class="bi bi-box-arrow-right"></i> Güvenli Çıkış</a>
    </div>
</nav>

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

<div class="main-content">
    <h1 class="page-title"><i class="bi bi-cash-stack"></i> KURYE FİNANS YÖNETİMİ</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4">
            <div class="section">
                <h5 class="mb-3"><i class="bi bi-person-lines-fill"></i> Kurye Seç</h5>
                <ul class="list-group">
                    <?php foreach($couriers as $c): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="?id=<?= $c['id'] ?>" class="text-decoration-none text-dark">
                            <?= htmlspecialchars($c['name']) ?> <small class="opacity-75"><?= htmlspecialchars($c['phone']) ?></small>
                        </a>
                        <span class="badge bg-<?= $c['advance_balance'] > 0 ? 'danger' : 'success' ?>">
                            <?= formatTL($c['advance_balance']) ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <?php if ($selected): ?>
        <div class="col-lg-8">
            <div class="section">
                <h5 class="mb-3"><i class="bi bi-cash-stack"></i> <?= htmlspecialchars($selected['name']) ?> - Finans Yönetimi</h5>

                <div class="alert alert-info">
                    <strong>Mevcut Bakiye:</strong> <?= formatTL($selected['advance_balance']) ?>
                    <br><small class="opacity-75">(+) Borç, (-) Alacak</small>
                </div>

                <form method="POST" class="row g-3">
                    <input type="hidden" name="courier_id" value="<?= $courier_id ?>">
                    <div class="col-md-6">
                        <label class="form-label">İşlem Türü</label>
                        <select name="type" class="form-select" required>
                            <option value="borc">Kurye Avans Aldı (Borç Artar)</option>
                            <option value="avans">Kuryeye Ödeme Yapıldı (Borç Azalır)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tutar</label>
                        <input type="number" name="amount" step="0.01" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Örn: Haftalık avans, nakit ödeme vb." required></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_finance" class="btn btn-primary-custom"><i class="bi bi-save-fill me-2"></i>İşlemi Kaydet</button>
                    </div>
                </form>
            </div>

            <!-- FİNANS HAREKETLERİ -->
            <div class="section">
                <h5 class="mb-3"><i class="bi bi-clock-history"></i> Son Finans Hareketleri</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr><th>Tarih</th><th>İşlem</th><th>Tutar</th><th>Açıklama</th><th>İşlem</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($history as $h): ?>
                            <tr>
                                <td><?= formatDate($h['created_at']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $h['transaction_type'] === 'withdrawal' ? 'danger' : 'success' ?>">
                                        <?= $h['transaction_type'] === 'withdrawal' ? 'Avans' : 'Ödeme' ?>
                                    </span>
                                </td>
                                <td><?= formatTL($h['amount']) ?></td>
                                <td><?= htmlspecialchars($h['description']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Bu işlemi silmek istediğinize emin misiniz?');">
                                        <input type="hidden" name="finance_id" value="<?= $h['id'] ?>">
                                        <input type="hidden" name="courier_id" value="<?= $courier_id ?>">
                                        <button type="submit" name="delete_finance" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i> Sil
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
</script>
</body>
</html>