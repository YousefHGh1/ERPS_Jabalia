<?php

/**
 * Sidebar Permission Groups Configuration
 *
 * This file contains all permission groups used in the sidebar menu.
 * Permissions follow CRUD pattern: index, store, update, delete
 *
 * Usage:
 *   use Illuminate\Support\Facades\Auth;
 *   $user = Auth::user();
 *   $permissions = config('sidebar_permissions.groups');
 *   $user->hasAllPermissions($permissions['structural']);
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Permission Groups
    |--------------------------------------------------------------------------
    |
    | Each group contains an array of permissions required to access
    | the corresponding menu section. These can be combined using
    | array_merge() to create composite permission sets.
    |
    */

    'groups' => [
        /**
         * Core Entity Permissions
         * Standard CRUD operations for basic entities
         */
        'categories' => [
            'index categories',
            'store categories',
            'update categories',
            'delete categories',
        ],

        'bonus' => [
            'index bonus',
            'store bonus',
            'update bonus',
            'delete bonus',
        ],

        'jobs' => [
            'index jobs',
            'store jobs',
            'update jobs',
            'delete jobs',
        ],

        /**
         * Structural Organization
         * Departments, Sections, and Branches hierarchy
         */
        'structural' => [
            'index departments',
            'store departments',
            'update departments',
            'delete departments',
            'index sections',
            'store sections',
            'update sections',
            'delete sections',
            'index branches',
            'store branches',
            'update branches',
            'delete branches',
        ],

        // Individual structural entity permissions
        'departments' => [
            'index departments',
            'store departments',
            'update departments',
            'delete departments',
        ],

        'sections' => [
            'index sections',
            'store sections',
            'update sections',
            'delete sections',
        ],

        'branches' => [
            'index branches',
            'store branches',
            'update branches',
            'delete branches',
        ],

        /**
         * Employee Management
         * Employees, Directors, and related entities
         */
        'employees_menu' => [
            'index employees',
            'store employees',
            'update employees',
            'delete employees',
            'index departmentdirector',
            'store departmentdirector',
            'update departmentdirector',
            'delete departmentdirector',
            'index sectiondirector',
            'store sectiondirector',
            'update sectiondirector',
            'delete sectiondirector',
            'index branchdirector',
            'store branchdirector',
            'update branchdirector',
            'delete branchdirector',
        ],

        'employees' => [
            'index employees',
            'store employees',
            'update employees',
            'delete employees',
        ],

        /**
         * Holidays & Leave Management
         * Holiday types, main holidays, and employee leave requests
         */
        'holidays' => [
            'index holidays',
            'store holidays',
            'update holidays',
            'delete holidays',
        ],

        'main_holiday' => [
            'index main_holiday',
            'store main_holiday',
            'update main_holiday',
            'delete main_holiday',
        ],

        'employee_holiday' => [
            'index employee_holiday',
            'store employee_holiday',
            'update employee_holiday',
            'delete employee_holiday',
        ],

        'employee_permissions' => [
            'index EmployeePermissions',
            'store EmployeePermissions',
            'update EmployeePermissions',
            'delete EmployeePermissions',
        ],

        /**
         * Attendance & Shifts
         * ZKTeco integration and shift management
         */
        'shift' => [
            'index Shift',
            'store Shift',
            'update Shift',
            'delete Shift',
        ],

        'zk' => [
            'index zk',
            'store zk',
            'update zk',
            'delete zk',
        ],

        /**
         * Vehicle Management
         */
        'vehicle' => [
            'index vehicle',
            'store vehicle',
            'update vehicle',
            'delete vehicle',
        ],

        /**
         * Settings & Access Control
         * Permissions, Roles, and Users management
         */
        'settings' => [
            'index permissions',
            'update permissions',
            'delete permissions',
            'index roles',
            'update roles',
            'delete roles',
            'index users',
            'update users',
            'delete users',
        ],

        /**
         * Central Treasury
         * Revenue batch and payment management
         */
        'central_treasury' => [
            'view central_treasury',
        ],

        /**
         * External Collections
         * Third-party collection records
         */
        'external_collections' => [
            'view external_collections',
            'create external_collections',
            'edit external_collections',
            'delete external_collections',
            'payment external_collections',
        ],

        /**
         * Public Relations (قلم الجمهور)
         * Mokalafs, Requests, and Citizen Services
         */
        'mokalafs' => [
            'index mokalafs',
            'store mokalafs',
            'update mokalafs',
            'delete mokalafs',
        ],

        'request_types' => [
            'index request_types',
            'store request_types',
            'update request_types',
            'delete request_types',
        ],

        'requests' => [
            'index requests',
            'store requests',
            'update requests',
            'delete requests',
            'show requests',
        ],
        /**
         * Request Mail Officer (موظف متابعة بريد الطلبات)
         * رد ومتابعة بريد الطلبات فقط: عرض الطلبات + عرض/تسجيل/تعديل الردود
         * + استعلام مرجعي للمكلفين وأنواع الطلبات (بدون إدارة).
         * ملاحظة: الرد الفعلي يعتمد على ربط الموظف بقسم/دائرة
         * (انظر StoreRequestMailRequest::authorize و RequestMailController).
         */
        'request_mail_officer' => [
            'index requests',
            'show requests',
            'show request_mails',
            'store request_mails',
            'update request_mails',
            'index mokalafs',
            'index request_types',
        ],

        /**
         * Craft Licenses
         * Trade and craft licensing
         */
        'craft_licenses' => [
            'index craft_licenses',
            'store craft_licenses',
            'update craft_licenses',
            'delete craft_licenses',
        ],

        /**
         * Engineering
         * Building permits and engineering files
         */
        'engineerings' => [
            'index engineerings',
            'store engineerings',
            'update engineerings',
            'delete engineerings',
        ],

        /**
         * Legal Affairs
         * Notices, Imprisonment Orders, Reservations
         */
        'reservations' => [
            'index reservations',
            'store reservations',
            'update reservations',
            'delete reservations',
        ],

        'notices' => [
            'index notices',
            'store notices',
            'update notices',
            'delete notices',
        ],

        'imprisonment_orders' => [
            'index imprisonment_orders',
            'store imprisonment_orders',
            'update imprisonment_orders',
            'delete imprisonment_orders',
        ],

        'real_time_dashboard' => [
            'index real_time_dashboards',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Composite Permission Sets
    |--------------------------------------------------------------------------
    |
    | Pre-defined permission combinations for common access patterns.
    | These are used when a menu requires access to multiple resource types.
    |
    */

    'composites' => [
        'administrative_affairs' => [
            'categories',
            'bonus',
            'jobs',
            'structural',
            'employees_menu',
            'holidays',
            'employee_permissions',
            'shift',
            'zk',
            'vehicle',
        ],

        'employee_management' => [
            'employees_menu',
            'holidays',
            'employee_holiday',
            'employee_permissions',
        ],

        'public_relations' => [
            'mokalafs',
            'request_types',
            'requests',
        ],

        'legal_affairs_employee' => [
            'reservations',
            'notices',
            'imprisonment_orders',
        ],

        'crafts' => [
            'craft_licenses',
            'mokalafs',
        ],

        'engineering' => [
            'engineerings',
            'mokalafs',
        ],

        'legal_affairs' => [
            'reservations',
            'notices',
            'imprisonment_orders',
        ],
    ],
];
