<?php

session_start();

require_once "config/database.php";



// ========== GİRİŞ & ROL KONTROLÜ ==========

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php?redirect=profile.php");

    exit;

}



$user_id = $_SESSION['user_id'];

$user_name = htmlspecialchars($_SESSION['name'] ?? 'Misafir');

$user_role_raw = $_SESSION['role'] ?? 'customer';



// Rollerin Türkçe karşılıkları

$role_names = [

    'customer' => 'Kullanıcı',

    'admin' => 'Yönetici',

    'restaurant' => 'Restoran Sahibi',

    'courier' => 'Kurye'

];

$user_role_display = $role_names[$user_role_raw] ?? 'Kullanıcı';



// Tablo kontrol fonksiyonu

function tableExists($pdo, $table) {

    try {

        return $pdo->query("SHOW TABLES LIKE '$table'")->rowCount() > 0;

    } catch(Exception $e) {

        return false;

    }

}



// ========== İŞLEM: ADRES EKLE/DÜZENLE/SİL & ŞİFRE DEĞİŞTİR ==========

$message = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        // Adres Ekle

        if (isset($_POST['action']) && $_POST['action'] === 'add_address') {

            $title = $_POST['address_title'] ?? '';

            $address = $_POST['address_text'] ?? '';

            $is_default = isset($_POST['is_default']) ? 1 : 0;

            

            if (empty($title) || empty($address)) {

                $message = ['type' => 'danger', 'text' => 'Lütfen tüm alanları doldurun!'];

            } else {

                // Eğer varsayılan işaretlendiyse, diğerlerini kaldır

                if ($is_default) {

                    $pdo->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = ?")->execute([$user_id]);

                }

                

                $stmt = $pdo->prepare("INSERT INTO addresses (user_id, title, address, is_default) VALUES (?, ?, ?, ?)");

                $stmt->execute([$user_id, $title, $address, $is_default]);

                $message = ['type' => 'success', 'text' => '✅ Adres başarıyla eklendi!'];

            }

        }

        

        // Adres Sil

        if (isset($_POST['action']) && $_POST['action'] === 'delete_address' && isset($_POST['address_id'])) {

            $address_id = (int)$_POST['address_id'];

            $stmt = $pdo->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");

            $stmt->execute([$address_id, $user_id]);

            $message = ['type' => 'success', 'text' => '🗑️ Adres silindi!'];

        }

        

        // E-posta Değiştir

        if (isset($_POST['action']) && $_POST['action'] === 'change_email') {

            $new_email = $_POST['new_email'] ?? '';

            $current_password = $_POST['current_password'] ?? '';

            

            // Şifre doğrulama

            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");

            $stmt->execute([$user_id]);

            $user = $stmt->fetch();

            

            if ($user && password_verify($current_password, $user['password'])) {

                // E-posta kontrolü

                $check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");

                $check->execute([$new_email, $user_id]);

                if ($check->fetch()) {

                    $message = ['type' => 'danger', 'text' => '❌ Bu e-posta zaten kullanımda!'];

                } else {

                    $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");

                    $stmt->execute([$new_email, $user_id]);

                    $user['email'] = $new_email; // Anında güncelle

                    $message = ['type' => 'success', 'text' => '✅ E-posta adresiniz güncellendi!'];

                }

            } else {

                $message = ['type' => 'danger', 'text' => '❌ Şifreniz yanlış!'];

            }

        }

        

        // Telefon Değiştir

        if (isset($_POST['action']) && $_POST['action'] === 'change_phone') {

            $new_phone = $_POST['new_phone'] ?? '';

            $current_password = $_POST['current_password_phone'] ?? '';

            

            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");

            $stmt->execute([$user_id]);

            $user = $stmt->fetch();

            

            if ($user && password_verify($current_password, $user['password'])) {

                $stmt = $pdo->prepare("UPDATE users SET phone = ? WHERE id = ?");

                $stmt->execute([$new_phone, $user_id]);

                $user['phone'] = $new_phone; // Anında güncelle

                $message = ['type' => 'success', 'text' => '✅ Telefon numaranız güncellendi!'];

            } else {

                $message = ['type' => 'danger', 'text' => '❌ Şifreniz yanlış!'];

            }

        }

        

        // Şifre Değiştir

        if (isset($_POST['action']) && $_POST['action'] === 'change_password') {

            $current_password = $_POST['current_password'] ?? '';

            $new_password = $_POST['new_password'] ?? '';

            $confirm_password = $_POST['confirm_password'] ?? '';

            

            // Şifre doğrulama

            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");

            $stmt->execute([$user_id]);

            $user = $stmt->fetch();

            

            if ($user && password_verify($current_password, $user['password'])) {

                if ($new_password !== $confirm_password) {

                    $message = ['type' => 'danger', 'text' => '❌ Yeni şifreler eşleşmiyor!'];

                } elseif (strlen($new_password) < 6) {

                    $message = ['type' => 'danger', 'text' => '❌ Şifre en az 6 karakter olmalı!'];

                } else {

                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");

                    $stmt->execute([$hashed_password, $user_id]);

                    $message = ['type' => 'success', 'text' => '✅ Şifreniz başarıyla güncellendi!'];

                }

            } else {

                $message = ['type' => 'danger', 'text' => '❌ Mevcut şifreniz yanlış!'];

            }

        }

        

    } catch(PDOException $e) {

        $message = ['type' => 'danger', 'text' => '❌ Hata: ' . $e->getMessage()];

    }

}



