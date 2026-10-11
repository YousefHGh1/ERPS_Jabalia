<?php

/**
 * Module audit configuration: log names and key fields per model.
 * Used by App\Models\Concerns\Auditable when model has no auditAttributes() override.
 */
return [

    /*
    |--------------------------------------------------------------------------
    | الصندوق المركزي
    |--------------------------------------------------------------------------
    */
    'App\\Models\\RevenueBatch' => [
        'log_name' => 'revenue-batches',
        'attributes' => ['date', 'status', 'total_amount', 'payments_count', 'batch_kind', 'section_id'],
    ],
    'App\\Models\\Payment' => [
        'log_name' => 'payments',
        'attributes' => ['craft_license_id', 'amount', 'payment_method', 'payment_date', 'status', 'revenue_batch_id'],
    ],
    'App\\Models\\RequestPayment' => [
        'log_name' => 'request-payments',
        'attributes' => ['request_id', 'amount', 'payment_method', 'payment_date', 'status'],
    ],
    'App\\Models\\ExternalCollection' => [
        'log_name' => 'external-collections',
        'attributes' => ['name', 'identity_number', 'total_amount', 'paid_amount', 'remaining_amount', 'status'],
    ],
    'App\\Models\\ExternalCollectionPayment' => [
        'log_name' => 'external-collection-payments',
        'attributes' => ['external_collection_id', 'amount', 'payment_date', 'status'],
    ],

    /*
    |--------------------------------------------------------------------------
    | قلم الجمهور
    |--------------------------------------------------------------------------
    */
    'App\\Models\\Mokalaf' => [
        'log_name' => 'mokalafs',
        'attributes' => ['name', 'identity_number', 'license_number', 'mobile', 'address'],
    ],
    'App\\Models\\Request' => [
        'log_name' => 'requests',
        'attributes' => ['text', 'request_type_id', 'status_id', 'status_reason', 'mokalaf_id', 'deadline', 'is_urgent'],
    ],
    'App\\Models\\RequestAttachment' => [
        'log_name' => 'request-attachments',
        'attributes' => ['request_id', 'file_name', 'file_path'],
    ],
    'App\\Models\\RequestResponse' => [
        'log_name' => 'request-responses',
        'attributes' => ['request_id', 'response_type_id', 'text', 'status'],
    ],
    'App\\Models\\RequestType' => [
        'log_name' => 'request-types',
        'attributes' => ['name', 'description', 'fee_amount'],
    ],
    'App\\Models\\RequestWorkflow' => [
        'log_name' => 'request-workflows',
        'attributes' => ['request_type_id', 'department_id', 'order', 'is_active'],
    ],

    /*
    |--------------------------------------------------------------------------
    | الشؤون القانونية
    |--------------------------------------------------------------------------
    */
    'App\\Models\\Notice' => [
        'log_name' => 'notices',
        'attributes' => ['notice_number', 'notice_date', 'notice_reason_id', 'rent_location_id', 'status'],
    ],
    'App\\Models\\NoticeAttachment' => [
        'log_name' => 'notice-attachments',
        'attributes' => ['notice_id', 'file_name', 'file_path'],
    ],
    'App\\Models\\ImprisonmentOrder' => [
        'log_name' => 'imprisonment-orders',
        'attributes' => ['order_number', 'order_date', 'court_name', 'defendant_name', 'status'],
    ],
    'App\\Models\\ImprisonmentOrderAttachment' => [
        'log_name' => 'imprisonment-order-attachments',
        'attributes' => ['imprisonment_order_id', 'file_name', 'file_path'],
    ],
    'App\\Models\\Reservation' => [
        'log_name' => 'reservations',
        'attributes' => ['reservation_number', 'reservation_date', 'court_name', 'judge_name', 'case_number', 'status'],
    ],
    'App\\Models\\ReservationAttachment' => [
        'log_name' => 'reservation-attachments',
        'attributes' => ['reservation_id', 'file_name', 'file_path'],
    ],
    'App\\Models\\Document' => [
        'log_name' => 'documents',
        'attributes' => ['document_number', 'document_type', 'title', 'status'],
    ],
    'App\\Models\\Defendant' => [
        'log_name' => 'defendants',
        'attributes' => ['name', 'identity_number', 'reservation_id'],
    ],
    'App\\Models\\Summoner' => [
        'log_name' => 'summoners',
        'attributes' => ['name', 'identity_number', 'reservation_id'],
    ],

    /*
    |--------------------------------------------------------------------------
    | الشؤون الإدارية
    |--------------------------------------------------------------------------
    */
    'App\\Models\\Employee' => [
        'log_name' => 'employees',
        'attributes' => ['name', 'czn_id', 'emp_number', 'department_id', 'section_id', 'branch_id', 'job_id', 'status', 'salary'],
    ],
    'App\\Models\\EmployeeDependent' => [
        'log_name' => 'employee-dependents',
        'attributes' => ['employee_id', 'name', 'relation', 'czn_id'],
    ],
    'App\\Models\\EmployeeBonus' => [
        'log_name' => 'employee-bonuses',
        'attributes' => ['employee_id', 'bonus_id', 'amount', 'date'],
    ],
    'App\\Models\\EmployeeHoliday' => [
        'log_name' => 'employee-holidays',
        'attributes' => ['employee_id', 'holiday_id', 'from_date', 'to_date', 'status'],
    ],
    'App\\Models\\EmployeePermission' => [
        'log_name' => 'employee-permissions',
        'attributes' => ['employee_id', 'from_date', 'to_date', 'reason', 'status'],
    ],
    'App\\Models\\EmployeeJobInfo' => [
        'log_name' => 'employee-job-infos',
        'attributes' => ['employee_id', 'emp_number', 'current_job', 'hire_date', 'status'],
    ],
    'App\\Models\\EmployeeJobHistory' => [
        'log_name' => 'employee-job-histories',
        'attributes' => ['employee_id', 'job_id', 'from_date', 'to_date'],
    ],
    'App\\Models\\Department' => [
        'log_name' => 'departments',
        'attributes' => ['name', 'description'],
    ],
    'App\\Models\\Section' => [
        'log_name' => 'sections',
        'attributes' => ['name', 'department_id'],
    ],
    'App\\Models\\Branch' => [
        'log_name' => 'branches',
        'attributes' => ['name', 'address'],
    ],
    'App\\Models\\Job' => [
        'log_name' => 'jobs',
        'attributes' => ['name', 'category_id'],
    ],
    'App\\Models\\Category' => [
        'log_name' => 'categories',
        'attributes' => ['name', 'description'],
    ],
    'App\\Models\\Bonus' => [
        'log_name' => 'bonuses',
        'attributes' => ['name', 'amount'],
    ],
    'App\\Models\\Shift' => [
        'log_name' => 'shifts',
        'attributes' => ['name', 'start_time', 'end_time'],
    ],
    'App\\Models\\Holiday' => [
        'log_name' => 'holidays',
        'attributes' => ['name', 'days_count'],
    ],
    'App\\Models\\MainHoliday' => [
        'log_name' => 'main-holidays',
        'attributes' => ['name', 'from_date', 'to_date'],
    ],
    'App\\Models\\Attachment' => [
        'log_name' => 'attachments',
        'attributes' => ['employee_id', 'file_name', 'file_path', 'type'],
    ],
    'App\\Models\\Vehicle' => [
        'log_name' => 'vehicles',
        'attributes' => ['number', 'name', 'color', 'images'],
    ],
    'App\\Models\\DepartmentDirector' => [
        'log_name' => 'department-directors',
        'attributes' => ['department_id', 'employee_id', 'from_date', 'to_date'],
    ],
    'App\\Models\\SectionDirector' => [
        'log_name' => 'section-directors',
        'attributes' => ['section_id', 'employee_id', 'from_date', 'to_date'],
    ],
    'App\\Models\\BranchDirector' => [
        'log_name' => 'branch-directors',
        'attributes' => ['branch_id', 'employee_id', 'from_date', 'to_date'],
    ],
];
