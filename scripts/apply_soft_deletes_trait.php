<?php

/**
 * Apply SoftDeletes trait to module models.
 * Run: php scripts/apply_soft_deletes_trait.php
 */

$modelsPath = __DIR__ . '/../app/Models';

$targetModels = [
    'RevenueBatch.php',
    'Payment.php',
    'RequestAttachment.php',
    'RequestType.php',
    'RequestWorkflow.php',
    'NoticeAttachment.php',
    'ImprisonmentOrderAttachment.php',
    'ReservationAttachment.php',
    'Employee.php',
    'EmployeeDependent.php',
    'EmployeeBonus.php',
    'EmployeeHoliday.php',
    'EmployeePermission.php',
    'EmployeeJobInfo.php',
    'EmployeeJobHistory.php',
    'EmployeeMainHoliday.php',
    'EmployeeJobInfoShift.php',
    'EmployeeOverTime.php',
    'Department.php',
    'Section.php',
    'Branch.php',
    'Job.php',
    'Category.php',
    'Bonus.php',
    'Shift.php',
    'Holiday.php',
    'MainHoliday.php',
    'Attachment.php',
    'Vehicle.php',
    'DepartmentDirector.php',
    'SectionDirector.php',
    'BranchDirector.php',
];

foreach ($targetModels as $basename) {
    $file = $modelsPath . '/' . $basename;
    if (!is_file($file)) {
        echo "MISSING: {$basename}\n";
        continue;
    }

    $content = file_get_contents($file);
    $original = $content;

    if (str_contains($content, 'SoftDeletes')) {
        echo "SKIP (has SoftDeletes): {$basename}\n";
        continue;
    }

    if (!str_contains($content, 'use Illuminate\Database\Eloquent\SoftDeletes;')) {
        $content = preg_replace(
            '/(use App\\\\Models\\\\Concerns\\\\Auditable;\s*\n)/',
            "$1use Illuminate\Database\Eloquent\SoftDeletes;\n",
            $content,
            1
        );
    }

    if (preg_match('/class \w+ extends \w+\s*\n\{\s*\n(\s*)use /', $content, $m)) {
        $firstUse = preg_replace(
            '/(class \w+ extends \w+\s*\n\{\s*\n\s*use [^;]+;)/',
            '$1' . "\n{$m[1]}use SoftDeletes;",
            $content,
            1
        );
        if ($firstUse) {
            $content = $firstUse;
        }
    }

    if (preg_match('/protected \$casts\s*=\s*\[/', $content)) {
        if (!str_contains($content, "'deleted_at'")) {
            $content = preg_replace(
                '/(protected \$casts\s*=\s*\[)/',
                "$1\n        'deleted_at' => 'datetime',",
                $content,
                1
            );
        }
    } else {
        $content = preg_replace(
            '/(class \w+ extends \w+\s*\n\{\s*\n(?:\s*use [^;]+;\s*\n)+)/',
            "$1\n    protected \$casts = [\n        'deleted_at' => 'datetime',\n    ];\n",
            $content,
            1
        );
    }

    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "UPDATED: {$basename}\n";
    } else {
        echo "NO CHANGE: {$basename}\n";
    }
}

echo "Done.\n";
