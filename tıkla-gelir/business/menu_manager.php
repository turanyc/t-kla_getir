<?php
session_start();
require_once "../config/database.php";
require_once "auth.php";

$message = '';
$title = match(BUSINESS_TYPE) {
    'market'     => 'Market Ürünleri',
    'grocery'    => 'Manav Ürünleri',
    'dried_goods'=> 'Kuruyemiş Ürünleri',
    default      => 'Menü Yönetimi'
};

// CSRF
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = $_SESSION['csrf_token'];

// Upload klasörü
$upload_dir = __DIR__ . "/../assets/uploads/menus/";
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

// Ürün ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_menu'])) {
    if (!hash_equals($csrf_token, $_POST['csrf_token'])) die('❌ CSRF hatası!');
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    if (empty($name) || $price <= 0) {
        $message = '<div class="alert error"><i class="fas fa-exclamation-circle"></i> Ad ve fiyat zorunludur!</div>';
    } else {
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $_FILES['image']['size'] < 2000000) {
                $filename = uniqid('menu_') . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                    $image = $filename;
                }
            }
        }
        $stmt = $pdo->prepare("INSERT INTO menu_items (business_id, name, price, description, image) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([BUSINESS_ID, $name, $price, $description, $image])) {
            header("Location: menu_manager.php?msg=" . urlencode('✅ Ürün eklendi!') . "&type=success");
            exit;
        }
    }
}

// Ürün silme
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (!hash_equals($csrf_token, $_GET['token'])) die('❌ CSRF hatası!');
    $id = intval($_GET['delete']);
    $img_stmt = $pdo->prepare("SELECT image FROM menu_items WHERE id = ? AND business_id = ?");
    $img_stmt->execute([$id, BUSINESS_ID]);
    $img = $img_stmt->fetchColumn();
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ? AND business_id = ?");
    if ($stmt->execute([$id, BUSINESS_ID])) {
        if ($img && file_exists($upload_dir . $img)) unlink($upload_dir . $img);
        header("Location: menu_manager.php?msg=" . urlencode('✅ Ürün silindi!') . "&type=success");
        exit;
    }
}

