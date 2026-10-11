# Module Routes Guide

**Version:** 2.0  
**Date:** April 20, 2026  
**Status:** IMPLEMENTED

---

## Overview

Each module in the Jabalia system has its own route files that are automatically loaded by the `ModuleServiceProvider`. This guide explains the routing structure and how to work with module routes.

---

## Route Structure

### Automatic Route Loading

Routes are automatically loaded from:
- `app/Modules/{Module}/routes/web.php` - Web routes
- `app/Modules/{Module}/routes/api.php` - API routes

### Route Prefixes

All module routes are automatically prefixed with the module name:
- Requests: `/requests/*`
- Craft Licenses: `/craftlicenses/*`
- Engineering: `/engineering/*`
- Employees: `/employees/*`

### Route Namespacing

Routes are automatically namespaced with the module name:
- Requests: `requests.*`
- Craft Licenses: `craftlicenses.*`
- Engineering: `engineering.*`
- Employees: `employees.*`

---

## Available Routes

### Requests Module

#### Web Routes
| Method | URI | Name | Permission | Description |
|--------|-----|------|------------|-------------|
| GET | `/requests` | `requests.index` | `view requests` | List all requests |
| GET | `/requests/{request}` | `requests.show` | `view requests` | Show request details |
| GET | `/requests/create` | `requests.create` | `create requests` | Create new request form |
| POST | `/requests` | `requests.store` | `create requests` | Store new request |
| GET | `/requests/{request}/edit` | `requests.edit` | `update requests` | Edit request form |
| PUT | `/requests/{request}` | `requests.update` | `update requests` | Update request |
| DELETE | `/requests/{request}` | `requests.destroy` | `delete requests` | Delete request |
| POST | `/requests/{request}/status` | `requests.status.update` | `update requests` | Update request status |
| POST | `/requests/{request}/attachment` | `requests.attachment.add` | `update requests` | Add attachment |

#### API Routes
| Method | URI | Name | Permission | Description |
|--------|-----|------|------------|-------------|
| GET | `/requests/search` | `requests.search` | `view requests` | Search requests |

---

### Craft Licenses Module

#### Web Routes
| Method | URI | Name | Permission | Description |
|--------|-----|------|------------|-------------|
| GET | `/craftlicenses` | `craftlicenses.index` | `view craft licenses` | List all licenses |
| GET | `/craftlicenses/{license}` | `craftlicenses.show` | `view craft licenses` | Show license details |
| GET | `/craftlicenses/create` | `craftlicenses.create` | `create craft licenses` | Create new license form |
| POST | `/craftlicenses` | `craftlicenses.store` | `create craft licenses` | Store new license |
| GET | `/craftlicenses/{license}/edit` | `craftlicenses.edit` | `update craft licenses` | Edit license form |
| PUT | `/craftlicenses/{license}` | `craftlicenses.update` | `update craft licenses` | Update license |
| DELETE | `/craftlicenses/{license}` | `craftlicenses.destroy` | `delete craft licenses` | Delete license |
| POST | `/craftlicenses/{license}/renew` | `craftlicenses.renew` | `update craft licenses` | Renew license |
| POST | `/craftlicenses/{license}/suspend` | `craftlicenses.suspend` | `update craft licenses` | Suspend license |
| POST | `/craftlicenses/{license}/reactivate` | `craftlicenses.reactivate` | `update craft licenses` | Reactivate license |

#### Special Routes
| Method | URI | Name | Permission | Description |
|--------|-----|------|------------|-------------|
| GET | `/craftlicenses/expiring` | `craftlicenses.expiring` | `view craft licenses` | Get expiring licenses |
| GET | `/craftlicenses/outstanding-payments` | `craftlicenses.outstanding.payments` | `view craft licenses` | Get outstanding payments |

#### API Routes
| Method | URI | Name | Permission | Description |
|--------|-----|------|------------|-------------|
| GET | `/craftlicenses/search` | `craftlicenses.search` | `view craft licenses` | Search licenses |

---

### Engineering Module

#### Web Routes
| Method | URI | Name | Permission | Description |
|--------|-----|------|------------|-------------|
| GET | `/engineering` | `engineering.index` | `view engineering` | List all files |
| GET | `/engineering/{file}` | `engineering.show` | `view engineering` | Show file details |
| GET | `/engineering/create` | `engineering.create` | `create engineering` | Create new file form |
| POST | `/engineering` | `engineering.store` | `create engineering` | Store new file |
| GET | `/engineering/{file}/edit` | `engineering.edit` | `update engineering` | Edit file form |
| PUT | `/engineering/{file}` | `engineering.update` | `update engineering` | Update file |
| DELETE | `/engineering/{file}` | `engineering.destroy` | `delete engineering` | Delete file |

