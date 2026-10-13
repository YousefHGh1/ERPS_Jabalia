<?php

/**
 * Module Themes Configuration
 *
 * هوية لونية لكل قسم في النظام. تُستخدم في:
 * - تلوين ترويسة الصفحة (مكوّن x-page-header) تلقائياً
 * - تلوين أيقونات السايدر بار لكل قسم (التمييز البصري بين الأقسام)
 * - لمسات لونية على الكروت والجداول ضمن صفحات القسم
 *
 * ملاحظات:
 * - الترتيب مهم: الأقسام الأكثر تخصيصاً أولاً (مثال: mokalafs.for-engineering
 *   يجب أن يُطابَق قبل mokalafs.* الخاص بقلم الجمهور).
 * - 'routes' تُطابَق مع اسم المسار (Route::currentRouteName) و 'paths' مع مسار URL.
 * - 'rgb' مطلوب لبناء درجات الشفافية (soft/strong) في CSS.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | القسم الافتراضي (عند عدم مطابقة أي نمط)
    |--------------------------------------------------------------------------
    */
    'default' => 'dashboard',

    /*
    |--------------------------------------------------------------------------
    | الأقسام وألوانها
    |--------------------------------------------------------------------------
    */
    'modules' => [

        // --- الهندسة (Engineering) ---
        'engineering' => [
            'label'  => 'الهندسة',
            'icon'   => 'fa-compass-drafting',
            'color'  => '#0BB783',
            'rgb'    => '11, 183, 131',
            'routes' => [
                'engineering.*', 'Engineering.*', 'legacy-engineering.*',
                'mokalafs.for-engineering', 'receipts.payments.engineering',
                'admin.engineering',
            ],
            'paths'  => ['engineering*', 'legacy-engineering*', 'admin/engineering*'],
        ],

        // --- الحرف (Crafts) ---
        'crafts' => [
            'label'  => 'الحرف',
            'icon'   => 'fa-helmet-safety',
            'color'  => '#FF6B00',
            'rgb'    => '255, 107, 0',
            'routes' => [
                'craft_licenses.*', 'CraftLicenses.*', 'historical_crafts.*',
                'historical-files.*', 'mokalafs.for-crafts', 'receipts.payments.craft',
                'admin.crafts',
            ],
            'paths'  => ['craft_licenses*', 'craft-licenses*', 'historical_crafts*', 'historical-files*', 'admin/crafts*'],
        ],

        // --- التحصيل الخارجي (External Collections) ---
        'external_collections' => [
            'label'  => 'التحصيل الخارجي',
            'icon'   => 'fa-hand-holding-dollar',
            'color'  => '#FFA800',
            'rgb'    => '255, 168, 0',
            'routes' => ['external-collections.*', 'receipts.external-collection'],
            'paths'  => ['external-collections*'],
        ],

        // --- الصندوق المركزي (Central Treasury) ---
        'central_treasury' => [
            'label'  => 'الصندوق المركزي',
            'icon'   => 'fa-cash-register',
            'color'  => '#1BC5BD',
            'rgb'    => '27, 197, 189',
            'routes' => ['central_treasury.*', 'citizen.*'],
            'paths'  => ['central-treasury*', 'citizens*'],
        ],

        // --- الشؤون القانونية (Legal Affairs) ---
        'legal' => [
            'label'  => 'الشؤون القانونية',
            'icon'   => 'fa-gavel',
            'color'  => '#F64E60',
            'rgb'    => '246, 78, 96',
            'routes' => ['notices.*', 'imprisonment_orders.*', 'reservations.*'],
            'paths'  => ['notices*', 'imprisonment_orders*', 'imprisonment-orders*', 'reservations*'],
        ],

        // --- الشؤون الإدارية (Administrative Affairs) ---
        'administrative' => [
            'label'  => 'الشؤون الإدارية',
            'icon'   => 'fa-briefcase',
            'color'  => '#F64E60',
            'rgb'    => '78, 115, 223',
            'routes' => [
                'categories.*', 'bonus.*', 'bonus_employee.*', 'jobs.*',
                'departments.*', 'sections.*', 'branches.*', 'structure.*',
                'employees.*', 'departmentdirector.*', 'sectiondirector.*', 'branchdirector.*',
                'holidays.*', 'main_holiday.*', 'EmployeeHolidays.*', 'employee_holidays.*',
                'employee-permissions.*', 'EmployeePermissions.*', 'employee_advance.*',
                'employee-advances.*',
                'Shift.*', 'shift.*', 'zk*', 'attendance.*', 'vehicle.*',
                'admin.administrative', 'organization.*',
                'employee.portal', 'employee.calendar', 'employee.requests',
                'employee.holidays.*', 'employee.permissions.*', 'employee.attendance.*',
                'settings.*', 'permission_ui.*', 'roles.*', 'users.*', 'permissions.*',
            ],
            'paths'  => [
                'categories*', 'bonus*', 'jobs*', 'departments*', 'sections*', 'branches*',
                'structure*', 'employees*', 'departmentdirector*', 'sectiondirector*',
                'branchdirector*', 'holidays*', 'main_holiday*', 'employee-holidays*',
                'employee-permissions*', 'employee-advance*', 'employee-advances*',
                'shift*', 'Shift*', 'zk*',
                'attendance*', 'vehicle*', 'admin/administrative*',
                'employee-portal*',
                'settings*', 'permissions*', 'roles*', 'users*',
            ],
        ],

        // --- قلم الجمهور (Public Pen) ---
        'public_pen' => [
            'label'  => 'قلم الجمهور',
            'icon'   => 'fa-bullhorn',
            'color'  => '#8950FC',
            'rgb'    => '137, 80, 252',
            'routes' => [
                'mokalafs.*', 'requests.*', 'request-types.*', 'request-workflows.*',
                'documents.*', 'collection.inquiry*', 'receipts.payments.request',
                'admin.public_office',
            ],
            'paths'  => [
                'mokalafs*', 'requests*', 'request-types*', 'request-workflows*',
                'documents*', 'public-pen*', 'admin/public-office*',
            ],
        ],

        // --- لوحة التحكم والعام (Dashboard / Global) ---
        'dashboard' => [
            'label'  => 'لوحة التحكم',
            'icon'   => 'fa-gauge-high',
            'color'  => '#3699FF',
            'rgb'    => '54, 153, 255',
            'routes' => [],
            'paths'  => [],
        ],
    ],
];