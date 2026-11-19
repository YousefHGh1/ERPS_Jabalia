# تعليمات استخدام أمر تعيين دور موظف متابعة بريد الطلبات

## نظرة عامة

هذا الأمر يقوم بتعيين دور `request_mail_officer` (موظف متابعة بريد الطلبات) لكل موظف موجود في مسار محدد أو قسم أو قسم فرعي، أو يمكنه فحص جميع المسارات تلقائياً باستخدام خيار `--all-workflows`.

## المتطلبات

- يجب أن يكون دور `request_mail_officer` موجوداً في النظام
- يجب أن تكون الصلاحيات مقترنة بالدور
- يجب ربط الموظفين بالأقسام/الدوائر في النظام

## الاستخدام

### 1. تعيين الدور بناءً على مسار (Workflow)

```bash
php artisan roles:assign-request-mail-officer --workflow-id=3
```

أو في وضع المعاينة (Dry-Run) لعرض ما سيحدث دون تنفيذ:

```bash
php artisan roles:assign-request-mail-officer --workflow-id=3 --dry-run
```

### 2. تعيين الدور بناءً على قسم (Department)

```bash
php artisan roles:assign-request-mail-officer --department-id=5
```

أو في وضع المعاينة:

```bash
php artisan roles:assign-request-mail-officer --department-id=5 --dry-run
```

### 3. تعيين الدور بناءً على قسم فرعي (Section)

```bash
php artisan roles:assign-request-mail-officer --section-id=10
```

أو في وضع المعاينة:

```bash
php artisan roles:assign-request-mail-officer --section-id=10 --dry-run
```

### 4. فحص جميع المسارات تلقائياً (All Workflows)

هذا الخيار يقوم بفحص جميع المسارات في النظام ويجمع كل الأقسام والدوائر المرتبطة بها، ثم يعطي الدور لكل موظف موجود فيها:

```bash
php artisan roles:assign-request-mail-officer --all-workflows
```

أو في وضع المعاينة:

```bash
php artisan roles:assign-request-mail-officer --all-workflows --dry-run
```

## الخطوات على السيرفر

### الخطوة 1: إنشاء الدور (إذا لم يكن موجوداً)

```bash
php scripts/setup_request_mail_officer_role.php
```

### الخطوة 2: عرض المسارات المتاحة

```bash
php artisan tinker --execute="print_r(App\Models\RequestWorkflow::all(['id', 'name', 'request_type_id', 'section_id', 'department_id'])->toArray());"
```

أو استخدم الأمر:

```bash
php artisan roles:assign-request-mail-officer --workflow-id=1 --dry-run
```

### الخطوة 3: معاينة التغييرات (Dry-Run)

اختر المسار/القسم/القسم الفرعي الذي تريد تعيين الدور له، واستخدم `--dry-run` لمعاينة قائمة الموظفين قبل التنفيذ:

```bash
# مثال: تعيين الدور لموظفي قسم قلم الجمهور (section_id=16)
php artisan roles:assign-request-mail-officer --section-id=16 --dry-run
```

سيعرض الأمر قائمة الموظفين الذين سيتم تعيين الدور لهم دون تنفيذ التغيير فعلياً.

### الخطوة 4: تنفيذ التغيير فعلياً

بعد التأكد من قائمة الموظفين في الخطوة السابقة، نفذ الأمر بدون `--dry-run`:

```bash
php artisan roles:assign-request-mail-officer --section-id=16
```

### الخطوة 5: تنظيف الكاش

```bash
php artisan cache:clear
php artisan config:cache
php artisan view:clear
```

### الأوامر الكاملة بالتسلسل (مع --dry-run)

#### الخيار 1: فحص جميع المسارات (موصى به)

```bash
# 1) إنشاء الدور (إذا لم يكن موجوداً)
php scripts/setup_request_mail_officer_role.php

# 2) فحص جميع المسارات ومعاينة التغييرات
php artisan roles:assign-request-mail-officer --all-workflows --dry-run

# 3) تنفيذ التغيير فعلياً
php artisan roles:assign-request-mail-officer --all-workflows

# 4) تنظيف الكاش
php artisan cache:clear
php artisan config:cache
php artisan view:clear
```

