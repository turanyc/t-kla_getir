<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Madame Patisserie & Coffee galeri — Mekanımız, ürünlerimiz ve mutfağımızdan kareler.">
  <title>Galeri — Madame Patisserie & Coffee</title>
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
        <a href="qr.php" class="nav__link">Menü</a>
        <a href="galeri.php" class="nav__link active">Galeri</a>
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
        <source src="videos/kruvasante.mp4" type="video/mp4">
      </video>
    </div>
    <div class="page-hero__overlay"></div>
    <div class="page-hero__content">
      <span class="script-text">Anlarımız</span>
      <h1>Galeri</h1>
      <p>Mekanımızdan, mutfağımızdan ve sunumlarımızdan kareler.</p>
    </div>
    <div class="page-hero__scroll">
      <span>Keşfet</span>
      <div class="scroll-line"></div>
    </div>
  </section>

  <!-- PHOTO GALLERY -->
  <section class="section-padding">
    <div class="container">
      <div style="text-align:center; margin-bottom:4rem; position:relative;" class="reveal">
        <span class="label">Fotoğraflar</span>
        <hr class="divider" style="margin:1rem auto;">
        <h2>Görsel Dünyamız</h2>
        <p class="body-text" style="margin:1rem auto 0; text-align:center;">Her fotoğraf bir hikaye anlatır. Madame Patisserie'nin büyülü dünyasına göz atın.</p>

        <!-- 3D Decorative Elements -->
        <div class="deco-3d deco-3d--float" style="top:-40px; right:-60px;">
          <div class="deco-donut"></div>
        </div>
        <div class="deco-3d deco-3d--float-delayed" style="top:20px; left:-50px;">
          <div class="deco-macaron">
            <div class="deco-macaron__top" style="background:linear-gradient(135deg,#849c49,#a0b56a);"></div>
            <div class="deco-macaron__fill"></div>
            <div class="deco-macaron__bottom" style="background:linear-gradient(135deg,#a0b56a,#849c49);"></div>
          </div>
        </div>
      </div>

      <div class="gallery-grid">
        <!-- Column 1 items -->
        <div class="gallery-item reveal reveal-delay-1" data-lightbox="imgs/hero_bg.webp">
          <img loading="lazy" src="imgs/kahve_kru.jpg" alt="Patisserie mekan">
          <div class="gallery-item__overlay">
            <span class="gallery-item__caption">Mekanımız</span>
          </div>
        </div>

        <div class="gallery-item reveal reveal-delay-2" data-lightbox="imgs/waffle.jpg">
          <img loading="lazy" src="imgs/wafflee.jpg" alt="Tatlılar">
          <div class="gallery-item__overlay">
            <span class="gallery-item__caption">Özel Tatlılar</span>
          </div>
        </div>

        <div class="gallery-item reveal reveal-delay-1" data-lightbox="imgs/chef_hands.png">
          <img loading="lazy" src="imgs/chef_hands.jpg" alt="Şef elleri">
          <div class="gallery-item__overlay">
            <span class="gallery-item__caption">Kahvaltı Sunumu</span>
          </div>
        </div>

        <!-- Column 2 items -->
        <div class="gallery-item reveal reveal-delay-2" data-lightbox="imgs/gallery_1.webp">
          <img loading="lazy" src="imgs/paket.jpg" alt="Galeri">
          <div class="gallery-item__overlay">
           
          </div>
        </div>

        <div class="gallery-item reveal reveal-delay-3" data-lightbox="imgs/about_interior.webp">
          <img loading="lazy" src="imgs/sunum.jpg" alt="İç mekan">
          <div class="gallery-item__overlay">
            
          </div>
        </div>

        <div class="gallery-item reveal reveal-delay-1" data-lightbox="imgs/banner_6a25a0685cb5a.webp">
          <img loading="lazy" src="imgs/pastate.jpg" alt="Pastalar">
          <div class="gallery-item__overlay">
            
          </div>
        </div>

        <!-- Column 3 items -->
        <div class="gallery-item reveal reveal-delay-2" data-lightbox="imgs/gallery_2.webp">
          <img loading="lazy" src="imgs/sosis.jpg" alt="Galeri 2">
          <div class="gallery-item__overlay">
           
          </div>
        </div>

        <div class="gallery-item reveal reveal-delay-3" data-lightbox="imgs/banner_6a25a068b9cdd.webp">
          <img loading="lazy" src="imgs/pasta_super.jpg" alt="Kahvaltı">
          <div class="gallery-item__overlay">
            
          </div>
        </div>

        <div class="gallery-item reveal reveal-delay-1" data-lightbox="imgs/gallery_3.webp">
          <img loading="lazy" src="imgs/premium.jpg" alt="Galeri 3">
          <div class="gallery-item__overlay">
           
          </div>
        </div>

        <div class="gallery-item reveal reveal-delay-2" data-lightbox="imgs/chocolate_tart.png">
          <img loading="lazy" src="imgs/kahvaltı.jpg" alt="Çikolatalı Tart">
          <div class="gallery-item__overlay">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- VIDEO GALLERY -->
  <section class="section-padding dark-section">
    <div class="container">
      <div style="text-align:center; margin-bottom:4rem; position:relative;" class="reveal">
        <span class="label" style="color:var(--gold-light);">Videolar</span>
        <hr class="divider" style="margin:1rem auto;">
        <h2 style="color:var(--cream);">Mutfağımızdan Kareler</h2>
        <p class="body-text" style="margin:1rem auto 0; text-align:center;">Ustalık ve tutkunun buluştuğu anları izleyin.</p>

        <!-- 3D floating elements -->
        <div class="deco-3d deco-3d--float-slow" style="bottom:-30px; right:-40px;">
          <div class="deco-cup">
            <div class="deco-cup__handle"></div>
            <div class="deco-cup__steam"><span></span><span></span><span></span></div>
          </div>
        </div>
        <div class="deco-3d deco-3d--float" style="top:0; left:-30px;">
          <div class="deco-croissant" style="width:55px; height:32px;"></div>
        </div>
      </div>

      <div class="grid-2">

        <div class="video-showcase reveal reveal-delay-1" style="aspect-ratio:16/9;">
          <video muted loop playsinline>
            <source src="videos/coffe.mp4" type="video/mp4">
          </video>
          <div class="video-showcase__play">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
          </div>
        </div>

        <div class="video-showcase reveal reveal-delay-2" style="aspect-ratio:16/9;">
          <video muted loop playsinline>
            <source src="videos/biskuvi.mp4" type="video/mp4">
          </video>
          <div class="video-showcase__play">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
          </div>
        </div>

        <div class="video-showcase reveal reveal-delay-3" style="aspect-ratio:16/9;">
          <video muted loop playsinline>
            <source src="videos/cake.mp4" type="video/mp4">
          </video>
          <div class="video-showcase__play">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
          </div>
        </div>

        <div class="video-showcase reveal reveal-delay-4" style="aspect-ratio:16/9;">
          <video muted loop playsinline>
            <source src="videos/kruvasante.mp4" type="video/mp4">
          </video>
          <div class="video-showcase__play">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
          </div>
        </div>

      </div>

      <!-- Full width video -->
      <div class="video-showcase reveal-scale" style="margin-top:1.5rem; aspect-ratio:21/9;">
        <video muted loop playsinline>
          <source src="videos/cakes.mp4" type="video/mp4">
        </video>
        <div class="video-showcase__play">
          <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
        </div>
      </div>

    </div>
  </section>

  <!-- CTA -->
  <section class="section-padding cta-section" style="background:var(--cream-dark);">
    <div class="container container--narrow reveal">
      <span class="label">Bize Ulaşın</span>
      <hr class="divider" style="margin:1rem auto;">
      <h2>Bir Parça<br>Olmak İster misiniz?</h2>
      <p class="body-text" style="margin:1rem auto 2.5rem; text-align:center;">Özel etkinlikler, işbirlikleri ve daha fazlası için bizimle iletişime geçin.</p>
      <div class="cta-section__buttons">
        <a href="iletisim.php" class="btn btn--gold">
          İletişime Geç
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
        <a href="menu.php" class="btn btn--outline">Menüyü İncele</a>
      </div>
    </div>
  </section>

  <!-- LIGHTBOX -->
  <div class="lightbox" id="lightbox">
    <button class="lightbox__close" aria-label="Kapat">✕</button>
    <div class="lightbox__content"></div>
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
            <li><a href="qr" style="color:var(--gold-light);">📱 Dijital QR Menü</a></li>
            <li><a href="admin/" style="color:var(--gold-light);">⚙ Yönetim Paneli</a></li>
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
