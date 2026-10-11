<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "<pre>";
echo "perm_name_ar('accept_permission employee_permissions'): " . perm_name_ar('accept_permission employee_permissions') . "\n";
echo "perm_name_ar('accepted request_mails'): " . perm_name_ar('accepted request_mails') . "\n";
echo "perm_name_ar('acceptHoliday employee_holidays'): " . perm_name_ar('acceptHoliday employee_holidays') . "\n";
echo "</pre>";
