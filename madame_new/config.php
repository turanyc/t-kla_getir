<?php
// ============================================
// MADAME PATISSERIE & CAFE - CONFIGURATION (DB VERSION)
// ============================================

// Veritabanı Bağlantı Bilgileri
define('DB_HOST', 'localhost');
define('DB_NAME', 'madamDB');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP kullanıyorsan varsayılan şifre boştur
define('SITE_NAME', 'Madame Patisserie & Cafe');

// PDO ile Veritabanı Bağlantısını ve Tablo Yapısını Başlatma (Self-healing Schema)
try {
    try {
        // Önce doğrudan veritabanına bağlanmayı dene (Canlı sunucular için standart yöntem)
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // Eğer veritabanı yoksa (1049 hatası), oluşturmayı dene (Yerel XAMPP ortamları için)
        if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8", DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8 COLLATE utf8_general_ci");
            $pdo->exec("USE `" . DB_NAME . "`");
        } else {
            throw $e; // Bağlantı hatası, şifre yanlış vs.
        }
    }
    
    // 3. Tabloları oluştur (eğer yoksa)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `admin_users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `login_attempts` (
        `ip_address` VARCHAR(45) PRIMARY KEY,
        `attempts` INT NOT NULL DEFAULT 1,
        `last_attempt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `categories` (
        `id` VARCHAR(50) PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `img` VARCHAR(100) DEFAULT NULL,
        `banner` VARCHAR(100) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `products` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `category_id` VARCHAR(50) NOT NULL,
        `name` VARCHAR(255) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `price` INT NOT NULL,
        `image_path` VARCHAR(255) DEFAULT NULL,
        `status` VARCHAR(20) DEFAULT 'Aktif',
        FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `contact_messages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `phone` VARCHAR(20) DEFAULT NULL,
        `subject` VARCHAR(100) DEFAULT NULL,
        `message` TEXT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    // 3.5. Eksik Sütun Kontrolleri (Migration Helper)
    try {
        $pdo->query("SELECT `img` FROM `categories` LIMIT 1");
    } catch (PDOException $e) {
        $pdo->exec("ALTER TABLE `categories` ADD COLUMN `img` VARCHAR(100) DEFAULT NULL;");
    }

    try {
        $pdo->query("SELECT `banner` FROM `categories` LIMIT 1");
    } catch (PDOException $e) {
        $pdo->exec("ALTER TABLE `categories` ADD COLUMN `banner` VARCHAR(100) DEFAULT NULL;");
    }

    try {
        $pdo->query("SELECT `is_featured` FROM `products` LIMIT 1");
    } catch (PDOException $e) {
        $pdo->exec("ALTER TABLE `products` 
            ADD COLUMN `is_featured` TINYINT DEFAULT 0,
            ADD COLUMN `featured_desc` TEXT DEFAULT NULL,
            ADD COLUMN `f_detail1_label` VARCHAR(50) DEFAULT NULL,
            ADD COLUMN `f_detail1_value` VARCHAR(100) DEFAULT NULL,
            ADD COLUMN `f_detail2_label` VARCHAR(50) DEFAULT NULL,
            ADD COLUMN `f_detail2_value` VARCHAR(100) DEFAULT NULL,
            ADD COLUMN `f_detail3_label` VARCHAR(50) DEFAULT NULL,
            ADD COLUMN `f_detail3_value` VARCHAR(100) DEFAULT NULL;");
    }

    // 4. Varsayılan Verileri Ekle (Yalnızca admin tablosu boşsa yönetici hesabını oluştur)
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    $checkAdmin = $pdo->query("SELECT COUNT(*) FROM `admin_users`")->fetchColumn();
    if ($checkAdmin == 0) {
        $hashedDefault = password_hash('madam2024', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT IGNORE INTO `admin_users` (`username`, `password`) VALUES (?, ?)");
        $stmt->execute(['root', $hashedDefault]);
    }

    // Veritabanındaki eski düz metin şifreleri güvenli şifreleme formatına (Bcrypt) dönüştür
    $users = $pdo->query("SELECT id, password FROM admin_users")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $u) {
        $pwd = $u['password'];
        $info = password_get_info($pwd);
        if ($info['algo'] === 0) { // Eğer şifrelenmemiş düz metin ise
            $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            $updateStmt->execute([$hashedPwd, $u['id']]);
        }
    }

    // Yabancı anahtar kontrollerini yeniden etkinleştiriyoruz
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
} catch (PDOException $e) {
    die("Veritabanı bağlantı veya kurulum hatası: " . $e->getMessage());
}

