# Module Views Guide

**Version:** 2.0  
**Date:** April 20, 2026  
**Status:** IMPLEMENTED

---

## Overview

Each module has its own view directory containing Blade templates. This guide explains how to work with module views, naming conventions, and best practices for creating maintainable templates.

---

## View Structure

### Directory Organization

```
app/Modules/{Module}/Views/
|
|-- index.blade.php          # List view
|-- create.blade.php         # Create form
|-- show.blade.php          # Detail view
|-- edit.blade.php          # Edit form
|-- partials/              # Reusable components
|   |-- header.blade.php
|   |-- sidebar.blade.php
|   |-- forms/
|   |-- tables/
|   |-- cards/
```

### View Namespacing

Views are automatically loaded with module-specific namespaces:
- `requests::index` maps to `app/Modules/Requests/Views/index.blade.php`
- `craftlicenses::show` maps to `app/Modules/CraftLicenses/Views/show.blade.php`
- `engineering::edit` maps to `app/Modules/Engineering/Views/edit.blade.php`
- `employees::create` maps to `app/Modules/Employees/Views/create.blade.php`

---

## Available Views

### Requests Module

#### `index.blade.php`
**Purpose:** List all requests with filtering and statistics
**Features:**
- Statistics cards with real-time data
- Advanced filtering system
- Responsive data table
- Search functionality
- Pagination

**Key Components:**
- Statistics cards showing totals and counts
- Filter form with multiple criteria
- Data table with status badges
- Action buttons with permission checks

#### `create.blade.php`
**Purpose:** Form for creating new requests
**Features:**
- Dynamic form based on citizen selection
- JavaScript form validation
- Help sidebar with guidelines
- Form switching between existing/new citizen

**Key Components:**
- Toggle between existing/new citizen forms
- Dynamic field requirements
- Request type selection with descriptions
- Form validation and error handling

#### `show.blade.php`
**Purpose:** Detailed view of a single request
**Features:**
- Complete request information display
- Status history timeline
- Attachment management
- Quick action buttons
- Related information sidebar

**Key Components:**
- Timeline for status changes
- Attachment list with download/delete
- Status information card
- Modal dialogs for updates

---

### Craft Licenses Module

#### `index.blade.php`
**Purpose:** List all craft licenses with financial overview
**Features:**
- Financial statistics (total, revenue, outstanding)
- Status-based filtering
- Owner name search
- Expiry date tracking
- Revenue display

**Key Components:**
- Financial statistics cards
- Filter form with multiple criteria
- Data table with financial data
- Badge indicators for status and amounts

---

### Engineering Module

#### `index.blade.php`
**Purpose:** List all engineering files with workflow status
**Features:**
- Workflow statistics
- Neighborhood-based filtering
- Status tracking
- Performance metrics
- Payment tracking

**Key Components:**
- Performance metrics cards
- Filter form with engineering-specific options
- Data table with workflow status
- Financial information display

---

### Employees Module

#### `index.blade`
**Purpose:** List all employees with HR overview
**Features:**
- Employee statistics
- Department/section filtering
- Status management
- Attendance tracking
- Leave management

**Key Components:**
- HR statistics cards
- Filter form with HR options
- Data table with employee details
- Status indicators and badges

---

## Blade Component Usage

### Shared Components

#### Partial Views
Create reusable partials in `partials/` directory:

```php
// app/Modules/Shared/Views/partials/header.blade.php
@include('shared::partials.header', ['title' => $title])

// app/Modules/Shared/Views/partials/table.blade.php
@include('shared::partials.table', ['headers' => $headers, 'data' => $data])
```

#### Reusable Components
Create reusable components in `components/` directory:

```php
// app/Modules/Shared/Views/components/status-badge.blade.php
@component('status-badge', ['status' => $status])
    <span class="badge bg-{{ getStatusColor($status) }}">
        {{ getStatusName($status) }}
    </span>
@endcomponent
```

### Module-Specific Components

#### Request Components
```php
// app/Modules/Requests/Views/components/request-card.blade.php
@component('request-card', ['request' => $request])
    <div class="card">
        <div class="card-body">
            <h5>{{ $request->sequence_number }}</h5>
            <p>{{ Str::limit($request->text, 100) }}</p>
        </div>
    </div>
@endcomponent
```

---

## Best Practices

### 1. Use Component-Based Architecture

**Good:**
```php
@component('status-badge', ['status' => $status])
    <span class="badge bg-{{ getStatusColor($status) }}">
        {{ getStatusName($status) }}
    </span>
@endcomponent
```

