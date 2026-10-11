# دليل نظام الصلاحيات والأدوار والمستخدمين

## 📋 نظرة عامة على النظام

النظام يستخدم حزمة **Spatie Laravel Permission** لإدارة الصلاحيات والأدوار بشكل احترافي مع واجهة إدارة شاملة.

### المكونات الرئيسية

#### 1. لوحة التحكم (Dashboard)
- **الموقع:** `app/Http/Controllers/Settings/DashboardController.php`
- **العرض:** `resources/views/settings/dashboard.blade.php`
- **المسار:** `/permissions/dashboard`
- **الوظيفة:** عرض إحصائيات شاملة وروابط سريعة

#### 2. الصلاحيات (Permissions)
- **الموقع:** `app/Http/Controllers/Settings/PermissionController.php`
- **العرض:** `resources/views/settings/permissions/`
- **المسارات:** `/permissions/permissions/*`
- **الوظيفة:** إدارة جميع صلاحيات النظام

#### 3. الأدوار (Roles)
- **الموقع:** `app/Http/Controllers/Settings/RoleController.php`
- **العرض:** `resources/views/settings/roles/`
- **المسارات:** `/permissions/roles/*`
- **الوظيفة:** إدارة الأدوار وتوزيع الصلاحيات

#### 4. المستخدمين (Users)
- **الموقع:** `app/Http/Controllers/Settings/UserController.php`
- **العرض:** `resources/views/settings/users/`
- **المسارات:** `/permissions/users/*`
- **الوظيفة:** إدارة المستخدمين وتعيين الأدوار

## 🏗️ الهيكل التنظيمي

### التسلسل الهرمي للصلاحيات

```
المستخدم (User)
  └── الدور (Role)
       └── الصلاحيات (Permissions)
```

### مسارات الإدارة

```
/permissions/dashboard           # لوحة التحكم الشاملة
/permissions/permissions         # إدارة الصلاحيات
/permissions/roles              # إدارة الأدوار
/permissions/users               # إدارة المستخدمين
```

## 🎨 المميزات الجديدة

### 1. لوحة تحكم شاملة
- إحصائيات فورية عن النظام
- عرض آخر المستخدمين والأدوار والصلاحيات
- روابط سريعة لجميع صفحات الإدارة
- أزرار للمزامنة السريعة

### 2. فلاتر متقدمة
- **صفحة المستخدمين:** فلترة بالاسم، الدور، الحالة
- **صفحة الأدوار:** فلترة بالاسم، التصنيف (أساسي/أقسام)
- **صفحة الصلاحيات:** فلترة بالاسم، النوع (عرض/إضافة/تعديل/حذف)

### 3. إحصائيات فورية
- إجمالي المستخدمين والأدوار والصلاحيات
- عدد المستخدمين النشطين
- توزيع الصلاحيات حسب النوع
- أعداد المستخدمين لكل دور

### 4. دوال مساعدة جديدة
- `get_role_badge_class()` - تحديد لون البadge حسب نوع الدور
- `get_permission_badge_class()` - تحديد لون البadge حسب نوع الصلاحية
- `is_basic_role()` - التحقق من الأدوار الأساسية
- `is_section_role()` - التحقق من أدوار الأقسام

### 5. نظام التصدير
- تصدير المستخدمين إلى Excel
- تصدير الأدوار إلى Excel
- تصدير الصلاحيات إلى Excel
- تصدير مصفوفة الصلاحيات (الأدوار × الصلاحيات)

### 6. حماية الأدوار الأساسية
- منع حذف الأدوار الأساسية (admin, super-admin, super_user, employee)
- منع حذف الأدوار التي لديها مستخدمين نشطين
- رسائل تحذير واضحة

### 7. نسخ الأدوار
- إمكانية نسخ دور مع جميع صلاحياته
- إنشاء دور جديد بالاسم المنسوخ مع timestamp
- سجل نشاط لعمليات النسخ

### الأدوار الموجودة في النظام

#### الأدوار الأساسية
- **admin** - مدير النظام (لديه جميع الصلاحيات)
- **super-admin** - المدير الأعلى (لديه جميع الصلاحيات)
- **super_user** - مستخدم متقدم
- **employee** - موظف (صلاحيات محدودة)

#### أدوار الأقسام

