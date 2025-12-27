<?php
// Tamponlamayı başlat
ob_start();

// Veritabanı bağlantısı
include("../inc/vt.php");
include("admin/inc/ahead.php");

// ID kontrolü
if (!isset($_GET["id"])) {
    die("ID parametresi eksik.");
}

$id = (int)$_GET["id"];  // ID'yi al

// Veritabanından veriyi çekme
$sorgu = $baglanti->prepare("SELECT * FROM galeri WHERE id = :id");
$sorgu->execute(['id' => $id]);
$sonuc = $sorgu->fetch();

if (!$sonuc) {
    die("Bu ID'ye ait veri bulunamadı.");
}

// Form verisi kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $guncelle = $baglanti->prepare("UPDATE galeri SET foto = :foto WHERE id = :id");
    $guncelleBasarili = $guncelle->execute([
        'foto' => $foto,
        'id' => $id
    ]);

    if ($guncelleBasarili) {
        header("Location: galeri.php");  // Ana sayfaya yönlendir
        exit;
    } else {
        echo "Güncelleme başarısız.";
    }
}
?>

<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Galeri Güncelleme</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
            <li class="breadcrumb-item active">Galeri Güncelleme</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Galeri Güncelleme Formu
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <label for="foto">Fotoğraf:</label>
                    <input type="file" name="foto" id="foto">
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
