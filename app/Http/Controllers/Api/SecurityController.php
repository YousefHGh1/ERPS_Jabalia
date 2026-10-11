<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SecurityController extends Controller
{
    /**
     * التحقق من قوة كلمة المرور
     */
    public function checkPasswordStrength(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $password = $request->get('password');
        $result = SecurityService::validatePasswordStrength($password);

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * التحقق من محاولات تسجيل الدخول المشبوهة
     */
    public function checkSuspiciousActivity(Request $request): JsonResponse
    {
        $ip = $request->ip();
        $email = $request->get('email', '');

        $result = SecurityService::detectSuspiciousLogin($ip, $email);

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * التحقق من صحة المدخلات
     */
    public function sanitizeInput(Request $request): JsonResponse
    {
        $data = $request->all();
        $sanitized = SecurityService::sanitizeInput($data);

        return response()->json([
            'success' => true,
            'data' => [
                'original' => $data,
                'sanitized' => $sanitized
            ]
        ]);
    }

    /**
     * التحقق من صحة الملف المرفوع
     */
    public function validateFile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');
        $result = SecurityService::validateUploadedFile($file);

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * إنشاء رمز أمان
     */
    public function generateToken(Request $request): JsonResponse
    {
        $length = $request->get('length', 32);
        $token = SecurityService::generateSecureToken($length);

        // إضافة timestamp للتحقق من انتهاء الصلاحية
        $tokenData = [
            'token' => $token,
            'timestamp' => now()->toISOString(),
            'user_id' => Auth::id()
        ];

        $signedToken = base64_encode(json_encode($tokenData));

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $signedToken,
                'expires_at' => now()->addHours(24)->toISOString()
            ]
        ]);
    }

    /**
     * التحقق من الرمز
     */
    public function verifyToken(Request $request): JsonResponse
    {
        $token = $request->get('token');
        $validHours = $request->get('valid_hours', 24);

        $isValid = !SecurityService::isTokenExpired($token, $validHours);

        return response()->json([
            'success' => true,
            'data' => [
                'is_valid' => $isValid,
                'expired' => !$isValid
            ]
        ]);
    }

    /**
     * تسجيل حدث أمني
     */
    public function logSecurityEvent(Request $request): JsonResponse
    {
        $event = $request->get('event');
        $data = $request->except(['event']);

        SecurityService::logSecurityEvent($event, $data);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الحدث الأمني بنجاح'
        ]);
    }

    /**
     * الحصول على معلومات أمان الجلسة
     */
    public function getSessionInfo(): JsonResponse
    {
        $user = Auth::user();
        $session = session()->all();

        $securityInfo = [
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'last_activity' => $user->last_activity ? $user->last_activity->toISOString() : null,
            'permissions' => $user->getAllPermissions()->toArray(),
            'roles' => $user->roles->pluck('name')->toArray(),
        ];

        return response()->json([
            'success' => true,
            'data' => $securityInfo
        ]);
    }

    /**
     * تسجيل الخروج من جميع الأجهزة
     */
    public function logoutAllDevices(): JsonResponse
    {
        $user = Auth::user();
        
        // حذف جميع الجلسات الأخرى
        \DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->delete();

        SecurityService::logSecurityEvent('logout_all_devices', [
            'sessions_terminated' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج من جميع الأجهزة الأخرى'
        ]);
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        
        // التحقق من كلمة المرور الحالية
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'كلمة المرور الحالية غير صحيحة'
            ], 422);
        }

        // فحص قوة كلمة المرور الجديدة
        $passwordCheck = SecurityService::validatePasswordStrength($request->new_password);
        
        if (!$passwordCheck['is_strong']) {
            return response()->json([
                'success' => false,
                'message' => 'كلمة المرور الجديدة غير قوية بما فيه الكفاية',
                'errors' => $passwordCheck['errors']
            ], 422);
        }

        // تحديث كلمة المرور
        $user->update([
            'password' => Hash::make($request->new_password),
            'password_changed_at' => now(),
        ]);

        SecurityService::logSecurityEvent('password_changed', [
            'password_strength' => $passwordCheck['score']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح'
        ]);
    }
}
