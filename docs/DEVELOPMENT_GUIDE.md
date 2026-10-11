# Jabalia Development Guide

**Version:** 2.0  
**Date:** April 20, 2026  
**Status:** IMPLEMENTED

---

## Overview

This guide provides comprehensive instructions for developing with the modular Jabalia system. It covers development workflows, best practices, and common patterns.

---

## Development Environment Setup

### Prerequisites

- **PHP 8.1+**
- **Laravel 10**
- **MySQL 5.7+**
- **Node.js 16+**
- **Composer**
- **Git**

### Development Tools

- **IDE:** VS Code, PhpStorm, or equivalent
- **Browser:** Chrome/Firefox with developer tools
- **Terminal:** PowerShell or Git Bash
- **API Client:** Postman or similar

---

## Development Workflow

### 1. Getting Started

#### Clone Repository
```bash
git clone <repository-url>
cd jabalia
```

#### Install Dependencies
```bash
composer install
npm install
npm run build
```

#### Environment Configuration
```bash
cp .env.example .env
# Edit .env with your database credentials
```

#### Database Setup
```bash
php artisan migrate
php artisan db:seed
```

#### Start Development Server
```bash
php artisan serve
```

### 2. Development Cycle

#### Create Feature
```bash
# Create new module
php artisan make:module NewModule

# Create controller
php artisan make:controller NewModuleController

# Create service
php artisan make:service NewModuleService
```

#### Test Feature
```bash
# Run feature tests
php artisan test --filter=NewModule

# Run specific test
php artisan test --filter=NewModuleTest::test_method
```

#### Deploy Changes
```bash
# Run migrations
php artisan migrate

# Clear caches
php artisan config:cache
php artisan view:cache
php artisan route:cache
```

---

## Module Development

### Creating a New Module

#### 1. Create Module Structure

```bash
# Create module directories
mkdir -p app/Modules/NewModule/{Controllers,Services,Policies,Models,Views}
```

#### 2. Create Base Classes

**Controller:**
```php
<?php
namespace App\Modules\NewModule\Controllers;

use App\Modules\Shared\Controllers\BaseModuleController;

class NewModuleController extends BaseModuleController
{
    public function index()
    {
        $data = $this->getService()->getAll();
        return $this->indexResponse(['items' => $data]);
    }
}
```

**Service:**
```php
<?php
namespace App\Modules\NewModule\Services;

use App\Modules\Shared\Services\BaseModuleService;

class NewModuleService extends BaseModuleService
{
    protected function initializeModel(): void
    {
        $this->modelClass = \App\Models\NewModel::class;
    }
    
    protected function getValidationRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ];
    }
}
```

#### 3. Register Service

Add to `app/Providers/ModuleServiceProvider.php`:
```php
// In registerModuleServices()
$this->app->singleton('App\Modules\NewModule\Services\NewModuleService', function ($app) {
    return new \App\Modules\NewModule\Services\NewModuleService();
});
```

#### 4. Create Routes

Create `app/Modules/NewModule/routes/web.php`:
```php
<?php

use App\Modules\NewModule\Controllers\NewModuleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:view newmodule'])->group(function () {
    Route::get('/', [NewModuleController::class, 'index'])->name('newmodule.index');
    Route::get('/{item}', [NewModuleController::class, 'show'])->name('newmodule.show');
});
```

#### 5. Create Views

Create `app/Modules/NewModule/Views/index.blade.php`:
```php
@extends('layouts.app')

@section('title')
{{ __('New Module') }}

@section('content')
<div class="container-fluid">
    <h1>{{ __('New Module') }}</h1>
    <!-- View content -->
</div>
```

---

### 2. Adding Features

#### Adding New Controller Method

```php
public function search(Request $request)
{
    try {
        $term = $request->get('q', '');
        $filters = $request->only(['status', 'priority']);
        
        $results = $this->getService()->search($term, $filters);
        
        return response()->json($results);
    }
```

#### Adding New Service Method

```php
public function searchLicenses(string $term, array $filters): Collection
{
    $query = $this->modelClass::query();
    
    // Apply search
    $query->where(function ($q) use ($term) {
        $q->where('name', 'like', "%{$term}%")
          ->orWhere('description', 'like', "%{$term}%");
    });
    
    // Apply filters
    foreach ($filters as $key => $value) {
        if ($value !== null) {
            $query->where($key, $value);
        }
    }
    
    return $query->limit(50)->get();
}
```

