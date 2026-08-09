<?php
require_once __DIR__ . '/config.php';

$menuData = getMenuData();
$categories = $menuData['categories'];
$items      = $menuData['items'];

// Group items by category (only active ones)
$itemsByCat = [];
foreach ($items as $item) {
    if ($item['active']) {
        $itemsByCat[$item['cat']][] = $item;
    }
}

// Convert data to JSON for JavaScript
$menuJson = json_encode($itemsByCat, JSON_UNESCAPED_UNICODE);
$catsJson  = json_encode($categories,  JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?= htmlspecialchars(SITE_NAME) ?> | Dijital Menü</title>
<meta name="description" content="Madame Patisserie & Cafe dijital menüsü - Masadan sipariş verin">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --cream: #faf6f0;
    --cream2: #ffffff;
    --gold: #c9a84c;
    --gold-light: #e2c47e;
    --gold-dark: #8a6a1f;
    --dark: #1a1511;
    --dark-2: #2c2219;
    --text: #3a2e22;
    --text-light: #7a6a58;
    --white: #ffffff;
    --card-bg: #ffffff;
    --border: #e8dcc8;
    --green: #4a7c59;
    --shadow: 0 4px 24px rgba(26,21,17,0.06);
  }

  /* LOADING */
  #loader {
    position: fixed; inset: 0; z-index: 9999;
    background: #0f0a06; /* Şık pastane koyu kahve arka planı */
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 20px;
    transition: opacity 0.6s cubic-bezier(0.25, 1, 0.5, 1), visibility 0.6s;
    background-image: 
      radial-gradient(circle at 50% 50%, rgba(201,168,76,0.08) 0%, transparent 60%),
      url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M40 0l40 40-40 40L0 40z' fill='%23c9a84c' fill-opacity='0.006' fill-rule='evenodd'/%3E%3C/svg%3E");
  }
  #loader.hidden { opacity: 0; visibility: hidden; }
  
  .loader-art-container {
    position: relative;
    width: 140px; height: 140px;
    display: flex; align-items: center; justify-content: center;
  }
  
  .loader-svg {
    filter: drop-shadow(0 0 10px rgba(201, 168, 76, 0.45));
  }
  
  /* SVG Çizim ve Buhar Animasyonları */
  .croissant-line {
    stroke-dasharray: 100;
    stroke-dashoffset: 100;
    animation: drawCroissant 2.2s cubic-bezier(0.4, 0, 0.2, 1) forwards;
  }
  .crescent-1 { animation-delay: 0.1s; }
  .crescent-2 { animation-delay: 0.3s; }
  .crescent-3 { animation-delay: 0.5s; }
  .horn-l { animation-delay: 0.6s; }
  .horn-r { animation-delay: 0.7s; }
  
  .steam {
    stroke-dasharray: 30;
    stroke-dashoffset: 30;
    animation: riseSteam 2.2s ease-in-out infinite;
  }
  .steam-1 { animation-delay: 0.2s; }
  .steam-2 { animation-delay: 0.8s; }
  .steam-3 { animation-delay: 1.4s; }
  
  @keyframes drawCroissant {
    to { stroke-dashoffset: 0; }
  }
  
  @keyframes riseSteam {
    0% { stroke-dashoffset: 30; opacity: 0; transform: translateY(6px); }
    50% { stroke-dashoffset: 0; opacity: 0.75; }
    100% { stroke-dashoffset: -30; opacity: 0; transform: translateY(-10px); }
  }
  
  .loader-logo-text {
    font-family: 'Cormorant Garamond', serif;
    font-size: 32px;
    font-weight: 600;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: 2px;
    margin-top: 10px;
    text-align: center;
    opacity: 0;
    transform: translateY(10px);
    animation: fadeInUp 0.8s ease forwards 0.7s;
  }
  
  @media (max-width: 768px) {
    .loader-logo-text { font-size: 24px; }
  }
  
  .loader-status {
    font-family: 'Jost', sans-serif;
    font-size: 11px;
    color: rgba(250, 246, 240, 0.45);
    letter-spacing: 4px;
    text-transform: uppercase;
    opacity: 0;
    transform: translateY(10px);
    animation: fadeInUp 0.8s ease forwards 0.9s;
    margin-top: 5px;
    text-align: center;
  }
  
  .loader-bar-container {
    width: 140px; height: 1px;
    background: rgba(201, 168, 76, 0.12);
    border-radius: 1px;
    overflow: hidden;
    margin-top: 12px;
    opacity: 0;
    animation: fadeInOnly 0.8s ease forwards 0.7s;
  }
  
  .loader-bar-fill {
    height: 100%; width: 0;
    background: var(--gold);
    animation: fillProgress 1.6s cubic-bezier(0.1, 0.8, 0.1, 1) forwards;
  }
  
  @keyframes fadeInUp {
    to { opacity: 1; transform: translateY(0); }
  }
  @keyframes fadeInOnly {
    to { opacity: 1; }
  }
  @keyframes fillProgress {
    to { width: 100%; }
  }

  /* PRODUCT MODAL */
  #pModal {
    position: fixed; inset: 0; z-index: 500;
    background: rgba(26,21,17,0.6);
    display: none; align-items: center; justify-content: center;
    padding: 24px;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
  }
  #pModal.open { display: flex; }
  .pm-box {
    background: var(--white); border-radius: 20px;
    width: 100%; max-width: 340px;
    overflow: hidden;
    position: relative;
    animation: popIn 0.3s cubic-bezier(.34,1.56,.64,1);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border);
  }
  @keyframes popIn { from{transform:scale(0.8);opacity:0} to{transform:scale(1);opacity:1} }
  .pm-img { 
    width: 100%; 
    height: auto; 
    aspect-ratio: 1 / 1; 
    object-fit: contain; 
    background-color: var(--cream); 
    display: block; 
  }
  .pm-body { padding: 20px; }
  .pm-name { font-family:'Cormorant Garamond',serif; font-size:22px; font-weight:600; color:var(--dark); margin-bottom:6px; }
  .pm-desc { font-size:13px; color:var(--text-light); margin-bottom:16px; line-height:1.5; font-weight: 300; }
  .pm-footer { display:flex; align-items:center; justify-content:space-between; }
  .pm-price { font-family:'Cormorant Garamond',serif; font-size:22px; font-weight:700; color:var(--gold-dark); }
  .pm-close {
    position:absolute; top:12px; right:12px;
    background:rgba(255,255,255,0.9); border:none;
    width:32px; height:32px; border-radius:50%;
    font-size:16px; cursor:pointer; display:flex;
    align-items:center; justify-content:center;
    box-shadow:0 2px 8px rgba(0,0,0,0.15);
    z-index: 10;
  }
  .pm-zoom {
    position:absolute; top:12px; left:12px;
    background:rgba(255,255,255,0.9); border:none;
    width:32px; height:32px; border-radius:50%;
    cursor:pointer; display:flex;
    align-items:center; justify-content:center;
    box-shadow:0 2px 8px rgba(0,0,0,0.15);
    color: var(--dark);
    transition: background 0.2s, transform 0.15s;
    z-index: 10;
  }
  .pm-zoom:hover { background: #ffffff; }
  .pm-zoom:active { transform: scale(0.9); }

  /* FULLSCREEN IMAGE MODAL */
  #fsImageModal {
    position: fixed; inset: 0; z-index: 1000;
    background: rgba(26,21,17,0.95);
    display: none; align-items: center; justify-content: center;
    padding: 16px;
    backdrop-filter: blur(10px);
  }
  #fsImageModal.open { display: flex; }
  .fs-img-content {
    max-width: 95%; max-height: 85vh;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
    transform: scale(0.9);
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    object-fit: contain;
    border: 1.5px solid var(--gold);
  }
  #fsImageModal.open .fs-img-content {
    transform: scale(1);
  }
  .fs-close {
    position: absolute; top: 20px; right: 20px;
    background: rgba(255,255,255,0.15); border: none;
    width: 40px; height: 40px; border-radius: 50%;
    color: #fff; font-size: 20px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background 0.2s, transform 0.2s;
    z-index: 1010;
  }
  .fs-close:hover { background: rgba(255,255,255,0.3); }

  body {
    font-family: 'Jost', sans-serif;
    background: var(--cream);
    color: var(--text);
    min-height: 100vh;
    max-width: 480px;
    margin: 0 auto;
    position: relative;
    padding-bottom: 24px;
    box-shadow: 0 0 30px rgba(26,21,17,0.05);
  }

  .hero { position: relative; height: 230px; overflow: hidden; border-radius: 0 0 28px 28px; }
  .hero-video { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.65); display: block; }
  .hero img { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.65); display: none; }
  .hero img.active-img { display: block; }
  .hero-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(26,21,17,0.4)); }
  
  .home-btn {
    position: absolute; top: 16px; left: 16px; z-index: 100;
    background: rgba(255,255,255,0.9); border-radius: 50%;
    width: 36px; height: 36px; display: flex;
    align-items: center; justify-content: center;
    text-decoration: none; color: var(--dark);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15); font-size: 16px;
    transition: transform 0.2s;
  }
  .home-btn:active { transform: scale(0.9); }

  .header { text-align: center; padding: 22px 20px 16px; background: var(--cream); position: relative; }
  .header::after { content:''; display:block; height:1px; margin:16px -20px 0; background: linear-gradient(90deg,transparent,var(--border),transparent); }
  .logo-wrap { display: flex; justify-content: center; align-items: center; }
  .logo-wrap img { width: 130px; height: auto; object-fit: contain; }
  .header-subtitle { font-size: 11px; color: var(--text-light); font-weight: 500; letter-spacing: 2px; text-transform: uppercase; margin-top: 6px; }

  .section-title { font-family: 'Cormorant Garamond', serif; font-size: 20px; font-weight: 600; color: var(--dark); padding: 20px 16px 10px; letter-spacing: 0.5px; }
  .menu-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding: 0 16px; }
  .menu-card {
    background: var(--white); border-radius: 16px; overflow: hidden;
    box-shadow: var(--shadow); border: 1px solid var(--border);
    transition: transform 0.22s ease, box-shadow 0.22s ease;
    cursor: pointer; animation: fadeUp 0.4s ease both;
  }
  @keyframes fadeUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
  .menu-card:active { transform: scale(0.97); }
  .card-img { 
    width: 100%; 
    aspect-ratio: 1 / 1; 
    object-fit: contain; 
    background-color: var(--cream); 
    display: block; 
    transition: transform 0.35s ease; 
  }
  .card-body { padding: 12px; }
  .card-name { font-family: 'Cormorant Garamond', serif; font-weight: 600; font-size: 15px; color: var(--dark); line-height: 1.3; margin-bottom: 3px; }
  .card-desc { font-size: 11px; color: var(--text-light); line-height: 1.4; margin-bottom: 8px; min-height: 28px; font-weight: 300; }
  .card-footer { display: flex; align-items: center; justify-content: space-between; }
  .card-price { font-family: 'Cormorant Garamond', serif; font-size: 16px; font-weight: 700; color: var(--gold-dark); }

  .menu-section { display: none; }
  .menu-section.active { display: block; }

  .toast {
    position: fixed; bottom: 40px; left: 50%;
    transform: translateX(-50%) translateY(20px);
    background: var(--dark); color: white;
    padding: 10px 22px; border-radius: 20px;
    font-size: 13px; font-weight: 500;
    opacity: 0; transition: all 0.3s ease; z-index: 300;
    pointer-events: none;
  }
  .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

  /* VIEWS SYSTEM */
  .view-container {
    display: none;
    animation: fadeInView 0.4s ease both;
  }
  .view-container.active {
    display: block;
  }
  @keyframes fadeInView {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* CATEGORIES VIEW (GRID SCREEN) */
  .categories-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
    padding: 0 16px 24px;
  }
  .category-card {
    background: var(--white);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    cursor: pointer;
    transition: all 0.25s ease;
    position: relative;
    aspect-ratio: 1 / 1.05;
  }
  .category-card:active {
    transform: scale(0.97);
  }
  .cat-card-img-wrap {
    width: 100%;
    height: 68%;
    position: relative;
    overflow: hidden;
  }
  .cat-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.45s ease;
  }
  .cat-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0) 60%, rgba(26,21,17,0.15));
  }
  .cat-card-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 2px;
    padding: 8px 10px;
    height: 32%;
    text-align: center;
    background: var(--white);
  }
  .cat-card-name {
    font-family: 'Cormorant Garamond', serif;
    font-size: 16px;
    font-weight: 600;
    color: var(--dark);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
  }
  .cat-card-count {
    font-size: 11px;
    color: var(--text-light);
    font-weight: 400;
  }

  /* PRODUCTS VIEW HEADER */
  .products-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: var(--white);
    border-bottom: 1px solid var(--border);
    margin-bottom: 12px;
  }
  .back-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    background: none;
    border: none;
    font-family: 'Jost', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: var(--gold-dark);
    cursor: pointer;
    padding: 6px 12px;
    border-radius: 8px;
    transition: all 0.2s;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .back-btn:active {
    background: var(--cream);
  }
  .active-category-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
  }

  /* FOOTER */
  .menu-footer {
    text-align: center;
    padding: 32px 16px 28px;
    background: var(--white);
    border-top: 1px solid var(--border);
    margin-top: 36px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
  }
  .footer-social a {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: var(--dark);
    text-decoration: none;
    transition: color 0.2s;
  }
  .footer-social a:active {
    color: var(--gold-dark);
  }
  .insta-icon {
    color: var(--gold);
  }
  .footer-links a {
    font-size: 12px;
    color: var(--text-light);
    text-decoration: none;
    border-bottom: 1px dashed var(--border);
    padding-bottom: 2px;
  }
  .footer-copyright {
    font-size: 11px;
    color: var(--text-light);
    opacity: 0.8;
  }

  .card-img, .cat-card-img, .pm-img {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
  }
  .card-img.loaded, .cat-card-img.loaded, .pm-img.loaded {
    opacity: 1;
  }
