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
$sorgu = $baglanti->prepare("SELECT * FROM ilkbolum WHERE id = :id");
$sorgu->execute(['id' => $id]);
$sonuc = $sorgu->fetch();

if (!$sonuc) {
    die("Bu ID'ye ait veri bulunamadı.");
}

// Form verisi kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST["baslik"]) && !empty($_POST["paragraf"])) {
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
        $guncelle = $baglanti->prepare("UPDATE ilkbolum SET foto = :foto, baslik = :baslik, paragraf = :paragraf WHERE id = :id");
        $guncelleBasarili = $guncelle->execute([
            'foto' => $foto,
            'baslik' => $_POST["baslik"],
            'paragraf' => $_POST["paragraf"],
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
                        window.location.href = 'ilkbolum.php';
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
        <h1 class="mt-4">İlk Bölüm Güncelleme</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
            <li class="breadcrumb-item active">İlk Bölüm Güncelleme</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <label for="foto">Fotoğraf:</label>
                    <input type="file" name="foto" id="foto">
                    <br>
                    <label for="baslik">Başlık:</label>
                    <input type="text" name="baslik" id="baslik" value="<?= htmlspecialchars($sonuc['baslik']); ?>" required>
                    <br>
                    <label for="paragraf">Paragraf:</label>
                    <textarea name="paragraf" id="paragraf" rows="5" cols="50" required><?= htmlspecialchars($sonuc['paragraf']); ?></textarea>
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