<?php
$sayfa = "Ana Sayfa";
include("admin/inc/ahead.php");
$sorgu = $baglanti->prepare("SELECT * FROM anasayfa");

$sorgu->execute();
$sonuc = $sorgu->fetchAll(); // Tüm kayıtları al
?>
<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Ana Sayfa</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
            <li class="breadcrumb-item active">Ana Sayfa</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
            </div>
            <div class="card-body">
                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>Ana Başlik</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sonuc as $satir): ?>
                            <tr>
                                <td><?= htmlspecialchars($satir["anaBaslik"]) ?></td>
                                <td><a href="anasayfaGuncelle.php?id=<?= $satir['id'] ?>"><span class="fa fa-edit fa-2x"></span></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
