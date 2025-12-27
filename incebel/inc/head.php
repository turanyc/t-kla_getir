<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <!-- link the css -->
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="home-css/faq.css">
  <link rel="stylesheet" href="home-css/project.css">
  <link rel="stylesheet" href="home-css/footer.css">
  <link rel="stylesheet" href="home-css/bahce.css">
  <link rel="stylesheet" href="home-css/baypas.css">
  <link rel="stylesheet" href="home-css/bouw.css">
  <link rel="stylesheet" href="home-css/contact.css">
  <link rel="stylesheet" href="home-css/dolgu.css">
  <link rel="stylesheet" href="home-css/hizmetlerimiz.css">
  <link rel="stylesheet" href="home-css/kazı.css">
  <link rel="stylesheet" href="home-css/nakliyat.css">
  <link rel="stylesheet" href="home-css/peysaj.css">
  <link rel="stylesheet" href="home-css/project.css">
  <link rel="stylesheet" href="home-css/responsive-container-1.css">
  <link rel="stylesheet" href="home-css/responsive-container-2.css">
  <link rel="stylesheet" href="home-css/responsive-container-3.css">

  <link rel="icon" href="img/favicon.png" type="image/png"> <!-- PNG formatı -->
  <!-- link the icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- For the font -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap"="fas fa-times close-btn"></i>

  <!-- Parallax Effect -->
  <script src="https://cdn.jsdelivr.net/npm/simple-parallax-js@5.5.1/dist/simpleParallax.min.js"></script>
  <!-- Swipper Library -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <title><?= $sayfa ?> | İncebel Harfiyat</title>
  <script type="text/javascript" src="home-css/sweetalert2.all.min.js"></script>
  <!-- link the icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- For the font -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap"
    rel="stylesheet" />
  <!-- Parallax Effect -->
  <script src="https://cdn.jsdelivr.net/npm/simple-parallax-js@5.5.1/dist/simpleParallax.min.js"></script>
  <!-- Swipper Library -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <!-- Scroll Reveal Library -->
  <script src="https://unpkg.com/scrollreveal"></script>
</head>

<body>
  <!-- Navbar -->
  <header>
    <a href="index.php" class="brand"><img src="img/incebel-logo.png"></a>
    <div class="menu">
      <div class="btn">
        <i class="fas fa-times close-btn"></i>
      </div>
      <a href="index.php">Ana Sayfa</a>
      <a href="index.php#header-section">Hizmetlerimiz</a>
      <div class="dropdown">
        <a href="index.php#header-section" class="dropdown-btn">Hakkımızda</a>
        <ul class="dropdown-content">
          <li><a href="hakkimizda.php">Hakkımızda</a></li>
          <li><a href="vizyon.php">Vizyonumuz</a></li>
          <li><a href="misyon.php">Misyonumuz</a></li>
        </ul>
      </div>
      <a href="galeri.php">Galeri</a>
      <a href="contact-block.php">İletişim</a>
      <div class="mededeling">
        <a href='tel:05424542796'>Hemen Ara</a>
      </div>

    </div>

    <div class="btn">
      <i class="fas fa-bars menu-btn"></i>
    </div>
  </header>
  <!-- Navbar End -->