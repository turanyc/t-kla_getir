<?php
$sayfa = "İletişim";
include("inc/vt.php");
include("inc/head.php");
?>
<div class="call-icon">
    <a href="tel:05424542796">
      <img src="img/call.png" alt="Arama İkonu">
    </a>
</div>
<head>
  <meta name="description" content="Hafriyat için yardıma ihtiyacınız olduğunu düşünüyorsanız formu doldurun!">
</head>
<!--Keunken Block Start-->
<div class="contact-block-price-container">
  <div class="contact-block-price-row">
    <!-- Sol blok -->
    <div class="contact-block-price-column contact-block-price-left" id="left-block">
      <h2>Maliyetleri mi merak ediyorsunuz?</h2>
      <p>
        Modern ekipmanlarımız ve geniş araç filomuz sayesinde, büyük veya küçük her projeyi başarıyla tamamlama
        yeteneğine sahibiz. Çevreye duyarlı, planlı ve ekonomik yaklaşımlarımızla sektörde fark yaratmayı
        hedefliyoruz.
      </p>

      <p class="contact-block-price-note">
        Hafriyat için yardıma ihtiyacınız olduğunu düşünüyorsanız formu doldurun!
      </p>
    </div>

    <!-- Sağ blok (form) -->
    <div class="contact-block-price-column contact-block-price-right" id="right-block">
      <h2>Fiyat teklifi isteyin</h2>
      <p>Hafriyatta Kalite, Hizmette Güvence.</p>

      <form action="https://api.web3forms.com/submit" class="contact-block-price-form" method="post">
        <input type="hidden" name="access_key" value="39335b65-0d0a-46c2-b35b-6c1900c45555">
        <div class="contact-block-price-form-row">
          <input type="text" placeholder="Adı" name="İsim" required>
          <input type="email" placeholder="Email" name="e-mail" required>
        </div>
        <div class="contact-block-price-form-row">
          <input type="text" placeholder="Telefon Numarası" name="Telefon No" required>
          <input type="text" placeholder="Adres" name="Adres" required>
        </div>
        <input type="text" class="contact-block-price-long-input" name="Hizmet türü" placeholder="Konu">
        <input type="submit" class="contact-block-price-submit-btn" value="Gönder">
      </form>
        <?php
        if($_POST)
        {
            echo '<script>Swal.fire("Başarılı", "Mesajınız bize ulaştı", "success"); </script>';
        }
        ?>
    </div>
  </div>
</div>
<!--Keuken Block End-->

<!--Map Start -->
<div class="map" id="map">
  <iframe
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3568.850607048694!2d33.250658432511614!3d37.16495644138021!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14d98d815c2f4d75%3A0xee0af081d113103f!2sGevher%20Hatun%2C%201831.%20Sk.%20No%3A7%20D%3A3%2C%2070200%20Karaman%20Merkez%2FKaraman!5e0!3m2!1str!2str!4v1734829545099!5m2!1str!2str"
    width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
    referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>
<!--Map End -->
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

  const elements = document.querySelectorAll('#left-block, #right-block, #map, .contact-block-price-container');
  elements.forEach(element => {
    observer.observe(element);
  });
</script>
<?php

include("inc/footer.php");
?>
