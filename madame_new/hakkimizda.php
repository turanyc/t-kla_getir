<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Madame Patisserie & Coffee — Hikayemiz, değerlerimiz ve pastacılığa olan tutkumuz. Her ürünümüzde sevgi ve titizliği bir araya getiriyoruz.">
  <title>Hakkımızda — Madame Patisserie & Coffee</title>
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
        <a href="hakkimizda.php" class="nav__link active">Hakkımızda</a>
        <a href="qr.php" class="nav__link">Menü</a>
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
        <source src="videos/coffe.mp4" type="video/mp4">
      </video>
    </div>
    <div class="page-hero__overlay"></div>
    <div class="page-hero__content">
      <span class="script-text">Hikayemiz</span>
      <h1>Lezzetin Zarif Hali</h1>
      <p>Her lokmada bir hikaye, her detayda bir tutku. Mutfaktaki titizliğimizi ve işimize olan büyük sevgimizi her tarifte yaşatıyoruz.</p>
    </div>
    <div class="page-hero__scroll">
      <span>Keşfet</span>
      <div class="scroll-line"></div>
    </div>
  </section>

  <!-- MARQUEE -->
  <div class="marquee marquee--gold">
    <div class="marquee__track">
      <span class="marquee__item"><span class="marquee__dot"></span>Pastane</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Kahve</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Kruvasanlar</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Makaron</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Tatlılar</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Ekler</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Kahvaltı</span>
      <span class="marquee__item"><span class="marquee__dot"></span>El Yapımı</span>
      <!-- duplicate for seamless loop -->
      <span class="marquee__item"><span class="marquee__dot"></span>Pastane</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Kahve</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Kruvasanlar</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Makaron</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Tatlılar</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Ekler</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Kahvaltı</span>
      <span class="marquee__item"><span class="marquee__dot"></span>El Yapımı</span>
    </div>
  </div>

  <!-- OUR STORY -->
  <section class="section-padding" id="hikaye">
    <div class="container">
      <div class="grid-2-wide" style="align-items:center;">

        <div class="reveal-left">
          <span class="label">Biz Kimiz</span>
          <hr class="divider">
          <h2>Zarafetle<br>Yoğrulan Lezzet</h2>
          <p class="body-text" style="margin:1.5rem 0;">
            Madame Patisserie & Coffee, pastacılığa duyduğumuz derin sevgi ve mutfaktaki tavizsiz titizliğimizin bir eseri olarak kuruldu. Her tarifimiz, işimize olan bu tutkulu aşkın ve taze yerel tatların eşsiz uyumuyla şekillenmiştir.
          </p>
          <p class="body-text" style="margin-bottom:2rem;">
            Kurucularımız, en seçkin mutfaklarda edindikleri yılların tecrübesiyle, her tabakta gerçek zanaatkarlığı hissettirmektedir. Bizim için fırından çıkan her lezzet, sadece bir tarif değil; büyük bir adanmışlık ve mükemmeliyet arayışıyla yazılan bir hikayedir.
          </p>

          <blockquote style="font-family:var(--font-heading); font-size:1.4rem; font-style:italic; color:var(--text); border-left:2px solid var(--gold); padding-left:1.5rem; line-height:1.6;">
            "Biz pasta yapmıyoruz. Damakta ve hafızada iz bırakan anlar yaratıyoruz."
          </blockquote>
        </div>

        <div class="reveal-right">
          <div class="about-image-wrapper">
            <img loading="lazy" src="imgs/limonata.jpg" alt="madame" class="about-image-main">
            <div class="about-image-floating">
              <img loading="lazy" src="imgs/sosis.jpg" alt="sosis" class="about-image-sub">
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- VIDEO STRIP -->
  <section style="padding:0;">
    <div class="video-showcase reveal-scale" style="border-radius:0; aspect-ratio:21/7;">
      <video autoplay muted loop playsinline>
        <source src="videos/cake.mp4" type="video/mp4">
      </video>
    </div>
  </section>

  <!-- VALUES -->
  <section class="section-padding dark-section">
    <div class="container">
      <div style="text-align:center; margin-bottom:4rem;" class="reveal">
        <span class="label">Değerlerimiz</span>
        <hr class="divider" style="margin:1rem auto;">
        <h2 style="color:var(--cream);">Neden Madame?</h2>
        <p class="body-text" style="margin:1rem auto 0; text-align:center;">Her detayda mükemmelliği arayan, sanata dönüştürülmüş bir lezzet deneyimi sunuyoruz.</p>
      </div>

      <div class="values-grid" style="position:relative;">
        <!-- 3D Decorative Elements -->
        <div class="deco-3d deco-3d--float" style="top:-30px; right:80px;">
          <div class="deco-macaron">
            <div class="deco-macaron__top"></div>
            <div class="deco-macaron__fill"></div>
            <div class="deco-macaron__bottom"></div>
          </div>
        </div>
        <div class="deco-3d deco-3d--float-delayed" style="bottom:-20px; left:60px;">
          <div class="deco-croissant"></div>
        </div>
        <div class="deco-3d deco-3d--float-slow" style="top:50%; right:-20px;">
          <div class="deco-cup">
            <div class="deco-cup__handle"></div>
            <div class="deco-cup__steam"><span></span><span></span><span></span></div>
          </div>
        </div>

        <div class="value-card reveal reveal-delay-1">
          <div class="value-card__icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg>
          </div>
          <h4>Özel Malzemeler</h4>
          <p>Özenle seçilmiş katkısız tereyağı, el yapımı gerçek kakao çikolataları ve mevsimin en taze yerel malzemeleri — sadece en iyisi.</p>
        </div>

        <div class="value-card reveal reveal-delay-2">
          <div class="value-card__icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <h4>El İşçiliği</h4>
          <p>Her hamur işi elle şekillendirilir, süslenir ve bir zanaatkarın özeniyle sunulur.</p>
        </div>

        <div class="value-card reveal reveal-delay-3">
          <div class="value-card__icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
          </div>
          <h4>Zarif Atmosfer</h4>
          <p>Her ziyaretinizde huzur, zarafet ve sıcaklığı bir arada hissedeceğiniz bir mekan.</p>
        </div>
      </div>

      <!-- STATS -->
      <div class="stats reveal" style="margin-top:5rem; padding-top:3rem; border-top:1px solid rgba(250,246,240,0.06);">
        <div>
          <div class="stat__number" data-count="50" data-suffix="+">0</div>
          <div class="stat__label">Menü Çeşidi</div>
        </div>
        <div>
          <div class="stat__number" data-count="72" data-suffix="s">0</div>
          <div class="stat__label">Fermantasyon Süresi</div>
        </div>
        <div>
          <div class="stat__number" data-count="100" data-suffix="%">0</div>
          <div class="stat__label">Doğal Malzeme</div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="section-padding cta-section" style="background:var(--cream-dark);">
    <div class="container container--narrow reveal">
      <span class="label">Bizi Ziyaret Edin</span>
      <hr class="divider" style="margin:1rem auto;">
      <h2>Deneyiminize<br>Başlayın</h2>
      <p class="body-text" style="margin:1rem auto 2.5rem; text-align:center;">Madame Patisserie & Coffee'ye adım atın ve her detayın düşünüldüğü, her lezzetin kasıtlı olduğu bir dünyayı keşfedin.</p>
      <div class="cta-section__buttons">
        <a href="iletisim.php" class="btn btn--gold">
          Rezervasyon Yap
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
        <a href="menu.php" class="btn btn--outline">Menüyü İncele</a>
      </div>
    </div>
  </section>

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