</style>
</head>
<body>

<!-- LOADER -->
<div id="loader">
  <div class="loader-art-container">
    <svg width="120" height="120" viewBox="0 0 100 100" class="loader-svg">
      <!-- Buhar Dalgaları -->
      <path class="steam steam-1" d="M 40,25 Q 36,15 42,5" fill="none" stroke="url(#goldGradient)" stroke-width="2.5" stroke-linecap="round"/>
      <path class="steam steam-2" d="M 50,22 Q 55,12 47,5" fill="none" stroke="url(#goldGradient)" stroke-width="2.5" stroke-linecap="round"/>
      <path class="steam steam-3" d="M 60,25 Q 64,15 58,5" fill="none" stroke="url(#goldGradient)" stroke-width="2.5" stroke-linecap="round"/>
      
      <!-- Kruvasan Çizimi -->
      <path class="croissant-line crescent-1" d="M 30,55 Q 50,32 70,55 Q 50,78 30,55 Z" fill="none" stroke="url(#goldGradient)" stroke-width="3" stroke-linejoin="round"/>
      <path class="croissant-line crescent-2" d="M 38,51 Q 50,38 62,51" fill="none" stroke="url(#goldGradient)" stroke-width="2.5"/>
      <path class="croissant-line crescent-3" d="M 44,48 Q 50,42 56,48" fill="none" stroke="url(#goldGradient)" stroke-width="2.5"/>
      <path class="croissant-line horn-l" d="M 30,55 Q 18,58 10,49 Q 20,42 32,52" fill="none" stroke="url(#goldGradient)" stroke-width="2.5"/>
      <path class="croissant-line horn-r" d="M 70,55 Q 82,58 90,49 Q 80,42 68,52" fill="none" stroke="url(#goldGradient)" stroke-width="2.5"/>
      
      <defs>
        <linearGradient id="goldGradient" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#c9a84c"/>
          <stop offset="100%" stop-color="#e2c47e"/>
        </linearGradient>
      </defs>
    </svg>
  </div>
  <div class="loader-logo-text">Madame Patisserie & Coffee</div>
  <div class="loader-status">Fırından taze kokular geliyor...</div>
  <div class="loader-bar-container"><div class="loader-bar-fill"></div></div>