#### Adding New View Component

```php
// app/Modules/Shared/Views/components/status-badge.blade.php
@component('status-badge', ['status' => $status])
    <span class="badge bg-{{ getStatusColor($status) }}">
        {{ getStatusName($status) }}
    </span>
@endcomponent
```

---

## Testing Strategies

### Unit Testing

#### Testing Services

```php
// tests/Unit/Requests/RequestServiceTest.php
class RequestServiceTest extends TestCase
{
    public function test_can_create_request()
    {
        $data = [
            'text' => 'Test request',
            'request_type_id' => 1,
            'status' => 'pending',
        ];
        
        $request = $this->service->create($data);
        
        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals('Test request', $request->text);
    }
}
```

#### Testing Controllers

```php
// tests/Feature/Requests/RequestTest.php
class RequestTest extends TestCase
{
    public function test_can_view_requests_index()
    {
        $response = $this->get('/requests');
        $response->assertOk();
        $response->assertSee('Requests Management');
    }
}
```

#### Testing Views

```php
// tests/Feature/Requests/RequestViewTest.php
class RequestViewTest extends TestCase
{
    public function test_index_page_displays_requests()
    {
        $user = User::factory()->create();
        $request = Request::factory()->create();
        
        $response = $this->actingAs($user)
            ->get('/requests');
            
        $response->assertOk();
        $response->assertViewIs('requests::index');
    }
}
```

### Integration Testing

#### Testing Module Integration

```php
// tests/Feature/ModuleIntegrationTest.php
class ModuleIntegrationTest extends TestCase
{
    public function test_requests_module_integration()
    {
        // Test service registration
        $service = app('App\Modules\Requests\Services\RequestService');
        $this->assertInstanceOf(RequestService::class, $service);
        
        // Test route loading
        $route = Route::get('requests.index');
        $this->assertNotNull($route);
        
        // Test view loading
        $view = view('requests::index');
        $this->assertNotNull($view);
    }
}
```

---

## Code Quality Standards

### 1. PHP Standards

#### PSR-12 Coding Standards

```php
<?php
class RequestService extends BaseModuleService
{
    /**
     * Create a new request
     *
     * @param array $data
     * @return Request
     */
    public function create(array $data): Request
    {
        // Validate data
        $data = $this->validate($data);
        
        // Create request
        return parent::create($data);
    }
}
```

#### Naming Conventions

- **Classes:** PascalCase (PascalCase)
- **Methods:** camelCase (camelCase)
- **Variables:** snake_case (snake_case)
- **Constants:** UPPER_SNAKE_CASE
- **Files:** kebab-case

### 2. Laravel Best Practices

#### Service Layer

```php
// Keep controllers thin
class RequestController extends BaseModuleController
{
    public function index(Request $request)
    {
        $data = $this->getService()->getAll($request->all());
        return $this->indexResponse($data);
    }
}

// Keep services focused
class RequestService extends BaseModuleService
{
    public function getAll(array $filters = []): Collection
    {
        return $this->modelClass::when($filters)
            ->latest()
            ->paginate(15);
    }
}
```

#### Model Relationships

```php
// Use proper relationships
class Request extends Model
{
    public function mokalaf()
    {
        return $this->belongsTo(Mokalaf::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
```

### 3. Security Practices

#### Permission Checks

```php
// Always check permissions
public function edit($id)
{
    $request = $this->getService()->findById($id);
    
    $this->authorize('update', $request);
    
    return $this->editResponse(['request' => $request]);
}
```

#### Input Validation

```php
// Validate all inputs
$data = $request->validate([
    'title' => 'required|string|max:255',
    'status' => 'required|in:pending,approved,rejected',
]);
```

---

## Performance Optimization

### 1. View Caching

```php
@if (Cache::has('dashboard_stats'))
    <!-- Cached content -->
    {{ Cache::get('dashboard_stats') }}
@else
    <!-- Fresh content -->
@endif
```

### 2. Lazy Loading

```php
@if ($items->hasPages())
    <!-- Table with pagination -->
    <table class="table">
        <!-- Table content -->
    </table>
    {{ $items->links() }}
@else
    <!-- Empty state -->
@endif
```

### 3. Asset Optimization

