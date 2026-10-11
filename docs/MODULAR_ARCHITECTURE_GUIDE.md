# Jabalia Modular Architecture Guide

**Version:** 2.0  
**Date:** April 20, 2026  
**Status:** IMPLEMENTED

---

## Overview

The Jabalia Municipality Management System has been restructured using a modular architecture pattern to improve maintainability, scalability, and code organization. This guide explains the new structure and how to work with it.

---

## Architecture Overview

### Modular Structure

The system is now organized into self-contained modules, each with its own:

- **Controllers** - Handle HTTP requests and responses
- **Services** - Business logic and data operations
- **Policies** - Authorization rules
- **Models** - Database models (shared with main app)
- **Views** - Blade templates specific to the module

### Directory Structure

```
app/
Modules/
|
|-- Requests/                    # Request Management Module
|   |-- Controllers/
|   |   `-- RequestController.php
|   |-- Services/
|   |   `-- RequestService.php
|   |-- Policies/
|   |-- Models/
|   `-- Views/
|
|-- CraftLicenses/              # Craft Licenses Module
|   |-- Controllers/
|   |   `-- CraftLicenseController.php
|   |-- Services/
|   |   `-- CraftLicenseService.php
|   |-- Policies/
|   |-- Models/
|   `-- Views/
|
|-- Engineering/                # Engineering Module
|   |-- Controllers/
|   |   `-- EngineeringController.php
|   |-- Services/
|   |   `-- EngineeringService.php
|   |-- Policies/
|   |-- Models/
|   `-- Views/
|
|-- Employees/                  # Employee Management Module
|   |-- Controllers/
|   |   `-- EmployeeController.php
|   |-- Services/
|   |   `-- EmployeeService.php
|   |-- Policies/
|   |-- Models/
|   `-- Views/
|
`-- Shared/                     # Shared Components
    |-- Controllers/
    |   `-- BaseModuleController.php
    |-- Services/
    |   `-- BaseModuleService.php
    |-- Traits/
    |   |-- HasPermissions.php
    |   |-- HasAuditTrail.php
    |   `-- HasStatusTransitions.php
    `-- Middleware/
```

---

## Core Components

### BaseModuleController

All module controllers extend `BaseModuleController` which provides:

- **Standard CRUD operations** with consistent response handling
- **Permission checking** using module-specific permissions
- **Common response methods** for success/error handling
- **Module name detection** for automatic routing

```php
class RequestController extends BaseModuleController
{
    public function index(Request $request)
    {
        $data = $this->getService()->getAll($filters);
        return $this->indexResponse(['requests' => $data]);
    }
}
```

### BaseModuleService

All module services extend `BaseModuleService` which provides:

- **Standard CRUD operations** with validation
- **Search and filtering** capabilities
- **Bulk operations** support
- **Statistics generation**
- **Data export** functionality

```php
class RequestService extends BaseModuleService
{
    protected function getValidationRules(): array
    {
        return ['title' => 'required', 'status' => 'required'];
    }
}
```

### Shared Traits

#### HasPermissions
Provides unified permission management across all modules:

```php
class Request extends Model
{
    use HasPermissions;
    
    public function canEdit(): bool
    {
        return $this->canPerformAction('update');
    }
}
```

#### HasAuditTrail
Automatic audit logging for all model changes:

```php
class Request extends Model
{
    use HasAuditTrail;
    
    // Automatically logs create, update, delete operations
}
```

#### HasStatusTransitions
Status workflow management with validation:

```php
class Request extends Model
{
    use HasStatusTransitions;
    
    protected function getAllowedStatusTransitions(): array
    {
        return [
            'draft' => ['pending', 'cancelled'],
            'pending' => ['approved', 'rejected'],
            'approved' => ['completed'],
        ];
    }
}
```

---

## ModuleServiceProvider

The `ModuleServiceProvider` manages:

- **Service registration** for dependency injection
- **Route loading** from module-specific route files
- **View loading** from module view directories
- **Migration loading** from module migration directories

### Service Registration

```php
$this->app->singleton('App\Modules\Requests\Services\RequestService', function ($app) {
    return new \App\Modules\Requests\Services\RequestService();
});
```

### Route Loading

Routes are automatically loaded from:
- `app/Modules/{Module}/routes/web.php`
- `app/Modules/{Module}/routes/api.php`

### View Loading

Views are automatically loaded with namespace:
- `requests::index` maps to `app/Modules/Requests/Views/index.blade.php`
- `craftlicenses::show` maps to `app/Modules/CraftLicenses/Views/show.blade.php`

---

## Working with Modules

### Creating a New Module

1. **Create module directory structure**
```bash
mkdir -p app/Modules/NewModule/{Controllers,Services,Policies,Models,Views}
```

2. **Create controller extending BaseModuleController**
```php
namespace App\Modules\NewModule\Controllers;

use App\Modules\Shared\Controllers\BaseModuleController;

class NewModuleController extends BaseModuleController
{
    // Implement your methods
}
```

3. **Create service extending BaseModuleService**
```php
namespace App\Modules\NewModule\Services;

use App\Modules\Shared\Services\BaseModuleService;

class NewModuleService extends BaseModuleService
{
    protected function initializeModel(): void
    {
        $this->modelClass = \App\Models\NewModel::class;
    }
}
```

4. **Register service in ModuleServiceProvider**
```php
$this->app->singleton('App\Modules\NewModule\Services\NewModuleService', function ($app) {
    return new \App\Modules\NewModule\Services\NewModuleService();
});
```

### Using Module Services

Services are automatically available through dependency injection:

```php
class MyController extends Controller
{
    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }
}
```

Or resolve manually:

```php
$requestService = app('App\Modules\Requests\Services\RequestService');
```

### Module Views

Use module-specific view namespaces:

```php
// In controller
return view('requests::index', $data);

