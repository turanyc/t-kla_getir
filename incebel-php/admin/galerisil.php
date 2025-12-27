<?php
include("admin/inc/ahead.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];

    // Veritabanından veriyi çekme
    $sorgu = $baglanti->prepare("SELECT * FROM galeri WHERE id = :id");
    $sorgu->execute(['id' => $id]);
    $sonuc = $sorgu->fetch();

    if ($sonuc) {
        $foto = $sonuc['foto'];
        if (file_exists(__DIR__ . "/../" . $foto)) {
            unlink(__DIR__ . "/../" . $foto);
        }
        $sil = $baglanti->prepare("DELETE FROM galeri WHERE id = :id");
        $sil->execute(['id' => $id]);
    }

    header("Location: galeri.php");
    exit;
}
?>