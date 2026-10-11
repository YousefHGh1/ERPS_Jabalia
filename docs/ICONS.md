# ملف مرجع الأيقونات — بلدية جباليا

المشروع يستخدم مصدرين رئيسيين للأيقونات:

---

## 1. Font Awesome (أيقونات الخط)

**التحميل:** مضمّن في `public/assets/plugins/global/plugins.bundle.rtl.css` (قالب Metronic).

**الصيغة:**
```html
<i class="fas fa-اسم-الأيقونة"></i>
```

**البادئات:**
- `fas` — Solid (الافتراضي)
- `far` — Regular
- `fab` — Brands

### أيقونات Font Awesome المستخدمة في المشروع

| الفئة | الاستخدام |
|--------|-----------|
| `fa fa-th-large` | لوحة التحكم |
| `fa fa-chart-line` | مركز التحكم التفاعلي |
| `fa fa-user-tie` | بوابة الموظف |
| `fa fa-bell` | الإشعارات |
| `fa fa-briefcase` | الشؤون الإدارية |
| `fa fa-tags` | الفئات |
| `fa fa-money-bill-wave` | المكافآت |
| `fa fa-id-badge` | المسميات الوظيفية |
| `fa fa-sitemap` | الهيكلية |
| `fa fa-building` | الدوائر |
| `fa fa-th-list` | الأقسام |
| `fa fa-code-branch` | الشعب |
| `fa fa-users-cog` | الموظفين |
| `fa fa-calendar-alt` | الإجازات الرئيسية |
| `fa fa-umbrella-beach` | الإجازات |
| `fa fa-user-clock` | الأذونات |
| `fa fa-user-lock` | صلاحيات الموظف |
| `fa fa-clock` | الورديات |
| `fa fa-fingerprint` | الحضور |
| `fa fa-car-side` | المركبات |
| `fa fa-cogs` | الإعدادات |
| `fa fa-shield-alt` | الصلاحيات |
| `fa fa-user-tag` | المكلفين |
| `fa fa-clipboard-list` | الطلبات |
| `fa fa-users` | الموظفين (إحصائيات) |
| `fa fa-fingerprint` | الحضور اليومي |
| `fa fa-calendar-check` | الإجازات المعلقة |
| `fa fa-clock` | الأذونات المعلقة |
| `fa fa-briefcase` | المسميات |
| `fa fa-car` | المركبات |
| `fa fa-building` | الدوائر |
| `fa fa-network-wired` | الأقسام |
| `fa fa-gavel` | أوامر الحبس |
| `fa fa-calendar-alt` | الحجوزات |
| `fa fa-spinner fa-spin` | تحميل |
| `fa fa-check` | موافق |
| `fa fa-times` | إلغاء |
| `fa fa-edit` | تعديل |
| `fa fa-trash` | حذف |
| `fa fa-print` | طباعة |
| `fa fa-file-excel` | تصدير إكسل |
| `fa fa-file-csv` | تصدير CSV |
| `fa fa-eye` | عرض |
| `fa fa-check-circle` | تم التحقق |
| `fa fa-arrow-right` | عودة |
| `fa fa-paper-plane` | طلباتي |
| `fa fa-sync-alt` | تحديث |
| `fa fa-drafting-compass` | الهندسة |
| `fa fa-tools` | الحرف |
| `fa fa-plus-circle` | إضافة |
| `fa fa-exclamation-triangle` | تحذير |
| `fa fa-circle` | نقطة (مؤشر) |

