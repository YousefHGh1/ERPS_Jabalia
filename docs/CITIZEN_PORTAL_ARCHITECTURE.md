# بوابة المواطن الرقمية | Citizen Digital Portal
## تصميم البنية المعمارية | Architecture Design

### نظرة عامة | Overview
بوابة المواطن الرقمية هي منصة ويب منفصلة تتيح للمواطنين تقديم الطلبات الإلكترونية، متابعة حالتها، والدفع الإلكتروني دون الحاجة لزيارة البلدية شخصياً.

### الميزات الرئيسية | Key Features

#### 1. إدارة الحسابات | Account Management
- تسجيل الدخول عبر رقم الهوية (National ID)
- التحقق من الهوية عبر SMS أو البريد الإلكتروني
- إدارة الملف الشخصي
- استعادة كلمة المرور

#### 2. تقديم الطلبات | Request Submission
- **طلبات التراخيص التجارية**: طلب رخصة مهنة جديدة، تجديد رخصة
- **طلبات التراخيص الهندسية**: طلب رخصة بناء، تصريح حفر
- **طلبات الخدمات البلدية**: طلب شهادة، طلب معاملة
- **التحصيل الخارجي**: الاستعلام عن الديون ودفعها
- **الشكاوى والاقتراحات**: تقديم شكوى أو اقتراح

#### 3. متابعة الطلبات | Request Tracking
- عرض حالة الطلب (قيد المراجعة، قيد المعالجة، مكتمل، مرفوض)
- عرض تاريخ التغييرات على الطلب
- إشعارات عبر البريد الإلكتروني أو SMS عند تغيير الحالة
- تحميل المستندات المرفقة

#### 4. الدفع الإلكتروني | Electronic Payment
- دفع الرسوم عبر البطاقة البنكية
- دفع عبر محافظ إلكترونية (Apple Pay, Google Pay)
- دفع عبر فوري
- إيصالات الدفع الإلكترونية

#### 5. التواصل | Communication
- نظام الشات مع موظفي البلدية
- نظام الإشعارات
- مركز المساعدة والدعم الفني

### البنية المعمارية التقنية | Technical Architecture

#### 1. Frontend (تطبيق منفصل)
```
citizen-portal-frontend/
├── src/
│   ├── components/
│   │   ├── Auth/
│   │   ├── Requests/
│   │   ├── Payments/
│   │   ├── Profile/
│   │   └── Notifications/
│   ├── pages/
│   │   ├── login.tsx
│   │   ├── dashboard.tsx
│   │   ├── requests/
│   │   ├── payments/
│   │   └── profile/
│   ├── services/
│   │   ├── api.ts
│   │   ├── auth.ts
│   │   └── payment.ts
│   └── store/
│       └── (Redux/Zustand)
├── package.json
└── vite.config.ts
```

**التقنيات المقترحة:**
- Framework: React 18+ أو Vue 3
- Build Tool: Vite
- UI Library: TailwindCSS + shadcn/ui أو Ant Design
- State Management: Zustand أو Redux Toolkit
- Forms: React Hook Form
- Validation: Zod
- HTTP Client: Axios
- Authentication: JWT Tokens

#### 2. Backend API (في النظام الحالي)
```
app/Http/Controllers/Api/Citizen/
├── AuthController.php
├── RequestController.php
├── PaymentController.php
├── ProfileController.php
├── NotificationController.php
└── ChatController.php
```

**المسارات المقترحة:**
```
/api/v1/citizen/auth/
├── POST /register - تسجيل حساب جديد
├── POST /login - تسجيل الدخول
├── POST /logout - تسجيل الخروج
├── POST /refresh-token - تجديد الرمز
└── POST /forgot-password - استعادة كلمة المرور

/api/v1/citizen/requests/
├── GET / - عرض جميع طلبات المستخدم
├── POST / - إنشاء طلب جديد
├── GET /{id} - عرض تفاصيل الطلب
├── PUT /{id} - تحديث الطلب
├── DELETE /{id} - حذف الطلب
└── POST /{id}/documents - رفع مستندات

/api/v1/citizen/payments/
├── GET / - عرض جميع المدفوعات
├── POST / - إنشاء دفعة جديدة
├── GET /{id} - عرض تفاصيل الدفعة
└── POST /{id}/receipt - تحميل الإيصال

/api/v1/citizen/profile/
├── GET / - عرض الملف الشخصي
├── PUT / - تحديث الملف الشخصي
├── POST /change-password - تغيير كلمة المرور
└── POST /upload-avatar - رفع صورة الملف الشخصي
```