```php
{{-- Use Vite for CSS/JS --}}
<link href="{{ Vite::asset('css/app.css') }}" rel="stylesheet">
<script src="{{ Vite::asset('js/app.js') }}"></script>
```

---

## Debugging

### 1. Common Issues

#### View Not Found
```
View [requests::index] not found
```
**Solutions:**
1. Check file exists: `app/Modules/Requests/Views/index.blade.php`
2. Check namespace: Ensure view uses correct module prefix
3. Check service registration: Ensure service is registered
4. Check route definition: Ensure route points to correct view

#### Component Not Found
```
@component('non-existent', [])
```
**Solutions:**
1. Create component file in correct location
2. Ensure component is registered in service provider
3. Check component name spelling
4. Check component usage in view

#### Permission Denied
```
This action is unauthorized
```
**Solutions:**
1. Check user permissions in database
2. Ensure middleware is applied correctly
3. Check policy implementation
4. Verify role assignments

### 2. Debugging Tools

#### Laravel Debug
```bash
php artisan tinker
```

#### Log Analysis
```bash
php artisan log:clear
php artisan log:tail
```

#### Route Debug
```bash
php artisan route:list
php artisan route:list --path=requests
```

---

## Deployment Considerations

### 1. Production Deployment

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### 2. Asset Compilation

```bash
# Build for production
npm run build --production
```

### 3. Database Migration

```bash
# Run migrations
php artisan migrate --force
```

---

## Security Considerations

### 1. Input Validation

```php
// Always validate user input
$data = $request->validate($rules);
```

### 2. Permission Checks

```php
// Always authorize actions
$this->authorize('update', $request);
```

### 3. Output Escaping

```php
{{ $data->name }}
{{ Str::limit($text, 100) }}
```

---

## Development Tools

### 1. IDE Configuration

#### VS Code
```json
{
    "php.executablePath": "C:\\php\\php.exe",
    "terminal.integrated": true,
    "files.associations": ["*.php", "*.blade.php"],
    "files.exclude": [
        "vendor/**/*",
        "node_modules/*",
        "storage/framework/cache/*",
        "storage/logs/*"
    ]
}
```

#### PhpStorm Configuration
```json
{
    "php.suggest.basic": false,
    "php.suggest.phpstan": true,
    "php.suggest.phpstan.rules.strict": true,
    "php.suggest.phpstan.risky": true
}
```

### 2. Browser DevTools
- Chrome DevTools for debugging JavaScript
- Network tab for API testing
- Console for JavaScript debugging
- Elements panel for CSS inspection

### 3. API Testing
- Postman for API testing
- Insomnia for alternative API testing
- Postman Collections for test organization

---

## Best Practices Summary

### Do's
- Follow PSR-12 coding standards
- Use Laravel best practices
- Write comprehensive tests
- Document your code
- Use type hints
- Follow naming conventions
- Implement proper error handling
- Use dependency injection

### Don'ts
- Hardcode configuration values
- Skip input validation
- Ignore security checks
- Use magic numbers or strings
- Skip error handling
- Ignore documentation

---

## Common Patterns

### 1. Repository Pattern

```php
interface RequestRepositoryInterface
{
    public function findById(int $id): ?Request;
    public function findAll(): Collection;
    public function create(array $data): Request;
    public function update(int $id, array $data): Request;
    public function delete(int $id): bool;
}
```

### 2. Factory Pattern

```php
class RequestFactory
{
    public static function create(array $attributes = []): Request
    {
        return Request::create($attributes);
    }
}
```

### 3. Observer Pattern

```php
class RequestObserver
{
    public function created(Request $request)
    {
        Log::info("Request created: " . $request->id);
    }
}
```

---

## Future Enhancements

### Planned Features

1. **API Resources**
2. **Email Templates**
3. **PDF Generation**
4. **Mobile Apps**
5. **Admin Panel**

### Extension Points

1. **Custom Components**
2. **View Macros**
3. **View Composers**
4. **Service Extensions**
5. **Middleware**

---

## Conclusion

The modular architecture provides:

- **Clear Organization:** Code organized by module
- **Easy Maintenance:** Isolated modules are easier to maintain
- **Scalable:** New modules can be added independently
- **Testable:** Each module can be tested independently
- **Secure:** Centralized permission and security

---

*For questions or support, refer to the development team or check the system documentation.*
