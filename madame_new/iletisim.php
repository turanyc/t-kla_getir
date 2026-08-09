<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Madame Patisserie & Coffee iletişim — Adres, telefon, e-posta ve rezervasyon bilgileri.">
  <title>İletişim — Madame Patisserie & Coffee</title>
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
        <a href="galeri.php" class="nav__link">Galeri</a>
        <a href="iletisim.php" class="nav__link active">İletişim</a>
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
        <source src="videos/pasta_yapimi.mp4" type="video/mp4">
      </video>
    </div>
    <div class="page-hero__overlay"></div>
    <div class="page-hero__content">
      <span class="script-text">Bize Ulaşın</span>
      <h1>İletişim</h1>
      <p>Sorularınız, rezervasyonlarınız ve özel talepleriniz için buradayız.</p>
    </div>
    <div class="page-hero__scroll">
      <span>Keşfet</span>
      <div class="scroll-line"></div>
    </div>
  </section>

  <!-- CONTACT SECTION -->
  <section class="section-padding">
    <div class="container">
      <div class="grid-2-wide" style="align-items:start;">

        <!-- 3D Decoration -->
        <div class="deco-3d deco-3d--float" style="top:-50px; right:50%; transform:translateX(50%);">
          <div class="deco-cup" style="width:60px; height:54px;">
            <div class="deco-cup__handle" style="right:-14px; top:10px; width:16px; height:24px;"></div>
            <div class="deco-cup__steam"><span></span><span></span><span></span></div>
          </div>
        </div>

        <!-- Info Column -->
        <div class="reveal-left">
          <span class="label">Bilgilerimiz</span>
          <hr class="divider">
          <h2>Sizinle Tanışmak<br>İstiyoruz</h2>
          <p class="body-text" style="margin:1.5rem 0 2.5rem;">Madame Patisserie & Coffee olarak her ziyaretçimize özel bir deneyim sunmak için buradayız. Bize ulaşmaktan çekinmeyin.</p>

          <div style="display:flex; flex-direction:column; gap:2rem;">
            <!-- Address -->
            <div style="display:flex; gap:1rem; align-items:flex-start;">
              <div style="width:44px; height:44px; border-radius:50%; background:rgba(201,168,76,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="var(--gold)" stroke-width="1.5" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
              </div>
              <div>
                <h4 style="font-family:var(--font-heading); font-size:1.2rem; margin-bottom:0.3rem;">Adres</h4>
                <p style="font-size:0.9rem; color:var(--text-light); font-weight:300; line-height:1.6;">Şevkiye, Şht. Şefik Uçak Sk. 40/E<br>01500 Kozan/Adana</p>
              </div>
            </div>

            <!-- Phone -->
            <div style="display:flex; gap:1rem; align-items:flex-start;">
              <div style="width:44px; height:44px; border-radius:50%; background:rgba(201,168,76,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="var(--gold)" stroke-width="1.5" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
              </div>
              <div>
                <h4 style="font-family:var(--font-heading); font-size:1.2rem; margin-bottom:0.3rem;">Telefon</h4>
                <p style="font-size:0.9rem; color:var(--text-light); font-weight:300;"><a href="tel:+905310230374" style="color:var(--text-light); transition:color 0.3s;">+90 531 023 03 74</a></p>
              </div>
            </div>

              </div>
            </div>

            <!-- Hours -->
            <div style="display:flex; gap:1rem; align-items:flex-start;">
              <div style="width:44px; height:44px; border-radius:50%; background:rgba(201,168,76,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="var(--gold)" stroke-width="1.5" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <div>
                <h4 style="font-family:var(--font-heading); font-size:1.2rem; margin-bottom:0.3rem;">Çalışma Saatleri</h4>
                <p style="font-size:0.9rem; color:var(--text-light); font-weight:300; line-height:1.6;">Her gün: 08:00 — 22:00<br>Pazar: 09:00 — 21:00</p>
              </div>
            </div>

          </div>

          <!-- Social Links -->
          <div style="margin-top:2.5rem; display:flex; gap:0.8rem;">
            <a href="#" style="width:44px; height:44px; border-radius:50%; border:1.5px solid var(--cream-mid); display:flex; align-items:center; justify-content:center; color:var(--text-light); transition:all 0.3s;" onmouseover="this.style.borderColor='var(--gold)'; this.style.color='var(--gold)'; this.style.background='rgba(201,168,76,0.08)';" onmouseout="this.style.borderColor='var(--cream-mid)'; this.style.color='var(--text-light)'; this.style.background='transparent';">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" width="18" height="18"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg>
            </a>
            <a href="#" style="width:44px; height:44px; border-radius:50%; border:1.5px solid var(--cream-mid); display:flex; align-items:center; justify-content:center; color:var(--text-light); transition:all 0.3s;" onmouseover="this.style.borderColor='var(--gold)'; this.style.color='var(--gold)'; this.style.background='rgba(201,168,76,0.08)';" onmouseout="this.style.borderColor='var(--cream-mid)'; this.style.color='var(--text-light)'; this.style.background='transparent';">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" width="18" height="18"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            </a>
            <a href="#" style="width:44px; height:44px; border-radius:50%; border:1.5px solid var(--cream-mid); display:flex; align-items:center; justify-content:center; color:var(--text-light); transition:all 0.3s;" onmouseover="this.style.borderColor='var(--gold)'; this.style.color='var(--gold)'; this.style.background='rgba(201,168,76,0.08)';" onmouseout="this.style.borderColor='var(--cream-mid)'; this.style.color='var(--text-light)'; this.style.background='transparent';">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" width="18" height="18"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
            </a>
          </div>
        </div>

        <!-- Form Column -->
        <div class="reveal-right">
          <div style="background:var(--white); border-radius:var(--radius-xl); padding:2.5rem; box-shadow:var(--shadow-md); border:1px solid rgba(0,0,0,0.04);">
            <h3 style="margin-bottom:0.3rem;">Bize Yazın</h3>
            <p style="font-size:0.9rem; color:var(--text-light); font-weight:300; margin-bottom:2rem;">Mesajınızı alır almaz size dönüş yapacağız.</p>

            <form id="contactForm">
              <div class="form-group">
                <label for="name">Adınız Soyadınız</label>
                <input type="text" id="name" name="name" placeholder="Adınızı giriniz" required>
              </div>
              <div class="form-group">
                <label for="email">E-posta Adresiniz</label>
                <input type="email" id="email" name="email" placeholder="ornek@email.com" required>
              </div>
              <div class="form-group">
                <label for="phone">Telefon (Opsiyonel)</label>
                <input type="tel" id="phone" name="phone" placeholder="+90 5XX XXX XX XX">
              </div>
              <div class="form-group">
                <label for="subject">Konu</label>
                <input type="text" id="subject" name="subject" placeholder="Rezervasyon, Sipariş, Genel Soru...">
              </div>
              <div class="form-group">
                <label for="message">Mesajınız</label>
                <textarea id="message" name="message" placeholder="Mesajınızı buraya yazabilirsiniz..." required></textarea>
              </div>
              <button type="submit" id="submitBtn" class="btn btn--gold" style="width:100%; justify-content:center;">
                Mesajı Gönder
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- MAP AREA -->
  <section style="padding:0;">
    <div style="position:relative; height:400px; width: 100%; overflow:hidden;">
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d197.93951487500541!2d35.80307149687594!3d37.45996074700368!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1529310b84845c15%3A0x6cd18c8545924d56!2zxZ5ldmtpeWUsIMWeaHQuIMWeZWZpayBVw6dhayBTay4gNDAvRSwgMDE1MDAgS296YW4vQWRhbmE!5e0!3m2!1str!2str!4v1786296760246!5m2!1str!2str" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
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
          <a href="#" class="footer__social" aria-label="Facebook"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg></a>
          <a href="#" class="footer__social" aria-label="Twitter"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg></a>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="script.js"></script>
  <script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const btn = document.getElementById('submitBtn');
      const originalText = btn.innerHTML;
      btn.innerHTML = 'Gönderiliyor...';
      btn.disabled = true;

      fetch('submit_contact.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if(data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Başarılı!',
            text: data.message,
            confirmButtonColor: '#c9a84c'
          });
          this.reset();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Hata!',
            text: data.message,
            confirmButtonColor: '#c9a84c'
          });
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Hata!',
          text: 'Bir sorun oluştu. Lütfen daha sonra tekrar deneyin.',
          confirmButtonColor: '#c9a84c'
        });
      })
      .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
      });
    });
  </script>
</body>
</html>
