<?php
$sayfa = "Hizmetler";
include("admin/inc/ahead.php");
$sorgu = $baglanti->prepare("SELECT * FROM hizmetdb");
$sorgu->execute();
$sonuc = $sorgu->fetchAll(); // Tüm kayıtları al
?>
<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Hizmetler</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
            <li class="breadcrumb-item active">Hizmetler Güncellemesi</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
            </div>
            <div class="card-body">
                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>1.Foto</th>
                            <th>1.Başlık</th>
                            <th>2.Foto</th>
                            <th>2.Başlık</th>
                            <th>3.Foto</th>
                            <th>3.Başlık</th>
                            <th>4.Foto</th>
                            <th>4.Başlık</th>
                            <th>5.Foto</th>
                            <th>5.Başlık</th>
                            <th>6.Foto</th>
                            <th>6.Başlık</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sonuc as $satir): ?>
                            <tr>
                                <td><img src="<?= htmlspecialchars($satir["foto1"]) ?>" alt="Fotoğraf" style="width: 250px; height: auto;"></td>
                                <td><?= htmlspecialchars($satir["baslik1"]) ?></td>
                                <td><img src="<?= htmlspecialchars($satir["foto2"]) ?>" alt="Fotoğraf" style="width: 150px; height: auto;"></td>
                                <td><?= htmlspecialchars($satir["baslik2"]) ?></td>
                                <td><img src="<?= htmlspecialchars($satir["foto3"]) ?>" alt="Fotoğraf" style="width: 150px; height: auto;"></td>
                                <td><?= htmlspecialchars($satir["baslik3"]) ?></td>
                                <td><img src="<?= htmlspecialchars($satir["foto4"]) ?>" alt="Fotoğraf" style="width: 150px; height: auto;"></td>
                                <td><?= htmlspecialchars($satir["baslik4"]) ?></td>
                                <td><img src="<?= htmlspecialchars($satir["foto5"]) ?>" alt="Fotoğraf" style="width: 150px; height: auto;"></td>
                                <td><?= htmlspecialchars($satir["baslik5"]) ?></td>
                                <td><img src="<?= htmlspecialchars($satir["foto6"]) ?>" alt="Fotoğraf" style="width: 150px; height: auto;"></td>
                                <td><?= htmlspecialchars($satir["baslik6"]) ?></td>
                                <td><a href="hizmetguncelle.php?id=<?= $satir['id'] ?>"><span class="fa fa-edit fa-2x"></span></a></td>
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