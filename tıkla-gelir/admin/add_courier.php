<?php
session_start();
require_once "../config/database.php";  // ../ KALDIRILDI ✅

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");  // ../public/ KALDIRILDI ✅
    exit;
}

if (isset($_POST['add'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['pass'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if ($name && $email && $pass && $phone) {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        
        $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'courier')")
            ->execute([$name, $email, $hashed_pass, $phone]);
        $user_id = $pdo->lastInsertId();

        $pdo->prepare("INSERT INTO couriers (user_id, is_active) VALUES (?, 0)")
            ->execute([$user_id]);

        echo "<script>alert('Kurye eklendi kral!'); location.href='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Lütfen tüm alanları doldurun!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurye Ekle - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #0f0f1a; color: #fff; margin: 0; padding: 15px; overflow-x: hidden; }
        .container { max-width: 600px; margin: clamp(30px, 6vw, 50px) auto; background: #111; padding: clamp(25px, 5vw, 40px); border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); min-width: 0; }
        h2 { text-align: center; margin-bottom: 25px; color: #2962FF; font-size: clamp(24px, 5vw, 32px); }
        input { padding: clamp(12px, 3vw, 15px); width: 100%; margin: 12px 0; border: none; border-radius: 12px; background: #222; color: #fff; font-size: clamp(14px, 2.5vw, 16px); box-sizing: border-box; }
        button { padding: clamp(15px, 3vw, 18px); width: 100%; background: #2962FF; color: white; border: none; border-radius: 12px; font-size: clamp(16px, 3vw, 18px); font-weight: bold; cursor: pointer; transition: 0.3s; }
        button:hover { background: #1a52b3; transform: scale(1.01); }
        .back-btn { display: inline-block; margin-bottom: 15px; padding: clamp(10px, 2.5vw, 12px) clamp(20px, 4vw, 25px); background: #667eea; color: white; text-decoration: none; border-radius: 50px; font-size: clamp(14px, 2.5vw, 16px); }
        
        @media (max-width: 480px) {
            body { padding: 10px; }
            .container { padding: 20px; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">← Geri Dön</a>
        <h2>➕ Yeni Kurye Ekle</h2>
        <form method="post">
            <input type="text" name="name" placeholder="Ad Soyad" required>
            <input type="email" name="email" placeholder="E-posta (Giriş için)" required>
            <input type="password" name="pass" placeholder="Şifre" required>
            <input type="text" name="phone" placeholder="Telefon Numarası" required>
            <button type="submit" name="add">KURYE EKLE</button>
        </form>
    </div>
</body>
</html>