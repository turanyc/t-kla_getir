<?php
require_once __DIR__ . '/config.php';
$menuData = getMenuData();
$categories = $menuData['categories'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Madame Patisserie & Coffee menüsü — El yapımı kruvasanlar, pastalar, makaronlar ve özel kahveler.">
  <title>Menü — Madame Patisserie & Coffee</title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="imgs/logo.webp" type="image/webp">
</head>
<body>

  <!-- LOADER -->
  <div class="loader">
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

  <!-- NAVIGATION -->
  <nav class="nav nav--light" id="nav">
    <div class="nav__inner">
      <a href="index.php" class="nav__logo"><img loading="lazy" src="imgs/logo.webp" alt="Madame Patisserie"></a>
      <div class="nav__links" id="navLinks">
        <a href="index.php" class="nav__link">Ana Sayfa</a>
        <a href="hakkimizda.php" class="nav__link">Hakkımızda</a>
        <a href="qr.php" class="nav__link active">Menü</a>
        <a href="galeri.php" class="nav__link">Galeri</a>
        <a href="iletisim.php" class="nav__link">İletişim</a>
        <a href="https://www.instagram.com/madamepatisseriee/" target="_blank" class="nav__link" aria-label="Instagram" style="display:flex; align-items:center;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" width="18" height="18"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><circle cx="12" cy="12" r="5"></circle><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"></circle></svg>
        </a>
      </div>
      <button class="nav__toggle" id="navToggle" aria-label="Menüyü aç/kapat">
        <span></span><span></span><span></span>
      </button>
    </div>
  </nav>

  <!-- HERO -->
  <section class="page-hero">
    <div class="page-hero__video">
      <video autoplay muted loop playsinline>
        <source src="videos/biskuvi.mp4" type="video/mp4">
      </video>
    </div>
    <div class="page-hero__overlay"></div>
    <div class="page-hero__content">
      <span class="script-text">Özenle Hazırlandı</span>
      <h1>Menümüz</h1>
      <p>Her biri özenle hazırlanan seçkin lezzetlerimizi keşfedin.</p>
    </div>
    <div class="page-hero__scroll">
      <span>Keşfet</span>
      <div class="scroll-line"></div>
    </div>
  </section>

  <!-- CATEGORY BANNER CAROUSEL -->
  <section class="section-padding">
    <div class="container">
      <div style="text-align:center; margin-bottom:3rem;" class="reveal">
        <span class="label">Kategoriler</span>
        <hr class="divider" style="margin:1rem auto;">
        <h2>Lezzetlerimiz</h2>
      </div>

      <!-- Category banner (Limited to 6) -->
      <div class="reveal-scale" style="margin-bottom:3rem;">
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:1rem;">
          <?php 
          $displayCategories = array_slice($categories, 0, 6);
          foreach ($displayCategories as $cat): 
              $bannerImg = !empty($cat['banner']) ? $cat['banner'] : 'imgs/coffee.webp';
              if (strpos($bannerImg, 'img/') === 0) {
                  $bannerImg = str_replace('img/', 'imgs/', $bannerImg);
              }
          ?>
          <div class="img-soft" data-cat="<?= htmlspecialchars($cat['id']) ?>" style="border-radius:var(--radius-xl); overflow:hidden; aspect-ratio:3/4;">
            <img loading="lazy" src="<?= htmlspecialchars($bannerImg) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:var(--radius-xl);">
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      
      <!-- Link to QR Menu -->
      <div style="text-align:center; margin-top:2rem;" class="reveal">
        <a href="qr.php" class="btn btn--gold">
          Menüye Erişmek İçin Tıklayın
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </a>
      </div>
    </div>
  </section>

  <!-- FEATURED PRODUCT SHOWCASE -->
  <section class="section-padding dark-section">
    <div class="container container--wide">
      <div style="text-align:center; margin-bottom:3rem;" class="reveal">
        <span class="label" style="color:var(--gold-light);">Öne Çıkan</span>
        <hr class="divider" style="margin:1rem auto;">
        <h2 style="color:var(--cream);">Şefin Tercihi</h2>
      </div>

      <div class="showcase reveal-scale">
        <div class="showcase__media">
          <video autoplay muted loop playsinline>
            <source src="videos/cakes.mp4" type="video/mp4">
          </video>
        </div>
        <div class="showcase__info">
          <span class="label" style="color:var(--gold-light);">Sezonun Yıldızı</span>
          <hr class="divider">
          <h2>Fit<br>Bowl</h2>
          <p class="body-text">Sağlıklı ve lezzetli bowl çeşitlerimizle güne zinde bir başlangıç yapın veya gününüzü tatlandırın.</p>
        </div>
      </div>

      <!-- Secondary gallery -->
      <div class="grid-4" style="margin-top:1.5rem;">
        <div class="img-soft reveal reveal-delay-1" style="border-radius:var(--radius-lg); overflow:hidden; aspect-ratio:1;">
          <img loading="lazy" src="imgs/pasta_super.jpg" alt="Ürün" style="width:100%; height:100%; object-fit:cover; border-radius:var(--radius-lg);">
        </div>
        <div class="img-soft reveal reveal-delay-2" style="border-radius:var(--radius-lg); overflow:hidden; aspect-ratio:1;">
          <img loading="lazy" src="imgs/sunum.jpg" alt="Ürün" style="width:100%; height:100%; object-fit:cover; border-radius:var(--radius-lg);">
        </div>
        <div class="img-soft reveal reveal-delay-3" style="border-radius:var(--radius-lg); overflow:hidden; aspect-ratio:1;">
          <img loading="lazy" src="imgs/premium.jpg" alt="Ürün" style="width:100%; height:100%; object-fit:cover; border-radius:var(--radius-lg);">
        </div>
        <div class="img-soft reveal reveal-delay-4" style="border-radius:var(--radius-lg); overflow:hidden; aspect-ratio:1;">
          <img loading="lazy" src="imgs/wafflee.jpg" alt="Ürün" style="width:100%; height:100%; object-fit:cover; border-radius:var(--radius-lg);">
        </div>
      </div>

    </div>
  </section>

  <!-- MARQUEE -->
  <div class="marquee">
    <div class="marquee__track">
      <span class="marquee__item"><span class="marquee__dot"></span>Taze Pişmiş</span>
      <span class="marquee__item"><span class="marquee__dot"></span>El Yapımı</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Doğal Malzemeler</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Özel Seçim Tereyağı</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Hakiki Kakao Çikolatası</span>
      <span class="marquee__item"><span class="marquee__dot"></span>72 Saat Fermantasyon</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Taze Pişmiş</span>
      <span class="marquee__item"><span class="marquee__dot"></span>El Yapımı</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Doğal Malzemeler</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Özel Seçim Tereyağı</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Hakiki Kakao Çikolatası</span>
      <span class="marquee__item"><span class="marquee__dot"></span>72 Saat Fermantasyon</span>
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container">
      <div class="footer__grid">
        <div class="footer__brand">
          <img loading="lazy" src="imgs/logo.webp" alt="Madame Patisserie & Coffee">
          <p>Seçkin malzemeler ve ustalıkla hazırlanan el yapımı pastane ürünleri ve özel kahveler.</p>
        </div>
        <div class="footer__col">
          <h5>Keşfet</h5>
          <ul>
            <li><a href="index.php">Ana Sayfa</a></li>
            <li><a href="hakkimizda.php">Hakkımızda</a></li>
            <li><a href="menu.php">Menü</a></li>
            <li><a href="galeri.php">Galeri</a></li>
          </ul>
        </div>
        <div class="footer__col">
          <h5>Ziyaret</h5>
          <ul>
            <li><a href="#">Şevkiye, Şht. Şefik Uçak Sk. 40/E, 01500 Kozan/Adana</a></li>
            <li><a href="#">Her gün 08:00 — 22:00</a></li>
          </ul>
        </div>
        <div class="footer__col">
          <h5>İletişim</h5>
          <ul>
            <li><a href="tel:+905310230374">+90 531 023 03 74</a></li>
          </ul>
        </div>
      </div>
      <div class="footer__bottom">
        <p>&copy; 2026 Madame Patisserie & Coffee. Tüm hakları saklıdır.</p>
        <div class="footer__socials">
          <a href="#" class="footer__social" aria-label="Instagram"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg></a>
        </div>
      </div>
    </div>
  </footer>

  <script src="script.js"></script>
</body>
</html>
