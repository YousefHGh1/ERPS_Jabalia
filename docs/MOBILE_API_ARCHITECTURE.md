# REST API للموبايل مع Swagger | Mobile REST API with Swagger
## تصميم البنية المعمارية | Architecture Design

### نظرة عامة | Overview
REST API للموبايل يوفر واجهة برمجية قوية وموثقة لتطبيقات الطرف الثالث للتكامل مع نظام بلدية جباليا. يستخدم Laravel Passport للمصادقة و Swagger/OpenAPI للتوثيق التلقائي.

### الميزات الرئيسية | Key Features

#### 1. المصادقة والأمان | Authentication & Security
- OAuth2.0 مع Laravel Passport
- JWT Tokens
- Refresh Tokens
- Rate Limiting
- API Key Authentication

#### 2. التوثيق التلقائي | Automatic Documentation
- Swagger/OpenAPI 3.0
- Interactive API documentation
- Code generation for clients
- Version control for API

#### 3. الاستجابة الموحدة | Standardized Response
- JSON format
- Consistent error handling
- Pagination support
- Filtering and sorting

### البنية المعمارية التقنية | Technical Architecture

#### 1. التثبيت | Installation

```bash
# تثبيت Laravel Passport
composer require laravel/passport

# تثبيت Swagger (L5-Swagger)
composer require darkaonline/l5-swagger

# تثبيت API Versioning
composer require knuckleswtf/scribe
```

#### 2. إعداد Laravel Passport

```php
// config/auth.php
'guards' => [
    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

```bash
# تثبيت Passport
php artisan passport:install

# إنشاء keys
php artisan passport:keys
```

#### 3. إعداد Swagger

```php
// config/l5-swagger.php
'api' => [
    'title' => 'Jabalia Municipality API',
    'version' => '1.0.0',
    'description' => 'REST API for Jabalia Municipality Mobile Applications',
    'contact' => [
        'email' => 'api@jabalia.ps',
    ],
    'securityDefinitions' => [
        'security' => [
            'type' => 'oauth2',
            'description' => 'OAuth2 authentication',
            'flow' => 'password',
            'tokenUrl' => '/oauth/token',
        ],
    ],
],
```

### هيكل الـ API | API Structure

```
app/Http/Controllers/Api/V1/
├── AuthController.php
├── CitizenController.php
├── EmployeeController.php
├── RequestController.php
├── CraftLicenseController.php
├── EngineeringController.php
├── PaymentController.php
├── NotificationController.php
├── ReportController.php
└── DashboardController.php

app/Http/Requests/Api/V1/
├── Auth/
│   ├── LoginRequest.php
│   ├── RegisterRequest.php
│   └── RefreshTokenRequest.php
├── Request/
│   ├── CreateRequestRequest.php
│   └── UpdateRequestRequest.php
└── Payment/
    └── ProcessPaymentRequest.php

app/Http/Resources/Api/V1/
├── UserResource.php
├── RequestResource.php
├── CraftLicenseResource.php
├── PaymentResource.php
└── NotificationResource.php

app/Http/Middleware/
├── ApiVersionMiddleware.php
├── ApiLogMiddleware.php
└── ApiThrottleMiddleware.php
```

### مسارات الـ API | API Endpoints

#### 1. المصادقة | Authentication

```
POST /api/v1/auth/register
POST /api/v1/auth/login
POST /api/v1/auth/logout
POST /api/v1/auth/refresh
POST /api/v1/auth/forgot-password
POST /api/v1/auth/reset-password
GET  /api/v1/auth/me
```

**مثال Swagger:**
```yaml
/auth/login:
  post:
    tags:
      - Authentication
    summary: User login
    description: Authenticate user and return access token
    requestBody:
      required: true
      content:
        application/json:
          schema:
            type: object
            properties:
              email:
                type: string
                format: email
              password:
                type: string
                format: password
    responses:
      200:
        description: Successful login
        content:
          application/json:
            schema:
              type: object
              properties:
                access_token:
                  type: string
                token_type:
                  type: string
                  enum: [Bearer]
                expires_in:
                  type: integer
                refresh_token:
                  type: string
      401:
        description: Invalid credentials
