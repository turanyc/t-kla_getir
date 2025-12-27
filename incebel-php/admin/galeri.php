<?php
$sayfa = "Galeri";
include("../inc/vt.php");
include("admin/inc/ahead.php");

$sorgu = $baglanti->prepare("SELECT * FROM galeri");
$sorgu->execute();
$sonuc = $sorgu->fetchAll(); // Tüm kayıtları al

// Yeni fotoğraf ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    if (!empty($_FILES["foto"]["name"])) {
        $target_dir = __DIR__ . "/../uploads/"; // Tam yol kullan
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Klasör yoksa oluştur
        }
        $target_file = $target_dir . basename($_FILES["foto"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Dosya tipi kontrolü
        $check = getimagesize($_FILES["foto"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                $foto = "uploads/" . basename($_FILES["foto"]["name"]); // Yeni fotoğraf yolunu ayarla

                // Veritabanına ekleme işlemi
                $ekle = $baglanti->prepare("INSERT INTO galeri (foto) VALUES (:foto)");
                $ekle->execute([
                    'foto' => $foto
                ]);

                header("Location: galeri.php");
                exit;
            } else {
                echo "Fotoğraf yüklenirken bir hata oluştu.";
                echo "Hata: " . $_FILES["foto"]["error"];
            }
        } else {
            echo "Yüklenen dosya bir resim değil.";
        }
    } else {
        echo "Fotoğraf yüklemeniz gerekmektedir.";
    }
}
?>
<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Galeri</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
            <li class="breadcrumb-item active">Galeri Güncellemesi</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Galeri
            </div>
            <div class="card-body">
                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>Fotoğraf</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sonuc as $satir): ?>
                            <tr>
                                <td><img src="<?= htmlspecialchars($satir["foto"]) ?>" alt="Fotoğraf" style="width: 150px; height: auto;"></td>
                                <td>
                                    <a href="galeriguncelle.php?id=<?= $satir['id'] ?>"><span class="fa fa-edit fa-2x"></span></a>
                                    <form method="post" action="galerisil.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $satir['id'] ?>">
                                        <button type="submit" onclick="return confirm('Bu fotoğrafı silmek istediğinize emin misiniz?')"><span class="fa fa-trash fa-2x"></span></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-plus me-1"></i>
                Yeni Fotoğraf Ekle
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <label for="foto">Fotoğraf:</label>
                    <input type="file" name="foto" id="foto" required>
                    <br>
                    <button type="submit" name="add">Ekle</button>
                </form>
            </div>
        </div>
    </div>
</main>
<?php
include("admin/inc/afooter.php");
?>