#### 3. قاعدة البيانات | Database Extensions

**جدول المواطنين الجديد:**
```sql
CREATE TABLE citizen_users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    national_id VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(20) UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    date_of_birth DATE,
    gender ENUM('male', 'female'),
    address TEXT,
    city VARCHAR(100),
    is_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255),
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_national_id (national_id),
    INDEX idx_email (email),
    INDEX idx_phone (phone)
);

CREATE TABLE citizen_devices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    citizen_user_id BIGINT UNSIGNED NOT NULL,
    device_token VARCHAR(255) NOT NULL,
    device_type ENUM('ios', 'android', 'web') NOT NULL,
    last_used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_user_id) REFERENCES citizen_users(id) ON DELETE CASCADE
);
```

### الأمان | Security

#### 1. Authentication
- JWT Tokens مع refresh tokens
- Two-Factor Authentication (2FA) اختياري
- Session management مع timeout
- Rate limiting على مسارات المصادقة

#### 2. Authorization
- Role-based access control (citizen فقط)
- Permissions محددة لكل نوع طلب
- Audit logging لجميع العمليات

#### 3. Data Protection
- تشفير البيانات الحساسة (passwords, tokens)
- HTTPS إجباري في الإنتاج
- CORS settings صارمة
- Input validation و sanitization

### التكامل مع النظام الحالي | Integration

#### 1. Request System
- استخدام جدول `requests` الموجود مع إضافة `citizen_user_id`
- استخدام نظام الردود الموجود
- استخدام نظام الإشعارات الموجود

#### 2. Payment System
- استخدام جدول `request_payments` الموجود
- استخدام نظام Revenue Batch الموجود
- التكامل مع بوابات الدفع الإلكتروني

#### 3. Mokalaf System
- الربط مع جدول `mokalaf` للتحقق من بيانات المواطن
- عرض الديون المستحقة
- السماح بالدفع الإلكتروني

### خطوات التنفيذ | Implementation Steps

#### المرحلة 1: الأساسيات (أسبوع 1-2)
1. إنشاء جداول قاعدة البيانات
2. إنشاء API endpoints للمصادقة
3. إنشاء واجهة تسجيل الدخول
4. اختبار تدفق المصادقة

#### المرحلة 2: تقديم الطلبات (أسبوع 3-4)
1. إنشاء API endpoints للطلبات
2. إنشاء واجهة إنشاء الطلبات
3. إنشاء واجهة متابعة الطلبات
4. اختبار تدفق الطلبات

#### المرحلة 3: الدفع الإلكتروني (أسبوع 5-6)
1. التكامل مع بوابة الدفع
2. إنشاء API endpoints للمدفوعات
3. إنشاء واجهة الدفع
4. اختبار تدفق الدفع

#### المرحلة 4: الإشعارات والتواصل (أسبوع 7-8)
1. إنشاء نظام الإشعارات
2. إنشاء نظام الشات
3. اختبار التواصل

#### المرحلة 5: الاختبار والإطلاق (أسبوع 9-10)
1. اختبار شامل (End-to-End)
2. اختبار الأمان (Security Testing)
3. اختبار الأداء (Performance Testing)
4. إطلاق Beta
5. جمع الملاحظات
6. الإطلاق الرسمي

### التكاليف التقديرية | Estimated Costs

#### التطوير
- Frontend Developer: 2-3 أشهر
- Backend Developer: 2-3 أشهر
- UI/UX Designer: 1 شهر
- QA Engineer: 1 شهر

#### البنية التحتية
- Server: $50-100/شهرياً
- Database: $30-50/شهرياً
- Payment Gateway: 2-3% من كل معاملة
- SMS Service: $0.01-0.05/رسالة

### المخاطر والتحديات | Risks & Challenges

1. **الأمان**: حماية البيانات الشخصية للمواطنين
2. **الأداء**: التعامل مع عدد كبير من الطلبات المتزامنة
3. **التكامل**: التكامل مع الأنظمة الحكومية الأخرى
4. **تبني المستخدمين**: تشجيع المواطنين على استخدام البوابة
5. **الصيانة**: الحفاظ على البوابة محدثة وآمنة

### الخاتمة | Conclusion

بوابة المواطن الرقمية ستحسن بشكل كبير تجربة المواطنين وتقلل من عبء العمل على موظفي البلدية. مع التخطيط السليم والتنفيذ المنظم، يمكن أن تكون هذه الميزة ناجحة ومفيدة للجميع.
