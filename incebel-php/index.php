<?php
$sayfa = "Ana Sayfa";
include("inc/vt.php");
include("inc/head.php");
$sorgu=$baglanti->prepare("SELECT * FROM anasayfa");
$sorgu->execute();
$sonuc=$sorgu->fetch();
?>
<!-- Hero Section -->
<section class="hero-page">
  <div class="hero-page-headlines">
    <h1 id="hero-title"><?= $sonuc["anaBaslik"] ?></h1>
    <div id="hero-btn">
      <button type="button" class="btn-2-first btn-heropage" onclick="window.location.href='tel:05424542796'">
        Tel 05424542796
      </button>
      <button type="button" class="btn-2-first btn-transparent" onclick="window.location.href='/contact-block.html'">
        İletişime geç
      </button>
    </div>
  </div>
  <!-- Arama İkonu -->
  <div class="call-icon">
    <a href="tel:05424542796">
      <img src="/img/call.png" alt="Arama İkonu">
    </a>
  </div>
</section>
<!--Responsive Container 1 Start--->
<section>
<?php
    $sorgu2=$baglanti->prepare("SELECT * FROM ilkBolum");
    $sorgu2->execute();
    $sonuc2=$sorgu2->fetch();
    ?>
  <div class="responsive-container">
    <div class="image-block right-image">
      <img src="<?=$sonuc2["foto"]?>">
    </div>
    <div class="content-block left-content">
      <h1><?=$sonuc2["baslik"]?></h1>

      <p> <?=$sonuc2["paragraf"]?></p>
      <div class="button-group">
        <button class="button-first" onclick="window.location.href='tel:05424542796'">Tel 05424542796</button>
        <button class="button-second" onclick="window.location.href='contact.html'">İletişime geç</button>
      </div>
    </div>
  </div>
</section>
<!--Responsive Container 1 End--->
<!--Hizmetlerimiz Section-->
<!-- Üstte başlık ve sağda yazı -->
<div class="header-section" id="header-section">
  <h1 class="header-title">İncebel Hafriyat Hizmetleri</h1>
  <a href="/contact-block.html" class="header-link"> İletişime geç →</a>
</div>
<?php
    $sorgu21=$baglanti->prepare("SELECT * FROM hizmetdb");
    $sorgu21->execute();
    $sonuc21=$sorgu21->fetch();
    ?>
<!-- 3x2 Resim Galerisi -->
<div class="image-gallery">
  <div class="gallery-item">
    <img src="<?=$sonuc21["foto1"]?>" alt="Resim 1" class="gallery-image">
    <h3 class="gallery-title"><?=$sonuc21["baslik1"]?></h3>
    <a href="kazi.php" class="gallery-link">> Daha fazla bilgi</a>
  </div>
  <div class="gallery-item">
    <img src="<?=$sonuc21["foto2"]?>" alt="Resim 2" class="gallery-image">
    <h3 class="gallery-title"><?=$sonuc21["baslik2"]?></h3>
    <a href="dolgu.php" class="gallery-link">> Daha fazla bilgi</a>
  </div>
  <div class="gallery-item">
    <img src="<?=$sonuc21["foto3"]?>" alt="Resim 3" class="gallery-image">
    <h3 class="gallery-title"><?=$sonuc21["baslik3"]?></h3>
    <a href="peysaj.php" class="gallery-link">> Daha fazla bilgi</a>
  </div>
  <div class="gallery-item">
    <img src="<?=$sonuc21["foto4"]?>" alt="Resim 4" class="gallery-image">
    <h3 class="gallery-title"><?=$sonuc21["baslik4"]?></h3>
    <a href="baypas.php" class="gallery-link">> Daha fazla bilgi</a>
  </div>
  <div class="gallery-item">
    <img src="<?=$sonuc21["foto5"]?>" alt="Resim 5" class="gallery-image">
    <h3 class="gallery-title"><?=$sonuc21["baslik5"]?></h3>
    <a href="bahce.php" class="gallery-link">> Daha fazla bilgi</a>
  </div>
  <div class="gallery-item">
    <img src="<?=$sonuc21["foto6"]?>" alt="Resim 4" class="gallery-image">
    <h3 class="gallery-title"><?=$sonuc21["baslik6"]?></h3>
    <a href="nakliyat.php" class="gallery-link">> Daha fazla bilgi</a>
  </div>
</div>
<!--Hizmetlerimiz Section End-->
<!-- İkinci tanıtım sayfası start -->
<section>
    <?php
    $sorgu3=$baglanti->prepare("SELECT * FROM ikinciBolum");
    $sorgu3->execute();
    $sonuc3=$sorgu3->fetch();
    ?>
  <div class="responsive-container-two">
    <div class="content-block-two left-content-two">
      <h1><?=$sonuc3["baslik"]?></h1>
      <p><?=$sonuc3["paragraf"]?></p>
      <div class="button-group-two">
        <button class="button-first-two" onclick="window.location.href='tel:05424542796'">Tel 05424542796</button>
        <button class="button-second-two" onclick="window.location.href='contact.html'">İletişime geç</button>
      </div>
    </div>
    <div class="image-block-two right-image-two">
      <img src="<?=$sonuc3["foto"]?>">
    </div>
  </div>
</section>
<!-- İkinci tanıtım sayfası end -->
<!-- responsive-container-tree START -->
<section>
    <?php
    $sorgu4=$baglanti->prepare("SELECT * FROM ucuncuBolum");
    $sorgu4->execute();
    $sonuc4=$sorgu4->fetch();
    ?>
  <div class="responsive-container-tree">
    <div class="image-block-tree right-image-tree">
      <img src="<?=$sonuc4["foto"]?>">
    </div>
    <div class="content-block-tree left-content-tree">
      <h1><?=$sonuc4["baslik"]?></h1>
      <p><?=$sonuc4["paragraf"]?></p>
      <div class="button-group-tree">
        <button class="button-first-tree" onclick="window.location.href='tel:05424542796'">Tel 05424542796</button>
        <button class="button-second-tree" onclick="window.location.href='contact-block.php'">İletişime geç</button>
      </div>
    </div>
  </div>
</section>
<!-- responsive-container-tree End -->
<?php
include("inc/footer.php");
?>