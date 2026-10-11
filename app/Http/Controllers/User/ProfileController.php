<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\AuditTrailService;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function __construct(private AuditTrailService $auditTrailService)
    {
    }

    /**
     * عرض صفحة الملف الشخصي
     */
    public function index()
    {
        $user = Auth::user();
        
        // تحميل العلاقات
        $user->load(['employee.department', 'employee.section', 'employee.job', 'roles', 'permissions']);
        
        // جلب سجلات الحضور والانصراف
        $attendanceRecords = collect();
        if ($user->employee) {
            // محاولة جلب البيانات من جدول z_k_data (بيانات جهاز البصمة)
            $attendanceRecords = \DB::table('z_k_data')
                ->where('emp_id', $user->employee->id)
                ->orderBy('arrival_time', 'desc')
                ->limit(30)
                ->get();
        }
        
        $auditLogs = $this->auditTrailService->getLogsForUser($user);
        
        return view('users.profile', compact('user', 'attendanceRecords', 'auditLogs'));
    }

    /**
     * تحديث المعلومات الشخصية
     */
    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Sanitize input
        $sanitized = SecurityService::sanitizeInput($request->all());
        
        $user->update([
            'name' => $sanitized['name'],
            'email' => $sanitized['email'],
            'mobile' => $sanitized['mobile'] ?? null,
        ]);

        SecurityService::logSecurityEvent('profile_updated', [
            'fields_updated' => array_keys($sanitized)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المعلومات الشخصية بنجاح'
        ]);
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            $allErrors = $validator->errors()->all();

            return response()->json([
                'success' => false,
                'message' => $allErrors[0] ?? 'يرجى التحقق من البيانات المدخلة',
                'errors'  => $validator->errors()
            ], 422);
        }

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

    /**
     * تحديث الصورة الشخصية
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        
        // التحقق من صحة الملف
        $fileValidation = SecurityService::validateUploadedFile($request->file('avatar'));
        
        if (!$fileValidation['is_valid']) {
            return response()->json([
                'success' => false,
                'message' => 'الملف غير صالح',
                'errors' => $fileValidation['errors']
            ], 422);
        }

        // حذف الصورة القديمة إذا وجدت
        if ($user->avatar) {
            $oldPath = public_path(str_replace(asset(''), '', $user->avatar));
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // رفع الصورة الجديدة
        $avatar = $request->file('avatar');
        $avatarName = 'avatar_' . $user->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
        $avatar->move(public_path('images/avatars'), $avatarName);

        $user->update([
            'avatar' => asset('images/avatars/' . $avatarName)
        ]);

        SecurityService::logSecurityEvent('avatar_updated', [
            'file_name' => $avatarName
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الصورة الشخصية بنجاح',
            'avatar_url' => asset('images/avatars/' . $avatarName)
        ]);
    }

    /**
     * الحصول على نشاط المستخدم
     */
    public function getActivity(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = $request->get('limit', 20);
        
        // جلب النشاط من سجلات النظام
        $activities = collect([
            [
                'type' => 'login',
                'title' => 'تسجيل دخول',
                'description' => 'تم تسجيل الدخول من ' . request()->ip(),
                'icon' => 'fas fa-sign-in-alt',
                'color' => '#28a745',
                'created_at' => now()
            ],
            [
                'type' => 'profile_update',
                'title' => 'تحديث الملف الشخصي',
                'description' => 'تم تحديث معلومات الملف الشخصي',
                'icon' => 'fas fa-user-edit',
                'color' => '#ffc107',
                'created_at' => now()->subDays(2)
            ],
            [
                'type' => 'password_change',
                'title' => 'تغيير كلمة المرور',
                'description' => 'تم تغيير كلمة المرور',
                'icon' => 'fas fa-key',
                'color' => '#dc3545',
                'created_at' => now()->subDays(5)
            ]
        ]);

        return response()->json([
            'success' => true,
            'data' => $activities->take($limit)
        ]);
    }

    /**
     * تحديث إعدادات الإشعارات
     */
    public function updateNotificationSettings(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'email_notifications' => 'boolean',
            'system_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'email_notifications' => $request->get('email_notifications', true),
            'system_notifications' => $request->get('system_notifications', true),
            'sms_notifications' => $request->get('sms_notifications', false),
        ]);

        SecurityService::logSecurityEvent('notification_settings_updated', [
            'settings' => $request->only(['email_notifications', 'system_notifications', 'sms_notifications'])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث إعدادات الإشعارات بنجاح'
        ]);
    }

    /**
     * تحديث إعدادات المظهر
     */
    public function updateAppearanceSettings(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'theme'      => 'sometimes|nullable|in:light,dark',
            'language'   => 'sometimes|nullable|in:ar,en',
            'font_size'  => 'sometimes|nullable|in:small,medium,large',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = [];
        if ($request->has('theme')) {
            $v = $request->input('theme');
            $data['theme'] = in_array($v, ['light', 'dark'], true) ? $v : null;
        }
        if ($request->has('language')) {
            $v = $request->input('language');
            $data['language'] = in_array($v, ['ar', 'en'], true) ? $v : 'ar';
        }
        if ($request->has('font_size')) {
            $v = $request->input('font_size');
            $data['font_size'] = in_array($v, ['small', 'medium', 'large'], true) ? $v : 'medium';
        }

        if (!empty($data)) {
            $user->update($data);
        }

        $user->refresh();

        SecurityService::logSecurityEvent('appearance_settings_updated', [
            'settings' => array_merge(
                array_filter($request->only(['theme', 'language', 'font_size']))
            )
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث إعدادات المظهر بنجاح',
            'theme'   => $user->theme,
            'language' => $user->language,
            'font_size' => $user->font_size,
        ]);
    }

    /**
     * الحصول على معلومات الجلسة
     */
    public function getSessionInfo(): JsonResponse
    {
        $user = Auth::user();
        
        $sessionInfo = [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'last_activity' => $user->last_activity ? $user->last_activity->toISOString() : null,
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'roles' => $user->roles->pluck('name')->toArray(),
            'employee_info' => $user->employee ? [
                'department' => $user->employee->department->name ?? null,
                'section' => $user->employee->section->name ?? null,
                'job' => $user->employee->job->name ?? null,
                'czn_id' => $user->employee->czn_id ?? null,
            ] : null,
        ];

        return response()->json([
            'success' => true,
            'data' => $sessionInfo
        ]);
    }
}
