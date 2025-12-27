<?php
/**
 * TIKLA GELİR - Ortak Fonksiyonlar
 * Bu dosya projenin her yerinde kullanılan yardımcı fonksiyonları içerir.
 */

// ==================== FORMAT FONKSİYONLARI ====================

/**
 * Para birimini TL formatında göster
 * @param float $amount
 * @return string
 */
function formatTL($amount): string
{
    return number_format($amount, 2, ',', '.') . ' ₺';
}

/**
 * Tarihi okunabilir formata çevir
 * @param string $date
 * @return string
 */
function formatDate($date): string
{
    return date('d.m.Y H:i', strtotime($date));
}

/**
 * Veriyi Excel formatında indir
 * @param array $data
 * @param string $filename
 * @return void
 */
function exportToExcel($data, $filename): void
{
    require 'vendor/autoload.php';
    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray(array_keys($data[0]), null, 'A1');
    $sheet->fromArray($data, null, 'A2');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}


// ==================== ADRES YÖNETİMİ ====================

/**
 * Kullanıcının tüm adreslerini getir
 * @param PDO $pdo
 * @param int $user_id
 * @return array
 */
function getUserAddresses(PDO $pdo, int $user_id): array
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Adres getirme hatası: " . $e->getMessage());
        return [];
    }
}

/**
 * Yeni adres ekle
 * @param PDO $pdo
 * @param int $user_id
 * @param string $title
 * @param string $address
 * @param bool $is_default
 * @return bool
 */
function addAddress(PDO $pdo, int $user_id, string $title, string $address, bool $is_default = false): bool
{
    try {
        // Eğer bu varsayılan adres ise, diğerlerini false yap
        if ($is_default) {
            $pdo->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = ?")->execute([$user_id]);
        }
        
        $stmt = $pdo->prepare("INSERT INTO addresses (user_id, title, address, is_default) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$user_id, $title, $address, $is_default ? 1 : 0]);
    } catch (Exception $e) {
        error_log("Adres ekleme hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Kullanıcının varsayılan adresini getir
 * @param PDO $pdo
 * @param int $user_id
 * @return array|null
 */
function getDefaultAddress(PDO $pdo, int $user_id): ?array
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? AND is_default = 1 LIMIT 1");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    } catch (Exception $e) {
        error_log("Varsayılan adres hatası: " . $e->getMessage());
        return null;
    }
}


// ==================== GÜVENLİK FONKSİYONLARI ====================

/**
 * Müşteri girişi kontrol et - Değilse login'e yönlendir
 * @return void
 */
function requireCustomerLogin(): void
{
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
        $redirect = urlencode($_SERVER['REQUEST_URI']);
        header("Location: login.php?redirect={$redirect}");
        exit;
    }
}

/**
 * Veriyi temizle ve güvenli hale getir (XSS önlemi)
 * @param mixed $data
 * @return string
 */
function sanitize($data): string
{
    return htmlspecialchars(strip_tags(trim((string)$data)));
}

/**
 * Güvenli integer dönüşümü
 * @param mixed $value
 * @param int $default
 * @return int
 */
function safeInt($value, int $default = 0): int
{
    return filter_var($value, FILTER_VALIDATE_INT) ?: $default;
}

/**
 * Güvenli float dönüşümü
 * @param mixed $value
 * @param float $default
 * @return float
 */
function safeFloat($value, float $default = 0.0): float
{
    return filter_var($value, FILTER_VALIDATE_FLOAT) ?: $default;
}


// ==================== SEPET FONKSİYONLARI ====================

/**
 * Sepet toplamını hesapla
 * @param array $items
 * @return float
 */
function calculateCartTotal(array $items): float
{
    $total = 0;
    foreach ($items as $item) {
        $total += safeFloat($item['price'] ?? 0) * safeInt($item['quantity'] ?? 0);
    }
    return round($total, 2);
}

/**
 * Sepet ürün sayısını hesapla
 * @param array $items
 * @return int
 */
function getCartItemCount(array $items): int
{
    return array_sum(array_map(function($item) {
        return safeInt($item['quantity'] ?? 0);
    }, $items));
}


// ==================== BİLDİRİM FONKSİYONLARI ====================

/**
 * Yeni bildirim oluştur
 * @param PDO $pdo
 * @param int $user_id
 * @param string $title
 * @param string $message
 * @param string $type
 * @return bool
 */
function createNotification(PDO $pdo, int $user_id, string $title, string $message, string $type = 'info'): bool
{
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, created_at) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([$user_id, $title, $message, $type]);
    } catch (Exception $e) {
        error_log("Bildirim hatası: " . $e->getMessage());
        return false;
    }
}


// ==================== KULLANICI FONKSİYONLARI ====================

/**
 * Kullanıcı rolünü kontrol et
 * @param string $role
 * @return bool
 */
function hasRole(string $role): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Kullanıcı ID'sini al
 * @return int|null
 */
function getUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}


// ==================== VERİTABANI FONKSİYONLARI ====================

/**
 * Son insert ID'yi al
 * @param PDO $pdo
 * @return int
 */
function getLastInsertId(PDO $pdo): int
{
    return (int)$pdo->lastInsertId();
}

/**
 * Tablo var mı kontrol et
 * @param PDO $pdo
 * @param string $table
 * @return bool
 */
function tableExists(PDO $pdo, string $table): bool
{
    try {
        $pdo->query("SELECT 1 FROM {$table} LIMIT 1");
        return true;
    } catch (Exception $e) {
        return false;
    }
}


// ==================== PROMOSYON & TESLİMAT FONKSİYONLARI ====================

/**
 * Para birimini TL formatında göster (İkinci versiyon)
 * @param float $amount
 * @return string
 */
function formatCurrency(float $amount): string
{
    return "₺" . number_format($amount, 2, ',', '.');
}

/**
 * Restoranın teslimat ücretini getir
 * @param PDO $pdo
 * @param int $restaurant_id
 * @return float
 */
function getDeliveryFee(PDO $pdo, int $restaurant_id): float
{
    $stmt = $pdo->prepare("SELECT delivery_fee FROM restaurants WHERE id = ?");
    $stmt->execute([$restaurant_id]);
    return (float) ($stmt->fetchColumn() ?: 10.00);
}