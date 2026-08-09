<?php
require_once __DIR__ . '/../config.php';
requireLogin();

// Dynamically generate the QR URL pointing to /qr
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
$scriptPath = $_SERVER['REQUEST_URI'];
// Resolve parent directory path of /admin
$parentPath = preg_replace('/\/admin\/[^\/]+$/', '', $scriptPath);
$qrTargetUrl = $protocol . $domainName . $parentPath . '/qr';

$tableNumber = isset($_GET['table']) ? trim($_GET['table']) : '';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sanatsal QR Menü Kartı Tasarımı | Madame Patisserie</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&family=Jost:wght@300;400;500;600&family=Great+Vibes&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --cream: #faf6f0;
    --cream-dark: #f0e8da;
    --gold: #c9a84c;
    --gold-light: #e2c47e;
    --gold-dark: #8a6a1f;
    --dark: #1a1511;
    --dark-2: #2c2219;
    --text: #3a2e22;
    --text-light: #7a6a58;
    --white: #ffffff;
  }

  body {
    font-family: 'Jost', sans-serif;
    background: #e8e3dc;
    color: var(--dark);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
  }

  /* Control Panel */
  .control-panel {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 16px;
    padding: 20px 30px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
    display: flex;
    flex-direction: column;
    gap: 15px;
  }
  .control-panel h3 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 18px;
    font-weight: 700;
  }
  .input-row {
    display: flex;
    gap: 10px;
  }
  .input-row input {
    flex: 1;
    padding: 10px 14px;
    border: 1px solid var(--cream-dark);
    border-radius: 10px;
    font-family: 'Jost', sans-serif;
    font-size: 14px;
    outline: none;
  }
  .input-row input:focus {
    border-color: var(--gold);
  }
  .btn-print {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #ffffff;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-family: 'Jost', sans-serif;
    font-weight: 600;
    cursor: pointer;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 1px;
    transition: transform 0.2s;
  }
  .btn-print:hover {
    transform: translateY(-1px);
  }

  /* The Flyer / Card Container */
  .qr-card-wrapper {
    background: var(--cream);
    width: 380px;
    height: 560px; /* Golden ratio style proportion */
    border-radius: 20px;
    position: relative;
    box-shadow: 0 20px 50px rgba(26,21,17,0.15);
    padding: 40px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    overflow: hidden;
    border: 1px solid rgba(201,168,76,0.15);
  }

  /* Elegant Double Borders */
  .inner-border {
    position: absolute;
    inset: 18px;
    border: 1px solid var(--gold);
    pointer-events: none;
    border-radius: 14px;
  }
  .inner-border-2 {
    position: absolute;
    inset: 22px;
    border: 0.5px solid rgba(201,168,76,0.5);
    pointer-events: none;
    border-radius: 10px;
  }

  /* Decorative corners */
  .corner-deco {
    position: absolute;
    width: 25px;
    height: 25px;
    border-color: var(--gold-dark);
    border-style: solid;
    pointer-events: none;
  }
  .c-tl { top: 10px; left: 10px; border-width: 1.5px 0 0 1.5px; border-radius: 6px 0 0 0; }
  .c-tr { top: 10px; right: 10px; border-width: 1.5px 1.5px 0 0; border-radius: 0 6px 0 0; }
  .c-bl { bottom: 10px; left: 10px; border-width: 0 0 1.5px 1.5px; border-radius: 0 0 0 6px; }
  .c-br { bottom: 10px; right: 10px; border-width: 0 1.5px 1.5px 0; border-radius: 0 0 6px 0; }

  /* Logo & Title */
  .logo-section {
    text-align: center;
    margin-top: 10px;
    z-index: 2;
  }
  .logo-img {
    width: 130px;
    height: auto;
    filter: sepia(0.3) saturate(1.2);
    margin: 0 auto 5px;
  }
  .brand-script {
    font-family: 'Great Vibes', cursive;
    font-size: 26px;
    color: var(--gold-dark);
    line-height: 1;
  }

  /* Table Badge */
  .table-badge {
    background: rgba(201,168,76,0.06);
    border: 1px solid rgba(201,168,76,0.25);
    color: var(--gold-dark);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    padding: 6px 20px;
    border-radius: 20px;
    margin-top: 8px;
    z-index: 2;
  }

  /* Scan instruction heading */
  .instruction-section {
    text-align: center;
    z-index: 2;
  }
  .inst-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 28px;
    font-weight: 500;
    color: var(--dark);
    line-height: 1.25;
  }
  .inst-sub {
    font-size: 12px;
    color: var(--text-light);
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-top: 6px;
    font-weight: 400;
  }

  /* QR Frame */
  .qr-frame {
    position: relative;
    width: 200px;
    height: 200px;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(26,21,17,0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--border);
    padding: 12px;
    z-index: 2;
  }
  .qr-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }
  /* Decorative frame lines around QR code */
  .qr-frame::before {
    content: '';
    position: absolute;
    inset: -6px;
    border: 1px dashed var(--gold);
    border-radius: 20px;
    pointer-events: none;
    opacity: 0.6;
  }

  /* Bottom greetings */
  .footer-greeting {
    text-align: center;
    font-family: 'Cormorant Garamond', serif;
    font-size: 16px;
    font-style: italic;
    color: var(--text-light);
    margin-bottom: 10px;
    z-index: 2;
  }
  
  /* Link hint info */
  .link-info {
    font-size: 8px;
    color: rgba(122,106,88,0.5);
    letter-spacing: 0.5px;
    margin-top: -15px;
    z-index: 2;
    word-break: break-all;
    max-width: 250px;
    text-align: center;
  }

  /* Print Stylesheet */
  @media print {
    body {
      background: #ffffff;
      padding: 0;
      min-height: auto;
    }
    .control-panel {
      display: none !important;
    }
    .qr-card-wrapper {
      box-shadow: none !important;
      border: 1px solid var(--gold) !important;
      margin: 0 auto;
      page-break-inside: avoid;
    }
  }
