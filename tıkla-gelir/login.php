<?php
session_start();
require_once "config/database.php";

// Zaten giriş yapmışsa yönlendir
if(isset($_SESSION['user_id'])) {
    switch($_SESSION['role']) {
        case 'admin':      header("Location: admin/index.php"); exit;
        case 'business':   header("Location: business/index.php"); exit;
        case 'courier':    header("Location: courier/index.php"); exit;
        default:           header("Location: index.php"); exit;
    }
}

$error = '';
$success = '';
$form_type = $_GET['form'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';

    if ($action === 'login') {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $error = "Lütfen tüm alanları doldurun.";
        } else {
            $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // DOĞRULAMA ZORUNLU DEĞİL → HEMEN GİRİŞ YAP
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'] ?? 'Kullanıcı';
                $_SESSION['role']    = $user['role'];

                switch ($user['role']) {
                    case 'admin':      header("Location: admin/index.php"); exit;
                    case 'business':   header("Location: business/index.php"); exit;
                    case 'courier':    header("Location: courier/index.php"); exit;
                    default:           header("Location: index.php"); exit;
                }
            } else {
                $error = "E-posta veya şifre yanlış!";
            }
        }
    }

    elseif ($action === 'register') {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');  // ← artık opsiyonel
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $error = "Ad, e-posta ve şifre zorunlu!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Geçerli e-posta girin!";
        } elseif (strlen($password) < 6) {
            $error = "Şifre en az 6 karakter olmalı!";
        } elseif ($password !== $confirm) {
            $error = "Şifreler eşleşmiyor!";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $error = "Bu e-posta zaten kayıtlı!";
            } else {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
                $stmt->execute([$name, $email, $phone, $hashed]);

                $success = "Kayıt başarılı! Giriş yapabilirsiniz.";
                $form_type = 'login';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tıkla Gelir - Giriş / Kayıt</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SENİN ORİJİNAL STİLİN %100 AYNI KALDI -->
    <style>
        :root { --primary: #FF6B35; --primary-dark: #FF4500; --light: #F8F9FA; --dark: #333333; --card-bg: #FFFFFF; --border-color: #E0E0E0; --shadow: 0 4px 12px rgba(0,0,0,0.08); --shadow-hover: 0 8px 25px rgba(0,0,0,0.15); --gradient: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Poppins', sans-serif; 
            background: var(--light); 
            color: var(--dark); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 15px; 
            overflow-x: hidden;
        }
        .mobile-container { 
            width: 100%; 
            max-width: 400px; 
            background: var(--card-bg); 
            border-radius: 25px; 
            box-shadow: var(--shadow-hover); 
            overflow: hidden; 
            min-width: 0;
        }
        .mobile-header { background: var(--gradient); color: white; padding: 30px 20px; text-align: center; }
        .mobile-header h1 { font-size: 28px; margin-bottom: 5px; }
        .mobile-header p { font-size: 14px; opacity: 0.9; }
        .mobile-tabs { display: flex; background: var(--light); }
        .tab-button { flex: 1; padding: 15px; border: none; background: transparent; color: var(--dark); font-weight: 600; cursor: pointer; transition: all 0.3s; border-bottom: 3px solid transparent; }
        .tab-button.active { background: var(--primary); color: white; border-bottom-color: var(--primary-dark); }
        .mobile-forms { padding: 30px 20px; }
        .mobile-form { display: none; }
        .mobile-form.active { display: block; }
        .input-group { position: relative; margin-bottom: 20px; }
        .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--primary); }
        .input-group input { width: 100%; padding: 15px 15px 15px 45px; border: 1px solid var(--border-color); border-radius: 50px; font-size: 15px; transition: all 0.3s; }
        .input-group input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,53,0.2); }
        .submit-btn { width: 100%; padding: 15px; background: var(--gradient); color: white; border: none; border-radius: 50px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .submit-btn:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }
        .alert { padding: 15px 20px; border-radius: 15px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        .alert.error { background: rgba(244, 67, 54, 0.15); color: var(--danger); border: 1px solid var(--danger); }
        .alert.success { background: rgba(0, 200, 83, 0.15); color: var(--success); border: 1px solid var(--success); }
        .mobile-footer { text-align: center; padding: 20px; font-size: 12px; color: #999; background: var(--light); }
        
        @media (max-width: 480px) {
            body { padding: 10px; }
            .mobile-container { border-radius: 20px; }
            .mobile-header { padding: 25px 15px; }
            .mobile-header h1 { font-size: 24px; }
            .mobile-forms { padding: 20px 15px; }
            .input-group input { padding: 12px 12px 12px 40px; font-size: 14px; }
            .submit-btn { padding: 12px; font-size: 15px; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>

<div class="mobile-container">
    <div class="mobile-header">
        <h1>Tıkla Gelir</h1>
        <p>Hızlı Teslimat, Lezzetli Sofralar</p>
    </div>

    <div class="mobile-tabs">
        <button class="tab-button <?= $form_type === 'login' ? 'active' : '' ?>" onclick="showForm('login')">Giriş Yap</button>
        <button class="tab-button <?= $form_type === 'register' ? 'active' : '' ?>" onclick="showForm('register')">Kayıt Ol</button>
    </div>

    <div class="mobile-forms">
        <!-- GİRİŞ FORMU -->
        <div id="loginForm" class="mobile-form <?= $form_type === 'login' ? 'active' : '' ?>">
            <form method="POST" action="?form=login">
                <input type="hidden" name="action" value="login">
                <div class="input-group"><i class="fas fa-envelope"></i><input type="email" name="email" placeholder="E-posta adresiniz" required></div>
                <div class="input-group"><i class="fas fa-lock"></i><input type="password" name="password" placeholder="Şifreniz" required></div>
                <button type="submit" class="submit-btn">Giriş Yap</button>
                <?php if($error && $form_type === 'login'): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            </form>
        </div>

        <!-- KAYIT FORMU -->
        <div id="registerForm" class="mobile-form <?= $form_type === 'register' ? 'active' : '' ?>">
            <form method="POST" action="?form=register">
                <input type="hidden" name="action" value="register">
                <div class="input-group"><i class="fas fa-user"></i><input type="text" name="name" placeholder="Adınız ve Soyadınız" required></div>
                <div class="input-group"><i class="fas fa-envelope"></i><input type="email" name="email" placeholder="E-posta adresiniz" required></div>
                <div class="input-group"><i class="fas fa-phone"></i><input type="text" name="phone" placeholder="Telefon (isteğe bağlı)" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"></div>
                <div class="input-group"><i class="fas fa-lock"></i><input type="password" name="password" placeholder="Şifre (en az 6 karakter)" required></div>
                <div class="input-group"><i class="fas fa-lock"></i><input type="password" name="confirm_password" placeholder="Şifre Tekrar" required></div>
                <button type="submit" class="submit-btn">Kayıt Ol</button>
                <?php if($error && $form_type === 'register'): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <?php if($success): ?><div class="alert success"><?= $success ?></div><?php endif; ?>
            </form>
        </div>
    </div>

    <div class="mobile-footer">
        <p>Tıkla Gelir © 2025 - Tüm hakları saklıdır</p>
    </div>
</div>

<script>
function showForm(type) {
    document.querySelectorAll('.mobile-form').forEach(f => f.classList.remove('active'));
    document.getElementById(type + 'Form').classList.add('active');
    document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');
    window.history.pushState({}, '', '?form=' + type);
}

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('form') === 'register') showForm('register');
});
</script>

</body>
</html>