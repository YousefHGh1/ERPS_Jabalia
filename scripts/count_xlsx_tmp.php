<?php
$file = __DIR__ . '/../بدون ارقام اشتراك جباية.xlsx';

$zip = new ZipArchive();
if ($zip->open($file) !== true) { echo "Cannot open\n"; exit(1); }

foreach (['xl/worksheets/sheet1.xml', 'xl/worksheets/sheet2.xml'] as $ws) {
    if ($zip->locateName($ws) === false) { echo "$ws: not found\n"; continue; }
    $start = microtime(true);
    $wr = new XMLReader();
    $wr->open('zip://' . str_replace('\\', '/', $file) . '#' . $ws, null, LIBXML_NONET);
    $rows = 0;
    while ($wr->read()) {
        if ($wr->nodeType === XMLReader::ELEMENT && $wr->localName === 'row') $rows++;
    }
    $wr->close();
    echo "$ws => rows: $rows  (" . round(microtime(true)-$start,1) . "s)\n";
}
