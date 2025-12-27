"<?php
require_once \"config/database.php\";

// ==========================================
// LOG SİSTEMİ
// ==========================================
$log_file = __DIR__ . \"/logs/system_check.log\";
$log_dir = dirname($log_file);

if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

function log_check($message, $type = 'INFO') {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = \"[$timestamp] [$type] $message
\";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// Log görüntüleme modu
if (isset($_GET['view_logs'])) {
    header('Content-Type: text/plain; charset=UTF-8');
    if (file_exists($log_file)) {
        echo file_get_contents($log_file);
    } else {
        echo \"Log dosyası bulunamadı.\";
    }
    exit;
}

// Log temizleme
if (isset($_GET['clear_logs'])) {
    if (file_exists($log_file)) {
        unlink($log_file);
        log_check(\"Log dosyası temizlendi\", \"INFO\");
    }
    header(\"Location: check_system.php\");
    exit;
}

log_check(\"=== Sistem kontrolü başlatıldı ===\", \"INFO\");

echo \"<html><head><meta charset='UTF-8'><title>Sistem Kontrolü - Tıkla Gelir</title>\";
echo \"<style>
    body{font-family:Arial;padding:20px;background:#f5f5f5;} 
    .ok{color:green;} 
    .error{color:red;} 
    .warning{color:orange;} 
    table{background:white;border-collapse:collapse;width:100%;margin:20px 0;box-shadow:0 2px 10px rgba(0,0,0,0.1);} 
    th,td{border:1px solid #ddd;padding:10px;text-align:left;} 
    th{background:#FF6B35;color:white;font-weight:bold;}
    .section{background:white;padding:20px;margin:20px 0;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
    .btn{background:#FF6B35;color:white;padding:12px 25px;text-decoration:none;border-radius:50px;display:inline-block;margin:10px 5px;font-weight:bold;transition:all 0.3s;}
    .btn:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(255,107,53,0.4);}
    .btn-secondary{background:#6c757d;}
    .btn-danger{background:#dc3545;}
    .btn-success{background:#28a745;}
    .code-block{background:#f8f9fa;border-left:3px solid #FF6B35;padding:10px;margin:10px 0;font-family:monospace;white-space:pre-wrap;}
    h1{color:#FF6B35;border-bottom:3px solid #FF6B35;padding-bottom:10px;}
    h2{color:#FF6B35;margin-top:30px;}
    .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;margin:20px 0;}
    .stat-card{background:linear-gradient(135deg,#FF6B35,#FF4500);color:white;padding:20px;border-radius:10px;text-align:center;}
    .stat-card h3{margin:0;font-size:36px;}
</style>\";
echo \"</head><body>\";

echo \"<h1>🔍 Tıkla Gelir - Gelişmiş Sistem Kontrolü</h1>\";
echo \"<p><strong>Kontrol Zamanı:</strong> \" . date('d.m.Y H:i:s') . \"</p>\";

// Butonlar
echo \"<div>\";
echo \"<a href='check_system.php' class='btn btn-success'><i class='fas fa-sync'></i> Yenile</a>\";
echo \"<a href='check_system.php?view_logs' class='btn btn-secondary' target='_blank'><i class='fas fa-file-alt'></i> Logları Görüntüle</a>\";
echo \"<a href='check_system.php?clear_logs' class='btn btn-danger' onclick='return confirm(\\"Tüm logları silmek istediğinize emin misiniz?\\");'><i class='fas fa-trash'></i> Logları Temizle</a>\";
echo \"<a href='index.php' class='btn'><i class='fas fa-home'></i> Ana Sayfaya Dön</a>\";
echo \"</div>\";

// İSTATİSTİKLER
$total_errors = 0;
$total_warnings = 0;
$total_ok = 0;

// ==========================================
// 1. VERİTABANI TABLO KONTROLÜ
// ==========================================
$tables_to_check = [
    'users', 'restaurants', 'couriers', 'orders', 'menu_items',
    'vendor_types', 'admin_roles', 'cities', 'districts',
    'verification_logs', 'courier_location', 'categories',
    'reviews', 'promotions', 'courier_finances', 'restaurant_payments',
    'settings', 'notifications', 'courier_payment_confirm'
];

echo \"<div class='section'>\";
echo \"<h2>📊 Veritabanı Tablo Kontrolü</h2>\";
echo \"<table><tr><th>Tablo</th><th>Durum</th><th>Kayıt Sayısı</th></tr>\";

foreach ($tables_to_check as $table) {
    try {
        $stmt = $pdo->query(\"SELECT COUNT(*) FROM $table\");
        $count = $stmt->fetchColumn();
        echo \"<tr><td><strong>$table</strong></td><td class='ok'>✅ Var</td><td>$count</td></tr>\";
        log_check(\"Tablo kontrolü: $table - OK ($count kayıt)\", \"INFO\");
        $total_ok++;
    } catch (Exception $e) {
        echo \"<tr><td><strong>$table</strong></td><td class='error'>❌ Yok</td><td>-</td></tr>\";
        log_check(\"Tablo kontrolü: $table - HATA: \" . $e->getMessage(), \"ERROR\");
        $total_errors++;
    }
}
echo \"</table></div>\";

// ==========================================
// 2. KRİTİK KOLON KONTROLÜ
// ==========================================
echo \"<div class='section'>\";
echo \"<h2>🔧 Kritik Kolon Kontrolü</h2>\";
echo \"<table><tr><th>Tablo</th><th>Kolon</th><th>Durum</th></tr>\";

$columns_to_check = [
    ['restaurants', 'vendor_type_id'],
    ['restaurants', 'is_approved'],
    ['restaurants', 'is_open'],
    ['users', 'admin_role_id'],
    ['users', 'email_verified'],
    ['users', 'phone_verified'],
    ['users', 'kvkk_accepted'],
    ['users', 'city_id'],
    ['users', 'district_id'],
    ['couriers', 'advance_balance'],
    ['couriers', 'is_active'],
    ['orders', 'courier_id'],
    ['orders', 'commission_amount']
];

foreach ($columns_to_check as $check) {
    list($table, $column) = $check;
    try {
        $stmt = $pdo->query(\"SHOW COLUMNS FROM $table LIKE '$column'\");
        if ($stmt->rowCount() > 0) {
            echo \"<tr><td>$table</td><td><strong>$column</strong></td><td class='ok'>✅ Var</td></tr>\";
            log_check(\"Kolon kontrolü: $table.$column - OK\", \"INFO\");
            $total_ok++;
        } else {
            echo \"<tr><td>$table</td><td><strong>$column</strong></td><td class='error'>❌ Yok</td></tr>\";
            log_check(\"Kolon kontrolü: $table.$column - EKSİK\", \"ERROR\");
            $total_errors++;
        }
    } catch (Exception $e) {
        echo \"<tr><td>$table</td><td><strong>$column</strong></td><td class='error'>❌ Tablo yok</td></tr>\";
        log_check(\"Kolon kontrolü: $table.$column - HATA: \" . $e->getMessage(), \"ERROR\");
        $total_errors++;
    }
}
echo \"</table></div>\";

// ==========================================
// 3. DOSYA YAPISI KONTROLÜ
// ==========================================
echo \"<div class='section'>\";
echo \"<h2>📁 Kritik Dosya Kontrolü</h2>\";
echo \"<table><tr><th>Dosya/Klasör</th><th>Yol</th><th>Durum</th><th>İzinler</th></tr>\";

$files_to_check = [
    ['config/database.php', 'file'],
    ['admin/', 'dir'],
    ['admin/index.php', 'file'],
    ['restaurant/', 'dir'],
    ['restaurant/index.php', 'file'],
    ['market/', 'dir'],
    ['market/index.php', 'file'],
    ['grocery/', 'dir'],
    ['grocery/index.php', 'file'],
    ['dried_goods/', 'dir'],
    ['dried_goods/index.php', 'file'],
    ['restaurant/api/', 'dir'],
    ['restaurant/api/toggle_restaurant_status.php', 'file'],
    ['index.php', 'file'],
    ['login.php', 'file'],
    ['logout.php', 'file'],
    ['menu.php', 'file'],
    ['checkout.php', 'file'],
    ['assets/', 'dir'],
    ['logs/', 'dir'],
    ['vendor/', 'dir']
];

foreach ($files_to_check as $item) {
    list($path, $type) = $item;
    $full_path = __DIR__ . '/' . $path;
    
    if (file_exists($full_path)) {
        $perms = substr(sprintf('%o', fileperms($full_path)), -4);
        $writable = is_writable($full_path) ? 'Yazılabilir' : 'Salt okunur';
        echo \"<tr><td><strong>$path</strong></td><td>$full_path</td><td class='ok'>✅ Var</td><td>$perms ($writable)</td></tr>\";
        log_check(\"Dosya kontrolü: $path - OK (İzin: $perms)\", \"INFO\");
        $total_ok++;
    } else {
        echo \"<tr><td><strong>$path</strong></td><td>$full_path</td><td class='error'>❌ Yok</td><td>-</td></tr>\";
        log_check(\"Dosya kontrolü: $path - EKSİK\", \"WARNING\");
        $total_warnings++;
    }
}
echo \"</table></div>\";

// ==========================================
// 4. PHP SYNTAX KONTROLÜ
// ==========================================
echo \"<div class='section'>\";
echo \"<h2>🐘 PHP Syntax Kontrolü</h2>\";
echo \"<table><tr><th>Dosya</th><th>Durum</th><th>Hata Detayı</th></tr>\";

$php_files_to_check = [
    'index.php',
    'login.php',
    'logout.php',
    'menu.php',
    'admin/index.php',
    'restaurant/index.php',
    'market/index.php',
    'grocery/index.php',
    'dried_goods/index.php',
    'restaurant/api/toggle_restaurant_status.php',
    'restaurant/api/update_order_status.php'
];

foreach ($php_files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        $output = [];
        $return_var = 0;
        exec(\"php -l \" . escapeshellarg($full_path) . \" 2>&1\", $output, $return_var);
        
        if ($return_var === 0) {
            echo \"<tr><td><strong>$file</strong></td><td class='ok'>✅ Syntax OK</td><td>-</td></tr>\";
            log_check(\"PHP Syntax: $file - OK\", \"INFO\");
            $total_ok++;
        } else {
            $error_msg = implode(\"
\", $output);
            echo \"<tr><td><strong>$file</strong></td><td class='error'>❌ Syntax Hatası</td><td><div class='code-block'>$error_msg</div></td></tr>\";
            log_check(\"PHP Syntax: $file - HATA: $error_msg\", \"ERROR\");
            $total_errors++;
        }
    } else {
        echo \"<tr><td><strong>$file</strong></td><td class='warning'>⚠️ Dosya Yok</td><td>Dosya bulunamadı</td></tr>\";
        log_check(\"PHP Syntax: $file - DOSYA YOK\", \"WARNING\");
        $total_warnings++;
    }
}
echo \"</table></div>\";

// ==========================================
// 5. VENDOR TİPLERİ
// ==========================================
echo \"<div class='section'>\";
echo \"<h2>🏪 Vendor Tipleri</h2>\";
try {
    $stmt = $pdo->query(\"SELECT * FROM vendor_types\");
    $types = $stmt->fetchAll();
    if ($types) {
        echo \"<table><tr><th>ID</th><th>İsim</th><th>Slug</th><th>İkon</th><th>Panel</th><th>Aktif</th></tr>\";
        foreach ($types as $t) {
            $active_badge = $t['is_active'] ? \"<span class='ok'>✅ Aktif</span>\" : \"<span class='error'>❌ Pasif</span>\";
            echo \"<tr><td>{$t['id']}</td><td>{$t['name']}</td><td>{$t['slug']}</td><td>{$t['icon']}</td><td>{$t['panel_path']}</td><td>$active_badge</td></tr>\";
        }
        echo \"</table>\";
        log_check(\"Vendor tipleri: \" . count($types) . \" tip bulundu\", \"INFO\");
        $total_ok++;
    } else {
        echo \"<p class='warning'>⚠️ Vendor tipi bulunamadı!</p>\";
        log_check(\"Vendor tipleri: Hiç tip yok\", \"WARNING\");
        $total_warnings++;
    }
} catch (Exception $e) {
    echo \"<p class='error'>❌ vendor_types tablosu yok! Hata: \" . $e->getMessage() . \"</p>\";
    log_check(\"Vendor tipleri: HATA - \" . $e->getMessage(), \"ERROR\");
    $total_errors++;
}
echo \"</div>\";

// ==========================================
// 6. ADMİN ROLLERİ
// ==========================================
echo \"<div class='section'>\";
echo \"<h2>👑 Admin Rolleri</h2>\";
try {
    $stmt = $pdo->query(\"SELECT * FROM admin_roles\");
    $roles = $stmt->fetchAll();
    if ($roles) {
        echo \"<table><tr><th>ID</th><th>Rol</th><th>Slug</th><th>Açıklama</th><th>Aktif</th></tr>\";
        foreach ($roles as $r) {
            $active_badge = $r['is_active'] ? \"<span class='ok'>✅ Aktif</span>\" : \"<span class='error'>❌ Pasif</span>\";
            echo \"<tr><td>{$r['id']}</td><td>{$r['role_name']}</td><td>{$r['role_slug']}</td><td>{$r['description']}</td><td>$active_badge</td></tr>\";
        }
        echo \"</table>\";
        log_check(\"Admin rolleri: \" . count($roles) . \" rol bulundu\", \"INFO\");
        $total_ok++;
    } else {
        echo \"<p class='warning'>⚠️ Admin rolü bulunamadı!</p>\";
        log_check(\"Admin rolleri: Hiç rol yok\", \"WARNING\");
        $total_warnings++;
    }
} catch (Exception $e) {
    echo \"<p class='error'>❌ admin_roles tablosu yok! Hata: \" . $e->getMessage() . \"</p>\";
    log_check(\"Admin rolleri: HATA - \" . $e->getMessage(), \"ERROR\");
    $total_errors++;
}
echo \"</div>\";

// ==========================================
// 7. İLLER VE İLÇELER
// ==========================================
echo \"<div class='section'>\";
echo \"<h2>🌍 İller ve İlçeler</h2>\";
try {
    $cities_count = $pdo->query(\"SELECT COUNT(*) FROM cities\")->fetchColumn();
    $districts_count = $pdo->query(\"SELECT COUNT(*) FROM districts\")->fetchColumn();
    
    echo \"<div class='stats'>\";
    echo \"<div class='stat-card'><h3>$cities_count</h3><p>İl</p></div>\";
    echo \"<div class='stat-card'><h3>$districts_count</h3><p>İlçe</p></div>\";
    echo \"</div>\";
    
    $stmt = $pdo->query(\"SELECT * FROM cities LIMIT 10\");
    $cities = $stmt->fetchAll();
    if ($cities) {
        echo \"<h4>İlk 10 İl:</h4>\";
        echo \"<table><tr><th>ID</th><th>İl</th><th>Plaka</th></tr>\";
        foreach ($cities as $c) {
            echo \"<tr><td>{$c['id']}</td><td>{$c['name']}</td><td>{$c['plate_code']}</td></tr>\";
        }
        echo \"</table>\";
    }
    
    log_check(\"İller/İlçeler: $cities_count il, $districts_count ilçe bulundu\", \"INFO\");
    $total_ok++;
} catch (Exception $e) {
    echo \"<p class='error'>❌ cities/districts tablosu yok! Hata: \" . $e->getMessage() . \"</p>\";
    log_check(\"İller/İlçeler: HATA - \" . $e->getMessage(), \"ERROR\");
    $total_errors++;
}
echo \"</div>\";

// ==========================================
// 8. API ENDPOİNT KONTROLÜ
// ==========================================
echo \"<div class='section'>\";
echo \"<h2>🌐 API Endpoint Kontrolü</h2>\";
echo \"<table><tr><th>Endpoint</th><th>Dosya</th><th>Durum</th></tr>\";

$api_endpoints = [
    'restaurant/api/toggle_restaurant_status.php',
    'restaurant/api/update_order_status.php',
    'restaurant/api/count_waiting_payments.php',
    'admin/api/toggle_restaurant_status.php'
];

foreach ($api_endpoints as $endpoint) {
    $full_path = __DIR__ . '/' . $endpoint;
    if (file_exists($full_path)) {
        echo \"<tr><td><strong>$endpoint</strong></td><td>$full_path</td><td class='ok'>✅ Var</td></tr>\";
        log_check(\"API Endpoint: $endpoint - OK\", \"INFO\");
        $total_ok++;
    } else {
        echo \"<tr><td><strong>$endpoint</strong></td><td>$full_path</td><td class='error'>❌ Yok</td></tr>\";
        log_check(\"API Endpoint: $endpoint - EKSİK\", \"WARNING\");
        $total_warnings++;
    }
}
echo \"</table></div>\";

// ==========================================
// 9. COMPOSER & VENDOR
// ==========================================
echo \"<div class='section'>\";
echo \"<h2>📦 Composer & Vendor</h2>\";

if (file_exists(__DIR__ . '/composer.json')) {
    echo \"<p class='ok'>✅ composer.json bulundu</p>\";
    $composer = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);
    echo \"<div class='code-block'>\" . json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . \"</div>\";
    log_check(\"Composer: composer.json OK\", \"INFO\");
    $total_ok++;
} else {
    echo \"<p class='error'>❌ composer.json bulunamadı</p>\";
    log_check(\"Composer: composer.json YOK\", \"WARNING\");
    $total_warnings++;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo \"<p class='ok'>✅ vendor/autoload.php bulundu</p>\";
    log_check(\"Composer: vendor/autoload.php OK\", \"INFO\");
    $total_ok++;
} else {
    echo \"<p class='warning'>⚠️ vendor/autoload.php bulunamadı - Composer install çalıştırın!</p>\";
    log_check(\"Composer: vendor/autoload.php YOK - composer install gerekli\", \"WARNING\");
    $total_warnings++;
}
echo \"</div>\";

// ==========================================
// 10. ÖZET İSTATİSTİKLER
// ==========================================
log_check(\"=== Sistem kontrolü tamamlandı ===\", \"INFO\");
log_check(\"Toplam Hata: $total_errors, Uyarı: $total_warnings, Başarılı: $total_ok\", \"INFO\");

echo \"<div class='section'>\";
echo \"<h2>📊 Özet İstatistikler</h2>\";
echo \"<div class='stats'>\";
echo \"<div class='stat-card' style='background:linear-gradient(135deg,#28a745,#20c997);'><h3>$total_ok</h3><p>✅ Başarılı</p></div>\";
echo \"<div class='stat-card' style='background:linear-gradient(135deg,#ffc107,#ff9800);'><h3>$total_warnings</h3><p>⚠️ Uyarı</p></div>\";
echo \"<div class='stat-card' style='background:linear-gradient(135deg,#dc3545,#c82333);'><h3>$total_errors</h3><p>❌ Hata</p></div>\";
echo \"</div>\";

$total_checks = $total_ok + $total_warnings + $total_errors;
$success_rate = $total_checks > 0 ? round(($total_ok / $total_checks) * 100, 2) : 0;

echo \"<h3>Başarı Oranı: <span class='\" . ($success_rate >= 80 ? 'ok' : ($success_rate >= 60 ? 'warning' : 'error')) . \"'>$success_rate%</span></h3>\";

if ($total_errors == 0 && $total_warnings == 0) {
    echo \"<div style='background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:20px;border-radius:10px;margin:20px 0;'>\";
    echo \"<h2 style='color:#155724;'>🎉 Mükemmel! Sistem tamamen sağlıklı.</h2>\";
    echo \"<p>Tüm kontroller başarıyla geçti. Sisteminiz sorunsuz çalışıyor.</p>\";
    echo \"</div>\";
} elseif ($total_errors == 0) {
    echo \"<div style='background:#fff3cd;border:1px solid #ffeeba;color:#856404;padding:20px;border-radius:10px;margin:20px 0;'>\";
    echo \"<h2 style='color:#856404;'>⚠️ Uyarılar Mevcut</h2>\";
    echo \"<p>Sistem çalışıyor ancak bazı uyarılar var. Logları kontrol edin.</p>\";
    echo \"</div>\";
} else {
    echo \"<div style='background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:20px;border-radius:10px;margin:20px 0;'>\";
    echo \"<h2 style='color:#721c24;'>❌ Kritik Hatalar Tespit Edildi!</h2>\";
    echo \"<p>Sistemde $total_errors hata bulundu. Lütfen logları kontrol edin ve hataları düzeltin.</p>\";
    echo \"</div>\";
}

echo \"</div>\";

// Son butonlar
echo \"<div style='text-align:center;margin-top:30px;'>\";
echo \"<a href='check_system.php' class='btn btn-success'>🔄 Tekrar Kontrol Et</a>\";
echo \"<a href='check_system.php?view_logs' class='btn btn-secondary' target='_blank'>📄 Logları Görüntüle</a>\";
echo \"<a href='index.php' class='btn'>🏠 Ana Sayfaya Dön</a>\";
echo \"</div>\";

echo \"<hr><p style='text-align:center;color:#999;margin-top:50px;'>© 2025 Tıkla Gelir | Sistem Kontrolü v2.0</p>\";
echo \"</body></html>\";
?>
"