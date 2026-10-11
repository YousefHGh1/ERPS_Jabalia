# صلاحيات نظام التحصيل الخارجي

## نظرة عامة

تم إضافة صلاحيات جديدة لنظام التحصيل الخارجي للتحكم في الوصول إلى وظائف النظام المختلفة.

## الصلاحيات المضافة

### 1. `external-collection-view` (ID: 198)
- **الوصف**: عرض قائمة التحصيل الخارجي وتفاصيلها
- **الوظائف المسموح بها**:
  - عرض قائمة التحصيلات
  - عرض تفاصيل التحصيل الفردي
  - عرض سجل المدفوعات
  - طباعة الإيصالات

### 2. `external-collection-create` (ID: 199)
- **الوصف**: إنشاء تحصيل خارجي جديد
- **الوظائف المسموح بها**:
  - إضافة تحصيل خارجي جديد
  - البحث عن المكلفين
  - تعيين القسم والمبلغ

### 3. `external-collection-edit` (ID: 200)
- **الوصف**: تعديل بيانات التحصيل الخارجي
- **الوظائف المسموح بها**:
  - تعديل معلومات التحصيل
  - تحديث القسم والمبلغ
  - تعديل الملاحظات

### 4. `external-collection-delete` (ID: 201)
- **الوصف**: حذف التحصيل الخارجي
- **الوظائف المسموح بها**:
  - حذف سجلات التحصيل
  - الحذف الناعف (Soft Delete)

### 5. `external-collection-payment` (ID: 202)
- **الوصف**: معالجة المدفوعات وإلغاؤها
- **الوظائف المسموح بها**:
  - إضافة دفعات جديدة
  - إلغاء الدفعات
  - طباعة إيصالات الدفع
  - عرض سجل المدفوعات

## توزيع الصلاحيات على الأدوار

### دور `admin` (المسؤول)
- **جميع الصلاحيات**: كامل صلاحيات نظام التحصيل الخارجي
- **الصلاحيات**: `external-collection-view`, `external-collection-create`, `external-collection-edit`, `external-collection-delete`, `external-collection-payment`

### دور `employee` (الموظف)
- **صلاحية محدودة**: العرض فقط
- **الصلاحيات**: `external-collection-view`

### دور `finance_external_collection` (المحاسب المالي)
- **صلاحيات مالية**: العرض والإنشاء والتعديل والدفع
- **الصلاحيات**: `external-collection-view`, `external-collection-create`, `external-collection-edit`, `external-collection-payment`
- **ملاحظة**: لا يملك صلاحية الحذف

## أوامر تشغيل الصلاحيات

### 1. إضافة الصلاحيات إلى قاعدة البيانات
```bash
php artisan db:seed --class=PermissionSeeder --force
```

### 2. تحديث صلاحيات الأدوار
```bash
php artisan db:seed --class=RolePermissionsSeeder --force
php artisan db:seed --class=ExternalCollectionRoleSeeder --force
php artisan db:seed --class=ExternalCollectionFinanceRoleSeeder --force
```

### 3. مسح الكاش
```bash
php artisan cache:clear
php artisan config:clear
php artisan permission:cache-reset
```

## تخصيص الصلاحيات

### إضافة دور جديد
يمكن إنشاء أدوار جديدة وتخصيص الصلاحيات حسب الحاجة:

```php
// مثال: دور مشرف التحصيل
$supervisorRole = Role::firstOrCreate([
    'name' => 'external_collection_supervisor',
    'guard_name' => 'web',
]);

$supervisorPermissions = [
    'external-collection-view',
    'external-collection-edit',
    'external-collection-payment',
];

$supervisorRole->syncPermissions($supervisorPermissions);
```

### منح صلاحيات لمستخدم معين
```php
$user = User::find($userId);
$user->givePermissionTo('external-collection-payment');
```

### التحقق من الصلاحيات
```php
if ($user->hasPermissionTo('external-collection-payment')) {
    // السماح بمعالجة الدفعات
}
```

## ملاحظات هامة

1. **حماية البيانات**: جميع العمليات محمية بالصلاحيات
2. **التدقيق**: يتم تسجيل جميع العمليات في سجل التدقيق
3. **الكاش**: يجب مسح الكاش بعد تغيير الصلاحيات
4. **التحديث**: عند إضافة صلاحيات جديدة، يجب تحديث الأدوار المعنية

## استكشاف الأخطاء

### مشكلة: الصلاحيات لا تعمل
1. تحقق من تشغيل أمر seeders
2. مسح الكاش
3. التحقق من أسماء الصلاحيات في الكود

### مشكلة: القائمة الجانبية لا تظهر
1. تحقق من صلاحيات المستخدم
2. تحقق من إعدادات sidebar.php
3. تحقق من تفعيل middleware

### مشكلة: أزرار الدفع لا تعمل
1. تحقق من صلاحية `external-collection-payment`
2. تحقق من حالة التحصيل (مدفوع بالكامل)
3. تحقق من صلاحيات المستخدم