</div>

<!-- HERO -->
<div class="hero">
  <video class="hero-video" autoplay muted loop playsinline id="heroVideo">
    <source src="videos/cakes.mp4" type="video/mp4">
  </video>
  <img loading="lazy" id="heroFallback" src="imgs/hero_bg.webp" alt="Madame Cafe" onload="this.classList.add('loaded')" onerror="this.src='imgs/hero.webp'; this.classList.add('loaded')">
  <div class="hero-overlay"></div>
</div>

<!-- HEADER -->
<div class="header">
  <div class="logo-wrap">
    <img loading="lazy" src="imgs/logo.webp" alt="<?= htmlspecialchars(SITE_NAME) ?> Logo" onerror="this.style.display='none'; document.getElementById('logo-text').style.display='block'">
    <div id="logo-text" style="display:none; font-family:'Cormorant Garamond',serif; font-size:26px; font-weight:700; background:linear-gradient(135deg,#c9a84c,#8a6a1f); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">Madame<br><span style="font-size:12px; font-family:'Jost',sans-serif; font-weight:400; -webkit-text-fill-color:#7a6a58;">Patisserie &amp; Cafe</span></div>
  </div>
  <div class="header-subtitle">Hoş Geldiniz</div>
</div>

<!-- CATEGORIES VIEW (First Screen) -->
<div id="categories-view" class="view-container active">
  <p class="section-title">Kategoriler</p>
  <div class="categories-grid">
    <?php 
    foreach ($categories as $cat): 
        $itemCount = isset($itemsByCat[$cat['id']]) ? count($itemsByCat[$cat['id']]) : 0;
    ?>
    <div class="category-card" onclick="openCategory('<?= htmlspecialchars($cat['id']) ?>')">
      <div class="cat-card-img-wrap">
        <img loading="lazy" class="cat-card-img" src="imgs/<?= htmlspecialchars($cat['img']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" onload="this.classList.add('loaded')" onerror="this.src='imgs/croissant.webp'; this.classList.add('loaded')">
        <div class="cat-card-overlay"></div>
      </div>
      <div class="cat-card-info">
        <span class="cat-card-name"><?= htmlspecialchars($cat['name']) ?></span>
        <span class="cat-card-count"><?= $itemCount ?> Ürün</span>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- PRODUCTS VIEW (Second Screen) -->