#### Workflow Routes
| Method | URI | Name | Permission | Description |
|--------|-----|------|------------|-------------|
| POST | `/engineering/{file}/approve` | `engineering.approve` | `update engineering` | Approve file |
| POST | `/engineering/{file}/reject` | `engineering.reject` | `update engineering` | Reject file |
| POST | `/engineering/{file}/complete` | `engineering.complete` | `update engineering` | Complete file |

#### Special Routes
| Method | URI | Name | Permission | Description |
|--------|-----|------|------------|-------------|
| GET | `/engineering/neighborhood/{neighborhood}` | `engineering.neighborhood` | `view engineering` | Get files by neighborhood |
| GET | `/engineering/outstanding-payments` | `engineering.outstanding.payments` | `view engineering` | Get outstanding payments |

#### API Routes
| Method | URI | Name | Permission | Description |
|--------|-----|------|------------|-------------|
| GET | `/engineering/search` | `engineering.search` | `view engineering` | Search files |

---

### Employees Module

#### Web Routes
| Method | URI | Name | Permission | Description |
|--------|-----|------|------------|-------------|
| GET | `/employees` | `employees.index` | `view employees` | List all employees |
| GET | `/employees/{employee}` | `employees.show` | `view employees` | Show employee details |
| GET | `/employees/create` | `employees.create` | `create employees` | Create new employee form |
| POST | `/employees` | `employees.store` | `create employees` | Store new employee |
| GET | `/employees/{employee}/edit` | `employees.edit` | `update employees` | Edit employee form |
| PUT | `/employees/{employee}` | `employees.update` | `update employees` | Update employee |
| DELETE | `/employees/{employee}` | `employees.destroy` | `delete employees` | Delete employee |

#### Management Routes
| Method | URI | Name | Permission | Description |
|--------|-----|------|------------|-------------|
| POST | `/employees/{employee}/status` | `employees.status.update` | `update employees` | Update employee status |
| POST | `/employees/bulk-update-status` | `employees.bulk.status.update` | `update employees` | Bulk update status |

#### Special Routes
| Method | URI | Name | Permission | Description |
|--------|-----|------|------------|-------------|
| GET | `/employees/department/{departmentId}` | `employees.department` | `view employees` | Get by department |
| GET | `/employees/section/{sectionId}` | `employees.section` | `view employees` | Get by section |
| GET | `/employees/active` | `employees.active` | `view employees` | Get active employees |
| GET | `/employees/on-leave` | `employees.on.leave` | `view employees` | Get employees on leave |
| GET | `/employees/upcoming-holidays` | `employees.upcoming.holidays` | `view employees` | Get upcoming holidays |
| GET | `/employees/expiring-documents` | `employees.expiring.documents` | `view employees` | Get expiring documents |

#### API Routes
| Method | URI | Name | Permission | Description |
|--------|-----|------|------------|-------------|
| GET | `/employees/search` | `employees.search` | `view employees` | Search employees |

---

## Route Examples

### Using Named Routes

```php
// In Blade templates
<a href="{{ route('requests.index') }}">All Requests</a>
<a href="{{ route('craftlicenses.create') }}">New License</a>

// In Controllers
return redirect()->route('engineering.show', $file->id);
return redirect()->route('employees.index');
```

### URL Generation

```php
// Generate URLs
$url = route('requests.edit', $request->id);
$url = route('craftlicenses.show', $license->id);

// With parameters
$url = route('engineering.neighborhood', 'Al-Nasr');
$url = route('employees.department', $departmentId);
```

### Form Actions

```php
// Form submissions
<form action="{{ route('requests.store') }}" method="POST">
<form action="{{ route('craftlicenses.update', $license->id) }}" method="PUT">
<form action="{{ route('engineering.destroy', $file->id) }}" method="POST">
```

---

## Permission-Based Routing

### Middleware Protection

All routes are protected by appropriate permissions:

```php
Route::middleware(['auth', 'permission:view requests'])->group(function () {
    Route::get('/', [RequestController::class, 'index']);
    Route::get('/{request}', [RequestController::class, 'show']);
});
```

### Permission Matrix

| Permission | Routes Protected |
|------------|------------------|
| `view requests` | GET routes for requests |
| `create requests` | POST /create routes for requests |
| `update requests` | PUT /edit routes for requests |
| `delete requests` | DELETE routes for requests |
| `view craft licenses` | GET routes for craft licenses |
| `create craft licenses` | POST /create routes for craft licenses |
| `update craft licenses` | PUT /edit routes for craft licenses |
| `delete craft licenses` | DELETE routes for craft licenses |
| `view engineering` | GET routes for engineering |
| `create engineering` | POST /create routes for engineering |
| `update engineering` | PUT /edit routes for engineering |
| `delete engineering` | DELETE routes for engineering |
| `view employees` | GET routes for employees |
| `create employees` | POST /create routes for employees |
| `update employees` | PUT /edit routes for employees |
| `delete employees` | DELETE routes for employees |