// Cloudflare ve Güvenlik Sabitleri
define('SESSION_TIMEOUT', 900); // 15 dakika (saniye cinsinden)

// IP Alıcı
function getClientIp() {
    return $_SERVER['REMOTE_ADDR'] ?? '';
}

// Oturum Yönetimi (Güvenli Çerez Ayarları ile)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}

// Admin Giriş Kontrol Fonksiyonları
function isLoggedIn() {
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        if (isset($_SESSION['last_activity'])) {
            $elapsedTime = time() - $_SESSION['last_activity'];
            if ($elapsedTime > SESSION_TIMEOUT) {
                return false; // Oturum süresi dolmuş
            }
        }
        return true;
    }
    return false;
}

function requireLogin() {
    if (!isLoggedIn()) {
        // Eğer giriş yapmıştı ama süresi dolduysa, temizleyip timeout uyarısı ile yönlendir
        if (isset($_SESSION['admin_logged_in'])) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            header('Location: index.php?timeout=1');
            exit;
        }
        header('Location: index.php');
        exit;
    }
    // Aktivite zamanını güncelle
    $_SESSION['last_activity'] = time();
}

// IP Tabanlı Kaba Kuvvet (Brute Force) ve Robot Engelleme Yardımcıları
function checkIpBlocked($ip) {
    global $pdo;
    try {
        // 15 dakikadan eski denemeleri temizle
        $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE last_attempt < DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
        $stmt->execute();
        
        $stmt = $pdo->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE ip_address = ?");
        $stmt->execute([$ip]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row && $row['attempts'] >= 5) {
            $lastTime = strtotime($row['last_attempt']);
            $timeLeft = 900 - (time() - $lastTime);
            if ($timeLeft > 0) {
                return ceil($timeLeft / 60); // Kalan dakika
            }
        }
    } catch (PDOException $e) {
        // Veritabanı hatasında bloklama yapma
    }
    return 0;
}

// failed attempt, clear attempt logs
function registerFailedAttempt($ip) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO login_attempts (ip_address, attempts, last_attempt) VALUES (?, 1, NOW()) ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()");
        $stmt->execute([$ip]);
    } catch (PDOException $e) {
        // Hataları yut
    }
}

function clearAttempts($ip) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
        $stmt->execute([$ip]);
    } catch (PDOException $e) {
        // Hataları yut
    }
}

