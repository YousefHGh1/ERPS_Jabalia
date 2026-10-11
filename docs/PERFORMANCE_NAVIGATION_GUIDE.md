# دليل تسريع التنقل وتحميل الصفحات

**الهدف:** تقليل زمن تحميل الصفحة وانتقال شبه فوري بين صفحات الموقع.

---

## ما تم تنفيذه

### 1) ضغط المحتوى وترويسات الكاش — `public/.htaccess`
- **Gzip** لكل الاستجابات النصية (JS/CSS/HTML/JSON/SVG) عبر `mod_deflate` → تقليل حجم النقل ~80% لملفات JS/CSS.
- **ترويسات كاش** عبر `mod_expires` و`mod_headers`:
  - الأصول الثابتة: `Cache-Control: public, max-age=2592000` (شهر) + `Expires`.
  - أصول Vite المُبصّمة (`app-*.js`): `max-age=31536000, immutable` (سنة).
- صفحات Laravel الديناميكية تُترك لـ Laravel (`no-cache, private`) لتفعيل bfcache وسرعة الرجوع.

**القياسات الفعلية بعد التفعيل:**

| الملف | قبل (raw) | بعد (gzip) | التوفير |
|---|---|---|---|
| `plugins.bundle.js` | 4,364,437 | 975,162 | 78% |
| `style.bundle.rtl.css` | 2,127,606 | 176,731 | 92% |
| `vendor.bundle.rtl.min.css` | 2,365,782 | 240,600 | 90% |
| `scripts.bundle.js` | 332,554 | 60,012 | 82% |
| `plugins.bundle.rtl.css` | 567,812 | 80,682 | 86% |

### 1.ب) تفعيل وحدات Apache المطلوبة (بيئة Laragon)
كانت هذه الوحدات **معلّقة** في `C:\laragon\bin\apache\httpd-2.4.54-win64-VS16\conf\httpd.conf`:
```apache
LoadModule filter_module        modules/mod_filter.so   # مطلوب لـ AddOutputFilterByType
LoadModule deflate_module       modules/mod_deflate.so  # الضغط gzip
LoadModule expires_module       modules/mod_expires.so  # ترويسات Expires
```
> ملاحظة: `AddOutputFilterByType` يأتي من `mod_filter` لذا يجب تفعيله مع `mod_deflate`.
> بعد التعديل يجب **إعادة تشغيل Apache** من Laragon (Stop ثم Start).


### 2) تفريغ السكربتات الثقيلة من التحميل العام — `resources/views/layout/script.blade.php`
- أُزيل `fullcalendar.bundle.js` (**638 KB**) من كل الصفحات — كان يُحمَّل عالمياً بينما صفحة التقويم الوحيدة تستخدم نسخة CDN من FullCalendar.
- أُزيل `widgets.js` (**142 KB**) من التحميل العام — الصفحات التي تحتاجه (19 صفحة) تُحمّله أصلاً عبر `@section('scripts')`، فكان يُحمَّل مرتين.
- **النتيجة:** توفير ≈ **780 KB** في كل صفحة لا تحتاج هذين الملفين.
- أُضيف `@stack('vendor')` ليتمكن أي صفحة من تحميل مكتبات ثقيلة عند الحاجة فقط:
  ```blade
  @push('vendor')
      <script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
  @endpush
  ```

### 3) التحميل المسبق للتنقل الفوري (Prefetch)
- سكربت خفيف في `script.blade.php` يضيف `<link rel="prefetch" as="document">` للروابط الداخلية عند مرور المؤشر/اللمس/التركيز، فيكون المتصفح قد حمّل الصفحة قبل الضغط عليها → انتقال شبه فوري.

### 4) حزمة CSS واحدة للإنتاج
- `npm run build:css` يُنتج `public/assets/css/vendor.bundle.rtl.min.css` (يجمع 8 ملفات CSS في طلب واحد).
- يُستخدم تلقائياً في بيئة `production` فقط (انظر `layout/head.blade.php`).

