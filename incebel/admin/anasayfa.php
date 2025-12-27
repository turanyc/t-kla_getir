<?php
$sayfa = "Ana Sayfa";
include("admin/inc/ahead.php");

// Veritabanından tüm kayıtları çek
$sorgu = $baglanti->prepare("SELECT * FROM anasayfa");
$sorgu->execute();
$sonuc = $sorgu->fetchAll();

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guncelle'])) {
    $id = (int)$_POST['id'];
    
    if (!empty($_POST["anaBaslik"])) {
        // Güncelleme işlemi
        $guncelle = $baglanti->prepare("UPDATE anasayfa SET anaBaslik = :anaBaslik WHERE id = :id");
        $guncelleBasarili = $guncelle->execute([
            'anaBaslik' => $_POST["anaBaslik"],
            'id' => $id
        ]);

        if ($guncelleBasarili) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Başarılı!',
                        text: 'Güncelleme başarılı.',
                        icon: 'success',
                        confirmButtonText: 'Tamam'
                    }).then(function() {
                        window.location.href = 'anasayfa.php';
                    });
                });
            </script>";
        } else {
            echo "Güncelleme başarısız.";
        }
    } else {
        echo "Başlık boş olamaz.";
    }
}

// Düzenleme modunda mı kontrol et
$duzenle = false;
$duzenlenecek = null;
if (isset($_GET['duzenle'])) {
    $duzenle = true;
    $id = (int)$_GET['duzenle'];
    foreach ($sonuc as $kayit) {
        if ($kayit['id'] == $id) {
            $duzenlenecek = $kayit;
            break;
        }
    }
}
?>

<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Ana Sayfa</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
            <li class="breadcrumb-item active">Ana Sayfa</li>
        </ol>

        <?php if ($duzenle && $duzenlenecek): ?>
        <!-- Düzenleme Formu -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-edit me-1"></i>
                Ana Başlık Düzenle
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="id" value="<?= $duzenlenecek['id'] ?>">
                    <div class="mb-3">
                        <label for="anaBaslik" class="form-label">Ana Başlık:</label>
                        <input type="text" class="form-control" name="anaBaslik" id="anaBaslik" 
                               value="<?= htmlspecialchars($duzenlenecek['anaBaslik']) ?>" required>
                    </div>
                    <button type="submit" name="guncelle" class="btn btn-primary">Güncelle</button>
                    <a href="anasayfa.php" class="btn btn-secondary">İptal</a>
                </form>
            </div>
        </div>
        <?php else: ?>
        <!-- Liste Tablosu -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Ana Sayfa İçerikleri
            </div>
            <div class="card-body">
                <table id="datatablesSimple" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Ana Başlık</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sonuc as $satir): ?>
                            <tr>
                                <td><?= htmlspecialchars($satir["anaBaslik"]) ?></td>
                                <td>
                                    <a href="?duzenle=<?= $satir['id'] ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Düzenle
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php
include("admin/inc/afooter.php");
?>
