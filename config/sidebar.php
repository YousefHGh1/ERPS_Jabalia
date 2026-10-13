<?php

/**
 * Sidebar Configuration
 *
 * This file contains the complete sidebar menu configuration.
 * Permissions are defined inline (backward compatible with existing system).
 *
 * Features:
 * - Caching support for improved performance
 * - Gate and permission-based visibility
 * - Nested submenus support
 */

$perm = [
    'categories'           => ['index categories', 'store categories', 'update categories', 'delete categories'],
    'bonus'                => ['index bonus', 'store bonus', 'update bonus', 'delete bonus'],
    'jobs'                 => ['index jobs', 'store jobs', 'update jobs', 'delete jobs'],
    'structural'           => [
        'index departments', 'store departments', 'update departments', 'delete departments',
        'index sections', 'store sections', 'update sections', 'delete sections',
        'index branches', 'store branches', 'update branches', 'delete branches',
    ],
    'departments'          => ['index departments', 'store departments', 'update departments', 'delete departments'],
    'sections'             => ['index sections', 'store sections', 'update sections', 'delete sections'],
    'branches'             => ['index branches', 'store branches', 'update branches', 'delete branches'],
    'employees_menu'       => [
        'index employees', 'store employees', 'update employees', 'delete employees',
        'index departmentdirector', 'store departmentdirector', 'update departmentdirector', 'delete departmentdirector',
        'index sectiondirector', 'store sectiondirector', 'update sectiondirector', 'delete sectiondirector',
        'index branchdirector', 'store branchdirector', 'update branchdirector', 'delete branchdirector',
    ],
    'holidays'             => ['index holidays', 'store holidays', 'update holidays', 'delete holidays'],
    'main_holiday'         => ['index main_holiday', 'store main_holiday', 'update main_holiday', 'delete main_holiday'],
    'employee_holiday'     => ['index employee_holiday', 'store employee_holiday', 'update employee_holiday', 'delete employee_holiday'],
    'employee_permissions' => ['index EmployeePermissions', 'store EmployeePermissions', 'update EmployeePermissions', 'delete EmployeePermissions'],
    'shift'                => ['index Shift', 'store Shift', 'update Shift', 'delete Shift'],
    'zk'                   => ['index zk', 'store zk', 'update zk', 'delete zk'],
    'vehicle'              => ['index vehicle', 'store vehicle', 'update vehicle', 'delete vehicle'],
    'settings'             => [
        'index permissions', 'update permissions', 'delete permissions',
        'index roles', 'update roles', 'delete roles',
        'index users', 'update users', 'delete users',
    ],
    'central_treasury'     => ['view central_treasury'],
    'external_collections' => ['external-collection-view', 'external-collection-create', 'external-collection-edit', 'external-collection-delete', 'external-collection-payment'],
    'mokalafs'             => ['index mokalafs'],
    'request_types'        => ['index request_types'],
    'requests'             => ['index requests'],
    'craft_licenses'       => ['index craft_licenses'],
    'engineerings'         => ['index engineerings'],
    'reservations'         => ['index reservations', 'store reservations', 'update reservations', 'delete reservations'],
    'notices'              => ['index notices', 'store notices', 'update notices', 'delete notices'],
    'imprisonment_orders'  => ['index imprisonment_orders', 'store imprisonment_orders', 'update imprisonment_orders', 'delete imprisonment_orders'],
];

