<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\RequestWorkflow;
use App\Models\Section;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AssignRequestMailOfficerRole extends Command
{
    protected $signature = 'roles:assign-request-mail-officer {--workflow-id= : ID المسار المحدد} {--department-id= : ID القسم} {--section-id= : ID القسم الفرعي} {--all-workflows : فحص جميع المسارات وإعطاء الدور لكل موظف} {--dry-run : عرض ما سيحدث دون تنفيذ}';

    protected $description = 'تعيين دور موظف متابعة بريد الطلبات (request_mail_officer) لكل موظف في مسار محدد أو جميع المسارات';

    public function handle(): int
    {
        $workflowId = $this->option('workflow-id');
        $departmentId = $this->option('department-id');
        $sectionId = $this->option('section-id');
        $allWorkflows = $this->option('all-workflows');
        $dryRun = $this->option('dry-run');

        $this->info('========== تعيين دور موظف متابعة بريد الطلبات ==========');
        $this->newLine();

        // التحقق من وجود الدور
        $role = Role::where('name', 'request_mail_officer')->where('guard_name', 'web')->first();
        if (!$role) {
            $this->error('الدور request_mail_officer غير موجود. يرجى تشغيل سكربت الإعداد أولاً.');
            $this->line('شغّل: php scripts/setup_request_mail_officer_role.php');
            return 1;
        }

        $this->info("الدور: {$role->name}");
        $this->newLine();

        // جمع الأقسام والدوائر المستهدفة
        $targetDepartmentIds = collect();
        $targetSectionIds = collect();

        if ($allWorkflows) {
            $this->info('فحص جميع المسارات (Workflows)...');
            $workflows = RequestWorkflow::with(['departments', 'section', 'department', 'requestType'])->get();

            $this->info("تم العثور على {$workflows->count()} مسار/مسارات");
            $this->newLine();

            foreach ($workflows as $workflow) {
                $this->line("المسار: {$workflow->name} (ID: {$workflow->id}) - نوع الطلب: {$workflow->requestType->name}");

                // جمع الدوائر من المسار
                foreach ($workflow->departments as $department) {
                    if (!$targetDepartmentIds->contains($department->id)) {
                        $targetDepartmentIds->push($department->id);
                        $this->line("  + دائرة: {$department->name} (ID: {$department->id})");
                    }
                }

                // جمع القسم الفرعي إذا موجود
                if ($workflow->section_id && !$targetSectionIds->contains($workflow->section_id)) {
                    $targetSectionIds->push($workflow->section_id);
                    $this->line("  + قسم فرعي: {$workflow->section->name} (ID: {$workflow->section_id})");
                }

                // إذا كان المسار مرتبط بدائرة محددة مباشرة
                if ($workflow->department_id && !$targetDepartmentIds->contains($workflow->department_id)) {
                    $targetDepartmentIds->push($workflow->department_id);
                    $dept = Department::find($workflow->department_id);
                    $this->line("  + دائرة مباشرة: {$dept?->name} (ID: {$workflow->department_id})");
                }
            }
            $this->newLine();
        } elseif ($workflowId) {
            $this->info("البحث في المسار (Workflow ID: {$workflowId})...");
            $workflow = RequestWorkflow::with(['departments', 'section', 'department'])->find($workflowId);

            if (!$workflow) {
                $this->error("المسار رقم {$workflowId} غير موجود.");
                return 1;
            }

            $this->info("المسار: {$workflow->name}");
            $this->info("نوع الطلب: {$workflow->requestType->name}");
            $this->newLine();

            // جمع الدوائر من المسار
            foreach ($workflow->departments as $department) {
                $targetDepartmentIds->push($department->id);
                $this->line("  - دائرة: {$department->name} (ID: {$department->id})");
            }

            // جمع القسم الفرعي إذا موجود
            if ($workflow->section_id) {
                $targetSectionIds->push($workflow->section_id);
                $this->line("  - قسم فرعي: {$workflow->section->name} (ID: {$workflow->section_id})");
            }

            // إذا كان المسار مرتبط بدائرة محددة مباشرة
            if ($workflow->department_id && !$targetDepartmentIds->contains($workflow->department_id)) {
                $targetDepartmentIds->push($workflow->department_id);
                $dept = Department::find($workflow->department_id);
                $this->line("  - دائرة مباشرة: {$dept?->name} (ID: {$workflow->department_id})");
            }
        } elseif ($departmentId) {
            $this->info("البحث في الدائرة (Department ID: {$departmentId})...");
            $department = Department::find($departmentId);

            if (!$department) {
                $this->error("الدائرة رقم {$departmentId} غير موجودة.");
                return 1;
            }

            $this->info("الدائرة: {$department->name}");
            $targetDepartmentIds->push($departmentId);
            $this->newLine();
        } elseif ($sectionId) {
            $this->info("البحث في القسم الفرعي (Section ID: {$sectionId})...");
            $section = Section::find($sectionId);

            if (!$section) {
                $this->error("القسم الفرعي رقم {$sectionId} غير موجود.");
                return 1;
            }

            $this->info("القسم الفرعي: {$section->name}");
            if ($section->department_id) {
                $targetDepartmentIds->push($section->department_id);
                $dept = Department::find($section->department_id);
                $this->line("الدائرة الأم: {$dept?->name} (ID: {$section->department_id})");
            }
            $targetSectionIds->push($sectionId);
            $this->newLine();
        } else {
            $this->error('يجب تحديد أحد الخيارات: --workflow-id أو --department-id أو --section-id أو --all-workflows');
            $this->line('أمثلة:');
            $this->line('  php artisan roles:assign-request-mail-officer --workflow-id=1');
            $this->line('  php artisan roles:assign-request-mail-officer --department-id=5');
            $this->line('  php artisan roles:assign-request-mail-officer --section-id=10');
            $this->line('  php artisan roles:assign-request-mail-officer --all-workflows');
            return 1;
        }

        // جمع جميع الأقسام الفرعية للدائرة المستهدفة
        if ($targetDepartmentIds->isNotEmpty()) {
            $sectionsInDepartments = Section::whereIn('department_id', $targetDepartmentIds)->pluck('id');
            $targetSectionIds = $targetSectionIds->merge($sectionsInDepartments)->unique();
        }

        $this->info('========== البحث عن الموظفين ==========');
        $this->info("الدوائر المستهدفة: {$targetDepartmentIds->count()}");
        $this->info("الأقسام الفرعية المستهدفة: {$targetSectionIds->count()}");
        $this->newLine();

        // البحث عن المستخدمين المرتبطين بموظفين في الدوائر/الأقسام المستهدفة
        $users = User::whereHas('employee', function ($query) use ($targetDepartmentIds, $targetSectionIds) {
            $query->where(function ($q) use ($targetDepartmentIds, $targetSectionIds) {
                if ($targetDepartmentIds->isNotEmpty()) {
                    $q->whereIn('department_id', $targetDepartmentIds);
                }
                if ($targetSectionIds->isNotEmpty()) {
                    $q->orWhereIn('section_id', $targetSectionIds);
                }
            });
        })
        ->with('employee.department', 'employee.section')
        ->get();

        if ($users->isEmpty()) {
            $this->warn('لم يتم العثور على أي موظفين في الدوائر/الأقسام المحددة.');
            return 0;
        }

        $this->info("تم العثور على {$users->count()} موظف/مستخدم:");
        $this->newLine();

        $tableData = [];
        $assignedCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            $employee = $user->employee;
            $departmentName = $employee->department?->name ?? '-';
            $sectionName = $employee->section?->name ?? '-';

            // التحقق مما إذا كان المستخدم لديه الدور بالفعل
            $hasRole = $user->hasRole('request_mail_officer');

            $status = $hasRole ? 'لديه الدور بالفعل' : 'سيتم تعيين الدور';
            if ($dryRun && !$hasRole) {
                $status = '[DRY-RUN] سيتم تعيين الدور';
            }

            $tableData[] = [
                $user->id,
                $user->name,
                $user->email,
                $departmentName,
                $sectionName,
                $status,
            ];

            // تعيين الدور إذا لم يكن لديه بالفعل
            if (!$hasRole && !$dryRun) {
                $user->assignRole($role);
                $assignedCount++;
            } elseif (!$hasRole && $dryRun) {
                $assignedCount++;
            } else {
                $skippedCount++;
            }
        }

        $this->table(
            ['User ID', 'الاسم', 'البريد الإلكتروني', 'الدائرة', 'القسم الفرعي', 'الحالة'],
            $tableData
        );

        $this->newLine();
        $this->info('========== ملخص ==========');
        if ($dryRun) {
            $this->warn("وضع المعاينة (Dry-Run): سيتم تعيين الدور لـ {$assignedCount} موظف");
            $this->line('لتنفيذ التغيير فعلياً، أعد الأمر بدون --dry-run');
        } else {
            $this->info("تم تعيين الدور لـ {$assignedCount} موظف");
            $this->info("تم تخطي {$skippedCount} موظف (لديهم الدور بالفعل)");
        }

        if (!$dryRun && $assignedCount > 0) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $this->newLine();
            $this->info('تم تفريغ كاش الصلاحيات.');
        }

        $this->newLine();
        $this->info('========== الخطوات التالية ==========');
        $this->line('1) الموظفون الآن لديهم دور «موظف متابعة بريد الطلبات»');
        $this->line('2) قم بتنظيف الكاش على السيرفر:');
        $this->line('   php artisan cache:clear');
        $this->line('   php artisan config:cache');
        $this->line('   php artisan view:clear');
        $this->line('3) الموظفون يستطيعون الوصول إلى بريد الطلبات والرد على الطلبات');
        $this->line('   (بغض النظر عن القسم الذي هم فيه)');

        return 0;
    }
}
