<?php

/**
 * Sidebar Menu Structure Configuration
 *
 * This file contains only the menu structure without permissions.
 * Permissions are now defined in sidebar_permissions.php
 *
 * Leaf items use AND logic by default (user must have ALL listed permissions)
 * Parent menus are shown when ANY child is visible by default.
 */

return [
    'menu' => [
        // ============================================================
        // TOP-LEVEL LINKS (No permissions = any authenticated user)
        // ============================================================
        [
            'type'   => 'link',
            'route'  => 'home.index',
            'label'  => 'لوحة التحكم الرئيسية',
            'icon'   => 'fas fa-th-large text-danger',
            'active' => ['is' => ['/']],
            'badge'  => null,
        ],
        [
            'type'            => 'link',
            'route'           => 'dashboard.realtime',
            'label'           => 'مركز التحكم التفاعلي',
            'icon'            => 'fas fa-chart-line text-success',
            'active'          => ['is' => ['dashboard/realtime*']],
            'live_indicator'  => true,
            'permissions_key' => 'real_time_dashboard',
        ],
        [
            'type'   => 'link',
            'route'  => 'employee.portal',
            'label'  => 'بوابة الموظف',
            'icon'   => 'fas fa-user-tie text-primary',
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
            'icon'   => 'fas fa-bell text-warning',
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
        // ============================================================
        // ADMINISTRATIVE AFFAIRS (الشؤون الإدارية)
        // ============================================================
        [
            'type'                  => 'submenu',
            'label'                 => 'الشؤون الإدارية',
            'icon'                  => 'fas fa-briefcase text-danger',
            'active'                => ['is' => [
                'admin/administrative*',
                'categories*',
                'bonus*',
                'jobs*',
                'departments*',
                'sections*',
                'branches*',
                'employees*',
                'departmentdirector*',
                'sectiondirector*',
                'branchdirector*',
                'holidays*',
                'main_holiday*',
                'EmployeeHolidays*',
                'employee-permissions*',
                'EmployeePermissions*',
                'shift*',
                'Shift*',
                'zk*',
                'attendance*',
                'vehicle*',
                'permissions*',
            ]],
            'permissions_composite' => 'administrative_affairs',
            'children'              => [
                // Dashboard
                [
                    'route'           => 'admin.administrative',
                    'label'           => 'Administrative Affairs',
                    'icon'            => 'fas fa-briefcase text-danger',
                    'active'          => ['is' => ['admin/administrative*']],
                    'permissions_key' => 'categories',
                ],

                // Categories
                [
                    'route'           => 'categories.index',
                    'label'           => 'Categories',
                    'icon'            => 'fas fa-tags text-primary',
                    'active'          => ['is' => ['categories*']],
                    'permissions_key' => 'categories',
                ],

                // Bonus
                [
                    'route'           => 'bonus.index',
                    'label'           => 'Bonus',
                    'icon'            => 'fas fa-money-bill-wave text-success',
                    'active'          => ['is' => ['bonus*']],
                    'permissions_key' => 'bonus',
                ],

                // Jobs
                [
                    'route'           => 'jobs.index',
                    'label'           => 'Jobs',
                    'icon'            => 'fas fa-id-badge text-warning',
                    'active'          => ['is' => ['jobs*']],
                    'permissions_key' => 'jobs',
                ],

                // Structural Organization
                [
                    'route'           => 'departments.index',
                    'label'           => 'Structural (Departments, Sections, Branches)',
                    'icon'            => 'fas fa-sitemap text-primary',
                    'active'          => ['is' => ['departments*', 'sections*', 'branches*', 'structure*']],
                    'permissions_key' => 'structural',
                ],

                // Employees Submenu
                [
                    'type'            => 'submenu',
                    'label'           => 'Employees',
                    'icon'            => 'fas fa-user-friends text-info',
                    'active'          => ['is' => ['employees*', 'departmentdirector*', 'sectiondirector*', 'branchdirector*']],
                    'permissions_key' => 'employees_menu',
                    'children'        => [
                        [
                            'route'  => 'employees.index',
                            'label'  => 'Employees',
                            'icon'   => 'fas fa-users text-success',
                            'active' => ['is' => ['employees*']],
                        ],
                        [
                            'route'  => 'departmentdirector.index',
                            'label'  => 'Director',
                            'icon'   => 'fas fa-user-tie text-danger',
                            'active' => ['is' => ['departmentdirector*']],
                        ],
                        [
                            'route'  => 'employee_reports',
                            'label'  => 'Reports',
                            'icon'   => 'fas fa-chart-bar text-primary',
                            'active' => ['is' => ['employee_reports*', 'employee-reports*']],
                        ],
                    ],
                ],

                // Holidays Submenu
                [
                    'type'            => 'submenu',
                    'label'           => 'Holidays',
                    'icon'            => 'fas fa-calendar-alt text-info',
                    'active'          => ['is' => ['holidays*', 'main_holiday*', 'EmployeeHolidays*']],
                    'permissions_key' => 'holidays',
                    'children'        => [
                        [
                            'route'  => 'holidays.index',
                            'label'  => 'Holiday Types',
                            'icon'   => 'fas fa-calendar-alt text-primary',
                            'active' => ['is' => ['holidays*']],
                        ],
                        [
                            'route'           => 'main_holiday.index',
                            'label'           => 'Main Holidays',
                            'icon'            => 'fas fa-umbrella-beach text-success',
                            'active'          => ['is' => ['main_holiday*']],
                            'permissions_key' => 'main_holiday',
                        ],
                        [
                            'route'           => 'EmployeeHolidays.index',
                            'label'           => 'Employee Holidays',
                            'icon'            => 'fas fa-user-clock text-danger',
                            'active'          => ['is' => ['EmployeeHolidays*']],
                            'permissions_key' => 'employee_holiday',
                        ],
                    ],
                ],

                // Employee Permissions
                [
                    'route'           => 'EmployeePermissions.index',
                    'label'           => 'Employee Permissions',
                    'icon'            => 'fas fa-user-lock text-danger',
                    'active'          => ['is' => ['employee-permissions*', 'EmployeePermissions*']],
                    'permissions_key' => 'employee_permissions',
                ],

                // Shift
                [
                    'route'           => 'Shift.index',
                    'label'           => 'Shift',
                    'icon'            => 'fas fa-clock text-success',
                    'active'          => ['is' => ['shift*', 'Shift*']],
                    'permissions_key' => 'shift',
                ],

                // Attendance (ZKTeco)
                [
                    'route'           => 'zk',
                    'label'           => 'Attendance',
                    'icon'            => 'fas fa-fingerprint text-primary',
                    'active'          => ['is' => ['zk*', 'attendance*']],
                    'permissions_key' => 'zk',
                ],

                // Vehicle
                [
                    'route'           => 'vehicle.index',
                    'label'           => 'Vehicle',
                    'icon'            => 'fas fa-car-side text-warning',
                    'active'          => ['is' => ['vehicle*']],
                    'permissions_key' => 'vehicle',
                ],

                // Settings Submenu
                [
                    'type'            => 'submenu',
                    'label'           => 'Settings',
                    'icon'            => 'fas fa-cogs text-info',
                    'active'          => ['is' => ['permissions*']],
                    'permissions_key' => 'settings',
                    'children'        => [
                        [
                            'route'  => 'permission_ui.permissions.index',
                            'label'  => 'Permissions',
                            'icon'   => 'fas fa-shield-alt text-danger',
                            'active' => ['is' => ['permissions/permissions*']],
                        ],
                        [
                            'route'  => 'permission_ui.roles.index',
                            'label'  => 'Roles',
                            'icon'   => 'fas fa-user-shield text-warning',
                            'active' => ['is' => ['permissions/roles*']],
                        ],
                        [
                            'route'  => 'permission_ui.users.index',
                            'label'  => 'Users',
                            'icon'   => 'fas fa-user-tag text-success',
                            'active' => ['is' => ['permissions/users*']],
                        ],
                    ],
                ],
            ],
        ],

        // ============================================================
        // CENTRAL TREASURY (الصندوق المركزي)
        // ============================================================
        [
            'type'            => 'submenu',
            'label'           => 'الصندوق المركزي',
            'icon'            => 'fas fa-cash-register text-success',
            'active'          => ['is' => [
                'central-treasury/today*',
                'central-treasury/history*',
                'central-treasury/batches*',
                'citizens/unified-invoice*',
            ]],
            'permissions_key' => 'central_treasury',
            'children'        => [
                [
                    'route'  => 'central_treasury.today',
                    'label'  => 'الصندوق المركزي للإيرادات',
                    'icon'   => 'fas fa-cash-register text-success',
                    'active' => ['is' => ['central-treasury/today*']],
                ],
                [
                    'route'  => 'central_treasury.history',
                    'label'  => 'سجل الرزم المغلقة',
                    'icon'   => 'fas fa-history text-primary',
                    'active' => ['is' => ['central-treasury/history*', 'central-treasury/batches*']],
                ],
                [
                    'route'  => 'citizen.unified_invoice',
                    'label'  => 'فاتورة موحدة للمواطن',
                    'icon'   => 'fas fa-file-invoice-dollar text-info',
                    'active' => ['is' => ['citizens/unified-invoice*']],
                ],
            ],
        ],

        // ============================================================
        // EXTERNAL COLLECTION (التحصيل الخارجي)
        // ============================================================
        [
            'type'            => 'link',
            'route'           => 'external-collections.index',
            'label'           => 'التحصيل الخارجي',
            'icon'            => 'fas fa-hand-holding-usd text-warning',
            'active'          => ['is' => ['external-collections*']],
            'permissions_key' => 'external_collections',
        ],

        // ============================================================
        // PUBLIC RELATIONS (قلم الجمهور)
        // ============================================================
        [
            'type'            => 'submenu',
            'label'           => 'قلم الجمهور',
            'icon'            => 'flaticon2-user text-primary',
            'active'          => ['is' => [
                'mokalafs*',
                'requests*',
                'request-types*',
                'request-workflows*',
                'documents*',
                'requests-mail*',
            ]],
            'permissions_key' => 'mokalafs',
            'children'        => [
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

        // ============================================================
        // CRAFTS (الحرف)
        // ============================================================
        [
            'type'                  => 'submenu',
            'label'                 => 'الحرف',
            'icon'                  => 'flaticon-tools text-warning',
            'active'                => ['is' => [
                'admin/crafts*',
                'craft_licenses*',
                'mokalafs.for-crafts',
            ]],
            'permissions_composite' => 'crafts',
            'children'              => [
                [
                    'route'           => 'admin.crafts',
                    'label'           => 'Crafts and Industries',
                    'icon'            => 'flaticon-tools text-danger',
                    'active'          => ['is' => ['admin/crafts*']],
                    'permissions_key' => 'craft_licenses',
                ],
                [
                    'route'           => 'craft_licenses.index',
                    'label'           => 'Craft Licenses',
                    'icon'            => 'flaticon-tools text-primary',
                    'active'          => ['is' => ['craft_licenses*']],
                    'permissions_key' => 'craft_licenses',
                ],
                [
                    'route'           => 'mokalafs.for-crafts',
                    'label'           => 'المكلفين — طلب حرفة',
                    'icon'            => 'flaticon2-user text-success',
                    'active'          => ['route' => ['mokalafs.for-crafts']],
                    'permissions_key' => 'mokalafs',
                ],
                [
                    'type'   => 'link',
                    'route'  => 'historical-files.index',
                    'label'  => 'الاستعلام التاريخي',
                    'icon'   => 'fas fa-history text-info',
                    'active' => ['is' => ['historical-files*']],
                    'permissions_key' => 'craft_licenses',
                ],
            ],
        ],

        // ============================================================
        // ENGINEERING (الهندسة)
        // ============================================================
        [
            'type'                  => 'submenu',
            'label'                 => 'الهندسة',
            'icon'                  => 'flaticon2-architecture text-success',
            'active'                => ['is' => [
                'admin/engineering*',
                'engineering*',
                'legacy-engineering*',
                'mokalafs.for-engineering',
            ]],
            'permissions_composite' => 'engineering',
            'children'              => [
                [
                    'route'           => 'admin.engineering',
                    'label'           => 'Engineering and Organization',
                    'icon'            => 'flaticon2-architecture text-danger',
                    'active'          => ['is' => ['admin/engineering*']],
                    'permissions_key' => 'engineerings',
                ],
                [
                    'route'           => 'engineering.index',
                    'label'           => 'Engineering Files',
                    'icon'            => 'flaticon2-architecture text-primary',
                    'active'          => [
                        'is'        => ['engineering*'],
                        'except_is' => ['legacy-engineering*'],
                    ],
                    'permissions_key' => 'engineerings',
                ],
                [
                    'route'           => 'mokalafs.for-engineering',
                    'label'           => 'المكلفين — ترخيص بناء',
                    'icon'            => 'flaticon2-user text-success',
                    'active'          => ['route' => ['mokalafs.for-engineering']],
                    'permissions_key' => 'mokalafs',
                ],
                [
                    'route'           => 'legacy-engineering.index',
                    'label'           => 'Legacy Engineering Files',
                    'icon'            => 'flaticon2-folder text-warning',
                    'active'          => ['is' => ['legacy-engineering*']],
                    'permissions_key' => 'engineerings',
                ],
            ],
        ],

        // ============================================================
        // LEGAL AFFAIRS (الشؤون القانونية)
        // ============================================================
        [
            'type'                  => 'submenu',
            'label'                 => 'الشؤون القانونية',
            'icon'                  => 'fas fa-gavel text-danger',
            'active'                => ['is' => [
                'reservations*',
                'notices*',
                'imprisonment_orders*',
            ]],
            'permissions_composite' => 'legal_affairs',
            'children'              => [
                [
                    'route'           => 'notices.index',
                    'label'           => 'الإخطارات',
                    'icon'            => 'fas fa-bell text-info',
                    'active'          => ['is' => ['notices*']],
                    'permissions_key' => 'notices',
                ],
                [
                    'route'           => 'imprisonment_orders.index',
                    'label'           => 'أوامر الحبس',
                    'icon'            => 'fas fa-gavel text-danger',
                    'active'          => ['is' => ['imprisonment_orders*']],
                    'permissions_key' => 'imprisonment_orders',
                ],
                [
                    'route'           => 'reservations.index',
                    'label'           => 'الحجوزات',
                    'icon'            => 'fas fa-calendar-check text-warning',
                    'active'          => ['is' => ['reservations*']],
                    'permissions_key' => 'reservations',
                ],
            ],
        ],
    ],
];