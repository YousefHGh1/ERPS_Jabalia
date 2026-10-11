<?php

/**
 * Add Performance Monitoring Permission
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Adding Performance Monitoring Permission ===\n\n";

try {
    // Find the admin user
    $user = User::where('name', 'like', '%admin%')->first();
    
    if (!$user) {
        echo "[ERROR] Admin user not found\n";
        
        // Try to find any user with admin in email
        $adminUser = User::where('email', 'like', '%admin%')->first();
        if ($adminUser) {
            echo "[INFO] Found admin user: {$adminUser->name}\n";
            $user = $adminUser;
        } else {
            echo "[ERROR] No admin user found\n";
            exit(1);
        }
    }
    
    echo "[INFO] User found: {$user->name} (ID: {$user->id})\n";
    
    // Create or get the permission
    $permission = Permission::firstOrCreate(['name' => 'monitor performance']);
    echo "[INFO] Permission created/found: {$permission->name}\n";
    
    // Give permission to user
    $user->givePermissionTo($permission);
    echo "[SUCCESS] Permission granted to user\n";
    
    // Also give to admin role if exists
    $adminRole = Role::where('name', 'admin')->first();
    if ($adminRole) {
        $adminRole->givePermissionTo($permission);
        echo "[SUCCESS] Permission granted to admin role\n";
    }
    
    // Verify permission was granted
    if ($user->hasPermissionTo('monitor performance')) {
        echo "[SUCCESS] Permission verification: PASSED\n";
    } else {
        echo "[ERROR] Permission verification: FAILED\n";
    }
    
    echo "\n=== COMPLETION SUMMARY ===\n";
    echo "User: {$user->name}\n";
    echo "Permission: monitor performance\n";
    echo "Status: GRANTED\n";
    echo "\nYou can now access: http://localhost:8000/performance\n";
    
} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    echo "[INFO] Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\nPermission setup completed successfully! \u2728\n";