return [
    /*
    |--------------------------------------------------------------------------
    | Sidebar Configuration
    |--------------------------------------------------------------------------
    */
    'use_cache'   => env('SIDEBAR_CACHE_ENABLED', true),
    'cache_ttl'   => env('SIDEBAR_CACHE_TTL', 1800), // 30 minutes

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | The complete sidebar menu array with permissions inline.
    | This maintains backward compatibility with the existing system.
    |
    */

    'menu'        => [
        // --- Top-level (no permissions = any authenticated user) ---
        [
            'type'   => 'link',
            'route'  => 'home.index',
            'label'  => 'لوحة التحكم الرئيسية',
            'icon'   => 'fas fa-th-large',
            'module' => 'dashboard',
            'accent' => '#3699FF',
            'active' => ['is' => ['/']],
            'badge'  => null,
        ],
        [
            'type'           => 'link',
            'route'          => 'dashboard.realtime',
            'label'          => 'مركز التحكم التفاعلي',
            'icon'           => 'fas fa-chart-line',
            'module'         => 'dashboard',
            'accent'         => '#1BC5BD',
            'active'         => ['is' => ['dashboard/realtime*']],
            'live_indicator' => true,
        ],
        [
            'type'   => 'link',
            'route'  => 'employee.portal',
            'label'  => 'بوابة الموظف',
            'icon'   => 'fas fa-user-tie',
            'module' => 'dashboard',
            'accent' => '#8950FC',
            'active' => ['is' => ['employee-portal*']],
        ],
        // [
        //     'type'   => 'link',
        //     'route'  => 'user-guide',
        //     'label'  => 'دليل المستخدم',
        //     'icon'   => 'fas fa-book text-info',
        //     'active' => ['is' => ['user-guide*']],
        // ],
        [
            'type'   => 'link',
            'route'  => 'notifications.index',
            'label'  => 'Notifications',
            'icon'   => 'fas fa-bell',
            'module' => 'dashboard',
            'accent' => '#FFA800',
            'active' => ['is' => ['notifications*']],
        ],
        [
            'type'   => 'link',
            'url'    => 'https://as.jabalia.ps',
            'label'  => 'المخازن و الأرشيف المركزي',
            'icon'   => 'fas fa-warehouse text-info',
            'target' => '_blank',
                'gate'   => 'is_admin',

        ],

        // --- الشؤون الإدارية (Administrative Affairs) ---
        [
            'type'             => 'submenu',
            'label'            => 'الشؤون الإدارية',
            'icon'             => 'fas fa-briefcase text-danger',
            'module'           => 'administrative',
            'active'           => ['is' => ['admin/administrative*', 'categories*', 'bonus*', 'jobs*', 'departments*', 'sections*', 'branches*', 'employees*', 'departmentdirector*', 'sectiondirector*', 'branchdirector*', 'holidays*', 'main_holiday*', 'EmployeeHolidays*', 'employee-permissions*', 'EmployeePermissions*', 'shift*', 'Shift*', 'zk*', 'attendance*', 'vehicle*', 'employee-advances*', 'performance*', 'user/profile*', 'attachments*', 'jobinfo*', 'dependents*', 'job_history*']],
            'permission_match' => 'any',
            'permissions'      => array_merge($perm['categories'], $perm['bonus'], $perm['jobs'], $perm['structural'], $perm['employees_menu'], $perm['holidays'], $perm['employee_permissions'], $perm['shift'], $perm['zk'], $perm['vehicle']),
            'children'         => [
                [
                    'route'       => 'admin.administrative',
                    'label'       => 'الشؤون الإدارية',
                    'icon'        => 'fas fa-briefcase text-danger',
                    'active'      => ['is' => ['admin/administrative*']],
                    'permissions' => $perm['categories'],
                ],
                [
                    'route'       => 'categories.index',
                    'label'       => 'التصنيفات',
                    'icon'        => 'fas fa-tags text-primary',
                    'active'      => ['is' => ['categories*']],
                    'permissions' => $perm['categories'],
                ],
                [
                    'route'       => 'bonus.index',
                    'label'       => 'المكافآت',
                    'icon'        => 'fas fa-money-bill-wave text-success',
                    'active'      => ['is' => ['bonus*']],
                    'permissions' => $perm['bonus'],
                ],
                [
                    'route'       => 'jobs.index',
                    'label'       => 'المسميات الوظيفية',
                    'icon'        => 'fas fa-id-badge text-warning',
                    'active'      => ['is' => ['jobs*']],
                    'permissions' => $perm['jobs'],
                ],
                [
                    'route'       => 'departments.index',
                    'label'       => 'الهيكل التنظيمي (الأقسام، الشعب، الفروع)',
                    'icon'        => 'fas fa-sitemap text-primary',
                    'active'      => ['is' => ['departments*', 'sections*', 'branches*', 'structure*']],
                    'permissions' => $perm['structural'],
                ],
                [
                    'type'        => 'submenu',
                    'label'       => 'الموظفين',
                    'icon'        => 'fas fa-user-friends text-success',
                    'active'      => ['is' => ['employees*', 'departmentdirector*', 'sectiondirector*', 'branchdirector*', 'employee-advances*', 'performance*', 'user/profile*', 'attachments*', 'jobinfo*', 'dependents*', 'job_history*']],
                    'permissions' => $perm['employees_menu'],
                    'children'    => [
                        [
                            'route'       => 'employees.index',
                            'label'       => 'الموظفين',
                            'icon'        => 'fas fa-users text-success',
                            'active'      => ['is' => ['employees*']],
                            'permissions' => ['index employees', 'store employees', 'update employees', 'delete employees'],
                        ],
                        [
                            'route'       => 'departmentdirector.index',
                            'label'       => 'المدراء',
                            'icon'        => 'fas fa-user-tie text-danger',
                            'active'      => ['is' => ['departmentdirector*']],
                            'permissions' => ['index departmentdirector', 'store departmentdirector', 'update departmentdirector', 'delete departmentdirector'],
                        ],
                        [
                            'route'       => 'employee-advances.index',
                            'label'       => 'سلف الموظفين',
                            'icon'        => 'fas fa-hand-holding-dollar text-success',
                            'active'      => ['is' => ['employee-advances*']],
                            'permissions' => ['index employees'],
                        ],
                        [
                            'route'       => 'performance.dashboard',
                            'label'       => 'مراقبة الأداء',
                            'icon'        => 'fas fa-gauge-high text-info',
                            'active'      => ['is' => ['performance*']],
                            'permissions' => ['index employees'],
                        ],
                        [
                            'route'       => 'employee_reports',
                            'label'       => 'التقارير',
                            'icon'        => 'fas fa-chart-bar text-primary',
                            'active'      => ['is' => ['employee_reports*', 'employee-reports*']],
                            'permissions' => ['index employees'],
                        ],
                        [
                            'route'       => 'user.profile',
                            'label'       => 'الملف الشخصي',
                            'icon'        => 'fas fa-user-circle text-info',
                            'active'      => ['is' => ['user/profile*', 'attachments*', 'jobinfo*', 'dependents*', 'job_history*']],
                            'permissions' => ['index employees'],
                        ],
                    ],
                ],
                [
                    'type'        => 'submenu',
                    'label'       => 'العطلات',
                    'icon'        => 'fas fa-calendar-alt text-info',
                    'active'      => ['is' => ['holidays*', 'main_holiday*', 'EmployeeHolidays*']],
                    'permissions' => $perm['holidays'],
                    'children'    => [
                        [
                            'route'       => 'holidays.index',
                            'label'       => 'أنواع العطلات',
                            'icon'        => 'fas fa-calendar-alt text-primary',
                            'active'      => ['is' => ['holidays*']],
                            'permissions' => $perm['holidays'],
                        ],
                        [
                            'route'       => 'main_holiday.index',
                            'label'       => 'العطلات الرئيسية',
                            'icon'        => 'fas fa-umbrella-beach text-success',
                            'active'      => ['is' => ['main_holiday*']],
                            'permissions' => $perm['main_holiday'],
                        ],
                        [
                            'route'       => 'EmployeeHolidays.index',
                            'label'       => 'إجازات الموظفين',
                            'icon'        => 'fas fa-user-clock text-danger',
                            'active'      => ['is' => ['EmployeeHolidays*']],
                            'permissions' => $perm['employee_holiday'],
                        ],
                    ],
                ],
                [
                    'route'       => 'EmployeePermissions.index',
                    'label'       => 'أذونات الموظفين',
                    'icon'        => 'fas fa-user-lock text-danger',
                    'active'      => ['is' => ['employee-permissions*', 'EmployeePermissions*']],
                    'permissions' => $perm['employee_permissions'],
                ],
                [
                    'route'       => 'Shift.index',
                    'label'       => 'الورديات',
                    'icon'        => 'fas fa-clock text-success',
                    'active'      => ['is' => ['shift*', 'Shift*']],
                    'permissions' => $perm['shift'],
                ],
                [
                    'route'       => 'zk',
                    'label'       => 'الحضور والانصراف',
                    'icon'        => 'fas fa-fingerprint text-primary',
                    'active'      => ['is' => ['zk*', 'attendance*']],
                    'permissions' => $perm['zk'],
                ],
                [
                    'route'       => 'vehicle.index',
                    'label'       => 'المركبات',
                    'icon'        => 'fas fa-car-side text-warning',
                    'active'      => ['is' => ['vehicle*']],
                    'permissions' => $perm['vehicle'],
                ],
                [
                    'type'        => 'submenu',
                    'label'       => 'الإعدادات',
                    'icon'        => 'fas fa-cogs text-info',
                    'active'      => ['is' => ['permissions*']],
                    'permissions' => $perm['settings'],
                    'children'    => [
                        [
                            'route'       => 'permission_ui.permissions.index',
                            'label'       => 'الصلاحيات',
                            'icon'        => 'fas fa-shield-alt text-danger',
                            'active'      => ['is' => ['permissions/permissions*']],
                            'permissions' => ['index permissions'],
                        ],
                        [
                            'route'       => 'permission_ui.roles.index',
                            'label'       => 'الأدوار',
                            'icon'        => 'fas fa-user-shield text-warning',
                            'active'      => ['is' => ['permissions/roles*']],
                            'permissions' => ['index roles'],
                        ],
                        [
                            'route'       => 'permission_ui.users.index',
                            'label'       => 'المستخدمين',
                            'icon'        => 'fas fa-user-tag text-success',
                            'active'      => ['is' => ['permissions/users*']],
                            'permissions' => ['index users'],
                        ],
                    ],
                ],
            ],
        ],

        // --- الصندوق المركزي (Central Treasury) ---
        [
            'type'        => 'submenu',
            'label'       => 'الصندوق المركزي',
            'icon'        => 'fas fa-cash-register text-success',
            'module'      => 'central_treasury',
            'active'      => ['is' => ['central-treasury/today*', 'central-treasury/history*', 'central-treasury/batches*', 'citizens/unified-invoice*']],
            'permissions' => $perm['central_treasury'],
            'children'    => [
                [
                    'route'       => 'central_treasury.today',
                    'label'       => 'الصندوق المركزي للإيرادات',
                    'icon'        => 'fas fa-cash-register text-success',
                    'active'      => ['is' => ['central-treasury/today*']],
                    'permissions' => $perm['central_treasury'],
                ],
                [
                    'route'       => 'central_treasury.history',
                    'label'       => 'سجل الرزم المغلقة',
                    'icon'        => 'fas fa-history text-primary',
                    'active'      => ['is' => ['central-treasury/history*', 'central-treasury/batches*']],
                    'permissions' => $perm['central_treasury'],
                ],
                [
                    'route'       => 'citizen.unified_invoice',
                    'label'       => 'فاتورة موحدة للمواطن',
                    'icon'        => 'fas fa-file-invoice-dollar text-info',
                    'active'      => ['is' => ['citizens/unified-invoice*']],
                    'permissions' => $perm['central_treasury'],
                ],
            ],
        ],

        // --- التحصيل الخارجي (External Collection) ---
        [
            'type'        => 'link',
            'route'       => 'external-collections.index',
            'label'       => 'التحصيل الخارجي',
            'icon'        => 'fas fa-hand-holding-usd text-warning',
            'module'      => 'external_collections',
            'active'      => ['is' => ['external-collections*', 'receipts/external-collection*']],
            'permissions' => ['view external_collections'],
        ],

        // ============================================================
        // PUBLIC RELATIONS (قلم الجمهور)
        // ============================================================
        [
            'type'            => 'submenu',
            'label'           => 'قلم الجمهور',
            'icon'            => 'flaticon2-user text-primary',
            'active'          => ['is' => [
                'admin/public-office*',
                'mokalafs*',
                'requests*',
                'request-types*',
                'request-workflows*',
                'documents*',
                'requests-mail*',
            ]],
            'permissions_key' => 'mokalafs',
            'children'        => [
                // Dashboard
                [
                    'route'           => 'admin.public_office',
                    'label'           => 'داشبور قلم الجمهور',
                    'icon'            => 'fas fa-chart-pie text-danger',
                    'active'          => ['is' => ['admin/public-office*']],
                    'permissions_key' => 'requests',
                ],
                [
                    'route'  => 'mokalafs.index',
                    'label'  => 'المكلفين',
                    'icon'   => 'flaticon2-user text-success',
                    'active' => ['is' => ['mokalafs*']],
                ],

                // Collection inquiry (فواتير الجباية)
                [
                    'type'            => 'link',
                    'route'           => 'collection.inquiry',
                    'label'           => 'استعلام فواتير الجباية',
                    'icon'            => 'fas fa-file-invoice-dollar text-warning',
                    'active'          => ['is' => ['public-pen/collection-inquiry*']],
                    'permissions_key' => 'mokalafs',
                ],

                // Requests Submenu
                [
                    'type'     => 'submenu',
                    'label'    => 'الطلبات',
                    'icon'     => 'fas fa-clipboard-list text-info',
                    'active'   => ['is' => [
                        'requests*',
                        'request-types*',
                        'request-workflows*',
                        'documents*',
                        'requests-mail*',
                    ]],
                    'gate'     => 'sidebar_can_see_requests_menu',
                    'children' => [
                        [
                            'route'           => 'request-types.index',
                            'label'           => 'Request Types',
                            'icon'            => 'flaticon2-list-3 text-primary',
                            'active'          => ['is' => ['request-types*']],
                            'permissions_key' => 'request_types',
                        ],
                        [
                            'route'           => 'requests.index',
                            'label'           => 'Requests',
                            'icon'            => 'flaticon2-list text-danger',
                            'active'          => [
                                'is'        => ['requests*'],
                                'except_is' => ['request-types*', 'request-workflows*', 'requests-mail*'],
                            ],
                            'permissions_key' => 'requests',
                        ],
                        [
                            'route'        => 'requests.mail.all',
                            'label'        => 'Mail',
                            'icon'         => 'flaticon2-mail text-warning',
                            'active'       => ['is' => ['requests-mail/all']],
                            'active_class' => 'menu-item-open menu-item-active',
                            'li_class'     => 'menu-item-submenu',
                            'gate'         => 'sidebar_can_see_request_mail',
                        ],
                    ],
                ],
            ],
        ],

        // --- الحرف (Crafts) ---
        [
            'type'        => 'submenu',
            'label'       => 'الحرف',
            'icon'        => 'fas fa-helmet-safety text-warning',
            'module'      => 'crafts',
            'active'      => ['is' => ['admin/crafts*', 'craft_licenses*', 'craftlicenses*', 'historical-crafts*', 'historical_crafts*', 'mokalafs.for-crafts', 'receipts/payments/craft*']],
            'permissions' => array_merge($perm['craft_licenses'], $perm['mokalafs']),
            'children'    => [
                [
                    'route'       => 'admin.crafts',
                    'label'       => 'الحرف والصناعات',
                    'icon'        => 'fas fa-industry text-danger',
                    'active'      => ['is' => ['admin/crafts*']],
                    'permissions' => $perm['craft_licenses'],
                ],
                [
                    'route'       => 'craft_licenses.index',
                    'label'       => 'تراخيص الحرف',
                    'icon'        => 'fas fa-certificate text-primary',
                    'active'      => ['is' => ['craft_licenses*']],
                    'permissions' => $perm['craft_licenses'],
                ],
                [
                    'route'       => 'mokalafs.for-crafts',
                    'label'       => 'المكلفين — طلب حرفة',
                    'icon'        => 'fas fa-user-check text-success',
                    'active'      => ['route' => ['mokalafs.for-crafts']],
                    'permissions' => $perm['mokalafs'],
                ],
                        [
            'type' => 'link',
            'route' => 'historical-files.index',
            'label' => 'الاستعلام التاريخي',
            'icon' => 'fas fa-history text-info',
            'active' => ['is' => ['historical-files*', 'historical-crafts*', 'historical_crafts*']],
                    'permissions' => $perm['craft_licenses'],

        ],
            ],
        ],

        // --- الهندسة (Engineering) ---
        [
            'type'        => 'submenu',
            'label'       => 'الهندسة',
            'icon'        => 'fas fa-compass-drafting text-success',
            'module'      => 'engineering',
            'active'      => ['is' => ['admin/engineering*', 'engineering*', 'legacy-engineering*', 'mokalafs.for-engineering', 'receipts/payments/engineering*']],
            'permissions' => array_merge($perm['engineerings'], $perm['mokalafs']),
            'children'    => [
                [
                    'route'       => 'admin.engineering',
                    'label'       => 'الهندسة والتنظيم',
                    'icon'        => 'fas fa-ruler-combined text-danger',
                    'active'      => ['is' => ['admin/engineering*']],
                    'permissions' => $perm['engineerings'],
                ],
                [
                    'route'       => 'engineering.index',
                    'label'       => 'الملفات الهندسية',
                    'icon'        => 'fas fa-folder-open text-primary',
                    'active'      => ['is' => ['engineering*'], 'except_is' => ['legacy-engineering*']],
                    'permissions' => $perm['engineerings'],
                ],
                [
                    'route'       => 'mokalafs.for-engineering',
                    'label'       => 'المكلفين — ترخيص بناء',
                    'icon'        => 'fas fa-user-check text-success',
                    'active'      => ['route' => ['mokalafs.for-engineering']],
                    'permissions' => $perm['mokalafs'],
                ],
                [
                    'route'       => 'legacy-engineering.index',
                    'label'       => 'الملفات الهندسية القديمة',
                    'icon'        => 'fas fa-box-archive text-warning',
                    'active'      => ['is' => ['legacy-engineering*']],
                    'permissions' => $perm['engineerings'],
                ],
            ],
        ],

        // --- الشؤون القانونية (Legal Affairs) ---
        [
            'type'        => 'submenu',
            'label'       => 'الشؤون القانونية',
            'icon'        => 'fas fa-gavel text-danger',
            'module'      => 'legal',
            'active'      => ['is' => ['reservations*', 'notices*', 'imprisonment_orders*']],
            'permissions' => array_merge($perm['reservations'], $perm['notices'], $perm['imprisonment_orders']),
            'children'    => [
                [
                    'route'       => 'notices.index',
                    'label'       => 'الإخطارات',
                    'icon'        => 'fas fa-bell text-info',
                    'active'      => ['is' => ['notices*']],
                    'permissions' => $perm['notices'],
                ],
                [
                    'route'       => 'imprisonment_orders.index',
                    'label'       => 'أوامر الحبس',
                    'icon'        => 'fas fa-gavel text-danger',
                    'active'      => ['is' => ['imprisonment_orders*']],
                    'permissions' => $perm['imprisonment_orders'],
                ],
                [
                    'route'       => 'reservations.index',
                    'label'       => 'الحجوزات',
                    'icon'        => 'fas fa-calendar-check text-warning',
                    'active'      => ['is' => ['reservations*']],
                    'permissions' => $perm['reservations'],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Groups (for reference)
    |--------------------------------------------------------------------------
    |
    | Exposes permission arrays for use in views and other parts of the app.
    |
    */

    'permissions' => $perm,
];
