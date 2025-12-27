<?php
// Tamponlamayı başlat
ob_start();

// Veritabanı bağlantısı
include("admin/inc/ahead.php");

// ID ve tablo parametresi kontrolü
if (!isset($_GET["id"]) || !isset($_GET["table"])) {
    die("ID veya tablo parametresi eksik.");
}

$id = (int)$_GET["id"];  // ID'yi al
$table = $_GET["table"]; // Tabloyu al

// Veritabanından veriyi çekme
$sorgu = $baglanti->prepare("SELECT * FROM $table WHERE id = :id");
$sorgu->execute(['id' => $id]);
$sonuc = $sorgu->fetch();

if (!$sonuc) {
    die("Bu ID'ye ait veri bulunamadı.");
}

// Form verisi kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (in_array($table, ["kazıdb", "dolgudb", "peysajdb", "bahcedb", "nakliyatdb", "bypassdb"])) {
        if (!empty($_POST["baslik"]) && !empty($_POST["paragraf"]) && !empty($_POST["madde1"]) && !empty($_POST["madde2"]) && !empty($_POST["madde3"])) {
            $foto = $sonuc['foto']; // Mevcut fotoğrafı al

            // Yeni fotoğraf yüklenmişse
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
                    } else {
                        echo "Fotoğraf yüklenirken bir hata oluştu.";
                        echo "Hata: " . $_FILES["foto"]["error"];
                    }
                } else {
                    echo "Yüklenen dosya bir resim değil.";
                }
            }

            // Güncelleme işlemi
            $guncelle = $baglanti->prepare("UPDATE $table SET foto = :foto, baslik = :baslik, paragraf = :paragraf, madde1 = :madde1, madde2 = :madde2, madde3 = :madde3 WHERE id = :id");
            $guncelleBasarili = $guncelle->execute([
                'foto' => $foto,
                'baslik' => $_POST["baslik"],
                'paragraf' => $_POST["paragraf"],
                'madde1' => $_POST["madde1"],
                'madde2' => $_POST["madde2"],
                'madde3' => $_POST["madde3"],
                'id' => $id
            ]);

            if ($guncelleBasarili) {
                // SweetAlert bildirimi için JavaScript kodu ekleyin
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Başarılı!',
                            text: 'Güncelleme başarılı.',
                            icon: 'success',
                            confirmButtonText: 'Tamam'
                        }).then(function() {
                            window.location.href = 'hizmetlerimiz.php';
                        });
                    });
                </script>";
                // Tamponlamayı temizle ve kapat
                ob_end_flush();
                exit;
            } else {
                echo "Güncelleme başarısız.";
            }
        } else {
            echo "Tüm alanları doldurmanız gerekmektedir.";
        }
    } elseif (in_array($table, ["kazıdb2", "dolgudb2", "peysajdb2", "bahcedb2", "nakliyatdb2", "bypassdb2"])) {
        if (!empty($_POST["baslik"]) && !empty($_POST["paragraf"]) && !empty($_POST["paragraf2"])) {
            $foto = $sonuc['foto']; // Mevcut fotoğrafı al

            // Yeni fotoğraf yüklenmişse
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
                    } else {
                        echo "Fotoğraf yüklenirken bir hata oluştu.";
                        echo "Hata: " . $_FILES["foto"]["error"];
                    }
                } else {
                    echo "Yüklenen dosya bir resim değil.";
                }
            }

            // Güncelleme işlemi
            $guncelle = $baglanti->prepare("UPDATE $table SET foto = :foto, baslik = :baslik, paragraf = :paragraf, paragraf2 = :paragraf2 WHERE id = :id");
            $guncelleBasarili = $guncelle->execute([
                'foto' => $foto,
                'baslik' => $_POST["baslik"],
                'paragraf' => $_POST["paragraf"],
                'paragraf2' => $_POST["paragraf2"],
                'id' => $id
            ]);

            if ($guncelleBasarili) {
                // SweetAlert bildirimi için JavaScript kodu ekleyin
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Başarılı!',
                            text: 'Güncelleme başarılı.',
                            icon: 'success',
                            confirmButtonText: 'Tamam'
                        }).then(function() {
                            window.location.href = 'hizmetlerimiz.php';
                        });
                    });
                </script>";
                // Tamponlamayı temizle ve kapat
                ob_end_flush();
                exit;
            } else {
                echo "Güncelleme başarısız.";
            }
        } else {
            echo "Tüm alanları doldurmanız gerekmektedir.";
        }
    }
}
?>

<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Hizmetlerimiz Güncelleme</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
            <li class="breadcrumb-item active">Hizmetlerimiz Güncelleme</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Hizmetlerimiz Güncelleme Formu
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <label for="foto">Fotoğraf:</label>
                    <input type="file" name="foto" id="foto">
                    <br>
                    <label for="baslik">Başlık:</label>
                    <input type="text" name="baslik" id="baslik" value="<?= htmlspecialchars($sonuc['baslik']); ?>" required>
                    <br>
                    <?php if (in_array($table, ["kazıdb", "dolgudb", "peysajdb", "bahcedb", "nakliyatdb", "bypassdb"])): ?>
                        <label for="paragraf">Paragraf:</label>
                        <textarea name="paragraf" id="paragraf" rows="5" cols="50" required><?= htmlspecialchars($sonuc['paragraf']); ?></textarea>
                        <br>
                        <label for="madde1">Madde 1:</label>
                        <input type="text" name="madde1" id="madde1" value="<?= htmlspecialchars($sonuc['madde1']); ?>" required>
                        <br>
                        <label for="madde2">Madde 2:</label>
                        <input type="text" name="madde2" id="madde2" value="<?= htmlspecialchars($sonuc['madde2']); ?>" required>
                        <br>
                        <label for="madde3">Madde 3:</label>
                        <input type="text" name="madde3" id="madde3" value="<?= htmlspecialchars($sonuc['madde3']); ?>" required>
                    <?php elseif (in_array($table, ["kazıdb2", "dolgudb2", "peysajdb2", "bahcedb2", "nakliyatdb2", "bypassdb2"])): ?>
                        <label for="paragraf">Paragraf 1:</label>
                        <textarea name="paragraf" id="paragraf" rows="5" cols="50" required><?= htmlspecialchars($sonuc['paragraf']); ?></textarea>
                        <br>
                        <label for="paragraf2">Paragraf 2:</label>
                        <textarea name="paragraf2" id="paragraf2" rows="5" cols="50" required><?= htmlspecialchars($sonuc['paragraf2']); ?></textarea>
                    <?php endif; ?>
                    <br>
                    <button type="submit">Güncelle</button>
                </form>
            </div>
        </div>
    </div>
</main>
<?php
include("admin/inc/afooter.php");
?>
