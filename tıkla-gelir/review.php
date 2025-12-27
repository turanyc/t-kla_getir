<?php
session_start();
require_once "config/database.php";

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=review.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$restaurant_id = (int)($_GET['rid'] ?? 0);

if ($restaurant_id <= 0) {
    die("Geçersiz restoran ID!");
}

// Sipariş teslim edildi mi kontrolü
$can_review = false;
try {
    $order_stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders 
                                WHERE customer_id = ? 
                                AND restaurant_id = ? 
                                AND status = 'teslim' 
                                AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"); // Son 30 gün içinde
    $order_stmt->execute([$user_id, $restaurant_id]);
    $order_result = $order_stmt->fetch();
    $can_review = $order_result['count'] > 0;
} catch(PDOException $e) {
    // orders tablosu yoksa veya hata olursa
    $can_review = false;
}

// Eğer teslim edilmiş sipariş yoksa veya review yetkisi yoksa
if (!$can_review) {
    die('<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="font-family:Poppins; text-align:center; padding:50px;">
         <div style="background:#f8f9fa; padding:40px; border-radius:20px; max-width:500px; margin:0 auto;">
         <h2 style="color:#FF6B35;">❌ Erişim Engellendi</h2>
         <p style="margin:20px 0; color:#666;">Restoranı puanlayabilmek için önce sipariş vermeli ve siparişinin teslim edilmiş olması gerekmektedir.</p>
         <a href="index.php" style="background: linear-gradient(135deg, #FF6B35, #FF4500); color:white; padding:12px 30px; border-radius:50px; text-decoration:none; font-weight:600;">Ana Sayfaya Dön</a>
         </div></body></html>');
}

// Mevcut yorum kontrolü
$existing_review = null;
try {
    $stmt_check = $pdo->prepare("SELECT * FROM reviews WHERE restaurant_id = ? AND user_id = ?");
    $stmt_check->execute([$restaurant_id, $user_id]);
    $existing_review = $stmt_check->fetch();
} catch(PDOException $e) {
    $existing_review = null;
}

// Form gönderildiğinde
$message = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        
        if ($rating < 1 || $rating > 5) {
            $message = ['type' => 'danger', 'text' => '⭐ Lütfen 1-5 arası puan verin!'];
        } elseif (empty($comment)) {
            $message = ['type' => 'danger', 'text' => '📝 Yorum alanı boş bırakılamaz!'];
        } else {
            if ($existing_review) {
                // Güncelle
                $stmt = $pdo->prepare("UPDATE reviews SET rating = ?, comment = ?, created_at = NOW() WHERE id = ? AND user_id = ?");
                $stmt->execute([$rating, $comment, $existing_review['id'], $user_id]);
                $message = ['type' => 'success', 'text' => '✅ Yorumunuz güncellendi!'];
            } else {
                // Yeni yorum
                $stmt = $pdo->prepare("INSERT INTO reviews (restaurant_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
                $stmt->execute([$restaurant_id, $user_id, $rating, $comment]);
                $message = ['type' => 'success', 'text' => '✅ Yorumunuz başarıyla eklendi!'];
            }
            
            // 2 saniye sonra menu.php'ye yönlendir
            header("refresh:2;url=menu.php?rid=$restaurant_id");
        }
    } catch(PDOException $e) {
        $message = ['type' => 'danger', 'text' => '❌ Hata: ' . $e->getMessage()];
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoranı Değerlendir - <?= htmlspecialchars($restaurant['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            overflow-x: hidden;
        }
        .review-container {
            background: white;
            border-radius: 25px;
            padding: clamp(25px, 5vw, 40px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            border: 1px solid #eee;
            min-width: 0;
        }
        .restaurant-info {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #FFE0B2;
        }
        .restaurant-name {
            font-size: clamp(24px, 5vw, 32px);
            font-weight: 800;
            color: #FF6B35;
            margin-bottom: 10px;
        }
        .rating-stars {
            font-size: clamp(32px, 6vw, 48px);
            color: #FFE0B2;
            cursor: pointer;
            transition: color 0.2s;
            text-align: center;
            margin: 25px 0;
        }
        .rating-stars .star:hover,
        .rating-stars .star.active {
            color: #FFD700;
        }
        .form-control {
            border-radius: 15px;
            padding: 15px;
            border: 2px solid #FFE0B2;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #FF6B35;
            box-shadow: 0 0 0 0.25rem rgba(255,107,53,0.15);
        }
        .btn-submit {
            background: linear-gradient(135deg, #FF6B35, #FF4500);
            border: none;
            border-radius: 50px;
            padding: 15px 30px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(255,107,53,0.3);
        }
        .existing-review {
            background: #FFF8F0;
            border: 2px solid #FFE0B2;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .existing-review .your-rating {
            color: #FF6B35;
            font-size: clamp(18px, 4vw, 24px);
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            body { padding: 10px; }
            .review-container { padding: 25px; border-radius: 20px; }
            .restaurant-info { padding-bottom: 15px; }
        }
        
        @media (max-width: 480px) {
            .review-container { padding: 20px; }
            .restaurant-name { font-size: 20px; }
            .rating-stars { font-size: 36px; margin: 20px 0; }
            .form-control { padding: 12px; }
            .btn-submit { padding: 12px 25px; }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>

<div class="review-container">
    <!-- Bildirim -->
    <?php if(!empty($message)): ?>
    <div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show" role="alert">
        <?= $message['text'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Restoran Bilgisi -->
    <div class="restaurant-info">
        <div class="restaurant-name"><?= htmlspecialchars($restaurant['name']) ?></div>
        <div style="color:#666;font-size:18px;">Siparişinizi değerlendirin</div>
    </div>

    <?php if($existing_review): ?>
    <!-- Mevcut Yorum -->
    <div class="existing-review">
        <h5 class="text-center" style="color:#FF6B35;margin-bottom:15px;"><i class="bi bi-chat-square-text-fill"></i> Mevcut Yorumunuz</h5>
        <div class="your-rating text-center"><?= str_repeat('★', $existing_review['rating']) . str_repeat('☆', 5 - $existing_review['rating']) ?></div>
        <p style="text-align:center;"><?= htmlspecialchars($existing_review['comment']) ?></p>
        <hr>
    </div>
    <?php endif; ?>

    <!-- Yorum Formu -->
    <form method="post" id="reviewForm">
        <!-- Yıldızlar -->
        <div class="mb-3">
            <label class="form-label" style="font-weight:700;color:#FF6B35;"><i class="bi bi-star-fill"></i Puanınız:</label>
            <div class="rating-stars" id="ratingStars">
                <span class="star" data-rating="1">★</span>
                <span class="star" data-rating="2">★</span>
                <span class="star" data-rating="3">★</span>
                <span class="star" data-rating="4">★</span>
                <span class="star" data-rating="5">★</span>
            </div>
            <input type="hidden" name="rating" id="ratingInput" required>
        </div>

        <!-- Yorum Metni -->
        <div class="mb-3">
            <label class="form-label" style="font-weight:700;color:#FF6B35;"><i class="bi bi-chat-square-text-fill"></i> Yorumunuz:</label>
            <textarea name="comment" class="form-control" rows="5" placeholder="Restoran hakkındaki deneyimlerinizi paylaşın..." required><?= $existing_review['comment'] ?? '' ?></textarea>
        </div>

        <!-- Butonlar -->
        <div class="d-grid gap-2">
            <button type="submit" class="btn-submit">
                <?= $existing_review ? 'Yorumu Güncelle' : 'Yorum Yap' ?>
            </button>
            <a href="menu.php?rid=<?= $restaurant_id ?>" class="btn btn-secondary btn-lg" style="border-radius:50px;">İptal</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Yıldız seçme
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('ratingInput');
    const existingRating = <?= $existing_review['rating'] ?? 0 ?>;
    
    if (existingRating > 0) {
        ratingInput.value = existingRating;
        for(let i = 0; i < existingRating; i++) {
            stars[i].classList.add('active');
        }
    }

    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            ratingInput.value = rating;
            
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });

        // Hover efekti
        star.addEventListener('mouseenter', function() {
            const hoverRating = this.getAttribute('data-rating');
            stars.forEach((s, index) => {
                if (index < hoverRating) {
                    s.style.color = '#FFD700';
                } else {
                    s.style.color = '#FFE0B2';
                }
            });
        });
    });

    // Mouse çıkınca aktif olanı göster
    document.getElementById('ratingStars').addEventListener('mouseleave', function() {
        stars.forEach((s, index) => {
            if (s.classList.contains('active')) {
                s.style.color = '#FFD700';
            } else {
                s.style.color = '#FFE0B2';
            }
        });
    });
});
</script>

</body>
</html>