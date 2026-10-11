<?php

namespace App\Services;

class ModelAuditLabels
{
    private const LABELS = [
        'App\\Models\\Attachment' => 'مرفق',
        'App\\Models\\Bonus' => 'مكافأة',
        'App\\Models\\Branch' => 'فرع',
        'App\\Models\\BranchDirector' => 'مدير فرع',
        'App\\Models\\Category' => 'فئة',
        'App\\Models\\CategoryDegree' => 'درجة فئة',
        'App\\Models\\CraftFeeHistory' => 'سجل رسوم حرفة',
        'App\\Models\\CraftLicense' => 'رخصة حرفة',
        'App\\Models\\CraftLicenseAttachment' => 'مرفق رخصة حرفة',
        'App\\Models\\CraftLicensePartner' => 'شريك رخصة',
        'App\\Models\\Defendant' => 'متهم',
        'App\\Models\\Department' => 'دائرة',
        'App\\Models\\DepartmentDirector' => 'مدير دائرة',
        'App\\Models\\Document' => 'مستند',
        'App\\Models\\Driver' => 'سائق',
        'App\\Models\\Employee' => 'موظف',
        'App\\Models\\EmployeeBonus' => 'مكافأة موظف',
        'App\\Models\\EmployeeDependent' => 'معال',
        'App\\Models\\EmployeeHoliday' => 'إجازة موظف',
        'App\\Models\\EmployeeJobHistory' => 'سجل وظيفي',
        'App\\Models\\EmployeeJobInfo' => 'بيانات وظيفية',
        'App\\Models\\EmployeeJobInfoShift' => 'وردية موظف',
        'App\\Models\\EmployeeLateHistory' => 'سجل تأخير',
        'App\\Models\\EmployeeLateTime' => 'وقت تأخير',
        'App\\Models\\EmployeeMainHoliday' => 'عطلة رئيسية للموظف',
        'App\\Models\\EmployeeOverTime' => 'ساعات إضافية',
        'App\\Models\\EmployeePermission' => 'إذن موظف',
        'App\\Models\\Engineering' => 'ملف هندسي',
        'App\\Models\\EngineeringAttachment' => 'مرفق هندسي',
        'App\\Models\\EngineeringsFeeHistory' => 'سجل رسوم هندسة',
        'App\\Models\\ExternalCollection' => 'تحصيل خارجي',
        'App\\Models\\ExternalCollectionPayment' => 'دفعة تحصيل خارجي',
        'App\\Models\\Holiday' => 'نوع إجازة',
        'App\\Models\\ImprisonmentOrder' => 'أمر حبس',
        'App\\Models\\ImprisonmentOrderAttachment' => 'مرفق أمر حبس',
        'App\\Models\\Job' => 'وظيفة',
        'App\\Models\\MainHoliday' => 'عطلة رئيسية',
        'App\\Models\\MasterFile' => 'ملف رئيسي',
        'App\\Models\\Mokalaf' => 'مكلف',
        'App\\Models\\Notice' => 'إخطار',
        'App\\Models\\NoticeAttachment' => 'مرفق إخطار',
        'App\\Models\\NoticeReason' => 'سبب إخطار',
        'App\\Models\\Notification' => 'إشعار',
        'App\\Models\\Payment' => 'دفعة',
        'App\\Models\\PaymentEngineering' => 'دفعة هندسية',
        'App\\Models\\RentLocation' => 'موقع إيجار',
        'App\\Models\\Request' => 'طلب',
        'App\\Models\\RequestAttachment' => 'مرفق طلب',
        'App\\Models\\RequestPayment' => 'دفعة طلب',
        'App\\Models\\RequestResponse' => 'رد طلب',
        'App\\Models\\RequestStatus' => 'حالة طلب',
        'App\\Models\\RequestType' => 'نوع طلب',
        'App\\Models\\RequestWorkflow' => 'مسار طلب',
        'App\\Models\\Reservation' => 'حجز',
        'App\\Models\\ReservationAttachment' => 'مرفق حجز',
        'App\\Models\\ResponseType' => 'نوع رد',
        'App\\Models\\RevenueBatch' => 'دفعة إيرادات',
        'App\\Models\\Section' => 'قسم',
        'App\\Models\\SectionDirector' => 'مدير قسم',
        'App\\Models\\Shift' => 'وردية',
        'App\\Models\\Summoner' => 'مدعي',
        'App\\Models\\Traffic' => 'مرور',
        'App\\Models\\User' => 'مستخدم',
        'App\\Models\\Vehicle' => 'مركبة',
        'App\\Models\\WorkflowDepartment' => 'دائرة مسار',
        'App\\Models\\Role' => 'دور',
        'App\\Models\\Permission' => 'صلاحية',
        'Spatie\\Permission\\Models\\Role' => 'دور',
        'Spatie\\Permission\\Models\\Permission' => 'صلاحية',
    ];

    public static function get(string $modelClass): string
    {
        return self::LABELS[$modelClass] ?? class_basename($modelClass);
    }

    public static function all(): array
    {
        return self::LABELS;
    }
}
