<?php 
session_start();
include("../inc/vt.php");

if (isset($_POST['kullanici'])) {
    
    $kadi = $_POST['kadi'];
    $parola = $_POST['parola'];

    // Veritabanından kullanıcıyı sorgula
    $sorgu = $baglanti->prepare("SELECT * FROM kullanici WHERE kadi = ? AND parola = ?");
    $sorgu->execute([$kadi, $parola]);
    $sonuc = $sorgu->fetch();

    if ($sonuc) {
        // Kullanıcı doğrulandı
        $_SESSION['kadi'] = $kadi;
        $_SESSION['parola'] = $parola;

        if (isset($_POST['beni_hatirla'])) {
            // Cookie atama işlemleri
            setcookie("kadi", $kadi, strtotime("+1 day"));
            setcookie("parola", $parola, strtotime("+1 day"));
        } else {
            // Cookie sil
            setcookie("kadi", $kadi, strtotime("-1 day"));
            setcookie("parola", $parola, strtotime("-1 day")); 
        }

        header("Location:index.php");
        exit;
    } else {
        // Giriş bilgileri yanlış
        header("Location:login.php");
        exit;
    }
}
?>