<div id="products-view" class="view-container">
  <div class="products-header">
    <button class="back-btn" onclick="showCategoriesView()">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      Geri
    </button>
    <div class="active-category-title" id="activeCategoryName"></div>
  </div>

  <!-- MENU SECTIONS -->
  <?php 
  $activeCount = 0;
  foreach ($categories as $cat): 
      $activeClass = $activeCount === 0 ? 'active' : '';
      $activeCount++;
  ?>
  <div class="menu-section <?= $activeClass ?>" id="sec-<?= htmlspecialchars($cat['id']) ?>">
    <div class="menu-grid" id="grid-<?= htmlspecialchars($cat['id']) ?>"></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- PRODUCT MODAL -->
<div id="pModal" onclick="closePModal(event)">
  <div class="pm-box" id="pmBox">
    <div style="position: relative; overflow: hidden; cursor: zoom-in;" onclick="openFsImage()">
      <img loading="lazy" class="pm-img" id="pmImg" src="" alt="">
      <div class="pm-zoom" title="Tam Ekran Görsel">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>
        </svg>
      </div>
    </div>
    <button class="pm-close" onclick="document.getElementById('pModal').classList.remove('open')">✕</button>
    <div class="pm-body">
      <div class="pm-name" id="pmName"></div>
      <div class="pm-desc" id="pmDesc"></div>
      <div class="pm-footer" style="justify-content: center;">
        <span class="pm-price" id="pmPrice"></span>
      </div>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer class="menu-footer">
  <div class="footer-social">
    <a href="https://www.instagram.com/madamepatisseriee/" target="_blank" rel="noopener noreferrer">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="insta-icon"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><circle cx="12" cy="12" r="4"></circle><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
      @madamepatisseriee
    </a>
  </div>
  <div class="footer-links">
    <a href="#" onclick="openKvkkModal(event)">KVKK Aydınlatma Metni</a>
  </div>
  <div class="footer-copyright">
    © 2026 <?= htmlspecialchars(SITE_NAME) ?>
  </div>