// Mevcut ürünler
$menus = $pdo->prepare("SELECT * FROM menu_items WHERE business_id = ? ORDER BY created_at DESC");
$menus->execute([BUSINESS_ID]);
$menu_items = $menus->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?> - <?= htmlspecialchars(BUSINESS_NAME) ?></title>
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
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--primary); font-size: clamp(14px, 2.5vw, 16px); }
        input[type="text"], input[type="number"], textarea, input[type="file"] { width: 100%; padding: clamp(10px, 2vw, 12px) clamp(15px, 3vw, 18px); border: 1px solid var(--border-color); border-radius: 50px; font-size: clamp(14px, 2.5vw, 15px); transition: all 0.3s; }
        textarea { border-radius: 20px; resize: vertical; min-height: clamp(100px, 15vw, 120px); }
        input:focus, textarea:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.2); }
        .btn-primary { background: var(--gradient); color: white; border: none; padding: clamp(12px, 2.5vw, 14px) clamp(25px, 4vw, 30px); border-radius: 50px; font-size: clamp(14px, 2.5vw, 16px); font-weight: 600; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }
        .btn-danger { background: var(--danger); color: white; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(clamp(250px, 30vw, 300px), 1fr)); gap: clamp(20px, 3vw, 25px); margin-top: 25px; }
        .menu-item { background: var(--card-bg); padding: clamp(15px, 3vw, 20px); border-radius: 20px; border: 1px solid var(--border-color); transition: all 0.4s; position: relative; overflow: hidden; text-align: center; }
        .menu-item:hover { transform: translateY(-10px); border-color: var(--primary); box-shadow: var(--shadow-hover); }
        .menu-item img { width: 100%; height: clamp(150px, 25vw, 200px); object-fit: cover; border-radius: 15px; margin-bottom: 15px; }
        .menu-item h3 { font-size: clamp(16px, 3vw, 18px); margin-bottom: 10px; }
        .menu-price { color: var(--primary); font-size: clamp(22px, 4vw, 28px); font-weight: bold; margin: 15px 0; }
        .delete-btn { position: absolute; top: 15px; right: 15px; background: var(--danger); color: white; width: clamp(30px, 5vw, 36px); height: clamp(30px, 5vw, 36px); border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.3s; z-index: 10; font-size: clamp(14px, 2.5vw, 16px); }
        .delete-btn:hover { transform: scale(1.1); box-shadow: 0 3px 10px rgba(244, 67, 54, 0.5); }
        .empty-state { text-align: center; padding: clamp(40px, 8vw, 60px) 20px; color: var(--primary); background: #FFF3E0; border-radius: 20px; border: 2px solid #FFE0B2; grid-column: 1 / -1; }
        .empty-state h3 { font-size: clamp(20px, 3vw, 24px); }
        .empty-state p { font-size: clamp(14px, 2.5vw, 16px); }
        .alert { padding: clamp(12px, 2.5vw, 15px) clamp(18px, 3vw, 20px); border-radius: 15px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px; font-size: clamp(14px, 2.5vw, 16px); }
        .alert.success { background: rgba(0, 200, 83, 0.15); color: var(--success); border: 1px solid var(--success); }
        .alert.error { background: rgba(244, 67, 54, 0.15); color: var(--danger); border: 1px solid var(--danger); }
        
        @media (max-width: 768px) {
            .top-bar { flex-direction: column; align-items: flex-start; }
            .menu-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
        }
        
        @media (max-width: 480px) {
            .menu-grid { grid-template-columns: 1fr; }
            .btn-primary { width: 100%; justify-content: center; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>
<div class="container">
    <div class="top-bar">
        <div class="logo"><i class="fas fa-utensils"></i> <?= htmlspecialchars(BUSINESS_NAME) ?></div>
        <div class="nav-buttons">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Panele Dön</a>
        </div>
    </div>

    <?php if ($message) echo $message; ?>

    <div class="header-card">
        <h1><i class="fas fa-plus-circle"></i> <?= $title ?></h1>
        <p>Yeni ürün ekleyebilir veya mevcut ürünleri silebilirsiniz.</p>
    </div>

    <!-- YENİ ÜRÜN EKLEME -->
    <div class="card">
        <h2>Yeni Ürün Ekle</h2>
        <form method="POST" enctype="multipart/form-data" id="menuForm">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <div class="form-group">
                <label>Ürün Adı *</label>
                <input type="text" name="name" placeholder="Örn: Domates" required maxlength="100">
            </div>
            <div class="form-group">
                <label>Fiyat (₺) *</label>
                <input type="number" name="price" step="0.01" placeholder="Örn: 12.90" required min="0">
            </div>
            <div class="form-group">
                <label>Açıklama</label>
                <textarea name="description" placeholder="Ürün hakkında bilgi..." maxlength="500"></textarea>
            </div>
            <div class="form-group">
                <label>Fotoğraf</label>
                <input type="file" name="image" accept="image/*">
            </div>
            <button type="submit" name="add_menu" class="btn-primary">
                <i class="fas fa-save"></i> Kaydet
            </button>
        </form>
    </div>

    <!-- MEVCUT ÜRÜNLER -->
    <div class="card">
        <h2><i class="fas fa-list"></i> Ürün Listesi (<?= count($menu_items) ?>)</h2>
        <div class="menu-grid">
            <?php if (empty($menu_items)): ?>
                <div class="empty-state">
                    <i class="fas fa-utensils"></i>
                    <h3>Henüz ürün eklemediniz</h3>
                    <p>Yukarıdaki formdan yeni ürün ekleyebilirsiniz</p>
                </div>
            <?php else: ?>
                <?php foreach ($menu_items as $item): ?>
                    <div class="menu-item">
                        <a href="?delete=<?= $item['id'] ?>&token=<?= $csrf_token ?>" class="delete-btn"
                           onclick="return confirm('<?= htmlspecialchars($item['name']) ?> silinsin mi?')" title="Sil">
                            <i class="fas fa-times"></i>
                        </a>
                        <?php if (!empty($item['image'])): ?>
                            <img src="../assets/uploads/menus/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 200px; background: var(--gradient); display: flex; align-items: center; justify-content: center; border-radius: 15px; color: white; font-size: 16px;">
                                <i class="fas fa-image" style="font-size: 40px;"></i>
                            </div>
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <div class="menu-price"><?= number_format($item['price'], 2) ?> ₺</div>
                        <?php if (!empty($item['description'])): ?>
                            <small><?= htmlspecialchars($item['description']) ?></small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.getElementById('menuForm')?.addEventListener('submit', function () {
    const btn = this.querySelector('button[type="submit"]');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Kaydediliyor...';
    btn.disabled = true;
});
</script>
</body>
</html>