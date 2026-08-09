<?php
require_once __DIR__ . '/../config.php';
requireLogin();

$menuData   = getMenuData();
$categories = $menuData['categories'];
$items      = $menuData['items'];
$msg        = '';

// Fetch contact messages
$contactMessages = [];
try {
    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    $contactMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fail silently if table doesn't exist yet
}

// ─── ACTIONS ───────────────────────────────────────────
// Delete item
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$delId]);
    header('Location: dashboard.php?msg=deleted'); exit;
}

// Delete category
if (isset($_GET['delete_cat'])) {
    $catId = $_GET['delete_cat'];
    // Delete all products belonging to this category first to satisfy FK constraint
    $stmt = $pdo->prepare("DELETE FROM products WHERE category_id = ?");
    $stmt->execute([$catId]);
    
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$catId]);
    header('Location: dashboard.php?msg=cat_deleted'); exit;
}

// Toggle active/inactive
if (isset($_GET['toggle'])) {
    $tid = (int)$_GET['toggle'];
    // Fetch current status
    $stmt = $pdo->prepare("SELECT status FROM products WHERE id = ?");
    $stmt->execute([$tid]);
    $status = $stmt->fetchColumn();
    $newStatus = ($status === 'Aktif') ? 'Pasif' : 'Aktif';
    
    $stmt = $pdo->prepare("UPDATE products SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $tid]);
    header('Location: dashboard.php?msg=toggled'); exit;
}

// Delete message
if (isset($_GET['delete_msg'])) {
    $delId = (int)$_GET['delete_msg'];
    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->execute([$delId]);
    header('Location: dashboard.php?tab=messages&msg=msg_deleted'); exit;
}

// Save item (new or update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_item') {
        $id    = (int)$_POST['item_id'];
        
        $img = trim($_POST['existing_img'] ?? '');
        $uploadWarning = '';

        if (isset($_FILES['img_file']) && $_FILES['img_file']['name'] !== '') {
            $fileError = $_FILES['img_file']['error'];
            if ($fileError === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['img_file']['tmp_name'];
                $uploadFileDir = __DIR__ . '/../imgs/';
                $newFileName = compressAndConvertToWebP($fileTmpPath, $uploadFileDir, 'prod_');
                if ($newFileName) {
                    $img = $newFileName;
                } else {
                    $uploadWarning = 'Ürün görseli işlenemedi. Formatı desteklenmiyor veya dosya bozuk olabilir. (Maksimum dosya boyutu limitini aşmış veya PHP bellek yetersizliği de olabilir)';
                }
            } else if ($fileError !== UPLOAD_ERR_NO_FILE) {
                if ($fileError === UPLOAD_ERR_INI_SIZE || $fileError === UPLOAD_ERR_FORM_SIZE) {
                    $uploadWarning = 'Yüklenen görsel boyutu çok büyük (Maksimum limit: ' . ini_get('upload_max_filesize') . ').';
                } else {
                    $uploadWarning = 'Görsel yüklenirken bir hata oluştu (Hata kodu: ' . $fileError . ').';
                }
            }
        }

        if (empty($img)) {
            $img = 'croissant.webp';
        }

        $cat   = $_POST['cat'];
        $name  = trim($_POST['name']);
        $desc  = trim($_POST['desc']);
        $price = (int)$_POST['price'];

        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $featured_desc = trim($_POST['featured_desc'] ?? '');
        $f_detail1_label = trim($_POST['f_detail1_label'] ?? '');
        $f_detail1_value = trim($_POST['f_detail1_value'] ?? '');
        $f_detail2_label = trim($_POST['f_detail2_label'] ?? '');
        $f_detail2_value = trim($_POST['f_detail2_value'] ?? '');
        $f_detail3_label = trim($_POST['f_detail3_label'] ?? '');
        $f_detail3_value = trim($_POST['f_detail3_value'] ?? '');

        if ($id) {
            $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, image_path = ?, is_featured = ?, featured_desc = ?, f_detail1_label = ?, f_detail1_value = ?, f_detail2_label = ?, f_detail2_value = ?, f_detail3_label = ?, f_detail3_value = ? WHERE id = ?");
            $stmt->execute([$cat, $name, $desc, $price, $img, $is_featured, $featured_desc, $f_detail1_label, $f_detail1_value, $f_detail2_label, $f_detail2_value, $f_detail3_label, $f_detail3_value, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, image_path, status, is_featured, featured_desc, f_detail1_label, f_detail1_value, f_detail2_label, f_detail2_value, f_detail3_label, f_detail3_value) VALUES (?, ?, ?, ?, ?, 'Aktif', ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cat, $name, $desc, $price, $img, $is_featured, $featured_desc, $f_detail1_label, $f_detail1_value, $f_detail2_label, $f_detail2_value, $f_detail3_label, $f_detail3_value]);
        }

        if (!empty($uploadWarning)) {
            $_SESSION['flash_warning'] = $uploadWarning;
        }
        header('Location: dashboard.php?msg=saved'); exit;
    }

    if ($_POST['action'] === 'save_category') {
        $catName = trim($_POST['cat_name']);
        $catId   = trim($_POST['cat_id']);
        if (empty($catId)) {
            // Slugify name
            $catId = strtolower(preg_replace('/[^a-zA-Z0-9-]+/', '-', $catName));
        }

        $img = trim($_POST['existing_cat_img'] ?? 'cat_tatli.webp');
        $uploadWarning = '';

        if (isset($_FILES['cat_img_file']) && $_FILES['cat_img_file']['name'] !== '') {
            $fileError = $_FILES['cat_img_file']['error'];
            if ($fileError === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['cat_img_file']['tmp_name'];
                $uploadFileDir = __DIR__ . '/../imgs/';
                $newFileName = compressAndConvertToWebP($fileTmpPath, $uploadFileDir, 'cat_');
                if ($newFileName) {
                    $img = $newFileName;
                } else {
                    $uploadWarning .= 'Kategori ikon görseli işlenemedi. ';
                }
            } else if ($fileError !== UPLOAD_ERR_NO_FILE) {
                if ($fileError === UPLOAD_ERR_INI_SIZE || $fileError === UPLOAD_ERR_FORM_SIZE) {
                    $uploadWarning .= 'Kategori ikon boyutu çok büyük (Maksimum limit: ' . ini_get('upload_max_filesize') . '). ';
                } else {
                    $uploadWarning .= 'Kategori ikon yükleme hatası (Hata kodu: ' . $fileError . '). ';
                }
            }
        }

        $banner = trim($_POST['existing_cat_banner'] ?? 'imgs/coffee.webp');
        if (isset($_FILES['cat_banner_file']) && $_FILES['cat_banner_file']['name'] !== '') {
            $fileError = $_FILES['cat_banner_file']['error'];
            if ($fileError === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['cat_banner_file']['tmp_name'];
                $uploadFileDir = __DIR__ . '/../imgs/';
                $newFileName = compressAndConvertToWebP($fileTmpPath, $uploadFileDir, 'banner_', 1200);
                if ($newFileName) {
                    $banner = 'imgs/' . $newFileName;
                } else {
                    $uploadWarning .= 'Kategori banner görseli işlenemedi. ';
                }
            } else if ($fileError !== UPLOAD_ERR_NO_FILE) {
                if ($fileError === UPLOAD_ERR_INI_SIZE || $fileError === UPLOAD_ERR_FORM_SIZE) {
                    $uploadWarning .= 'Kategori banner boyutu çok büyük (Maksimum limit: ' . ini_get('upload_max_filesize') . '). ';
                } else {
                    $uploadWarning .= 'Kategori banner yükleme hatası (Hata kodu: ' . $fileError . '). ';
                }
            }
        }

        $stmt = $pdo->prepare("INSERT INTO categories (id, name, img, banner) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = ?, img = ?, banner = ?");
        $stmt->execute([$catId, $catName, $img, $banner, $catName, $img, $banner]);

        if (!empty($uploadWarning)) {
            $_SESSION['flash_warning'] = trim($uploadWarning);
        }
        header('Location: dashboard.php?msg=cat_added'); exit;
    }

    if ($_POST['action'] === 'bulk_delete_categories') {
        $catIds = $_POST['cat_ids'] ?? [];
        if (!empty($catIds)) {
            $placeholders = implode(',', array_fill(0, count($catIds), '?'));
            
            $stmt = $pdo->prepare("DELETE FROM products WHERE category_id IN ($placeholders)");
            $stmt->execute($catIds);
            
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id IN ($placeholders)");
            $stmt->execute($catIds);
            header('Location: dashboard.php?msg=cats_deleted'); exit;
        }
    }

    if ($_POST['action'] === 'bulk_delete_products') {
        $productIds = $_POST['product_ids'] ?? [];
        if (!empty($productIds)) {
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $stmt = $pdo->prepare("DELETE FROM products WHERE id IN ($placeholders)");
            $stmt->execute($productIds);
            header('Location: dashboard.php?msg=products_deleted'); exit;
        }
    }

    if ($_POST['action'] === 'update_price') {
        $pid   = (int)$_POST['item_id'];
        $price = (int)$_POST['price'];
        $stmt = $pdo->prepare("UPDATE products SET price = ? WHERE id = ?");
        $stmt->execute([$price, $pid]);
        header('Location: dashboard.php?msg=price_updated'); exit;
    }
}

