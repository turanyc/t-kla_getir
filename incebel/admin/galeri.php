<?php
$sayfa = "Galeri";
include("../inc/vt.php");
include("admin/inc/ahead.php");

// Veritabanından tüm kayıtları çek
$sorgu = $baglanti->prepare("SELECT * FROM galeri");
$sorgu->execute();
$sonuc = $sorgu->fetchAll();

// Güncelleme ve Silme işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guncelle'])) {
        $id = (int)$_POST['id'];
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
        $guncelle = $baglanti->prepare("UPDATE galeri SET foto = :foto WHERE id = :id");
        $guncelleBasarili = $guncelle->execute([
            'foto' => $foto,
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
                        window.location.href = 'galeri.php';
                    });
                });
            </script>";
        } else {
            echo "Güncelleme başarısız.";
        }
    } elseif (isset($_POST['sil'])) {
        // Silme işlemi
        $id = (int)$_POST['id'];
        
        // Önce fotoğrafı al
        $sorgu = $baglanti->prepare("SELECT foto FROM galeri WHERE id = :id");
        $sorgu->execute(['id' => $id]);
        $kayit = $sorgu->fetch();
        
        if ($kayit) {
            // Veritabanından kaydı sil
            $sil = $baglanti->prepare("DELETE FROM galeri WHERE id = :id");
            $silmeBasarili = $sil->execute(['id' => $id]);
            
            if ($silmeBasarili) {
                // Dosyayı sil
                $dosyaYolu = __DIR__ . "/../" . $kayit['foto'];
                if (file_exists($dosyaYolu)) {
                    unlink($dosyaYolu);
                }
                
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Başarılı!',
                            text: 'Fotoğraf başarıyla silindi.',
                            icon: 'success',
                            confirmButtonText: 'Tamam'
                        }).then(function() {
                            window.location.href = 'galeri.php';
                        });
                    });
                </script>";
            } else {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Hata!',
                            text: 'Silme işlemi başarısız.',
                            icon: 'error',
                            confirmButtonText: 'Tamam'
                        });
                    });
                </script>";
            }
        }
    } elseif (isset($_POST['add'])) {
        // Yeni fotoğraf ekleme işlemi
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

                    // Veritabanına ekleme işlemi
                    $ekle = $baglanti->prepare("INSERT INTO galeri (foto) VALUES (:foto)");
                    $ekleBasarili = $ekle->execute([
                        'foto' => $foto
                    ]);

                    if ($ekleBasarili) {
                        echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    title: 'Başarılı!',
                                    text: 'Fotoğraf başarıyla eklendi.',
                                    icon: 'success',
                                    confirmButtonText: 'Tamam'
                                }).then(function() {
                                    window.location.href = 'galeri.php';
                                });
                            });
                        </script>";
                    } else {
                        echo "Ekleme başar��sız.";
                    }
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
        } else {
            echo "Fotoğraf yüklemeniz gerekmektedir.";
            exit;
        }
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mt-4">Galeri Yönetimi</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Dashboard</li>
                    <li class="breadcrumb-item active">Galeri</li>
                </ol>
            </div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#yeniFotoModal">
                <i class="fas fa-plus me-2"></i>Yeni Fotoğraf Ekle
            </button>
        </div>

        <?php if ($duzenle && $duzenlenecek): ?>
        <!-- Düzenleme Formu -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-edit me-1"></i>
                        Fotoğraf Düzenle
                    </div>
                    <a href="galeri.php" class="btn btn-light btn-sm">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <input type="hidden" name="id" value="<?= $duzenlenecek['id'] ?>">
                    <input type="hidden" name="mevcut_foto" value="<?= $duzenlenecek['foto'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="position-relative">
                                <label class="form-label fw-bold">Mevcut Fotoğraf:</label><br>
                                <img src="../<?= $duzenlenecek['foto'] ?>" alt="Mevcut Fotoğraf" 
                                     class="img-thumbnail mb-2 shadow-sm" style="max-width: 300px;">
                                <div class="image-overlay">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="foto" class="form-label fw-bold">Yeni Fotoğraf:</label>
                            <div class="custom-file-upload">
                                <input type="file" class="form-control" name="foto" id="foto">
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Maksimum dosya boyutu: 5MB. İzin verilen formatlar: JPG, JPEG, PNG, GIF
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <a href="galeri.php" class="btn btn-light me-2">
                            <i class="fas fa-times me-2"></i>İptal
                        </a>
                        <button type="submit" name="guncelle" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <!-- Galeri Grid Görünümü -->
        <div class="row g-4">
            <?php foreach ($sonuc as $satir): ?>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm gallery-card">
                    <div class="position-relative">
                        <img src="../<?= $satir["foto"] ?>" alt="Fotoğraf" 
                             class="card-img-top gallery-image" 
                             style="height: 200px; object-fit: cover;">
                        <div class="gallery-overlay">
                            <div class="d-flex justify-content-center align-items-center h-100">
                                <a href="?duzenle=<?= $satir['id'] ?>" class="btn btn-light btn-sm me-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-light btn-sm" 
                                        onclick="silmeOnayi(<?= $satir['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 p-3">
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Eklenme: <?= date("d.m.Y", strtotime($satir["created_at"] ?? "now")) ?>
                        </small>
                    </div>
                </div>
                <form id="silForm_<?= $satir['id'] ?>" method="post" style="display: none;">
                    <input type="hidden" name="id" value="<?= $satir['id'] ?>">
                    <input type="hidden" name="sil" value="1">
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Yeni Fotoğraf Ekleme Modal -->
<div class="modal fade" id="yeniFotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Yeni Fotoğraf Ekle
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="foto" class="form-label fw-bold">Fotoğraf Seçin:</label>
                        <div class="custom-file-upload">
                            <input type="file" class="form-control" name="foto" id="foto" required>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Maksimum dosya boyutu: 5MB. İzin verilen formatlar: JPG, JPEG, PNG, GIF
                            </div>
                        </div>
                        <div class="invalid-feedback">Lütfen bir fotoğraf seçin.</div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" name="add" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Ekle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Genel Stiller */