#### الخيار 2: تعيين الدور لقسم محدد

```bash
# 1) إنشاء الدور (إذا لم يكن موجوداً)
php scripts/setup_request_mail_officer_role.php

# 2) معاينة التغييرات لقسم معين
php artisan roles:assign-request-mail-officer --section-id=16 --dry-run

# 3) تنفيذ التغيير فعلياً
php artisan roles:assign-request-mail-officer --section-id=16

# 4) تنظيف الكاش
php artisan cache:clear
php artisan config:cache
php artisan view:clear
```

## ملاحظات مهمة

1. **ربط الموظفين**: يجب التأكد من ربط كل موظف بقسم «قسم قلم الجمهور» من شاشة الموظفين حتى يتمكن من الوصول إلى بريد الطلبات.

2. **الأدوار المتعددة**: يمكن للموظف أن يكون لديه أكثر من دور. هذا الأمر يضيف الدور `request_mail_officer` دون إزالة الأدوار الأخرى.

3. **الصلاحيات**: الموظفون الذين يتم تعيين الدور لهم سيحصلون تلقائياً على صلاحيات:
   - عرض قائمة الطلبات
   - عرض تفاصيل الطلب
   - عرض/تسجيل/تعديل الردود على الطلبات
   - الاستعلام المرجعي للمكلفين وأنواع الطلبات

4. **القسم المطلوب**: للرد الفعلي على الطلبات، يجب أن يكون الموظف مرتبطاً بقسم «قسم قلم الجمهور» (section_id=16).

## أمثلة عملية

### مثال 1: تعيين الدور لموظفي قسم قلم الجمهور

```bash
# معاينة أولاً
php artisan roles:assign-request-mail-officer --section-id=16 --dry-run

# ثم تنفيذ
php artisan roles:assign-request-mail-officer --section-id=16
```

### مثال 2: تعيين الدور بناءً على مسار معين

```bash
# معاينة أولاً
php artisan roles:assign-request-mail-officer --workflow-id=3 --dry-run

# ثم تنفيذ
php artisan roles:assign-request-mail-officer --workflow-id=3
```

### مثال 3: تعيين الدور لموظفي دائرة كاملة

```bash
# معاينة أولاً
php artisan roles:assign-request-mail-officer --department-id=5 --dry-run

# ثم تنفيذ
php artisan roles:assign-request-mail-officer --department-id=5
```

### مثال 4: فحص جميع المسارات وإعطاء الدور لكل موظف

```bash
# معاينة أولاً
php artisan roles:assign-request-mail-officer --all-workflows --dry-run

# ثم تنفيذ
php artisan roles:assign-request-mail-officer --all-workflows
```

## استكشاف الأخطاء

### خطأ: "الدور request_mail_officer غير موجود"

الحل: شغّل سكربت الإعداد أولاً:
```bash
php scripts/setup_request_mail_officer_role.php
```

### خطأ: "لم يتم العثور على أي موظفين"

الحل: تأكد من:
1. الموظفين مرتبطون بالأقسام/الدوائر في النظام
2. الأقسام/الدوائر تحتوي على موظفين
3. استخدم `--dry-run` لمعاينة قائمة الموظفين أولاً

### الموظفون لا يستطيعون الوصول إلى بريد الطلبات

الحل:
1. تأكد من ربط الموظفين بقسم «قسم قلم الجمهور» (section_id=16)
2. تأكد من تنظيف الكاش بعد تعيين الدور
3. تأكد من أن الصلاحيات مقترنة بالدور بشكل صحيح

## الدعم

للمزيد من المساعدة، راجع:
- `database/seeders/RoleSeeder.php` - تعريف الصلاحيات
- `config/sidebar_permissions.php` - صلاحيات الشريط الجانبي
- `scripts/setup_request_mail_officer_role.php` - سكربت الإعداد
