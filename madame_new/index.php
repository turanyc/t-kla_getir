<?php
require_once __DIR__ . '/config.php';

// Fetch featured products from database
$featuredProducts = [];
try {
    $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 'Aktif' AND p.is_featured = 1 ORDER BY p.id DESC LIMIT 4");
    $featuredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fail silently
}

$mainFeatured = null;
$galleryFeatured = [];
if (!empty($featuredProducts)) {
    $mainFeatured = $featuredProducts[0];
    $galleryFeatured = array_slice($featuredProducts, 1, 3);
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description"
    content="Madame Patisserie & Coffee — Özenle hazırlanan el yapımı pastane ürünleri, taze kruvasanlar ve nitelikli özel kahveler.">
  <title>Madame Patisserie & Coffee — Seçkin Pastane & Nitelikli Kahve</title>
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
      <a href="index.php" class="nav__logo" aria-label="Madame Patisserie Ana Sayfa">
        <img loading="lazy" src="imgs/logo.webp" alt="Madame Patisserie & Coffee">
      </a>
      <div class="nav__links" id="navLinks">
        <a href="index.php" class="nav__link active">Ana Sayfa</a>
        <a href="hakkimizda.php" class="nav__link">Hakkımızda</a>
        <a href="qr.php" class="nav__link">Menü</a>
        <a href="galeri.php" class="nav__link">Galeri</a>
        <a href="iletisim.php" class="nav__link">İletişim</a>
        <a href="https://www.instagram.com/madamepatisseriee/" target="_blank" class="nav__link" aria-label="Instagram" style="display:flex; align-items:center;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" width="18" height="18"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><circle cx="12" cy="12" r="5"></circle><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"></circle></svg>
        </a>
      </div>
      <button class="nav__toggle" id="navToggle" aria-label="Menüyü aç/kapat">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </nav>

  <!-- HERO SECTION -->
  <section class="hero" id="hero">
    <div class="hero__video-wrapper">
      <video class="hero__video" autoplay muted loop playsinline>
        <source src="videos/cakes.mp4" type="video/mp4">
      </video>
    </div>

    <div class="hero__content">
      <p class="hero__tagline">Kuruluş 2024 — El Yapımı Pastane</p>
      <h1 class="hero__title">Her Lokmanın<br>Bir Deneyime<br>Dönüştüğü <em>Yer</em></h1>
      <p class="hero__subtitle">Özenle hazırlanan el yapımı pastane ürünleri ve nitelikli kahveler. Zarafet, tutku ve
        ustalığın buluştuğu yer.</p>
      <a href="menu.php" class="hero__cta">
        Menümüzü Keşfedin
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
      </a>
    </div>

    <div class="hero__scroll-indicator">
      <span>Keşfet</span>
      <div class="scroll-line"></div>
    </div>
  </section>

  <!-- MARQUEE BAND -->
  <div class="marquee marquee--gold">
    <div class="marquee__track">
      <span class="marquee__item"><span class="marquee__dot"></span>Taze Pişmiş</span>
      <span class="marquee__item"><span class="marquee__dot"></span>El Yapımı</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Doğal Malzemeler</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Özel Seçim Tereyağı</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Hakiki Kakao Çikolatası</span>
      <span class="marquee__item"><span class="marquee__dot"></span>72 Saat Fermantasyon</span>
      <!-- duplicate for seamless loop -->
      <span class="marquee__item"><span class="marquee__dot"></span>Taze Pişmiş</span>
      <span class="marquee__item"><span class="marquee__dot"></span>El Yapımı</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Doğal Malzemeler</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Özel Seçim Tereyağı</span>
      <span class="marquee__item"><span class="marquee__dot"></span>Hakiki Kakao Çikolatası</span>
      <span class="marquee__item"><span class="marquee__dot"></span>72 Saat Fermantasyon</span>
    </div>
  </div>

  <!-- CRAFTSMANSHIP SECTION -->
  <section class="craft" id="craft">
    <div class="container">
      <div class="craft__grid">
        <div class="craft__image-wrapper reveal-left">
          <img loading="lazy" src="imgs/chef_hands.jpg" alt="Pastacı şefin elleri özenle çalışırken">
          <div class="craft__image-accent"></div>
        </div>

        <div class="craft__content reveal-right">
          <div class="craft__number">01</div>
          <span class="label">Mutfak Sanatı</span>
          <hr class="divider">
          <h2 class="craft__title">Her Detayda<br>Üstün Özen ve Ustalık</h2>
          <p class="body-text">Mutfak tezgahımızdan çıkan her lezzet, saatler süren titiz bir çalışmanın sonucudur.
            Gerçek ustalığın gösterişli sunumlarda değil; her bir katın, katlamanın ve dokunuşun ardındaki sessiz
            hassasiyette saklı olduğuna inanıyoruz.</p>

          <div class="craft__features">
            <div class="craft__feature reveal reveal-delay-1">
              <div class="craft__feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                  stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                </svg>
              </div>
              <div>
                <h4>Seçkin Malzemeler</h4>
                <p>Her lokmada hissedeceğiniz taze yerel meyveler, özenle seçilmiş katkısız tereyağı ve gerçek el yapımı çikolatalar lezzetimizin temelidir.</p>
              </div>
            </div>

            <div class="craft__feature reveal reveal-delay-2">
              <div class="craft__feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                  stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h4>72 Saatlik Süreç</h4>
                <p>Özel kruvasan hamurumuz, eşsiz bir lezzet derinliği ve çıtırlık için 72 saatlik soğuk fermantasyondan
                  geçer.</p>
              </div>
            </div>

            <div class="craft__feature reveal reveal-delay-3">
              <div class="craft__feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                  stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
              </div>
              <div>
                <h4>Tutkuyla Üretildi</h4>
                <p>Her hamur işi el yapımı olarak tamamlanır — sırlanır, pudralanır ve bir zanaatkarın özeniyle sunulur.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- BRAND STORY SECTION -->
  <section class="story" id="story">
    <div class="container">
      <div class="story__grid">
        <div class="story__content reveal-left">
          <span class="label">Hikayemiz</span>
          <hr class="divider">
          <h2>Seçkin Lezzetlerin<br>Köklü Geleneği</h2>
          <p class="story__text">Madame Patisserie & Coffee, mutfaktaki sonsuz titizliğimiz ve pastacılık sanatına olan derin sevgimizle kuruldu. Her tarifimiz, işimize duyduğumuz bu büyük aşkın ve tavizsiz mükemmeliyet arayışının birer yansımasıdır.</p>
          <p class="story__text">Kurucularımız, en seçkin atölyelerde eğitim alarak yılların
            ustalığını her tabağa yansıtmaktadır. Bizim için hamur işleri sadece pişirilmez — hassasiyet, duygu ve
            tavizsiz mükemmeliyet arayışıyla bir beste gibi işlenir.</p>

          <blockquote class="story__quote">
            "Biz hamur işi yapmıyoruz. Damakta ve hafızada iz bırakan unutulmaz anlar tasarlıyoruz."
          </blockquote>
        </div>

        <div class="story__image-column reveal-right">
          <img loading="lazy" class="story__image-main" src="imgs/kahvaltı.jpg" alt="Zarif bir şekilde sunulmuş kahvaltı">
          <img loading="lazy" class="story__image-float" src="imgs/delux.jpg" alt="Premium latte sanatı kahve">
        </div>
      </div>
    </div>
  </section>
  <!-- FEATURED PRODUCT SECTION -->
  <section class="featured" id="featured">
    <div class="container container--wide">
      <div class="featured__showcase">
        <?php if ($mainFeatured): ?>
          <div class="featured__image-side reveal-left">
            <img loading="lazy" src="imgs/<?= htmlspecialchars($mainFeatured['image_path']) ?>" alt="<?= htmlspecialchars($mainFeatured['name']) ?>" onerror="this.src='imgs/croissant.png'">
          </div>

          <div class="featured__info-side reveal-right">
            <span class="label">Öne Çıkan Lezzet</span>
            <hr class="divider">
            <h2><?= nl2br(htmlspecialchars($mainFeatured['name'])) ?></h2>
            <p class="body-text"><?= htmlspecialchars(!empty($mainFeatured['featured_desc']) ? $mainFeatured['featured_desc'] : $mainFeatured['description']) ?></p>

            <div class="featured__details">
              <?php if (!empty($mainFeatured['f_detail1_label'])): ?>
                <div class="featured__detail">
                  <span class="featured__detail-label"><?= htmlspecialchars($mainFeatured['f_detail1_label']) ?></span>
                  <span class="featured__detail-value"><?= htmlspecialchars($mainFeatured['f_detail1_value']) ?></span>
                </div>
              <?php endif; ?>
              <?php if (!empty($mainFeatured['f_detail2_label'])): ?>
                <div class="featured__detail">
                  <span class="featured__detail-label"><?= htmlspecialchars($mainFeatured['f_detail2_label']) ?></span>
                  <span class="featured__detail-value"><?= htmlspecialchars($mainFeatured['f_detail2_value']) ?></span>
                </div>
              <?php endif; ?>
              <?php if (!empty($mainFeatured['f_detail3_label'])): ?>
                <div class="featured__detail">
                  <span class="featured__detail-label"><?= htmlspecialchars($mainFeatured['f_detail3_label']) ?></span>
                  <span class="featured__detail-value"><?= htmlspecialchars($mainFeatured['f_detail3_value']) ?></span>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php else: ?>
          
        <?php endif; ?>
      </div>

      <div class="featured__gallery">
        <?php if (!empty($galleryFeatured)): ?>
          <?php 
          $delay = 1;
          foreach ($galleryFeatured as $gf): 
          ?>
            <div class="featured__gallery-item reveal reveal-delay-<?= $delay++ ?>">
              <img loading="lazy" src="imgs/<?= htmlspecialchars($gf['image_path']) ?>" alt="<?= htmlspecialchars($gf['name']) ?>" onerror="this.src='imgs/croissant.png'">
              <div class="featured__gallery-caption">
                <h4><?= htmlspecialchars($gf['name']) ?></h4>
                <span><?= htmlspecialchars($gf['category_name']) ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Static Fallback Gallery -->
          <div class="featured__gallery-item reveal reveal-delay-1">
            <img loading="lazy" src="imgs/pasta_super.jpg" alt="Pastlar">
            <div class="featured__gallery-caption">
              <h4>Leziz Pastalar</h4>
              <span>Pastalar</span>
            </div>
          </div>
          <div class="featured__gallery-item reveal reveal-delay-2">
            <img loading="lazy" src="imgs/sunum.jpg" alt="Kruvasanlar">
            <div class="featured__gallery-caption">
              <h4>Çikolatalı Kruvasanlar</h4>
              <span>Kruvasanlar</span>
            </div>
          </div>
          <div class="featured__gallery-item reveal reveal-delay-3">
            <img loading="lazy" src="imgs/premium.jpg" alt="Klasik Ekler">
            <div class="featured__gallery-caption">
              <h4>Enfes Lezzetler</h4>
              <span>Kahvaltılar</span>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- CTA SECTION -->
  <section class="cta" id="contact">
    <div class="cta__decoration cta__decoration--left"></div>
    <div class="cta__decoration cta__decoration--right"></div>

    <div class="container">
      <div class="cta__inner reveal">
        <span class="label">Bizi Ziyaret Edin</span>
        <hr class="divider" style="margin: 1rem auto;">
        <h2>Deneyiminize<br>Başlayın</h2>
        <p class="body-text">Madame Patisserie & Coffee'ye adım atın ve her detayın özenle düşünüldüğü, her lezzetin bir
          amaçla hazırlandığı, her ziyaretin güzel bir anıya dönüştüğü dünyayı keşfedin.</p>

        <div class="cta__buttons">
          <a href="iletisim.php" class="btn btn--gold">
            Masa Rezervasyonu
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
              stroke-width="2" width="14" height="14">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
          </a>
          <a href="menu.php" class="btn btn--outline">Tüm Menüyü Gör</a>
        </div>
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
            <li><a href="index.php#craft">Ustalık</a></li>
            <li><a href="hakkimizda.php">Hikayemiz</a></li>
            <li><a href="menu.php">Menü</a></li>
            <li><a href="galeri.php">Galeri</a></li>
            <li><a href="qr" style="color: var(--gold-light);">📱 Dijital QR Menü</a></li>
            <li><a href="admin/" style="color: var(--gold-light);">⚙ Yönetim Paneli</a></li>
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
          <a href="#" class="footer__social" aria-label="Instagram">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
              stroke-width="1.5">
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
              <circle cx="12" cy="12" r="5" />
              <circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none" />
            </svg>
          </a>

        </div>
      </div>
    </div>
  </footer>

  <script src="script.js"></script>
</body>

</html>