// Görsel Boyutlandırma ve WebP Dönüştürme Yardımcı Fonksiyonu
function compressAndConvertToWebP($tmpPath, $destFolder, $prefix = '', $maxGenislik = 800) {
    @ini_set('memory_limit', '256M');

    if (!extension_loaded('gd')) {
        $_SESSION['flash_warning'] = 'Sunucuda PHP GD kütüphanesi aktif değil. Görsel yükleme yapılamıyor.';
        return false;
    }

    if (!is_writable($destFolder)) {
        $_SESSION['flash_warning'] = 'Sunucudaki "imgs" klasörü yazılabilir değil. Lütfen FTP üzerinden bu klasörün izinlerini (CHMOD 755 veya 777) olarak ayarlayın.';
        return false;
    }

    if (!file_exists($tmpPath)) {
        $_SESSION['flash_warning'] = 'Geçici dosya bulunamadı veya sunucu tarafından silindi. PHP upload_tmp_dir ayarlarını kontrol edin.';
        return false;
    }

    $info = getimagesize($tmpPath);
    if (!$info) {
        $_SESSION['flash_warning'] = 'Yüklenen dosya geçerli bir görsel değil.';
        return false;
    }

    $mime = $info['mime'];
    $kaynak = null;

    switch ($mime) {
        case 'image/jpeg':
        case 'image/jpg':
        case 'image/pjpeg':
            if (function_exists('imagecreatefromjpeg')) {
                $kaynak = @imagecreatefromjpeg($tmpPath);
            } else {
                $_SESSION['flash_warning'] = 'Sunucu GD kütüphanesi JPEG dosyalarını okuyamıyor.';
                return false;
            }
            break;
        case 'image/png':
        case 'image/x-png':
            if (function_exists('imagecreatefrompng')) {
                $kaynak = @imagecreatefrompng($tmpPath);
            } else {
                $_SESSION['flash_warning'] = 'Sunucu GD kütüphanesi PNG dosyalarını okuyamıyor.';
                return false;
            }
            break;
        case 'image/gif':
            if (function_exists('imagecreatefromgif')) {
                $kaynak = @imagecreatefromgif($tmpPath);
            } else {
                $_SESSION['flash_warning'] = 'Sunucu GD kütüphanesi GIF dosyalarını okuyamıyor.';
                return false;
            }
            break;
        case 'image/webp':
            if (function_exists('imagecreatefromwebp')) {
                $kaynak = @imagecreatefromwebp($tmpPath);
            } else {
                $_SESSION['flash_warning'] = 'Sunucu GD kütüphanesi WebP dosyalarını okuyamıyor.';
                return false;
            }
            break;
        case 'image/avif':
            if (function_exists('imagecreatefromavif')) {
                $kaynak = @imagecreatefromavif($tmpPath);
            } else {
                $_SESSION['flash_warning'] = 'Sunucu GD kütüphanesi AVIF dosyalarını okuyamıyor.';
                return false;
            }
            break;
        case 'image/bmp':
        case 'image/x-ms-bmp':
            if (function_exists('imagecreatefrombmp')) {
                $kaynak = @imagecreatefrombmp($tmpPath);
            } else {
                $_SESSION['flash_warning'] = 'Sunucu GD kütüphanesi BMP dosyalarını okuyamıyor.';
                return false;
            }
            break;
        default:
            $_SESSION['flash_warning'] = 'Desteklenmeyen görsel formatı (' . htmlspecialchars($mime) . ').';
            return false;
    }

    if (!$kaynak) {
        $_SESSION['flash_warning'] = 'Görsel dosyası açılamadı. Dosya bozuk veya çok yüksek çözünürlüklü olabilir.';
        return false;
    }

    $genislik = imagesx($kaynak);
    $yukseklik = imagesy($kaynak);

    if ($genislik > $maxGenislik) {
        $yeni_genislik = $maxGenislik;
        $yeni_yukseklik = floor($yukseklik * ($yeni_genislik / $genislik));
    } else {
        $yeni_genislik = $genislik;
        $yeni_yukseklik = $yukseklik;
    }

    $yeni_gorsel = imagecreatetruecolor($yeni_genislik, $yeni_yukseklik);

    if ($mime == 'image/png' || $mime == 'image/webp' || $mime == 'image/gif') {
        imagealphablending($yeni_gorsel, false);
        imagesavealpha($yeni_gorsel, true);
        $transparent = imagecolorallocatealpha($yeni_gorsel, 255, 255, 255, 127);
        imagefilledrectangle($yeni_gorsel, 0, 0, $yeni_genislik, $yeni_yukseklik, $transparent);
    }

    imagecopyresampled($yeni_gorsel, $kaynak, 0, 0, 0, 0, $yeni_genislik, $yeni_yukseklik, $genislik, $yukseklik);

    $supportsWebP = function_exists('imagewebp');
    
    if ($supportsWebP) {
        $newFileName = $prefix . uniqid() . '.webp';
        $destPath = rtrim($destFolder, '/\\') . DIRECTORY_SEPARATOR . $newFileName;
        $saved = imagewebp($yeni_gorsel, $destPath, 80);
    } else {
        $isTransparent = ($mime == 'image/png' || $mime == 'image/webp' || $mime == 'image/gif');
        if ($isTransparent && function_exists('imagepng')) {
            $newFileName = $prefix . uniqid() . '.png';
            $destPath = rtrim($destFolder, '/\\') . DIRECTORY_SEPARATOR . $newFileName;
            $saved = imagepng($yeni_gorsel, $destPath, 8);
        } else if (function_exists('imagejpeg')) {
            $newFileName = $prefix . uniqid() . '.jpg';
            $destPath = rtrim($destFolder, '/\\') . DIRECTORY_SEPARATOR . $newFileName;
            $saved = imagejpeg($yeni_gorsel, $destPath, 85);
        } else {
            $saved = false;
        }
    }

    imagedestroy($kaynak);
    imagedestroy($yeni_gorsel);

    if (!$saved) {
        $_SESSION['flash_warning'] = 'Sunucu görseli kaydedemedi. İzinleri veya disk doluluğunu kontrol edin.';
        return false;
    }

    return $newFileName;
}

// Get Menu Data helper
function getMenuData() {
    global $pdo;
    
    try {
        $catStmt = $pdo->query("SELECT id, name, img, banner FROM categories");
        $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $prodStmt = $pdo->query("SELECT id, category_id as cat, name, description as `desc`, price, image_path as img, status, is_featured, featured_desc, f_detail1_label, f_detail1_value, f_detail2_label, f_detail2_value, f_detail3_label, f_detail3_value FROM products");
        $products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($products as &$item) {
            $item['active'] = ($item['status'] === 'Aktif');
        }
        
        return [
            'categories' => $categories,
            'items' => $products
        ];
        
    } catch (PDOException $e) {
        return ['categories' => [], 'items' => []];
    }
}
