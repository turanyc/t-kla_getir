<?php
$sayfa = "Galeri";
include("inc/vt.php");
include("inc/head.php");
$sorgu5=$baglanti->prepare("SELECT * FROM galeri");
$sorgu5->execute();
$sonuc5=$sorgu5->fetch();
?>
<!--Görseller Start-->
<div class="dugun-all">
  <div class="container-dugun">
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
