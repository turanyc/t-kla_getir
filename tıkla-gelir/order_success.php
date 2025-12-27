<?php
session_start();
if (!isset($_SESSION['user_id'])) header("Location: login.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sipariş Başarılı - Kral Kurye</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Poppins', sans-serif; 
            background: linear-gradient(135deg, #FF8C42 0%, #FF6B35 100%); 
            display:flex; 
            align-items:center; 
            justify-content:center; 
            min-height:100vh; 
            margin:0; 
            padding: 15px;
            overflow-x: hidden;
        }
        .success-box { 
            background: rgba(255,255,255,0.95); 
            padding: clamp(30px, 6vw, 50px); 
            border-radius: 20px; 
            text-align: center; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.3); 
            max-width: 500px; 
            width: 100%;
            min-width: 0;
        }
        h1 { 
            color: #FF6B35; 
            font-size: clamp(24px, 5vw, 36px); 
            margin-bottom: 20px; 
        }
        p { 
            color: #333; 
            font-size: clamp(14px, 3vw, 18px); 
            margin-bottom: 25px; 
        }
        .order-id { 
            background: #FFD700; 
            color: #333; 
            padding: 10px 20px; 
            border-radius: 50px; 
            font-weight: bold; 
            font-size: clamp(16px, 3vw, 20px); 
            display: inline-block; 
            margin: 15px 0; 
        }
        a { 
            display:inline-block; 
            padding:clamp(12px, 3vw, 15px) clamp(25px, 5vw, 30px); 
            background:linear-gradient(135deg, #FF6B35 0%, #FF4500 100%); 
            color:white; 
            text-decoration:none; 
            border-radius:50px; 
            font-weight: bold; 
            transition: 0.3s; 
            font-size: clamp(14px, 2.5vw, 16px);
        }
        a:hover { 
            transform: scale(1.02); 
            box-shadow: 0 8px 25px rgba(255,107,53,0.5); 
        }
        
        @media (max-width: 480px) {
            body { padding: 10px; }
            .success-box { padding: 25px; border-radius: 15px; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>
    <div class="success-box">
        <h1>🎉 Siparişin Krala Yakışır!</h1>
        <p>Siparişin başarıyla alındı ve hazırlanıyor.</p>
        <div class="order-id">Sipariş #<?= htmlspecialchars($_GET['id'] ?? 'N/A') ?></div>
        <p>En kısa sürede kapında! 👑</p>
        <a href="index.php">Anasayfaya Dön</a>
    </div>
</body>
</html>