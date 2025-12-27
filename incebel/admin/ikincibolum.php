<?php
$sayfa = "İkinci Bölüm";

include("admin/inc/ahead.php");
session_start();
if (!isset($_SESSION['kadi']) && basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'islem.php') {
    header("Location: login.php");
    exit;
}
// Veritabanından tüm kayıtları çek
$sorgu = $baglanti->prepare("SELECT * FROM ikincibolum");
$sorgu->execute();
$sonuc = $sorgu->fetchAll();

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guncelle'])) {
    $id = (int)$_POST['id'];
    
    if (!empty($_POST["baslik"]) && !empty($_POST["paragraf"])) {
        $foto = $_POST['mevcut_foto']; // Mevcut fotoğrafı al

        // Yeni fotoğraf yüklenmişse
        if (!empty($_FILES["foto"]["name"])) {
            $target_dir = __DIR__ . "/../uploads/"; // Düzeltilmiş yol
            if (!file_exists($target_dir)) {
                if (!mkdir($target_dir, 0777, true)) {
                    echo "Klasör oluşturulamadı. Lütfen dizin izinlerini kontrol edin.";
                    exit;
                }
            }

            if (!is_writable($target_dir)) {
                echo "Uploads klasörü yazılabilir değil. Lütfen dizin izinlerini kontrol edin.";
                exit;
            }

            $target_file = $target_dir . basename($_FILES["foto"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Dosya tipi kontrolü
            $check = getimagesize($_FILES["foto"]["tmp_name"]);
            if ($check !== false) {
                // Dosya boyutu kontrolü
                if ($_FILES["foto"]["size"] > 5000000) {
                    echo "Dosya boyutu çok büyük (maksimum 5MB).";
                    exit;
                }

                // İzin verilen dosya tipleri
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    echo "Sadece JPG, JPEG, PNG & GIF dosyaları yüklenebilir.";
                    exit;
                }

                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                    $foto = "uploads/" . basename($_FILES["foto"]["name"]); // Veritabanı için yol
                } else {
                    echo "Dosya yükleme hatası. Hata kodu: " . $_FILES["foto"]["error"];
                    echo "<br>Hedef dizin: " . $target_dir;
                    echo "<br>Hedef dosya: " . $target_file;
                    exit;
                }
            } else {
                echo "Yüklenen dosya bir resim değil.";
                exit;
            }
        }

        // Güncelleme işlemi
        $guncelle = $baglanti->prepare("UPDATE ikincibolum SET foto = :foto, baslik = :baslik, paragraf = :paragraf WHERE id = :id");
        $guncelleBasarili = $guncelle->execute([
            'foto' => $foto,
            'baslik' => $_POST["baslik"],
            'paragraf' => $_POST["paragraf"],
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
                        window.location.href = 'ikincibolum.php';
                    });
                });
            </script>";
        } else {
            echo "Güncelleme başarısız.";
        }
    } else {
        echo "Tüm alanları doldurmanız gerekmektedir.";
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
        <h1 class="mt-4">İkinci Bölüm</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
            <li class="breadcrumb-item active">İkinci Bölüm</li>
        </ol>

        <?php if ($duzenle && $duzenlenecek): ?>
        <!-- Düzenleme Formu -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-edit me-1"></i>
                İkinci Bölüm Düzenle
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $duzenlenecek['id'] ?>">
                    <input type="hidden" name="mevcut_foto" value="<?= $duzenlenecek['foto'] ?>">
                    
                    <div class="mb-3">
                        <label for="foto" class="form-label">Mevcut Fotoğraf:</label><br>
                        <img src="../<?= $duzenlenecek['foto'] ?>" alt="Mevcut Fotoğraf" style="max-width: 200px;"><br><br>
                        <label for="foto" class="form-label">Yeni Fotoğraf:</label>
                        <input type="file" class="form-control" name="foto" id="foto">
                    </div>

                    <div class="mb-3">
                        <label for="baslik" class="form-label">Başlık:</label>
                        <input type="text" class="form-control" name="baslik" id="baslik" 
                               value="<?= htmlspecialchars($duzenlenecek['baslik']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="paragraf" class="form-label">Paragraf:</label>
                        <textarea class="form-control" name="paragraf" id="paragraf" 
                                rows="5" required><?= htmlspecialchars($duzenlenecek['paragraf']) ?></textarea>
                    </div>

                    <button type="submit" name="guncelle" class="btn btn-primary">Güncelle</button>
                    <a href="ikincibolum.php" class="btn btn-secondary">İptal</a>
                </form>
            </div>
        </div>
        <?php else: ?>
        <!-- Liste Tablosu -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                İkinci Bölüm İçerikleri
            </div>
            <div class="card-body">
                <table id="datatablesSimple" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Fotoğraf</th>
                            <th>Başlık</th>
                            <th>Paragraf</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sonuc as $satir): ?>
                            <tr>
                                <td><img src="../<?= $satir["foto"] ?>" alt="Fotoğraf" style="max-width: 100px;"></td>
                                <td><?= htmlspecialchars($satir["baslik"]) ?></td>
                                <td><?= htmlspecialchars($satir["paragraf"]) ?></td>
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