<?php

/**
 * فحص حيّ لترويسات الأداء على السيرفر (الضغط Gzip + ترويسات الكاش).
 *
 * الاستخدام:
 *   php scripts/check_performance_headers.php http://jabalia.test
 *   php scripts/check_performance_headers.php http://localhost/jabalia/public
 *
 * يتحقق من:
 *   - Content-Encoding: gzip  على ملفات CSS/JS وصفحة HTML
 *   - Cache-Control: ... max-age=...  على الأصول الثابتة
 *   - مقدار التوفير في الحجم عند الضغط
 */

$base = $argv[1] ?? (getenv('PERF_BASE_URL') ?: 'http://jabalia.test');
$base = rtrim($base, '/');

$targets = [
    ['label' => 'CSS  views-theme.css',   'url' => '/assets/css/views-theme.css',            'gzip' => true,  'cache' => true],
    ['label' => 'JS   scripts.bundle.js', 'url' => '/assets/js/scripts.bundle.js',           'gzip' => true,  'cache' => true],
    ['label' => 'JS   plugins.bundle.js', 'url' => '/assets/plugins/global/plugins.bundle.js', 'gzip' => true, 'cache' => true],
    ['label' => 'HTML /login',            'url' => '/login',                                  'gzip' => true,  'cache' => false],
];

$pass = 0;
$fail = 0;

/**
 * إرجاع [headers, size] للطلب.
 */
function fetch(string $url, bool $gzip): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_HTTPHEADER     => $gzip ? ['Accept-Encoding: gzip, deflate'] : ['Accept-Encoding: identity'],
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);

        fwrite(STDERR, "  ! تعذّر الاتصال: {$error}\n");

        return ['', 0];
    }

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $size = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
    curl_close($ch);

    return [substr((string) $response, 0, $headerSize), (int) $size];
}

echo "== فحص ترويسات الأداء ==\n";
echo "الخادم: {$base}\n\n";

foreach ($targets as $t) {
    $url = $base . $t['url'];

    [$gzHeaders, $gzSize] = fetch($url, true);
    [$rawHeaders, $rawSize] = fetch($url, false);

    $status = preg_match('#HTTP/\S+\s+(\d+)#', $gzHeaders, $sm) ? (int) $sm[1] : 0;
    $hasGzip = stripos($gzHeaders, 'Content-Encoding: gzip') !== false;
    $hasCache = preg_match('/Cache-Control:[^\r\n]*max-age=(\d+)/i', $gzHeaders, $cm) === 1;

    echo "• {$t['label']}  [HTTP {$status}]\n";

    // فحص الضغط
    if ($t['gzip']) {
        if ($hasGzip) {
            $saved = $rawSize > 0 ? round(100 - ($gzSize / $rawSize * 100)) : 0;
            echo sprintf("    ✔ gzip: raw=%s B → %s B (توفير %d%%)\n", number_format($rawSize), number_format($gzSize), $saved);
            $pass++;
        } else {
            echo "    ✘ gzip: غير مُطبّق (Content-Encoding مفقود)\n";
            $fail++;
        }
    }

    // فحص الكاش
    if ($t['cache']) {
        if ($hasCache && (int) $cm[1] > 0) {
            echo "    ✔ cache: max-age={$cm[1]}\n";
            $pass++;
        } else {
            echo "    ✘ cache: ترويسة max-age مفقودة\n";
            $fail++;
        }
    }

    echo "\n";
}

echo "== النتيجة: {$pass} نجاح، {$fail} فشل ==\n";

exit($fail === 0 ? 0 : 1);