</footer>

<!-- KVKK MODAL -->
<div id="kvkkModal" class="modal-overlay" onclick="closeKvkkModal(event)" style="position: fixed; inset: 0; background: rgba(26,21,17,0.6); z-index: 600; display: none; align-items: flex-end; justify-content: center; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
  <div class="modal" style="background: var(--white); width: 100%; max-width: 480px; border-radius: 24px 24px 0 0; padding: 24px 20px 36px; transform: translateY(100%); transition: transform 0.3s ease; border: 1px solid var(--border); border-bottom: none;">
    <div class="modal-handle" style="width: 40px; height: 4px; background: var(--border); border-radius: 2px; margin: 0 auto 16px;"></div>
    <div class="modal-title" style="font-family: 'Cormorant Garamond', serif; font-size: 20px; font-weight: 700; color: var(--dark); margin-bottom: 20px; text-align: center;">KVKK Aydınlatma Metni</div>
    <div class="kvkk-content" style="max-height: 280px; overflow-y: auto; text-align: left; font-size: 13px; color: var(--text-light); line-height: 1.6; padding: 0 10px 10px; font-weight: 300;">
      <p><strong>Değerli Misafirlerimiz,</strong></p><br>
      <p>Madame Patisserie & Cafe olarak kişisel verilerinizin güvenliği hususuna azami hassasiyet göstermekteyiz. 6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") uyarınca, veri sorumlusu sıfatıyla, kişisel verilerinizi mevzuata uygun olarak işleyeceğimizi taahhüt ederiz.</p><br>
      <p><strong>1. Verilerin İşlenme Amacı:</strong> Kişisel verileriniz, sizlere sunduğumuz hizmetlerin geliştirilmesi, memnuniyetinizin artırılması ve yasal yükümlülüklerimizin yerine getirilmesi amacıyla işlenmektedir.</p><br>
      <p><strong>2. Verilerin Paylaşılması:</strong> Toplanan kişisel verileriniz, yasal zorunluluklar haricinde üçüncü şahıslarla asla paylaşılmamaktadır.</p><br>
      <p><strong>3. Haklarınız:</strong> Kanun kapsamında verilerinizin silinmesini, düzeltilmesini talep etme ve verilerinizin işlenip işlenmediğini öğrenme hakkına sahipsiniz. Detaylı bilgi için işletmemizle iletişime geçebilirsiniz.</p>
    </div>
    <button class="confirm-btn" onclick="document.getElementById('kvkkModal').classList.remove('open')" style="width: 100%; background: linear-gradient(135deg, var(--gold), var(--gold-light)); color: var(--dark); border: none; border-radius: 12px; padding: 14px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: 'Jost', sans-serif; text-transform: uppercase; margin-top: 15px; letter-spacing: 0.5px;">Kapat</button>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<!-- Fullscreen Image Modal -->
