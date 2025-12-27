<?php
$sayfa = "Galeri";
include("inc/vt.php");
include("inc/head.php");
$sorgu5=$baglanti->prepare("SELECT * FROM galeri");
$sorgu5->execute();
$sonuc5=$sorgu5->fetch();
?>
<head>
<link rel="stylesheet" href="home-css/faq.css">
<meta name="description" content="Hafriyat işlerinizde merak ettiğiniz her şey için bize ulaşın!">
</head>
<div class="call-icon">
    <a href="tel:05424542796">
      <img src="img/call.png" alt="Arama İkonu">
    </a>
  </div>
<!--Görseller Start-->
<div class="dugun-all">
  <div class="container-dugun">
  <h1>Galeri</h1>
    <div class="image-container">
        <?php
        $sorgu6 = $baglanti->prepare("SELECT * FROM galeri ORDER BY id");
        $sorgu6->execute();
       while($sonuc6=$sorgu6->fetch()) {
        ?>
        
      <div class="image"><img src="<?=$sonuc6["foto"]?>"></div>
      
        <?php
        
       }
        ?>
    </div>
  </div>
  <!--Görseller End-->
 <?php 
 include("inc/footer.php");
 ?>