**الشؤون الإدارية:**
- `administrative_affairs_manager` - مدير الشؤون الإدارية
- `categories_manager` - مدير التصنيفات
- `employees_manager` - مدير الموظفين
- `bonus_manager` - مدير المكافآت
- `jobs_manager` - مدير الوظائف
- `departments_manager` - مدير الأقسام
- `holidays_manager` - مدير العطلات
- `shift_manager` - مدير الورديات
- `vehicle_manager` - مدير المركبات
- `attendance_manager` - مدير الحضور والانصراف

**الصندوق المركزي:**
- `central_treasury_manager` - مدير الصندوق المركزي

**الحرف والصناعات:**
- `crafts_manager` - مدير الحرف والصناعات
- `craft_licenses_manager` - مدير تراخيص الحرف

**الهندسة والتنظيم:**
- `engineering_manager` - مدير الهندسة والتنظيم
- `engineering_files_manager` - مدير الملفات الهندسية

**الشؤون القانونية:**
- `legal_affairs_manager` - مدير الشؤون القانونية
- `notices_manager` - مدير الإخطارات
- `imprisonment_orders_manager` - مدير أوامر الحبس
- `legal_affairs_employee` - موظف الشؤون القانونية

**قلم الجمهور:**
- `republic_pen_manager` - مدير قلم الجمهور
- `public_relations` - قلم الجمهور

**التحصيل الخارجي:**
- `external_collection_manager` - مدير التحصيل الخارجي

## 🔐 قواعد الصلاحيات الخاصة

### أدمن النظام
يتمتع أدمن النظام بجميع الصلاحيات من خلال:

1. **Gate::before** في `AuthServiceProvider`:
```php
Gate::before(function ($user, $ability) {
    if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
        return true;
    }
});
```

2. **منح كل الصلاحيات لدور الأدمن عبر Seeder**:
```php
// database/seeders/AdminPermissionsSyncSeeder.php
$adminRole->syncPermissions(Permission::all());
```

> ملاحظة: لا يوجد حالياً Trait باسم `HasPermissions` في `app/Traits/`. التجاوز يُدار حصرياً عبر `Gate::before` في `AuthServiceProvider` (موثّق في `docs/MODULAR_ARCHITECTURE_GUIDE.md` كجزء من البنية المخططة).

## 📊 إحصائيات النظام

- **إجمالي الصلاحيات:** 456 صلاحية
- **إجمالي الأدوار:** ~40 دور
- **إجمالي المستخدمين:** ~350 مستخدم

## 🎯 الوصول إلى صفحات الإدارة

### إعداد المسارات
تُعرَّف المسارات في `routes/web.php` داخل مجموعة واحدة:

```php
Route::middleware(config('permission_ui.middleware'))->group(function () {
    Route::redirect(config('permission_ui.url_prefix'), config('permission_ui.url_prefix') . '/users');

    Route::group([
        'prefix' => config('permission_ui.url_prefix'),   // permissions
        'as'     => config('permission_ui.route_name_prefix'), // permission_ui.
    ], function () {
        Route::resource('roles',       RoleController::class)->except('show');
        Route::resource('permissions', PermissionController::class)->except('show');
        Route::post('permissions/sync-controllers', [PermissionController::class, 'syncControllerPermissions'])
            ->name('permissions.sync-controllers');
        Route::resource('users',        UserController::class)->only('index', 'edit', 'update', 'destroy');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('users.reset-password');
    });
});
```

### محتوى `config/permission_ui.php`
```php
return [
    'middleware'        => ['web', 'auth'],
    'url_prefix'        => 'permissions',
    'route_name_prefix' => 'permission_ui.',
];
```

> ملاحظة: هذه المسارات محمية بـ `auth` فقط. الحماية الدقيقة تتم عبر صلاحيات داخل الكنترولرات وواجهات العرض.

### المسارات الرئيسية
| المسار | الاسم | الوصف |
|--------|-------|-------|
| `/permissions` | — | تحويل تلقائي إلى `/permissions/users` |
| `/permissions/roles` | `permission_ui.roles.index` | عرض الأدوار |
| `/permissions/roles/create` | `permission_ui.roles.create` | إضافة دور |
| `/permissions/roles/{role}/edit` | `permission_ui.roles.edit` | تعديل دور |
| `/permissions/permissions` | `permission_ui.permissions.index` | عرض الصلاحيات |
| `/permissions/permissions/create` | `permission_ui.permissions.create` | إضافة صلاحية |
| `/permissions/permissions/{permission}/edit` | `permission_ui.permissions.edit` | تعديل صلاحية |
| `/permissions/permissions/sync-controllers` | `permission_ui.permissions.sync-controllers` | مزامنة من الكنترولرات (POST) |
| `/permissions/users` | `permission_ui.users.index` | عرض المستخدمين |
| `/permissions/users/{user}/edit` | `permission_ui.users.edit` | تعديل مستخدم |
| `POST /permissions/users/{user}/reset-password` | `permission_ui.users.reset-password` | إعادة تعيين كلمة المرور |

