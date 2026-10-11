<?php
$file = __DIR__ . '/../fawatweeer.xlsx';
$zip = new ZipArchive(); $zip->open($file);
$ws = 'xl/worksheets/sheet1.xml';
$wr = new XMLReader();
$wr->open('zip://' . str_replace('\\','/',$file) . '#' . $ws, null, LIBXML_NONET);
$rows = 0; $start = microtime(true);
while ($wr->read()) { if ($wr->nodeType===XMLReader::ELEMENT && $wr->localName==='row') $rows++; }
$wr->close();
echo "rows=$rows  time=" . round(microtime(true)-$start,1) . "s\n";
