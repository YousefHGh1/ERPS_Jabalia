<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'fawatweeer.xlsx';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

echo "عدد الصفوف: " . count($rows) . PHP_EOL;
echo "عدد الأعمدة: " . count($rows[0]) . PHP_EOL;
echo PHP_EOL;
echo "أسماء الأعمدة (الصف الأول):" . PHP_EOL;
foreach ($rows[0] as $i => $col) {
    echo chr(65 + $i) . '. ' . $col . PHP_EOL;
}
echo PHP_EOL;
echo "أول 3 صفوف من البيانات:" . PHP_EOL;
for ($i = 1; $i <= min(3, count($rows) - 1); $i++) {
    echo "الصف $i: " . PHP_EOL;
    foreach ($rows[$i] as $j => $col) {
        echo "  " . chr(65 + $j) . ": " . $col . PHP_EOL;
    }
    echo PHP_EOL;
}
