<?php
ini_set('display_errors', 1);

error_reporting(E_ALL);

/**

 * Excel Rapor – Composer yok, PhpSpreadsheet-5.3.0 klasörü kullanıyor

 * Kök dizine at: htdocs/excel.php

 * Kullanım: excel.php?filter=monthly&from=2025-11-01&to=2025-11-30&token=XYZ

 */



 session_start();
 require_once "config/database.php";
 require_once "business/auth.php";      // <-- ekle
 require_once __DIR__ . '/PhpSpreadsheet-5.3.0/vendor/autoload.php';

// CSRF

if (!hash_equals($_SESSION['csrf_token'] ?? '', $_GET['token'] ?? '')) {
    die('❌ CSRF hatası');
}



$business_id = BUSINESS_ID;



// Tarih filtresi (reports.php ile aynı)

$filter = $_GET['filter'] ?? 'monthly';

$date_from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));

$date_to   = $_GET['to']   ?? date('Y-m-d');



switch ($filter) {

    case 'today': $date_from = $date_to = date('Y-m-d'); break;

    case 'weekly': $date_from = date('Y-m-d', strtotime('-7 days')); break;

    case 'yearly': $date_from = date('Y-m-d', strtotime('-1 year')); break;

}



// Verileri çek

$stmt = $pdo->prepare("

    SELECT o.id, u.name AS customer_name, u.phone, o.created_at, o.total_price,

           CASE o.payment_method 

               WHEN 'online' THEN 'Online POS'

               WHEN 'kapida_pos' THEN 'Kapıda POS'

               ELSE 'Kapıda Nakit'

           END AS payment_type,

           o.status

    FROM orders o

    JOIN users u ON o.customer_id = u.id

    WHERE o.business_id = ? AND o.created_at BETWEEN ? AND ?

    ORDER BY o.created_at DESC

");

$stmt->execute([$business_id, $date_from, $date_to . ' 23:59:59']);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);



// --------- PhpSpreadsheet Autoload ---------

$autoload = __DIR__ . '/PhpSpreadsheet-5.3.0/vendor/autoload.php';

if (!file_exists($autoload)) {

    die('❌ PhpSpreadsheet autoload bulunamadı! Klasörü kontrol et: PhpSpreadsheet-5.3.0/vendor/autoload.php');

}

require_once $autoload;



use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\IOFactory;



// HTTP başlıkları

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

header('Content-Disposition: attachment;filename="rapor_' . $business_id . '_' . date('Y-m-d') . '.xlsx"');

header("Cache-Control: max-age=0");



// Spreadsheet oluştur

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Rapor');



// Başlıklar

$headers = ['SİPARİŞ NO', 'MÜŞTERİ', 'TELEFON', 'TARİH', 'TUTAR (₺)', 'ÖDEME', 'DURUM'];

$sheet->fromArray([$headers], NULL, 'A1');



// Verileri dök

$row = 2;

foreach ($orders as $o) {

    $sheet->fromArray([

        $o['id'],

        $o['customer_name'],

        $o['phone'],

        date('d.m.Y H:i', strtotime($o['created_at'])),

        number_format($o['total_price'], 2),

        $o['payment_type'],

        strtoupper($o['status'])

    ], NULL, "A$row");

    $row++;

}



// Stil

$sheet->getStyle('A1:G1')->getFont()->setBold(true);

foreach (range('A', 'G') as $col) {

    $sheet->getColumnDimension($col)->setAutoSize(true);

}



// İndir

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

$writer->save('php://output');

exit;