### 5) تخزين الإنتاج المؤقت (Bootstrap caches) — خطوة نشر
- **عند النشر:** `php artisan config:cache` + `route:cache` + `view:cache` + `event:cache`.
- **في التطوير:** اتركها مُنظّفة `php artisan optimize:clear`؛ لأن `config:cache` يمنع Laravel من تحميل `.env` (فتتوقف قيم البيئة وتفشل الاختبارات التي تعتمد على `phpunit.xml`).
- لإصلاح تعطّل `env()` مع كاش الإعدادات: نُقلت القيم إلى ملفات الإعدادات:
  - `config/attendance.php` (كان `env('ZKTECO_IP')` داخل `AttendanceService`).
  - `CheckTreasurySections` صار يقرأ من `config('central_treasury...')`.

---

## الاختبارات

| الملف | الوصف |
|---|---|
| `tests/Unit/PerformanceOptimizationTest.php` | 11 اختبار: إعدادات Attendance، محتوى `.htaccess` (gzip/كاش)، تفريغ السكربتات الثقيلة، التحميل المسبق، `.gitignore` |
| `tests/Feature/PerformanceNavigationTest.php` | 3 اختبارات: عرض `layout.script` فعلياً والتأكد من عدم تحميل fullcalendar/widgets عالمياً + وجود prefetch |
| `scripts/check_performance_headers.php` | فحص حيّ لترويسات الضغط/الكاش على السيرفر |

**التشغيل:**
```bash
php artisan test --filter=Performance
php scripts/check_performance_headers.php http://127.0.0.1/jabalia/public
```

**النتيجة الحالية:** `14 passed (38 assertions)` + `7 نجاح / 0 فشل` في الفحص الحي.


---

## التشغيل عند النشر (Deployment)

```bash
# 1) بناء الأصول
npm ci
npm run build          # Vite (resources/js, resources/css)
npm run build:css      # حزمة CSS المجمّعة للقالب

# 2) تخزين الإنتاج المؤقت
php artisan config:cache
php artisan view:cache
php artisan event:cache
composer install --optimize-autoloader --no-dev
```

وفي `.env` للإنتاج:
```
APP_ENV=production
APP_DEBUG=false
```

للتراجع عن الكاش أثناء التطوير:
```bash
php artisan optimize:clear
```

---

## قيود معروفة (تحتاج قراراً قبل التفعيل)

### تعذّر `php artisan route:cache`
يوجد **تعارض حقيقي في أسماء المسارات**: مسار `employees.update` مُعرّف مرتين:
1. `routes/web.php` → `Route::resource('/employees', App\Http\Controllers\EmployeeController::class)`
2. وحدة `app/Modules/Employees/routes/web.php` (تُحمَّل عبر `ModuleServiceProvider` ببادئة `employees`)

لتفعيل كاش المسارات يجب إزالة/إعادة تسمية أحد المجموعتين. **لم يُغيّر هذا السلوك تلقائياً** لأنه يؤثر على أي Controller يخدم الطلب فعلياً.

التحقق:
```bash
php artisan route:list --name=employees.update
# يعرض مسارين — يجب أن يكون واحداً
```

---

## توصيات مستقبلية (أثر أكبر)
1. **تقسيم `plugins.bundle.js` (4.26 MB أكبر أصل في الموقع):** تحميل مكتبات محددة حسب الصفحة فقط (select2/datatables/...) بدل الحزمة الكاملة.
2. **كاش وليست/Redis:** تحويل `CACHE_DRIVER` و`SESSION_DRIVER` من `file` إلى `redis` عند توفر Redis.
3. **استضافة خط Cairo محلياً** بدل Google Fonts لإزالة زمن الطرف الثالث.
4. **Livewire `navigate`** لتنقل SPA كامل (Livewire مثبّت لكنه غير مستخدم حالياً).
5. مواصلة معالجة استعلامات N+1 في القوائم الطويلة.
