<?php
// Tamponlamayı başlat
ob_start();

// Veritabanı bağlantısı
include("admin/inc/ahead.php");

// ID parametresi kontrolü
if (!isset($_GET["id"])) {
    die("ID parametresi eksik.");
}

$id = (int)$_GET["id"];  // ID'yi al

// Veritabanından veriyi çekme
$sorgu = $baglanti->prepare("SELECT * FROM hizmetdb WHERE id = :id");
$sorgu->execute(['id' => $id]);
$sonuc = $sorgu->fetch();

if (!$sonuc) {
    die("Bu ID'ye ait veri bulunamadı.");
}

// Form verisi kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST["baslik1"]) && !empty($_POST["baslik2"]) && !empty($_POST["baslik3"]) && !empty($_POST["baslik4"]) && !empty($_POST["baslik5"]) && !empty($_POST["baslik6"])) {
        $foto1 = $sonuc['foto1']; // Mevcut fotoğrafı al
        $foto2 = $sonuc['foto2'];
        $foto3 = $sonuc['foto3'];
        $foto4 = $sonuc['foto4'];
        $foto5 = $sonuc['foto5'];
        $foto6 = $sonuc['foto6'];

        // Yeni fotoğraf yüklenmişse
        if (!empty($_FILES["foto1"]["name"])) {
            $target_dir = __DIR__ . "/../uploads/"; // Tam yol kullan
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true); // Klasör yoksa oluştur
            }
            $target_file = $target_dir . basename($_FILES["foto1"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Dosya tipi kontrolü
            $check = getimagesize($_FILES["foto1"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["foto1"]["tmp_name"], $target_file)) {
                    $foto1 = "uploads/" . basename($_FILES["foto1"]["name"]); // Yeni fotoğraf yolunu ayarla
                } else {
                    echo "Fotoğraf yüklenirken bir hata oluştu.";
                    echo "Hata: " . $_FILES["foto1"]["error"];
                }
            } else {
                echo "Yüklenen dosya bir resim değil.";
            }
        }

        // Diğer fotoğraflar için aynı işlemi tekrarla
        if (!empty($_FILES["foto2"]["name"])) {
            $target_file = $target_dir . basename($_FILES["foto2"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["foto2"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["foto2"]["tmp_name"], $target_file)) {
                    $foto2 = "uploads/" . basename($_FILES["foto2"]["name"]);
                } else {
                    echo "Fotoğraf yüklenirken bir hata oluştu.";
                    echo "Hata: " . $_FILES["foto2"]["error"];
                }
            } else {
                echo "Yüklenen dosya bir resim değil.";
            }
        }

        if (!empty($_FILES["foto3"]["name"])) {
            $target_file = $target_dir . basename($_FILES["foto3"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["foto3"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["foto3"]["tmp_name"], $target_file)) {
                    $foto3 = "uploads/" . basename($_FILES["foto3"]["name"]);
                } else {
                    echo "Fotoğraf yüklenirken bir hata oluştu.";
                    echo "Hata: " . $_FILES["foto3"]["error"];
                }
            } else {
                echo "Yüklenen dosya bir resim değil.";
            }
        }

        if (!empty($_FILES["foto4"]["name"])) {
            $target_file = $target_dir . basename($_FILES["foto4"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["foto4"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["foto4"]["tmp_name"], $target_file)) {
                    $foto4 = "uploads/" . basename($_FILES["foto4"]["name"]);
                } else {
                    echo "Fotoğraf yüklenirken bir hata oluştu.";
                    echo "Hata: " . $_FILES["foto4"]["error"];
                }
            } else {
                echo "Yüklenen dosya bir resim değil.";
            }
        }

        if (!empty($_FILES["foto5"]["name"])) {
            $target_file = $target_dir . basename($_FILES["foto5"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["foto5"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["foto5"]["tmp_name"], $target_file)) {
                    $foto5 = "uploads/" . basename($_FILES["foto5"]["name"]);
                } else {
                    echo "Fotoğraf yüklenirken bir hata oluştu.";
                    echo "Hata: " . $_FILES["foto5"]["error"];
                }
            } else {
                echo "Yüklenen dosya bir resim değil.";
            }
        }

        if (!empty($_FILES["foto6"]["name"])) {
            $target_file = $target_dir . basename($_FILES["foto6"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["foto6"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["foto6"]["tmp_name"], $target_file)) {
                    $foto6 = "uploads/" . basename($_FILES["foto6"]["name"]);
                } else {
                    echo "Fotoğraf yüklenirken bir hata oluştu.";
                    echo "Hata: " . $_FILES["foto6"]["error"];
                }
            } else {
                echo "Yüklenen dosya bir resim değil.";
            }
        }

        // Güncelleme işlemi
        $guncelle = $baglanti->prepare("UPDATE hizmetdb SET foto1 = :foto1, baslik1 = :baslik1, foto2 = :foto2, baslik2 = :baslik2, foto3 = :foto3, baslik3 = :baslik3, foto4 = :foto4, baslik4 = :baslik4, foto5 = :foto5, baslik5 = :baslik5, foto6 = :foto6, baslik6 = :baslik6 WHERE id = :id");
        $guncelleBasarili = $guncelle->execute([
            'foto1' => $foto1,
            'baslik1' => $_POST["baslik1"],
            'foto2' => $foto2,
            'baslik2' => $_POST["baslik2"],
            'foto3' => $foto3,
            'baslik3' => $_POST["baslik3"],
            'foto4' => $foto4,
            'baslik4' => $_POST["baslik4"],
            'foto5' => $foto5,
            'baslik5' => $_POST["baslik5"],
            'foto6' => $foto6,
            'baslik6' => $_POST["baslik6"],
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
                        window.location.href = 'hizmet.php';
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
?>

<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Hizmet Güncelleme</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
            <li class="breadcrumb-item active">Hizmet Güncelleme</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <label for="foto1">Fotoğraf 1:</label>
                    <input type="file" name="foto1" id="foto1">
                    <br>
                    <label for="baslik1">Başlık 1:</label>
                    <input type="text" name="baslik1" id="baslik1" value="<?= htmlspecialchars($sonuc['baslik1']); ?>" required>
                    <br>
                    <label for="foto2">Fotoğraf 2:</label>
                    <input type="file" name="foto2" id="foto2">
                    <br>
                    <label for="baslik2">Başlık 2:</label>
                    <input type="text" name="baslik2" id="baslik2" value="<?= htmlspecialchars($sonuc['baslik2']); ?>" required>
                    <br>
                    <label for="foto3">Fotoğraf 3:</label>
                    <input type="file" name="foto3" id="foto3">
                    <br>
                    <label for="baslik3">Başlık 3:</label>
                    <input type="text" name="baslik3" id="baslik3" value="<?= htmlspecialchars($sonuc['baslik3']); ?>" required>
                    <br>
                    <label for="foto4">Fotoğraf 4:</label>
                    <input type="file" name="foto4" id="foto4">
                    <br>
                    <label for="baslik4">Başlık 4:</label>
                    <input type="text" name="baslik4" id="baslik4" value="<?= htmlspecialchars($sonuc['baslik4']); ?>" required>
                    <br>
                    <label for="foto5">Fotoğraf 5:</label>
                    <input type="file" name="foto5" id="foto5">
                    <br>
                    <label for="baslik5">Başlık 5:</label>
                    <input type="text" name="baslik5" id="baslik5" value="<?= htmlspecialchars($sonuc['baslik5']); ?>" required>
                    <br>
                    <label for="foto6">Fotoğraf 6:</label>
                    <input type="file" name="foto6" id="foto6">
                    <br>
                    <label for="baslik6">Başlık 6:</label>
                    <input type="text" name="baslik6" id="baslik6" value="<?= htmlspecialchars($sonuc['baslik6']); ?>" required>
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