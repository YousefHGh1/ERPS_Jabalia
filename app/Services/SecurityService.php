<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SecurityService
{
    /**
     * تحقق من قوة كلمة المرور
     */
    public static function validatePasswordStrength(string $password): array
    {
        $errors = [];

        // الشرط الوحيد: الطول الأدنى 8 خانات
        if (strlen($password) < 8) {
            $errors[] = 'كلمة المرور يجب أن تكون 8 خانات على الأقل';
        }

        return [
            'is_strong' => empty($errors),
            'errors' => $errors,
            'score' => self::calculatePasswordScore($password)
        ];
    }

    /**
     * حساب درجة قوة كلمة المرور
     */
    private static function calculatePasswordScore(string $password): int
    {
        $score = 0;

        // طول
        $score += strlen($password) * 2;

        // تنوع الأحرف
        if (preg_match('/[a-z]/', $password)) $score += 10;
        if (preg_match('/[A-Z]/', $password)) $score += 10;
        if (preg_match('/[0-9]/', $password)) $score += 10;
        if (preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) $score += 15;

        // مكافأة للطول الزائد
        if (strlen($password) > 12) $score += 20;
        if (strlen($password) > 16) $score += 30;

        return min(100, $score);
    }

    /**
     * التحقق من محاولات تسجيل الدخول المشبوهة
     */
    public static function detectSuspiciousLogin(string $ip, string $email): array
    {
        $cacheKey = "login_attempts_{$ip}";
        $attempts = cache()->get($cacheKey, []);
        
        $suspicious = [];
        
        // أكثر من 5 محاولات في 5 دقائق
        $recentAttempts = collect($attempts)->filter(function ($attempt) {
            return $attempt['timestamp'] > now()->subMinutes(5)->timestamp;
        });

        if ($recentAttempts->count() > 5) {
            $suspicious[] = 'multiple_attempts';
        }

        // محاولات من عناوين بريد إلكتروني مختلفة
        $uniqueEmails = $recentAttempts->pluck('email')->unique();
        if ($uniqueEmails->count() > 3) {
            $suspicious[] = 'multiple_emails';
        }

        // تسجيل المحاولة
        $attempts[] = [
            'email' => $email,
            'timestamp' => now()->timestamp,
            'user_agent' => request()->userAgent()
        ];

        cache()->put($cacheKey, $attempts, 300); // 5 دقائق

        return [
            'is_suspicious' => !empty($suspicious),
            'reasons' => $suspicious,
            'attempts_count' => $recentAttempts->count()
        ];
    }

    /**
     * التحقق من صحة المدخلات
     */
    public static function sanitizeInput(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // إزالة HTML tags
                $value = strip_tags($value);
                
                // تحويل HTML entities
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                
                // إزالة مسافات زائدة
                $value = trim($value);
                
                // التحقق من XSS
                $value = self::removeXSS($value);
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    /**
     * إزالة XSS attacks
     */
    private static function removeXSS(string $string): string
    {
        $xssPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi',
            '/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/mi',
            '/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/mi',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
            '/onmouseover\s*=/i',
        ];

        foreach ($xssPatterns as $pattern) {
            $string = preg_replace($pattern, '', $string);
        }

        return $string;
    }

    /**
     * تسجيل الأحداث الأمنية
     */
    public static function logSecurityEvent(string $event, array $data = []): void
    {
        $logData = array_merge([
            'event' => $event,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
        ], $data);

        Log::channel('security')->warning('Security event', $logData);
    }

    /**
     * التحقق من صحة الملفات المرفوعة
     */
    public static function validateUploadedFile($file): array
    {
        $errors = [];
        
        // حجم الملف (5MB كحد أقصى)
        if ($file->getSize() > 5 * 1024 * 1024) {
            $errors[] = 'حجم الملف يتجاوز الحد المسموح (5MB)';
        }

        // نوع الملف المسموح
        $allowedTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain', 'text/csv'
        ];

        if (!in_array($file->getMimeType(), $allowedTypes)) {
            $errors[] = 'نوع الملف غير مسموح';
        }

        // التحقق من امتداد الملف
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            $errors[] = 'امتداد الملف غير مسموح';
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * إنشاء رمز أمان عشوائي
     */
    public static function generateSecureToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * التحقق من تاريخ انتهاء الصلاحية
     */
    public static function isTokenExpired(string $token, int $validHours = 24): bool
    {
        try {
            $data = json_decode(base64_decode($token), true);
            
            if (!$data || !isset($data['timestamp'])) {
                return true;
            }

            $tokenTime = Carbon::parse($data['timestamp']);
            $expiryTime = $tokenTime->copy()->addHours($validHours);

            return now()->gt($expiryTime);
        } catch (\Exception $e) {
            return true;
        }
    }
}
