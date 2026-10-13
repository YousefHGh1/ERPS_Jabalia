cd ~/jabalia
git pull origin main
php artisan config:clear && php artisan view:clear && php artisan cache:clear
php artisan config:cache && php artisan view:cache && php artisan event:cache



# تفعيل كاش الإعدادات والعرض فقط (آمنة ولا تتأثر بالتعارض)
php artisan config:cache
php artisan view:cache
php artisan event:cache

cd ~/jabalia

# ألغِ كاش المسارات فقط (هذا يحل المشكلة فوراً)
php artisan route:clear

# وللأمان نظّف باقي الكاش
php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan event:clear


المشكلة الأساسية اتضحت: مجلد `jabalia` على السيرفر **ليس مستودع git** — الملفات رُفعت يدوياً وليس عبر `git clone`، لذلك `git pull` يفشل (`not a git repository`).

بما أنك أكدت أن **لا توجد ملفات مرفوعة مهمة ويمكن المزامنة من GitHub بأمان**، أنسب حل هو **استنساخ نظيف من GitHub مع الحفاظ على `.env` الخاص بسيرفر الإنتاج**. بهذا يصبح المجلد مستودع git حقيقياً، ويعمل `git pull` في كل مرة لاحقاً.

## ⚠️ نقطة حرجة قبل البدء
مسار المشروع على السيرفر يجب أن يبقى `~/jabalia` (لأن document root يشير إليه). الطريقة أدناه تحافظ على نفس المسار.

---

## 🚀 خطوات النشر على cPanel (Terminal / SSH)

**1) انقل المجلد الحالي كنسخة احتياطية (نفس المسار `jabalia` سيبقى متاحاً):**
```bash
cd ~
mv jabalia jabalia_backup
```
> إن فشل الأمر لأن `jabalia_backup` موجود من قبل، استخدم اسماً آخر مثل `jabalia_backup2`.

**2) استنسخ نسخة نظيفة من GitHub:**
```bash
git clone https://github.com/YousefHGh1/ERPS_Jabalia.git jabalia
cd jabalia
```

**3) استعِد `.env` الإنتاجي من النسخة القديمة (مهم جداً):**
```bash
cp ../jabalia_backup/.env .env
```

**4) تأكد من مجلدات التخزين وصلاحياتها:**
```bash
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

**5) (فقط إن كان `.env` بلا مفتاح) ولّد مفتاح التطبيق:**
```bash
php artisan key:generate
```
> في الغالب لا تحتاجها لأن `.env` المنقول يحتوي `APP_KEY` أصلاً.

**6) أعد بناء كاش الإنتاج:**
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize
php artisan migrate --force
```

---

## ✅ تحقق من النجاح
- افتح الموقع → يجب أن يعمل بدون شاشة بيضاء/خطأ 500.
- تأكد أن المستودع أصبح git ويتبع GitHub:
```bash
cd ~/jabalia && git remote -v && git log --oneline -1
```
- يجب أن يظهر `origin/main` وأحدث commit `كامل`.

## 🧹 تنظيف (بعد التأكد أن كل شيء يعمل)
```bash
rm -rf ~/jabalia_backup
```

---

## 📌 ملاحظات مهمة
- **`composer install` غير مطلوب** — لأن `vendor/` متتبَّع كاملاً في GitHub (10351 ملف).
- **`npm run build` غير مطلوب** — لأن `public/build` (Vite) متتبَّع في GitHub.
- **التحديثات المستقبلية** بعد هذه التهيئة ستكون بسيطة: `cd ~/jabalia && git pull origin main` ثم أوامر الكاش.
- ⚠️ ملف `.env` متتبَّع في GitHub ويحتوي `APP_ENV=local, APP_DEBUG=true` وإعدادات DB محلية — لهذا نُبقي `.env` الخاص بالسيرفر كما هو (الخطوة 3) ولا نسمح لـ git باستبداله.

جرّب الخطوات وأخبرني بالنتيجة (خصوصاً إن ظهر أي خطأ في الخطوة 5 أو 6)، وسأساعدك في أي مشكلة.