**Avoid:**
```php
<span class="badge bg-{{ getStatusColor($request->status) }}">
    {{ getStatusName($request->status) }}
</span>
```

### 2. Use Helper Functions

**Good:**
```php
@php
function getStatusColor($status) {
    return $statusColors[$status] ?? 'secondary';
}
```

**Avoid:**
```php
@if ($request->status === 'pending')
    <span class="badge bg-warning">Pending</span>
@endif
```

### 3. Use Conditional Content

**Good:**
```php
@if ($attachments->isNotEmpty())
    <div class="attachments-section">
        <!-- Attachment content -->
    </div>
@else
    <div class="no-data">
        <!-- Empty state -->
    </div>
@endif
```

### 4. Use Consistent Styling

**Good:**
```css
.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}
```

### 5. Use Semantic HTML5

**Good:**
```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('requests.index') }}">Requests</a>
        </li>
    </ol>
</nav>
```

---

## Data Display Patterns

### 1. Status Badges

```php
<span class="badge bg-{{ getStatusColor($status) }} fs-6">
    {{ getStatusName($status) }}
</span>
```

### 2. Financial Data

```php
<span class="text-success">{{ number_format($amount, 2) }}</span>
<span class="text-danger">{{ number_format($remaining, 2) }}</span>
```

### 3. Dates

```php
{{ $date->format('Y-m-d') }}
{{ $date->diffForHumans() }}
{{ $date->isPast() ? 'text-danger' : 'text-success' }}
```

### 4. Empty States

```php
@if ($items->isEmpty())
    <div class="text-center py-4">
        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
        <p class="text-muted">{{ __('No items found') }}</p>
    </div>
@endif
```

---

## Form Patterns

### 1. Dynamic Forms

```php
<div id="mokalaf_fields">
    <!-- Content that appears/disappears based on checkbox -->
</div>

<script>
document.addEventListener('change', function() {
    const checkbox = document.getElementById('has_mokalaf');
    const fields = document.getElementById('mokalaf_fields');
    fields.style.display = checkbox.checked ? 'block' : 'none';
});
</script>
```

### 2. Form Validation

```php
@error('field_name')
    <div class="text-danger">{{ $message }}</div>
@enderror

<input type="text" name="field_name" 
       class="form-control @error('field_name')" 
       value="{{ old('field_name') }}">
```

### 3. Form Actions

```php
<div class="d-flex justify-content-between">
    <a href="{{ route('index') }}" class="btn btn-outline-secondary">
        {{ __('Cancel') }}
    </a>
    <button type="submit" class="btn btn-primary">
        {{ __('Save') }}
    </button>
</div>
```

---

## Modal Patterns

### 1. Standard Modal

```php
<div class="modal fade" id="myModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Modal Title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Modal content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    {{ __('Save') }}
                </button>
            </div>
        </div>
    </div>
</div>
```

### 2. Confirmation Modals

```php
<button type="submit" 
        onclick="confirm('{{ __('Are you sure?') }}')">
    <i class="fas fa-trash"></i>
</button>

<script>
function confirm(message) {
    return confirm(message);
}
</script>
```

---

## Navigation Patterns

### 1. Breadcrumbs

```php
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('module.index') }}">{{ __('Module Name') }}</a>
        </li>
        <li class="breadcrumb-item active">{{ __('Current Page') }}</li>
    </ol>
</nav>
```

### 2. Action Buttons

```php
<div class="d-flex justify-content-between">
    <a href="{{ route('index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i>
        {{ __('Back') }}
    </a>
    @if (Auth::user()->can('update'))
        <a href="{{ route('edit', $id) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit"></i>
            {{ __('Edit') }}
        </a>
    @endif
</div>
```

---

## Data Tables

### 1. Standard Table

```php
<table class="table table-striped table-hover">
    <thead>
        <th>{{ __('ID') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Status') }}</th>
        <th class="text-end">{{ __('Actions') }}</th>
    </thead>
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->name }}</td>
                <td>
                    <span class="badge bg-{{ getStatusColor($item->status) }}">
                        {{ getStatusName($item->status) }}
                    </span>
                </td>
                <td class="text-end">
                    <div class="btn-group">
                        <a href="{{ route('show', $item->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if (Auth::user()->can('update'))
                            <a href="{{ route('edit', $item->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                        @if (Auth::user()->can('delete'))
                            <form action="{{ route('destroy', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="confirm('{{ __('Are you sure? }}') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

### 2. Responsive Tables

```php
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <!-- Table content -->
    </table>
