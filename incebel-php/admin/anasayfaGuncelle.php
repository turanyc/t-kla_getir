<?php
include("admin/inc/ahead.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];

    // Debugging: Ana Başlık verisini kontrol et
    var_dump($_POST["anaBaslik"]);  // Burada gönderilen değeri kontrol ediyoruz

    if (!empty($_POST["anaBaslik"])) {
        // Güncelleme işlemi
        $guncelle = $baglanti->prepare("UPDATE anasayfa SET anaBaslik = :anaBaslik WHERE id = :id");
        $guncelleBasarili = $guncelle->execute([
            'anaBaslik' => $_POST["anaBaslik"],
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
                        window.location.href = 'anasayfa.php';
                    });
                });
            </script>";
            exit;
        } else {
            echo "Güncelleme başarısız.";
        }
    } else {
        echo "Başlık boş olamaz.";  // Bu mesajı, "anaBaslik" boş olduğunda gösteriyoruz.
    }
}
?>

<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Ana Sayfa Güncelleme</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
            <li class="breadcrumb-item active">Ana Sayfa Güncelleme</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Ana Sayfa Güncelleme Formu
            </div>
            <div class="card-body">
                <form method="post">
                    <label for="anaBaslik">Ana Başlık:</label>
                    <input type="text" name="anaBaslik" id="anaBaslik" required>
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