<?php
session_start();
if (!isset($_SESSION['kadi']) && basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'islem.php') {
    header("Location: login.php");
    exit;
}
include("admin/inc/ahead.php");
?>
<main class="py-4">
    <div class="container-fluid px-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">İncebel Hafriyat Yönetim Paneli</h1>
        </div>
        
        <div class="row">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2 transition-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="h5 mb-0 font-weight-bold text-primary">Ana Sayfa Düzenleme</div>
                                <div class="text-xs font-weight-bold text-gray-800 mb-1">Ana sayfa içeriklerini düzenleyin</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-home fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <a href="anasayfa.php" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2 transition-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="h5 mb-0 font-weight-bold text-success">Hizmetlerimiz</div>
                                <div class="text-xs font-weight-bold text-gray-800 mb-1">Hizmet listesini düzenleyin</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tools fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <a href="hizmet.php" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2 transition-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="h5 mb-0 font-weight-bold text-info">Galeri</div>
                                <div class="text-xs font-weight-bold text-gray-800 mb-1">Galeri fotoğraflarını yönetin</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-images fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <a href="galeri.php" class="stretched-link"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.transition-card {
    transition: all 0.3s ease;
    border-left: 4px solid;
}

.transition-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

.border-left-primary {
    border-left-color: #4e73df!important;
}

.border-left-success {
    border-left-color: #1cc88a!important;
}

.border-left-info {
    border-left-color: #36b9cc!important;
}

.text-primary {
    color: #4e73df!important;
}

.text-success {
    color: #1cc88a!important;
}

.text-info {
    color: #36b9cc!important;
}
</style>

<?php
include("admin/inc/afooter.php");
?>