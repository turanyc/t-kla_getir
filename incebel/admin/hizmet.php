<?php
$sayfa = "Hizmet";
include("admin/inc/ahead.php");

// Veritabanından tüm kayıtları çek
$sorgu = $baglanti->prepare("SELECT * FROM hizmetdb");
$sorgu->execute();
$sonuc = $sorgu->fetchAll();

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guncelle'])) {
    $id = (int)$_POST['id'];
    
    if (!empty($_POST["baslik1"]) && !empty($_POST["baslik2"]) && !empty($_POST["baslik3"]) && 
        !empty($_POST["baslik4"]) && !empty($_POST["baslik5"]) && !empty($_POST["baslik6"])) {
        
        $foto1 = $_POST['mevcut_foto1'];
        $foto2 = $_POST['mevcut_foto2'];
        $foto3 = $_POST['mevcut_foto3'];
        $foto4 = $_POST['mevcut_foto4'];
        $foto5 = $_POST['mevcut_foto5'];
        $foto6 = $_POST['mevcut_foto6'];

        // Fotoğraf yükleme fonksiyonu
        function uploadPhoto($file, $current_photo) {
            if (!empty($file["name"])) {
                $target_dir = __DIR__ . "/../uploads/";
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

                $target_file = $target_dir . basename($file["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                $check = getimagesize($file["tmp_name"]);
                if ($check !== false) {
                    if ($file["size"] > 5000000) {
                        echo "Dosya boyutu çok büyük (maksimum 5MB).";
                        exit;
                    }

                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                        echo "Sadece JPG, JPEG, PNG & GIF dosyaları yüklenebilir.";
                        exit;
                    }

                    if (move_uploaded_file($file["tmp_name"], $target_file)) {
                        return "uploads/" . basename($file["name"]);
                    } else {
                        echo "Dosya yükleme hatası. Hata kodu: " . $file["error"];
                        echo "<br>Hedef dizin: " . $target_dir;
                        echo "<br>Hedef dosya: " . $target_file;
                        exit;
                    }
                } else {
                    echo "Yüklenen dosya bir resim değil.";
                    exit;
                }
            }
            return $current_photo;
        }

        // Fotoğrafları yükle
        $foto1 = uploadPhoto($_FILES["foto1"], $foto1);
        $foto2 = uploadPhoto($_FILES["foto2"], $foto2);
        $foto3 = uploadPhoto($_FILES["foto3"], $foto3);
        $foto4 = uploadPhoto($_FILES["foto4"], $foto4);
        $foto5 = uploadPhoto($_FILES["foto5"], $foto5);
        $foto6 = uploadPhoto($_FILES["foto6"], $foto6);

        // Güncelleme işlemi
        $guncelle = $baglanti->prepare("UPDATE hizmetdb SET 
            foto1 = :foto1, baslik1 = :baslik1,
            foto2 = :foto2, baslik2 = :baslik2,
            foto3 = :foto3, baslik3 = :baslik3,
            foto4 = :foto4, baslik4 = :baslik4,
            foto5 = :foto5, baslik5 = :baslik5,
            foto6 = :foto6, baslik6 = :baslik6
            WHERE id = :id");

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

<main class="py-4">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Hizmetler Yönetimi</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 mt-2">
                        <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">Hizmetler</li>
                    </ol>
                </nav>
            </div>
        </div>

        <?php if ($duzenle && $duzenlenecek): ?>
        <!-- Düzenleme Formu -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-gradient-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-edit me-2"></i>
                        Hizmet Düzenle
                    </div>
                    <a href="hizmet.php" class="btn btn-light btn-sm rounded-circle">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <input type="hidden" name="id" value="<?= $duzenlenecek['id'] ?>">
                    <?php for($i = 1; $i <= 6; $i++): ?>
                        <input type="hidden" name="mevcut_foto<?= $i ?>" value="<?= $duzenlenecek['foto'.$i] ?>">
                    <?php endfor; ?>

                    <div class="row">
                        <?php for($i = 1; $i <= 6; $i++): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light py-3">
                                    <h5 class="mb-0">Hizmet <?= $i ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <div class="position-relative d-inline-block">
                                            <img src="../<?= $duzenlenecek['foto'.$i] ?>" 
                                                 alt="Mevcut Fotoğraf <?= $i ?>" 
                                                 class="img-fluid rounded"
                                                 style="max-height: 150px; object-fit: cover;">
                                            <div class="image-overlay">
                                                <i class="fas fa-camera fa-2x text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="custom-file-upload mb-3">
                                        <label for="foto<?= $i ?>" class="form-label fw-bold">Yeni Fotoğraf</label>
                                        <input type="file" class="form-control" name="foto<?= $i ?>" id="foto<?= $i ?>">
                                        <small class="text-muted mt-1 d-block">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Maksimum: 5MB (JPG, PNG, GIF)
                                        </small>
                                    </div>

                                    <div class="form-floating">
                                        <input type="text" class="form-control" 
                                               name="baslik<?= $i ?>" id="baslik<?= $i ?>"
                                               value="<?= htmlspecialchars($duzenlenecek['baslik'.$i]) ?>" 
                                               required>
                                        <label for="baslik<?= $i ?>">Başlık</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" name="guncelle" class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-save me-2"></i>Güncelle
                        </button>
                        <a href="hizmet.php" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-times me-2"></i>İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <!-- Liste Görünümü -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-list me-2"></i>
                        Hizmet Listesi
                    </div>
                    <?php if (!empty($sonuc)): ?>
                    <div>
                        <a href="?duzenle=<?= $sonuc[0]['id'] ?>" class="btn btn-light btn-sm px-3">
                            <i class="fas fa-edit me-2"></i>Düzenle
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4" style="width: 80px;">No</th>
                                <th class="px-4" style="width: 100px;">Fotoğraf</th>
                                <th>Başlık</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sonuc as $kayit): ?>
                                <?php for($i = 1; $i <= 6; $i++): ?>
                                <tr>
                                    <td class="px-4 text-muted">
                                        <?= $i ?>
                                    </td>
                                    <td class="px-4">
                                        <img src="../<?= htmlspecialchars($kayit["foto".$i]) ?>" 
                                             alt="Hizmet <?= $i ?>" 
                                             class="rounded shadow-sm"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <span class="fw-medium"><?= htmlspecialchars($kayit["baslik".$i]) ?></span>
                                    </td>
                                </tr>
                                <?php endfor; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<style>
.bg-gradient-primary {
    background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 0.25rem;
}

.position-relative:hover .image-overlay {
    opacity: 1;
}

.custom-file-upload {
    position: relative;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    color: #4e73df;
}

.btn-primary {
    background-color: #4e73df;
    border-color: #4e73df;
}

.btn-primary:hover {
    background-color: #224abe;
    border-color: #224abe;
}

.breadcrumb-item a {
    color: #4e73df;
}

.breadcrumb-item.active {
    color: #858796;
}

.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}

.table tbody tr:hover {
    background-color: #f8f9fc;
}

@media (max-width: 768px) {
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

.table img {
    transition: transform 0.2s ease;
}

.table img:hover {
    transform: scale(1.1);
}

.btn-light {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.1);
    color: white;
}

.btn-light:hover {
    background-color: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.2);
    color: white;
}
</style>

<?php
include("admin/inc/afooter.php");
?>