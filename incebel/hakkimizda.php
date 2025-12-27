<?php
$sayfa = "Hakkımızda";
include("inc/vt.php");
include("inc/head.php");
$sorgu = $baglanti->prepare("SELECT * FROM anasayfa");
$sorgu->execute();
$sonuc = $sorgu->fetch();
?>

<head>
  <meta name="description" content="Şirketimiz, hafriyat sektöründe uzun yıllara dayanan tecrübesi ve geniş makine parkıyla güvenilir hizmet sunmaktadır. Profesyonel ekibimiz ve teknolojik altyapımızla, müşteri ihtiyaçlarını en üst düzeyde karşılayarak sektörde öncü bir konuma ulaşmayı başardık.">
</head>
<div class="call-icon">
  <a href="tel:05424542796">
    <img src="img/call.png" alt="Arama İkonu">
  </a>
</div>
<div class="container">
  <div class="about-section" id="about-section">
    <h1 class="about-title">Hakkımızda</h1>
    <div class="about-content">
      <div class="about-image" id="about-image">
        <img src="img/1.jpg" alt="Hakkımızda">
      </div>
      <div class="about-text" id="about-text">
        <p>
          Şirketimiz, hafriyat sektöründe uzun yıllara dayanan tecrübesi ve geniş makine parkıyla güvenilir hizmet sunmaktadır. Profesyonel ekibimiz ve teknolojik altyapımızla, müşteri ihtiyaçlarını en üst düzeyde karşılayarak sektörde öncü bir konuma ulaşmayı başardık.
        </p>
        <p>
          Hizmetlerimiz arasında kazı, dolgu, arazi düzenleme ve alt yapı çalışmaları gibi çeşitli hafriyat çözümleri yer almaktadır. Çalışmalarımızda çevre dostu uygulamalara ve iş güvenliğine büyük önem veriyoruz. Bu yaklaşımla, projelerimizi hem kaliteli hem de sürdürülebilir bir şekilde tamamlıyoruz.
        </p>
        <p>
          Yerel ekonomiye katkı sağlamayı ve uzun vadeli müşteri ilişkileri kurmayı hedefliyoruz. Faaliyet gösterdiğimiz bölgelerde iş ortaklarımıza ve toplumumuza değer katmayı bir görev olarak kabul ediyoruz.
        </p>
      </div>
    </div>
    <div class="social-media">
      <h2>Bizi Takip Edin</h2>
      <a href="https://www.facebook.com/profile.php?id=61572552560969" target="_blank" class="social-icon facebook">Facebook</a>
      <a href="https://www.instagram.com/incebelhafriyat/" target="_blank" class="social-icon instagram">Instagram</a>
    </div>
  </div>
</div>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Shadows+Into+Light&display=swap');
  .about-section {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
    opacity: 0;
    /* Initially hidden */
    transform: translateY(100px);
    /* Initially positioned below */
    transition: opacity 1s ease-out, transform 1s ease-out;
  }

  .about-title {
    font-family: 'Shadows Into Light', cursive;
    text-align: center;
    margin-top: 20px;
    margin-bottom: 20px;
    font-size: 50px;
  }

  .about-content {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 40px;
    align-items: center;
    justify-content: center;
  }

  .about-image img {
    width: 100%;
    max-width: 500px;
    height: auto;
    border-radius: 10px;
    transform: translateX(-100px);
    /* Initially from left */
    transition: opacity 1s ease-out, transform 1s ease-out;
  }

  .about-text {
    flex: 1;
    min-width: 280px;
    max-width: 600px;
    padding: 0 15px;
    opacity: 0;
    /* Initially hidden */
    transform: translateX(100px);
    /* Initially from right */
    transition: opacity 1s ease-out, transform 1s ease-out;
  }

  .about-text p {
    margin-bottom: 15px;
    line-height: 1.6;
    font-size: 16px;
  }

  .social-media {
    margin-top: 40px;
  }

  .social-media h2 {
    font-size: 24px;
    margin-bottom: 20px;
  }

  .social-icon {
    display: inline-block;
    margin: 0 10px;
    padding: 10px 20px;
    font-size: 18px;
    color: white;
    text-decoration: none;
    border-radius: 5px;
  }

  .social-icon.facebook {
    background-color: #3b5998;
  }

  .social-icon.instagram {
    background-color: #e4405f;
  }

  .social-icon.whatsapp {
    background-color: #25d366;
  }

  @media screen and (max-width: 768px) {
    .about-content {
      flex-direction: column;
    }

    .about-image,
    .about-text {
      width: 100%;
      max-width: 100%;
    }

    .about-text {
      padding: 15px;
    }

    .about-image img {
      position: relative;
      left: 100px;
    }
  }
</style>

<div class="video-section" id="video-section">
  <h2>Tanıtım Videomuz</h2>
  <div class="video-container">
    <iframe width="560" height="315" src="https://www.youtube.com/embed/Mp8IXI1kzvQ?si=BN72knsVv9tRPWGx" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
  </div>
</div>

<style>
  .video-section {
    text-align: center;
    margin: 40px 90px;
    opacity: 0;
    /* Initially hidden */
    transform: translateY(100px);
    /* Initially positioned below */
    transition: opacity 1s ease-out, transform 1s ease-out;
  }

  .video-section h2 {
    text-align: center;
    margin: 40px;
  }

  .video-container {
    position: relative;
    padding-bottom: 56.25%;
    /* 16:9 */
    overflow: hidden;
    background: #000;
    margin: 0 auto;
  }

  .video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }

  @media screen and (max-width: 768px) {
    .video-section {
      margin: 40px 20px;
    }
  }
</style>

<script>
  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = 1;
        entry.target.style.transform = 'translateY(0)';
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1
  });

  const elements = document.querySelectorAll('#about-section, #about-image, #about-text, #video-section');
  elements.forEach(element => {
    observer.observe(element);
  });
</script>

<?php
include("inc/footer.php");
?>