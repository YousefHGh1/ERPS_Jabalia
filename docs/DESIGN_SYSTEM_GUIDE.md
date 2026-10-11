# دليل نظام التصميم الموحد | Unified Design System Guide
## بلدية جباليا | Jabalia Municipality

### نظرة عامة | Overview

تم إنشاء نظام تصميم موحد وشامل لتحسين التناسق في جميع واجهات المستخدم في نظام بلدية جباليا. هذا النظام يوفر متغيرات موحدة للألوان، الخطوط، التباعد، والحدود، بالإضافة إلى مكونات CSS جاهزة للاستخدام.

---

### الملفات الجديدة | New Files

#### 1. نظام التصميم الأساسي | Core Design System
- `resources/sass/_design-system.scss` - نظام التصميم الشامل مع 16 فئة من المتغيرات

#### 2. مكونات CSS | CSS Components
- `resources/sass/components/_buttons.scss` - نظام الأزرار الموحد
- `resources/sass/components/_cards.scss` - نظام البطاقات الموحد
- `resources/sass/components/_forms.scss` - نظام النماذج الموحد
- `resources/sass/components/_tables.scss` - نظام الجداول الموحد
- `resources/sass/components/_alerts.scss` - نظام التنبيهات الموحد
- `resources/sass/components/_badges.scss` - نظام الشارات الموحد
- `resources/sass/components/_sidebar.scss` - نظام السايدبار الموحد

#### 3. الملفات المحدثة | Updated Files
- `resources/sass/app.scss` - تم تحديثه لاستخدام نظام التصميم والمكونات الجديدة
- `vite.config.js` - تم إصلاحه لبناء CSS بنجاح

---

### نظام الألوان | Color System

#### الألوان الأساسية للعلامة التجارية
```scss
$jabalia-blue: #3699FF;        // اللون الأساسي
$jabalia-blue-dark: #2875d9;   // أزرق داكن
$jabalia-blue-light: #63B3ED;  // أزرق فاتح
```

#### الألوان الوظيفية
```scss
$jabalia-success: #1BC5BD;     // أخضر للنجاح
$jabalia-success-light: #68D391;
$jabalia-warning: #FFA800;     // برتقالي للتحذير
$jabalia-warning-light: #F6AD55;
$jabalia-danger: #F64E60;      // أحمر للخطر
$jabalia-danger-light: #FC8181;
$jabalia-info: #8950FC;        // بنفسجي للمعلومات
$jabalia-info-light: #76E4F7;
```

#### الألوان المحايدة
```scss
$jabalia-gray-50: #F9FAFB;
$jabalia-gray-100: #F3F4F6;
$jabalia-gray-200: #E5E7EB;
$jabalia-gray-300: #D1D5DB;
$jabalia-gray-400: #9CA3AF;
$jabalia-gray-500: #6B7280;
$jabalia-gray-600: #4B5563;
$jabalia-gray-700: #374151;
$jabalia-gray-800: #1F2937;
$jabalia-gray-900: #111827;
```

---

### نظام الخطوط | Typography System

#### عائلة الخطوط
```scss
$font-primary: 'Cairo', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
```

#### أحجام الخطوط
```scss
$font-xs: 0.75rem;    // 12px
$font-sm: 0.875rem;   // 14px
$font-base: 1rem;     // 16px
$font-lg: 1.125rem;   // 18px
$font-xl: 1.25rem;    // 20px
$font-2xl: 1.5rem;    // 24px
$font-3xl: 1.875rem;  // 30px
$font-4xl: 2.25rem;   // 36px
$font-5xl: 3rem;      // 48px
```

#### أوزان الخطوط
```scss
$font-light: 300;
$font-normal: 400;
$font-medium: 500;
$font-semibold: 600;
$font-bold: 700;
$font-extrabold: 800;
$font-black: 900;
```

---

### نظام التباعد | Spacing System

```scss
$spacing-0: 0;
$spacing-1: 0.25rem;   // 4px
$spacing-2: 0.5rem;    // 8px
$spacing-3: 0.75rem;   // 12px
$spacing-4: 1rem;      // 16px
$spacing-5: 1.25rem;   // 20px
$spacing-6: 1.5rem;    // 24px
$spacing-8: 2rem;      // 32px
$spacing-10: 2.5rem;   // 40px
$spacing-12: 3rem;     // 48px
$spacing-16: 4rem;     // 64px
```

---

### استخدام المكونات | Using Components

#### الأزرار | Buttons
```html
<!-- أزرار أساسية -->
<button class="btn btn-primary">زر أساسي</button>
<button class="btn btn-success">زر نجاح</button>
<button class="btn btn-warning">زر تحذير</button>
<button class="btn btn-danger">زر خطر</button>

<!-- أحجام مختلفة -->
<button class="btn btn-primary btn-sm">صغير</button>
<button class="btn btn-primary">متوسط</button>
<button class="btn btn-primary btn-lg">كبير</button>

<!-- أزرار Outline -->
<button class="btn btn-outline-primary">Outline</button>

<!-- أزرار Ghost -->
<button class="btn btn-ghost">Ghost</button>

<!-- أزرار أيقونة -->
<button class="btn btn-icon btn-primary">
    <i class="fa fa-plus"></i>
</button>
```

