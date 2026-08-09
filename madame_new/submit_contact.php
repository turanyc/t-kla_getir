<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Lütfen zorunlu alanları doldurun.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $subject, $message]);
        
        echo json_encode(['success' => true, 'message' => 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.']);
    } catch (PDOException $e) {
        error_log("Contact form error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Sistemde bir hata oluştu. Lütfen daha sonra tekrar deneyin.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek.']);
}
?>