// ========== VERİTABANI SORGULARI ==========

try {

    // Kullanıcı profili

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");

    $stmt->execute([$user_id]);

    $user = $stmt->fetch();



    // İstatistikler

    $stats = ['total_orders' => 0, 'completed_orders' => 0, 'total_spending' => 0];

    if (tableExists($pdo, 'orders')) {

        $stats_stmt = $pdo->prepare("SELECT 

            COUNT(*) as total_orders,

            COUNT(CASE WHEN status = 'teslim' THEN 1 END) as completed_orders,

            COALESCE(SUM(total_price), 0) as total_spending

            FROM orders WHERE customer_id = ?");

        $stats_stmt->execute([$user_id]);

        $stats = $stats_stmt->fetch();

    }



    // Son siparişler

    $recent_orders = [];

    if (tableExists($pdo, 'orders')) {

        $orders_stmt = $pdo->prepare("SELECT o.*, r.name as restaurant_name 

                                      FROM orders o 

                                      LEFT JOIN restaurants r ON o.restaurant_id = r.id 

                                      WHERE o.customer_id = ? 

                                      ORDER BY o.created_at DESC LIMIT 5");

        $orders_stmt->execute([$user_id]);

        $recent_orders = $orders_stmt->fetchAll();

    }



    // Adresler (user_id ile sorgula)

    $addresses = [];

    if (tableExists($pdo, 'addresses')) {

        $addresses_stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC");

        $addresses_stmt->execute([$user_id]);

        $addresses = $addresses_stmt->fetchAll();

    }



    // Yorumlar

    $review_stats = ['avg_rating' => 0, 'total_reviews' => 0];

    if (tableExists($pdo, 'reviews')) {

        $review_stmt = $pdo->prepare("SELECT COALESCE(AVG(rating), 0) as avg_rating, COUNT(*) as total_reviews 

                                      FROM reviews WHERE user_id = ?");

        $review_stmt->execute([$user_id]);

        $review_stats = $review_stmt->fetch();

    }



} catch(PDOException $e) {

    die("Veritabanı Hatası: " . $e->getMessage());

}

?>

<!DOCTYPE html>

<html lang="tr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profilim - İste Gelir</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>

        body { 

            background: #ffffff;

            color: #333;

            font-family: 'Poppins', sans-serif;

            min-height: 100vh;

            padding: 15px;

            background-image: radial-gradient(#FFE0B2 1px, transparent 1px);

            background-size: 20px 20px;
            overflow-x: hidden;

        }

        .profile-header {

            background: linear-gradient(135deg, #FF6B35, #FF4500);

            color: white;

            padding: 40px 30px;

            border-radius: 30px;

            margin-bottom: 30px;

            box-shadow: 0 10px 30px rgba(255,107,53,0.3);

            text-align: center;

            position: relative; /* Geri butonu için */

        }

        .profile-header i { font-size: 90px; margin-bottom: 15px; }

        .profile-header h1 { font-weight: 800; font-size: 36px; }

        .profile-header p { font-size: 18px; margin-top: 10px; opacity: 0.9; }

        .profile-card {

            background: #ffffff;

            border: 2px solid #FFE0B2;

            border-radius: 25px;

            padding: 30px;

            margin-bottom: 25px;

            box-shadow: 0 8px 25px rgba(0,0,0,0.08);

        }

        .section-title {

            color: #FF6B35;

            font-weight: 700;

            margin: 25px 0 20px;

            font-size: 24px;

            padding-bottom: 10px;

            border-bottom: 3px solid #FFE0B2;

        }

        .info-row {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 15px 0;

            border-bottom: 1px solid #FFE0B2;

        }

        .info-row:last-child { border-bottom: none; }

        .info-row span i { color: #FF6B35; margin-right: 10px; width: 20px; }

        .stats-grid {

            display: grid;

            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));

            gap: 20px;

        }

        .stat-card {

            background: linear-gradient(135deg, #FFF3E0, #FFE0B2);

            padding: 25px;

            border-radius: 20px;

            text-align: center;

            border: 2px solid #FFCC80;

        }

        .stat-card .number { font-size: 40px; font-weight: 800; color: #FF6B35; }

        .address-card, .order-card {

            background: #FFF8F0;

            border-radius: 15px;

            padding: 20px;

            margin-bottom: 15px;

            border-left: 5px solid #FF6B35;

        }

        .btn-primary-custom {

            background: linear-gradient(135deg, #FF6B35, #FF4500);

            border: none;

            border-radius: 50px;

            padding: 12px 30px;

            font-weight: 600;

            color: #fff;

            text-decoration: none;

            display: inline-block;

        }

        .modal-header {

            background: linear-gradient(135deg, #FF6B35, #FF4500);

            color: white;

        }

        

        /* Zarif geri butonu stili */

        .btn-back-custom {

            background: rgba(255, 255, 255, 0.15) !important;

            border: 1px solid rgba(255, 255, 255, 0.3) !important;

            border-radius: 50px !important;

            padding: 8px 18px !important;

            font-size: 14px !important;

            font-weight: 500 !important;

            transition: all 0.3s ease !important;

            backdrop-filter: blur(5px);

            -webkit-backdrop-filter: blur(5px);

        }

        .btn-back-custom:hover {

            background: rgba(255, 255, 255, 0.25) !important;

            transform: translateX(-3px); /* Hafif sola kayma efekti */

        }

        .btn-back-custom i {

            font-size: 16px; /* İkon boyutu */

        }
        
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            body { padding: 10px; overflow-x: hidden; }
            .profile-header { padding: 30px 20px; margin-bottom: 20px; }
            .profile-header i { font-size: 60px; }
            .profile-header h1 { font-size: clamp(24px, 5vw, 36px); }
            .profile-header p { font-size: clamp(14px, 3vw, 18px); }
            .profile-card { padding: 20px; }
            .section-title { font-size: clamp(18px, 4vw, 24px); }
            .info-row { 
                flex-direction: column; 
                align-items: flex-start; 
                gap: 10px; 
            }
            .stats-grid { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; }
            .stat-card { padding: 20px; }
            .stat-card .number { font-size: clamp(28px, 6vw, 40px); }
            .address-card, .order-card { padding: 15px; }
            .container { padding: 0; }
        }
        
        @media (max-width: 480px) {
            body { padding: 8px; }
            .profile-header { padding: 25px 15px; }
            .profile-header i { font-size: 50px; }
            .stats-grid { grid-template-columns: 1fr; }
            .profile-card { padding: 15px; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }

    </style>

</head>

<body>



<!-- ========== BİLDİRİM ========== -->

<?php if(!empty($message)): ?>

<div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index:1050;">

    <?= $message['text'] ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php endif; ?>



<!-- ========== PROFİL HEADER ========== -->


<div class="profile-header">

    <!-- Zarif Geri Butonu -->

    <div class="position-absolute top-0 start-0 m-4">

        <a href="index.php" class="btn btn-back-custom text-white">

            <i class="bi bi-arrow-left"></i> Geri

        </a>

    </div>

    

    <i class="bi bi-person-circle"></i>

    <h1><?= htmlspecialchars($user['name']) ?></h1>

    <p>Hoş geldiniz, <?= htmlspecialchars($user['name']) ?>!</p>

</div>



<div class="container" style="max-width: 1000px;">

    

    <!-- ========== KİŞİSEL BİLGİLER ========== -->

    <div class="profile-card">

        <h3 class="section-title"><i class="bi bi-info-circle-fill"></i> Kişisel Bilgiler</h3>

        

        <div class="info-row">

            <span><i class="bi bi-envelope-fill"></i> E-posta Adresi</span>

            <div>

                <strong><?= htmlspecialchars($user['email']) ?></strong>

                <button class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#changeEmailModal">

                    <i class="bi bi-pencil-fill"></i> Değiştir

                </button>

            </div>

        </div>

        

        <div class="info-row">

            <span><i class="bi bi-telephone-fill"></i> Telefon Numarası</span>

            <div>

                <strong><?= htmlspecialchars($user['phone'] ?? 'Belirtilmemiş') ?></strong>

                <button class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#changePhoneModal">

                    <i class="bi bi-pencil-fill"></i> Değiştir

                </button>

            </div>

        </div>

        

        <div class="info-row">

            <span><i class="bi bi-person-badge-fill"></i> Kullanıcı Adı</span>

            <strong><?= htmlspecialchars($user['username']) ?></strong>

        </div>

        

        <div class="info-row">

            <span><i class="bi bi-calendar-check-fill"></i> Üyelik Tarihi</span>

            <strong><?= date('d.m.Y', strtotime($user['created_at'])) ?></strong>

        </div>

        

        <div class="info-row">

            <span><i class="bi bi-shield-check"></i> Rol</span>

            <span class="badge bg-primary" style="font-size:14px;padding:8px 20px;"><?= $user_role_display ?></span>

        </div>

    </div>



    <!-- ========== İSTATİSTİKLER ========== -->

    <div class="profile-card">

        <h3 class="section-title"><i class="bi bi-graph-up"></i> Aktivite İstatistikleri</h3>

        <div class="stats-grid">

            <div class="stat-card">

                <span class="number"><?= $stats['total_orders'] ?></span>

                <div class="label">Toplam Sipariş</div>

            </div>

            <div class="stat-card">

                <span class="number"><?= $stats['completed_orders'] ?></span>

                <div class="label">Tamamlanan</div>

            </div>

            <div class="stat-card">

                <span class="number"><?= number_format($stats['total_spending'], 0) ?>₺</span>

                <div class="label">Harcama</div>

            </div>

            <div class="stat-card">

                <span class="number"><?= number_format($review_stats['avg_rating'], 1) ?>★</span>

                <div class="label">Ortalama Puan</div>

            </div>

        </div>

    </div>



    <!-- ========== ADRES YÖNETİMİ ========== -->

    <div class="profile-card">

        <h3 class="section-title"><i class="bi bi-geo-alt-fill"></i> Adres Yönetimi</h3>

        

        <!-- Yeni Adres Ekle Butonu -->

        <div class="mb-3">

            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addAddressModal">

                <i class="bi bi-plus-circle-fill"></i> Yeni Adres Ekle

            </button>

        </div>

        

        <!-- Adres Listesi -->

        <?php if(!empty($addresses)): ?>

            <?php foreach($addresses as $address): ?>

                <div class="address-card">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <h6 class="mb-1"><?= htmlspecialchars($address['title']) ?></h6>

                            <p class="mb-1 text-muted"><?= htmlspecialchars($address['address']) ?></p>

                            <small class="text-success"><?= $address['is_default'] ? '✅ Varsayılan Adres' : '' ?></small>

                        </div>

                        <div>

                            <form method="post" class="d-inline">

                                <input type="hidden" name="action" value="delete_address">

                                <input type="hidden" name="address_id" value="<?= $address['id'] ?>">

                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Adresi silmek istediğinize emin misiniz?')">

                                    <i class="bi bi-trash-fill"></i> Sil

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="text-center p-5 text-muted">

                <i class="bi bi-geo-alt" style="font-size: 48px;"></i>

                <p class="mt-3">Henüz adres eklemediniz</p>

            </div>

        <?php endif; ?>

    </div>



    <!-- ========== GÜVENLİK ========== -->

    <div class="profile-card">

        <h3 class="section-title"><i class="bi bi-shield-lock-fill"></i> Güvenlik</h3>

        <div class="info-row mb-3">

            <span><i class="bi bi-key-fill"></i> Şifre</span>

            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">

                <i class="bi bi-pencil-fill"></i> Şifre Değiştir

            </button>

        </div>

        <div class="text-center">

            <a href="logout.php" class="btn btn-primary-custom" style="background: linear-gradient(135deg, #F44336, #D32F2F);">

                <i class="bi bi-box-arrow-right"></i> Güvenli Çıkış

            </a>

        </div>

    </div>



</div>



<!-- ========== E-POSTA DEĞİŞTİRME MODAL ========== -->

<div class="modal fade" id="changeEmailModal" tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title"><i class="bi bi-envelope-fill"></i> E-posta Değiştir</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>

            <form method="post">

                <div class="modal-body">

                    <input type="hidden" name="action" value="change_email">

                    <div class="mb-3">

                        <label>Yeni E-posta</label>

                        <input type="email" name="new_email" class="form-control" required>

                    </div>

                    <div class="mb-3">

                        <label>Şifreniz</label>

                        <input type="password" name="current_password" class="form-control" required>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>

                    <button type="submit" class="btn btn-primary-custom">Güncelle</button>

                </div>

            </form>

        </div>

    </div>

</div>



<!-- ========== TELEFON DEĞİŞTİRME MODAL ========== -->

<div class="modal fade" id="changePhoneModal" tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title"><i class="bi bi-telephone-fill"></i> Telefon Değiştir</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>

            <form method="post">

                <div class="modal-body">

                    <input type="hidden" name="action" value="change_phone">

                    <div class="mb-3">

                        <label>Yeni Telefon</label>

                        <input type="tel" name="new_phone" class="form-control" required>

                    </div>

                    <div class="mb-3">

                        <label>Şifreniz</label>

                        <input type="password" name="current_password_phone" class="form-control" required>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>

                    <button type="submit" class="btn btn-primary-custom">Güncelle</button>

                </div>

            </form>

        </div>

    </div>

</div>



<!-- ========== ŞİFRE DEĞİŞTİRME MODAL ========== -->

<div class="modal fade" id="changePasswordModal" tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title"><i class="bi bi-key-fill"></i> Şifre Değiştir</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>

            <form method="post">

                <div class="modal-body">

                    <input type="hidden" name="action" value="change_password">

                    <div class="mb-3">

                        <label>Mevcut Şifre</label>

                        <input type="password" name="current_password" class="form-control" required>

                    </div>

                    <div class="mb-3">

                        <label>Yeni Şifre</label>

                        <input type="password" name="new_password" class="form-control" required>

                    </div>

                    <div class="mb-3">

                        <label>Yeni Şifre Tekrar</label>

                        <input type="password" name="confirm_password" class="form-control" required>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>

                    <button type="submit" class="btn btn-primary-custom">Güncelle</button>

                </div>

            </form>

        </div>

    </div>

</div>



<!-- ========== ADRES EKLEME MODAL ========== -->

<div class="modal fade" id="addAddressModal" tabindex="-1">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title"><i class="bi bi-geo-alt-fill"></i> Yeni Adres Ekle</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>

            <form method="post">

                <div class="modal-body">

                    <input type="hidden" name="action" value="add_address">

                    <div class="mb-3">

                        <label>Adres Başlığı (Ev, İş, vb.)</label>

                        <input type="text" name="address_title" class="form-control" placeholder="Örn: Evim" required>

                    </div>

                    <div class="mb-3">

                        <label>Tam Adres</label>

                        <textarea name="address_text" class="form-control" rows="4" placeholder="Mahalle, Cadde, Sokak, Daire No..." required></textarea>

                    </div>

                    <div class="form-check">

                        <input type="checkbox" name="is_default" class="form-check-input" id="is_default">

                        <label class="form-check-label" for="is_default">Varsayılan adres yap</label>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>

                    <button type="submit" class="btn btn-primary-custom">Adres Ekle</button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- Bootstrap JS (BU SATIRI EKLE!) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
</script>

</body>

</html>