</style>
<script>
  function updateTable() {
    const val = document.getElementById('tableInput').value.trim();
    const badge = document.getElementById('tableBadge');
    if (val) {
      badge.textContent = 'MASA ' + val;
      badge.style.display = 'inline-block';
    } else {
      badge.style.display = 'none';
    }
  }
  
  function triggerPrint() {
    window.print();
  }
</script>
</head>
<body>

<!-- Control Panel -->
<div class="control-panel">
  <h3>Poster Ayarları</h3>
  <p style="font-size: 12px; color: var(--text-light)">Yazıcıdan çıktı almadan önce masa numarası yazabilirsiniz. Masa numarası boş bırakılırsa rozet gizlenir.</p>
  <div class="input-row">
    <input type="text" id="tableInput" placeholder="Örn: 5" value="<?= htmlspecialchars($tableNumber) ?>" onkeyup="updateTable()">
    <button class="btn-print" onclick="triggerPrint()">🖨 Yazdır / Kaydet</button>
  </div>
  <p style="font-size: 11px; color: rgba(201,168,76,0.85); font-weight: 500">QR Hedef URL: <span style="font-family: monospace; font-size: 10px; color: var(--dark)"><?= htmlspecialchars($qrTargetUrl) ?></span></p>
</div>

<!-- Printable Card Wrapper -->
<div class="qr-card-wrapper">
  <!-- Elegant Borders & Accents -->
  <div class="inner-border"></div>
  <div class="inner-border-2"></div>
  <div class="corner-deco c-tl"></div>
  <div class="corner-deco c-tr"></div>
  <div class="corner-deco c-bl"></div>
  <div class="corner-deco c-br"></div>

  <!-- Brand logo -->
  <div class="logo-section">
    <img src="../imgs/logo.webp" class="logo-img" alt="Madame Patisserie">
    <div class="brand-script">Zarafet &amp; Lezzet</div>
    <div class="table-badge" id="tableBadge" style="<?= empty($tableNumber) ? 'display: none;' : '' ?>">MASA <?= htmlspecialchars($tableNumber) ?></div>
  </div>

  <!-- Instruction Text -->
  <div class="instruction-section">
    <h2 class="inst-title">Temassız<br>Dijital Menü</h2>
    <p class="inst-sub">Okutun &amp; Keşfedin</p>
  </div>

  <!-- QR Frame -->
  <div class="qr-frame">
    <!-- QR Code Server API with dark brown color #1a1511 and warm cream background #faf6f0 -->
    <?php
      $qrDataEncoded = urlencode($qrTargetUrl);
      $qrCodeApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&color=1a1511&bgcolor=faf6f0&data={$qrDataEncoded}";
    ?>
    <img class="qr-img" src="<?= $qrCodeApiUrl ?>" alt="Menü QR Kodu">
  </div>

  <!-- Greetings -->
  <div class="footer-greeting">
    Keyifli anlar dileriz...
  </div>
  
  <!-- Direct Link Print Hint -->
  <div class="link-info">
    <?= htmlspecialchars($qrTargetUrl) ?>
  </div>
</div>

<!-- Otomatik Güvenli Çıkış (İnaktivite Takibi ve Sekmeler Arası Eşleme) -->
<script>
(function() {
    const timeoutDuration = <?= SESSION_TIMEOUT ?> * 1000; // milisaniye cinsinden
    let lastActivity = Date.now();

    // Son aktivite zamanını localStorage'a yazarak diğer sekmelerle senkronize ediyoruz
    localStorage.setItem('admin_last_activity', lastActivity);

    function updateActivity() {
        lastActivity = Date.now();
        localStorage.setItem('admin_last_activity', lastActivity);
    }

    // Kullanıcının aktif olduğunu gösteren olayları dinle
    const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
    events.forEach(eventName => {
        document.addEventListener(eventName, updateActivity, true);
    });

    // Diğer sekmelerdeki hareketleri dinle
    window.addEventListener('storage', (e) => {
        if (e.key === 'admin_last_activity') {
            lastActivity = parseInt(e.newValue, 10);
        }
    });

    // Her saniye kontrol et
    const interval = setInterval(() => {
        const inactiveTime = Date.now() - lastActivity;
        if (inactiveTime >= timeoutDuration) {
            clearInterval(interval);
            // Oturumu kapatmak üzere yönlendir
            window.location.href = 'logout.php?reason=timeout';
        }
    }, 1000);
})();
</script>

</body>
</html>