// Edit Mode
$editItem = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    foreach ($items as $it) { if ($it['id'] === $eid) { $editItem = $it; break; } }
}

$editCategory = null;
if (isset($_GET['edit_cat'])) {
    $ecId = $_GET['edit_cat'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$ecId]);
    $editCategory = $stmt->fetch(PDO::FETCH_ASSOC);
}

$msgs = [
    'saved'            => '✓ Ürün başarıyla kaydedildi.',
    'deleted'          => '✓ Ürün başarıyla silindi.',
    'products_deleted' => '✓ Seçilen ürünler başarıyla silindi.',
    'toggled'          => '✓ Ürün aktiflik durumu değiştirildi.',
    'price_updated'    => '✓ Ürün fiyatı başarıyla güncellendi.',
    'cat_added'        => '✓ Kategori başarıyla kaydedildi.',
    'cat_deleted'      => '✓ Kategori ve içerisindeki tüm ürünler başarıyla silindi.',
    'cats_deleted'     => '✓ Seçilen kategoriler ve içerisindeki tüm ürünler başarıyla silindi.'
];
$notice = $msgs[$_GET['msg'] ?? ''] ?? '';
$warningNotice = '';
if (isset($_SESSION['flash_warning'])) {
    $warningNotice = $_SESSION['flash_warning'];
    unset($_SESSION['flash_warning']);
}

$catNames = array_column($categories, 'name', 'id');
$stats = [
    'total'  => count($items), 
    'active' => count(array_filter($items, fn($i) => $i['active'])), 
    'cats'   => count($categories)
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Panel | Madame Patisserie</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,400&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --cream:#faf6f0;
  --cream-dark:#f0e8da;
  --cream-mid:#e8dcc8;
  --gold:#c9a84c;
  --gold-light:#e2c47e;
  --gold-dark:#8a6a1f;
  --dark:#1a1511;
  --dark-2:#2c2219;
  --text:#3a2e22;
  --text-light:#7a6a58;
  --border:rgba(201,168,76,0.18);
  --green:#4a7c59;
  --red:#c94a4a;
  --radius:16px;
}
body{font-family:'Jost',sans-serif;background:var(--cream);color:var(--text);min-height:100vh;display:flex}

/* SIDEBAR */
.sidebar{width:250px;background:var(--dark);border-right:1px solid var(--border);padding:32px 20px;display:flex;flex-direction:column;gap:8px;flex-shrink:0;color:var(--cream-dark)}
.sidebar-logo{text-align:center;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--border)}
.sidebar-logo-title{font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:600;background:linear-gradient(135deg,var(--gold),var(--gold-light));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.sidebar-logo-sub{font-size:10px;color:rgba(201,168,76,0.6);letter-spacing:2px;text-transform:uppercase;margin-top:4px}
.badge{display:inline-block;background:rgba(201,168,76,0.12);border:1px solid rgba(201,168,76,0.25);color:var(--gold-light);font-size:10px;padding:3px 12px;border-radius:20px;margin-top:6px;letter-spacing:1px}
.nav-link{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;color:rgba(250,246,240,0.6);text-decoration:none;font-size:13px;font-weight:500;transition:all .3s ease;cursor:pointer;border:none;background:none;width:100%;text-align:left}
.nav-link:hover,.nav-link.active{background:rgba(201,168,76,0.1);color:var(--gold-light)}
.nav-link .icon{font-size:16px;width:20px;text-align:center}
.nav-section{font-size:10px;color:rgba(201,168,76,0.4);letter-spacing:2px;text-transform:uppercase;padding:12px 14px 4px;margin-top:8px}
.sidebar-footer{margin-top:auto;padding-top:20px;border-top:1px solid var(--border)}
.sidebar-footer .menu-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  border-radius: 12px;
  color: #ffb3b3;
  background: rgba(192, 57, 43, 0.1);
  border: 1px solid rgba(192, 57, 43, 0.25);
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  transition: all 0.2s ease;
  cursor: pointer;
  justify-content: center;
}
.sidebar-footer .menu-link:hover {
  background: rgba(192, 57, 43, 0.25);
  border-color: #c0392b;
  color: #ffcccc;
  transform: translateY(-1px);
}

