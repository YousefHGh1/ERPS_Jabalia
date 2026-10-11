<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

$perm = Permission::firstOrCreate([
    'name' => 'index real_time_dashboards',
    'guard_name' => 'web'
]);

echo "Permission created: " . $perm->name . "\n";

$admin = Role::findByName('admin', 'web');
if ($admin) {
    $admin->givePermissionTo($perm);
    echo "Assigned to admin role\n";
} else {
    echo "Admin role not found\n";
}
