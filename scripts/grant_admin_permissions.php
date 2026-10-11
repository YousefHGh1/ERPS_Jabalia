<?php

/**
 * Grant Performance Permissions to All Admin Users
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Granting Admin Permissions ===\n\n";

try {
    // Create performance monitoring permission
    $permission = Permission::firstOrCreate(['name' => 'monitor performance']);
    echo "[INFO] Performance permission ready\n";
    
    // Find admin role
    $adminRole = Role::where('name', 'admin')->first();
    if ($adminRole) {
        $adminRole->givePermissionTo($permission);
        echo "[SUCCESS] Permission granted to admin role\n";
    }
    
    // Find all admin users
    $adminUsers = User::where('name', 'like', '%admin%')
                    ->orWhere('email', 'like', '%admin%')
                    ->orWhereHas('roles', function($query) {
                        $query->where('name', 'admin');
                    })
                    ->get();
    
    $grantedCount = 0;
    foreach ($adminUsers as $user) {
        if (!$user->hasPermissionTo('monitor performance')) {
            $user->givePermissionTo($permission);
            echo "[SUCCESS] Granted permission to: {$user->name}\n";
            $grantedCount++;
        } else {
            echo "[INFO] User already has permission: {$user->name}\n";
        }
    }
    
    echo "\n=== SUMMARY ===\n";
    echo "Total admin users found: " . $adminUsers->count() . "\n";
    echo "New permissions granted: {$grantedCount}\n";
    echo "Permission: monitor performance\n";
    echo "Status: COMPLETED\n";
    
    echo "\nAll admin users can now access: http://localhost:8000/performance\n";
    
} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nPermission setup completed successfully! \u2728\n";