.card {
    transition: all 0.3s ease;
    border-radius: 10px;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
}

/* Galeri Kartları */
.gallery-card {
    position: relative;
}

.gallery-image {
    transition: all 0.3s ease;
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: all 0.3s ease;
    display: flex;
    justify-content: center;
    align-items: center;
}

.gallery-card:hover .gallery-overlay {
    opacity: 1;
}

.gallery-overlay .btn {
    width: 35px;
    height: 35px;
    padding: 0;
    line-height: 35px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    color: #333;
    transition: all 0.3s ease;
}

.gallery-overlay .btn:hover {
    background: #fff;
    transform: scale(1.1);
}

/* Form Elemanları */
.custom-file-upload {
    position: relative;
}

.custom-file-upload .form-control {
    padding-right: 90px;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Modal */
.modal-content {
    border-radius: 15px;
}

.modal-header {
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}

/* Düğmeler */
.btn {
    border-radius: 5px;
    padding: 8px 15px;
    font-weight: 500;
}

.btn-sm {
    padding: 5px 10px;
}

/* Responsive */
@media (max-width: 768px) {
    .gallery-overlay {
        opacity: 1;
        background: rgba(0, 0, 0, 0.3);
    }
    
    .btn-sm {
        width: 30px;
        height: 30px;
        line-height: 30px;
    }
}

/* Image Preview Overlay */
.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: all 0.3s ease;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
}

.position-relative:hover .image-overlay {
    opacity: 1;
}

/* Animasyonlar */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal.show {
    animation: fadeIn 0.3s ease;
}

.card {
    animation: fadeIn 0.5s ease;
}
</style>

<script>
// Form doğrulama
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()

// Silme onayı
function silmeOnayi(id) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Bu fotoğrafı silmek istediğinize emin misiniz?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, sil!',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('silForm_' + id).submit();
        }
    })
}

// Fotoğraf önizleme
document.querySelectorAll('.image-overlay').forEach(overlay => {
    overlay.addEventListener('click', function() {
        const imgSrc = this.previousElementSibling.src;
        Swal.fire({
            imageUrl: imgSrc,
            imageAlt: 'Fotoğraf Önizleme',
            showConfirmButton: false,
            showCloseButton: true,
            width: '80%'
        })
    });
});
</script>

<?php
include("admin/inc/afooter.php");
?>