<div id="fsImageModal" onclick="closeFsImageModal(event)">
  <button class="fs-close" onclick="document.getElementById('fsImageModal').classList.remove('open')">✕</button>
  <img loading="lazy" class="fs-img-content" id="fsImgTarget" src="" alt="Büyütülmüş Ürün Görseli">
</div>

<script>
// JSON Menu and Categories from PHP
const menuData = <?= $menuJson ?>;
const categories = <?= $catsJson ?>;

// Map product ID to object details
let allItems = {};
Object.values(menuData).flat().forEach(it => allItems[it.id] = it);

function openProduct(id) {
  const it = allItems[id];
  const pmImg = document.getElementById('pmImg');
  pmImg.classList.remove('loaded');
  pmImg.src = 'imgs/' + it.img;
  pmImg.alt = it.name;
  pmImg.onload = function() { this.classList.add('loaded'); };
  pmImg.onerror = function() { this.src = 'imgs/croissant.webp'; this.classList.add('loaded'); };
  document.getElementById('pmName').textContent = it.name;
  document.getElementById('pmDesc').textContent = it.desc;
  document.getElementById('pmPrice').textContent = '₺' + it.price;
  document.getElementById('pModal').classList.add('open');
}

function closePModal(e) {
  if (e.target === document.getElementById('pModal'))
    document.getElementById('pModal').classList.remove('open');
}

