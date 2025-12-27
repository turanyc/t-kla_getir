<?php
session_start();
require_once "../config/database.php";

// ---------- GÜVENLİK ----------
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'courier') {
    header("Location: ../login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// ---------- KURYE ID ----------
$cou = $pdo->prepare("SELECT id FROM couriers WHERE user_id = ?");
$cou->execute([$user_id]);
$courier_id = $cou->fetchColumn();
if (!$courier_id) {
    die("Kurye kaydınız bulunamadı.");
}

// ---------- TARİH FİLTRESİ ----------
$start = $_GET['start'] ?? date('Y-m-01');
$end   = $_GET['end']   ?? date('Y-m-t');
$excel = isset($_GET['excel']);

// ---------- RAPOR SORGUSU ----------
$sql = "
SELECT
    o.id                                    AS order_id,
    COALESCE(b.name, r.name, 'Bilinmiyor') AS restaurant_name,
    o.total_price,
    o.delivered_at,
    cp.paid_at,
    cp.amount                               AS rest_paid,
    (o.total_price * c.commission_rate/100) AS courier_earn
FROM orders o
JOIN couriers c ON c.id = o.courier_id
LEFT JOIN businesses b ON b.id = o.business_id
LEFT JOIN restaurants r ON r.id = o.restaurant_id
LEFT JOIN courier_payment_confirm cp ON cp.order_id = o.id
WHERE o.courier_id = :cid
  AND o.status = 'teslim'
  AND DATE(o.delivered_at) BETWEEN :ds AND :de
ORDER BY o.delivered_at DESC
";
$st = $pdo->prepare($sql);
$st->execute([
    ':cid' => $courier_id,
    ':ds'  => $start,
    ':de'  => $end
]);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

// ---------- ÖZET ----------
$totDelivery = count($rows);
$totEarn     = array_sum(array_column($rows, 'courier_earn'));
$totRestPay  = array_sum(array_filter(array_column($rows, 'rest_paid')));

// ---------- EXCEL ÇIKTISI ----------
if ($excel) {
    require_once "../vendor/autoload.php";

    // BURAYA DİKKAT: use satırları namespace'ten SONRA gelmeli → burada bir fonksiyon içinde olduğundan geçici namespace tanımlıyoruz
    $loader = new \Nette\Loaders\RobotLoader();
    // PhpSpreadsheet'i doğru şekilde yükle
    if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
        die("PhpSpreadsheet kütüphanesi yüklü değil. Lütfen composer ile kurun: composer require phpoffice/phpspreadsheet");
    }

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Kurye Raporu');

    $headers = ['Sipariş No','Restoran','Tutar (₺)','Teslim Tarihi','Rest.Ödeme Tarihi','Rest.Ödeme (₺)','Kazancın (₺)'];
    $sheet->fromArray($headers, null, 'A1');
    $sheet->getStyle('A1:G1')->getFont()->setBold(true);

    foreach ($rows as $k => $v) {
        $sheet->fromArray([
            $v['order_id'],
            $v['restaurant_name'],
            $v['total_price'],
            $v['delivered_at'] ? date('d.m.Y H:i', strtotime($v['delivered_at'])) : '-',
            $v['paid_at'] ? date('d.m.Y H:i', strtotime($v['paid_at'])) : '-',
            $v['rest_paid'] ? number_format($v['rest_paid'], 2) : 0,
            number_format($v['courier_earn'], 2)
        ], null, 'A' . ($k + 2));
    }

    // Toplam satırı
    $lastRow = $totDelivery + 2;
    $sheet->setCellValue("A$lastRow", 'TOPLAM');
    $sheet->setCellValue("C$lastRow", $totDelivery . ' paket');
    $sheet->setCellValue("F$lastRow", number_format($totRestPay, 2) . ' ₺');
    $sheet->setCellValue("G$lastRow", number_format($totEarn, 2) . ' ₺');
    $sheet->getStyle("A$lastRow:G$lastRow")->getFont()->setBold(true);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="kurye_rapor_' . date('Y-m-d') . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Kurye Raporlarım</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { max-width: 100%; box-sizing: border-box; }
        @media (max-width: 768px) {
            .col-md-4 {
                width: 100% !important;
            }
            .d-flex.gap-2 {
                flex-direction: column;
            }
            .d-flex.gap-2 .btn {
                width: 100%;
            }
            .table-responsive {
                margin: 0 -15px;
                padding: 0 15px;
            }
        }
    </style>
</head>
<body class="bg-light" style="overflow-x: hidden;">
<div class="container py-4" style="padding: clamp(20px, 4vw, 30px);">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white flex-wrap gap-2" style="padding: clamp(15px, 3vw, 20px);">
            <h4 class="mb-0" style="font-size: clamp(18px, 3vw, 22px);"><i class="fa-solid fa-chart-line"></i> Kurye Raporlarım</h4>
            <a href="index.php" class="btn btn-light btn-sm" style="font-size: clamp(12px, 2.5vw, 14px);"><i class="fa fa-arrow-left"></i> Panele Dön</a>
        </div>
        <div class="card-body" style="padding: clamp(20px, 4vw, 30px);">
            <form method="get" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label" style="font-size: clamp(14px, 2.5vw, 16px);">Başlangıç</label>
                    <input type="date" name="start" class="form-control" value="<?= htmlspecialchars($start) ?>" style="font-size: clamp(14px, 2.5vw, 16px);">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size: clamp(14px, 2.5vw, 16px);">Bitiş</label>
                    <input type="date" name="end" class="form-control" value="<?= htmlspecialchars($end) ?>" style="font-size: clamp(14px, 2.5vw, 16px);">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2 flex-wrap">
                    <button class="btn btn-primary" style="font-size: clamp(12px, 2.5vw, 14px);"><i class="fa fa-filter"></i> Listele</button>
                    <button type="submit" name="excel" value="1" class="btn btn-success" style="font-size: clamp(12px, 2.5vw, 14px);">
                        <i class="fa fa-file-excel"></i> Excel İndir
                    </button>
                </div>
            </form>

            <div class="alert alert-info d-flex flex-wrap justify-content-between gap-3" style="font-size: clamp(14px, 2.5vw, 16px); padding: clamp(15px, 3vw, 20px);">
                <div><strong><?= $totDelivery ?></strong> teslimat</div>
                <div><strong><?= number_format($totEarn, 2) ?> ₺</strong> komisyon kazancın</div>
                <div><strong><?= number_format($totRestPay, 2) ?> ₺</strong> restoran ödemesi</div>
            </div>

            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table table-hover align-middle" style="min-width: 900px; font-size: clamp(12px, 2.5vw, 14px);">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Restoran</th>
                            <th>Tutar</th>
                            <th>Teslim Tarihi</th>
                            <th>Rest. Ödeme</th>
                            <th>Ödeme Tarihi</th>
                            <th class="text-end">Senin Kazancın</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4" style="padding: clamp(30px, 6vw, 40px); font-size: clamp(14px, 2.5vw, 16px);">Seçilen tarihlerde teslimat yok.</td></tr>
                        <?php else: ?>
                            <?php foreach ($rows as $r): ?>
                                <tr>
                                    <td><?= $r['order_id'] ?></td>
                                    <td><?= htmlspecialchars($r['restaurant_name']) ?></td>
                                    <td><?= number_format($r['total_price'], 2) ?> ₺</td>
                                    <td><?= $r['delivered_at'] ? date('d.m H:i', strtotime($r['delivered_at'])) : '-' ?></td>
                                    <td><?= $r['rest_paid'] ? number_format($r['rest_paid'], 2) . ' ₺' : '-' ?></td>
                                    <td><?= $r['paid_at'] ? date('d.m.Y H:i', strtotime($r['paid_at'])) : '-' ?></td>
                                    <td class="text-end fw-bold text-success"><?= number_format($r['courier_earn'], 2) ?> ₺</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>