/* MAIN */
.main{flex:1;overflow-y:auto;padding:36px;display:flex;flex-direction:column}
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px}
.topbar-title{font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:600;color:var(--dark)}
.topbar-title span{color:var(--gold)}
.user-chip{display:flex;align-items:center;gap:8px;background:var(--cream-dark);border:1px solid var(--border);border-radius:20px;padding:8px 16px;font-size:13px;color:var(--text)}
.user-chip .dot{width:8px;height:8px;background:var(--green);border-radius:50%}

/* NOTICE */
.notice{background:rgba(74,124,89,0.1);border:1px solid rgba(74,124,89,0.25);color:var(--green);border-radius:12px;padding:14px 20px;margin-bottom:24px;font-size:14px;font-weight:500}
.notice-error{background:rgba(201,74,74,0.1);border:1px solid rgba(201,74,74,0.25);color:var(--red);border-radius:12px;padding:14px 20px;margin-bottom:24px;font-size:14px;font-weight:500}

/* STATS */
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:32px}
.stat-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;text-align:center;box-shadow:0 4px 15px rgba(0,0,0,0.02)}
.stat-num{font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:700;color:var(--dark)}
.stat-label{font-size:12px;color:var(--text-light);text-transform:uppercase;letter-spacing:1px;margin-top:6px}

/* CARD */
.panel-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:24px;box-shadow:0 8px 30px rgba(0,0,0,0.03)}
.panel-header{display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--border)}
.panel-header h2{font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:600;color:var(--dark)}

.panel-body{padding:24px}

/* FORM */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.form-group{display:flex;flex-direction:column;gap:8px}
.form-group label{font-size:11px;font-weight:600;color:var(--text-light);letter-spacing:1px;text-transform:uppercase}
.form-group input,.form-group select,.form-group textarea{background:var(--cream);border:1px solid var(--border);border-radius:12px;padding:12px 16px;font-size:14px;color:var(--text);font-family:'Jost',sans-serif;outline:none;transition:all .3s ease;width:100%}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--gold);background:#fff;box-shadow:0 0 0 3px rgba(201,168,76,0.15)}
.form-group.full{grid-column:1/-1}
.btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:12px;font-size:13px;font-weight:600;cursor:pointer;border:none;font-family:'Jost',sans-serif;transition:all .3s ease;text-decoration:none;text-transform:uppercase;letter-spacing:1px}
.btn-gold{background:linear-gradient(135deg,var(--gold),var(--gold-light));color:var(--dark)}
.btn-gold:hover{opacity:.95;transform:translateY(-1px);box-shadow:0 4px 15px rgba(201,168,76,0.25)}
.btn-outline{background:transparent;border:1.5px solid var(--border);color:var(--text-light)}
.btn-outline:hover{border-color:var(--gold);color:var(--gold)}
.btn-red{background:rgba(201,74,74,0.1);border:1px solid rgba(201,74,74,0.3);color:var(--red)}
.btn-red:hover{background:rgba(201,74,74,0.2)}
.btn-green{background:rgba(74,124,89,0.1);border:1px solid rgba(74,124,89,0.3);color:var(--green)}
.btn-green:hover{background:rgba(74,124,89,0.2)}
.btn-sm{padding:8px 16px;font-size:11px;border-radius:10px}