### المسارات المطلوبة
- `index permissions` - عرض الصلاحيات
- `store permissions` - إضافة صلاحية
- `update permissions` - تعديل صلاحية
- `delete permissions` - حذف صلاحية

- `index roles` - عرض الأدوار
- `store roles` - إضافة دور
- `update roles` - تعديل دور
- `delete roles` - حذف دور

- `index users` - عرض المستخدمين
- `update users` - تعديل مستخدم
- `delete users` - حذف مستخدم

## 🛡️ أنماط الحماية في الكنترولرات

### 1. داخل الـ Constructor (النمط المستخدم فعلياً)
```php
public function __construct()
{
    $this->middleware('permission:index EmployeePermissions', ['only' => ['index']]);
    $this->middleware('permission:store EmployeePermissions', ['only' => ['store']]);
    $this->middleware('permission:update EmployeePermissions', ['only' => ['update']]);
    $this->middleware('permission:delete EmployeePermissions', ['only' => ['destroy']]);
}
```

### 2. على مستوى المجموعة في المسارات
```php
Route::middleware('can:index real_time_dashboards')->group(function () { /* ... */ });

Route::prefix('cache')->middleware(['auth', 'role:admin'])->group(function () { /* ... */ });
```

### 3. داخل الـ Blade
```blade
@can('update users') ... @endcan
@role('admin') ... @endrole
@hasanyrole('admin', 'super-admin') ... @endhasanyrole
```

### 4. في الموديل / الكود
```php
$user->can('update users');
$user->hasRole('admin');
$user->hasAllPermissions(config('sidebar_permissions.groups')['structural']);
$user->givePermissionTo('index requests');
```

## 🔐 البوابات المعرّفة (Gates)

يتم تعريف البوابات في `app/Providers/AuthServiceProvider.php`:

| البوابة | الوصف |
|---------|-------|
| `is_admin` | `admin` أو `super-admin` |
| `is_super_admin` | `super-admin` فقط |
| `sidebar_can_see_requests_menu` | يملك `index requests` أو `index request_types` |
| `sidebar_can_see_request_mail` | يملك `index requests` |

### الـ Policies المربوطة بالنماذج
```php
Request                => RequestPolicy
CraftLicense           => CraftLicensePolicy
Mokalaf                => MokalafPolicy
Employee               => EmployeePolicy
Engineering            => EngineeringPolicy
Notice                 => NoticePolicy
ExternalCollection     => ExternalCollectionPolicy
ExternalCollectionPayment => ExternalCollectionPaymentPolicy
```

### أمثلة الاستخدام في الـ Blade
```blade
@can('index requests')
    <a href="{{ route('requests.index') }}">الطلبات</a>
@endcan

@can('is_admin')
    <a href="{{ url('permissions/users') }}">إدارة المستخدمين</a>
@endcan
```

## 🔧 الأوامر المفيدة

> ⚠️ هذه الأوامر فعّالة ومنفّذة في `app/Console/Commands/`.

| الأمر | الوصف | الخيارات |
|-------|-------|----------|
| `permissions:sync-admin` | مزامنة دور الأدمن مع جميع الصلاحيات (منع التكرار) | — |
| `permissions:sync-controllers` | مسح الكنترولرات وتوليد صلاحيات عربية مطابقة لصلاحيات القائمة الجانبية | `--force` لإعادة الإنشاء |
| `permissions:auto-generate` | **حذف كل الصلاحيات والأدوار** وإنشاؤها تلقائياً من الكنترولرات | — |
| `permissions:translate-check` | طباعة كل الصلاحيات والأدوار مع الترجمة العربية للتأكد | — |

> ⚠️ **تحذير:** `permissions:auto-generate` يحذف البيانات الموجودة. خذ نسخة احتياطية قبل تشغيله.