```

#### 2. المواطنون | Citizens

```
GET    /api/v1/citizens/profile
PUT    /api/v1/citizens/profile
GET    /api/v1/citizens/requests
POST   /api/v1/citizens/requests
GET    /api/v1/citizens/requests/{id}
GET    /api/v1/citizens/payments
POST   /api/v1/citizens/payments
GET    /api/v1/citizens/notifications
PUT    /api/v1/citizens/notifications/{id}/read
```

#### 3. الموظفون | Employees

```
GET    /api/v1/employees/profile
PUT    /api/v1/employees/profile
GET    /api/v1/employees/attendance
POST   /api/v1/employees/attendance/check-in
POST   /api/v1/employees/attendance/check-out
GET    /api/v1/employees/holidays
GET    /api/v1/employees/permissions
```

#### 4. الطلبات | Requests

```
GET    /api/v1/requests
POST   /api/v1/requests
GET    /api/v1/requests/{id}
PUT    /api/v1/requests/{id}
DELETE /api/v1/requests/{id}
GET    /api/v1/requests/{id}/responses
POST   /api/v1/requests/{id}/responses
GET    /api/v1/requests/{id}/attachments
POST   /api/v1/requests/{id}/attachments
```

#### 5. التراخيص | Licenses

```
GET    /api/v1/craft-licenses
GET    /api/v1/craft-licenses/{id}
GET    /api/v1/craft-licenses/{id}/payments
POST   /api/v1/craft-licenses/{id}/payments
GET    /api/v1/engineering
GET    /api/v1/engineering/{id}
GET    /api/v1/engineering/{id}/payments
```

#### 6. المدفوعات | Payments

```
GET    /api/v1/payments
POST   /api/v1/payments
GET    /api/v1/payments/{id}
POST   /api/v1/payments/{id}/cancel
GET    /api/v1/payments/{id}/receipt
GET    /api/v1/revenue-batches
```

#### 7. الإشعارات | Notifications

```
GET    /api/v1/notifications
GET    /api/v1/notifications/{id}
PUT    /api/v1/notifications/{id}/read
PUT    /api/v1/notifications/mark-all-read
```

#### 8. التقارير | Reports

```
GET    /api/v1/reports/employees
GET    /api/v1/reports/payments
GET    /api/v1/reports/requests
GET    /api/v1/reports/revenue
```

### تنسيق الاستجابة | Response Format

#### النجاح | Success
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "message": "Operation completed successfully"
}
```

#### الخطأ | Error
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "details": {
      "email": ["The email field is required."]
    }
  }
}
```

#### التصفح | Pagination
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Item 1"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

### المصادقة | Authentication Implementation

#### Controller Example
```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('mobile')->accessToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => $user,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'INVALID_CREDENTIALS',
                'message' => 'Invalid email or password',
            ],
        ], 401);
    }
}
```

### Middleware

#### API Version Middleware
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiVersionMiddleware
{
    public function handle(Request $request, Closure $next, $version)
    {
        $request->attributes->set('api_version', $version);
        return $next($request);
    }
}
```

#### API Log Middleware
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        Log::info('API Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
            'status' => $response->status(),
        ]);

        return $response;
    }
}
```

### Routes Configuration

```php
// routes/api.php
Route::prefix('v1')->group(function () {
    // Public routes
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    });

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me', [AuthController::class, 'me']);
        });

        Route::apiResource('requests', RequestController::class);
        Route::apiResource('craft-licenses', CraftLicenseController::class);
        Route::apiResource('payments', PaymentController::class);
        Route::apiResource('notifications', NotificationController::class);
    });
});
```

### Swagger Documentation

#### Generate Documentation
```bash
php artisan l5-swagger:generate
```

#### Access Documentation
```
http://your-domain.com/api/documentation
```

### Rate Limiting

```php
// config/rate_limiting.php (already created)
'api' => [
    'default' => '60,1',
    'strict' => '30,1',
    'very_strict' => '10,1',
],
```

```php
// routes/api.php
Route::middleware('auth:api', 'throttle:60,1')->group(function () {
    // API routes
});
```

### خطوات التنفيذ | Implementation Steps

#### المرحلة 1: الإعداد (أسبوع 1)
1. تثبيت Laravel Passport
2. تثبيت L5-Swagger
3. إعداد قاعدة البيانات
4. إنشاء OAuth clients

#### المرحلة 2: المصادقة (أسبوع 2)
1. إنشاء Auth endpoints
2. إضافة Swagger annotations
3. اختبار المصادقة
4. إضافة refresh tokens

#### المرحلة 3: الـ APIs الأساسية (أسبوع 3-4)
1. إنشاء Resource endpoints
2. إضافة Swagger documentation
3. اختبار الـ endpoints
4. إضافة error handling

#### المرحلة 4: الـ APIs المتقدمة (أسبوع 5-6)
1. إنشاء Reports endpoints
2. إضافة filtering و sorting
3. إضافة caching
4. تحسين الأداء

#### المرحلة 5: الاختبار والإطلاق (أسبوع 7-8)
1. اختبار شامل
2. اختبار الأمان
3. اختبار الأداء
4. إطلاق Beta
5. جمع الملاحظات
6. الإطلاق الرسمي

### الأمان | Security

1. **HTTPS**: إجبار HTTPS في الإنتاج
2. **CORS**: إعدادات CORS صارمة
3. **Rate Limiting**: حماية من DDoS
4. **Input Validation**: التحقق من جميع المدخلات
5. **SQL Injection**: استخدام Eloquent ORM
6. **XSS**: Escaping all outputs
7. **CSRF**: CSRF tokens للـ stateful operations

### التوثيق | Documentation

1. **Swagger UI**: واجهة تفاعلية للتوثيق
2. **Postman Collection**: مجموعة Postman جاهزة
3. **README.md**: وثائق الاستخدام
4. **Examples**: أمثلة على الاستخدام
5. **Changelog**: سجل التغييرات

### الخاتمة | Conclusion

REST API للموبايل مع Swagger سيسهل على المطورين التكامل مع نظام بلدية جباليا وسيوفر واجهة موحدة وآمنة لتطبيقات الطرف الثالث.
