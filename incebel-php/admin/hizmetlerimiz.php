<?php
$sayfa = "Hizmetlerimiz";
include("admin/inc/ahead.php");

$tables = ["kazıdb", "dolgudb", "peysajdb", "bahcedb", "nakliyatdb", "bypassdb", "kazıdb2", "dolgudb2", "peysajdb2", "bahcedb2", "nakliyatdb2", "bypassdb2"];
?>
<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Hizmetlerimiz</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
            <li class="breadcrumb-item active">Hizmetlerimiz Güncellemesi</li>
        </ol>

        <?php foreach ($tables as $table): ?>
            <?php
            $sorgu = $baglanti->prepare("SELECT * FROM $table");
            $sorgu->execute();
            $sonuc = $sorgu->fetchAll(); // Tüm kayıtları al
            ?>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    <?= strtoupper($table) ?>
                </div>
                <div class="card-body">
                    <table id="datatablesSimple_<?= $table ?>">
                        <thead>
                            <tr>
                                <th>Fotoğraf</th>
                                <th>Başlık</th>
                                <?php if (in_array($table, ["kazıdb", "dolgudb", "peysajdb", "bahcedb", "nakliyatdb", "bypassdb"])): ?>
                                    <th>Paragraf</th>
                                    <th>Madde 1</th>
                                    <th>Madde 2</th>
                                    <th>Madde 3</th>
                                <?php elseif (in_array($table, ["kazıdb2", "dolgudb2", "peysajdb2", "bahcedb2", "nakliyatdb2", "bypassdb2"])): ?>
                                    <th>Paragraf 1</th>
                                    <th>Paragraf 2</th>
                                <?php endif; ?>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sonuc as $satir): ?>
                                <tr>
                                    <td><img src="<?= htmlspecialchars($satir["foto"]) ?>" alt="Fotoğraf" style="width: 150px; height: auto;"></td>
                                    <td><?= htmlspecialchars($satir["baslik"]) ?></td>
                                    <?php if (in_array($table, ["kazıdb", "dolgudb", "peysajdb", "bahcedb", "nakliyatdb", "bypassdb"])): ?>
                                        <td><?= htmlspecialchars($satir["paragraf"]) ?></td>
                                        <td><?= htmlspecialchars($satir["madde1"]) ?></td>
                                        <td><?= htmlspecialchars($satir["madde2"]) ?></td>
                                        <td><?= htmlspecialchars($satir["madde3"]) ?></td>
                                    <?php elseif (in_array($table, ["kazıdb2", "dolgudb2", "peysajdb2", "bahcedb2", "nakliyatdb2", "bypassdb2"])): ?>
                                        <td><?= htmlspecialchars($satir["paragraf"]) ?></td>
                                        <td><?= htmlspecialchars($satir["paragraf2"]) ?></td>
                                    <?php endif; ?>
                                    <td><a href="hizmetlerimizguncelle.php?id=<?= $satir['id'] ?>&table=<?= urlencode($table) ?>"><span class="fa fa-edit fa-2x"></span></a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>
<?php
include("admin/inc/afooter.php");
?>
