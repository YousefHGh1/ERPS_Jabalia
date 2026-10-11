# Jabalia System Permissions Guide

## Overview

This document outlines the permissions system in Jabalia Municipality Management System and how to manage user access to different features.

## Core Permissions

### Performance Monitoring
- **Permission Name:** `monitor performance`
- **Description:** Access to performance monitoring dashboard and metrics
- **Route:** `/performance/*`
- **Controller:** `PerformanceMonitorController`
- **Required For:** System administrators, IT staff

### Administrative Permissions
- **Permission Name:** `manage users`
- **Description:** Create, edit, and delete user accounts
- **Route:** `/users/*`
- **Required For:** System administrators

### Data Management
- **Permission Name:** `manage requests`
- **Description:** Access and manage citizen requests
- **Route:** `/requests/*`
- **Required For:** Administrative staff

### Financial Permissions
- **Permission Name:** `manage payments`
- **Description:** Access payment processing and financial reports
- **Route:** `/payments/*`
- **Required For:** Finance department

## Permission Assignment

### Method 1: Using Artisan Commands

```bash
# Create a new permission
php artisan tinker
> $permission = Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'monitor performance']);

# Assign permission to user
> $user = App\Models\User::find(1);
> $user->givePermissionTo('monitor performance');

# Assign permission to role
> $role = Spatie\Permission\Models\Role::find(1);
> $role->givePermissionTo('monitor performance');
```

### Method 2: Using Provided Scripts

```bash
# Grant performance monitoring permission
php scripts/add_performance_permission.php

# Grant permissions to all admin users
php scripts/grant_admin_permissions.php
```

### Method 3: Database Direct Access

```sql
-- Add permission to user
INSERT INTO model_has_permissions (model_type, model_id, permission_id)
VALUES ('App\Models\User', 1, (SELECT id FROM permissions WHERE name = 'monitor performance'));

-- Add permission to role
INSERT INTO role_has_permissions (role_id, permission_id)
VALUES (1, (SELECT id FROM permissions WHERE name = 'monitor performance'));
```

## Available Scripts

### Permission Management Scripts

1. **`scripts/add_performance_permission.php`**
   - Adds performance monitoring permission to admin users
   - Creates permission if it doesn't exist
   - Verifies permission assignment

2. **`scripts/grant_admin_permissions.php`**
   - Grants performance permissions to all admin users
   - Updates admin role with performance permissions
   - Provides summary of granted permissions

## Performance Monitoring Access

### Prerequisites
- User must be authenticated (`auth` middleware)
- User must have `monitor performance` permission
- User account must be active

### Access Points
- **Dashboard:** `/performance`
- **Metrics API:** `/performance/metrics`
- **Cache Management:** `/performance/clear-cache`
- **Reports:** `/performance/report`

### Troubleshooting Access Issues

#### 403 Forbidden Error
If you receive a 403 error when accessing `/performance`, follow these steps:

1. **Check User Permissions:**
   ```bash
   php artisan tinker
   > $user = App\Models\User::where('name', 'like', '%admin%')->first();
   > $user->hasPermissionTo('monitor performance');
   ```

2. **Grant Permission:**
   ```bash
   php scripts/add_performance_permission.php
   ```

3. **Clear Cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

4. **Verify Middleware:**
   - Check `routes/web_performance.php`
   - Ensure middleware is properly configured
   - Verify permission name matches exactly

## Role-Based Access Control

### Default Roles

1. **Super Admin**
   - All permissions
   - System configuration
   - User management

2. **Admin**
   - Most administrative permissions
   - Performance monitoring
   - Data management

3. **Manager**
   - Department-specific permissions
   - Report access
   - Limited user management

4. **Employee**
   - Basic system access
   - Request management
   - Personal data access

### Creating Custom Roles

```bash
php artisan tinker
> $role = Spatie\Permission\Models\Role::firstOrCreate(['name' => 'performance_analyst']);
> $role->givePermissionTo(['monitor performance', 'view reports']);
```

## Security Best Practices

### Permission Management
1. **Principle of Least Privilege:** Grant only necessary permissions
2. **Regular Audits:** Review user permissions periodically
3. **Role-Based Access:** Use roles instead of direct user permissions
4. **Permission Logging:** Track permission changes

### Performance Monitoring Access
1. **Limit Access:** Only grant to trusted administrators
2. **Monitor Usage:** Track who accesses performance metrics
3. **Regular Review:** Periodically review access logs
4. **Secure Routes:** Ensure all performance routes are protected

## Common Issues and Solutions

### Issue: Permission Not Working
**Solution:**
- Clear application cache
- Check permission name spelling
- Verify user is authenticated
- Check middleware configuration

### Issue: 403 Error Despite Permission
**Solution:**
- Run permission grant script
- Check user role assignments
- Verify middleware order
- Check route protection

### Issue: Performance Dashboard Not Loading
**Solution:**
- Check database connection
- Verify performance indexes exist
- Check cache configuration
- Review error logs

## Monitoring and Maintenance

### Regular Tasks
1. **Weekly:** Review user permissions
2. **Monthly:** Audit performance dashboard access
3. **Quarterly:** Update role permissions
4. **Annually:** Review security policies

### Automated Monitoring
- Set up alerts for permission changes
- Monitor performance dashboard usage
- Track unauthorized access attempts
- Log permission modifications

## Support

For permission-related issues:
1. Check this documentation first
2. Run the provided diagnostic scripts
3. Review system logs
4. Contact system administrator

---

**Last Updated:** April 20, 2026  
**Version:** 1.0  
**System:** Jabalia Municipality Management System