function renderGrid(cat) {
  const grid = document.getElementById('grid-' + cat);
  if (!grid) return;
  if (!menuData[cat] || menuData[cat].length === 0) {
    grid.innerHTML = `<div class="empty-category-message" style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: var(--text-light); font-size: 14px;">Bu kategoride henüz ürün bulunmamaktadır.</div>`;
    return;
  }
  grid.innerHTML = menuData[cat].map((item, i) => `
    <div class="menu-card" style="animation-delay:${i*0.06}s" onclick="openProduct(${item.id})">
      <img class="card-img" src="imgs/${item.img}" alt="${item.name}" loading="lazy" onload="this.classList.add('loaded')" onerror="this.src='imgs/croissant.webp'; this.classList.add('loaded')">
      <div class="card-body">
        <div class="card-name">${item.name}</div>
        <div class="card-desc">${item.desc}</div>
        <div class="card-footer">
          <span class="card-price">₺${item.price}</span>
        </div>
      </div>
    </div>
  `).join('');
}

// Render cards for loaded categories
categories.forEach(c => renderGrid(c.id));

function openCategory(catId) {
  document.querySelectorAll('.menu-section').forEach(s => s.classList.remove('active'));
  const sec = document.getElementById('sec-' + catId);
  if (sec) {
    sec.classList.add('active');
    sec.querySelectorAll('.menu-card').forEach((c,i) => {
      c.style.animation = 'none';
      c.offsetHeight;
      c.style.animation = `fadeUp 0.4s ease ${i*0.06}s both`;
    });
  }
  
  const catObj = categories.find(c => c.id === catId);
  if (catObj) {
    document.getElementById('activeCategoryName').textContent = catObj.name;
  }
  
  document.getElementById('categories-view').classList.remove('active');
  document.getElementById('products-view').classList.add('active');
  
  window.scrollTo({
    top: document.querySelector('.header').offsetTop,
    behavior: 'smooth'
  });
}

function showCategoriesView() {
  document.getElementById('products-view').classList.remove('active');
  document.getElementById('categories-view').classList.add('active');
  
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
}

function openFsImage() {
  const pmImg = document.getElementById('pmImg');
  const fsImgTarget = document.getElementById('fsImgTarget');
  if (pmImg && fsImgTarget) {
    fsImgTarget.src = pmImg.src;
    fsImgTarget.alt = pmImg.alt;
    document.getElementById('fsImageModal').classList.add('open');
  }
}

function closeFsImageModal(e) {
  document.getElementById('fsImageModal').classList.remove('open');
}

function openKvkkModal(e) {
  e.preventDefault();
  document.getElementById('kvkkModal').classList.add('open');
  document.querySelector('#kvkkModal .modal').style.transform = 'translateY(0)';
}

function closeKvkkModal(e) {
  if (e.target === document.getElementById('kvkkModal')) {
    document.getElementById('kvkkModal').classList.remove('open');
    document.querySelector('#kvkkModal .modal').style.transform = 'translateY(100%)';
  }
}

// Intercept video fail-over (e.g. on low power mode, slow connections)
document.addEventListener('DOMContentLoaded', () => {
  const video = document.getElementById('heroVideo');
  const fallback = document.getElementById('heroFallback');
  
  if (video) {
    video.play().then(() => {
      // video successfully started playing, hide static image
      fallback.style.display = 'none';
      video.classList.add('loaded');
    }).catch(err => {
      // autoplay blocked or video failed to load, display fallback image instead
      console.log("Video playback blocked or failed, loading fallback image: ", err);
      fallback.style.display = 'block';
      video.style.display = 'none';
    });
  }
});

window.addEventListener('load', () => {
  setTimeout(() => {
    const loader = document.getElementById('loader');
    if (loader) {
      loader.style.opacity = '0';
      loader.style.pointerEvents = 'none';
      setTimeout(() => loader.style.display = 'none', 500);
    }
  }, 2500);
});
</script>
</body>
</html>
