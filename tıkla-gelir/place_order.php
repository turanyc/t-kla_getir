<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json');

// ==== HTTP Metodu Kontrolü ====
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// ==== Oturum ve Yetki Kontrolü ====
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Giriş yapmalısınız!']);
    exit;
}

// ==== JSON Veri Alımı ve Doğrulama ====
$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['items']) || !is_array($input['items'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Sepet boş!']);
    exit;
}

// ==== Değişken Tanımlama ve Temizleme ====
$customer_id = (int)$_SESSION['user_id'];
$payment_method = sanitize($input['payment_method'] ?? 'kapida_nakit');
$address = sanitize(trim($input['address'] ?? ''));
$items = $input['items'];

// ==== Promosyon Kodu Var mı? ====
$promo = $_SESSION['promo'] ?? null;
$discount_amount = 0;
$delivery_fee = 10.00;

// ==== Adres Validasyonu ====
if (empty($address)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Lütfen teslimat adresi girin!']);
    exit;
}

// ==== Sepet Ürünlerini Doğrula ve Toplamı Hesapla ====
$sub_total = 0;
$business_id = null;  // ← restaurant_id yerine business_id kullanıyoruz

foreach ($items as $item) {
    if (!is_array($item) || !isset($item['id'], $item['price'], $item['quantity'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Geçersiz sepet verisi!']);
        exit;
    }

    $item_id = (int)$item['id'];
    $price = (float)$item['price'];
    $quantity = (int)$item['quantity'];
    $item_business_id = (int)($item['restaurant_id'] ?? 0);  // ← frontend'den gelen restaurant_id

    if ($quantity <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Geçersiz ürün miktarı!']);
        exit;
    }

    $sub_total += $price * $quantity;

    if ($business_id === null) {
        $business_id = $item_business_id;
    } elseif ($business_id !== $item_business_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Tüm ürünler aynı işletmeden olmalı!']);
        exit;
    }
}

if (empty($business_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'İşletme bilgisi eksik!']);
    exit;
}

// ==== İşletme Kapalı mı Kontrol Et ====
$stmt_check = $pdo->prepare("SELECT is_open FROM businesses WHERE id = ?");
$stmt_check->execute([$business_id]);
$biz = $stmt_check->fetch();

if (!$biz || $biz['is_open'] == 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Bu işletme şu anda sipariş kabul etmiyor!']);
    exit;
}

// ==== Promosyon Uygula ====
if ($promo) {
    $discount_amount = round($sub_total * ($promo['discount_percent'] / 100), 2);
    if ($promo['free_delivery']) {
        $delivery_fee = 0;
    }
}
$final_total = $sub_total + $delivery_fee - $discount_amount;

// ==== Veritabanı İşlemleri (Transaction) ====
try {
    $pdo->beginTransaction();

    // ← BURAYI DÜZELTTİK: restaurant_id → business_id
    $stmt = $pdo->prepare("INSERT INTO orders 
                           (customer_id, business_id, subtotal, delivery_fee, discount, total_price, 
                            payment_method, status, address, notes, promo_code) 
                           VALUES 
                           (:customer_id, :business_id, :subtotal, :delivery_fee, :discount, :total_price, 
                            :payment_method, 'yeni', :address, :notes, :promo_code)");

    $stmt->execute([
        ':customer_id'    => $customer_id,
        ':business_id'    => $business_id,        // ← DÜZELTİLDİ
        ':subtotal'       => $sub_total,
        ':delivery_fee'   => $delivery_fee,
        ':discount'       => $discount_amount,
        ':total_price'    => $final_total,
        ':payment_method' => $payment_method,
        ':address'        => $address,
        ':notes'          => sanitize($input['note'] ?? ''),
        ':promo_code'     => $promo['code'] ?? null
    ]);

    $order_id = $pdo->lastInsertId();

    // Sipariş kalemlerini kaydet
    $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, product_name, quantity, unit_price, total_price) 
                                VALUES (:order_id, :menu_item_id, :product_name, :quantity, :unit_price, :total_price)");

    foreach ($items as $item) {
        $item_total = $item['price'] * $item['quantity'];
        $stmt_item->execute([
            ':order_id'     => $order_id,
            ':menu_item_id' => $item['id'],
            ':product_name' => $item['name'] ?? 'Ürün',
            ':quantity'     => $item['quantity'],
            ':unit_price'   => $item['price'],
            ':total_price'  => $item_total
        ]);
    }

    // Bildirimleri gönder (businesses tablosuna göre düzeltildi)
    $notify_sql = "INSERT INTO notifications (user_id, title, message, type, created_at) VALUES 
                   ((SELECT user_id FROM businesses WHERE id = :business_id), :title1, :msg1, 'order', NOW()),
                   ((SELECT id FROM users WHERE role = 'admin'), :title2, :msg2, 'order', NOW())";

    $stmt_notify = $pdo->prepare($notify_sql);
    $stmt_notify->execute([
        ':business_id' => $business_id,  // ← DÜZELTİLDİ
        ':title1' => 'YENİ SİPARİŞ!',
        ':msg1' => "Sipariş #{$order_id} - " . number_format($final_total, 2) . "₺",
        ':title2' => 'YENİ MÜŞTERİ SİPARİŞİ',
        ':msg2' => "Sipariş oluşturuldu #{$order_id}"
    ]);

    $pdo->commit();

    // Sepeti temizle
    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$customer_id]);

    // Promosyonu temizle
    unset($_SESSION['promo']);

    // Başarılı sonuç
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'total' => $final_total,
        'payment_method' => $payment_method,
        'message' => 'Siparişiniz başarıyla oluşturuldu!'
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Sipariş oluşturulamadı']);
    error_log("Sipariş Hatası [Kullanıcı: {$customer_id}]: " . $e->getMessage());
}

// ==== Yardımcı Fonksiyon ====
function sanitize($data): string
{
    return htmlspecialchars(strip_tags(trim($data)));
}
?>