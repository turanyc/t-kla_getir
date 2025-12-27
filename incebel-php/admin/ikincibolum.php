<?php
$sayfa = "İkinci Bölüm";
include("admin/inc/ahead.php");
$sorgu = $baglanti->prepare("SELECT * FROM ikincibolum");
$sorgu->execute();
$sonuc = $sorgu->fetchAll(); // Tüm kayıtları al
?>
<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Ana Sayfa</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
            <li class="breadcrumb-item active">Ana Sayfa İkinci Bölüm Güncellemesi</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
            </div>
            <div class="card-body">
                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Başlık</th>
                            <th>Paragraf</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sonuc as $satir): ?>
                            <tr>
                                <td><img src="<?= htmlspecialchars($satir["foto"]) ?>" alt="Fotoğraf" style="width: 150px; height: auto;"></td>
                                <td><?= htmlspecialchars($satir["baslik"]) ?></td>
                                <td><?= htmlspecialchars($satir["paragraf"]) ?></td>
                                <td><a href="ikincibolumguncelle.php?id=<?= $satir['id'] ?>"><span class="fa fa-edit fa-2x"></span></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php
include("admin/inc/afooter.php");
?>