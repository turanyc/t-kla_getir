<?php
include("admin/inc/ahead.php");
?>
<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">İncebel Hafriyat Yönetim Paneli</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">İncebel Hafriyat Yönetim Paneli</li>
        </ol>
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">Ana Sayfa Düzenleme</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="anasayfa.php">Düzenle</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white mb-4">
                    <div class="card-body">Hizmetlerimiz Düzenle</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="hizmet.php">Düzenle</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">Galeri Düzenle</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="galeri.php">Düzenle</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
include("admin/inc/afooter.php");
?>