---

## Custom Route Groups

### Additional Middleware

You can add additional middleware to route groups:

```php
Route::middleware(['auth', 'permission:view requests', 'throttle:60,1'])->group(function () {
    Route::get('/', [RequestController::class, 'index']);
});
```

### Sub-grouping

Organize related routes in sub-groups:

```php
Route::middleware(['auth', 'permission:update requests'])->group(function () {
    Route::get('/{request}/edit', [RequestController::class, 'edit']);
    Route::put('/{request}', [RequestController::class, 'update']);
    
    Route::prefix('/{request}')->group(function () {
        Route::post('/status', [RequestController::class, 'updateStatus']);
        Route::post('/attachment', [RequestController::class, 'addAttachment']);
    });
});
```

---

## API Routes

### API Authentication

API routes use the same permission system but can be configured for API tokens:

```php
Route::middleware(['auth:sanctum', 'permission:view requests'])->group(function () {
    Route::get('/search', [RequestController::class, 'search']);
});
```

### Response Format

API routes return JSON responses:

```php
// Successful response
return response()->json($data);

// Error response
return response()->json([
    'success' => false,
    'message' => $errorMessage
], 422);
```

---

## Route Caching

### Route Caching

After deploying to production, cache the routes:

```bash
php artisan route:cache
```

### Clearing Cache

Clear route cache when making changes:

```bash
php artisan route:clear
```

---

## Testing Routes

### Route Testing

Test routes using Laravel's route testing:

```php
// Test route exists
$this->assertTrue(Route::has('requests.index'));
$this->assertTrue(Route::has('craftlicenses.create'));

// Test route URL
$url = route('engineering.show', 1);
$this->assertEquals('/engineering/1', $url);

// Test route name
$name = Route::currentRouteName();
$this->assertEquals('employees.index', $name);
```

### Permission Testing

Test route permissions:

```php
// Test user can access route
$user->givePermissionTo('view requests');
$this->actingAs($user)->get('/requests')->assertOk();

// Test user cannot access route
$this->actingAs($user)->post('/requests')->assertForbidden();
```

---

## Best Practices

### 1. Use Named Routes

Always use named routes for maintainability:
```php
// Good
return redirect()->route('requests.index');

// Avoid
return redirect('/requests');
```

### 2. Group Related Routes

Organize routes logically:
```php
Route::prefix('/{file}')->group(function () {
    Route::post('/approve', [Controller::class, 'approve']);
    Route::post('/reject', [Controller::class, 'reject']);
    Route::post('/complete', [Controller::class, 'complete']);
});
```

### 3. Use Resource Routes

For standard CRUD operations, consider resource routes:
```php
Route::resource('requests', RequestController::class);
```

### 4. Validate Permissions

Always protect routes with appropriate permissions:
```php
Route::middleware(['auth', 'permission:view requests'])->group(function () {
    // Protected routes
});
```

### 5. Document Routes

Document complex routes and their purposes:
```php
// Get expiring licenses (within 30 days)
Route::get('/expiring', [Controller::class, 'expiring']);
```

---

## Troubleshooting

### Common Issues

#### Route Not Found
```
Route [requests.index] not defined
```
**Solution:** Check if route file exists in correct module directory

#### Permission Denied
```
This action is unauthorized
```
**Solution:** Check if user has required permission

#### Method Not Allowed
```
Method Not Allowed
```
**Solution:** Check HTTP method matches route definition

#### Model Not Found
```
No query results for model [Request]
```
**Solution:** Check route parameter name matches model binding

### Debugging Tips

1. **List all routes:** `php artisan route:list`
2. **Check specific module:** `php artisan route:list --path=requests`
3. **Test route existence:** `php artisan route:list --name=requests.index`
4. **Verify permission:** Check user permissions in database

---

## Future Enhancements

### Planned Improvements

1. **API Versioning** - Version-specific API routes
2. **Rate Limiting** - Per-endpoint rate limiting
3. **Route Caching** - Advanced caching strategies
4. **Route Groups** - More granular route organization
5. **Custom Middleware** - Module-specific middleware

### Extension Points

- **Custom route patterns** - Module-specific route patterns
- **Dynamic routes** - Database-driven routing
- **Conditional routes** - Environment-specific routes
- **Route macros** - Custom route helpers

---

## Conclusion

The modular routing system provides:

- **Clear organization** - Routes grouped by module
- **Automatic loading** - No manual route registration
- **Permission protection** - Built-in security
- **Consistent naming** - Predictable route names
- **Easy testing** - Standard route testing patterns

This routing system supports the modular architecture and makes the application more maintainable and secure.

---

*For questions or support, refer to the development team or check the system documentation.*