#### البطاقات | Cards
```html
<!-- بطاقة أساسية -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">عنوان البطاقة</h5>
    </div>
    <div class="card-body">
        محتوى البطاقة
    </div>
</div>

<!-- بطاقة ملونة -->
<div class="card card-primary">
    <!-- ... -->
</div>

<!-- بطاقة إحصائيات -->
<div class="card-stats">
    <div class="stats-icon icon-primary">
        <i class="fa fa-chart-line"></i>
    </div>
    <div class="stats-value">1,234</div>
    <div class="stats-label">إجمالي المستخدمين</div>
</div>
```

#### النماذج | Forms
```html
<div class="form-group">
    <label class="form-label required">البريد الإلكتروني</label>
    <input type="email" class="form-control" placeholder="example@email.com">
    <small class="form-text">معلومات إضافية</small>
</div>

<!-- بحث مع أيقونة -->
<div class="input-search">
    <i class="search-icon fa fa-search"></i>
    <input type="text" class="form-control" placeholder="بحث...">
</div>
```

#### الجداول | Tables
```html
<!-- جدول أساسي -->
<table class="table">
    <thead>
        <tr>
            <th>الاسم</th>
            <th>البريد</th>
            <th>الحالة</th>
        </tr>
    </thead>
    <tbody>
        <!-- ... -->
    </tbody>
</table>

<!-- جدول مع بطاقة -->
<div class="table-card">
    <div class="table-header">
        <h5 class="table-title">قائمة الموظفين</h5>
        <div class="table-actions">
            <!-- أزرار -->
        </div>
    </div>
    <div class="table-body">
        <table class="table">
            <!-- ... -->
        </table>
    </div>
</div>

<!-- شارة الحالة في الجدول -->
<span class="table-status status-active">نشط</span>
<span class="table-status status-pending">قيد الانتظار</span>
<span class="table-status status-danger">مرفوض</span>
```

#### التنبيهات | Alerts
```html
<div class="alert alert-success">
    <div class="alert-icon">
        <i class="fa fa-check-circle"></i>
    </div>
    <div class="alert-content">
        <div class="alert-title">نجاح!</div>
        <div class="alert-message">تمت العملية بنجاح</div>
    </div>
</div>

<!-- تنبيه صلب -->
<div class="alert alert-solid alert-danger">
    <!-- ... -->
</div>
```

#### الشارات | Badges
```html
<span class="badge badge-primary">أساسي</span>
<span class="badge badge-success">نجاح</span>
<span class="badge badge-warning">تحذير</span>

<!-- شارة Outline -->
<span class="badge badge-outline-primary">Outline</span>

<!-- شارة Pill -->
<span class="badge badge-pill badge-info">Pill</span>

<!-- شارة الحالة -->
<span class="badge-status status-active">
    نشط
</span>
```

---

### إرشادات الاستخدام | Usage Guidelines

#### 1. استخدام الألوان
- استخدم الألوان الأساسية للعلامة التجارية ($jabalia-blue) للعناصر الرئيسية
- استخدم الألوان الوظيفية (success, warning, danger) للإشارات الوظيفية
- استخدم الألوان المحايدة (gray) للخلفيات والنصوص الثانوية

#### 2. استخدام التباعد
- استخدم نظام التباعد الموحد ($spacing-*) بدلاً من القيم العشوائية
- الحفاظ على التناسق في التباعد بين العناصر المتشابهة

#### 3. استخدام الزوايا
- استخدم $radius-md للعناصر العادية
- استخدم $radius-lg للبطاقات والأزرار الكبيرة
- استخدم $radius-full للأزرار الدائرية والشارات

#### 4. استخدام الظلال
- استخدم $shadow-base للعناصر العادية
- استخدم $shadow-lg للعناصر المرتفعة (hover, cards)
- استخدم $shadow-colored-sm للعناصر الملونة

---

### البناء | Build

لبناء CSS الجديد:
```bash
npm run build
```

لبناء CSS في وضع التطوير:
```bash
npm run dev
```

---

### الصيانة | Maintenance

#### إضافة متغير جديد
1. أضف المتغير في `resources/sass/_design-system.scss`
2. استخدم المتغير في مكونات CSS المناسبة
3. أعد بناء CSS

#### إضافة مكون جديد
1. أنشئ ملف جديد في `resources/sass/components/`
2. استخدم متغيرات نظام التصميم
3. أضف الـ import في `resources/sass/app.scss`
4. أعد بناء CSS

---

### التوافق | Compatibility

- متوافق مع Bootstrap 5
- يدعم جميع المتصفحات الحديثة
- يدعم RTL (من اليمين لليسار) للعربية
- متجاوب مع جميع أحجام الشاشات

---

### التطوير المستقبلي | Future Development

1. إضافة المزيد من المكونات (Modals, Dropdowns, Tabs)
2. إنشاء Theme Switcher (Dark/Light mode)
3. إضافة Animations و Transitions مخصصة
4. إنشاء Component Library مستقلة
5. إضافة Storybook للتوثيق المرئي