**رابط المراجع الرسمي:** [fontawesome.com/icons](https://fontawesome.com/icons)

---

## 2. Flaticon (قالب Metronic / KeenTheme)

**التحميل:** مضمّن في نفس ملف الـ plugins (Flaticon خطوط مخصّصة للقالب).

> **ملاحظة (ترحيل):** القائمة الجانبية (`config/sidebar.php`) لم تعد تستخدم أيقونات
> Flaticon — تم ترحيلها بالكامل إلى Font Awesome 6 (المحمّل في `layout/head`).
> أيقونات `flaticon-tools` و`flaticon2-architecture` لم تكن معرّفة أصلاً في خط
> Metronic، واستُبدلت بأيقونات FA6 معبّرة مع نظام بلاطات موحد في `public/css/sidebar-custom.css`.

**الصيغة:**
```html
<i class="flaticon2-اسم-الأيقونة"></i>
<!-- أو -->
<i class="flaticon-اسم-الأيقونة"></i>
```

### أيقونات Flaticon المستخدمة في المشروع

#### عمليات شائعة
| الفئة | الاستخدام |
|--------|-----------|
| `flaticon-search` | بحث |
| `flaticon-search-1` | بحث (بديل) |
| `flaticon2-search-1` | بحث في حقول |
| `flaticon2-plus` | إضافة / جديد |
| `flaticon2-refresh` | إعادة تعيين / تحديث |
| `flaticon2-pen` | تعديل |
| `flaticon2-edit` | تعديل |
| `flaticon2-trash` | حذف |
| `flaticon2-check-mark` | حفظ / موافق / تم |
| `flaticon2-cancel` | إلغاء |
| `flaticon2-cross` | إغلاق |
| `flaticon2-delete` | حذف (إعدادات) |
| `flaticon2-back` | عودة |
| `flaticon2-loading flaticon-spin` | جاري التحميل |

#### مستندات وملفات
| الفئة | الاستخدام |
|--------|-----------|
| `flaticon2-document` | مستند / رخصة |
| `flaticon2-file` | ملف |
| `flaticon2-attachment` | مرفق |
| `flaticon2-download` | تحميل |
| `flaticon2-print` | طباعة |
| `flaticon2-open-box` | لا توجد بيانات (فارغ) |

#### أشخاص ومستخدمون
| الفئة | الاستخدام |
|--------|-----------|
| `flaticon2-user` | مستخدم / شخص |
| `flaticon2-user-1` | مستخدم (بديل) |
| `flaticon2-users` | مجموعة أشخاص |
| `flaticon2-identification` | رقم هوية |
| `flaticon2-id-badge` | بطاقة هوية |
| `flaticon2-group` | أدوار / مجموعة |
| `flaticon2-pin` | عنوان / موقع |

#### تقويم وإشعارات
| الفئة | الاستخدام |
|--------|-----------|
| `flaticon2-calendar` | تقويم |
| `flaticon2-calendar-1` | تاريخ |
| `flaticon2-calendar-2` | تقويم (فارغ) |
| `flaticon2-calendar-3` | تاريخ |
| `flaticon2-calendar-8` | إجازات / حجوزات |
| `flaticon2-bell-alarm-symbol` | عاجل |
| `flaticon2-notification` | إخطار |
| `flaticon2-info` | معلومات |
| `flaticon2-information` | معلومات (تفاصيل) |

#### إعدادات وأمان
| الفئة | الاستخدام |
|--------|-----------|
| `flaticon2-lock` | صلاحيات / قفل |
| `flaticon2-gear` | إعدادات |
| `flaticon2-checking` | حالة / تحقق |
| `flaticon2-warning` | تحذير |
| `flaticon2-add` | إضافة (توسيع) |
| `flaticon2-list` | قائمة |
| `flaticon2-list-3` | قائمة / أوامر |
| `flaticon2-phone` | هاتف |
| `flaticon2-location` | عنوان |
| `flaticon2-tag` | نوع / تصنيف |
| `flaticon2-shield` | حماية / صلاحيات |
| `flaticon2-delivery-truck` | مركبات |

#### وحدات التطبيق
| الفئة | الاستخدام |
|--------|-----------|
| `flaticon-tools` | الحرف والصناعات |
| `flaticon2-architecture` | الهندسة والتنظيم |
| `flaticon2-money` | مدفوعات / أموال |
| `flaticon-chart-line` | إيرادات / رسوم بيانية |
| `flaticon-time` | وقت / معلق |
| `flaticon-alert` | تنبيه / غير مدفوع |
| `flaticon-warning` | تحذير (تنبيهات) |
| `flaticon-edit` | تعديل (قديم) |
| `flaticon-delete` | حذف (قديم) |
| `flaticon-download` | تصدير / تحميل |
| `flaticon-users-1` | موظفين (إجراءات سريعة) |
| `flaticon2-clipboard` | طلب جديد |
| `flaticon-analytics` | تقارير |

---

## 3. أحجام وألوان شائعة

```html
<!-- حجم -->
<i class="fas fa-users fa-2x"></i>
<i class="flaticon2-open-box icon-4x"></i>

<!-- لون (Bootstrap / قالب) -->
<i class="fas fa-bell text-primary"></i>
<i class="flaticon2-trash text-danger"></i>
<i class="flaticon2-check-mark text-success"></i>
<i class="flaticon2-pen text-primary"></i>
<i class="flaticon2-user text-muted"></i>

<!-- دوار (تحميل) -->
<i class="fas fa-spinner fa-spin"></i>
<i class="flaticon2-loading flaticon-spin"></i>
```

---

## 4. أين توجد الملفات

- **خطوط وأيقونات القالب:** `public/assets/plugins/global/plugins.bundle.rtl.css` وملفات الخطوط المرتبطة في `public/assets/`.
- **أيقونات SVG داخل الصفحات:** بعض الأيقونات مكتوبة كـ SVG داخل القوالب (مثل قسم الإعدادات في السايدبار).

---

## 5. إضافة أيقونة جديدة

1. **Font Awesome:** اختر الاسم من [fontawesome.com](https://fontawesome.com/icons) ثم استخدم:
   ```html
   <i class="fas fa-اسم-الأيقونة"></i>
   ```
2. **Flaticon (القالب):** الأسماء المتوفرة هي التي تظهر في القالب (مثل `flaticon2-*` و `flaticon-*`). للتحقق من وجود أيقونة، ابحث في المشروع عن `flaticon2-` أو راجع توثيق قالب Metronic.

تم إنشاء هذا الملف في: `docs/ICONS.md`