/* TABLE */
.menu-table{width:100%;border-collapse:collapse}
.menu-table th{text-align:left;font-size:11px;font-weight:600;color:var(--text-light);letter-spacing:1px;text-transform:uppercase;padding:14px 18px;border-bottom:1px solid var(--border)}
.menu-table td{padding:14px 18px;font-size:14px;border-bottom:1px solid rgba(201,168,76,0.1);vertical-align:middle}
.menu-table tr:last-child td{border-bottom:none}
.menu-table tr:hover td{background:rgba(201,168,76,0.03)}
.item-img{width:46px;height:46px;border-radius:10px;object-fit:cover;cursor:pointer;transition:transform 0.3s ease,box-shadow 0.3s ease}
.item-img:hover{transform:scale(1.08);box-shadow:0 4px 12px rgba(201,168,76,0.3)}
#itemTable .item-img{min-width:46px;min-height:46px;flex-shrink:0}
.item-name{font-weight:600;color:var(--dark);font-family:'Cormorant Garamond',serif;font-size:16px}
.item-desc{font-size:12px;color:var(--text-light);margin-top:3px;word-break:break-word;white-space:normal;line-height:1.4}
.status-on{display:inline-block;background:rgba(74,124,89,0.12);color:var(--green);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:600}
.status-off{display:inline-block;background:rgba(201,74,74,0.1);color:var(--red);border-radius:20px;padding:3px 12px;font-size:11px;font-weight:600}
.actions{display:flex;gap:8px}
.cat-filter{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px}
.cat-chip{padding:8px 18px;border-radius:25px;font-size:13px;font-weight:500;cursor:pointer;border:1px solid var(--border);background:#fff;color:var(--text-light);transition:all .3s ease}
.cat-chip:hover,.cat-chip.active{background:rgba(201,168,76,0.1);border-color:var(--gold);color:var(--gold-dark)}
.price-input{width:90px;background:var(--cream);border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:13px;color:var(--text);font-family:'Jost',sans-serif;text-align:center;font-weight:600}
.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* Image Modal/Zoom */
.image-modal {
  display: none;
  position: fixed;
  z-index: 10000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(26, 21, 17, 0.95);
  backdrop-filter: blur(10px);
  align-items: center;
  justify-content: center;
}
.image-modal.active {
  display: flex;
}
.image-modal-content {
  max-width: 85%;
  max-height: 85vh;
  border-radius: var(--radius);
  border: 2px solid var(--gold);
  box-shadow: 0 20px 50px rgba(0,0,0,0.4);
  transform: scale(0.9);
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.image-modal.active .image-modal-content {
  transform: scale(1);
}
.close-modal {
  position: absolute;
  top: 30px;
  right: 40px;
  color: var(--cream);
  font-size: 36px;
  font-weight: 300;
  cursor: pointer;
  transition: color 0.2s;
}
.close-modal:hover {
  color: var(--gold-light);
}

/* Responsive Table container */
.table-responsive {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  margin-bottom: 1rem;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  background: #fff;
}
.table-responsive .menu-table {
  margin-bottom: 0;
}

/* Mobile Nav Toggle Button */
.mobile-nav-trigger {
  display: none;
  background: #fff;
  border: 1px solid var(--border);
  color: var(--text);
  font-size: 20px;
  width: 42px;
  height: 42px;
  border-radius: 10px;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  margin-right: 12px;
  transition: all 0.2s;
  outline: none;
}
.mobile-nav-trigger:hover {
  border-color: var(--gold);
  color: var(--gold-dark);
}

/* Sidebar Overlay */
.sidebar-overlay {
  position: fixed;
  inset: 0;
  background: rgba(26, 21, 17, 0.6);
  backdrop-filter: blur(4px);
  z-index: 9998;
  display: none;
  opacity: 0;
  transition: opacity 0.3s ease;
}
.sidebar-overlay.active {
  display: block;
  opacity: 1;
}

@media(max-width:992px){
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 250px;
    height: 100vh;
    z-index: 9999;
    transform: translateX(-100%);
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 10px 0 30px rgba(0,0,0,0.3);
    display: flex !important;
  }
  .sidebar.open {
    transform: translateX(0);
  }
  .mobile-nav-trigger {
    display: flex;
  }
  .stats{grid-template-columns:1fr 1fr}
  .form-grid{grid-template-columns:1fr}
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  margin-bottom: 1rem;
}
.table-responsive table {
  width: 100%;
  min-width: 650px;
}

@media(max-width:576px){
  .stats{grid-template-columns:1fr}
  .main{padding:20px}
  .topbar-title{font-size:22px}
  .panel-body{padding:16px}
  .btn{padding:10px 18px;font-size:12px}
  .item-desc{display:none}
}
</style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-logo-title">Madame</div>
    <div class="sidebar-logo-sub">Patisserie & Cafe</div>
    <div class="badge">⚙ Admin Panel</div>
  </div>

  <span class="nav-section">Menü</span>
  <button id="link-items" class="nav-link active" onclick="navToTab('items')"><span class="icon">🍽</span> Ürünler</button>
  <button id="link-add" class="nav-link" onclick="navToTab('add')"><span class="icon">➕</span> Ürün Ekle</button>
  <button id="link-prices" class="nav-link" onclick="navToTab('prices')"><span class="icon">💰</span> Hızlı Fiyatlar</button>
  <button id="link-categories" class="nav-link" onclick="navToTab('categories')"><span class="icon">📁</span> Kategoriler</button>
  <button id="link-add-cat" class="nav-link" onclick="navToTab('add-cat')"><span class="icon">➕</span> Kategori Ekle</button>
  <button id="link-messages" class="nav-link" onclick="navToTab('messages')"><span class="icon">✉</span> Gelen Mesajlar</button>

  <span class="nav-section">Sistem</span>
  <a href="qr-card.php" target="_blank" class="nav-link"><span class="icon">🖨</span> Sanatsal QR Tasarımı</a>
  <a href="../" target="_blank" class="nav-link"><span class="icon">👁</span> Ana Sayfayı Gör</a>
  <a href="../qr" target="_blank" class="nav-link"><span class="icon">📱</span> QR Menüyü Gör</a>

  <div class="sidebar-footer">
    <a href="logout.php" class="menu-link">
      <span class="icon">🚪</span> <span>Çıkış Yap</span>
    </a>
  </div>
</aside>

<!-- MAIN -->
<main class="main">
  <div class="topbar">
    <div style="display:flex;align-items:center">
      <button class="mobile-nav-trigger" onclick="toggleSidebar()">☰</button>
      <div class="topbar-title">Admin <span>Panel</span></div>
    </div>
    <div class="user-chip"><span class="dot"></span> <?= htmlspecialchars($_SESSION['admin_user'] ?? 'admin') ?></div>
  </div>

  <?php if ($notice): ?><div class="notice"><?= $notice ?></div><?php endif; ?>
  <?php if (!empty($warningNotice)): ?><div class="notice-error"><?= htmlspecialchars($warningNotice) ?></div><?php endif; ?>

  <!-- STATS -->
  <div class="stats">
    <div class="stat-card"><div class="stat-num"><?= $stats['total'] ?></div><div class="stat-label">Toplam Ürün</div></div>
    <div class="stat-card"><div class="stat-num"><?= $stats['active'] ?></div><div class="stat-label">Aktif Ürün</div></div>
    <div class="stat-card"><div class="stat-num"><?= $stats['cats'] ?></div><div class="stat-label">Kategori Sayısı</div></div>
  </div>

  <!-- TAB: ÜRÜNLER -->
  <div id="tab-items" class="tab-panel active">
    <div class="panel-card">
      <div class="panel-header">
        <h2>🍽 Tüm Ürünler</h2>
        <button class="btn btn-gold btn-sm" onclick="showTab('add')">+ Yeni Ürün</button>
      </div>
      <div class="panel-body">
        <div class="cat-filter">
          <button class="cat-chip active" onclick="filterCat(this,'all')">Tümü</button>
          <?php foreach($categories as $c): ?>
          <button class="cat-chip" onclick="filterCat(this,'<?= $c['id'] ?>')"><?= htmlspecialchars($c['name']) ?></button>
          <?php endforeach; ?>
        </div>
        <form method="POST" action="dashboard.php">
          <input type="hidden" name="action" value="bulk_delete_products">
          <div style="margin-bottom:16px">
            <button type="submit" class="btn btn-red btn-sm" onclick="return confirm('Seçilen tüm ürünleri silmek istediğinize emin misiniz?')">🗑 Seçilenleri Sil</button>
          </div>
          <div class="table-responsive">
            <table class="menu-table" id="itemTable">
              <thead>
                <tr>
                  <th style="width:40px"><input type="checkbox" id="selectAllProducts" onclick="toggleSelectAllProducts(this)"></th>
                  <th style="width:65px">Görsel</th>
                  <th>Ürün</th>
                  <th>Kategori</th>
                  <th>Fiyat</th>
                  <th>Durum</th>
                  <th>İşlem</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($items as $it): ?>
                <tr data-cat="<?= $it['cat'] ?>">
                  <td style="width:40px;min-width:40px;"><input type="checkbox" name="product_ids[]" value="<?= $it['id'] ?>" class="product-checkbox"></td>
                  <td style="width:65px;min-width:65px;"><img class="item-img" src="../imgs/<?= htmlspecialchars($it['img']) ?>" alt="" onerror="this.src='../imgs/croissant.webp'"></td>
                  <td>
                    <div class="item-name">
                      <?= htmlspecialchars($it['name']) ?>
                      <?php if (!empty($it['is_featured'])): ?>
                        <span style="font-size: 11px; background: #fff8e1; border: 1px solid #ffe082; color: #ffb300; padding: 2px 6px; border-radius: 12px; margin-left: 6px; font-weight: 500;">⭐ Öne Çıkan</span>
                      <?php endif; ?>
                    </div>
                    <div class="item-desc"><?= htmlspecialchars($it['desc']) ?></div>
                  </td>
                  <td><?= htmlspecialchars($catNames[$it['cat']] ?? $it['cat']) ?></td>
                  <td>₺<?= $it['price'] ?></td>
                  <td>
                    <?php if($it['active']): ?>
                    <span class="status-on">Aktif</span>
                    <?php else: ?>
                    <span class="status-off">Pasif</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="actions">
                      <a href="?edit=<?= $it['id'] ?>" class="btn btn-outline btn-sm">✏</a>
                      <a href="?toggle=<?= $it['id'] ?>" class="btn btn-green btn-sm"><?= $it['active'] ? '⏸' : '▶' ?></a>
                      <a href="?delete=<?= $it['id'] ?>" class="btn btn-red btn-sm" onclick="return confirm('Bu ürünü silmek istiyor musunuz?')">🗑</a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- TAB: EKLE / DÜZENLE -->
  <div id="tab-add" class="tab-panel">
    <div class="panel-card">
      <div class="panel-header">
        <h2><?= $editItem ? '✏ Ürün Düzenle' : '➕ Yeni Ürün Ekle' ?></h2>
        <?php if($editItem): ?><a href="dashboard.php" class="btn btn-outline btn-sm">← Vazgeç</a><?php endif; ?>
      </div>
      <div class="panel-body">
        <form method="POST" action="dashboard.php" enctype="multipart/form-data">
          <input type="hidden" name="action" value="save_item">
          <input type="hidden" name="item_id" value="<?= $editItem['id'] ?? 0 ?>">
          <div class="form-grid">
            <div class="form-group">
              <label>Ürün Adı *</label>
              <input type="text" name="name" value="<?= htmlspecialchars($editItem['name'] ?? '') ?>" placeholder="Örn: Espresso" required>
            </div>
            <div class="form-group">
              <label>Kategori *</label>
              <select name="cat" required>
                <?php foreach($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($editItem['cat'] ?? '') === $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Fiyat (₺) *</label>
              <input type="number" name="price" value="<?= $editItem['price'] ?? '' ?>" placeholder="85" min="1" required>
            </div>
            <div class="form-group">
              <label>Ürün Görseli</label>
              <?php if($editItem && !empty($editItem['img'])): ?>
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                <img class="item-img" src="../imgs/<?= htmlspecialchars($editItem['img']) ?>" style="width:40px;height:40px" onerror="this.src='../imgs/croissant.webp'">
                <span style="font-size:12px;opacity:0.8"><?= htmlspecialchars($editItem['img']) ?></span>
              </div>
              <?php endif; ?>
              <input type="file" name="img_file" accept="image/*" style="padding:8px">
              <input type="hidden" name="existing_img" value="<?= htmlspecialchars($editItem['img'] ?? '') ?>">
              <small style="font-size:11px;color:var(--text-light)">Yeni görsel seçmezseniz mevcut görsel korunur.</small>
            </div>
            <div class="form-group full">
              <label>Açıklama</label>
              <input type="text" name="desc" value="<?= htmlspecialchars($editItem['desc'] ?? '') ?>" placeholder="Kısa açıklama giriniz...">
            </div>
            
            <!-- ÖNE ÇIKAN DETAYLARI -->
            <div class="form-group full" style="border-top: 1px solid rgba(0,0,0,0.08); padding-top: 20px; margin-top: 10px;">
              <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                <input type="checkbox" name="is_featured" value="1" <?= ($editItem['is_featured'] ?? 0) ? 'checked' : '' ?> onchange="toggleFeaturedFields(this)">
                ⭐ Öne Çıkan Ürün Yap (Ana Sayfada Gösterilsin)
              </label>
            </div>
            
            <div id="featured-fields-container" style="display: <?= ($editItem['is_featured'] ?? 0) ? 'contents' : 'none' ?>;" class="form-group full">
              <div class="form-grid" style="gap: 15px; width: 100%;">
                <div class="form-group full" style="grid-column: span 2;">
                  <label>Öne Çıkan Ürün Açıklaması (Ana sayfadaki büyük gösterim için özel açıklama - boş bırakılırsa normal açıklama kullanılır)</label>
                  <textarea name="featured_desc" rows="3" placeholder="Örn: Bitter çikolatalı tartımız, kıtır sable breton taban üzerine..." style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-family:inherit; resize:vertical; background: rgba(0,0,0,0.02);"><?= htmlspecialchars($editItem['featured_desc'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                  <label>Öne Çıkan Detay 1 (Başlık) - Örn: Menşe</label>
                  <input type="text" name="f_detail1_label" value="<?= htmlspecialchars($editItem['f_detail1_label'] ?? '') ?>" placeholder="Örn: Menşe">
                </div>
                <div class="form-group">
                  <label>Öne Çıkan Detay 1 (Değer) - Örn: Türkiye</label>
                  <input type="text" name="f_detail1_value" value="<?= htmlspecialchars($editItem['f_detail1_value'] ?? '') ?>" placeholder="Örn: Türkiye">
                </div>
                
                <div class="form-group">
                  <label>Öne Çıkan Detay 2 (Başlık) - Örn: Hazırlık</label>
                  <input type="text" name="f_detail2_label" value="<?= htmlspecialchars($editItem['f_detail2_label'] ?? '') ?>" placeholder="Örn: Hazırlık">
                </div>
                <div class="form-group">
                  <label>Öne Çıkan Detay 2 (Değer) - Örn: 24 Saat</label>
                  <input type="text" name="f_detail2_value" value="<?= htmlspecialchars($editItem['f_detail2_value'] ?? '') ?>" placeholder="Örn: 24 Saat">
                </div>
                
                <div class="form-group">
                  <label>Öne Çıkan Detay 3 (Başlık) - Örn: Malzeme</label>
                  <input type="text" name="f_detail3_label" value="<?= htmlspecialchars($editItem['f_detail3_label'] ?? '') ?>" placeholder="Örn: Malzeme">
                </div>
                <div class="form-group">
                  <label>Öne Çıkan Detay 3 (Değer) - Örn: El Yapımı</label>
                  <input type="text" name="f_detail3_value" value="<?= htmlspecialchars($editItem['f_detail3_value'] ?? '') ?>" placeholder="Örn: El Yapımı">
                </div>
              </div>
            </div>
          </div>
          <div style="margin-top:20px;display:flex;gap:10px">
            <button type="submit" class="btn btn-gold">💾 Kaydet</button>
            <button type="button" class="btn btn-outline" onclick="showTab('items')">İptal</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- TAB: FİYAT GÜNCELLE -->
  <div id="tab-prices" class="tab-panel">
    <div class="panel-card">
      <div class="panel-header"><h2>💰 Hızlı Fiyat Güncelleme</h2></div>
      <div class="panel-body">
        <div style="margin-bottom:16px;display:flex;gap:10px;align-items:center">
          <span style="font-size:16px;color:var(--text-light)">🔍</span>
          <input type="text" id="quickPriceSearch" placeholder="Ürün adı veya kategoriye göre ara..." onkeyup="filterQuickPrices()" style="width:100%;max-width:320px;padding:9px 12px;background:var(--cream);border:1px solid var(--border);border-radius:8px;outline:none;font-size:13px">
        </div>
        <div class="table-responsive">
          <table class="menu-table">
            <thead><tr><th>Ürün</th><th>Kategori</th><th>Mevcut Fiyat</th><th>Yeni Fiyat</th><th></th></tr></thead>
            <tbody>
              <?php foreach($items as $it): if(!$it['active']) continue; ?>
              <tr>
                <td><div class="item-name"><?= htmlspecialchars($it['name']) ?></div></td>
                <td><?= htmlspecialchars($catNames[$it['cat']] ?? $it['cat']) ?></td>
                <td>₺<?= $it['price'] ?></td>
                <td>
                  <form method="POST" style="display:flex;gap:8px;align-items:center">
                    <input type="hidden" name="action" value="update_price">
                    <input type="hidden" name="item_id" value="<?= $it['id'] ?>">
                    <input class="price-input" type="number" name="price" value="<?= $it['price'] ?>" min="1">
                    <button type="submit" class="btn btn-gold btn-sm">✓</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- TAB: KATEGORİLER -->
  <div id="tab-categories" class="tab-panel">
    <div class="panel-card" style="margin-bottom:28px">
      <div class="panel-header">
        <h2>📁 Kategori Yönetimi</h2>
        <button class="btn btn-gold btn-sm" onclick="showTab('add-cat')">+ Yeni Kategori</button>
      </div>
      <div class="panel-body">
        <form method="POST" action="dashboard.php">
          <input type="hidden" name="action" value="bulk_delete_categories">
          <div style="margin-bottom:16px">
            <button type="submit" class="btn btn-red btn-sm">🗑 Seçilenleri Sil</button>
          </div>
          <div class="table-responsive">
            <table class="menu-table">
              <thead>
                <tr>
                  <th style="width:40px"><input type="checkbox" id="selectAllCats" onclick="toggleSelectAllCats(this)"></th>
                  <th>İkon</th>
                  <th>Kategori Adı</th>
                  <th>Kod / ID</th>
                  <th>İşlem</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $fullCatsStmt = $pdo->query("SELECT * FROM categories");
                $fullCategories = $fullCatsStmt->fetchAll(PDO::FETCH_ASSOC);
                foreach($fullCategories as $c): 
                ?>
                <tr>
                  <td><input type="checkbox" name="cat_ids[]" value="<?= htmlspecialchars($c['id']) ?>" class="cat-checkbox"></td>
                  <td>
                    <img class="item-img" src="../imgs/<?= htmlspecialchars($c['img']) ?>" alt="" onerror="this.src='../imgs/croissant.webp'" style="width:40px;height:40px;border-radius:8px;object-fit:cover">
                  </td>
                  <td><div class="item-name"><?= htmlspecialchars($c['name']) ?></div></td>
                  <td><span style="font-family:monospace;background:var(--cream);padding:4px 8px;border-radius:4px;border:1px solid var(--border)"><?= htmlspecialchars($c['id']) ?></span></td>
                  <td>
                    <div class="actions">
                      <a href="?edit_cat=<?= urlencode($c['id']) ?>" class="btn btn-outline btn-sm">✏</a>
                      <a href="?delete_cat=<?= urlencode($c['id']) ?>" class="btn btn-red btn-sm" onclick="return confirm('Bu kategoriyi ve içindeki tüm ürünleri silmek istiyor musunuz?')">🗑 Sil</a>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- TAB: KATEGORİ EKLE / DÜZENLE -->
  <div id="tab-add-cat" class="tab-panel">
    <div class="panel-card">
      <div class="panel-header">
        <h2><?= $editCategory ? '✏ Kategori Düzenle' : '➕ Yeni Kategori Ekle' ?></h2>
        <?php if($editCategory): ?><a href="dashboard.php" class="btn btn-outline btn-sm">← Vazgeç</a><?php endif; ?>
      </div>
      <div class="panel-body">
        <form method="POST" action="dashboard.php" enctype="multipart/form-data">
          <input type="hidden" name="action" value="save_category">
          <input type="hidden" name="existing_cat_img" value="<?= htmlspecialchars($editCategory['img'] ?? '') ?>">
          <input type="hidden" name="existing_cat_banner" value="<?= htmlspecialchars($editCategory['banner'] ?? '') ?>">
          <div class="form-grid">
            <div class="form-group">
              <label>Kategori Adı *</label>
              <input type="text" name="cat_name" value="<?= htmlspecialchars($editCategory['name'] ?? '') ?>" placeholder="Örn: Ev Yapımı Limonatalar" required>
            </div>
            <div class="form-group">
              <label>Kategori Kodu / ID (Slug)</label>
              <input type="text" name="cat_id" value="<?= htmlspecialchars($editCategory['id'] ?? '') ?>" placeholder="Örn: limonata (Boş bırakılırsa otomatik üretilir)" <?= $editCategory ? 'readonly style="background:rgba(0,0,0,0.02);color:var(--text-light)"' : '' ?>>
              <?php if($editCategory): ?><small style="font-size:11px;color:var(--text-light)">Düzenleme modunda kategori kodu değiştirilemez.</small><?php endif; ?>
            </div>
            <div class="form-group">
              <label>Kategori Küçük İkon Görseli</label>
              <?php if($editCategory && !empty($editCategory['img'])): ?>
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                <img class="item-img" src="../imgs/<?= htmlspecialchars($editCategory['img']) ?>" style="width:40px;height:40px" onerror="this.src='../imgs/croissant.webp'">
                <span style="font-size:12px;opacity:0.8"><?= htmlspecialchars($editCategory['img']) ?></span>
              </div>
              <?php endif; ?>
              <input type="file" name="cat_img_file" accept="image/*" style="padding:8px">
              <small style="font-size:11px;color:var(--text-light)">QR menüdeki yuvarlak kategori listesinde görünür.</small>
            </div>
            <div class="form-group">
              <label>Kategori Banner Görseli</label>
              <?php if($editCategory && !empty($editCategory['banner'])): ?>
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                <img class="item-img" src="../<?= htmlspecialchars($editCategory['banner']) ?>" style="width:40px;height:40px" onerror="this.src='../imgs/coffee.webp'">
                <span style="font-size:12px;opacity:0.8"><?= htmlspecialchars($editCategory['banner']) ?></span>
              </div>
              <?php endif; ?>
              <input type="file" name="cat_banner_file" accept="image/*" style="padding:8px">
              <small style="font-size:11px;color:var(--text-light)">Web sitesi menü arka planında gösterilir.</small>
            </div>
          </div>
          <div style="margin-top:20px;display:flex;gap:10px">
            <button type="submit" class="btn btn-gold">📁 Kategoriyi Kaydet</button>
            <?php if($editCategory): ?><a href="dashboard.php" class="btn btn-outline">← Vazgeç</a><?php else: ?><button type="button" class="btn btn-outline" onclick="showTab('categories')">İptal</button><?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- TAB: GELEN MESAJLAR -->
  <div id="tab-messages" class="tab-panel">
    <div class="panel-card">
      <div class="panel-header">
        <h2>✉ Gelen Mesajlar</h2>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="menu-table">
            <thead>
              <tr>
                <th>Tarih</th>
                <th>İsim</th>
                <th>E-posta / Telefon</th>
                <th>Konu</th>
                <th>Mesaj</th>
                <th>İşlem</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($contactMessages)): ?>
                <tr><td colspan="6" style="text-align:center;color:var(--text-light)">Henüz mesaj bulunmuyor.</td></tr>
              <?php else: ?>
                <?php foreach($contactMessages as $cm): ?>
                <tr>
                  <td style="white-space:nowrap;font-size:12px;color:var(--text-light)"><?= date('d.m.Y H:i', strtotime($cm['created_at'])) ?></td>
                  <td style="font-weight:600"><?= htmlspecialchars($cm['name']) ?></td>
                  <td>
                    <div><a href="mailto:<?= htmlspecialchars($cm['email']) ?>" style="color:var(--gold)"><?= htmlspecialchars($cm['email']) ?></a></div>
                    <?php if(!empty($cm['phone'])): ?><div style="font-size:12px;color:var(--text-light)"><?= htmlspecialchars($cm['phone']) ?></div><?php endif; ?>
                  </td>
                  <td style="font-weight:500"><?= htmlspecialchars($cm['subject']) ?: '-' ?></td>
                  <td style="max-width:300px;font-size:13px;line-height:1.4"><?= nl2br(htmlspecialchars($cm['message'])) ?></td>
                  <td>
                    <a href="?delete_msg=<?= $cm['id'] ?>" class="btn btn-red btn-sm" onclick="return confirm('Bu mesajı kalıcı olarak silmek istiyor musunuz?')">🗑 Sil</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
function showTab(name) {
  document.querySelectorAll('.tab-panel').forEach(t => t.classList.remove('active'));
  document.getElementById('tab-'+name).classList.add('active');
  
  document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
  const activeLink = document.getElementById('link-'+name);
  if(activeLink) activeLink.classList.add('active');

  closeSidebar();
}

function navToTab(name) {
  if (window.location.search.includes('edit') || window.location.search.includes('edit_cat')) {
    if (name === 'items') {
      window.location.href = 'dashboard.php';
    } else {
      window.location.href = 'dashboard.php?tab=' + name;
    }
  } else {
    showTab(name);
  }
}

function toggleSidebar() {
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  if (sidebar && overlay) {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('active');
  }
}

function closeSidebar() {
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  if (sidebar && overlay) {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
  }
}

function toggleSelectAllCats(source) {
  document.querySelectorAll('.cat-checkbox').forEach(cb => {
    cb.checked = source.checked;
  });
}

function toggleSelectAllProducts(source) {
  document.querySelectorAll('.product-checkbox').forEach(cb => {
    cb.checked = source.checked;
  });
}

function filterCat(btn, cat) {
  document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('#itemTable tbody tr').forEach(row => {
    row.style.display = (cat==='all' || row.dataset.cat===cat) ? '' : 'none';
  });
}

function filterQuickPrices() {
  const query = document.getElementById('quickPriceSearch').value.toLowerCase();
  const rows = document.querySelectorAll('#tab-prices .menu-table tbody tr');
  rows.forEach(row => {
    const productName = row.cells[0].textContent.toLowerCase();
    const categoryName = row.cells[1].textContent.toLowerCase();
    if (productName.includes(query) || categoryName.includes(query)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

<?php if($editItem): ?>
  showTab('add');
<?php elseif($editCategory): ?>
  showTab('add-cat');
<?php elseif(isset($_GET['tab'])): ?>
  showTab('<?= htmlspecialchars($_GET['tab']) ?>');
<?php endif; ?>

// Modal zoom functions
function openImageModal(src) {
  const modal = document.getElementById('imageModal');
  const modalImg = document.getElementById('imgModalTarget');
  if (modal && modalImg) {
    modalImg.src = src;
    modal.classList.add('active');
  }
}
function closeImageModal() {
  const modal = document.getElementById('imageModal');
  if (modal) {
    modal.classList.remove('active');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  // Back button interception
  history.pushState(null, null, window.location.href);
  window.addEventListener('popstate', function (event) {
    history.pushState(null, null, window.location.href);
    Swal.fire({
      title: 'Panelden Çıkılıyor',
      text: 'Oturumunuz kapatılacaktır. Çıkış yapmak istediğinize emin misiniz?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#c9a84c',
      cancelButtonColor: '#2c2219',
      confirmButtonText: 'Evet, Çıkış Yap',
      cancelButtonText: 'Vazgeç',
      background: '#faf6f0',
      color: '#3a2e22',
      backdrop: 'rgba(26,21,17,0.7)'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'logout.php';
      }
    });
  });

  // Bind image modal click handlers
  document.querySelectorAll('.item-img').forEach(img => {
    img.addEventListener('click', function() {
      openImageModal(this.src);
    });
  });

  // --- Client-side Image Optimization ---
  const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
  
  imageInputs.forEach(input => {
    input.addEventListener('change', async (e) => {
      const file = e.target.files[0];
      if (!file) return;

      if (file.size < 200 * 1024) return;

      let maxWidth = 1000;
      let maxHeight = 1000;
      if (input.name === 'cat_banner_file') {
        maxWidth = 1200;
        maxHeight = 800;
      }

      input.disabled = true;
      
      let helper = input.parentNode.querySelector('.compress-helper');
      if (!helper) {
        helper = document.createElement('small');
        helper.className = 'compress-helper';
        helper.style.color = 'var(--gold-dark)';
        helper.style.display = 'block';
        helper.style.marginTop = '4px';
        input.parentNode.appendChild(helper);
      }
      helper.textContent = '⏳ Görsel optimize ediliyor (sıkıştırılıyor)...';

      try {
        const compressedBlob = await compressImage(file, maxWidth, maxHeight, 0.82);
        
        if (compressedBlob.size < file.size) {
          const compressedFile = new File([compressedBlob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
            type: 'image/jpeg',
            lastModified: Date.now()
          });

          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(compressedFile);
          input.files = dataTransfer.files;
          
          const oldSize = (file.size / 1024 / 1024).toFixed(2);
          const newSize = (compressedBlob.size / 1024).toFixed(0);
          helper.textContent = `✓ Görsel optimize edildi: ${oldSize} MB ➔ ${newSize} KB`;
          helper.style.color = 'var(--green)';
        } else {
          helper.textContent = '✓ Görsel zaten ideal boyutta.';
          helper.style.color = 'var(--green)';
        }
      } catch (err) {
        console.error(err);
        helper.textContent = '⚠ Optimizasyon yapılamadı, orijinal dosya yükleniyor.';
        helper.style.color = 'var(--red)';
      } finally {
        input.disabled = false;
      }
    });
  });
});

function compressImage(file, maxWidth, maxHeight, quality) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = function(e) {
      const img = new Image();
      img.onload = function() {
        let width = img.width;
        let height = img.height;

        if (width > height) {
          if (width > maxWidth) {
            height = Math.round((height * maxWidth) / width);
            width = maxWidth;
          }
        } else {
          if (height > maxHeight) {
            width = Math.round((width * maxHeight) / height);
            height = maxHeight;
          }
        }

        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, width, height);

        canvas.toBlob((blob) => {
          if (blob) {
            resolve(blob);
          } else {
            reject(new Error('Canvas toBlob failed'));
          }
        }, 'image/jpeg', quality);
      };
      img.onerror = () => reject(new Error('Image load failed'));
      img.src = e.target.result;
    };
    reader.onerror = () => reject(new Error('FileReader failed'));
    reader.readAsDataURL(file);
  });
}

