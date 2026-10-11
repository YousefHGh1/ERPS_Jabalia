<?php

namespace App\Console\Commands;

use App\Models\RequestResponse;
use App\Models\Section;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class RequestMailDebugCommand extends Command
{
    protected $signature = 'request-mail:debug {--user=1 : معرّف المستخدم} {--mail=4 : نوع التبويب 0=الكل 1=مقبول 2=معلق 3=مرفوض 4=قيد الانتظار}';

    protected $description = 'تشخيص سبب عدم ظهور الطلبات في بريد الطلبات';

    public function handle(): int
    {
        $userId = (int) $this->option('user');
        $mail = (int) $this->option('mail');

        $this->info('========== تشخيص بريد الطلبات ==========');
        $this->newLine();

        // 1. المستخدم
        $user = User::find($userId);
        if (!$user) {
            $this->error("المستخدم رقم $userId غير موجود.");
            return 1;
        }
        $this->info("1. المستخدم: ID={$user->id} | {$user->name} | {$user->email}");
        Auth::loginUsingId($user->id);
        $this->line('   تم تسجيل الدخول كمستخدم هذا الحساب.');
        $this->newLine();

        // 2. الصلاحية
        $canAccess = $this->canAccessRequestMail($user);
        $this->info('2. صلاحية الدخول إلى بريد الطلبات: ' . ($canAccess ? 'نعم ✓' : 'لا ✗'));
        if (!$canAccess) {
            $this->warn('   السبب: لا يملك المستخدم دور admin أو صلاحية البريد أو قسم (employee.section_id).');
        }
        $this->newLine();

        // 3. الأقسام المسموحة
        $allowedSectionIds = $this->getAllowedSectionIds($user);
        $this->info('3. الأقسام المسموح عرضها (allowedSectionIds):');
        if ($allowedSectionIds->isEmpty()) {
            $this->error('   فارغة! لذلك لا يظهر أي طلب.');
            $this->line('   الحل: التأكد من جدول sections أو من أن المستخدم admin/لديه صلاحية البريد.');
        } else {
            $this->line('   ' . $allowedSectionIds->implode(', '));
        }
        $this->newLine();

        // 4. إحصائيات request_responses
        $totalInDb = RequestResponse::count();
        $totalNotDeleted = RequestResponse::count(); // بدون soft delete
        $bySection = RequestResponse::whereIn('section_id', $allowedSectionIds->toArray())->count();
        $bySectionAndType = RequestResponse::whereIn('section_id', $allowedSectionIds->toArray())->where('response_type', $mail)->count();
        $withRequest = RequestResponse::whereIn('section_id', $allowedSectionIds->toArray())->where('response_type', $mail)->whereHas('request')->count();

        $this->info('4. إحصائيات جدول request_responses:');
        $this->table(
            ['البيان', 'العدد'],
            [
                ['إجمالي السجلات في الجدول', $totalInDb],
                ['سجلات في الأقسام المسموحة', $bySection],
                ["سجلات بقسم مسموح + response_type=$mail (تبويب الحالي)", $bySectionAndType],
                ['نفس الشرط + وجود طلب مرتبط (whereHas request)', $withRequest],
            ]
        );
        $this->newLine();

        // 5. عينة من الردود والطلب المرتبط
        $sample = RequestResponse::with(['request.requestType', 'request.mokalaf', 'section', 'responseType'])
            ->whereIn('section_id', $allowedSectionIds->toArray())
            ->where('response_type', $mail)
            ->whereHas('request')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $this->info('5. عينة (أحدث 5) من الردود المطابقة للاستعلام:');
        if ($sample->isEmpty()) {
            $this->warn('   لا توجد سجلات تطابق الاستعلام.');
        } else {
            $rows = [];
            foreach ($sample as $i => $response) {
                $req = $response->request;
                $rows[] = [
                    $i + 1,
                    $response->id,
                    $response->request_id,
                    $req ? $req->id : 'NULL',
                    $req ? ($req->requestType->name ?? '-') : '-',
                    $response->section->name ?? '-',
                ];
            }
            $this->table(['#', 'response_id', 'request_id', 'request->id', 'نوع الطلب', 'القسم'], $rows);
        }
        $this->newLine();

        // 6. استعلام بدون whereHas
        $withoutWhereHas = RequestResponse::with(['request'])
            ->whereIn('section_id', $allowedSectionIds->toArray())
            ->where('response_type', $mail)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $nullRequestCount = $withoutWhereHas->filter(fn ($r) => $r->request === null)->count();
        $this->info('6. فحص علاقة الطلب (بدون whereHas):');
        $this->line("   من أصل 5 سجلات، عدد الردود التي request = null: $nullRequestCount");
        if ($nullRequestCount > 0) {
            $this->warn('   بعض الردود لا يرتبط بها طلب (ربما الطلب محذوف soft delete).');
        }
        $this->newLine();

        // 7. خلاصة
        $this->info('========== الخلاصة ==========');
        if (!$canAccess) {
            $this->error('المشكلة: المستخدم لا يملك صلاحية الدخول إلى بريد الطلبات.');
            return 0;
        }
        if ($allowedSectionIds->isEmpty()) {
            $this->error('المشكلة: قائمة الأقسام المسموحة فارغة.');
            return 0;
        }
        if ($withRequest === 0) {
            $this->error('المشكلة: لا توجد سجلات request_responses تطابق (قسم مسموح + نوع التبويب + طلب موجود).');
            $this->line('   تحقق: أن مسارات نوع الطلب مرتبطة بأقسام، وأن الطلبات غير محذوفة (deleted_at = null في جدول requests).');
            return 0;
        }
        $this->info("عدد الصفوف المتوقعة في الجدول لهذا المستخدم والتبويب: $withRequest");
        $this->line('إذا الجدول لا يزال فارغاً في المتصفح، تحقق من:');
        $this->line('  - تبويب Network: هل طلب /requests-mail/data يعيد 200 و JSON فيه مفتاح data؟');
        $this->line('  - وجود أخطاء JavaScript في Console.');
        return 0;
    }

    private function canAccessRequestMail(User $user): bool
    {
        try {
            if ($user->hasRole('admin')) {
                return true;
            }
        } catch (\Throwable $e) {
        }
        if ($user->employee && ($user->employee->section_id !== null || $user->employee->department_id !== null)) {
            return true;
        }
        try {
            return $user->hasPermissionTo('mail.view')
                || $user->hasPermissionTo('mail.create')
                || $user->hasPermissionTo('mail.update')
                || $user->hasPermissionTo('mail.delete');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function getAllowedSectionIds(User $user): \Illuminate\Support\Collection
    {
        $fromSections = Section::pluck('id');
        try {
            if ($user->hasRole('admin')) {
                return $fromSections->isNotEmpty() ? $fromSections : RequestResponse::distinct()->pluck('section_id')->filter()->values();
            }
        } catch (\Throwable $e) {
        }
        try {
            if ($user->hasPermissionTo('mail.view') || $user->hasPermissionTo('mail.update') || $user->hasPermissionTo('mail.delete')) {
                return $fromSections->isNotEmpty() ? $fromSections : RequestResponse::distinct()->pluck('section_id')->filter()->values();
            }
        } catch (\Throwable $e) {
        }
        $employee = $user->employee;
        if ($employee && $employee->section_id !== null) {
            return collect([$employee->section_id]);
        }
        if ($employee && $employee->department_id !== null) {
            $ids = Section::where('department_id', $employee->department_id)->pluck('id');
            if ($ids->isNotEmpty()) {
                return $ids;
            }
        }
        return $fromSections->isNotEmpty() ? $fromSections : RequestResponse::distinct()->pluck('section_id')->filter()->values();
    }
}
