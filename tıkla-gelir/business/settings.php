<?php
session_start();
require_once "../config/database.php";
require_once "auth.php";

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $email       = trim($_POST['email']);
    $phone       = trim($_POST['phone']);
    $address     = trim($_POST['address']);
    $description = trim($_POST['description']);

    $hours = [];
    $days  = ['mon','tue','wed','thu','fri','sat','sun'];
    foreach ($days as $d) {
        $open  = trim($_POST[$d.'_open'] ?? '');
        $close = trim($_POST[$d.'_close'] ?? '');
        $hours[$d] = ['open' => $open, 'close' => $close];
    }
    $hours_json = json_encode($hours);

    $profile_image = '';
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['profile_image']['size'] < 3000000) {
            $filename = uniqid('rest_') . '.' . $ext;
            $path     = "../assets/uploads/restaurants/" . $filename;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $path)) {
                $profile_image = ", profile_image = '$filename'";
            }
        }
    }

    $stmt = $pdo->prepare("UPDATE businesses SET name = ?, email = ?, phone = ?, address = ?, description = ?, working_hours = ? $profile_image WHERE user_id = ?");
    if ($stmt->execute([$name, $email, $phone, $address, $description, $hours_json, $_SESSION['user_id']])) {
        $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Bilgiler güncellendi!</div>';
    } else {
        $message = '<div class="alert error"><i class="fas fa-exclamation-triangle"></i> Hata oluştu!</div>';
    }
}

$stmt = $pdo->prepare("SELECT * FROM businesses WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$business = $stmt->fetch(PDO::FETCH_ASSOC);

$working = json_decode($business['working_hours'] ?? '', true) ?: [];
$daysTurkish = [
    'mon' => 'Pazartesi', 'tue' => 'Salı', 'wed' => 'Çarşamba', 'thu' => 'Perşembe',
    'fri' => 'Cuma', 'sat' => 'Cumartesi', 'sun' => 'Pazar'
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ayarlar - <?= htmlspecialchars(BUSINESS_NAME) ?></title>
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
        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: clamp(14px, 2.5vw, 16px); color: var(--primary); }
        input[type="text"], input[type="email"], input[type="tel"], input[type="time"], input[type="file"], textarea { width: 100%; padding: clamp(10px, 2vw, 12px) clamp(15px, 3vw, 18px); border: 1px solid var(--border-color); border-radius: 50px; font-size: clamp(14px, 2.5vw, 15px); transition: all 0.3s; }
        textarea { border-radius: 20px; resize: vertical; min-height: clamp(100px, 15vw, 120px); }
        input:focus, textarea:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.2); }
        .btn-primary { background: var(--gradient); color: white; border: none; padding: clamp(12px, 2.5vw, 14px) clamp(25px, 4vw, 30px); border-radius: 50px; font-size: clamp(14px, 2.5vw, 16px); font-weight: 600; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }
        .alert { padding: clamp(12px, 2.5vw, 15px) clamp(18px, 3vw, 20px); border-radius: 15px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px; font-size: clamp(14px, 2.5vw, 16px); }
        .alert.success { background: var(--success); color: white; }
        .alert.error { background: var(--danger); color: white; }
        .profile-img { width: clamp(120px, 20vw, 150px); height: clamp(120px, 20vw, 150px); border-radius: 50%; object-fit: cover; border: 3px solid var(--primary); margin-bottom: 15px; }
        .week-table { display: grid; gap: clamp(12px, 2.5vw, 15px); }
        .day-row { display: grid; grid-template-columns: clamp(100px, 15vw, 120px) 1fr 1fr; gap: clamp(12px, 2.5vw, 15px); align-items: center; }
        
        @media (max-width: 768px) {
            .top-bar { flex-direction: column; align-items: flex-start; }
            .day-row { grid-template-columns: 1fr; gap: 10px; }
            .day-row label { margin-bottom: 5px; }
            .btn-primary { width: 100%; justify-content: center; }
        }
        
        @media (max-width: 480px) {
            .profile-img { width: 100px; height: 100px; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>
<div class="container">
    <div class="top-bar">
        <div class="logo"><i class="fas fa-cog"></i> Ayarlar - <?= htmlspecialchars(BUSINESS_NAME) ?></div>
        <div class="nav-buttons">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Geri Dön</a>
            <a href="../logout.php" onclick="return confirm('Çıkış yapmak istediğinize emin misiniz?');"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
        </div>
    </div>

    <div class="header-card">
        <h1><i class="fas fa-cog"></i> İşletme Ayarları</h1>
        <p>Profil bilgilerini ve çalışma saatlerini buradan yönetebilirsin.</p>
    </div>

    <?= $message ?>

    <form method="POST" enctype="multipart/form-data">
        <!-- PROFİL BİLGİLERİ -->
        <div class="card">
            <h2>Profil Bilgileri</h2>
            <?php if ($business['profile_image']): ?>
                <img src="../assets/uploads/restaurants/<?= htmlspecialchars($business['profile_image']) ?>" class="profile-img">
            <?php endif; ?>
            <div class="form-group">
                <label>Profil Fotoğrafı</label>
                <input type="file" name="profile_image" accept="image/*">
            </div>
            <div class="form-group">
                <label>İşletme Adı</label>
                <input type="text" name="name" value="<?= htmlspecialchars($business['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>E-posta</label>
                <input type="email" name="email" value="<?= htmlspecialchars($business['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Telefon</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($business['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Açıklama (Müşteriler görecek)</label>
                <textarea name="description"><?= htmlspecialchars($business['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Adres</label>
                <textarea name="address"><?= htmlspecialchars($business['address'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- HAFTALIK ÇALIŞMA SAATLERİ -->
        <div class="card">
            <h2><i class="fas fa-clock"></i> Haftalık Çalışma Saatleri</h2>
            <div class="week-table">
                <?php foreach ($daysTurkish as $en => $tr): ?>
                    <div class="day-row">
                        <label><strong><?= $tr ?></strong></label>
                        <input type="time" name="<?= $en ?>_open" value="<?= htmlspecialchars($working[$en]['open'] ?? '') ?>">
                        <input type="time" name="<?= $en ?>_close" value="<?= htmlspecialchars($working[$en]['close'] ?? '') ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="btn-primary">
            <i class="fas fa-save"></i> Bilgileri Kaydet
        </button>
    </form>
</div>
</body>
</html>