function toggleFeaturedFields(chk) {
  const container = document.getElementById('featured-fields-container');
  if (chk.checked) {
    container.style.display = 'contents';
  } else {
    container.style.display = 'none';
  }
}
</script>

<!-- Image Modal/Zoom Markup -->
<div id="imageModal" class="image-modal" onclick="closeImageModal()">
  <span class="close-modal">&times;</span>
  <img class="image-modal-content" id="imgModalTarget" alt="Büyütülmüş Ürün Görseli">
</div>

<!-- Otomatik Güvenli Çıkış (İnaktivite Takibi ve Sekmeler Arası Eşleme) -->
<script>
(function() {
    const timeoutDuration = <?= SESSION_TIMEOUT ?> * 1000; // milisaniye cinsinden
    let lastActivity = Date.now();

    // Son aktivite zamanını localStorage'a yazarak diğer sekmelerle senkronize ediyoruz
    localStorage.setItem('admin_last_activity', lastActivity);

    function updateActivity() {
        lastActivity = Date.now();
        localStorage.setItem('admin_last_activity', lastActivity);
    }

    // Kullanıcının aktif olduğunu gösteren olayları dinle
    const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
    events.forEach(eventName => {
        document.addEventListener(eventName, updateActivity, true);
    });

    // Diğer sekmelerdeki hareketleri dinle
    window.addEventListener('storage', (e) => {
        if (e.key === 'admin_last_activity') {
            lastActivity = parseInt(e.newValue, 10);
        }
    });

    // Her saniye kontrol et
    const interval = setInterval(() => {
        const inactiveTime = Date.now() - lastActivity;
        if (inactiveTime >= timeoutDuration) {
            clearInterval(interval);
            // Oturumu kapatmak üzere yönlendir
            window.location.href = 'logout.php?reason=timeout';
        }
    }, 1000);
})();
</script>

</body>
</html>
