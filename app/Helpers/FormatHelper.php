<?php

if (!function_exists('formatBytes')) {
    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @return string
     */
    function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 0) {
            return $bytes . ' bytes';
        } else {
            return '0 bytes';
        }
    }
}

if (!function_exists('normalizeArabicText')) {
    /**
     * تطبيع النص العربي للبحث المرن
     * يحول الحروف المتشابهة إلى شكل موحد
     * أ -> ا، ة -> ه، ى -> ي
     * ويزيل المسافات الزائدة
     *
     * @param string $text
     * @return string
     */
    function normalizeArabicText($text)
    {
        $text = (string) $text;
        $text = normalizeArabicLetters($text);
        // إزالة المسافات المتعددة
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}

if (!function_exists('normalizeArabicLetters')) {
    /**
     * توحيد الحروف العربية المتشابهة فقط (بدون المساس بالمسافات)
     * أ/إ/آ/ٱ -> ا، ة -> ه، ى/ئ/ؤ -> ي/و، إزالة التشكيل والكشيدة
     *
     * @param string $text
     * @return string
     */
    function normalizeArabicLetters($text)
    {
        $text = (string) $text;
        // إزالة التشكيل (الحركات) والكشيدة (ـ) أولاً
        $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{0640}]/u', '', $text) ?? (string) $text;
        // تحويل ألف إلى ا (بما فيها ألف الوصل ٱ)
        $text = str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $text);
        // تحويل تاء مربوطة إلى هاء
        $text = str_replace('ة', 'ه', $text);
        // تحويل ياء مقصورة وهمزة على نبرة إلى ياء
        $text = str_replace(['ى', 'ئ'], 'ي', $text);
        // تحويل همزة على واو إلى واو
        $text = str_replace('ؤ', 'و', $text);
        // تحويل الأرقام العربية والمشرقية إلى لاتينية
        $arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $latinDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $text = str_replace($arabicDigits, $latinDigits, $text);

        return $text;
    }
}

if (!function_exists('sqlNormalizeExpression')) {
    /**
     * تعبير SQL يطبّع عموداً نصياً داخل الاستعلام (أ/إ/آ/ٱ -> ا، ة -> ه، ى/ئ -> ي، ؤ -> و)
     * مع إزالة التشكيل والكشيدة وتوحيد الأرقام العربية، دون تعديل البيانات المخزنة.
     *
     * @param string $column اسم العمود أو تعبير SQL (يُستخدم حرفياً داخل REPLACE)
     * @return string
     */
    function sqlNormalizeExpression($column)
    {
        $pairs = [
            ['أ', 'ا'],
            ['إ', 'ا'],
            ['آ', 'ا'],
            ['ٱ', 'ا'],
            ['ة', 'ه'],
            ['ى', 'ي'],
            ['ئ', 'ي'],
            ['ؤ', 'و'],
            // التشكيل والكشيدة
            ['ـ', ''],
            ['ً', ''], ['ٌ', ''], ['ٍ', ''], ['َ', ''], ['ُ', ''], ['ِ', ''], ['ّ', ''], ['ْ', ''], ['ٰ', ''],
            // الأرقام العربية والمشرقية -> لاتينية
            ['٠', '0'], ['١', '1'], ['٢', '2'], ['٣', '3'], ['٤', '4'],
            ['٥', '5'], ['٦', '6'], ['٧', '7'], ['٨', '8'], ['٩', '9'],
            ['۰', '0'], ['۱', '1'], ['۲', '2'], ['۳', '3'], ['۴', '4'],
            ['۵', '5'], ['۶', '6'], ['۷', '7'], ['۸', '8'], ['۹', '9'],
        ];

        $expr = $column;
        foreach ($pairs as [$from, $to]) {
            $expr = "REPLACE({$expr},'{$from}','{$to}')";
        }

        return $expr;
    }
}

if (!function_exists('escapeLike')) {
    /**
     * تهريب محارف LIKE الخاصة حتى تُبحث حرفياً: % _ \
     *
     * @param mixed $value
     * @return string
     */
    function escapeLike($value)
    {
        return addcslashes((string) $value, '%_\\\\');
    }
}

if (!function_exists('normalizeArabicWords')) {
    /**
     * تطبيع النص العربي وتقسيمه إلى كلمات للبحث المتقدم
     * يحول الحروف المتشابهة إلى شكل موحد
     * أ -> ا، ة -> ه، ى -> ي
     * ويعيد مصفوفة من الكلمات المطبعة
     *
     * @param string $text
     * @return array
     */
    function normalizeArabicWords($text)
    {
        $text = trim($text);
        // تحويل ألف إلى ا
        $text = str_replace(['أ', 'إ', 'آ'], 'ا', $text);
        // تحويل تاء مربوطة إلى هاء
        $text = str_replace('ة', 'ه', $text);
        // تحويل ياء مقصورة إلى ياء
        $text = str_replace('ى', 'ي', $text);
        // إزالة المسافات المتعددة وتقسيم النص
        $text = preg_replace('/\s+/', ' ', $text);
        // تقسيم النص إلى كلمات وإزالة الكلمات الفارغة
        $words = array_filter(explode(' ', $text), function($word) {
            return trim($word) !== '';
        });
        return array_values($words);
    }
}
