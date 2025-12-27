<?php
$sayfa = "Hizmetlerimiz";
include("admin/inc/ahead.php");
if (!isset($_SESSION['kadi']) && basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'islem.php') {
    header("Location: login.php");
    exit;
}
$tables = ["kazıdb", "dolgudb", "peysajdb", "bahcedb", "nakliyatdb", "bypassdb", "kazıdb2", "dolgudb2", "peysajdb2", "bahcedb2", "nakliyatdb2", "bypassdb2"];

// Tablo isimlerini daha okunabilir hale getiren fonksiyon
function getTableDisplayName($tableName) {
    $names = [
        'kazıdb' => 'Kazı İşlemleri',
        'dolgudb' => 'Dolgu İşlemleri',
        'peysajdb' => 'Peyzaj İşlemleri',
        'bahcedb' => 'Bahçe Düzenleme',
        'nakliyatdb' => 'Nakliyat Hizmetleri',
        'bypassdb' => 'Bypass İşlemleri',
        'kazıdb2' => 'Kazı İşlemleri (Ek)',
        'dolgudb2' => 'Dolgu İşlemleri (Ek)',
        'peysajdb2' => 'Peyzaj İşlemleri (Ek)',
        'bahcedb2' => 'Bahçe Düzenleme (Ek)',
        'nakliyatdb2' => 'Nakliyat Hizmetleri (Ek)',
        'bypassdb2' => 'Bypass İşlemleri (Ek)'
    ];
    return $names[$tableName] ?? strtoupper($tableName);
}

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guncelle'])) {
    $id = (int)$_POST['id'];
    $table = $_POST['table'];
    
    if (!empty($_POST["baslik"])) {
        $foto = $_POST['mevcut_foto']; // Mevcut fotoğrafı al

        // Yeni fotoğraf yüklenmişse
        if (!empty($_FILES["foto"]["name"])) {
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

        // Güncelleme sorgusu için alanları hazırla
        $updateFields = ['foto = :foto', 'baslik = :baslik'];
        $params = [
            'foto' => $foto,
            'baslik' => $_POST["baslik"],
            'id' => $id
        ];

        // Tablo tipine göre ek alanları ekle
        if (in_array($table, ["kazıdb", "dolgudb", "peysajdb", "bahcedb", "nakliyatdb", "bypassdb"])) {
            if (!empty($_POST["paragraf"]) && !empty($_POST["madde1"]) && !empty($_POST["madde2"]) && !empty($_POST["madde3"])) {
                $updateFields[] = 'paragraf = :paragraf';
                $updateFields[] = 'madde1 = :madde1';
                $updateFields[] = 'madde2 = :madde2';
                $updateFields[] = 'madde3 = :madde3';
                $params['paragraf'] = $_POST["paragraf"];
                $params['madde1'] = $_POST["madde1"];
                $params['madde2'] = $_POST["madde2"];
                $params['madde3'] = $_POST["madde3"];
            } else {
                echo "Tüm alanları doldurmanız gerekmektedir.";
                exit;
            }
        } elseif (in_array($table, ["kazıdb2", "dolgudb2", "peysajdb2", "bahcedb2", "nakliyatdb2", "bypassdb2"])) {
            if (!empty($_POST["paragraf"]) && !empty($_POST["paragraf2"])) {
                $updateFields[] = 'paragraf = :paragraf';
                $updateFields[] = 'paragraf2 = :paragraf2';
                $params['paragraf'] = $_POST["paragraf"];
                $params['paragraf2'] = $_POST["paragraf2"];
            } else {
                echo "Tüm alanları doldurmanız gerekmektedir.";
                exit;
            }
        }

        // Güncelleme sorgusunu oluştur ve çalıştır
        $sql = "UPDATE $table SET " . implode(', ', $updateFields) . " WHERE id = :id";
        $guncelle = $baglanti->prepare($sql);
        $guncelleBasarili = $guncelle->execute($params);

        if ($guncelleBasarili) {
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
$duzenlenecekTable = null;
if (isset($_GET['id']) && isset($_GET['table'])) {
    $duzenle = true;
    $id = (int)$_GET['id'];
    $duzenlenecekTable = $_GET['table'];
    
    $sorgu = $baglanti->prepare("SELECT * FROM $duzenlenecekTable WHERE id = :id");
    $sorgu->execute(['id' => $id]);
    $duzenlenecek = $sorgu->fetch();
}
?>

<main>
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mt-4">Hizmetlerimiz</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Dashboard</li>
                    <li class="breadcrumb-item active">Hizmetlerimiz</li>
                </ol>
            </div>
        </div>

        <?php if ($duzenle && $duzenlenecek): ?>
        <!-- Düzenleme Formu -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-edit me-2"></i>
                        <?= getTableDisplayName($duzenlenecekTable) ?> Düzenle
                    </div>
                    <a href="hizmetlerimiz.php" class="btn btn-light btn-sm">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <input type="hidden" name="id" value="<?= $duzenlenecek['id'] ?>">
                    <input type="hidden" name="table" value="<?= $duzenlenecekTable ?>">
                    <input type="hidden" name="mevcut_foto" value="<?= $duzenlenecek['foto'] ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mevcut Fotoğraf</label><br>
                            <img src="../<?= $duzenlenecek['foto'] ?>" alt="Mevcut Fotoğraf" class="img-thumbnail mb-2" style="max-width: 200px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="foto" class="form-label">Yeni Fotoğraf</label>
                            <input type="file" class="form-control" name="foto" id="foto">
                            <div class="form-text">Maksimum dosya boyutu: 5MB. İzin verilen formatlar: JPG, JPEG, PNG, GIF</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="baslik" class="form-label">Başlık</label>
                        <input type="text" class="form-control" name="baslik" id="baslik" 
                               value="<?= htmlspecialchars($duzenlenecek['baslik']) ?>" required>
                    </div>

                    <?php if (in_array($duzenlenecekTable, ["kazıdb", "dolgudb", "peysajdb", "bahcedb", "nakliyatdb", "bypassdb"])): ?>
                        <div class="mb-3">
                            <label for="paragraf" class="form-label">Paragraf</label>
                            <textarea class="form-control" name="paragraf" id="paragraf" rows="4" required><?= htmlspecialchars($duzenlenecek['paragraf']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="madde1" class="form-label">Madde 1</label>
                                <input type="text" class="form-control" name="madde1" id="madde1" 
                                       value="<?= htmlspecialchars($duzenlenecek['madde1']) ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="madde2" class="form-label">Madde 2</label>
                                <input type="text" class="form-control" name="madde2" id="madde2" 
                                       value="<?= htmlspecialchars($duzenlenecek['madde2']) ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="madde3" class="form-label">Madde 3</label>
                                <input type="text" class="form-control" name="madde3" id="madde3" 
                                       value="<?= htmlspecialchars($duzenlenecek['madde3']) ?>" required>
                            </div>
                        </div>
                    <?php elseif (in_array($duzenlenecekTable, ["kazıdb2", "dolgudb2", "peysajdb2", "bahcedb2", "nakliyatdb2", "bypassdb2"])): ?>
                        
                        <div class="mb-3">
                            <label for="paragraf" class="form-label">Paragraf 1</label>
                            <textarea class="form-control" name="paragraf" id="paragraf" rows="4" required><?= htmlspecialchars($duzenlenecek['paragraf']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="paragraf2" class="form-label">Paragraf 2</label>
                            <textarea class="form-control" name="paragraf2" id="paragraf2" rows="4" required><?= htmlspecialchars($duzenlenecek['paragraf2']) ?></textarea>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <button type="submit" name="guncelle" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Güncelle
                        </button>
                        <a href="hizmetlerimiz.php" class="btn btn-secondary ms-2">
                            <i class="fas fa-times me-2"></i>İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <!-- Liste Görünümü -->
        <div class="row">
            <?php foreach ($tables as $table): ?>
                <?php
                $sorgu = $baglanti->prepare("SELECT * FROM $table");
                $sorgu->execute();
                $sonuc = $sorgu->fetchAll();
                ?>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-tools me-2"></i>
                                    <?= getTableDisplayName($table) ?>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse_<?= $table ?>" aria-expanded="true">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="collapse show" id="collapse_<?= $table ?>">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-3">Fotoğraf</th>
                                                <th>Başlık</th>
                                                <?php if (in_array($table, ["kazıdb", "dolgudb", "peysajdb", "bahcedb", "nakliyatdb", "bypassdb"])): ?>
                                                    <th>Paragraf</th>
                                                    <th>Maddeler</th>
                                                <?php elseif (in_array($table, ["kazıdb2", "dolgudb2", "peysajdb2", "bahcedb2", "nakliyatdb2", "bypassdb2"])): ?>
                                                    <th>Paragraflar</th>
                                                <?php endif; ?>
                                                <th class="text-center">İşlem</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($sonuc as $satir): ?>
                                                <tr>
                                                    <td class="px-3">
                                                        <img src="../<?= htmlspecialchars($satir["foto"]) ?>" 
                                                             alt="Fotoğraf" 
                                                             class="img-thumbnail"
                                                             style="width: 100px; height: 100px; object-fit: cover;">
                                                    </td>
                                                    <td class="align-middle">
                                                        <strong><?= htmlspecialchars($satir["baslik"]) ?></strong>
                                                    </td>
                                                    <?php if (in_array($table, ["kazıdb", "dolgudb", "peysajdb", "bahcedb", "nakliyatdb", "bypassdb"])): ?>
                                                        <td class="align-middle">
                                                            <div class="text-truncate" style="max-width: 200px;" 
                                                                 title="<?= htmlspecialchars($satir["paragraf"]) ?>">
                                                                <?= htmlspecialchars($satir["paragraf"]) ?>
                                                            </div>
                                                        </td>
                                                        <td class="align-middle">
                                                            <ul class="list-unstyled mb-0">
                                                                <li><i class="fas fa-check text-success me-2"></i><?= htmlspecialchars($satir["madde1"]) ?></li>
                                                                <li><i class="fas fa-check text-success me-2"></i><?= htmlspecialchars($satir["madde2"]) ?></li>
                                                                <li><i class="fas fa-check text-success me-2"></i><?= htmlspecialchars($satir["madde3"]) ?></li>
                                                            </ul>
                                                        </td>
                                                    <?php elseif (in_array($table, ["kazıdb2", "dolgudb2", "peysajdb2", "bahcedb2", "nakliyatdb2", "bypassdb2"])): ?>
                                                        <td class="align-middle">
                                                            <div class="mb-2 text-truncate" style="max-width: 200px;" 
                                                                 title="<?= htmlspecialchars($satir["paragraf"]) ?>">
                                                                1. <?= htmlspecialchars($satir["paragraf"]) ?>
                                                            </div>
                                                            <div class="text-truncate" style="max-width: 200px;" 
                                                                 title="<?= htmlspecialchars($satir["paragraf2"]) ?>">
                                                                2. <?= htmlspecialchars($satir["paragraf2"]) ?>
                                                            </div>
                                                        </td>
                                                    <?php endif; ?>
                                                    <td class="align-middle text-center">
                                                        <a href="?id=<?= $satir['id'] ?>&table=<?= urlencode($table) ?>" 
                                                           class="btn btn-primary btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                            <span class="d-none d-md-inline ms-1">Düzenle</span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<style>
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table th, .table td {
    white-space: nowrap;
    vertical-align: middle;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: help;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.card-header {
    background: linear-gradient(45deg, #007bff, #0056b3);
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

.list-unstyled li {
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
}

.img-thumbnail {
    transition: transform 0.3s ease;
}

.img-thumbnail:hover {
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .card-header {
        font-size: 0.9rem;
    }
    
    .table th, .table td {
        font-size: 0.9rem;
    }
    
    .list-unstyled li {
        font-size: 0.8rem;
    }
}

/* Form stilleri */
.needs-validation .form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.needs-validation .form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-text {
    color: #6c757d;
    font-size: 0.875rem;
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
</script>

<?php
include("admin/inc/afooter.php");
?>
