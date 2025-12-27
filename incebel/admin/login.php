<?php 
session_start();
if (!isset($_SESSION['kadi']) && basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'islem.php') {
    header("Location: login.php");
    exit;
}

// Hata mesajlarını göster
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Hata mesajı varsa göster
if(isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  <title>İncebel Hafriyat Login</title>
</head>
<body>

  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg" style="min-width: 400px;">
      <div class="card-body p-5">
        <h2 class="text-center mb-4">İncebel Hafriyat</h2>
        <h4 class="text-center mb-4">Yönetici Girişi</h4>
        <img src="img/incebel-logo.png" class="img-center mb-4"></img>
        
        <?php if(isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['kadi'])) { ?>
          <div class="text-center">
            <p class="mb-4">Çıkış yapmak istediğine emin misin?</p>
            <a href="cikis.php" class="btn btn-danger btn-lg px-5">Çıkış Yap</a>
          </div>
        <?php } else { ?>

        <form action="islem.php" method="POST">
          <div class="form-group mb-4">
            <label class="font-weight-bold">Kullanıcı Adı</label>
            <input type="text" class="form-control form-control-lg" name="kadi" required
            <?php if (isset($_COOKIE['kadi'])) { ?>
              value="<?php echo htmlspecialchars($_COOKIE['kadi']); ?>"
            <?php } else { ?>
              placeholder="Kullanıcı adınızı girin..."
            <?php } ?>>
          </div>

          <div class="form-group mb-4">
            <label class="font-weight-bold">Şifre</label>
            <input type="password" class="form-control form-control-lg" name="parola" required
            <?php if (isset($_COOKIE['parola'])) { ?>
              value="<?php echo htmlspecialchars($_COOKIE['parola']); ?>"
            <?php } else { ?>
              placeholder="Şifrenizi girin..."
            <?php } ?>>
          </div>

          <div class="form-group form-check mb-4">
            <input type="checkbox" <?php echo isset($_COOKIE['kadi']) ? "checked" : ""; ?> name="beni_hatirla" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Beni Hatırla</label>
          </div>

          <input type="hidden" name="kullanici" value="1">
          <button type="submit" class="btn btn-primary btn-lg btn-block">Giriş Yap</button>
        </form>

    <?php } ?>

  </div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>
</html>