<?php
$sayfa = "Nakliyat";
include("inc/vt.php");
include("inc/head.php");
$sorgu19=$baglanti->prepare("SELECT * FROM nakliyatdb");
$sorgu19->execute();
$sonuc19=$sorgu19->fetch();
?>
<!-- Arama İkonu -->
<div class="call-icon">
  <a href="tel:05424542796">
    <img src="img/call.png" alt="Arama İkonu">
  </a>
</div>
<div class="keuneken-body">
  <div class="keuken-container">
    <!-- Sol taraf: Başlık, yazı, maddeler, butonlar -->
    <div class="keuken-left-content">
      <h1 class="keuken-header-one"><?= $sonuc19["baslik"] ?></h1>
      <p class="keuken-description"><?= $sonuc19["paragraf"] ?>
      </p>

      <ul class="keuken-features">
        <li class="keuken-feature-item"><?= $sonuc19["madde1"] ?></li>
        <li class="keuken-feature-item"><?= $sonuc19["madde2"] ?></li>
        <li class="keuken-feature-item"><?= $sonuc19["madde3"] ?></li>
      </ul>

      <div>
        <button class="keuken-buttons" onclick="window.location.href='tel:05424542796'">Tel 05424542796 </button>
        <button class="keuken-buttons keuken-button-secondary"
          onclick="window.location.href='contact-block.php'">İletişime geç</button>
      </div>
    </div>

    <!-- Sağ taraf: Fotoğraf -->
    <div class="keuken-right-content">
      <img src="<?= $sonuc19["foto"] ?>" alt="Fotoğraf">
    </div>
  </div>
</div>
<!--Keuken end-->
<!--Keuken mid start-->
<div class="keuken-wrapper">
  <!-- Sol taraf: Başlık, paragraf, resim ve contact kısmı -->
  <div class="keuken-left-section">
      <?php
      $sorgu20=$baglanti->prepare("SELECT * FROM nakliyatdb2");
      $sorgu20->execute();
      $sonuc20=$sorgu20->fetch();
      ?>
    <h2 class="keuken-header"><?= $sonuc20["baslik"] ?></h2>
    <p class="keuken-paragraph"><?= $sonuc20["paragraf"] ?></p>
    <img class="keuken-large-image" src="<?= $sonuc20["foto"] ?>" alt="Büyük Resim 1">
    <p class="keuken-paragraph"><?= $sonuc20["paragraf2"] ?></p>
    <button class="keuken-alt-button-first keuken-alt-button" onclick="window.location.href='tel:05424542796'">Tel
      05424542796</button>
    <button class="keuken-alt-button-second keuken-alt-button"
      onclick="window.location.href='contact-block.php'">İletişime geç</button>
  </div>
  <form class="keuken-contact-section" action="https://api.web3forms.com/submit" method="POST">
    <input type="hidden" name="access_key" value="cdab65f0-0cf7-4c5a-9d8e-64bfbcf9cdce">
    <h2 class="keuken-contact-header">Geri arama isteği</h2>
    <p class="keuken-contact-paragraph">24 saat içinde sizinle iletişime geçeceğiz.</p>
    <!-- Input alanları -->
    <input class="keuken-input" name="Adı&Soyadı" type="text" placeholder="Adınız">
    <input class="keuken-input" name="e-mail" type="email" placeholder="E-mail">
    <input class="keuken-input" name="Telefon" type="tel" placeholder="Telefon">
    <button class="keuken-submit-button">Gönder</button>
  </form>
  <!-- Alt butonlar -->
</div>

<!--Keunken Block Start-->
<div class="open-section-keuken">
  <!-- Sol taraf: Başlık, paragraf, madde listesi -->
  <div class="left-column-keuken">
    <h2 class="main-title-keuken">Maliyetleri mi merak ediyorsunuz?</h2>
    <p class="description-keuken">Hafriyat için yardıma mı ihtiyacınız var ?</p>

    <ul class="item-list-keuken">
      <li class="list-item-keuken">
        <span class="check-icon-keuken">✅</span>
        Güçlü Makineler ile hizmetinizdeyiz.
      </li>
      <li class="list-item-keuken">
        <span class="check-icon-keuken">✅</span>
        +25 Yıllık Uzmanlık
      </li>
      <li class="list-item-keuken">
        <span class="check-icon-keuken">✅</span>
        +5 Çeşitli Harfiyat Aracı
      </li>
      <li class="list-item-keuken">
        <span class="check-icon-keuken">✅</span>
        150'den Fazla Proje Teslimi
      </li>
    </ul>

    <div class="contact-info-keuken" style="display: flex; align-items: center; margin-top: 10px; padding: 10px;">
      <span class="phone-icon-keuken" style="font-size: 2em;">📞</span>
      <div class="item-list-keuken">
        <p>Doğrudan konuşmak ister misiniz?</p>
        <p>Tel 05424542796</p>
      </div>
    </div>
  </div>

  <!-- Sağ taraf: Contact kısmı -->
  <form action="https://api.web3forms.com/submit" class="contact-section-keuken" method="POST">
    <input type="hidden" name="access_key" value="9b76edb3-a732-4808-98d1-5199460f938e">
    <h2 class="contact-title-keuken">Fiyat teklifi isteyin</h2>
    <p class="contact-description-keuken">Hafriyatta Kalite, Hizmette Güvence.</p>

    <div class="input-group-keuken">
      <input class="input-field-keuken" type="text" name="Adı&Soyadı" placeholder="Adınız" required>
      <input class="input-field-keuken" type="email" name="E-mail" placeholder="E-mail" required>
      <input class="input-field-keuken" type="tel" name="Telefon" placeholder="Telefon" required>
      <input class="input-field-keuken" type="text" name="Posta Kodu" placeholder="Posta Kodu" required>
    </div>

    <input class="full-width-input-keuken" type="text" name="Sokak" placeholder="Sokak" required>
    <input class="input-field-keuken" type="text" name="Konu" placeholder="Konu" required>
    <button class="submit-button-keuken">Gönder</button>
  </form>
</div>

<!--Keuken Block End-->
<?php
include("inc/footer.php");
?>