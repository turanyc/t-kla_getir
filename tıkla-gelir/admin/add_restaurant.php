<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_POST['add'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['pass'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name && $email && $pass && $address) {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        
        $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'restaurant')")
            ->execute([$name, $email, $hashed_pass, $phone]);
        $user_id = $pdo->lastInsertId();

        $pdo->prepare("INSERT INTO restaurants (user_id, name, address, phone) VALUES (?, ?, ?, ?)")
            ->execute([$user_id, $name, $address, $phone]);

        echo "<script>alert('İşyeri başarıyla eklendi!'); location.href='index.php';</script>";
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
    <title>İşyeri Ekle - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #FF4500;
            --secondary: #00C853;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Poppins', sans-serif; 
            background: linear-gradient(135deg, #F5F7FA, #E8EAF6); 
            color: #333; 
            margin: 0; 
            padding: 15px; 
            overflow-x: hidden;
        }
        .container { 
            max-width: 600px; 
            margin: clamp(30px, 6vw, 50px) auto; 
            background: #fff; 
            padding: clamp(25px, 5vw, 40px); 
            border-radius: 20px; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.15); 
            min-width: 0;
        }
        h2 { 
            text-align: center; 
            margin-bottom: 25px; 
            background: linear-gradient(45deg, var(--primary), var(--secondary)); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
            font-size: clamp(24px, 5vw, 32px); 
            font-weight: 800;
        }
        input, textarea { 
            padding: clamp(12px, 3vw, 15px); 
            width: 100%; 
            margin: 12px 0; 
            border: 2px solid #E0E0E0; 
            border-radius: 12px; 
            background: #F8F9FA; 
            color: #333; 
            font-size: clamp(14px, 2.5vw, 16px); 
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(255,107,53,0.1);
        }
        textarea { 
            min-height: 100px; 
            resize: vertical; 
        }
        button { 
            padding: clamp(15px, 3vw, 18px); 
            width: 100%; 
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)); 
            color: white; 
            border: none; 
            border-radius: 12px; 
            font-size: clamp(16px, 3vw, 18px); 
            font-weight: 700; 
            cursor: pointer; 
            transition: all 0.3s; 
            box-shadow: 0 5px 20px rgba(255,107,53,0.3);
        }
        button:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 30px rgba(255,107,53,0.5);
        }
        .back-btn { 
            display: inline-block; 
            margin-bottom: 15px; 
            padding: clamp(10px, 2.5vw, 12px) clamp(20px, 4vw, 25px); 
            background: white;
            color: var(--primary); 
            text-decoration: none; 
            border-radius: 50px; 
            border: 2px solid var(--primary);
            font-weight: 600;
            transition: all 0.3s;
            font-size: clamp(14px, 2.5vw, 16px);
        }
        .back-btn:hover {
            background: var(--primary);
            color: white;
        }
        
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
        <h2>➕ Yeni İşyeri Ekle</h2>
        <form method="post">
            <input type="text" name="name" placeholder="İşyeri Adı" required>
            <input type="email" name="email" placeholder="E-posta (Giriş için)" required>
            <input type="password" name="pass" placeholder="Şifre" required>
            <input type="text" name="phone" placeholder="Telefon Numarası">
            <textarea name="address" placeholder="Adres" required></textarea>
            <button type="submit" name="add">İŞYERİ EKLE</button>
        </form>
    </div>
</body>
</html>