// In Blade template
@include('requests::partials.form')
```

### Module Routes

Routes are automatically loaded with module prefix:

```php
// app/Modules/Requests/routes/web.php
Route::get('/', [RequestController::class, 'index']);
// Becomes: /requests/

Route::get('/{id}', [RequestController::class, 'show']);
// Becomes: /requests/{id}
```

---

## Best Practices

### 1. Service Layer

- **Keep controllers thin** - Move business logic to services
- **Use dependency injection** - Let Laravel handle service resolution
- **Implement validation** - Use service validation rules
- **Handle exceptions** - Provide meaningful error messages

### 2. Permission Management

- **Use HasPermissions trait** - Consistent permission checking
- **Define module permissions** - Create specific permissions for each module
- **Check permissions in controllers** - Use authorize() method
- **Implement view restrictions** - Filter data based on user permissions

### 3. Audit Trail

- **Use HasAuditTrail trait** - Automatic logging
- **Exclude sensitive fields** - Don't log passwords, tokens
- **Customize metadata** - Add context to audit entries
- **Monitor audit logs** - Regular review of system changes

### 4. Status Management

- **Use HasStatusTransitions trait** - Validated status changes
- **Define allowed transitions** - Prevent invalid status changes
- **Implement transition actions** - Handle status-specific logic
- **Log status changes** - Track status history

### 5. Code Organization

- **Follow naming conventions** - Consistent class and method names
- **Use shared components** - Leverage base classes and traits
- **Keep modules focused** - Single responsibility per module
- **Document module interfaces** - Clear API documentation

---

## Migration Strategy

### From Legacy to Modular

1. **Identify related functionality** - Group related controllers and models
2. **Create module structure** - Set up directories and base classes
3. **Move controllers** - Extend BaseModuleController
4. **Create services** - Move business logic from controllers
5. **Update routes** - Use module-specific route files
6. **Test functionality** - Ensure everything works as expected

### Gradual Migration

- **Start with new modules** - Use modular structure for new features
- **Migrate existing modules** - Convert one module at a time
- **Maintain compatibility** - Keep old routes working during transition
- **Update documentation** - Reflect new structure in docs

---

## Performance Considerations

### Service Registration

- **Use singletons for stateless services** - Improve performance
- **Lazy load heavy services** - Register when needed
- **Avoid circular dependencies** - Keep service graph simple

### View Loading

- **Use view caching** - Cache compiled views
- **Optimize view includes** - Minimize nested includes
- **Leverage view composers** - Share data efficiently

### Database Operations

- **Use repository pattern** - Abstract data access
- **Implement caching** - Cache frequently accessed data
- **Optimize queries** - Use eager loading and proper indexes

---

## Testing Strategy

### Unit Tests

- **Test services independently** - Mock dependencies
- **Test business logic** - Verify service methods
- **Test validation rules** - Ensure data integrity
- **Test permissions** - Verify access control

### Feature Tests

- **Test module workflows** - End-to-end scenarios
- **Test API endpoints** - Verify HTTP responses
- **Test permissions** - Verify access restrictions
- **Test error handling** - Verify graceful failures

### Integration Tests

- **Test module interactions** - Cross-module functionality
- **Test service resolution** - Dependency injection
- **Test route loading** - Module routing
- **Test view rendering** - Template compilation

---

## Security Considerations

### Permission Management

- **Implement least privilege** - Minimum required permissions
- **Validate all inputs** - Service-level validation
- **Sanitize outputs** - Prevent XSS attacks
- **Use CSRF protection** - Form submissions

### Audit Trail

- **Log sensitive operations** - Track important changes
- **Protect audit logs** - Prevent tampering
- **Regular log review** - Monitor for suspicious activity
- **Data retention policies** - Clean old logs appropriately

### Data Protection

- **Encrypt sensitive data** - Personal information
- **Use secure connections** - HTTPS everywhere
- **Implement rate limiting** - Prevent abuse
- **Validate file uploads** - Security scanning

---

## Troubleshooting

### Common Issues

#### Service Not Found
```
Target class [App\Modules\Requests\Services\RequestService] does not exist
```
**Solution:** Ensure service is registered in ModuleServiceProvider

#### View Not Found
```
View [requests::index] not found
```
**Solution:** Check view file exists in correct module directory

#### Permission Denied
```
This action is unauthorized
```
**Solution:** Check user has required module permission

#### Route Not Registered
```
Route [requests.index] not defined
```
**Solution:** Ensure route file exists and is loaded

### Debugging Tips

1. **Check service registration** - Verify ModuleServiceProvider
2. **Verify view paths** - Check module directory structure
3. **Test permissions** - Use `hasPermissionTo()` method
4. **Check route definitions** - Verify route files exist
5. **Review logs** - Check Laravel error logs

---

## Future Enhancements

### Planned Improvements

1. **API Module** - Dedicated API-only modules
2. **Event System** - Module-specific event handling
3. **Cache Management** - Module-level caching strategies
4. **Background Jobs** - Module-specific job queues
5. **Internationalization** - Module-specific translations

### Extension Points

- **Custom base classes** - Module-specific base classes
- **Shared libraries** - Common functionality across modules
- **Plugin system** - Dynamic module loading
- **Theme system** - Module-specific theming

---

## Conclusion

The modular architecture provides:

- **Better organization** - Clear separation of concerns
- **Improved maintainability** - Easier to update and debug
- **Enhanced scalability** - Add new modules independently
- **Consistent patterns** - Shared base classes and traits
- **Better testing** - Isolated module testing

This architecture positions Jabalia for long-term maintainability and growth while maintaining backward compatibility with existing functionality.

---

*For questions or support, refer to the development team or check the system documentation.*
