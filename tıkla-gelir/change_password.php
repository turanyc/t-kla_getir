<?php
session_start();
require_once "config/database.php";
require_once "functions.php";

// Giriş kontrolü
requireCustomerLogin();

$user_id = getUserId();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Mevcut şifreyi kontrol et
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current_password, $user['password'])) {
        $error = "Mevcut şifre yanlış!";
    } elseif (strlen($new_password) < 6) {
        $error = "Yeni şifre en az 6 karakter olmalı!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Yeni şifreler eşleşmiyor!";
    } else {
        // Şifreyi güncelle
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        
        if ($stmt->execute([$hashed_password, $user_id])) {
            $success = "Şifreniz başarıyla değiştirildi!";
            // Güvenlik için oturumu kapat
            session_destroy();
            header("Refresh:2; url=login.php");
        } else {
            $error = "Şifre değiştirilemedi!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Değiştir - Kral Kurye</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #FF8C42 0%, #FF6B35 50%, #FF4500 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            overflow-x: hidden;
        }
        .password-card {
            background: rgba(255,255,255,0.95);
            border-radius: 25px;
            padding: clamp(20px, 5vw, 40px);
            max-width: 500px;
            width: 100%;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            min-width: 0;
        }
        .password-card h2 {
            color: #FF6B35;
            margin-bottom: clamp(20px, 4vw, 30px);
            text-align: center;
            font-size: clamp(20px, 4vw, 28px);
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: clamp(14px, 2.5vw, 16px);
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 15px;
            font-size: clamp(14px, 2.5vw, 16px);
            transition: all 0.3s;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #FF6B35;
            box-shadow: 0 0 10px rgba(255,107,53,0.2);
        }
        .btn-primary-custom {
            width: 100%;
            padding: clamp(12px, 3vw, 15px);
            background: linear-gradient(135deg, #FF6B35, #FF4500);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: clamp(14px, 3vw, 18px);
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }
        .btn-primary-custom:hover {
            transform: scale(1.02);
        }
        .alert {
            padding: 12px 15px;
            border-radius: 15px;
            margin-bottom: 18px;
            font-size: clamp(13px, 2.5vw, 15px);
        }
        
        @media (max-width: 480px) {
            body { padding: 10px; }
            .password-card { padding: 20px; border-radius: 20px; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>

<div class="password-card">
    <h2><i class="fas fa-key"></i> Şifre Değiştir</h2>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="current_password">Mevcut Şifre</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        
        <div class="form-group">
            <label for="new_password">Yeni Şifre</label>
            <input type="password" id="new_password" name="new_password" required minlength="6">
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Yeni Şifre Tekrar</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
        </div>
        
        <button type="submit" class="btn-primary-custom">
            <i class="fas fa-lock"></i> Şifreyi Güncelle
        </button>
    </form>
</div>

</body>
</html>