### أمثلة
```bash
# مزامنة صلاحيات الأدمن
php artisan permissions:sync-admin

# إعادة إنشاء الصلاحيات من الكنترولرات
php artisan permissions:sync-controllers

# إعادة إنشاء قسرية مع تجاوز الموجود
php artisan permissions:sync-controllers --force

# طباعة الصلاحيات مع الترجمة
php artisan permissions:translate-check
```

### مسح cache الصلاحيات
```bash
php artisan cache:forget spatie.permission.cache
```

## 📁 الملفات الرئيسية

### Controllers
- `app/Http/Controllers/Settings/PermissionController.php`
- `app/Http/Controllers/Settings/RoleController.php`
- `app/Http/Controllers/Settings/UserController.php`

### Views
- `resources/views/settings/permissions/index.blade.php`
- `resources/views/settings/permissions/create.blade.php`
- `resources/views/settings/permissions/edit.blade.php`
- `resources/views/settings/roles/index.blade.php`
- `resources/views/settings/roles/create.blade.php`
- `resources/views/settings/roles/edit.blade.php`
- `resources/views/settings/users/index.blade.php`
- `resources/views/settings/users/edit.blade.php`

### Config
- `config/permission.php` - إعدادات الصلاحيات
- `config/permission_ui.php` - إعدادات واجهة الصلاحيات

### Seeders
| الملف | الوصف |
|-------|-------|
| `PermissionSeeder.php` | إنشاء الصلاحيات الأساسية |
| `RoleSeeder.php` | إنشاء الأدوار |
| `RolePermissionsSeeder.php` | ربط الأدوار بالصلاحيات |
| `ControllerPermissionsSeeder.php` | توليد الصلاحيات من الكنترولرات |
| `ControllerRolesSeeder.php` | توليد الأدوار من الكنترولرات |
| `ModelRolesSeeder.php` | ربط الأدوار بنماذج المتطلبات |
| `AdminPermissionsSyncSeeder.php` | منح كل الصلاحيات لدور الأدمن |
| `UserSeeder.php` | بيانات المستخدمين التجريبية |
| `EmployeeSeeder.php` / `EmployeesSeeder.php` | بيانات الموظفين |

### Commands
| الملف | الأمر |
|-------|-------|
| `SyncAdminPermissions.php` | `permissions:sync-admin` |
| `SyncControllerPermissions.php` | `permissions:sync-controllers` |
| `AutoGeneratePermissions.php` | `permissions:auto-generate` |
| `CheckPermissionTranslations.php` | `permissions:translate-check` |

### Providers
- `app/Providers/AuthServiceProvider.php` — تعريف البوابات وربط الـ Policies

## 📐 نمط تسمية الصلاحيات

```
<action> <resource>
```

| الإجراء | المعنى |
|---------|--------|
| `index` | عرض القائمة |
| `create` | عرض نموذج الإنشاء |
| `store` | حفظ جديد |
| `show` | عرض تفاصيل |
| `edit` | عرض نموذج التعديل |
| `update` | حفظ التعديل |
| `delete` | الحذف |

**أمثلة:**
- `index requests` — عرض الطلبات
- `store users` — إضافة مستخدم
- `update roles` — تعديل دور
- `delete EmployeePermissions` — حذف صلاحيات موظف

> التسمية حساسة لحالة الأحرف (case-sensitive). أبقِ الأسماء متطابقة بين الكنترولر والـ Blade والمسار.

## 🔄 سير العمل

### إضافة صلاحية جديدة
1. أضف الـ middleware داخل `__construct()` في الكنترولر.
2. شغّل `php artisan permissions:sync-controllers`
3. امسح الكاش: `php artisan cache:forget spatie.permission.cache`
4. اربط الصلاحية بالدور من صفحة `/permissions/roles`

### إضافة دور جديد
1. أنشئ الدور من `/permissions/roles/create`
2. حدّد الصلاحيات المطلوبة
3. عيّن الدور للمستخدم من `/permissions/users`

### إضافة وحدة (Module) جديدة
1. أنشئ الكنترولر بنفس نمط الصلاحيات أعلاه
2. أضف الصلاحيات إلى `config/sidebar_permissions.php` (اختياري)
3. شغّل `permissions:sync-controllers`
4. أضف عنصر القائمة في `config/sidebar.php` أو `config/sidebar_menu.php`
5. أنشئ الدور واربط الصلاحيات