</div>
```

---

## Styling Guidelines

### 1. CSS Organization

```css
/* Component styles */
.card {
    transition: transform 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 8px;
}

/* Hover effects */
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Badge styling */
.badge {
    font-size: 0.75rem;
    font-weight: 600;
}
```

### 2. JavaScript Integration

```javascript
// Component initialization
document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    initializeComponents();
    setupEventListeners();
});

// Event listeners
function setupEventListeners() {
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', handleFormSubmit);
    });
}

// Form submission
function handleFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    
    // Client-side validation
    if (!form.checkValidity()) {
        showValidationErrors(form);
        return;
    }
    
    // Submit form
    form.submit();
}
```

---

## Integration with Laravel

### 1. Route Usage

```php
// In controller
return view('requests::index', [
    'requests' => $requests,
    'statistics' => $statistics,
]);
```

### 2. Permission Checks

```php
@if (Auth::user()->can('view requests'))
    <div class="protected-content">
        <!-- Protected content -->
    </div>
@endif
```

### 3. URL Generation

```php
// Named routes
return redirect()->route('requests.index');
return redirect()->route('requests.create');
return redirect()->route('requests.show', $id);
```

---

## Mobile Optimization

### 1. Responsive Design

```php
<div class="row">
    <div class="col-md-8">
        <!-- Main content -->
    </div>
    <div class="col-md-4">
        <!-- Sidebar content -->
    </div>
</div>

<!-- Mobile adjustments -->
<div class="d-md-none d-lg-block">
    <!-- Mobile-only content -->
</div>
```

### 2. Touch-Friendly Interactions

```css
.btn {
    min-height: 44px;
    padding: 0.5rem 1rem;
}

.btn-sm {
    min-height: 32px;
    padding: 0.25rem 0.5rem;
}
```

---

## Performance Considerations

### 1. View Caching

```php
@if (Cache::has('dashboard_stats'))
    <!-- Cached content -->
    {{ Cache::get('dashboard_stats') }}
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

## Testing Views

### 1. View Testing

```php
// In Feature Test
$response = $this->get('/requests');
$response->assertSee('Requests Management');
$response->assertViewIs('requests::index');
```

### 2. Component Testing

```php
// Test component rendering
$view = view('requests::components.request-card', ['request' => $request]);
$html = $view->render();
$this->assertStringContains('sequence_number', $request->sequence_number);
```

---

## Common Patterns

### 1. Data Display

```php
// Safe data display
{{ $request->name ?? 'N/A' }}
{{ $request->created_at?->format('Y-m-d') }}
{{ number_format($amount, 2) }}
```

### 2. Conditional Content

```php
@if (condition)
    <!-- Content for when condition is true -->
@else
    <!-- Content for when condition is false -->
@endif
```

### 3. Loop Patterns

```php
@foreach ($items as $item)
    <tr>
        <td>{{ $item->name }}</td>
    </tr>
@endforeach
```

---

## Troubleshooting

### Common Issues

#### View Not Found
```
View [requests::index] not found
```
**Solution:** Check if view file exists in correct module directory

#### Component Not Found
```
@component('non-existent', [])
```
**Solution:** Ensure component is registered in service provider

#### Permission Denied
```
This action is unauthorized
```
**Solution:** Check user permissions in database

### Debugging Tips

1. **Check view file path:** Verify file exists in correct module
2. **Check namespace:** Ensure correct module namespace is used
3. **Check permissions:** Verify user has required permissions
4. **Check variables:** Ensure all variables are defined

---

## Future Enhancements

### Planned Improvements

1. **API Views:** JSON views for API endpoints
2. **Email Templates:** Email template views
3. **PDF Templates:** PDF generation views
4. **Print Views**: Print-optimized views
5. **Mobile Views**: Mobile-optimized views

### Extension Points

- **Custom Components:** Module-specific components
- **View Composers:** Complex view composition
- **View Macros:** Reusable view macros
- **View Composers:** Component composition
- **View Inheritance:** Base view templates

---

## Conclusion

The modular view system provides:

- **Clear Organization:** Views organized by module
- **Namespace Isolation:** Prevents naming conflicts
- **Reusability:** Shared components across modules
- **Maintainability:** Easy to update and maintain
- **Testing Support:** Easy to unit test views

This view system supports the modular architecture and makes the application more maintainable and user-friendly.

---

*For questions or support, refer to the development team or check the system documentation.*