## 🧪 التحقق من صحة الصلاحيات
```bash
php artisan permissions:translate-check    # عرض كل الصلاحيات والأدوار مترجمة
php artisan permissions:sync-controllers  # إعادة توليد
php artisan permissions:sync-admin         # ضمان اكتمال صلاحيات الأدمن
php artisan tinker
>>> \Spatie\Permission\Models\Permission::count();
>>> \Spatie\Permission\Models\Role::count();
>>> \App\Models\User::role('admin')->first()->getAllPermissions()->count();
```

## 🚀 اقتراحات للتحسين

### 1. إضافة لوحة تحكم شاملة
- إحصائيات فورية عن المستخدمين والأدوار
- رسوم بيانية لتوزيع الصلاحيات
- تنبيهات للأدوار بدون مستخدمين

### 2. تصدير البيانات
- تصدير قائمة المستخدمين إلى Excel
- تصدير تقرير الصلاحيات والأدوار
- تصدير سجل التغييرات

### 3. نظام المراجعة
- سجل لتغييرات الصلاحيات
- من يمكن قام بتعديل ماذا ومتى
- إمكانية التراجع عن التغييرات

### 4. قوالب الأدوار
- إنشاء قوالب جاهزة للأدوار
- نسخ أدوار مع صلاحياتها
- استيراد/تصدير إعدادات الأدوار

## 🔍 الصيانة والدعم

### مشاكل شائعة وحلولها

#### 1. صفحات فارغة بعد مزامنة الصلاحيات
**الحل:** مسح cache الصلاحيات
```bash
php artisan cache:forget spatie.permission.cache
php artisan cache:clear
```

#### 2. خطأ 403 على صفحات الإدارة
**الأسباب المحتملة:**
- المستخدم ليس له الدور المطلوب.
- الصلاحية غير مربوطة بالدور.
- الـ middleware يستخدم اسماً مختلفاً عن الصلاحية المسجّلة.

**الحل:**
```php
// داخل tinker
$user = \App\Models\User::find(1);
$user->getRoleNames();                  // الأدوار
$user->getAllPermissions()->pluck('name'); // كل الصلاحيات
$user->hasPermissionTo('index users');    // فحص مفرد
$user->assignRole('admin');               // إسناد دور
```

```bash
php artisan cache:forget spatie.permission.cache
```

#### 3. الصلاحيات لا تتحدث
**الحل:** إعادة مزامنة صلاحيات الأدمن
```bash
php artisan permissions:sync-admin
```

#### 4. المستخدم لا يرى عناصر القائمة الجانبية
**الحل:** التأكد من أن الصلاحيات مضافة في `config/sidebar_permissions.php` ومربوطة بالدور، ثم مسح الكاش:
```bash
php artisan sidebar:clear-cache   # مسح كاش القائمة الجانبية
php artisan cache:forget spatie.permission.cache
```

#### 5. أذونات جديدة لا تظهر بعد إضافة كنترولر
**الحل:**
```bash
php artisan permissions:sync-controllers
php artisan cache:forget spatie.permission.cache
```

#### 6. بعد استعادة نسخة احتياطية
```bash
php artisan migrate
php artisan cache:clear
php artisan config:clear
php artisan permissions:sync-admin
```

## ✅ قائمة تحقق سريعة

- [ ] المستخدم مربوط بدور صحيح
- [ ] الدور مربوط بالصلاحيات المطلوبة
- [ ] أسماء الصلاحيات متطابقة مع الـ middleware
- [ ] تم مسح كاش الصلاحيات
- [ ] `php artisan permissions:sync-admin` تم تشغيله
- [ ] سجل الأخطاء في `storage/logs/laravel.log` فارغ من أخطاء الصلاحيات

## 📞 الدعم الفني

للدعم والمساعدة، راجع:
- التوثيق الرسمي: https://spatie.be/docs/laravel-permission
- ملفات المساعدة في `docs/`
- سجلات النظام في `storage/logs/`

## 📚 ملفات ذات صلة
- `docs/MODULAR_ARCHITECTURE_GUIDE.md` — بنية الوحدات والـ Traits المشتركة
- `docs/FINAL_IMPLEMENTATION_SUMMARY.md` — ملخص التنفيذ
- `docs/PROJECT_COMPLETION_CERTIFICATE.md` — شهادة اكتمال المشروع
- `config/sidebar.php` / `config/sidebar_menu.php` — عناصر القائمة الجانبية
- `config/sidebar_permissions.php` — مجموعات صلاحيات القائمة الجانبية
